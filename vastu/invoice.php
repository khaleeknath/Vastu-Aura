<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please login First to Access Page.";
    header("Location: login.php");
    exit;
}

$name = $_SESSION['user']['first_name'] ?? '';
$lname = $_SESSION['user']['last_name'] ?? '';

$email = $_SESSION['email'] ?? '';
$phone = $_SESSION['phone'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice | VastuAura</title>

  <!-- Premium Typography — same as store page -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Invoice Styles -->
  <link rel="stylesheet" href="assets/css/invoice.css">
</head>
<body>

  <!-- Scroll Progress Bar -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- Navbar — aligned with store.php navbar structure -->
  <nav class="navbar navbar-expand-xl fixed-top bespoke-navbar" id="siteNavbar">
    <div class="container-fluid px-4 px-xl-5 align-items-center">
      
      <!-- 1. Brand Logo (Extreme Left) -->
      <a class="navbar-brand brand-logo" href="index.php">
        <div class="logo-wrapper">
            <img src="assets/images/logo2.png" alt="Vastu Shakti Rahasya" class="logo-img">
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
              <span id="cartCountMobile" class="cart-badge">0</span>
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
              <span id="cartCount" class="cart-badge">0</span>
          </a>
        </div>

      </div>
    </div>
  </nav>

  <main>

    <!-- Editorial Hero -->
    <section class="invoice-editorial-hero">
      <div class="hero-bg-glow"></div>
      <div class="container text-center position-relative">
        <span class="eyebrow-tag">Order Confirmation</span>
        <h1>Your Invoice</h1>
        <div class="ornate-divider mx-auto"></div>
      </div>
    </section>

    <!-- Invoice Section -->
    <section class="invoice-section">
      <div class="container">

        <!-- Invoice Card -->
        <div class="invoice-card reveal-up" id="invoiceCard">

          <!-- Header: brand + invoice number -->
          <div class="invoice-header">
            <div>
              <a class="invoice-brand-name" href="index.php">Vastu Shakti Rahasya</a>
              <p class="invoice-brand-address">Pune, India<br>info@vastushaktirahasya.com</p>
            </div>
            <div class="invoice-meta-block">
              <strong class="invoice-number" id="invoiceNumber">Invoice #VA-000000</strong>
              <span class="invoice-date" id="invoiceDate">Issued today</span>
            </div>
          </div>

          <!-- Bill To / Order Status -->
          <div class="invoice-parties">
            <div>
              <span class="meta-label">Bill To</span>
              <strong class="party-name" id="invoiceCustomer">—</strong>
              <p class="party-email" id="invoiceEmail">—</p>
            </div>
            <div>
              <span class="meta-label">Order Status</span>
              <span class="status-badge" id="invoiceStatus">Confirmed</span>
              <p class="status-note">Thank you for your purchase.</p>
            </div>
          </div>

          <!-- Line Items -->
          <div class="invoice-items-header">
            <span>Item</span>
            <span style="text-align:center;">Qty × Price</span>
            <span style="text-align:right;">Total</span>
          </div>
          <div class="invoice-items-list" id="invoiceItems">
            <!-- Populated by invoice.js -->
          </div>

          <!-- Totals -->
          <div class="invoice-totals-block">
            <div class="invoice-totals-inner">
              <div class="totals-row">
                <span class="label">Subtotal</span>
                <span class="value" id="invoiceSubtotal">₹0</span>
              </div>
              <div class="totals-row">
                <span class="label">Shipping</span>
                <span class="value">₹150</span>
              </div>
              <div class="totals-row grand-total">
                <span class="label">Total</span>
                <span class="value" id="invoiceTotal">₹0</span>
              </div>
            </div>
          </div>

        </div><!-- /.invoice-card -->

        <!-- Action Buttons -->
        <div class="invoice-actions stagger-1 reveal-up">
          <button id="downloadInvoice" class="btn-invoice-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
            Download PDF
          </button>
          <button id="printInvoice" class="btn-invoice-outline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            Print Invoice
          </button>
        </div>

      </div>
    </section>

  </main>

  <!-- Footer — dark luxury, aligned with store.php -->
  <footer class="bespoke-footer">
    <div class="container">
      <div class="row g-5 justify-content-between">
        <div class="col-lg-4">
          <a class="footer-brand-name" href="index.php">VastuAura</a>
          <p class="footer-muted-text font-montserrat small pe-lg-4">
            Balanced digital experiences for modern Vastu consulting, commerce, and client engagement.
          </p>
        </div>

        <div class="col-6 col-lg-2">
          <h5 class="footer-heading">Quick Links</h5>
          <ul class="footer-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="store.php">Store</a></li>
            <li><a href="booking.php">Appointments</a></li>
            <li><a href="admin-login.php">Admin</a></li>
          </ul>
        </div>

        <div class="col-6 col-lg-2">
          <h5 class="footer-heading">Contact</h5>
          <ul class="footer-links">
            <li>+91 98765 43210</li>
            <li>hello@vastuaura.com</li>
            <li>Jaipur, India</li>
          </ul>
        </div>

        <div class="col-lg-3">
          <h5 class="footer-heading">Newsletter</h5>
          <form class="newsletter-form" onsubmit="return false;">
            <input type="email" class="footer-newsletter-input" placeholder="Your email address">
            <button class="btn-footer-subscribe" type="submit">Subscribe</button>
          </form>
        </div>
      </div>

      <div class="footer-bottom d-flex flex-column flex-md-row justify-content-between align-items-center">
        <p class="footer-copyright">© 2026 Developed &amp; Maintained by Vyomark Digital Solutions</p>
      </div>
    </div>
  </footer>

  <!-- JS Dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="assets/js/invoice.js"></script>
</body>
</html>