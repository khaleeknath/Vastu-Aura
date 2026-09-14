<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please login First to Access Page.";
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Detail | Vastu Shakti Rahasya</title>

  <!-- Premium Typography — identical stack to store.php -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bespoke Product Detail Styles -->
  <link rel="stylesheet" href="assets/css/product-detail.css">
  <link rel="stylesheet" href="assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>
</head>
<body class="luxury-ecomm">

  <!-- Scroll Progress Bar -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- Bespoke Navbar — identical structure to store.php -->
  <nav class="navbar navbar-expand-lg fixed-top bespoke-navbar" id="siteNavbar">
    <div class="container-fluid px-4 px-xl-5">
      <a class="navbar-brand brand-logo" href="index.php">
        <div class="logo-wrapper">
          <img src="assets/images/logo.jpeg" alt="Vastu Shakti Rahasya" class="logo-img">
          <div class="logo-text d-none d-xl-flex flex-column justify-content-center">
            <span class="logo-title">VASTU SHAKTI</span>
            <span class="logo-subtitle">R A H A S Y A</span>
          </div>
        </div>
      </a>

      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="siteNav">
        <ul class="navbar-nav mx-auto nav-links-premium">
          <li class="nav-item"><a class="nav-link" href="index.php">The Atelier</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">S. Ramesh</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="services.php" data-bs-toggle="dropdown">Services</a>
            <ul class="dropdown-menu luxury-dropdown">
              <li><a class="dropdown-item" href="services.php#astrology">Vedic Astrology</a></li>
              <li><a class="dropdown-item" href="services.php#numerology">Numerology</a></li>
              <li><a class="dropdown-item" href="services.php#vastu-fire">Vastu Fire</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-gold" href="services.php">All Services <span style="font-family: sans-serif;">→</span></a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="booking.php">Consultations</a></li>
          <li class="nav-item"><a class="nav-link active text-gold" href="store.php">Curated Store</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        </ul>

        <div class="nav-actions d-flex align-items-center gap-3 mt-3 mt-lg-0">
          <a href="cart.php" class="cart-icon-btn position-relative" aria-label="Shopping Cart">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
          </a>
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle user-trigger" href="#" data-bs-toggle="dropdown">
                Namaste, <?= htmlspecialchars($_SESSION['name'] ?? 'Guest') ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end luxury-dropdown">
                <li><a class="dropdown-item" href="myorder.php">Order History</a></li>
                <li><a class="dropdown-item" href="my-appointments.php">My Sessions</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a href="#" class="dropdown-item text-danger" data-logout-url="assets/api/logout.php">Sign Out</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a class="btn-auth-premium" href="login.php">Sign In</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <main>

    <!-- Breadcrumb -->
    <div class="breadcrumb-trail">
      <div class="container px-4 px-xl-5">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="store.php">Curated Store</a></li>
            <li class="breadcrumb-item active" id="breadcrumbProduct">Product</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Product Detail Section -->
    <section class="product-shell">
      <div class="container px-4 px-xl-5">
        <div class="row g-5 g-xl-6 align-items-start">

          <!-- Left: Product Image -->
          <div class="col-lg-6 reveal-up active">
            <div class="product-visual">
              <div class="visual-main">
                <img id="productImage" class="img-fluid" src="" alt="">
              </div>
            </div>
          </div>

          <!-- Right: Product Info -->
          <div class="col-lg-6 reveal-up active stagger-1">
            <div class="product-info-panel">
              <span class="eyebrow" id="productCategory"></span>
              <h1 id="productName"></h1>
              <p class="product-lead" id="productDescription"></p>

              <div class="price-row">
                <strong id="productPrice"></strong>
                <span>Inclusive of premium packaging</span>
              </div>

              <ul class="feature-list">
                <li>Premium quality, authentically sourced</li>
                <li>Fast shipping — dispatched within 24 hours</li>
                <li>Genuine Vastu-energized product</li>
              </ul>

              <div class="buy-row">
                <div class="qty-box">
                  <button type="button" id="qtyMinus">&#8722;</button>
                  <span id="qtyValue">1</span>
                  <button type="button" id="qtyPlus">&#43;</button>
                </div>
                <button id="detailAddToCart" class="btn-luxury-solid">Add to Cart</button>
                <a class="btn-luxury-outline" href="cart.php">View Cart</a>
              </div>

              <div id="detailToast" class="toast-banner d-none">
                <span style="color: var(--clr-gold-main); margin-right: 8px;">✦</span> Product added to your cart.
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Product Notes & Shipping -->
    <section class="section-shell">
      <div class="section-shell-header mb-5 reveal-up">
        <div class="container px-4 px-xl-5">
          <div class="d-flex align-items-center gap-3">
            <div class="ornate-divider"></div>
            <span class="eyebrow mb-0">Product Information</span>
          </div>
        </div>
      </div>
      <div class="container px-4 px-xl-5">
        <div class="row g-4">
          <div class="col-lg-8 reveal-up stagger-1">
            <div class="content-card">
              <h2>Product Notes</h2>
              <p id="productNotes"></p>
            </div>
          </div>
          <div class="col-lg-4 reveal-up stagger-2">
            <div class="content-card shipping-card">
              <h2>Shipping &amp; Care</h2>
              <div class="shipping-item">
                <span class="shipping-item-icon">✦</span>
                <div class="shipping-item-text">
                  <strong>Dispatch</strong>
                  Prepared and shipped within 24 hours of order confirmation.
                </div>
              </div>
              <div class="shipping-item">
                <span class="shipping-item-icon">✦</span>
                <div class="shipping-item-text">
                  <strong>Delivery</strong>
                  Estimated delivery in 3–5 business days across India.
                </div>
              </div>
              <div class="shipping-item">
                <span class="shipping-item-icon">✦</span>
                <div class="shipping-item-text">
                  <strong>Packaging</strong>
                  Each piece is wrapped in premium protective packaging.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  </main>

  <!-- Premium Dark Footer — mirrors store.php -->
  <footer class="bespoke-footer">
    <div class="container px-4 px-xl-5">
      <div class="row g-5 justify-content-between">
        <div class="col-lg-4">
          <a class="brand-logo mb-4 d-inline-block" href="index.php">
            <div class="logo-wrapper">
              <img src="assets/images/logo.jpeg" alt="Vastu Shakti Rahasya" class="logo-img" style="height: 45px; border-radius: 4px;">
              <div class="logo-text">
                <span class="logo-title text-white">VASTU SHAKTI</span>
                <span class="logo-subtitle text-gold">R A H A S Y A</span>
              </div>
            </div>
          </a>
          <p class="font-montserrat footer-muted-text small pe-lg-4" style="font-family: 'Montserrat', sans-serif;">
            Curating positive spaces and aligning destinies through authentic traditional sciences, tailored for the modern world.
          </p>
        </div>

        <div class="col-6 col-lg-2">
          <h5 class="footer-heading">Shop</h5>
          <ul class="footer-links">
            <li><a href="store.php?category=gemstones">Gemstones</a></li>
            <li><a href="store.php?category=bracelets">Bracelets</a></li>
            <li><a href="store.php?category=yantras">Yantras</a></li>
          </ul>
        </div>

        <div class="col-6 col-lg-2">
          <h5 class="footer-heading">Services</h5>
          <ul class="footer-links">
            <li><a href="booking.php">Book Session</a></li>
            <li><a href="services.php#astrology">Astrology</a></li>
            <li><a href="services.php#vastu">Vastu Audits</a></li>
          </ul>
        </div>

        <div class="col-lg-2">
          <h5 class="footer-heading">Support</h5>
          <ul class="footer-links">
            <li><a href="contact.php">Contact Us</a></li>
            <li><a href="faq.php">FAQs</a></li>
            <li><a href="myorder.php">Order History</a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center mt-5 pt-4">
        <p class="mb-0 small footer-muted-text" style="font-family: 'Montserrat', sans-serif;">© 2026 Vastu Shakti Rahasya. All Rights Reserved.</p>
        <div class="legal-links mt-3 mt-md-0">
          <a href="#" class="small footer-muted-text me-3" style="font-family: 'Montserrat', sans-serif;">Privacy Policy</a>
          <a href="#" class="small footer-muted-text" style="font-family: 'Montserrat', sans-serif;">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Confirmation Modal -->
  <div class="custom-modal-overlay" id="productconfirmModal" style="display: none;">
    <div class="custom-modal">
      <div class="modal-icon warning">!</div>
      <h3>Confirm Add to Cart</h3>
      <p>Do you wish to add this product to your cart?</p>
      <div class="modal-actions">
        <button class="btn-luxury-outline" id="cancelOrder">Cancel</button>
        <button class="btn-luxury-solid" id="confirmOrder">Yes, Add to Cart</button>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="custom-modal-overlay" id="successModal" style="display: none;">
    <div class="custom-modal">
      <div class="modal-icon success">✓</div>
      <h3>Added to Cart</h3>
      <p>Your product has been added successfully to your cart.</p>
      <button class="btn-luxury-solid w-100" id="continueShopping">Continue Shopping</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/product-detail.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>