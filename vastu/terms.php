<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms and Conditions | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bespoke Styles -->
  <link rel="stylesheet" href="assets/css/terms.css">
  <link rel="stylesheet" href="assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>
</head>
<body class="luxury-ecomm bg-white">

  <!-- Progress Bar for Scroll -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- Redesigned High-End Navbar (Preserved from Store/Booking) -->
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
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">The Atelier</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'about.php') ? 'active' : '' ?>" href="about.php">S. Ramesh</a>
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
                  <li><a href="#" class="dropdown-item logout-link logout-btn" data-logout-url="assets/api/logout.php">Sign Out</a></li>
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
    <!-- Terms Hero Section -->
    <section class="terms-hero position-relative section-padding pb-0 bg-muted border-bottom">
      <div class="container mt-5 pt-4 pb-5 text-center z-index-2 reveal-up">
        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Legal Hub</span>
        <h1 class="cinzel-heading display-3 mb-4 text-dark-900">Terms and Conditions</h1>
        <div class="ornate-divider mx-auto mb-4"></div>
        <p class="font-montserrat text-muted-large mx-auto" style="max-width: 700px;">
          Last updated: <?= date('F d, Y'); ?>
        </p>
      </div>
    </section>

    <!-- Terms Content Section -->
    <section class="terms-content section-padding pt-5 bg-white">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-8 font-montserrat reveal-up stagger-1">
            
            <div class="notice mb-5">
              By checking "I agree to the Terms and Conditions" and completing payment for a booking on VastuAura,
              you confirm that you have read, understood, and accepted the terms below — in particular, that the
              <strong>consultation payment is non-refundable</strong>.
            </div>

            <h2 class="cinzel-heading">1. Booking &amp; Payment</h2>
            <ol>
              <li>A booking is confirmed only after successful payment through Razorpay at the time of submission.</li>
              <li>The amount charged is calculated based on your property type (Flat/Commercial), BHK or built-up area, and the road distance between our shop location and your property, as shown in the price summary before payment.</li>
              <li>The estimated charge shown at the time of booking is final once payment is completed and will not be revised afterward, including in cases of incorrect address or location pin placement by the client.</li>
            </ol>

            <h2 class="cinzel-heading">2. Refund Policy</h2>
            <ol>
              <li>All payments made for a consultation booking are <strong>non-refundable</strong> under any circumstances, once the booking is confirmed and payment is successful.</li>
              <li>This applies regardless of the reason, including change of mind, client unavailability at the scheduled time, incorrect details submitted by the client, or dissatisfaction with the scheduled slot.</li>
              <li>No partial or prorated refunds will be issued if a consultation is cut short or missed due to the client's absence.</li>
            </ol>

            <h2 class="cinzel-heading">3. Rescheduling</h2>
            <ol>
              <li>A confirmed booking may be rescheduled up to 24 hours before the selected date and time, subject to slot availability.</li>
              <li>Rescheduling requests must be raised through our official contact channel; slots cannot be changed by simply submitting a new booking.</li>
              <li>Each booking may be rescheduled only once. Repeated rescheduling requests may not be accommodated.</li>
            </ol>

            <h2 class="cinzel-heading">4. Consultation Timing</h2>
            <ol>
              <li>Clients should be available and reachable at the preferred date and time slot selected during booking.</li>
              <li>A grace period of 10 minutes is provided from the scheduled slot start; beyond this, the appointment may be treated as missed, without refund.</li>
            </ol>

            <h2 class="cinzel-heading">5. Location &amp; Address Accuracy</h2>
            <ol>
              <li>Distance-based pricing is calculated using the location pin, search result, or current location set by the client on the map at the time of booking.</li>
              <li>Clients are responsible for accurately placing the map pin on their building/society and providing the correct flat, house number, and address details separately.</li>
              <li>VastuAura is not liable for pricing discrepancies arising from an incorrectly placed pin or inaccurate address information provided by the client.</li>
            </ol>

            <h2 class="cinzel-heading">6. Nature of Consultation</h2>
            <ol>
              <li>Vastu consultation services provided are advisory in nature and based on the property details, photographs, and information shared by the client.</li>
              <li>We do not guarantee specific outcomes, results, or life changes from implementing the recommendations provided.</li>
              <li>Any structural, financial, or personal decisions made based on the consultation are the sole responsibility of the client.</li>
            </ol>

            <h2 class="cinzel-heading">7. Cancellation by VastuAura</h2>
            <ol>
              <li>If a confirmed consultation is cancelled from our end due to unavoidable circumstances, the client will be offered either a rescheduled slot or a full refund, at our discretion.</li>
            </ol>

            <h2 class="cinzel-heading">8. Privacy</h2>
            <ol>
              <li>Personal information, property details, and location data shared during booking are used solely for the purpose of providing the consultation service and will be kept confidential.</li>
            </ol>

            <h2 class="cinzel-heading">9. Changes to These Terms</h2>
            <ol>
              <li>VastuAura reserves the right to update these Terms and Conditions at any time without prior notice. Continued use of the booking service constitutes acceptance of the updated terms.</li>
            </ol>

            <h2 class="cinzel-heading">10. Contact</h2>
            <ol>
              <li>For questions regarding these terms, rescheduling, or booking issues, please reach us at <strong>hello@vastuaura.com</strong> or <strong>+91 98765 43210</strong>.</li>
            </ol>

            <div class="mt-5 pt-4">
                <a class="btn-luxury-outline text-dark w-auto" href="booking.php">&larr; Back to Booking</a>
            </div>

          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Premium Dark Luxury Footer (Preserved from Store/Booking) -->
  <footer class="bespoke-footer border-top mt-0">
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
            <a href="terms.php" class="small footer-muted-text font-montserrat me-3">Privacy Policy</a>
            <a href="terms.php" class="small footer-muted-text font-montserrat">Terms of Service</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/terms.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>