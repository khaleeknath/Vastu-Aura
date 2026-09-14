<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$cartCount = $_SESSION['cart_count'] ?? 0;

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
  <title>My Consultations | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bespoke E-Commerce Styles -->
  <link rel="stylesheet" href="assets/css/myappointments.css">
  <link rel="stylesheet" href="assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>
  <style>
    .luxury-confirm-table {
  --bs-table-bg: transparent;
  --bs-table-color: #fff;
  --bs-table-striped-bg: transparent;
  --bs-table-striped-color: #fff;
  --bs-table-active-bg: transparent;
  --bs-table-active-color: #fff;
  --bs-table-hover-bg: transparent;
  --bs-table-hover-color: #fff;
  background-color: transparent !important;
}

.luxury-confirm-table th,
.luxury-confirm-table td {
  background-color: transparent !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
}
    </style>
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

  <!-- Appointment Details Modal -->
<div class="modal fade luxury-modal" id="appointmentDetailsModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark-900 border-gold rounded-0">
      <div class="modal-header border-0 py-4 px-4 px-md-5 d-flex justify-content-between align-items-center">
        <h4 class="modal-title cinzel-heading text-gold mb-0">Session Itinerary</h4>
        <button type="button" class="btn-close btn-close-white opacity-50" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 p-md-5 pt-0">

        <!-- Loading state -->
        <div id="apptDetailsLoading" class="text-center py-5">
          <div class="spinner-border text-gold" role="status"></div>
          <p class="font-montserrat text-white-75 mt-3 mb-0">Retrieving consultation details...</p>
        </div>

        <!-- Content -->
        <div id="apptDetailsContent" class="d-none">
          <p class="font-montserrat text-white-75 mb-4 border-bottom border-light pb-4">
            Full details of your requested consultation.
          </p>

          <div class="table-responsive">
            <table class="table table-borderless luxury-confirm-table w-100 mb-0 font-montserrat text-white">
              <tbody>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide" style="width: 40%;">Booking No.</th>
                  <td id="apptId" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Client Name</th>
                  <td id="apptName" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Contact Email</th>
                  <td id="apptEmail" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Mobile Number</th>
                  <td id="apptMobile" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Service Required</th>
                  <td id="apptType" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Scheduled Date</th>
                  <td id="apptDate" class="text-end text-gold"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Scheduled Time</th>
                  <td id="apptTime" class="text-end text-gold"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Property Type</th>
                  <td id="apptPropertyType" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">BHK Type</th>
                  <td id="apptBhk" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Area (Sq.ft)</th>
                  <td id="apptSqft" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Unit / Flat</th>
                  <td id="apptUnit" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Address</th>
                  <td id="apptAddress" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Distance from Shop</th>
                  <td id="apptDistance" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Status</th>
                  <td id="apptStatus" class="text-end text-gold"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Payment Status</th>
                  <td id="apptPaymentStatus" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-white-50 text-uppercase small tracking-wide">Payment Ref.</th>
                  <td id="apptPaymentRef" class="text-end"></td>
                </tr>
                <tr>
                  <th class="text-gold text-uppercase small tracking-wide">Advance Paid</th>
                  <td id="apptAmount" class="text-end fs-5 fw-bold text-gold"></td>
                </tr>
              </tbody>
            </table>
          </div>

          <div id="apptCommentWrap" class="mt-4 pt-4 border-top border-light d-none">
            <h6 class="text-gold text-uppercase small tracking-wide mb-2 font-montserrat">Note from Consultant</h6>
            <p id="apptComment" class="font-montserrat text-white-75 mb-0"></p>
          </div>
        </div>

        <!-- Error state -->
        <div id="apptDetailsError" class="d-none text-center py-4 text-danger font-montserrat"></div>

      </div>
      <div class="modal-footer border-top border-light py-4 px-4 px-md-5 bg-dark-800">
        <button type="button" class="btn-luxury-outline text-white border-0" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

  <main>
    <!-- Account Dashboard Hero -->
    <section class="account-editorial-hero position-relative section-padding pb-0">
      <div class="container mt-5 pt-4 text-center z-index-2 reveal-up">
        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Your Account</span>
        <h1 class="cinzel-heading display-3 mb-4 text-dark-900">My Consultations</h1>
        <div class="ornate-divider mx-auto mb-5"></div>
      </div>
    </section>

    <!-- Appointments Feed -->
    <section class="appointments-interface section-padding pt-0">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-10">
            
            <!-- JS Inject Container -->
            <div id="appointmentsContainer" class="d-flex flex-column gap-4">
               <!-- Loading State -->
               <div class="text-center py-5 reveal-up">
                  <div class="spinner-border text-gold" role="status"></div>
                  <p class="font-montserrat text-muted mt-3">Retrieving your session itineraries...</p>
               </div>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>

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
  <script src="assets/js/myappointment.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>