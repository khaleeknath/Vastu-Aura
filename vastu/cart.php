
<?php
session_start();

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
  <title>Cart | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/cart.css">
  <link rel="stylesheet" href="assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>
</head>
<body>
  <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>

  <nav class="navbar navbar-expand-lg fixed-top site-navbar">
    <div class="container">
      <a class="navbar-brand logo-mark" href="index.php">VastuAura</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="siteNav">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
  <li class="nav-item">
    <span class="nav-link nav-auth">
      👋 Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?>
    </span>
  </li>
  <li>
    <a href="#"
       class="dropdown-item text-danger logout-btn"
       data-logout-url="assets/api/logout.php">
        🚪 Logout
    </a>
</li>
<?php else: ?>
  <li class="nav-item">
    <a class="nav-link nav-auth" href="login.php">Login/Register</a>
  </li>
<?php endif; ?> 
        </ul>
      </div>
    </div>
  </nav>

  <main>
    <section class="hero-shell">
      <div class="container">
        <span class="eyebrow">Shopping Cart</span>
        <h1>Your selected Vastu essentials.</h1>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-8">
            <div class="cart-card">
              <div id="cartItems" class="cart-items"></div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="summary-card">
              <h2>Order Summary</h2>
              <div class="summary-line"><span>Subtotal</span><strong id="subtotalValue">₹0</strong></div>
              <div class="summary-line"><span>Shipping</span><strong>₹150</strong></div>
              <div class="summary-line total"><span>Total</span><strong id="totalValue">₹0</strong></div>
              <div class="coupon-box">
                <label class="form-label">Promo Code</label>
                <input id="couponInput" type="text" class="form-control" placeholder="VASTU10">
                <button id="couponButton" class="btn btn-outline-brand w-100 mt-3" type="button">Apply Code</button>
              </div>
              <button id="checkoutBtn" class="btn btn-brand w-100 mt-4">Proceed To Checkout</button>              <a class="btn btn-link w-100 mt-2" href="store.php">Continue Shopping</a>
              <div id="couponMessage" class="coupon-message"></div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <div class="modal fade" id="emptyCartModal" tabindex="-1" aria-labelledby="emptyCartModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="emptyCartModalLabel">Cart Empty</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        Your cart is empty. Please add at least one product before proceeding to checkout.
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Close
        </button>
        <a href="store.php" class="btn btn-brand">
          Continue Shopping
        </a>
      </div>

    </div>
  </div>
</div>

  <footer class="site-footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4">
          <a class="footer-logo" href="index.php">VastuAura</a>
          <p>Balanced digital experiences for modern Vastu consulting, commerce, and client engagement.</p>
        </div>
        <div class="col-sm-6 col-lg-3">
          <h3>Quick Links</h3>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="store.php">Store</a></li>
            <li><a href="booking.php">Appointments</a></li>
            <li><a href="admin-login.php">Admin</a></li>
          </ul>
        </div>
        <div class="col-sm-6 col-lg-2">
          <h3>Contact</h3>
          <ul>
            <li>+91 98765 43210</li>
            <li>hello@vastuaura.com</li>
            <li>Jaipur, India</li>
          </ul>
        </div>
        <div class="col-lg-3">
          <h3>Newsletter</h3>
          <form class="newsletter-form">
            <input type="email" class="form-control" placeholder="Your email">
            <button class="btn btn-brand w-100 mt-3" type="submit">Subscribe</button>
          </form>
        </div>
      </div>
      <p class="copyright">© 2026 Developed &amp; Maintained by Vyomark Digital Solutions</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/cart.js"></script>
  <script src="assets/js/common-modal.js"></script>

</body>
</html>
