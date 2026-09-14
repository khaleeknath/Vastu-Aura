<?php
/**
 * contact-submit.php
 * Handles AJAX POST from the "Direct Enquiry" form on contact.php
 * Validates input, inserts into vastu_db.contact_enquiries, returns JSON.
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------
// 1. Only allow POST + require the same login the rest of the site uses
// ---------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     echo json_encode(['success' => false, 'message' => 'Please login first to submit an enquiry.']);
//     exit;
// }

// ---------------------------------------------------------------
// 2. DB connection
//    NOTE: If your project already has a shared connection file
//    (e.g. assets/config/db.php or includes/db.php) replace the
//    block below with: require_once __DIR__ . '/../config/db.php';
//    and make sure it defines a mysqli instance called $conn.
// ---------------------------------------------------------------
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'vastu_db';

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Please try again later.']);
    exit;
}
$conn->set_charset('utf8mb4');

// ---------------------------------------------------------------
// 3. Collect + sanitize input
// ---------------------------------------------------------------
$full_name    = trim($_POST['full_name'] ?? '');
$email        = trim($_POST['email'] ?? '');
$mobile       = trim($_POST['mobile'] ?? '');
$enquiry_type = trim($_POST['enquiry_type'] ?? '');
$message      = trim($_POST['message'] ?? '');

$allowed_types = [
    'Consultation Details',
    'Sacred Store & Products',
    'Commercial Partnership',
    'General Support'
];

$errors = [];

if ($full_name === '' || mb_strlen($full_name) > 150) {
    $errors[] = 'Please enter a valid full name.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if ($mobile !== '' && !preg_match('/^[0-9+\-\s()]{6,20}$/', $mobile)) {
    $errors[] = 'Please enter a valid mobile number.';
}

if (!in_array($enquiry_type, $allowed_types, true)) {
    $errors[] = 'Please select a valid enquiry topic.';
}

if ($message === '' || mb_strlen($message) > 5000) {
    $errors[] = 'Please enter a message (max 5000 characters).';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    $conn->close();
    exit;
}

// ---------------------------------------------------------------
// 4. Insert into DB (prepared statement)
// ---------------------------------------------------------------

$ip_address = $_SERVER['REMOTE_ADDR'] ?? null;

$sql = "INSERT INTO tbl_contact_enquiries
            (full_name, email, mobile, enquiry_type, message, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to process your enquiry right now.']);
    $conn->close();
    exit;
}

$stmt->bind_param(
    'ssssss',
    $full_name,
    $email,
    $mobile,
    $enquiry_type,
    $message,
    $ip_address
);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you. Your enquiry has been received — our concierge will reach out shortly.'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong while saving your enquiry. Please try again.']);
}

$stmt->close();
$conn->close();