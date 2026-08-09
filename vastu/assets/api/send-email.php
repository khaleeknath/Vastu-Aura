<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/Exception.php';
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';

function sendStatusEmail($to, $name, $status, $comment) {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'yourgmail@gmail.com';   // your Gmail
        $mail->Password = 'your_app_password';      // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('yourgmail@gmail.com', 'VastuAura');
        $mail->addAddress($to, $name);

        $mail->isHTML(true);
        $mail->Subject = "Appointment Status Updated";

        $mail->Body = "
            <h3>Hello $name,</h3>
            <p>Your appointment status has been updated.</p>
            <p><b>Status:</b> $status</p>
            <p><b>Comment:</b> $comment</p>
            <br>
            <p>Thanks,<br>VastuAura Team</p>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
?>