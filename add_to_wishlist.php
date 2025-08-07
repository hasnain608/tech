<?php
session_start();
require 'config/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
    // Store redirect target with product ID
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $_SESSION['redirect_after_login'] = "add_to_wishlist.php?id=" . intval($_GET['id']);
    }
    $_SESSION['error'] = "Please login to add to wishlist.";
    header("Location: login.php");
    exit;
}

// Validate product ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = intval($_GET['id']);
$user_id = $_SESSION['user']['id'];

$db = new Database();
$conn = $db->getConnection();

// Check if product exists
$stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$product_id]);
if ($stmt->rowCount() === 0) {
    $_SESSION['error'] = "Product not found.";
    header("Location: index.php");
    exit;
}

// Check if already in wishlist
$stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$user_id, $product_id]);

if ($stmt->rowCount() > 0) {
    $_SESSION['message'] = "Already in your wishlist.";
} else {
    // Add to wishlist
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $product_id]);
    $_SESSION['message'] = "Added to your wishlist.";
}

header("Location: index.php#products");
exit;
