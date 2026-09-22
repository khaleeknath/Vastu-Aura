<?php
/**
 * api/forgot-password-reset.php
 * -------------------------------
 * Step 3 of the "forgot password" flow.
 * Accepts { email, reset_token, new_password, confirm_password }.
 * Validates the reset token issued in step 2, then updates tbl_users.password.
 */

declare(strict_types=1);
header('Content-Type: application/json');

require_once('../config/db-conn.php');

function json_out(array $data, int $code = 200): void
{
    http_response_code($code);
    echo json_encode($data);
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_out(['success' => false, 'message' => 'Invalid request method.'], 405);
}
 
$input       = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email       = trim(strtolower((string)($input['email'] ?? '')));
$resetToken  = trim((string)($input['reset_token'] ?? ''));
$newPassword = (string)($input['new_password'] ?? '');
$confirm     = (string)($input['confirm_password'] ?? '');
 
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $resetToken === '') {
    json_out(['success' => false, 'message' => 'Invalid request. Please restart the reset process.'], 422);
}
 
if (strlen($newPassword) < 8) {
    json_out(['success' => false, 'message' => 'Password must be at least 8 characters.'], 422);
}
 
if ($newPassword !== $confirm) {
    json_out(['success' => false, 'message' => 'Passwords do not match.'], 422);
}
 
$startedTransaction = false;
 
try {
    $stmt = $conn->prepare("
        SELECT id, user_id, reset_token_hash, token_expires_at
        FROM tbl_password_resets
        WHERE email = ? AND used = 0 AND reset_token_hash IS NOT NULL
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
 
    if (!$row || !password_verify($resetToken, $row['reset_token_hash'])) {
        json_out(['success' => false, 'message' => 'This reset session is invalid. Please start again.'], 400);
    }
 
    if (strtotime($row['token_expires_at']) < time()) {
        json_out(['success' => false, 'message' => 'This reset session has expired. Please start again.'], 400);
    }
 
    $conn->begin_transaction();
    $startedTransaction = true;
 
    $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $userId = (int)$row['user_id'];
    $rowId = (int)$row['id'];
 
    $passStmt = $conn->prepare("UPDATE tbl_users SET password = ? WHERE id = ?");
    $passStmt->bind_param('si', $newPasswordHash, $userId);
    $passStmt->execute();
    $passStmt->close();
 
    $usedStmt = $conn->prepare("UPDATE tbl_password_resets SET used = 1 WHERE id = ?");
    $usedStmt->bind_param('i', $rowId);
    $usedStmt->execute();
    $usedStmt->close();
 
    $conn->commit();
    $startedTransaction = false;
 
    json_out(['success' => true, 'message' => 'Your password has been updated. You can now log in.']);
} catch (Throwable $e) {
    if ($startedTransaction) {
        $conn->rollback();
    }
    error_log('[forgot-password-reset] ' . $e->getMessage());
    json_out(['success' => false, 'message' => 'Something went wrong. Please try again.'], 500);
}
 