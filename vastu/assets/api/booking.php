<?php
session_start();
require_once('../config/db-conn.php');
require_once('razorpay-config.php');

header('Content-Type: application/json');

// ---- Auth check ----
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "Please login first"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// ---- Read form fields ----
$name          = $_POST['name'] ?? '';
$email         = $_POST['email'] ?? '';
$mobile        = $_POST['mobile'] ?? '';
$time          = $_POST['preferred_time'] ?? '';
$date          = $_POST['preferred_date'] ?? '';
$address       = $_POST['address'] ?? '';
$unit_number   = $_POST['unit_number'] ?? '';
$type          = $_POST['consultation_type'] ?? '';
$property_type = $_POST['property_type'] ?? '';
$bhk_type      = $_POST['bhk_type'] ?: null;
$sqft          = $_POST['sqft'] ?: null;
$client_lat    = $_POST['client_lat'] ?: null;
$client_lng    = $_POST['client_lng'] ?: null;
$distance_km   = $_POST['distance_km'] ?: null;

// ---- Razorpay fields returned by the checkout handler ----
$razorpay_order_id   = $_POST['razorpay_order_id'] ?? '';
$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
$razorpay_signature  = $_POST['razorpay_signature'] ?? '';

if (empty($name) || empty($email) || empty($mobile) || empty($date) || empty($time) || empty($address)) {
    echo json_encode(["status" => false, "message" => "All fields required"]);
    exit;
}

if (empty($razorpay_order_id) || empty($razorpay_payment_id) || empty($razorpay_signature)) {
    echo json_encode(["status" => false, "message" => "Payment details missing"]);
    exit;
}

// ---- Verify the payment signature server-side ----
// This proves the payment_id/order_id pair genuinely came back from
// Razorpay and wasn't just typed into the request by a client.
$generated_signature = hash_hmac(
    'sha256',
    $razorpay_order_id . '|' . $razorpay_payment_id,
    RAZORPAY_KEY_SECRET
);

if (!hash_equals($generated_signature, $razorpay_signature)) {
    echo json_encode(["status" => false, "message" => "Payment verification failed"]);
    exit;
}

// ---- Look up the amount WE recorded when the order was created ----
// Never trust an amount submitted by the browser - always use the value
// tied server-side to this order_id.
$amountInPaise = $_SESSION['rzp_orders'][$razorpay_order_id] ?? null;

if ($amountInPaise === null) {
    echo json_encode(["status" => false, "message" => "Unknown or expired order"]);
    exit;
}

$amount = $amountInPaise / 100;
$payment_status = 'paid';

$stmt = $conn->prepare("INSERT INTO tbl_appointment
    (name, email, mobile, preferred_time, preferred_date, address, unit_number,
     consultation_type, property_type, bhk_type, sqft, client_lat, client_lng,
     distance_km, amount, razorpay_order_id, razorpay_payment_id, razorpay_signature,
     payment_status, user_id)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

// 10 strings, 5 decimals (sqft/lat/lng/distance/amount), 4 strings, 1 int = 20
$stmt->bind_param(
    "ssssssssssdddddssssi",
    $name, $email, $mobile, $time, $date, $address, $unit_number,
    $type, $property_type, $bhk_type, $sqft, $client_lat, $client_lng,
    $distance_km, $amount, $razorpay_order_id, $razorpay_payment_id, $razorpay_signature,
    $payment_status, $user_id
);

if ($stmt->execute()) {
    // Order fulfilled - stop tracking it in the session
    unset($_SESSION['rzp_orders'][$razorpay_order_id]);
    echo json_encode(["status" => true, "message" => "Booking Successful"]);
} else {
    echo json_encode(["status" => false, "message" => $stmt->error]);
}