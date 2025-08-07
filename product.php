<?php
session_start(); // ✅ Required to access $_SESSION
require 'config/db.php';
$db   = new Database();
$conn = $db->getConnection();

$id   = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['name']) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS + Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    /* Page background */
    body {
      background: #f0f2f5;
      font-family: 'Segoe UI', sans-serif;
    }

    /* Container */
    .product-detail {
      max-width: 1000px;
      margin: 4rem auto;
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      align-items: center;
    }

    /* Image panel */
    .detail-img {
      flex: 1 1 400px;
      overflow: hidden;
      border-radius: 1rem;
      box-shadow: 0 12px 36px rgba(0,0,0,0.1);
      position: relative;
    }
    .detail-img img {
      width: 100%;
      height: auto;
      display: block;
      transition: transform .6s ease;
    }
    .detail-img::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(to bottom right, rgba(255,255,255,0.1), rgba(0,0,0,0.1));
      pointer-events: none;
    }
    .detail-img:hover img {
      transform: scale(1.1) rotate(1deg);
    }

    /* Info panel */
    .detail-info {
      flex: 1 1 400px;
      background: #fff;
      border-radius: 1rem;
      padding: 2.5rem;
      box-shadow: 0 12px 36px rgba(0,0,0,0.08);
      position: relative;
      overflow: hidden;
    }
    .detail-info::after {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at top left, rgba(40,167,69,0.15), transparent);
      transform: rotate(25deg);
      transition: transform .8s;
      pointer-events: none;
    }
    .detail-info:hover::after {
      transform: rotate(0deg) translate(-10%, 10%);
    }

    /* Title */
    .detail-info h2 {
      font-size: 2.5rem;
      font-weight: 700;
      margin-bottom: 1rem;
      position: relative;
      display: inline-block;
    }
    .detail-info h2::after {
      content: '';
      width: 60px;
      height: 4px;
      background: #28a745;
      position: absolute;
      bottom: -8px;
      left: 0;
      border-radius: 2px;
    }

    /* Description */
    .detail-info p {
      color: #555;
      line-height: 1.6;
      margin-bottom: 1.5rem;
    }

    /* Price badge */
    .price-badge {
      display: inline-block;
      font-size: 2rem;
      font-weight: 700;
      color: #fff;
      background: linear-gradient(45deg, #28a745, #218838);
      padding: .2rem 1.2rem;
      border-radius: 3rem;
      margin-bottom: 2rem;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      transition: transform .3s;
    }
    .price-badge:hover {
      transform: scale(1.1);
    }

    /* Buttons */
    .btn-extreme {
      position: relative;
      overflow: hidden;
      border: none;
      font-weight: 600;
      padding: .75rem 1.5rem;
      border-radius: 3rem;
      transition: background .4s, box-shadow .4s;
    }
    .btn-extreme::before {
      content: '';
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,0.2);
      transform: scale(0);
      transition: transform .5s;
      pointer-events: none;
      border-radius: inherit;
    }
    .btn-extreme:hover::before {
      transform: scale(1);
    }

    .btn-cart {
      background: #28a745;
      color: #fff;
      margin-right: 1rem;
      box-shadow: 0 8px 24px rgba(40,167,69,0.3);
    }
    .btn-cart:hover {
      background: #218838;
      box-shadow: 0 12px 28px rgba(40,167,69,0.4);
    }

    .btn-wish {
      background: #ffc107;
      color: #212529;
      box-shadow: 0 8px 24px rgba(255,193,7,0.3);
    }
    .btn-wish:hover {
      background: #e0a800;
      box-shadow: 0 12px 28px rgba(255,193,7,0.4);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .product-detail {
        flex-direction: column;
      }
      .detail-img, .detail-info {
        flex: 1 1 100%;
      }
    }
  </style>
</head>
<body>

  <div class="product-detail">
    <div class="detail-img">
      <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>
    <div class="detail-info">
      <h2><?= htmlspecialchars($product['name']) ?></h2>
      <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

      <div class="price-badge">
        Rs <?= number_format($product['price']) ?>
      </div>

      <div>
        <a href="add_to_cart.php?id=<?= $product['id'] ?>"
           class="btn btn-extreme btn-cart">
          <i class="bi bi-cart-plus-fill me-2"></i>Add to Cart
        </a>
        <?php if (isset($_SESSION['user'])): ?>
          <a href="add_to_wishlist.php?id=<?= $product['id'] ?>"
             class="btn btn-extreme btn-wish">
            <i class="bi bi-heart-fill me-2"></i>Add to Wishlist
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
