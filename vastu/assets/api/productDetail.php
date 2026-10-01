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

if ($product) {
    // Pull extra images for the slider
    $imgSql = "SELECT image FROM tbl_product_images WHERE product_id = $id ORDER BY sort_order ASC, id ASC";
    $imgResult = mysqli_query($conn, $imgSql);

    $images = [];
    while ($row = mysqli_fetch_assoc($imgResult)) {
        $images[] = $row['image'];
    }

    // Fallback: if no rows in the gallery table, use the main image
    if (empty($images) && !empty($product['image'])) {
        $images[] = $product['image'];
    }

    $product['images'] = $images;
}

echo json_encode([
    'status' => true,
    'data' => $product
]);