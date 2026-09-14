<?php
/**
 * admin-subscriber-update-status.php
 * Lets an admin toggle a subscriber's status (Active / Inactive).
 * Expects POST: id, status
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'vastu_db';

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}
$conn->set_charset('utf8mb4');

$id     = (int)($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');

$allowedStatuses = ['Active', 'Inactive'];

if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid subscriber ID or status.']);
    $conn->close();
    exit;
}

$stmt = $conn->prepare('UPDATE tbl_subscribe SET status = ? WHERE id = ?');
$stmt->bind_param('si', $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status updated.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not update status.']);
}

$stmt->close();
$conn->close();