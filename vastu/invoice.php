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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/invoice.css">
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
    <a class="nav-link nav-auth" id="invoiceLogout" href="">Logout</a>
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
        <span class="eyebrow">Invoice UI</span>
        <h1>Client invoice preview.</h1>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="invoice-card" id="invoiceCard">
          <div class="invoice-top">
            <div>
              <a class="logo-mark" href="index.php">VastuAura</a>
              <p>Jaipur, India<br>hello@vastuaura.com</p>
            </div>
            <div class="invoice-meta">
              <strong id="invoiceNumber">Invoice #VA-000000</strong>
              <span id="invoiceDate">Issued today</span>
            </div>
          </div>
          <div class="invoice-grid">
            <div>
              <p class="meta-label">Bill To</p>
              <strong id="invoiceCustomer">Aarav Sharma</strong>
              <p id="invoiceEmail">aarav@example.com</p>
            </div>
            <div>
              <p class="meta-label">Order Status</p>
              <strong id="invoiceStatus">Confirmed</strong>
              <p>UI preview for invoice layout</p>
            </div>
          </div>
          <div id="invoiceItems" class="invoice-items"></div>
          <div class="invoice-totals">
            <div><span>Subtotal</span><strong id="invoiceSubtotal">₹0</strong></div>
            <div><span>Shipping</span><strong>₹150</strong></div>
            <div class="total"><span>Total</span><strong id="invoiceTotal">₹0</strong></div>
          </div>
        </div>
        <div class="invoice-actions">

    <button id="downloadInvoice"
        class="btn btn-brand">
        Download PDF
    </button>

    <button id="printInvoice"
        class="btn btn-outline-brand">
        Print Invoice
    </button>

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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
  <script src="assets/js/invoice.js"></script>
</body>
</html>
