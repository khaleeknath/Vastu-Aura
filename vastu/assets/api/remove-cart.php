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

        // Get updated cart count after delete
        $countStmt = $conn->prepare(
            "SELECT COALESCE(SUM(quantity), 0) AS cart_count
             FROM tbl_cart
             WHERE user_id = ?"
        );
    
        $countStmt->bind_param("i", $user_id);
        $countStmt->execute();
    
        $countResult = $countStmt->get_result();
        $countRow = $countResult->fetch_assoc();
    
        $cartCount = (int) $countRow['cart_count'];
    
        // Store updated count in session
        $_SESSION['cart_count'] = $cartCount;

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