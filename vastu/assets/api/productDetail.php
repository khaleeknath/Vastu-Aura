<?php

header('Content-Type: application/json');

include('../config/db-conn.php');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
WHERE p.id = $id
LIMIT 1
";

$result = mysqli_query($conn, $sql);

$product = mysqli_fetch_assoc($result);

echo json_encode([
    'status' => true,
    'data' => $product
]);