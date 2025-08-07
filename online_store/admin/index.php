<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #eef2f7;
      font-family: 'Segoe UI', sans-serif;
    }
    .panel-card {
      max-width: 500px;
      margin: 4rem auto;
      background: #fff;
      border-radius: 1rem;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .panel-header {
      background: #343a40;
      color: #fff;
      padding: 1.5rem;
      text-align: center;
      font-weight: 700;
      font-size: 1.5rem;
    }
    .list-group-item {
      border: none;
      padding: 1rem 1.5rem;
      transition: background .2s;
    }
    .list-group-item:hover {
      background: #f8f9fa;
    }
    .list-group-item a {
      color: #343a40;
      text-decoration: none;
      display: block;
      font-weight: 500;
    }
    .list-group-item a .bi {
      margin-right: .5rem;
      color: #667eea;
    }
  </style>
</head>
<body>
  <div class="card panel-card">
    <div class="panel-header">
      Welcome, Admin
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item">
        <a href="products.php">
          <i class="bi bi-box-seam"></i>Manage Products
        </a>
      </li>
      <li class="list-group-item">
        <a href="orders.php">
          <i class="bi bi-receipt"></i>View Orders
        </a>
      </li>
      <li class="list-group-item">
        <a href="users.php">
          <i class="bi bi-people-fill"></i>Manage Users
        </a>
      </li>
      <li class="list-group-item">
        <a href="logout.php">
          <i class="bi bi-box-arrow-right"></i>Logout
        </a>
      </li>
    </ul>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
