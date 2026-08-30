<?php
session_start();
header('Content-Type: application/json');
require_once('../config/db-conn.php');
require_once('razorpay-config.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => false, 'message' => 'Please login first.']);
    exit;
}
$user_id = $_SESSION['user_id'];

// Recompute total from the cart server-side — never trust a client-sent amount
$stmt = $conn->prepare("
    SELECT c.quantity, p.price
    FROM tbl_cart c
    INNER JOIN tbl_products p ON p.id = c.product_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart = $stmt->get_result();

if ($cart->num_rows === 0) {
    echo json_encode(['status' => false, 'message' => 'Cart is empty.']);
    exit;
}

$subtotal = 0;
while ($row = $cart->fetch_assoc()) {
    $subtotal += $row['price'] * $row['quantity'];
}

$shipping   = 150;
$grandTotal = $subtotal + $shipping;

$payload = [
    'amount'          => (int) round($grandTotal * 100), // paise
    'currency'        => 'INR',
    'receipt'         => 'order_' . time() . '_' . $user_id,
    'payment_capture' => 1,
];

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode($response, true);

if ($httpCode !== 200 || empty($order['id'])) {
    echo json_encode(['status' => false, 'message' => 'Could not create payment order.']);
    exit;
}

// Stash the trusted amount + order id so order.php can verify against it
$_SESSION['pending_cart_order'] = [
    'razorpay_order_id' => $order['id'],
    'amount'            => $grandTotal,
];

echo json_encode([
    'status'   => true,
    'order_id' => $order['id'],
    'amount'   => $order['amount'],
    'currency' => $order['currency'],
    'key_id'   => RAZORPAY_KEY_ID,
]);