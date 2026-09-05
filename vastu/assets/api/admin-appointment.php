<?php
include('../config/db-conn.php');
require_once('mailer.php');
header('Content-Type: application/json');

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!isset($data)) {
    echo json_encode([
        "success" => false,
        "message" => "No data received"
    ]);
    exit;
}

    $id = (int)$data['id'];
    $status = $data['status'];
    $comment = $data['comment'];

    $sql = "UPDATE tbl_appointment SET status=?, comment=? WHERE id=?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed",
            "error" => $conn->error
        ]);
        exit;
    }

    $stmt->bind_param("ssi", $status, $comment, $id);

    // 🔥 THIS IS REQUIRED
    if (!$stmt->execute()) {
        echo json_encode([
            "success" => false,
            "message" => "Execute failed",
            "error" => $stmt->error,
            "id" => $id
        ]);
        exit;
    }


    $stmt2 = mysqli_prepare($conn, "SELECT name, email, preferred_date, preferred_time FROM tbl_appointment WHERE id = ?");
mysqli_stmt_bind_param($stmt2, 'i', $id);
mysqli_stmt_execute($stmt2);
$client = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));

$emailSent = true;
if ($client && !empty($client['email'])) {
    $emailSent = sendBookingStatusUpdateEmail([
        'name'           => $client['name'],
        'email'          => $client['email'],
        'preferred_date' => $client['preferred_date'],
        'preferred_time' => $client['preferred_time'],
        'status'         => $status,
        'comment'        => $comment,
    ]);
}


echo json_encode([
    'success' => true,
    'message' => $emailSent
        ? 'Appointment updated and client notified by email.'
        : 'Appointment updated, but the email notification failed to send.'
]);
?>