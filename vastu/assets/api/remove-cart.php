<?php
session_start();
include('../config/db-conn.php');

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'] ?? 0;

if (empty($cart_id)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid cart item."
    ]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM tbl_cart WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $cart_id, $user_id);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Product removed successfully."
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to remove product."
    ]);

}