<?php
include('../config/db-conn.php');

header('Content-Type: application/json');

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!isset($data['data'])) {
    echo json_encode([
        "success" => false,
        "message" => "No data received"
    ]);
    exit;
}

foreach ($data['data'] as $row) {

    $id = (int)$row['id'];
    $status = $row['status'];
    $comment = $row['comment'];

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
}

echo json_encode([
    "success" => true,
    "message" => "Updated successfully"
]);
?>