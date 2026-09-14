<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$cartCount = $_SESSION['cart_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Client Voices & Testimonials | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bespoke Styles -->
  <link rel="stylesheet" href="assets/css/testimonials.css">
  <link rel="stylesheet" href="assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>
</head>
<body class="luxury-ecomm bg-muted">

  <!-- Progress Bar for Scroll -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- Redesigned High-End Navbar (Consistent Left-to-Right Layout) -->
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
            <a class="nav-link <?= ($current_page == 'store.php') ? 'active' : '' ?>" href="store.php">Curated Store</a>
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
    <!-- Testimonials Editorial Hero -->
    <section class="testimonials-editorial-hero position-relative section-padding pb-0 bg-muted">
      <div class="hero-bg-elements">
        <div class="ambient-glow glow-1"></div>
      </div>
      <div class="container mt-5 pt-4 text-center z-index-2 reveal-up">
        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Client Voices</span>
        <h1 class="cinzel-heading display-2 mb-4 text-dark-900">Spaces Transformed</h1>
        <div class="ornate-divider mx-auto mb-4"></div>
        <p class="font-montserrat text-muted-large mx-auto mb-5" style="max-width: 700px;">
          Real stories from homeowners, entrepreneurs, and wellness brands who trusted Vastu Shakti Rahasya to align their architecture with their destiny.
        </p>
      </div>
    </section>

    <!-- Filters Section -->
    <section class="testimonials-filters-section pb-4 bg-muted">
      <div class="container reveal-up stagger-1">
        <div class="testimonial-filter-bar d-flex justify-content-center flex-wrap gap-2 gap-md-4">
          <button class="btn-filter active" data-filter="all">All Stories</button>
          <button class="btn-filter" data-filter="residential">Residential</button>
          <button class="btn-filter" data-filter="office">Office</button>
          <button class="btn-filter" data-filter="retail">Retail</button>
        </div>
      </div>
    </section>

    <!-- Dynamic Testimonials Grid -->
    <section class="dynamic-testimonials section-padding pt-5 bg-muted">
      <div class="container">
        <div class="row g-4 g-lg-5" id="testimonialGrid">
          
          <div class="col-md-6 col-lg-4 testimonial-item reveal-up stagger-1" data-group="residential">
            <article class="luxury-testimonial-card h-100">
              <span class="testimonial-category text-gold font-montserrat d-block mb-3">Residential</span>
              <p class="testimonial-desc font-montserrat text-dark-900 fs-5 fst-italic flex-grow-1">“Our home instantly felt calmer after the recommended entry changes and bedroom layout corrections.”</p>
              <div class="mt-4 pt-3 border-top border-light">
                <h3 class="cinzel-heading fs-4 mb-1 text-dark-900">Rhea &amp; Arjun Mehta</h3>
                <small class="font-montserrat text-muted small text-uppercase tracking-wide">Mumbai Apartment</small>
              </div>
            </article>
          </div>
          
          <div class="col-md-6 col-lg-4 testimonial-item reveal-up stagger-2" data-group="office">
            <article class="luxury-testimonial-card h-100">
              <span class="testimonial-category text-gold font-montserrat d-block mb-3">Office</span>
              <p class="testimonial-desc font-montserrat text-dark-900 fs-5 fst-italic flex-grow-1">“The new workstation plan and reception corrections brought a visible improvement in team focus and overall morale.”</p>
              <div class="mt-4 pt-3 border-top border-light">
                <h3 class="cinzel-heading fs-4 mb-1 text-dark-900">Sandeep Rao</h3>
                <small class="font-montserrat text-muted small text-uppercase tracking-wide">Creative Agency Studio</small>
              </div>
            </article>
          </div>
          
          <div class="col-md-6 col-lg-4 testimonial-item reveal-up stagger-3" data-group="retail">
            <article class="luxury-testimonial-card h-100">
              <span class="testimonial-category text-gold font-montserrat d-block mb-3">Retail</span>
              <p class="testimonial-desc font-montserrat text-dark-900 fs-5 fst-italic flex-grow-1">“We appreciated how modern and practical the recommendations were. Nothing felt superstitious or forced.”</p>
              <div class="mt-4 pt-3 border-top border-light">
                <h3 class="cinzel-heading fs-4 mb-1 text-dark-900">Naina Khanna</h3>
                <small class="font-montserrat text-muted small text-uppercase tracking-wide">Boutique Wellness Store</small>
              </div>
            </article>
          </div>
          
          <div class="col-md-6 col-lg-4 testimonial-item reveal-up stagger-1" data-group="residential">
            <article class="luxury-testimonial-card h-100">
              <span class="testimonial-category text-gold font-montserrat d-block mb-3">Residential</span>
              <p class="testimonial-desc font-montserrat text-dark-900 fs-5 fst-italic flex-grow-1">“The remote consultation report was beautifully structured and easy to implement room by room. Highly recommended.”</p>
              <div class="mt-4 pt-3 border-top border-light">
                <h3 class="cinzel-heading fs-4 mb-1 text-dark-900">Devika Menon</h3>
                <small class="font-montserrat text-muted small text-uppercase tracking-wide">Bengaluru Villa Review</small>
              </div>
            </article>
          </div>
          
          <div class="col-md-6 col-lg-4 testimonial-item reveal-up stagger-2" data-group="office">
            <article class="luxury-testimonial-card h-100">
              <span class="testimonial-category text-gold font-montserrat d-block mb-3">Office</span>
              <p class="testimonial-desc font-montserrat text-dark-900 fs-5 fst-italic flex-grow-1">“We used both the consultation and the curated product kit. The journey felt premium from start to finish.”</p>
              <div class="mt-4 pt-3 border-top border-light">
                <h3 class="cinzel-heading fs-4 mb-1 text-dark-900">Keshav Batra</h3>
                <small class="font-montserrat text-muted small text-uppercase tracking-wide">Co-working Lounge</small>
              </div>
            </article>
          </div>
          
          <div class="col-md-6 col-lg-4 testimonial-item reveal-up stagger-3" data-group="retail">
            <article class="luxury-testimonial-card h-100">
              <span class="testimonial-category text-gold font-montserrat d-block mb-3">Retail</span>
              <p class="testimonial-desc font-montserrat text-dark-900 fs-5 fst-italic flex-grow-1">“Appointments were easy to book, and the follow-up suggestions were exactly what our growing retail team needed.”</p>
              <div class="mt-4 pt-3 border-top border-light">
                <h3 class="cinzel-heading fs-4 mb-1 text-dark-900">Ira Sethi</h3>
                <small class="font-montserrat text-muted small text-uppercase tracking-wide">Luxury Home Fragrance</small>
              </div>
            </article>
          </div>

        </div>
      </div>
    </section>

    <!-- Metrics Section -->
    <section class="metrics-section section-padding bg-white border-top">
      <div class="container reveal-up">
        <div class="row g-4 g-lg-5">
          <div class="col-md-4">
            <div class="luxury-metric-card h-100 p-5 text-center">
              <strong class="cinzel-heading display-4 text-gold d-block mb-2">93%</strong>
              <span class="font-montserrat text-muted-large">Clients return for a second review or product order.</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="luxury-metric-card h-100 p-5 text-center">
              <strong class="cinzel-heading display-4 text-gold d-block mb-2">48 hrs</strong>
              <span class="font-montserrat text-muted-large">Average turnaround for personalized digital reports.</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="luxury-metric-card h-100 p-5 text-center">
              <strong class="cinzel-heading display-4 text-gold d-block mb-2">18+</strong>
              <span class="font-montserrat text-muted-large">Cities reached across remote and in-person engagements.</span>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Back to Top Button -->
  <button id="backToTop" class="back-to-top" aria-label="Back to top">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
  </button>

  <!-- Premium Dark Luxury Footer -->
  <footer class="bespoke-footer mt-0">
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
  <script src="assets/js/testimonials.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>