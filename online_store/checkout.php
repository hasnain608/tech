<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require 'config/db.php';
$db   = new Database();
$conn = $db->getConnection();

$user_id = $_SESSION['user']['id'];
$cart    = $_SESSION['cart'] ?? [];

$products = [];
$total    = 0;
$details  = [];

// Fetch products in cart
if (!empty($cart)) {
    $ids    = implode(',', array_keys($cart));
    $stmt   = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();

    foreach ($products as $p) {
        $qty      = $cart[$p['id']];
        $subtotal = $qty * $p['price'];
        $total   += $subtotal;
        $details[] = "{$p['name']} x {$qty}";
    }
}

// Place order
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($products)) {
    $summary = implode(", ", $details);
    $insert  = $conn->prepare(
        "INSERT INTO orders (user_id, details, total) VALUES (?, ?, ?)"
    );
    $insert->execute([$user_id, $summary, $total]);

    unset($_SESSION['cart']);
    header("Location: thank_you.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background: #f0f2f5;
    }
    .checkout-card {
      max-width: 900px;
      margin: 3rem auto;
      border: none;
      border-radius: 1rem;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    .checkout-header {
      background: #2a2e35;
      color: #fff;
      padding: 1.5rem 2rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .05em;
    }
    .table thead th {
      border-bottom: 2px solid #dee2e6;
      text-transform: uppercase;
      font-size: .85rem;
    }
    .table-hover tbody tr:hover {
      background: #f1f3f5;
    }
    .product-img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: .5rem;
    }
    .place-btn {
      transition: transform .2s;
    }
    .place-btn:hover {
      transform: scale(1.03);
    }
  </style>
</head>
<body>
  <div class="card checkout-card">
    <div class="checkout-header text-center">
      Confirm Your Order
    </div>
    <div class="card-body bg-white p-4">

      <?php if (!empty($products)): ?>
        <div class="table-responsive mb-4">
          <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th>Image</th>
                <th>Product</th>
                <th class="text-end">Price</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $p):
                $qty      = $cart[$p['id']];
                $subtotal = $qty * $p['price'];

                // Build image URL
                $filename   = $p['image'] ?? '';
                $serverPath = __DIR__ . '/uploads/' . $filename;
                if ($filename && file_exists($serverPath)) {
                  $imgSrc = 'uploads/' . rawurlencode($filename);
                } else {
                  $imgSrc = 'https://via.placeholder.com/60';
                }
              ?>
              <tr>
                <td>
                  <img
                    src="<?= htmlspecialchars($imgSrc) ?>"
                    alt="<?= htmlspecialchars($p['name']) ?>"
                    class="product-img"
                    onerror="this.onerror=null;this.src='https://via.placeholder.com/60';"
                  >
                </td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td class="text-end"><?= number_format($p['price']) ?> PKR</td>
                <td class="text-center"><?= $qty ?></td>
                <td class="text-end"><?= number_format($subtotal) ?> PKR</td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="text-center mb-4">
          <h4 class="fw-bold">Total: <?= number_format($total) ?> PKR</h4>
        </div>

        <form action="thank_you.php" method="POST">
          <button   type="submit" class="btn btn-success btn-lg w-100 place-btn py-3">
            Place Order
          </button>
        </form>

      <?php else: ?>
        <div class="alert alert-warning text-center mb-0">
          Your cart is empty.
        </div>
      <?php endif; ?>

    </div>
  </div>


</body>
</html>
