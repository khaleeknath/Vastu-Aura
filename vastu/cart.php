<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$cartCount = $_SESSION['cart_count'] ?? 0;

// Preserved Authentication Logic
if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please login First to Access Page";
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Selection | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bespoke E-Commerce Styles -->
  <link rel="stylesheet" href="assets/css/cart.css">
  <link rel="stylesheet" href="assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>
</head>
<body class="luxury-ecomm bg-muted">

  <!-- Progress Bar for Scroll -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- Redesigned High-End Navbar -->
  <nav class="navbar navbar-expand-xl fixed-top bespoke-navbar" id="siteNavbar">
    <div class="container-fluid px-4 px-xl-5 align-items-center">
      
      <!-- 1. Brand Logo (Extreme Left) -->
      <a class="navbar-brand brand-logo" href="index.php">
        <div class="logo-wrapper">
            <img src="assets/images/logo.jpeg" alt="Vastu Shakti Rahasya" class="logo-img">
            <div class="logo-text d-none d-xl-flex flex-column justify-content-center">
                <span class="logo-title">VASTU SHAKTI</span>
                <span class="logo-subtitle">R A H A S Y A</span>
            </div>
        </div>
      </a>
      
      <!-- Mobile Controls (Cart & Toggler group on right for mobile) -->
      <div class="d-flex align-items-center d-xl-none ms-auto gap-3">
          <a href="cart.php" class="cart-icon-btn position-relative" aria-label="Shopping Cart">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
              <span id="cartCountMobile" class="cart-badge"><?= $cartCount ?></span>
          </a>
          <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
      </div>
      
      <!-- Desktop & Mobile Collapse Container -->
      <div class="collapse navbar-collapse" id="siteNav">
        
        <!-- 2. Navigation Links (Right of Logo, pushes remaining items to Extreme Right) -->
        <ul class="navbar-nav align-items-xl-center nav-links-premium ms-xl-4 mt-4 mt-xl-0 pb-3 pb-xl-0 me-auto">
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">The Atelier</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'about.php') ? 'active' : '' ?>" href="about.php">S. Ramesh</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'testimonials.php') ? 'active' : '' ?>" href="testimonials.php">Testimonials</a>
          </li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= ($current_page == 'services.php') ? 'active' : '' ?>" href="services.php" data-bs-toggle="dropdown">Services</a>
            <ul class="dropdown-menu luxury-dropdown">
              <li><a class="dropdown-item" href="services.php#astrology">Vedic Astrology</a></li>
              <li><a class="dropdown-item" href="services.php#numerology">Numerology</a></li>
              <li><a class="dropdown-item" href="services.php#vastu-fire">Vastu Fire</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-gold" href="services.php">All Services <span style="font-family: sans-serif;">→</span></a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'booking.php') ? 'active' : '' ?>" href="booking.php">Consultations</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'store.php') ? 'active' : '' ?> text-gold" href="store.php">Curated Store</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'contact.php') ? 'active' : '' ?>" href="contact.php">Contact</a>
          </li>
        </ul>
        
        <!-- 3. Greeting / Sign In & Cart (Extreme Right) -->
        <div class="nav-actions d-flex flex-column flex-xl-row align-items-start align-items-xl-center gap-3 mt-2 mt-xl-0">
          
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle user-greeting-link fw-bold" href="#" data-bs-toggle="dropdown">
                   <?= htmlspecialchars($_SESSION['name'] ?? 'Guest') ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end luxury-dropdown">
                  <li><a class="dropdown-item" href="myorder.php">Order History</a></li>
                  <li><a class="dropdown-item" href="my-appointments.php">My Sessions</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a href="#" class="dropdown-item logout-link logout-btn" data-logout-url="assets/api/logout.php">Sign Out</a></li>
              </ul>
            </div>
          <?php else: ?>
            <a class="btn-auth-premium d-inline-block" href="login.php">Sign In</a>
          <?php endif; ?>

          <!-- Desktop Cart -->
          <a href="cart.php" class="cart-icon-btn d-none d-xl-flex ms-xl-2 position-relative" aria-label="Shopping Cart">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
              <span id="cartCount" class="cart-badge"><?= $cartCount ?></span>
          </a>
        </div>

      </div>
    </div>
  </nav>

  <main>
    <!-- Cart Editorial Hero -->
    <section class="cart-editorial-hero position-relative section-padding pb-0">
      <div class="container mt-5 pt-4 text-center z-index-2 reveal-up">
        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Your Selection</span>
        <h1 class="cinzel-heading display-3 mb-4 text-dark-900">Sacred Offerings</h1>
        <div class="ornate-divider mx-auto mb-5"></div>
      </div>
    </section>

    <!-- Main Cart Area -->
    <section class="cart-interface section-padding pt-0">
      <div class="container">
        <div class="row g-5">
          
          <!-- Left Column: The Items -->
          <div class="col-lg-8 reveal-up stagger-1">
            <div class="cart-items-container">
              <!-- Header Row -->
              <div class="d-none d-md-flex justify-content-between align-items-center mb-4 pb-2 border-bottom font-montserrat text-uppercase small tracking-wide fw-bold text-muted">
                <span style="width: 55%;">Product</span>
                <span style="width: 20%;" class="text-center">Quantity</span>
                <span style="width: 25%;" class="text-end">Subtotal</span>
              </div>
              
              <!-- Cart Items dynamically injected via JS -->
              <div id="cartItems" class="cart-items-wrapper">
                <div class="text-center py-5">
                  <div class="spinner-border text-gold" role="status"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Order Summary -->
          <div class="col-lg-4 reveal-up stagger-2">
            <div class="luxury-summary-card bg-white p-4 p-md-5 position-sticky" style="top: 120px;">
              <h3 class="cinzel-heading text-dark-900 mb-4 pb-3 border-bottom fs-4">Order Summary</h3>
              
              <div class="summary-details font-montserrat text-muted-large mb-4">
                <div class="d-flex justify-content-between mb-3">
                  <span>Subtotal</span>
                  <strong id="subtotalValue" class="text-dark-900">₹0</strong>
                </div>
                <div class="d-flex justify-content-between mb-4">
                  <span>Shipping (Flat)</span>
                  <strong class="text-dark-900">₹150</strong>
                </div>
                
                <div class="d-flex justify-content-between align-items-center py-3 border-top border-bottom">
                  <span class="fw-bold text-dark-900 text-uppercase tracking-wide small">Total</span>
                  <strong id="totalValue" class="fs-4 text-gold cinzel-heading">₹0</strong>
                </div>
              </div>

              <div class="promo-box mb-4">
                <div class="input-group luxury-input-group">
                  <input id="couponInput" type="text" class="form-control luxury-input" placeholder="Promo Code (Optional)">
                  <button id="couponButton" class="btn btn-luxury-outline py-2 px-3 text-dark-900" type="button">Apply</button>
                </div>
                <div id="couponMessage" class="coupon-message font-montserrat small mt-2"></div>
              </div>
              
              <button id="checkoutBtn" class="btn-luxury-solid w-100 mb-3">Proceed to Checkout</button>
              <a href="store.php" class="btn-luxury-outline w-100 text-dark-900 text-center">Continue Shopping</a>
              
              <!-- Trust Badges -->
              <div class="trust-badges mt-5 pt-3 border-top d-flex justify-content-center gap-3">
                <div class="trust-badge text-center">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold-main)" stroke-width="1.5" class="mb-2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                  <span class="d-block font-montserrat small text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">Secure<br>Checkout</span>
                </div>
                <div class="trust-badge text-center">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--clr-gold-main)" stroke-width="1.5" class="mb-2"><circle cx="12" cy="12" r="10"/><path d="M8 12l2 2 4-4"/></svg>
                  <span class="d-block font-montserrat small text-muted" style="font-size: 0.7rem; letter-spacing: 0.5px;">Authenticity<br>Guaranteed</span>
                </div>
              </div>

            </div>
          </div>

        </div>
      </div>
    </section>
  </main>

  <!-- Empty Cart Modal -->
  <div class="modal fade luxury-modal" id="emptyCartModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content bg-white border-0 rounded-0 text-center p-5 shadow-lg">
              <div class="modal-body p-0">
                  <div class="success-icon text-white mb-4 opacity-50">
                      <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                  </div>
                  <h3 class="cinzel-heading text-white-900 mb-3">Your Selection is Empty</h3>
                  <p class="font-montserrat text-white mb-4">
                      Please add at least one sacred artifact to your collection before proceeding to checkout.
                  </p>
                  <div class="d-flex justify-content-center gap-3">
                    <button class="btn-luxury-outline text-dark-900" data-bs-dismiss="modal">Close</button>
                    <a href="store.php" class="btn-luxury-solid">Explore Store</a>
                  </div>
              </div>
          </div>
      </div>
  </div>

  <!-- Premium Dark Luxury Footer -->
  <footer class="bespoke-footer border-top mt-5">
    <div class="container">
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
          <p class="font-montserrat footer-muted-text small pe-lg-4">
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
            <li><a href="admin-login.php">Admin</a></li>
          </ul>
        </div>
      </div>
      
      <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center mt-5 pt-4">
        <p class="mb-0 small footer-muted-text font-montserrat">© 2026 Vastu Shakti Rahasya. All Rights Reserved.</p>
        <div class="legal-links mt-3 mt-md-0">
            <a href="#" class="small footer-muted-text font-montserrat me-3">Privacy Policy</a>
            <a href="#" class="small footer-muted-text font-montserrat">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/cart.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>