<?php
session_start();
require_once('../config/db-conn.php');
require_once('razorpay-config.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "Please login first"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$amount = isset($_POST['amount']) ? (float) $_POST['amount'] : 0;

error_log("[create-order] amount received: " . $amount);

if ($amount <= 0) {
    echo json_encode(["status" => false, "message" => "Invalid amount"]);
    exit;
}

// Razorpay expects the amount in the smallest currency unit (paise for INR)
$amountInPaise = (int) round($amount * 100);

$orderData = [
    "amount"          => $amountInPaise,
    "currency"        => "INR",
    "receipt"         => "vastu_" . time() . "_" . $_SESSION['user_id'],
    "payment_capture" => 1
];

$ch = curl_init("https://api.razorpay.com/v1/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ":" . RAZORPAY_KEY_SECRET);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

error_log("[create-order] Razorpay HTTP code: " . $httpCode . " | response: " . $response);

if ($response === false) {
    echo json_encode(["status" => false, "message" => "Could not reach Razorpay: " . $curlErr]);
    exit;
}

$order = json_decode($response, true);

if ($httpCode !== 200 || !isset($order['id'])) {
    $msg = $order['error']['description'] ?? "Order creation failed";
    error_log("[create-order] Razorpay rejected order: " . $msg);
    echo json_encode(["status" => false, "message" => $msg]);
    exit;
}

// Record the order + trusted amount in the DB (not the session). This is
// what booking.php will look the amount up against, and it also leaves a
// permanent trail even if the user never finishes the booking step after
// paying (abandoned checkout, browser closed, etc).
$insertStmt = $conn->prepare(
    "INSERT INTO tbl_razorpay_orders (razorpay_order_id, user_id, amount, currency, status)
     VALUES (?, ?, ?, ?, 'created')"
);

if ($insertStmt === false) {
    // Prepare fails if tbl_razorpay_orders doesn't exist yet (or has a
    // different schema) - surface that clearly instead of a fatal error.
    error_log("[create-order] prepare() failed: " . $conn->error);
    echo json_encode([
        "status" => false,
        "message" => "Server setup issue: " . $conn->error
    ]);
    exit;
}

$amountRupees = $amountInPaise / 100;
$insertOk = $insertStmt->bind_param("sids", $order['id'], $user_id, $amountRupees, $order['currency'])
    && $insertStmt->execute();

if (!$insertOk || $insertStmt->affected_rows === 0) {
    error_log("[create-order] insert failed: " . $insertStmt->error);
    echo json_encode([
        "status" => false,
        "message" => "Could not record order: " . $insertStmt->error
    ]);
    exit;
}

error_log("[create-order] success, order_id: " . $order['id']);

echo json_encode([
    "status"   => true,
    "order_id" => $order['id'],
    "amount"   => $amountInPaise,
    "currency" => "INR",
    "key_id"   => RAZORPAY_KEY_ID
]);