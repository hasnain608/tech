<?php
session_start();
require 'config/db.php';
$db = new Database();
$conn = $db->getConnection();

// Cart in session (for guests)
$cart = $_SESSION['cart'] ?? [];
$products = [];

if (!empty($cart)) {
    $ids = implode(',', array_keys($cart));
    $stmt = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart — MyShop</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background-color: #f2f4f7;
      font-family: 'Segoe UI', sans-serif;
    }
    .cart-card {
      border: none;
      border-radius: 1rem;
      overflow: hidden;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    .table thead {
      background-color: #343a40;
      color: #fff;
    }
    .table tbody tr:hover {
      background-color: #e9ecef;
    }
    .product-thumb {
      width: 60px;
      height: 60px;
      object-fit: cover;
      border-radius: 0.5rem;
    }
    .checkout-bar {
      background-color: #fff;
      padding: 1rem 1.5rem;
      border-radius: 0.75rem;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .checkout-bar h4 {
      font-size: 1.25rem;
      font-weight: 600;
    }
    .btn-checkout {
      min-width: 180px;
    }
    .btn-shop {
      min-width: 180px;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-10">

      <div class="card cart-card mb-4">
        <div class="card-body p-4">
          <h2 class="text-center text-primary mb-4">
            <i class="bi bi-cart-fill me-2"></i>Your Shopping Cart
          </h2>

          <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= $_SESSION['message']; unset($_SESSION['message']); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <?php if (count($products) > 0): ?>
            <div class="table-responsive">
              <table class="table table-striped table-hover align-middle mb-0">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $total = 0; ?>
                  <?php foreach ($products as $product): 
                    $qty      = $cart[$product['id']];
                    $subtotal = $qty * $product['price'];
                    $total   += $subtotal;
                  ?>
                  <tr>
                    <td class="d-flex align-items-center">
                      <img src="uploads/<?= htmlspecialchars($product['image']) ?>" 
                           alt="" class="product-thumb me-3">
                      <div>
                        <div class="fw-semibold"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="text-muted small"><?= htmlspecialchars($product['description']) ?></div>
                      </div>
                    </td>
                    <td>Rs <?= number_format($product['price']) ?></td>
                    <td>
  <form method="POST" action="update_cart.php" class="d-flex justify-content-center">
    <input type="hidden" name="id" value="<?= $product['id'] ?>">
    <div class="input-group input-group-sm mx-auto" style="width: 100px;">
      <button 
        class="btn btn-outline-secondary" 
        type="submit" 
        name="action" 
        value="decrease"
        <?= $qty <= 1 ? 'disabled' : '' ?>>
        –
      </button>
      <input 
        type="text" 
        name="quantity" 
        value="<?= $qty ?>" 
        class="form-control text-center p-0" 
        style="max-width: 40px;">
      <button 
        class="btn btn-outline-secondary" 
        type="submit" 
        name="action" 
        value="increase">
        +
      </button>
    </div>
  </form>
</td>

                    <td class="text-end">Rs <?= number_format($subtotal) ?></td>
                    <td class="text-center">
                      <a href="remove_from_cart.php?id=<?= $product['id'] ?>" 
                         class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash3"></i>
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div class="checkout-bar d-flex justify-content-between align-items-center mt-4">
              <h4 class="mb-0">
                Grand Total: 
                <span class="text-success">Rs <?= number_format($total) ?></span>
              </h4>
              <div class="d-flex gap-3">
                <a href="index.php" class="btn btn-outline-secondary btn-shop">
                  <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                </a>
                <a href="checkout.php" class="btn btn-success btn-checkout">
                  <i class="bi bi-credit-card me-1"></i> Proceed to Checkout
                </a>
              </div>
            </div>

          <?php else: ?>
            <div class="text-center py-5">
              <i class="bi bi-cart-x display-1 text-muted"></i>
              <h4 class="mt-3 text-secondary">Your cart is empty.</h4>
              <a href="index.php" class="btn btn-primary btn-lg mt-3">
                <i class="bi bi-arrow-left me-2"></i> Continue Shopping
              </a>
            </div>
          <?php endif; ?>

        </div>
      </div>

    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
