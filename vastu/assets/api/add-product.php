<?php
session_start();
header('Content-Type: application/json');
include(__DIR__ . '/../config/db-conn.php');

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

// ---- Handle image upload (optional) ----
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

// New products always start as 'inactive' regardless of any client input
$status = 'inactive';

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
    $stock,
    $imageName,
    $status,
    $category_id
);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => true,
        'message' => 'Product added successfully and saved as Inactive.',
        'product_id' => mysqli_insert_id($conn)
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add product: ' . mysqli_error($conn)]);
}