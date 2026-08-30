<?php
/**
 * SMTP Settings
 * -------------
 * Fill these in with your actual SMTP provider details.
 *
 * GMAIL EXAMPLE (most common for testing):
 *   SMTP_HOST     = smtp.gmail.com
 *   SMTP_PORT     = 587
 *   SMTP_SECURE   = tls
 *   SMTP_USERNAME = youraddress@gmail.com
 *   SMTP_PASSWORD = a 16-character "App Password" (NOT your normal Gmail
 *                   password - Gmail blocks normal passwords for SMTP apps).
 *                   Generate one at: https://myaccount.google.com/apppasswords
 *                   (requires 2-Step Verification to be turned on first)
 *
 * OUTLOOK/OFFICE365 EXAMPLE:
 *   SMTP_HOST     = smtp.office365.com
 *   SMTP_PORT     = 587
 *   SMTP_SECURE   = tls
 *
 * OTHER PROVIDERS (SendGrid, Mailgun, Brevo/Sendinblue, your web host, etc.)
 * will give you their own host/port/username/password - use those instead.
 */

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');            // 'tls' or 'ssl'
define('SMTP_USERNAME', 'khaleeknath@gmail.com');
define('SMTP_PASSWORD', 'qmel gzcw yizh mhqr');

// What shows up as the "From" name/address on emails sent to customers
define('MAIL_FROM_EMAIL', 'khaleeknath@gmail.com'); // usually same as SMTP_USERNAME
define('MAIL_FROM_NAME', 'VastuAura');

// Where booking notifications should be sent to YOU (the business owner)
define('ADMIN_NOTIFICATION_EMAIL', 'khaleeknath@gmail.com');