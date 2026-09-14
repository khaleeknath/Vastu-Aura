<?php

session_start();
header("Content-Type: application/json");

include("../config/db-conn.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$orderId = isset($_GET['order_id']) ? (int) $_GET['order_id'] : 0;

if ($orderId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid order."
    ]);
    exit;
}

// 1. Fetch order + payment info (scoped to logged-in user so nobody can view someone else's order)
$sql = "SELECT
            o.id,
            o.total_amount,
            o.status AS order_status,
            DATE_FORMAT(o.order_date, '%d %b %Y, %h:%i %p') AS order_date,
            p.payment_method,
            p.payment_status,
            p.transaction_id
        FROM tbl_orders o
        LEFT JOIN tbl_payments p ON o.id = p.order_id
        WHERE o.id = ? AND o.user_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => $conn->error]);
    exit;
}

$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo json_encode([
        "success" => false,
        "message" => "Order not found."
    ]);
    exit;
}

// 2. Fetch line items with product details
$itemSql = "SELECT
                oi.quantity,
                oi.price,
                pr.name,
                pr.image
            FROM tbl_order_items oi
            JOIN tbl_products pr ON oi.product_id = pr.id
            WHERE oi.order_id = ?";

$itemStmt = $conn->prepare($itemSql);
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$itemsResult = $itemStmt->get_result();

$items = [];
while ($row = $itemsResult->fetch_assoc()) {
    $row['subtotal'] = number_format($row['quantity'] * $row['price'], 2);
    $items[] = $row;
}
$itemStmt->close();

$order['items'] = $items;

echo json_encode([
    "success" => true,
    "order" => $order
]);

$conn->close();