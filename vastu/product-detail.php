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
  <title>Product Detail | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/product-detail.css">
  <link rel="stylesheet" href="assets/css/checkout.css">
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

    <section class="product-shell">
        <div class="container">

            <div class="row g-5 align-items-center">

                <div class="col-lg-6">

                    <div class="product-visual">

                        <div class="visual-main">

                            <img
                                id="productImage"
                                src=""
                                alt=""
                                class="img-fluid">

                        </div>

                    </div>

                </div>

                <div class="col-lg-6">

                    <span
                        class="eyebrow"
                        id="productCategory">
                    </span>

                    <h1 id="productName"></h1>

                    <p
                        class="lead"
                        id="productDescription">
                    </p>

                    <div class="price-row">

                        <strong id="productPrice"></strong>

                        <span>
                            Inclusive of premium packaging
                        </span>

                    </div>

                    <ul class="feature-list">

                        <li>
                            Premium Quality Product
                        </li>

                        <li>
                            Fast Shipping Available
                        </li>

                        <li>
                            Genuine Vastu Product
                        </li>

                    </ul>

                    <div class="buy-row">

                        <div class="qty-box">

                            <button
                                type="button"
                                id="qtyMinus">
                                -
                            </button>

                            <span id="qtyValue">
                                1
                            </span>

                            <button
                                type="button"
                                id="qtyPlus">
                                +
                            </button>

                        </div>

                        <button
                            id="detailAddToCart"
                            class="btn btn-brand">
                            Add To Cart
                        </button>

                        <a
                            class="btn btn-outline-brand"
                            href="cart.php">
                            View Cart
                        </a>

                    </div>

                    <div
                        id="detailToast"
                        class="toast-banner d-none">
                        Product added to cart.
                    </div>

                </div>

            </div>

        </div>
    </section>

    <section class="section-shell">

        <div class="container">

            <div class="row g-4">

                <div class="col-lg-8">

                    <div class="content-card">

                        <h2>
                            Product Notes
                        </h2>

                        <p id="productNotes"></p>

                    </div>

                </div>

                <div class="col-lg-4">

                    <div class="content-card">

                        <h2>
                            Shipping
                        </h2>

                        <p>
                            Dispatch in 24 hours,
                            delivery in 3-5 days.
                        </p>

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
<div class="custom-modal-overlay" id="productconfirmModal">
  <div class="custom-modal">
    <div class="modal-icon warning">
      !
    </div>

    <h3>Confirm To Cart</h3>

    <p>
    Do you really want to add this product to your cart?
    </p>

    <div class="modal-actions">
      <button class="btn btn-secondary" id="cancelOrder">
        Cancel
      </button>

      <button class="btn btn-brand" id="confirmOrder">
        Yes, Add To Cart
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

    <h3>Added To Cart</h3>

    <p>
      Your Order is Added To Cart Successfully.
    </p>

    <button class="btn btn-brand w-100" id="continueShopping">
      Continue Shopping
    </button>

  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/product-detail.js"></script>
  <script src="assets/js/common-modal.js"></script>

</body>
</html>
