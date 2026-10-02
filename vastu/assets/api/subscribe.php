<?php
header('Content-Type: application/json');

// --- DB connection ---
$host = "localhost";
$dbUser = "u587738524_root";
$dbPass = "uM9>wCaKx|n";
$dbName = "u587738524_vastu_db";

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// --- Get email from POST ---
$email = trim($_POST['email'] ?? '');

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Please enter a valid email address."]);
    exit;
}

// --- Insert directly (no duplicate check as requested) ---
$stmt = $conn->prepare("INSERT INTO tbl_subscribe (email, status) VALUES (?, 'Active')");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Subscribed successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "Something went wrong. Please try again."]);
}

$stmt->close();
$conn->close();