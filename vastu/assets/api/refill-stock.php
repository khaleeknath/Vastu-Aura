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

$productId = $_POST['product_id'] ?? '';
$quantity  = $_POST['quantity'] ?? '';


if (!ctype_digit((string)$productId) || !ctype_digit((string)$quantity) || (int)$quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
    exit;
}


$user_id = (int)($_SESSION['user_id'] ?? 0);
$reason = 'Stock refill via admin panel';
$result = recordStockMovementStandalone(
    $conn,
    (int)$productId,
    'IN',
    (int)$quantity,
    $reason,
    $user_id
);

echo json_encode($result);