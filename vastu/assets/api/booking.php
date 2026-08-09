<?php
include('../config/db-conn.php');
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    echo "Please login first";
    exit;
}

$user_id = $_SESSION['user_id'];

$name = $_POST['name'];
$email = $_POST['email'];
$mobile = $_POST['mobile'];
$time = $_POST['preferred_time'];
$date = $_POST['preferred_date'];
$address = $_POST['address'];
$type = $_POST['consultation_type'];

if(empty($name) || empty($email)){
    echo "All fields required";
    exit;
}

$stmt = $conn->prepare("INSERT INTO tbl_appointment 
(name, email, mobile, preferred_time, preferred_date, address, consultation_type, user_id) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssssi", $name, $email, $mobile, $time, $date, $address, $type, $user_id);

if ($stmt->execute()) {

    echo json_encode([
        "status" => true,
        "message" => "Booking Successful"
    ]);

} else {

    echo json_encode([
        "status" => false,
        "message" => $stmt->error
    ]);

}
?>