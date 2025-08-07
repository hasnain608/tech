<?php
session_start();

require_once '../config/db.php'; // Include the Database class

$db = new Database();
$conn = $db->getConnection();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Delete product securely
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: products.php");
    exit;
}

// Fetch all products
$stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Manage Products</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>🛒 Product Management</h2>
      <a href="add_product.php" class="btn btn-success">➕ Add New Product</a>
    </div>

    <?php if (count($products) > 0): ?>
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price (PKR)</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $row): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td>
                <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" width="70" height="70" class="rounded">
              </td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td>Rs. <?= number_format($row['price']) ?></td>
              <td><?= substr(htmlspecialchars($row['description']), 0, 50) ?>...</td>
              <td>
                <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">✏️ Edit</a>
                <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">🗑️ Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-warning">No products found.</div>
    <?php endif; ?>
  </div>
</body>
</html>
