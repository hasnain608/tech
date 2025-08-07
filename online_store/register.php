<?php
require 'config/db.php';
require 'config/mail.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $token = bin2hex(random_bytes(16));

    $db = new Database();
    $conn = $db->getConnection();

    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $_SESSION['error'] = "Email already exists";
    } else {
        $insert = $conn->prepare("INSERT INTO users (name, email, password, token, is_verified) VALUES (?, ?, ?, ?, 0)");
        $insert->execute([$name, $email, $password, $token]);

        if (sendVerificationEmail($email, $token)) {
            $_SESSION['success'] = "Registration successful! Please check your email to verify.";
        } else {
            $_SESSION['error'] = "Email sending failed.";
        }
    }

    header("Location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .form-container {
            max-width: 420px;
            margin: 80px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .form-container h2 {
            margin-bottom: 25px;
            text-align: center;
        }
        .form-container .form-control {
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2>Create Account</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" required class="form-control" placeholder="John Doe">
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" required class="form-control" placeholder="you@example.com">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" required class="form-control" placeholder="********">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </div>
            <p class="mt-3 text-center">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </form>
    </div>
</div>
</body>
</html>
