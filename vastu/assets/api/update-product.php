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

$id = (int)$id;
$newStock = (int)$stock;

// ---- Optional new image (unchanged, happens before the transaction since
// it touches the filesystem, not the DB) ----
$imageName = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, PNG or WEBP images are allowed.']);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $imageName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
        echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
        exit;
    }
}

mysqli_begin_transaction($conn);

try {
    // Lock the row and read the CURRENT stock + image before changing anything,
    // so the delta we hand to the ledger is accurate even under concurrent orders.
    $lockStmt = mysqli_prepare($conn, "SELECT stock, image FROM tbl_products WHERE id = ? FOR UPDATE");
    mysqli_stmt_bind_param($lockStmt, "i", $id);
    mysqli_stmt_execute($lockStmt);
    $current = mysqli_fetch_assoc(mysqli_stmt_get_result($lockStmt));

    if (!$current) {
        throw new Exception("Product not found.");
    }

    $oldStock = (int)$current['stock'];
    $oldImage = $current['image'] ?? null;
    $delta    = $newStock - $oldStock;

    // Update everything EXCEPT stock here — stock is only ever changed
    // through recordStockMovement, so the ledger stays the single source of truth.
    if ($imageName !== null) {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE tbl_products
             SET name = ?, description = ?, details = ?, price = ?, category_id = ?, status = ?, image = ?
             WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "sssdissi", $name, $description, $details, $price, $category_id, $status, $imageName, $id);
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "UPDATE tbl_products
             SET name = ?, description = ?, details = ?, price = ?, category_id = ?, status = ?
             WHERE id = ?"
        );
        mysqli_stmt_bind_param($stmt, "sssdisi", $name, $description, $details, $price, $category_id, $status, $id);
    }
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

    // Delete old image file only after everything committed successfully
    if ($imageName !== null && !empty($oldImage)) {
        $oldPath = __DIR__ . '/../uploads/products/' . $oldImage;
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . $e->getMessage()]);
}