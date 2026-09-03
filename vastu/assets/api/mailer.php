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
    $mail = getConfiguredMailer();

    if ($mail === null) {
        return false;
    }

    try {
        $mail->addAddress($order['email'], $order['name']);

        $mail->Subject = "Your VastuAura Order #{$order['order_id']} is Confirmed";

        // $mail->Body = buildOrderConfirmationEmailHtml($order);
        $mail->AltBody = buildOrderConfirmationEmailPlainText($order);

        $mail->send();

        error_log(
            "[mailer] Order confirmation email sent to " .
            $order['email'] .
            " for order #" .
            $order['order_id']
        );

        return true;

    } catch (PHPMailerException $e) {

        error_log(
            "[mailer] Failed to send order confirmation email: " .
            $mail->ErrorInfo
        );

        return false;
    }
}


// function buildOrderConfirmationEmailHtml(array $o): string
// {
//     $itemsHtml = '';

//     foreach ($o['items'] as $item) {

//         $itemTotal = (float)$item['price'] * (int)$item['quantity'];

//         $itemsHtml .= "
//             <tr>
//                 <td style='padding:8px 0;'>
//                     " . e($item['product_name']) . "
//                 </td>

//                 <td style='padding:8px 0; text-align:center;'>
//                     " . e((string)$item['quantity']) . "
//                 </td>

//                 <td style='padding:8px 0; text-align:right;'>
//                     ₹" . number_format((float)$item['price'], 2) . "
//                 </td>

//                 <td style='padding:8px 0; text-align:right;'>
//                     ₹" . number_format($itemTotal, 2) . "
//                 </td>
//             </tr>
//         ";
//     }

//     return "
//     <div style='font-family:Arial,sans-serif; max-width:600px; margin:0 auto; color:#2c2620;'>

//         <h2 style='color:#8a6d3b;'>
//             🎉 Your Order is Confirmed!
//         </h2>

//         <p>
//             Hi <strong>" . e($o['name']) . "</strong>,
//         </p>

//         <p>
//             Thank you for shopping with <strong>VastuAura</strong>.
//             Your order has been successfully placed and your payment has been received.
//         </p>

//         <div style='background:#f8f5ef; padding:15px; margin:20px 0;'>
//             <strong>Order ID:</strong> #" . e((string)$o['order_id']) . "<br>
//             <strong>Order Date:</strong> " . e($o['order_date']) . "<br>
//             <strong>Payment Method:</strong> " . e($o['payment_method']) . "
//         </div>

//         <h3>Order Details</h3>

//         <table style='width:100%; border-collapse:collapse;'>
//             <thead>
//                 <tr style='border-bottom:1px solid #ddd;'>
//                     <th style='text-align:left; padding:8px 0;'>Product</th>
//                     <th style='text-align:center; padding:8px 0;'>Qty</th>
//                     <th style='text-align:right; padding:8px 0;'>Price</th>
//                     <th style='text-align:right; padding:8px 0;'>Total</th>
//                 </tr>
//             </thead>

//             <tbody>
//                 {$itemsHtml}
//             </tbody>
//         </table>

//         <div style='margin-top:20px; border-top:1px solid #ddd; padding-top:15px;'>

//             <p style='text-align:right;'>
//                 <strong>Products Total:</strong>
//                 ₹" . number_format((float)$o['total'], 2) . "
//             </p>

//             <p style='text-align:right;'>
//                 <strong>Shipping:</strong>
//                 ₹" . number_format((float)$o['shipping'], 2) . "
//             </p>

//             <p style='text-align:right; font-size:18px;'>
//                 <strong>Grand Total:</strong>
//                 ₹" . number_format((float)$o['grand_total'], 2) . "
//             </p>

//         </div>

//         " . (
//             !empty($o['razorpay_payment_id'])
//             ? "
//             <p style='margin-top:20px;'>
//                 <strong>Payment ID:</strong>
//                 " . e($o['razorpay_payment_id']) . "
//             </p>
//             "
//             : ""
//         ) . "

//         <p style='margin-top:25px;'>
//             We will process your order shortly and keep you updated about its status.
//         </p>

//         <p style='color:#7a7368; font-size:13px;'>
//             Thank you for choosing VastuAura.
//         </p>

//     </div>";
// }



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



