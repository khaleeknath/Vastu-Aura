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

$id = $_POST['id'] ?? '';

if ($id === '' || !ctype_digit((string)$id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid product id.']);
    exit;
}

$find = mysqli_prepare($conn, "SELECT image FROM tbl_products WHERE id = ?");
mysqli_stmt_bind_param($find, "i", $id);
mysqli_stmt_execute($find);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($find));

$stmt = mysqli_prepare($conn, "DELETE FROM tbl_products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    if (!empty($row['image'])) {
        $path = __DIR__ . '/../uploads/products/' . $row['image'];
        if (is_file($path)) {
            @unlink($path);
        }
    }
    echo json_encode(['success' => true, 'message' => 'Product deleted.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete product: ' . mysqli_error($conn)]);
}