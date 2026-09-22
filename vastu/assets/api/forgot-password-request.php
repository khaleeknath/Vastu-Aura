<?php
/**
 * api/forgot-password-request.php
 * --------------------------------
 * Step 1 of the "forgot password" flow.
 * Accepts { email }. If an account exists for that email, generates a
 * 6-digit OTP, stores only its hash, and emails it to the user.
 *
 * The response message is deliberately generic in both cases so this
 * endpoint can't be used to check which emails are registered.
 *
 * ASSUMES: the required file below defines a mysqli instance in $conn.
 */

declare(strict_types=1);
header('Content-Type: application/json');

require_once('../config/db-conn.php'); // expects $conn (mysqli)
require_once('mailer.php');

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

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_out(['success' => false, 'message' => 'Please enter a valid email address.'], 422);
}

$genericMessage = "If an account exists for that email, we've sent a 6-digit code to it.";

try {
    $stmt = $conn->prepare("SELECT id, first_name, email FROM tbl_users WHERE email = ? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user) {
        // Simple resend throttle: don't allow a new code more than once every 45s.
        // TIMESTAMPDIFF is computed by MySQL against its own clock, so this can't
        // drift out of sync with PHP's timezone/clock settings the way comparing
        // time() against strtotime($created_at) could.
        $throttleStmt = $conn->prepare(
            "SELECT TIMESTAMPDIFF(SECOND, created_at, NOW()) AS seconds_since
             FROM tbl_password_resets WHERE email = ? ORDER BY id DESC LIMIT 1"
        );
        $throttleStmt->bind_param('s', $email);
        $throttleStmt->execute();
        $throttleRow = $throttleStmt->get_result()->fetch_assoc();
        $throttleStmt->close();

        $secondsSinceLast = $throttleRow['seconds_since'] ?? null;

        if ($secondsSinceLast !== null && $secondsSinceLast < 45) {
            error_log("[mailer] Resend throttled for {$email}, {$secondsSinceLast}s since last code.");
            json_out(['success' => true, 'message' => $genericMessage]);
        }

        // Invalidate any previous unused codes for this email so only the
        // latest one is ever valid.
        $invalidateStmt = $conn->prepare("UPDATE tbl_password_resets SET used = 1 WHERE email = ? AND used = 0");
        $invalidateStmt->bind_param('s', $email);
        $invalidateStmt->execute();
        $invalidateStmt->close();

        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpHash = password_hash($otp, PASSWORD_DEFAULT);
        $otpExpiresAt = date('Y-m-d H:i:s', time() + 10 * 60);
        $userId = (int)$user['id'];

        $insertStmt = $conn->prepare("
            INSERT INTO tbl_password_resets (user_id, email, otp_hash, attempts, otp_expires_at, used, created_at)
            VALUES (?, ?, ?, 0, ?, 0, NOW())
        ");
        $insertStmt->bind_param('isss', $userId, $email, $otpHash, $otpExpiresAt);
        $insertStmt->execute();
        $insertStmt->close();

        sendPasswordResetOtpEmail([
            'name'  => $user['first_name'],
            'email' => $user['email'],
            'otp'   => $otp,
        ]);
    }
} catch (Throwable $e) {
    error_log('[forgot-password-request] ' . $e->getMessage());
    // Fall through to the generic message - never leak internal errors here.
}

json_out(['success' => true, 'message' => $genericMessage]);