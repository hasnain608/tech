<?php
require 'config/db.php';
require 'config/mail.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch();
        if ($user['is_verified'] == 1) {
            $_SESSION['message'] = "Email already verified.";
        } else {
            $token = bin2hex(random_bytes(16));
            $update = $conn->prepare("UPDATE users SET token = ? WHERE email = ?");
            $update->execute([$token, $email]);

            sendVerificationEmail($email, $token);
            $_SESSION['message'] = "Verification link has been resent!";
        }
    } else {
        $_SESSION['message'] = "Email not found.";
    }

    header("Location: resend_verification.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resend Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2>Resend Verification Link</h2>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info"><?= $_SESSION['message']; unset($_SESSION['message']); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" required class="form-control">
        </div>
        <button class="btn btn-primary">Resend</button>
    </form>
</div>
</body>
</html>
