<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

require '../config/db.php';
$db = new Database();
$conn = $db->getConnection();

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image);
    } else {
        $image = $product['image'];
    }

    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $desc, $price, $image, $id]);
    header("Location: products.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Product</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label>Name</label><input type="text" name="name" value="<?= $product['name'] ?>" class="form-control" required></div>
        <div class="mb-3"><label>Description</label><textarea name="description" class="form-control"><?= $product['description'] ?></textarea></div>
        <div class="mb-3"><label>Price</label><input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" class="form-control" required></div>
        <div class="mb-3"><label>Image</label><input type="file" name="image" class="form-control"></div>
        <img src="../uploads/<?= $product['image'] ?>" width="100">
        <br><br>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
</body>
</html>
