<?php
session_start();

header('Content-Type: application/json');

include('../config/db-conn.php');

$categoryId = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;

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
";

if ($categoryId > 0) {
    $sql .= " AND p.category_id = ? ";
}

$sql .= " ORDER BY p.id DESC ";

$stmt = $conn->prepare($sql);

if ($categoryId > 0) {
    $stmt->bind_param("i", $categoryId);
}

$stmt->execute();
$result = $stmt->get_result();

$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

/* Cart Count */
$cartCount = 0;

if (isset($_SESSION['user_id'])) {

    $user_id = $_SESSION['user_id'];

    $cartStmt = $conn->prepare("
        SELECT SUM(quantity) AS totalQty
        FROM tbl_cart
        WHERE user_id = ?
    ");

    $cartStmt->bind_param("i", $user_id);
    $cartStmt->execute();

    $cartResult = $cartStmt->get_result()->fetch_assoc();

    $cartCount = $cartResult['totalQty'] ?? 0;

    $_SESSION['cart_count'] = $cartCount;

}

echo json_encode([
    "status" => true,
    "data" => $products,
    "cartCount" => $cartCount
]);