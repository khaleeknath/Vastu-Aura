<?php
session_start();
require_once('../config/db-conn.php');
require_once('razorpay-config.php');
require_once('mailer.php');
header('Content-Type: application/json');

// ---- Auth check ----
if (!isset($_SESSION['user_id'])) {
    error_log("[booking] No user_id in session - not logged in");
    echo json_encode(["status" => false, "message" => "Please login first"]);
    exit;
}


$user_id = $_SESSION['user_id'];
error_log("[booking] ---- New request, user_id: $user_id ----");
error_log("[booking] Raw POST: " . json_encode($_POST));

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

error_log("[booking] razorpay_order_id: $razorpay_order_id | razorpay_payment_id: $razorpay_payment_id | signature present: " . (!empty($razorpay_signature) ? "yes" : "no"));

if (empty($name) || empty($email) || empty($mobile) || empty($date) || empty($time) || empty($address)) {
    error_log("[booking] Missing required field(s) - name:$name email:$email mobile:$mobile date:$date time:$time address:$address");
    echo json_encode(["status" => false, "message" => "All fields required"]);
    exit;
}

if (empty($razorpay_order_id) || empty($razorpay_payment_id) || empty($razorpay_signature)) {
    error_log("[booking] Missing Razorpay fields - order_id/payment_id/signature not all present");
    echo json_encode(["status" => false, "message" => "Payment details missing"]);
    exit;
}

// ---- Verify the payment signature server-side ----
$generated_signature = hash_hmac(
    'sha256',
    $razorpay_order_id . '|' . $razorpay_payment_id,
    RAZORPAY_KEY_SECRET
);

error_log("[booking] Generated signature: $generated_signature | Received signature: $razorpay_signature");

if (!hash_equals($generated_signature, $razorpay_signature)) {
    error_log("[booking] SIGNATURE MISMATCH - rejecting. Check RAZORPAY_KEY_SECRET matches the key used in create-order.php");
    echo json_encode(["status" => false, "message" => "Payment verification failed"]);
    exit;
}

error_log("[booking] Signature verified OK");

// ---- Look up the amount WE recorded when the order was created ----
$lookupStmt = $conn->prepare(
    "SELECT amount, user_id, status FROM tbl_razorpay_orders WHERE razorpay_order_id = ?"
);

if ($lookupStmt === false) {
    error_log("[booking] prepare() FAILED for order lookup: " . $conn->error);
    echo json_encode(["status" => false, "message" => "Server setup issue: " . $conn->error]);
    exit;
}

$lookupStmt->bind_param("s", $razorpay_order_id);
$lookupStmt->execute();
$orderRow = $lookupStmt->get_result()->fetch_assoc();

error_log("[booking] Order lookup result: " . json_encode($orderRow));

if (!$orderRow) {
    error_log("[booking] No row in tbl_razorpay_orders for order_id: $razorpay_order_id - was create-order.php's insert successful?");
    echo json_encode(["status" => false, "message" => "Unknown or expired order"]);
    exit;
}

// Extra safety: make sure the order actually belongs to the logged-in user
if ((int)$orderRow['user_id'] !== (int)$user_id) {
    error_log("[booking] User mismatch - order belongs to user_id {$orderRow['user_id']}, but request came from user_id $user_id");
    echo json_encode(["status" => false, "message" => "Order does not belong to this user"]);
    exit;
}

if ($orderRow['status'] === 'paid') {
    error_log("[booking] Order $razorpay_order_id was already marked paid - preventing duplicate insert");
    echo json_encode(["status" => false, "message" => "This payment has already been used for a booking"]);
    exit;
}

$amount = $orderRow['amount'];
$payment_status = 'paid';

error_log("[booking] About to insert appointment. amount=$amount bhk_type=$bhk_type sqft=$sqft property_type=$property_type");

$stmt = $conn->prepare("INSERT INTO tbl_appointment
    (name, email, mobile, preferred_time, preferred_date, address, unit_number,
     consultation_type, property_type, bhk_type, sqft, client_lat, client_lng,
     distance_km, amount, razorpay_order_id, razorpay_payment_id, razorpay_signature,
     payment_status, user_id)
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

if ($stmt === false) {
    // This is the most likely culprit if tbl_appointment doesn't have the
    // new columns (unit_number, property_type, bhk_type, sqft, client_lat,
    // client_lng, distance_km, amount, razorpay_order_id, razorpay_payment_id,
    // razorpay_signature, payment_status) - prepare() fails silently
    // without them and nothing gets inserted.
    error_log("[booking] INSERT prepare() FAILED: " . $conn->error);
    echo json_encode(["status" => false, "message" => "Server setup issue (insert prepare): " . $conn->error]);
    exit;
}

// 10 strings, 5 decimals (sqft/lat/lng/distance/amount), 4 strings, 1 int = 20
$bound = $stmt->bind_param(
    "ssssssssssdddddssssi",
    $name, $email, $mobile, $time, $date, $address, $unit_number,
    $type, $property_type, $bhk_type, $sqft, $client_lat, $client_lng,
    $distance_km, $amount, $razorpay_order_id, $razorpay_payment_id, $razorpay_signature,
    $payment_status, $user_id
);

if ($bound === false) {
    error_log("[booking] bind_param FAILED: " . $stmt->error);
    echo json_encode(["status" => false, "message" => "Server setup issue (bind_param): " . $stmt->error]);
    exit;
}

$executed = $stmt->execute();

error_log("[booking] Insert execute() returned: " . ($executed ? "true" : "false") . " | stmt->error: " . $stmt->error . " | affected_rows: " . $stmt->affected_rows);

if ($executed && $stmt->affected_rows > 0) {
    $updateStmt = $conn->prepare("UPDATE tbl_razorpay_orders SET status = 'paid' WHERE razorpay_order_id = ?");
    if ($updateStmt === false) {
        error_log("[booking] Order-status update prepare() FAILED: " . $conn->error);
    } else {
        $updateStmt->bind_param("s", $razorpay_order_id);
        $updateStmt->execute();
        error_log("[booking] tbl_razorpay_orders marked paid, affected_rows: " . $updateStmt->affected_rows);
    }

    error_log("[booking] SUCCESS - booking inserted for user_id $user_id");

       // Email sending happens after the booking is already saved, and never
    // blocks or fails the booking response - if SMTP is misconfigured or
    // down, the customer still gets their success message; only the emails
    // themselves are skipped (and logged) in that case.
    $bookingForEmail = [
        'name' => $name,
        'email' => $email,
        'mobile' => $mobile,
        'preferred_date' => $date,
        'preferred_time' => $time,
        'consultation_type' => $type,
        'property_type' => $property_type,
        'address' => $address,
        'amount' => $amount,
        'razorpay_payment_id' => $razorpay_payment_id,
    ];
 
    sendBookingConfirmationEmail($bookingForEmail);
    sendAdminBookingNotification($bookingForEmail);

    echo json_encode(["status" => true, "message" => "Booking Successful"]);
} else {
    error_log("[booking] INSERT FAILED - stmt->error: " . $stmt->error);
    echo json_encode(["status" => false, "message" => $stmt->error ?: "Insert did not affect any rows"]);
}



