<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please login First to Access Page.";
    header("Location: login.php");
    exit;
}

$name  = $_SESSION['user']['first_name'] ?? '';
$lname = $_SESSION['user']['last_name'] ?? '';
$email = $_SESSION['email'] ?? '';
$phone = $_SESSION['phone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout | Vastu Shakti Rahasya</title>

  <!-- Premium Typography — identical to store/product-detail -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bespoke Checkout Styles -->
  <link rel="stylesheet" href="assets/css/checkout.css">
</head>
<body>

  <!-- Scroll Progress Bar -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- Bespoke Navbar — identical to store/product-detail -->
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
              <li><a class="dropdown-item" style="color: var(--clr-gold-main);" href="services.php">All Services <span style="font-family: sans-serif;">→</span></a></li>
            </ul>
          </li>
          <li class="nav-item"><a class="nav-link" href="booking.php">Consultations</a></li>
          <li class="nav-item"><a class="nav-link" href="store.php">Curated Store</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        </ul>

        <div class="nav-actions d-flex align-items-center gap-3 mt-3 mt-lg-0">
          <a href="cart.php" class="cart-icon-btn" aria-label="Shopping Cart">
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
                <li><a href="#" class="dropdown-item text-danger" id="checkLogout">Sign Out</a></li>
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

    <!-- Checkout Hero with Step Indicators -->
    <div class="checkout-hero">
      <div class="container px-4 px-xl-5">
        <span class="eyebrow">Sacred Store</span>
        <h1>Complete Your Order</h1>

        <!-- Step Progress -->
        <div class="checkout-steps">
          <div class="step done">
            <div class="step-num">✓</div>
            <span>Cart</span>
          </div>
          <div class="step-connector"></div>
          <div class="step active">
            <div class="step-num">2</div>
            <span>Checkout</span>
          </div>
          <div class="step-connector"></div>
          <div class="step">
            <div class="step-num">3</div>
            <span>Confirmation</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Checkout Content -->
    <section class="checkout-shell">
      <div class="container px-4 px-xl-5">
        <div class="row g-4 g-xl-5">

          <!-- Left: Shipping & Payment Form -->
          <div class="col-lg-7 reveal-up active">
            <div class="checkout-card">
              <h2>Shipping Details</h2>
              <form id="checkoutForm" class="row g-3" method="POST">

                <div class="col-md-6">
                  <label class="form-label">First Name</label>
                  <input id="checkoutFirstName" class="form-control" type="text" value="<?= htmlspecialchars($name) ?>" placeholder="First name" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Last Name</label>
                  <input id="checkoutLastName" class="form-control" type="text" value="<?= htmlspecialchars($lname) ?>" placeholder="Last name" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Email Address</label>
                  <input id="checkoutEmail" class="form-control" type="email" readonly value="<?= htmlspecialchars($email) ?>" placeholder="your@email.com" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Delivery Address</label>
                  <input class="form-control" type="text" placeholder="House no., street, city, state — PIN code" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone Number</label>
                  <input id="checkoutPhone" class="form-control" type="tel" value="<?= htmlspecialchars($phone) ?>" placeholder="+91 00000 00000" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Payment Method</label>
                  <select class="form-select" id="paymentMethod">
                    <option>Card Payment</option>
                    <option>UPI</option>
                    <option id="codOption" value="Cash on Delivery">Cash on Delivery</option>
                  </select>
                  <small id="codHint" style="display:none;">
                    Cash on Delivery is available for orders above ₹3,000.
                  </small>
                </div>

                <div class="col-12 pt-2">
                  <button class="btn-luxury-solid w-100" type="submit">Place Order</button>
                </div>
              </form>

              <div id="checkoutAlert" class="alert mt-4 d-none"></div>
            </div>
          </div>

          <!-- Right: Order Summary -->
          <div class="col-lg-5 reveal-up active stagger-1">
            <div class="summary-card">
              <h2>Order Summary</h2>

              <!-- Dynamic items rendered by JS -->
              <div id="checkoutItems"></div>

              <!-- Totals -->
              <div class="summary-totals">
                <div class="summary-line">
                  <span>Subtotal</span>
                  <strong id="checkoutSubtotal">₹0</strong>
                </div>
                <div class="summary-line">
                  <span>Shipping</span>
                  <strong>₹150</strong>
                </div>
                <div class="summary-line total">
                  <span>Total</span>
                  <strong id="checkoutTotal">₹0</strong>
                </div>
              </div>

              <!-- Assurance block -->
              <div class="summary-assurance">
                <div class="assurance-item">
                  <span class="assurance-icon">✦</span>
                  <span class="assurance-text">Dispatched within 24 hours of confirmation.</span>
                </div>
                <div class="assurance-item">
                  <span class="assurance-icon">✦</span>
                  <span class="assurance-text">Premium packaging on every order.</span>
                </div>
                <div class="assurance-item">
                  <span class="assurance-icon">✦</span>
                  <span class="assurance-text">Secure payment via Razorpay.</span>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

  <!-- Premium Dark Footer — mirrors store/product-detail -->
  <footer class="bespoke-footer">
    <div class="container px-4 px-xl-5">
      <div class="row g-5 justify-content-between">
        <div class="col-lg-4">
          <a class="brand-logo mb-4 d-inline-block" href="index.php">
            <div class="logo-wrapper">
              <img src="assets/images/logo.jpeg" alt="Vastu Shakti Rahasya" class="logo-img" style="height: 45px; border-radius: 4px;">
              <div class="logo-text">
                <span class="logo-title" style="color: #ffffff;">VASTU SHAKTI</span>
                <span class="logo-subtitle" style="color: var(--clr-gold-main);">R A H A S Y A</span>
              </div>
            </div>
          </a>
          <p class="footer-muted-text small pe-lg-4">
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
        <p class="mb-0 small footer-muted-text">© 2026 Vastu Shakti Rahasya. All Rights Reserved.</p>
        <div class="legal-links mt-3 mt-md-0">
          <a href="#" class="me-3">Privacy Policy</a>
          <a href="#">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Confirmation Modal -->
  <div class="custom-modal-overlay" id="confirmModal">
    <div class="custom-modal">
      <div class="modal-icon warning">!</div>
      <h3>Confirm Order</h3>
      <p>Are you sure you want to place this order?</p>
      <div class="modal-actions">
        <button class="btn-luxury-outline" id="cancelOrder">Cancel</button>
        <button class="btn-luxury-solid" id="confirmOrder">Yes, Place Order</button>
      </div>
    </div>
  </div>
            <!-- Order Processing Modal -->
<div class="custom-modal-overlay" id="orderProcessingModal">
  <div class="custom-modal" style="background:#1a1712; border:1px solid #d8b671;">
    <div class="spinner-border text-gold mb-4" role="status" style="width: 3rem; height: 3rem; color: #ffffff;">
      <span class="visually-hidden">Loading...</span>
    </div>
    <h3 style="color:#d8b671;">Confirming Your Order</h3>
    <p style="color:#ffffffbf;">
      Payment received. Please wait while we save your order…<br>
      Do not close or refresh this page.
    </p>
  </div>
</div>
</div>
  <!-- Success Modal -->
  <div class="custom-modal-overlay" id="successModal">
    <div class="custom-modal">
      <div class="modal-icon success">✓</div>
      <h3>Order Placed!</h3>
      <p>Your order has been successfully placed. Thank you for shopping with Vastu Shakti Rahasya.</p>
      <button class="btn-luxury-solid w-100" id="invoice">View Invoice</button>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script src="assets/js/checkout.js"></script>
</body>
</html>