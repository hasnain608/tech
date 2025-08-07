<?php
session_start();
require 'config/db.php';
$db = new Database();
$conn = $db->getConnection();

// Track search
$searchTerm = isset($_GET['query']) ? trim($_GET['query']) : '';
if ($searchTerm !== '') {
    if (!isset($_SESSION['search_history'])) $_SESSION['search_history'] = [];
    if (!in_array($searchTerm, $_SESSION['search_history'])) {
        $_SESSION['search_history'][] = $searchTerm;
    }
}

// Clear search history
if (isset($_POST['clear_history'])) {
    unset($_SESSION['search_history']);
    header("Location: index.php");
    exit;
}

// Fetch products
if (!empty($_SESSION['search_history'])) {
    $placeholders = implode(',', array_fill(0, count($_SESSION['search_history']), '?'));
    $query = "SELECT * FROM products WHERE ";
    foreach ($_SESSION['search_history'] as $i => $term) {
        $query .= ($i > 0 ? " OR " : "") . "(name LIKE ? OR description LIKE ?)";
    }

    $stmt = $conn->prepare($query . " ORDER BY id DESC");
    $params = [];
    foreach ($_SESSION['search_history'] as $term) {
        $params[] = "%$term%";
        $params[] = "%$term%";
    }
    $stmt->execute($params);
    $products = $stmt->fetchAll();
} else {
    $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MyShop - Online Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar-brand,
        .nav-link {
            color: #fff !important;
        }
        .search-form input {
            border-radius: 20px;
            padding: 5px 15px;
        }
        .search-form button {
            border-radius: 20px;
        }
.hero {
    background-image: url('https://unsplash.com/photos/qnKhZJPKFD8/download?ixid=M3wxMjA3fDB8MXxzZWFyY2h8MTR8fHNob3B8ZW58MHx8fHwxNzUzOTQ2NTMzfDA&force=true'); /* working Unsplash direct image */
    background-size: cover;
    background-position: center;
    height: 100vh;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* dark overlay */
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    padding: 2rem;
}

.hero h1 {
    font-size: 4rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.hero p {
    font-size: 1.5rem;
    font-weight: 300;
}
.search-form input::placeholder {
    color: #6c757d;
    font-style: italic;
}
.search-form input:focus {
    box-shadow: none;
    outline: none;
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

  /* Container tweaks */
  #products { 
    background: #f7f8fa; 
    padding-top: 6rem; 
    padding-bottom: 6rem; 
  }
  #products .display-5 {
    position: relative;
    font-size: 3rem;
    margin-bottom: 3rem;
  }
  #products .display-5::after {
    content: '';
    width: 80px;
    height: 4px;
    background: #28a745;
    display: block;
    margin: 0.5rem auto 0;
    border-radius: 2px;
  }

  /* Card */
  .product-card {
    background: #fff;
    transition: transform .3s, box-shadow .3s;
    position: relative;
    overflow: hidden;
  }
  .product-card::before {
    content: '';
    position: absolute;
    top: -75%;
    left: -75%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, rgba(40,167,69,0.15), rgba(0,123,255,0.15));
    transform: rotate(25deg);
    transition: transform .7s;
    pointer-events: none;
    z-index: 0;
  }
  .product-card:hover::before {
    transform: rotate(0deg) translate(10%, 10%);
  }
  .product-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  }

  /* Ensure content sits above the overlay */
  .product-card * {
    position: relative;
    z-index: 1;
  }

  /* Image */
  .product-card img {
    border-bottom: 4px solid #28a745;
    transition: transform .3s;
  }
  .product-card:hover img {
    transform: scale(1.1);
  }

  /* Title & description */
  .product-card .card-title {
    color: #343a40;
    font-size: 1.25rem;
    transition: color .3s;
  }
  .product-card:hover .card-title {
    color: #28a745;
  }
  .product-card .card-text {
    color: #6c757d;
    font-size: .9rem;
    min-height: 3rem;
  }

  /* Price badge */
  .product-card .badge {
    background: #28a745;
    color: #fff;
    font-size: 1rem;
    transition: background .3s;
  }
  .product-card:hover .badge {
    background: #218838;
  }

  /* Buttons */
  .product-card .btn-outline-primary {
    border: 2px solid #007bff;
    color: #007bff;
    transition: background .3s, color .3s;
  }
  .product-card .btn-outline-primary:hover {
    background: #007bff;
    color: #fff;
  }
  .product-card .btn-success {
    background: linear-gradient(45deg, #28a745, #218838);
    border: none;
    transition: background .3s;
  }
  .product-card .btn-success:hover {
    background: linear-gradient(45deg, #218838, #28a745);
  }

  /* Spacing tweaks */
  .product-card .card-body {
    padding: 2rem;
  }
  .product-card .card-body .mt-3 {
    margin-top: 2rem !important;
  }
        footer {
            background-color: #343a40;
            color: #fff;
            padding: 30px 0;
            text-align: center;
        }

        .logout-banner {
    background: linear-gradient(to right, #00b09b, #96c93d);
    color: white;
    font-weight: 600;
    border-radius: 8px;
    max-width: 600px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    font-size: 1rem;
    animation: fadeInSlideDown 0.6s ease-in-out;
}

@keyframes fadeInSlideDown {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
.error-banner {
    background: linear-gradient(to right, #e53935, #e35d5b);
    color: white;
    font-weight: 600;
    border-radius: 8px;
    max-width: 600px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    font-size: 1rem;
    animation: fadeInSlideDown 0.6s ease-in-out;
    transition: opacity 0.5s ease, transform 0.5s ease;
}
.logout-banner i,
.error-banner i {
    vertical-align: middle;
}
.logout-banner span,
.error-banner span {
    display: inline-block;
}


@media (max-width: 991.98px) {
  /* Hide Home and Products nav links */
  .nav-item.hide-mobile {
    display: none !important;
  }

  /* Hide hamburger toggle */
  .navbar-toggler {
    display: none !important;
  }

  /* Product buttons stacked */
  .product-card .card-body .d-flex.justify-content-between {
    flex-direction: column;
    gap: 0.75rem;
  }

  .search-form {
    width: 100% !important;
  }

  .search-form .form-control {
    font-size: 0.95rem;
  }

  .hero h1 {
    font-size: 2.5rem;
  }

  .hero p {
    font-size: 1.1rem;
  }

  .product-card img {
    height: 200px !important;
  }

  .product-card .card-body {
    padding: 1.2rem;
  }

  .product-card .btn {
    font-size: 0.9rem;
    padding: 0.4rem 1rem;
  }

  .product-card .card-title {
    font-size: 1.1rem;
  }

  .product-card .card-text {
    font-size: 0.85rem;
  }

  .product-card .badge {
    font-size: 0.9rem;
  }

  #products .display-5 {
    font-size: 2.2rem;
  }

  footer h5 {
    font-size: 1.05rem;
  }

  footer ul li a {
    font-size: 0.9rem;
  }

  .logout-banner,
  .error-banner {   
    font-size: 0.9rem;
    padding: 0.75rem;
  }

  .hero-content {
    padding: 1rem;
    text-align: center;
  }
}

@media (max-width: 576px) {
  .hero {
    height: 70vh;
    padding: 1rem;
  }

  .hero h1 {
    font-size: 2.2rem;
  }

  .hero p {
    font-size: 1rem;
  }

  .search-form input,
  .search-form button {
    font-size: 0.85rem;
  }

  .card.product-card {
    margin-bottom: 1rem;
  }

  .stamps-section i {
    font-size: 2rem !important;
  }

  .stamps-section h5 {
    font-size: 1rem;
  }
}
@media (max-width: 768px) {
  .navbar-toggler {
    display: none !important;
  }

  .nav-home,
  .nav-products {
    display: none !important;
  }

  #navbarContent {
    display: flex !important;
  }

  .navbar-nav {
    flex-direction: row !important;
    justify-content: flex-end;
    width: 100%;
  }

  .navbar-nav .nav-item {
    margin-left: 0.5rem;
    margin-right: 0.5rem;
  }
}

    </style>
</head>
<body>
<?php if (isset($_SESSION['message'])): ?>
    <div class="logout-banner text-center py-3 px-4 mb-4 mx-auto d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <span><?= $_SESSION['message']; ?></span>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>


<?php if (isset($_SESSION['error'])): ?>
    <div class="error-banner text-center py-3 px-4 mb-4 mx-auto d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-x-circle-fill fs-5"></i>
        <span><?= $_SESSION['error']; ?></span>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['logout_message'])): ?>
    <div class="logout-banner text-center py-3 px-4 mb-4 mx-auto d-flex align-items-center justify-content-center gap-2">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <span><?= $_SESSION['logout_message']; ?></span>
    </div>
    <?php unset($_SESSION['logout_message']); ?>
<?php endif; ?>



<!-- Navbar -->
<nav class=" navbar navbar-expand-lg navbar-dark bg-dark sticky-top py-3 shadow-sm px-4">
  <div class=" container-fluid d-flex align-items-center justify-content-between">

    <!-- Left: Brand -->
    <a class=" navbar-brand fw-bold fs-4" href="#">MyShop</a>

    <!-- Center: Search -->
    <form class="d-none d-lg-flex mx-auto search-form align-items-center position-absolute start-50 translate-middle-x" 
          method="GET" action="index.php" style="width: 400px;">
      <div class="input-group shadow-sm rounded-pill overflow-hidden">
        <input 
          class="form-control border-0 px-3 py-2" 
          type="search" 
          name="query" 
          placeholder="🔍 Search products..." 
          aria-label="Search" 
          style="background-color: #f8f9fa;"
        >
        <button 
          class="btn px-4 text-white" 
          type="submit"
          style="background-color: #20c997; border-top-left-radius: 0; border-bottom-left-radius: 0;"
        >
          Search
        </button>
      </div>
    </form>

    <!-- Right: Nav Links & Toggler -->
  <!-- Right: Nav Links (Responsive) -->
<div class="d-flex align-items-center">
  <div class="collapse navbar-collapse show" id="navbarContent">
    <ul class="navbar-nav ms-auto align-items-center flex-row flex-wrap">
      <li class="nav-item mx-2 nav-home">
        <a class="nav-link active fw-semibold" style="font-size: 1.05rem;" href="#">Home</a>
      </li>
      <li class="nav-item mx-2 nav-products">
        <a class="nav-link fw-semibold" style="font-size: 1.05rem;" href="#products">Products</a>
      </li>
      <li class="nav-item mx-2">
        <a class="nav-link fw-semibold" style="font-size: 1.05rem;" href="wishlist.php">Wishlist</a>
      </li>
      <li class="nav-item mx-2">
        <a class="nav-link position-relative" href="cart.php">
          <i class="fas fa-shopping-cart fa-lg"></i>
        </a>
      </li>
    </ul>
  </div>
</div>
  </div>
</nav>

<!-- Hero Section -->
<div class="hero d-flex justify-content-center align-items-center text-white text-center">
    <div class="hero-content">
        <h1 class="display-3 fw-bold">Welcome to MyShop</h1>
        <p class="lead">Discover top picks just for you</p>
    </div>
</div> <br> <br>
      
    <!-- Deal section -->
         <h2 class="mb-5 text-center fw-bold display-5 text-dark">The Deal of the Day</h2>
  <div class="product-detail">
    <div class="detail-img">
      <img src="uploads/picture1.jpg" alt="Adidas Pro">
    </div>
    <div class="detail-info">
      <h2>Adidas Pro</h2>
      <p>Experience next-level comfort with the StrideMax CloudTec Runner.
          Its lightweight mesh upper keeps your feet cool and breathable all day long.
          Cushioned soles absorb impact and support every step, whether you're training or traveling.
          The modern design pair of shoes perfectly with athletic or casual looks.
          Built for speed, style, and all-day versatility.</p>

      <div class="price-badge">
        Rs 899
      </div>

      <div>
        <a href="add_to_cart.php?id=1"
           class="btn btn-extreme btn-cart">
          <i class="bi bi-cart-plus-fill me-2"></i>Add to Cart
        </a>
        <?php if (isset($_SESSION['user'])): ?>
          <a href="add_to_wishlist.php?id=1"
             class="btn btn-extreme btn-wish">
            <i class="bi bi-heart-fill me-2"></i>Add to Wishlist
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div> <br><hr>

<!-- Search Interest / Tags -->
<div class="container my-4">
    <?php if (!empty($_SESSION['search_history'])): ?>
        <form method="POST" class="mb-3">
            <button type="submit" name="clear_history" class="btn btn-danger btn-sm">Clear Interests</button>
        </form>
        <h5>Results based on your interests:
            <span class="badge bg-secondary"><?= implode('</span> <span class="badge bg-secondary">', $_SESSION['search_history']) ?></span>
        </h5>
    <?php endif; ?>
</div>

<!-- Products Section -->
<section id="products" class="py-5 bg-light">
  <div class="container">
    <h2 class="mb-5 text-center fw-bold display-5 text-dark">Our Products</h2>
    <div class="row g-4">
      <?php foreach ($products as $product): ?>
        <div class="col-md-4">
          <div class="card product-card h-100 shadow-lg border-0 rounded-4 overflow-hidden">
            <img src="uploads/<?= htmlspecialchars($product['image']) ?>"
                 class="card-img-top img-fluid"
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 style="height: 250px; object-fit: cover;">

            <div class="card-body d-flex flex-column justify-content-between">
              <div>
                <h5 class="card-title fw-semibold"><?= htmlspecialchars($product['name']) ?></h5>
                <p class="card-text small"><?= htmlspecialchars($product['description']) ?></p>
              </div>

              <div class="mt-3 d-flex flex-column">
                <p class="fw-bold fs-5 mb-3">
                  <span class="badge px-3 py-2">
                    Rs <?= number_format($product['price']) ?>
                  </span>
                </p>

                <div class="d-flex justify-content-between gap-2">
                  <a href="product.php?id=<?= $product['id'] ?>"
                     class="btn btn-outline-primary rounded-pill btn-md d-flex align-items-center justify-content-center px-3"
                     style="min-width: 120px;">
                    <i class="bi bi-eye me-2"></i> View
                  </a>

                  <a href="add_to_cart.php?id=<?= $product['id'] ?>"
                     class="btn btn-success rounded-pill btn-md d-flex align-items-center justify-content-center px-3"
                     style="min-width: 140px;">
                    <i class="bi bi-cart-plus me-2"></i> Add to Cart
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <?php if (count($products) === 0): ?>
        <div class="col-12">
          <div class="alert alert-info text-center shadow-sm">
            No products found for your interest.
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section> <br><br><hr>

<section class="py-5 bg-light" id="customer-reviews">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">Customer Reviews</h2>
      <p class="text-muted">What our happy customers say about us</p>
    </div>

    <div class="row g-4">
      <!-- Review 1 -->
      <div class="col-md-6 col-lg-4">
        <div class="bg-white rounded shadow-sm p-4 h-100">
          <div class="d-flex align-items-center mb-3">
            <img src="https://i.pravatar.cc/80?img=12" alt="Customer" class="rounded-circle me-3" width="60" height="60">
            <div>
              <h6 class="mb-0">Sarah Ahmed</h6>
              <small class="text-muted">Lahore, Pakistan</small>
            </div>
          </div>
          <p class="text-muted fst-italic">"Amazing service and fast delivery! Everything arrived perfectly packed. Highly recommend!"</p>
          <div class="text-warning">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-half"></i>
          </div>
        </div>
      </div>

      <!-- Review 2 -->
      <div class="col-md-6 col-lg-4">
        <div class="bg-white rounded shadow-sm p-4 h-100">
          <div class="d-flex align-items-center mb-3">
            <img src="https://i.pravatar.cc/80?img=18" alt="Customer" class="rounded-circle me-3" width="60" height="60">
            <div>
              <h6 class="mb-0">Ali Khan</h6>
              <small class="text-muted">Karachi, Pakistan</small>
            </div>
          </div>
          <p class="text-muted fst-italic">"Quality of the products is top-notch. The support team is very responsive too!"</p>
          <div class="text-warning">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
          </div>
        </div>
      </div>

      <!-- Review 3 -->
      <div class="col-md-6 col-lg-4">
        <div class="bg-white rounded shadow-sm p-4 h-100">
          <div class="d-flex align-items-center mb-3">
            <img src="https://i.pravatar.cc/80?img=32" alt="Customer" class="rounded-circle me-3" width="60" height="60">
            <div>
              <h6 class="mb-0">Maria Fatima</h6>
              <small class="text-muted">Islamabad, Pakistan</small>
            </div>
          </div>
          <p class="text-muted fst-italic">"Love the interface, super easy to use and the experience was seamless. 10/10!"</p>
          <div class="text-warning">
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star-fill"></i>
            <i class="bi bi-star"></i>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>  <br><br><hr><br> 

<!-- Stamps Section (Bootstrap Icons) -->
<section class="py-5 bg-light">
  <div class="px-4">  
    <div class="row justify-content-center g-4">

      <div class="col-6 col-md-4 col-lg-2">
        <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
          <i class="bi bi-truck display-4 text-primary mb-3"></i>
          <h6 class="fw-bold mb-0">FREE<br>SHIPPING</h6>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-2">
        <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
          <i class="bi bi-arrow-counterclockwise display-4 text-primary mb-3"></i>
          <h6 class="fw-bold mb-0">EASY<br>RETURNS</h6>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-2">
        <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
          <i class="bi bi-shield-lock display-4 text-primary mb-3"></i>
          <h6 class="fw-bold mb-0">SECURE<br>CHECKOUT</h6>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-2">
        <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
          <i class="bi bi-award display-4 text-primary mb-3"></i>
          <h6 class="fw-bold mb-0">1 YEAR<br>WARRANTY</h6>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-2">
        <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
          <i class="bi bi-arrow-repeat display-4 text-primary mb-3"></i>
          <h6 class="fw-bold mb-0">MONEY BACK<br>GUARANTEE</h6>
        </div>
      </div>

      <div class="col-6 col-md-4 col-lg-2">
        <div class="bg-white p-4 rounded shadow-sm h-100 text-center">
          <i class="bi bi-hand-thumbs-up display-4 text-primary mb-3"></i>
          <h6 class="fw-bold mb-0">SATISFACTION<br>GUARANTEE</h6>
        </div>
      </div>

    </div>
  </div>
</section>



<!-- Footer -->
<footer class="bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row text-center text-md-start">
            <!-- Quick Links -->
            <div class="col-md-6 mb-4 mb-md-0">
                <h5 class="text-warning mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-warning text-decoration-none d-block py-1">About Us</a></li>
                    <li><a href="#" class="text-warning text-decoration-none d-block py-1">FAQ</a></li>
                    <li><a href="#" class="text-warning text-decoration-none d-block py-1">Privacy Policy</a></li>
                    <li><a href="#" class="text-warning text-decoration-none d-block py-1">Contact</a></li>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="col-md-6">
                <h5 class="text-warning mb-3">Follow Us</h5>
                <ul class="list-unstyled">
                    <li>
                        <a href="https://www.instagram.com/" target="_blank" class="text-warning text-decoration-none d-block py-1">
                            <i class="bi bi-instagram me-1"></i> Instagram
                        </a>
                    </li>
                    <li>
                        <a href="https://www.facebook.com/" target="_blank" class="text-warning text-decoration-none d-block py-1">
                            <i class="bi bi-facebook me-1"></i> Facebook
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Logout Button -->
        <?php if (isset($_SESSION['user']['id'])): ?>
    <div class="row mt-4">
        <div class="col text-center">
            <a href="logout.php"
               class="btn btn-danger btn-lg px-5 py-2 fw-bold shadow"
               style="font-size: 1.25rem;">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>
    </div>
<?php endif; ?>


        <hr class="border-light mt-4">

        <div class="text-center mt-3">
            <p class="mb-0">&copy; <?= date('Y') ?> MyShop. All rights reserved.</p>
        </div>
    </div>
</footer>





<!-- JS -->
<script>
    // Scroll to top (slightly down) for "Home" link
    document.querySelector('a.nav-link[href="#"]').addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({ top: 50, behavior: 'smooth' });
    });

    // Scroll to #products for "Products" link
    document.querySelector('a.nav-link[href="#products"]').addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector('#products').scrollIntoView({ behavior: 'smooth' });
    });

    // Save flag to scroll after reload when searching
    document.querySelector('.search-form').addEventListener('submit', function () {
        sessionStorage.setItem('scrollToProducts', 'true');
    });

    // After reload, scroll to products if flag is present
    window.addEventListener('DOMContentLoaded', function () {
        if (sessionStorage.getItem('scrollToProducts') === 'true') {
            const section = document.querySelector('#products');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
            sessionStorage.removeItem('scrollToProducts');
        }
    });
    
    setTimeout(() => {
        document.querySelectorAll('.logout-banner, .error-banner').forEach(banner => {
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(-10px)';
            setTimeout(() => banner.remove(), 500);
        });
    }, 3000);

    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
    link.addEventListener('click', () => {
        const navbar = document.querySelector('.navbar-collapse');
        if (navbar.classList.contains('show')) {
            new bootstrap.Collapse(navbar).hide();
        }
    });
}); 


</script>

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
