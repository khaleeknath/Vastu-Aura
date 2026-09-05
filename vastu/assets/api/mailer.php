<?php
/**
 * mailer.php
 * ----------
 * Small wrapper around PHPMailer so the rest of the app can just call
 * sendBookingConfirmationEmail(...) and sendAdminBookingNotification(...)
 * without dealing with PHPMailer's setup every time.
 */

require_once __DIR__ . '/smtp-config.php';
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

/**
 * Configures a PHPMailer instance with our SMTP settings.
 * Returns null (and logs the error) if configuration itself fails -
 * callers should check for that before calling ->send().
 */
function getConfiguredMailer(): ?PHPMailer
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE === 'ssl'
            ? PHPMailer::ENCRYPTION_SMTPS
            : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->isHTML(true);

        return $mail;
    } catch (PHPMailerException $e) {
        error_log("[mailer] Failed to configure PHPMailer: " . $mail->ErrorInfo);
        return null;
    }
}

/**
 * Sends a booking confirmation email to the customer.
 * Returns true/false. Never throws - failures are logged, not fatal,
 * so a broken email setup can never block a booking from succeeding.
 */
function sendBookingConfirmationEmail(array $booking): bool
{
    $mail = getConfiguredMailer();
    if ($mail === null) return false;

    try {
        $mail->addAddress($booking['email'], $booking['name']);
        $mail->Subject = "Your VastuAura appointment is confirmed";

        $mail->Body = buildCustomerEmailHtml($booking);
        $mail->AltBody = buildCustomerEmailPlainText($booking);

        $mail->send();
        error_log("[mailer] Confirmation email sent to " . $booking['email']);
        return true;
    } catch (PHPMailerException $e) {
        error_log("[mailer] Failed to send customer email: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Sends a "new booking received" notification to the business owner.
 */
function sendAdminBookingNotification(array $booking): bool
{
    $mail = getConfiguredMailer();
    if ($mail === null) return false;

    try {
        $mail->addAddress(ADMIN_NOTIFICATION_EMAIL);
        $mail->Subject = "New booking: " . $booking['name'] . " - " . $booking['preferred_date'];

        $mail->Body = buildAdminEmailHtml($booking);
        $mail->AltBody = buildCustomerEmailPlainText($booking);

        $mail->send();
        error_log("[mailer] Admin notification sent for booking from " . $booking['email']);
        return true;
    } catch (PHPMailerException $e) {
        error_log("[mailer] Failed to send admin email: " . $mail->ErrorInfo);
        return false;
    }
}

function e(string $val): string
{
    return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
}

function buildCustomerEmailHtml(array $b): string
{
    return "
    <div style='font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; color: #2c2620;'>
        <h2 style='color:#8a6d3b;'>Your appointment is confirmed!</h2>
        <p>Hi " . e($b['name']) . ",</p>
        <p>Thank you for booking with <strong>VastuAura</strong>. Here are your appointment details:</p>
        <table style='width:100%; border-collapse: collapse;'>
            <tr><td style='padding:6px 0; color:#7a7368;'>Date</td><td style='padding:6px 0;'><strong>" . e($b['preferred_date']) . "</strong></td></tr>
            <tr><td style='padding:6px 0; color:#7a7368;'>Time</td><td style='padding:6px 0;'><strong>" . e($b['preferred_time']) . "</strong></td></tr>
            <tr><td style='padding:6px 0; color:#7a7368;'>Consultation Type</td><td style='padding:6px 0;'>" . e($b['consultation_type']) . "</td></tr>
            <tr><td style='padding:6px 0; color:#7a7368;'>Address</td><td style='padding:6px 0;'>" . e($b['address']) . "</td></tr>
            <tr><td style='padding:6px 0; color:#7a7368;'>Amount Paid</td><td style='padding:6px 0;'>₹" . e(number_format((float)$b['amount'], 2)) . "</td></tr>
            <tr><td style='padding:6px 0; color:#7a7368;'>Payment ID</td><td style='padding:6px 0;'>" . e($b['razorpay_payment_id']) . "</td></tr>
        </table>
        <p style='margin-top:24px;'>Our team will review your request and reach out to confirm final details.</p>
        <p style='color:#7a7368; font-size:.85rem;'>If you have any questions, just reply to this email.</p>
    </div>";
}

function buildAdminEmailHtml(array $b): string
{
    return "
    <div style='font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto;'>
        <h2>New Booking Received</h2>
        <table style='width:100%; border-collapse: collapse;'>
            <tr><td>Name</td><td><strong>" . e($b['name']) . "</strong></td></tr>
            <tr><td>Email</td><td>" . e($b['email']) . "</td></tr>
            <tr><td>Mobile</td><td>" . e($b['mobile']) . "</td></tr>
            <tr><td>Date</td><td>" . e($b['preferred_date']) . "</td></tr>
            <tr><td>Time</td><td>" . e($b['preferred_time']) . "</td></tr>
            <tr><td>Consultation Type</td><td>" . e($b['consultation_type']) . "</td></tr>
            <tr><td>Property Type</td><td>" . e($b['property_type'] ?? '') . "</td></tr>
            <tr><td>Address</td><td>" . e($b['address']) . "</td></tr>
            <tr><td>Amount Paid</td><td>₹" . e(number_format((float)$b['amount'], 2)) . "</td></tr>
            <tr><td>Razorpay Payment ID</td><td>" . e($b['razorpay_payment_id']) . "</td></tr>
        </table>
    </div>";
}

function buildCustomerEmailPlainText(array $b): string
{
    return "Your VastuAura appointment is confirmed.\n\n"
        . "Date: {$b['preferred_date']}\n"
        . "Time: {$b['preferred_time']}\n"
        . "Consultation Type: {$b['consultation_type']}\n"
        . "Address: {$b['address']}\n"
        . "Amount Paid: Rs. " . number_format((float)$b['amount'], 2) . "\n"
        . "Payment ID: {$b['razorpay_payment_id']}\n";
}


/**
 * Sends successful e-commerce order confirmation email to customer.
 */
function sendOrderConfirmationEmail(array $order): bool
{
    error_log("[mailer] Order data received: " . print_r($order, true));

    $mail = getConfiguredMailer();

    if ($mail === null) {
        error_log("[mailer] getConfiguredMailer() returned null — check mailer config/credentials.");
        return false;
    }

    try {
        $mail->addAddress($order['email'], $order['name']);

        $mail->Subject = "Your VastuAura Order #{$order['order_id']} is Confirmed";

        // $mail->Body = buildOrderConfirmationEmailHtml($order);
        $mail->AltBody = buildOrderConfirmationEmailPlainText($order);

        error_log("[mailer] Plain text body built:\n" . $mail->AltBody);

        $mail->send();

        error_log(
            "[mailer] Order confirmation email sent to " .
            $order['email'] .
            " for order #" .
            $order['order_id']
        );

        return true;

    } catch (\Throwable $e) {

        error_log(
            "[mailer] Failed to send order confirmation email: " .
            $e->getMessage() .
            " | PHPMailer ErrorInfo: " . ($mail->ErrorInfo ?? 'n/a')
        );

        return false;
    }
}






function buildOrderConfirmationEmailPlainText(array $o): string
{
    $text = "Your VastuAura order is confirmed.\n\n";

    $text .= "Order ID: #" . $o['order_id'] . "\n";
    $text .= "Order Date: " . $o['order_date'] . "\n";
    $text .= "Payment Method: " . $o['payment_method'] . "\n";

    if (!empty($o['razorpay_payment_id'])) {
        $text .= "Payment ID: " . $o['razorpay_payment_id'] . "\n";
    }

    $text .= "\n-----------------------------\n";
    $text .= "Order Items\n";
    $text .= "-----------------------------\n";

    foreach ($o['items'] as $item) {

        $itemTotal = (float)$item['price'] * (int)$item['quantity'];

        $text .= "Product: " . $item['name'] . "\n";
        $text .= "Quantity: " . $item['quantity'] . "\n";
        $text .= "Price: Rs. " . number_format((float)$item['price'], 2) . "\n";
        $text .= "Total: Rs. " . number_format($itemTotal, 2) . "\n";
        $text .= "-----------------------------\n";
    }

    $text .= "\nProducts Total: Rs. "
        . number_format((float)$o['total'], 2);

    $text .= "\nShipping: Rs. "
        . number_format((float)$o['shipping'], 2);

    $text .= "\nGrand Total: Rs. "
        . number_format((float)$o['grand_total'], 2);

    $text .= "\n\nThank you for shopping with VastuAura.";

    return $text;
}


/**
 * Sends a status-update email to the customer when an admin changes
 * their appointment status (approved / rejected / completed / cancelled).
 * Returns true/false. Never throws.
 */
function sendBookingStatusUpdateEmail(array $booking): bool
{
    $mail = getConfiguredMailer();
    if ($mail === null) return false;

    // Log target recipient before attempting to send, so we can verify
    // even if send() fails.
    error_log("[mailer] Attempting to send status update email to: " . $booking['email'] . " | status: " . ($booking['status'] ?? 'unknown'));

    try {
        $mail->addAddress($booking['email'], $booking['name']);
        $mail->Subject = "Update on your VastuAura appointment - " . ucfirst($booking['status']);

        $mail->Body    = buildStatusUpdateEmailHtml($booking);
        $mail->AltBody = buildStatusUpdateEmailPlainText($booking);

        $mail->send();
        error_log("[mailer] SUCCESS: Status update email sent to " . $booking['email'] . " (status: " . $booking['status'] . ")");
        return true;
    } catch (PHPMailerException $e) {
        error_log("[mailer] FAILED to send status update email to " . $booking['email'] . " | Error: " . $mail->ErrorInfo);
        return false;
    }
}

function statusColor(string $status): string
{
    $colors = [
        'approved'  => '#2e7d32',
        'rejected'  => '#c62828',
        'cancelled' => '#8a6d3b',
        'completed' => '#1565c0',
    ];
    return $colors[$status] ?? '#b58b00'; // pending / default
}

function buildStatusUpdateEmailHtml(array $b): string
{
    $status  = $b['status'] ?? 'pending';
    $color   = statusColor($status);
    $comment = trim($b['comment'] ?? '');

    return "
    <div style='font-family: Arial, sans-serif; max-width: 560px; margin: 0 auto; color: #2c2620;'>
        <h2 style='color:#8a6d3b;'>Appointment Update</h2>
        <p>Hi " . e($b['name']) . ",</p>
        <p>The status of your appointment with <strong>VastuAura</strong> has been updated to:</p>
        <p style='margin:16px 0;'>
            <span style='display:inline-block; padding:6px 14px; border-radius:20px; background:" . $color . "; color:#fff; font-weight:600; text-transform:capitalize;'>" . e($status) . "</span>
        </p>
        <table style='width:100%; border-collapse: collapse;'>
            " . (!empty($b['preferred_date']) ? "<tr><td style='padding:6px 0; color:#7a7368;'>Date</td><td style='padding:6px 0;'><strong>" . e($b['preferred_date']) . "</strong></td></tr>" : "") . "
            " . (!empty($b['preferred_time']) ? "<tr><td style='padding:6px 0; color:#7a7368;'>Time</td><td style='padding:6px 0;'><strong>" . e($b['preferred_time']) . "</strong></td></tr>" : "") . "
        </table>
        " . ($comment !== '' ? "<p style='margin-top:20px;'><strong>Note from our team:</strong><br>" . nl2br(e($comment)) . "</p>" : "") . "
        <p style='margin-top:24px;'>If you have any questions, just reply to this email.</p>
        <p style='color:#7a7368; font-size:.85rem;'>— The VastuAura Team</p>
    </div>";
}

function buildStatusUpdateEmailPlainText(array $b): string
{
    $status  = $b['status'] ?? 'pending';
    $comment = trim($b['comment'] ?? '');

    $text = "Your VastuAura appointment status has been updated to: " . ucfirst($status) . "\n\n";
    if (!empty($b['preferred_date'])) $text .= "Date: {$b['preferred_date']}\n";
    if (!empty($b['preferred_time'])) $text .= "Time: {$b['preferred_time']}\n";
    if ($comment !== '') $text .= "\nNote from our team: {$comment}\n";

    return $text;
}



