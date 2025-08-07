<?php
session_start();
require 'config/db.php';

if (!isset($_GET['token'])) {
    $_SESSION['error'] = "Invalid reset link.";
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$token = $_GET['token'];
$stmt = $conn->prepare("SELECT * FROM users WHERE token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Invalid or expired token.";
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $newPass = md5($_POST['password']);
    $update = $conn->prepare("UPDATE users SET password = ?, token = NULL WHERE id = ?");
    $update->execute([$newPass, $user['id']]);

    $_SESSION['success'] = "Password reset successfully.";
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm p-4">
                <h4 class="text-center mb-3">Reset Password</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button class="btn btn-success w-100">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
