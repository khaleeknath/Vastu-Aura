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

$user_id  = $_SESSION['user_id'];
$cart_id  = (int)($_POST['cart_id'] ?? 0);
$decrease = ($_POST['decrease'] ?? '0') === '1';

if ($cart_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid cart item."]);
    exit;
}

$ok = false;

if ($decrease) {
    // Read current quantity (scoped to this user)
    $sel = $conn->prepare("SELECT quantity FROM tbl_cart WHERE id=? AND user_id=?");
    $sel->bind_param("ii", $cart_id, $user_id);
    $sel->execute();
    $row = $sel->get_result()->fetch_assoc();

    if (!$row) {
        echo json_encode(["success" => false, "message" => "Cart item not found."]);
        exit;
    }

    if ((int)$row['quantity'] > 1) {
        $stmt = $conn->prepare("UPDATE tbl_cart SET quantity = quantity - 1 WHERE id=? AND user_id=?");
    } else {
        $stmt = $conn->prepare("DELETE FROM tbl_cart WHERE id=? AND user_id=?");
    }
} else {
    $stmt = $conn->prepare("DELETE FROM tbl_cart WHERE id=? AND user_id=?");
}

$stmt->bind_param("ii", $cart_id, $user_id);
$ok = $stmt->execute();

if ($ok) {
    $countStmt = $conn->prepare("SELECT COALESCE(SUM(quantity),0) AS cart_count FROM tbl_cart WHERE user_id=?");
    $countStmt->bind_param("i", $user_id);
    $countStmt->execute();
    $cartCount = (int)$countStmt->get_result()->fetch_assoc()['cart_count'];

    $_SESSION['cart_count'] = $cartCount;

    echo json_encode([
        "success"   => true,
        "message"   => "Cart updated successfully.",
        "cartCount" => $cartCount
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Unable to update cart."]);
}