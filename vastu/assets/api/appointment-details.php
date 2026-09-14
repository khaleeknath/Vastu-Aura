<?php

session_start();
header("Content-Type: application/json");

include("../config/db-conn.php");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit;
}

$userId = $_SESSION['user_id'];
$appointmentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($appointmentId <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid appointment."
    ]);
    exit;
}

$sql = "SELECT
            id,
            name,
            email,
            mobile,
            consultation_type,
            DATE_FORMAT(preferred_date, '%d %b %Y') AS preferred_date,
            preferred_time,
            address,
            property_type,
            bhk_type,
            sqft,
            unit_number,
            distance_km,
            amount,
            status,
            payment_status,
            razorpay_payment_id,
            comment,
            DATE_FORMAT(created_at, '%d %b %Y, %h:%i %p') AS created_at
        FROM tbl_appointment
        WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(["success" => false, "message" => $conn->error]);
    exit;
}

$stmt->bind_param("ii", $appointmentId, $userId);
$stmt->execute();
$appointment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$appointment) {
    echo json_encode([
        "success" => false,
        "message" => "Appointment not found."
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "appointment" => $appointment
]);

$conn->close();