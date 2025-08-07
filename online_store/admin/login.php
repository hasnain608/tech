<?php
session_start();
require '../config/db.php';
$db   = new Database();
$conn = $db->getConnection();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // **Unchanged** admin credentials check
    if ($email === "admin@shop.com" && $password === "admin123") {
        $_SESSION['admin'] = $email;
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid admin credentials";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 2rem;
      border-radius: 1rem;
      background: #ffffff;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    .login-card h2 {
      font-weight: 700;
      margin-bottom: 1.5rem;
      text-align: center;
      color: #343a40;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #667eea;
    }
    .btn-login {
      background: #667eea;
      border: none;
      transition: background .3s;
    }
    .btn-login:hover {
      background: #5a67d8;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h2>Admin Sign In</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-control" 
          placeholder="Shop@admin.com" 
          required>
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control" 
          placeholder="••••••••" 
          required>
      </div>
      <button type="submit" class="btn btn-login w-100 py-2 text-white">
        <i class="bi bi-lock-fill me-2"></i>Login
      </button>
    </form>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
