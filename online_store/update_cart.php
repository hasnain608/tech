<?php
session_start();

// must have cart and posted id
if (!isset($_SESSION['cart'], $_POST['id'], $_POST['action'])) {
    header('Location: cart.php');
    exit;
}

$id     = (string)$_POST['id'];
$action = $_POST['action'];
// sanitize posted qty if provided
$posted = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;

// ensure product exists in cart
if (!isset($_SESSION['cart'][$id])) {
    header('Location: cart.php');
    exit;
}

$current = (int)$_SESSION['cart'][$id];

switch ($action) {
    case 'decrease':
        $newQty = max(1, $current - 1);
        break;
    case 'increase':
        $newQty = $current + 1;
        break;
    default:
        // direct edit in input field
        $newQty = max(1, $posted ?? $current);
}
$_SESSION['cart'][$id] = $newQty;

// redirect back to cart
header('Location: cart.php');
exit;
