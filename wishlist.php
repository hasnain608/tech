<?php
require 'config/db.php';
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = "Please login to view your wishlist.";
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();
$userId = $_SESSION['user']['id'];

$stmt = $conn->prepare("
    SELECT p.* 
    FROM products p
    JOIN wishlist w ON p.id = w.product_id
    WHERE w.user_id = ?
");
$stmt->execute([$userId]);
$wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Your Wishlist</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <style>
    body {
      background-color: #f2f4f7;
      font-family: 'Segoe UI', sans-serif;
    }

    .wishlist-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .product-card {
      border: none;
      border-radius: 1rem;
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      background-color: #fff;
      height: 100%;
      display: flex;
      flex-direction: column;
    }

    .product-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }

    .product-card img {
      height: 200px;
      object-fit: cover;
      border-top-left-radius: 1rem;
      border-top-right-radius: 1rem;
      width: 100%;
    }

    .product-card .card-body {
      padding: 1.25rem;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }

    .product-card .card-title {
      font-size: 1.125rem;
      font-weight: 600;
    }

    .product-card .card-text {
      font-size: 0.9rem;
      color: #6c757d;
      margin-bottom: 1rem;
      height: 2.7rem;
      overflow: hidden;
    }

    .product-card .price {
      font-weight: 700;
      color: #20c997;
      margin-bottom: 1rem;
    }

    .btn-remove {
      min-width: 100%;
    }

    .continue-shopping {
      min-width: 180px;
    }

    .btn-sm {
      font-size: 0.9rem;
      padding: 0.45rem 0.75rem;
    }

    @media (max-width: 768px) {
      .continue-shopping {
        width: 100%;
        text-align: center;
      }
    }

    @media (max-width: 576px) {
      .wishlist-header {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
</head>

<body>
  <div class="container py-5">
    <div class="wishlist-header">
      <h2 class="fw-bold text-primary">Your Wishlist</h2>
      <a href="index.php" class="btn btn-outline-secondary continue-shopping">
        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
      </a>
    </div>

    <?php if (count($wishlist) === 0): ?>
<div class="d-flex justify-content-center my-5">
  <div class="p-5 bg-light border border-2 border-danger rounded-4 text-center shadow-lg" style="max-width: 600px; width: 100%;">
    <div class="mb-3">
      <i class="fas fa-heart-broken fa-3x text-danger"></i>
    </div>
    <h4 class="fw-bold text-danger">Oops! Wishlist is Empty</h4>
    <p class="text-muted">Looks like you haven't added any products to your wishlist yet.</p>
    <a href="index.php" class="btn btn-outline-danger px-4 rounded-pill mt-3">
      <i class="fas fa-shopping-bag me-2"></i> Browse Products
    </a>
  </div>
</div>
   <?php else: ?>
  <div class="row g-4">
    <?php foreach ($wishlist as $product): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card product-card h-100">

          <img src="uploads/<?= htmlspecialchars($product['image']) ?>" 
               alt="<?= htmlspecialchars($product['name']) ?>" 
               class="img-fluid"
               onerror="this.onerror=null;this.src='images/placeholder.jpg';">

          <div class="card-body d-flex flex-column">
            <h5 class="card-title text-truncate"><?= htmlspecialchars($product['name']) ?></h5>
            <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
            <div class="price mb-3">$<?= number_format($product['price'], 2) ?></div>

            <div class="mt-auto d-flex flex-column gap-2">
              <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary w-100">
                <i class="fas fa-eye me-1"></i> View
              </a>
              <a href="remove_from_wishlist.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-danger w-100">
                <i class="fas fa-trash me-1"></i> Remove
              </a>
            </div>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
