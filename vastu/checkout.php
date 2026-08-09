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
  <title>Checkout | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/checkout.css">
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
  <li class="nav-item">
    <a class="nav-link nav-auth" id="checkLogout" href="">Logout</a>
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
        <span class="eyebrow">Checkout</span>
        <h1>Complete your order details.</h1>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="checkout-card">
              <h2>Shipping &amp; Payment UI</h2>
              <form id="checkoutForm" class="row g-3" method="POST" >
                <div class="col-md-6">
                  <label class="form-label">First Name</label>
                  <input id="checkoutFirstName" class="form-control" type="text" value="<?= htmlspecialchars($name) ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Last Name</label>
                  <input id="checkoutLastName" class="form-control" type="text" value="<?= htmlspecialchars($lname) ?>" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Email</label>
                  <input id="checkoutEmail" class="form-control" type="email" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Address</label>
                  <input class="form-control" type="text" placeholder="House no, street, city" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone</label>
                  <input id="checkoutPhone" class="form-control" type="tel" value="<?= htmlspecialchars($phone) ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Payment Method</label>
                  <select class="form-select">
                    <option>Card Payment</option>
                    <option>UPI</option>
                    <option>Cash on Delivery</option>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-brand" type="submit">Place Order</button>
                </div>
              </form>
              <div id="checkoutAlert" class="alert alert-success mt-4 d-none"></div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="summary-card">
              <h2>Order Preview</h2>
              <div id="checkoutItems"></div>
              <div class="summary-line"><span>Subtotal</span><strong id="checkoutSubtotal">₹0</strong></div>
              <div class="summary-line"><span>Shipping</span><strong>₹150</strong></div>
              <div class="summary-line total"><span>Total</span><strong id="checkoutTotal">₹0</strong></div>
              <!-- <a class="btn btn-outline-brand w-100 mt-3" href="invoice.html">View Invoice UI</a> -->
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

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


  <!-- Common Confirmation Modal -->
<div class="custom-modal-overlay" id="confirmModal">
  <div class="custom-modal">
    <div class="modal-icon warning">
      !
    </div>

    <h3>Confirm Order</h3>

    <p>
      Are you sure you want to place your order?
    </p>

    <div class="modal-actions">
      <button class="btn btn-secondary" id="cancelOrder">
        Cancel
      </button>

      <button class="btn btn-brand" id="confirmOrder">
        Yes, Place Order
      </button>
    </div>
  </div>
</div>


<!-- Success Modal -->
<div class="custom-modal-overlay" id="successModal">
  <div class="custom-modal">

    <div class="modal-icon success">
      ✓
    </div>

    <h3>Order Placed!</h3>

    <p>
      Your order has been successfully placed.
      Thank you for shopping with VastuAura.
    </p>

    <button class="btn btn-brand w-100" id="invoice">
      Invoice
    </button>

  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/checkout.js"></script>
</body>
</html>
