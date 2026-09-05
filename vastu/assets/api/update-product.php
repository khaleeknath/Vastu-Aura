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

// ---- Optional new image ----
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

if ($imageName !== null) {
    // fetch old image to remove after successful update
    $old = mysqli_prepare($conn, "SELECT image FROM tbl_products WHERE id = ?");
    mysqli_stmt_bind_param($old, "i", $id);
    mysqli_stmt_execute($old);
    $oldResult = mysqli_stmt_get_result($old);
    $oldRow = mysqli_fetch_assoc($oldResult);
    $oldImage = $oldRow['image'] ?? null;

    $stmt = mysqli_prepare(
        $conn,
        "UPDATE tbl_products
         SET name = ?, description = ?, details = ?, price = ?, stock = ?, category_id = ?, status = ?, image = ?
         WHERE id = ?"
    );
    mysqli_stmt_bind_param(
        $stmt,
        "sssdiissi",
        $name, $description, $details, $price, $stock, $category_id, $status, $imageName, $id
    );
} else {
    $stmt = mysqli_prepare(
        $conn,
        "UPDATE tbl_products
         SET name = ?, description = ?, details = ?, price = ?, stock = ?, category_id = ?, status = ?
         WHERE id = ?"
    );
    mysqli_stmt_bind_param(
        $stmt,
        "sssdiisi",
        $name, $description, $details, $price, $stock, $category_id, $status, $id
    );
}

if (mysqli_stmt_execute($stmt)) {
    if ($imageName !== null && !empty($oldImage)) {
        $oldPath = __DIR__ . '/../uploads/products/' . $oldImage;
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }
    echo json_encode(['success' => true, 'message' => 'Product updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update product: ' . mysqli_error($conn)]);
}