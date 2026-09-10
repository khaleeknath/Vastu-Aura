<?php
session_start();
include('../config/db-conn.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => false, "message" => "Please login first"]);
    exit;
}

$user_id   = $_SESSION['user_id'];
$newEmail  = trim($_POST['email'] ?? '');

error_log("[update-email] user_id: " . $user_id);
error_log("[update-email] newEmail: " . $newEmail);


if (empty($newEmail) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["status" => false, "message" => "Please enter a valid email address"]);
    exit;
}

// Prevent collisions with another account
$checkStmt = $conn->prepare("SELECT id FROM tbl_users WHERE email = ? AND id != ?");
$checkStmt->bind_param("si", $newEmail, $user_id);
$checkStmt->execute();
if ($checkStmt->get_result()->fetch_assoc()) {
    echo json_encode(["status" => false, "message" => "This email is already registered with another account"]);
    exit;
}

$updateStmt = $conn->prepare("UPDATE tbl_users SET email = ? WHERE id = ?");
if ($updateStmt === false) {
    error_log("[update-email] prepare() failed: " . $conn->error);
    echo json_encode(["status" => false, "message" => "Server error, please try again"]);
    exit;
}

$updateStmt->bind_param("si", $newEmail, $user_id);
if ($updateStmt->execute()) {
    $_SESSION['email'] = $newEmail; // keep session in sync
    error_log("[update-email] user_id $user_id -> $newEmail");
    echo json_encode(["status" => true, "message" => "Email updated successfully"]);
} else {
    error_log("[update-email] UPDATE failed: " . $updateStmt->error);
    echo json_encode(["status" => false, "message" => "Could not update email, please try again"]);
}