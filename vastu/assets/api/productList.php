<?php
session_start();

header('Content-Type: application/json');

include('../config/db-conn.php');

$sql = "
SELECT
    p.id,
    p.name,
    p.description,
    p.price,
    p.image,
    p.stock,
    c.name AS category_name
FROM tbl_products p
LEFT JOIN tbl_categories c
    ON c.id = p.category_id
WHERE p.status = 1
ORDER BY p.id DESC
";

$result = mysqli_query($conn, $sql);

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

/* Cart Count */
$cartCount = 0;

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("
        SELECT SUM(quantity) AS totalQty
        FROM tbl_cart
        WHERE user_id = ?
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $cartResult = $stmt->get_result()->fetch_assoc();

    $cartCount = $cartResult['totalQty'] ?? 0;
}

echo json_encode([
    "status" => true,
    "data" => $products,
    "cartCount" => $cartCount
]);