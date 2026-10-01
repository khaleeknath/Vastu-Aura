<?php
session_start();
header('Content-Type: application/json');
include(__DIR__ . '/../config/db-conn.php');
require_once('stock-ledger.php');

if (!isset($_SESSION['name'])) {
    echo json_encode(['success' => false, 'message' => 'Not authorized.']);
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$id          = $_POST['id'] ?? '';
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$details     = trim($_POST['details'] ?? '');
$price       = $_POST['price'] ?? '';
$stock       = $_POST['stock'] ?? '';
$category_id = $_POST['category_id'] ?? '';
$status      = $_POST['status'] ?? '';

$allowedStatus = ['active', 'inactive'];

if ($id === '' || $name === '' || $price === '' || $stock === '' || $category_id === '' || !in_array($status, $allowedStatus)) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid fields.']);
    exit;
}

if (!is_numeric($price) || !is_numeric($stock) || !ctype_digit((string)$category_id) || !ctype_digit((string)$id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid price, stock, category or product id.']);
    exit;
}

$id       = (int)$id;
$newStock = (int)$stock;

// ---- Image IDs the user removed in the gallery (must belong to this product) ----
$removedIdsRaw = $_POST['removed_images'] ?? [];
if (!is_array($removedIdsRaw)) {
    $removedIdsRaw = [$removedIdsRaw];
}
$removedIds = array_values(array_unique(array_filter(array_map(function ($v) {
    return ctype_digit((string)$v) ? (int)$v : null;
}, $removedIdsRaw), fn($v) => $v !== null)));

// ---- New images to upload (optional, multiple) ----
$allowed  = ['jpg', 'jpeg', 'png', 'webp'];
$maxBytes = 5 * 1024 * 1024;
$maxTotal = 3; // total images allowed per product after this update
$newFiles = [];

if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
    $count = count($_FILES['images']['name']);
    for ($i = 0; $i < $count; $i++) {
        $error = $_FILES['images']['error'][$i];
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($error !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'One of the new images failed to upload.']);
            exit;
        }

        $origName = $_FILES['images']['name'][$i];
        $size     = $_FILES['images']['size'][$i];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Only JPG, PNG or WEBP images are allowed.']);
            exit;
        }
        if ($size > $maxBytes) {
            echo json_encode(['success' => false, 'message' => "$origName is larger than 5MB."]);
            exit;
        }

        $newFiles[] = $_FILES['images']['tmp_name'][$i];
        $newFiles[count($newFiles) - 1] = [
            'tmp'  => $_FILES['images']['tmp_name'][$i],
            'ext'  => $ext,
        ];
    }
}

$uploadDir = __DIR__ . '/../uploads/products/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

mysqli_begin_transaction($conn);

try {
    // Lock the row and read the CURRENT stock before changing anything,
    // so the delta we hand to the ledger is accurate even under concurrent orders.
    $lockStmt = mysqli_prepare($conn, "SELECT stock FROM tbl_products WHERE id = ? FOR UPDATE");
    mysqli_stmt_bind_param($lockStmt, "i", $id);
    mysqli_stmt_execute($lockStmt);
    $current = mysqli_fetch_assoc(mysqli_stmt_get_result($lockStmt));

    if (!$current) {
        throw new Exception("Product not found.");
    }

    $oldStock = (int)$current['stock'];
    $delta    = $newStock - $oldStock;

    // ---- Validate removed image IDs actually belong to this product ----
    $filesToDelete = [];
    if (!empty($removedIds)) {
        $placeholders = implode(',', array_fill(0, count($removedIds), '?'));
        $types = 'i' . str_repeat('i', count($removedIds));
        $checkStmt = mysqli_prepare(
            $conn,
            "SELECT id, image FROM tbl_product_images WHERE product_id = ? AND id IN ($placeholders)"
        );
        mysqli_stmt_bind_param($checkStmt, $types, $id, ...$removedIds);
        mysqli_stmt_execute($checkStmt);
        $ownedRows = mysqli_stmt_get_result($checkStmt);

        $verifiedIds = [];
        while ($r = mysqli_fetch_assoc($ownedRows)) {
            $verifiedIds[]   = (int)$r['id'];
            $filesToDelete[] = $r['image'];
        }

        // Silently ignore any ids that didn't belong to this product rather than
        // trusting client input for a delete.
        if (!empty($verifiedIds)) {
            $delPlaceholders = implode(',', array_fill(0, count($verifiedIds), '?'));
            $delTypes = str_repeat('i', count($verifiedIds));
            $delStmt = mysqli_prepare($conn, "DELETE FROM tbl_product_images WHERE id IN ($delPlaceholders)");
            mysqli_stmt_bind_param($delStmt, $delTypes, ...$verifiedIds);
            mysqli_stmt_execute($delStmt);
        }
    }

    // ---- Check total image count won't exceed the cap ----
    $countStmt = mysqli_prepare($conn, "SELECT COUNT(*) AS c, COALESCE(MAX(sort_order), -1) AS max_order FROM tbl_product_images WHERE product_id = ?");
    mysqli_stmt_bind_param($countStmt, "i", $id);
    mysqli_stmt_execute($countStmt);
    $countRow  = mysqli_fetch_assoc(mysqli_stmt_get_result($countStmt));
    $remaining = (int)$countRow['c'];
    $nextOrder = (int)$countRow['max_order'] + 1;

    if ($remaining + count($newFiles) > $maxTotal) {
        throw new Exception("A product can have at most $maxTotal images.");
    }

    // ---- Move new files to disk now that validation has passed ----
    $savedNames = [];
    foreach ($newFiles as $f) {
        $newName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $f['ext'];
        if (!move_uploaded_file($f['tmp'], $uploadDir . $newName)) {
            throw new Exception('Failed to upload one of the new images.');
        }
        $savedNames[] = $newName;
    }

    if (!empty($savedNames)) {
        $imgStmt = mysqli_prepare(
            $conn,
            "INSERT INTO tbl_product_images (product_id, image, sort_order) VALUES (?, ?, ?)"
        );
        foreach ($savedNames as $offset => $imgName) {
            $order = $nextOrder + $offset;
            mysqli_stmt_bind_param($imgStmt, "isi", $id, $imgName, $order);
            mysqli_stmt_execute($imgStmt);
        }
    }

    // ---- Recompute the primary image: whichever image now has the lowest sort_order ----
    $primaryStmt = mysqli_prepare(
        $conn,
        "SELECT image FROM tbl_product_images WHERE product_id = ? ORDER BY sort_order ASC LIMIT 1"
    );
    mysqli_stmt_bind_param($primaryStmt, "i", $id);
    mysqli_stmt_execute($primaryStmt);
    $primaryRow    = mysqli_fetch_assoc(mysqli_stmt_get_result($primaryStmt));
    $primaryImage  = $primaryRow['image'] ?? null;

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE tbl_products
         SET name = ?, description = ?, details = ?, price = ?, category_id = ?, status = ?, image = ?
         WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "sssdissi", $name, $description, $details, $price, $category_id, $status, $primaryImage, $id);
    mysqli_stmt_execute($stmt);

    // Only touch the ledger if stock actually changed
    $user_id = (int)($_SESSION['user_id'] ?? 0);
    if ($delta !== 0) {
        recordStockMovement(
            $conn,
            $id,
            'ADJUSTMENT',
            $delta,
            'Manual correction via product edit',
            $user_id
        );
    }

    mysqli_commit($conn);

    // Delete removed image files from disk only after everything committed successfully
    foreach ($filesToDelete as $oldImage) {
        if (!empty($oldImage)) {
            $oldPath = $uploadDir . $oldImage;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);

} catch (Exception $e) {
    mysqli_rollback($conn);

    // Clean up any newly uploaded files since the DB changes were rolled back
    foreach ($savedNames ?? [] as $imgName) {
        $path = $uploadDir . $imgName;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . $e->getMessage()]);
}