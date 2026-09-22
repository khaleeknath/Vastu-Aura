<?php
/**
 * api/forgot-password-verify-otp.php
 * -----------------------------------
 * Step 2 of the "forgot password" flow.
 * Accepts { email, otp }. On success, issues a short-lived, single-use
 * reset token so the final "set new password" step doesn't need to
 * re-transmit the OTP (and can't be replayed once used).
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
 
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = trim(strtolower((string)($input['email'] ?? '')));
$otp   = trim((string)($input['otp'] ?? ''));
 
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^\d{6}$/', $otp)) {
    json_out(['success' => false, 'message' => 'Please enter the 6-digit code sent to your email.'], 422);
}
 
try {
    $stmt = $conn->prepare("
        SELECT id, otp_hash, attempts, otp_expires_at
        FROM tbl_password_resets
        WHERE email = ? AND used = 0
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
 
    if (!$row) {
        json_out(['success' => false, 'message' => 'No active code found. Please request a new one.'], 400);
    }
 
    if ((int)$row['attempts'] >= 5) {
        json_out(['success' => false, 'message' => 'Too many attempts. Please request a new code.'], 429);
    }
 
    if (strtotime($row['otp_expires_at']) < time()) {
        json_out(['success' => false, 'message' => 'This code has expired. Please request a new one.'], 400);
    }
 
    if (!password_verify($otp, $row['otp_hash'])) {
        $failStmt = $conn->prepare("UPDATE tbl_password_resets SET attempts = attempts + 1 WHERE id = ?");
        $failStmt->bind_param('i', $row['id']);
        $failStmt->execute();
        $failStmt->close();
        json_out(['success' => false, 'message' => 'Incorrect code. Please try again.'], 400);
    }
 
    // OTP is correct — issue a reset token so step 3 doesn't reuse the OTP.
    $resetToken = bin2hex(random_bytes(32));
    $resetTokenHash = password_hash($resetToken, PASSWORD_DEFAULT);
    $tokenExpiresAt = date('Y-m-d H:i:s', time() + 10 * 60);
    $rowId = (int)$row['id'];
 
    $updateStmt = $conn->prepare("
        UPDATE tbl_password_resets
        SET reset_token_hash = ?, token_expires_at = ?
        WHERE id = ?
    ");
    $updateStmt->bind_param('ssi', $resetTokenHash, $tokenExpiresAt, $rowId);
    $updateStmt->execute();
    $updateStmt->close();
 
    json_out(['success' => true, 'message' => 'Code verified.', 'reset_token' => $resetToken]);
} catch (Throwable $e) {
    error_log('[forgot-password-verify-otp] ' . $e->getMessage());
    json_out(['success' => false, 'message' => 'Something went wrong. Please try again.'], 500);
}
 