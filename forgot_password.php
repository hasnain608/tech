<?php
session_start();
require 'config/db.php';
require 'config/mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        $token = bin2hex(random_bytes(50));
        $update = $conn->prepare("UPDATE users SET token = ? WHERE email = ?");
        $update->execute([$token, $email]);

        if (sendResetEmail($email, $token)) {
            $_SESSION['success'] = "Reset link sent to your email.";
        } else {
            $_SESSION['error'] = "Failed to send email. Try again later.";
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
    }

    header("Location: forgot_password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h4 class="text-center mb-3">Forgot Password</h4>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Enter your email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button class="btn btn-primary w-100">Send Reset Link</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
