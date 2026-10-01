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

$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$details     = trim($_POST['details'] ?? '');
$price       = $_POST['price'] ?? '';
$stock       = $_POST['stock'] ?? '';
$category_id = $_POST['category_id'] ?? '';

if ($name === '' || $price === '' || $stock === '' || $category_id === '') {
    echo json_encode(['success' => false, 'message' => 'Name, category, price and stock are required.']);
    exit;
}

if (!is_numeric($price) || !is_numeric($stock) || !ctype_digit((string)$category_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid price, stock or category.']);
    exit;
}

// ---- Handle image upload(s) ----
// The form field is now `images[]` (multiple). We still keep a single
// `image` value on tbl_products (the first uploaded file) so existing
// listing/table code that reads $row['image'] keeps working unchanged.
// Any further images go into tbl_product_images.
$allowed    = ['jpg', 'jpeg', 'png', 'webp'];
$maxFiles   = 3;             // sane cap so someone can't upload 500 files in one request
$maxBytes   = 5 * 1024 * 1024; // 5MB per file
$savedNames = [];

if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {

    $fileCount = count($_FILES['images']['name']);

    if ($fileCount > $maxFiles) {
        echo json_encode(['success' => false, 'message' => "You can upload up to $maxFiles images."]);
        exit;
    }

    $uploadDir = __DIR__ . '/../uploads/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    for ($i = 0; $i < $fileCount; $i++) {
        $error = $_FILES['images']['error'][$i];

        // Skip empty file inputs (e.g. user picked fewer than the input allows)
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        if ($error !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'One of the images failed to upload.']);
            exit;
        }

        $tmpName  = $_FILES['images']['tmp_name'][$i];
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

        $newName = 'prod_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        if (!move_uploaded_file($tmpName, $uploadDir . $newName)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload one of the images.']);
            exit;
        }

        $savedNames[] = $newName;
    }
}

$primaryImage = $savedNames[0] ?? null; // first image becomes the main/thumbnail image

// New products always start as 'inactive' regardless of any client input
$status = 'inactive';
// Initially the stock = 0 ; Updated in ledger
$initialStock = 0;

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO tbl_products (name, description, details, price, stock, image, status, created_at, category_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)"
);
mysqli_stmt_bind_param(
    $stmt,
    "sssdissi",
    $name,
    $description,
    $details,
    $price,
    $initialStock,
    $primaryImage,
    $status,
    $category_id
);

if (mysqli_stmt_execute($stmt)) {

    $product_id = mysqli_insert_id($conn);
    $user_id = (int)($_SESSION['user_id'] ?? 0); // adjust key to match your login session

    // ---- Save all uploaded images (including the primary one) into tbl_product_images ----
    if (!empty($savedNames)) {
        $imgStmt = mysqli_prepare(
            $conn,
            "INSERT INTO tbl_product_images (product_id, image, sort_order) VALUES (?, ?, ?)"
        );
        foreach ($savedNames as $order => $imgName) {
            mysqli_stmt_bind_param($imgStmt, "isi", $product_id, $imgName, $order);
            if (!mysqli_stmt_execute($imgStmt)) {
                error_log('Failed to save product image for product ' . $product_id . ': ' . mysqli_error($conn));
            }
        }
    }

    try {
        $ledgerResult = recordStockMovementStandalone(
            $conn,
            $product_id,
            'IN',
            (int)$stock,
            'Initial stock on product creation',
            $user_id
        );

        if (!$ledgerResult['success']) {
            error_log('Ledger failed for product ' . $product_id . ': ' . $ledgerResult['message']);
        }
    } catch (Throwable $e) {
        error_log('Stock ledger insert failed for product ' . $product_id . ': ' . $e->getMessage());
    }

    echo json_encode([
        'success' => true,
        'message' => 'Product added successfully and saved as Inactive.',
        'product_id' => $product_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product: ' . mysqli_error($conn)]);
}