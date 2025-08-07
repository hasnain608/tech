<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$userId = $_SESSION['user']['id'];
$cart = $_SESSION['cart'];

// Fetch product details
$productIds = implode(',', array_keys($cart));
$stmt = $conn->query("SELECT id, name, price FROM products WHERE id IN ($productIds)");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build order details
$orderItems = [];
$totalPrice = 0;

foreach ($products as $product) {
    $qty = $cart[$product['id']];
    $subtotal = $product['price'] * $qty;
    $totalPrice += $subtotal;

    $orderItems[] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $qty
    ];
}

// Insert order
$productDetailsJson = json_encode($orderItems);

$stmt = $conn->prepare("INSERT INTO orders (user_id, product_details, total_price) VALUES (?, ?, ?)");
$stmt->execute([$userId, $productDetailsJson, $totalPrice]);

// Clear cart
unset($_SESSION['cart']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Placed</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }

    .thank-you-box {
      background: white;
      padding: 3rem;
      border-radius: 1rem;
      text-align: center;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      max-width: 400px;
    }

    .thank-you-box h1 {
      font-size: 2rem;
      color: #28a745;
      margin-bottom: 1rem;
    }

    .thank-you-box p {
      font-size: 1.1rem;
      color: #6c757d;
    }

    .countdown {
      font-size: 1.2rem;
      font-weight: 600;
      color: #343a40;
      margin-top: 1.5rem;
    }
  </style>
</head>
<body>
  <div class="thank-you-box">
    <h1>🎉 Your Order Has Been Placed!</h1>
    <p>Thank you for shopping with us.</p>
    <div class="countdown">
      Redirecting to home in <span id="timer">3</span>...
    </div>
  </div>

  <script>
    let timer = 3;
    const countdownEl = document.getElementById('timer');

    const interval = setInterval(() => {
      timer--;
      countdownEl.textContent = timer;

      if (timer <= 0) {
        clearInterval(interval);
        window.location.href = 'index.php';
      }
    }, 1000);
  </script>
</body>
</html>
