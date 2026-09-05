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

$name = trim($_POST['name'] ?? '');

if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'Category name is required.']);
    exit;
}

// Prevent duplicate category names
$check = mysqli_prepare($conn, "SELECT id FROM tbl_categories WHERE name = ?");
mysqli_stmt_bind_param($check, "s", $name);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) > 0) {
    echo json_encode(['success' => false, 'message' => 'This category already exists.']);
    exit;
}
mysqli_stmt_close($check);

$stmt = mysqli_prepare($conn, "INSERT INTO tbl_categories (name) VALUES (?)");
mysqli_stmt_bind_param($stmt, "s", $name);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => true,
        'message' => 'Category added successfully.',
        'category' => [
            'id'   => mysqli_insert_id($conn),
            'name' => $name
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add category: ' . mysqli_error($conn)]);
}