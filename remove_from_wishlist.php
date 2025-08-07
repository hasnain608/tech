<?php
require 'config/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = "You must be logged in to perform this action.";
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['message'] = "Invalid product ID.";
    header("Location: wishlist.php");
    exit;
}

$productId = (int)$_GET['id'];
$userId = $_SESSION['user']['id'];

$db = new Database();
$conn = $db->getConnection();

// Remove from wishlist
$stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, $productId]);

$_SESSION['message'] = "Product removed from wishlist.";
header("Location: wishlist.php");
exit;
