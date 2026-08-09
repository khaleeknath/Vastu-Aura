<?php
session_start();
header('Content-Type: application/json');

include('../config/db-conn.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Please login."
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
SELECT
    p.name,
    p.price,
    c.quantity
FROM tbl_cart c
INNER JOIN tbl_products p
    ON p.id = c.product_id
WHERE c.user_id = ?
ORDER BY c.id DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "data" => $data
]);