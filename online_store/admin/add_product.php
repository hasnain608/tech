<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
require '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    $image = $_FILES['image']['name'];
    $path = "../uploads/" . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $path);

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $desc, $price, $image]);
    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Add Product</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-3"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
        <div class="mb-3"><label>Price</label><input type="number" step="0.01" name="price" class="form-control" required></div>
        <div class="mb-3"><label>Image</label><input type="file" name="image" class="form-control" required></div>
        <button type="submit" class="btn btn-primary">Add</button>
    </form>
</div>
</body>
</html>
