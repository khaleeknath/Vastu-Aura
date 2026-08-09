<?php
session_start();
include('../config/db-conn.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $_SESSION['user_id'];
$product_id = (int)$data['product_id'];
$quantity = (int)$data['quantity'];

// Check if already in cart
$sql = "SELECT id, quantity FROM tbl_cart WHERE user_id=? AND product_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $cart = $result->fetch_assoc();

    $newQty = $cart['quantity'] + $quantity;

    $update = $conn->prepare("UPDATE tbl_cart SET quantity=? WHERE id=?");
    $update->bind_param("ii", $newQty, $cart['id']);
    $update->execute();

} else {

    $insert = $conn->prepare("INSERT INTO tbl_cart(user_id, product_id, quantity) VALUES(?,?,?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();

}

echo json_encode([
    "success" => true,
    "message" => "Product added to cart successfully."
]);