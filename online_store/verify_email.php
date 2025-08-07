<?php
require 'config/db.php';
session_start();

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $db = new Database();
    $conn = $db->getConnection();

    // Check if token exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE token = ?");
    $stmt->execute([$token]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Update user as verified
        $update = $conn->prepare("UPDATE users SET is_verified = 1, token = NULL WHERE id = ?");
        $update->execute([$user['id']]);

        // Auto-login the user after verification
        $_SESSION['user'] = $user;

        // Redirect to home page
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['error'] = "Invalid or expired verification link.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Verification token is missing.";
    header("Location: login.php");
    exit;
}
