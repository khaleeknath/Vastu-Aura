<?php
session_start();
include('../config/db-conn.php');

$first_name = trim($_POST['FirstName']);
$last_name  = trim($_POST['LastName']);
$email      = trim($_POST['email']);
$phone      = trim($_POST['mobileNumber']);
$password   = $_POST['password'];

if (empty($email) || empty($password) || empty($phone)) {
    $_SESSION['register_error'] = "All fields are required.";
    header("Location: ../../register.php");
    exit;
}

// Check if mobile already exists
$checkQuery = "SELECT id FROM tbl_users WHERE phone = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $_SESSION['register_error'] = "This mobile number is already registered. Please login.";

    $_SESSION['old_first_name'] = $first_name;
    $_SESSION['old_last_name'] = $last_name;
    $_SESSION['old_email'] = $email;
    $_SESSION['old_mobile'] = $phone;

    header("Location: ../../register.php");
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$query = "INSERT INTO tbl_users (first_name, last_name, email, phone, password, role_id)
VALUES (?, ?, ?, ?, ?, 2)";

$stmt = $conn->prepare($query);
$stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $hashedPassword);

if ($stmt->execute()) {

    $_SESSION['success'] = "Registration successful. Please login.";
    header("Location: ../../login.php");
    exit;

} else {

    echo "Database Error: " . $stmt->error;
}
?>