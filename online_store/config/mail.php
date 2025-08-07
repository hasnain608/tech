<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hasnaintalibawan@gmail.com';
        $mail->Password   = 'vkimemvfqrdpqyoo'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender & Recipient
        $mail->setFrom('hasnaintalibawan@gmail.com', 'Online Shopping');
        $mail->addAddress($email);

        // ✅ Use localhost for local dev
        $host = 'localhost';
        $link = "http://$host/online_store/verify_email.php?token=$token";

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email';
        $mail->Body    = "
            <h3>Welcome to Online Shopping!</h3>
            <p>Thank you for registering. Please verify your email by clicking the link below:</p>
            <a href='$link'>Verify Now</a>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
function sendResetEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hasnaintalibawan@gmail.com';
        $mail->Password   = 'vkimemvfqrdpqyoo'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Sender & Recipient
        $mail->setFrom('hasnaintalibawan@gmail.com', 'Online Shopping');
        $mail->addAddress($email);

        // Reset Password URL
        $host = 'localhost'; // ✅ or your domain if hosted
        $link = "http://$host/online_store/reset_password.php?token=$token";

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password';
        $mail->Body    = "
            <h3>Password Reset Request</h3>
            <p>We received a request to reset your password.</p>
            <p>Click the link below to set a new password:</p>
            <a href='$link'>$link</a>
            <p>If you didn't request this, please ignore this email.</p>
        ";

        return $mail->send();
    } catch (Exception $e) {
        error_log("Reset Email failed: {$mail->ErrorInfo}");
        return false;
    }
}
