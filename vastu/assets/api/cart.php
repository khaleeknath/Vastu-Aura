<?php
session_start();
include('../config/db-conn.php');

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];

$sql = "
SELECT
    c.id AS cart_id,
    c.quantity,
    p.id AS product_id,
    p.name,
    p.price,
    p.image,
    p.stock
FROM tbl_cart c
INNER JOIN tbl_products p
ON c.product_id = p.id
WHERE c.user_id = ?
ORDER BY c.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$cart = [];

while ($row = $result->fetch_assoc()) {
    $cart[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $cart
]);