<?php
session_start();
$cartCount = $_SESSION['cart_count'] ?? 0;
// Define the current page so the frontend navigation highlighting works without PHP warnings
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vastu Shakti Rahasya | Premium Vastu & Astrological Guidance</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bespoke E-Commerce Styles -->
  <link rel="stylesheet" href="./assets/css/index.css">
  <link rel="stylesheet" href="./assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>
</head>
<body class="luxury-ecomm">

  <!-- Progress Bar for Scroll -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- Redesigned High-End Navbar (Left-to-Right Layout) -->
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

  <main>
    <!-- Asymmetrical Editorial Hero -->
    <section class="editorial-hero position-relative">
      <div class="hero-bg-elements">
        <div class="ambient-glow glow-1"></div>
        <div class="ambient-glow glow-2"></div>
      </div>
      
      <div class="container h-100 position-relative z-index-2">
        <div class="row h-100 align-items-center pt-5">
          <div class="col-lg-6 hero-content pr-lg-5 stagger-reveal">
            <div class="hero-badge reveal-item">✧ Signature Collection</div>
            <h1 class="hero-title reveal-item mt-4 mb-4">
              Align Your Space.<br>
              <span class="text-gold">Elevate Your Destiny.</span>
            </h1>
            <p class="hero-subtitle reveal-item mb-5">
              Discover bespoke Vastu remedies, ethically sourced gemstones, and profound astrological guidance curated by master consultant S. Ramesh.
            </p>
            <!-- <div class="hero-cta-group reveal-item d-flex gap-4 align-items-center">
              <a href="store.php" class="btn-luxury-solid">Explore The Store</a>
              <a href="booking.php" class="link-luxury">Book a Consultation <span class="arrow">→</span></a>
            </div> -->
          </div>
          
          <div class="col-lg-6 d-none d-lg-block h-100 position-relative reveal-fade">
            <div class="hero-visual-composition">
                <div class="primary-visual"></div>
                <div class="secondary-visual"></div>
                <div class="floating-stat glass-panel">
                    <span class="stat-number">25+</span>
                    <span class="stat-label">Years of Mastery</span>
                </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Global Clientele Carousel Section -->
    <section class="client-carousel-section section-padding bg-muted">
      <!-- Section Header restricted to standard container width -->
      <div class="container">
        <div class="section-header text-center mb-5 pb-3 reveal-up">
          <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-2 d-block">Worldwide Trust</span>
          <h2 class="cinzel-heading display-5">Our Global Clientele</h2>
          <div class="ornate-divider mx-auto my-3"></div>
          <p class="font-montserrat text-muted-large mx-auto" style="max-width: 600px;">
            Aligning destinies across borders. A glimpse into the environments and lives we've transformed worldwide.
          </p>
        </div>
      </div>

      <!-- Carousel expanded to full viewport width -->
      <div class="container-fluid px-0 reveal-up stagger-1">
        <div id="clientCarousel" class="carousel slide luxury-carousel carousel-fade" data-bs-ride="carousel" data-bs-interval="4500">
          
          <!-- Indicators -->
          <div class="carousel-indicators custom-indicators">
            <button type="button" data-bs-target="#clientCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#clientCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#clientCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#clientCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
            <button type="button" data-bs-target="#clientCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
            <button type="button" data-bs-target="#clientCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
            <button type="button" data-bs-target="#clientCarousel" data-bs-slide-to="6" aria-label="Slide 7"></button>
          </div>
          
          <!-- Images -->
          <div class="carousel-inner overflow-hidden">
            <div class="carousel-item active">
              <img src="assets/images/client1.jpeg" class="d-block w-100 carousel-img" alt="Global Client 1">
            </div>
            <div class="carousel-item">
              <img src="assets/images/client2.jpeg" class="d-block w-100 carousel-img" alt="Global Client 2">
            </div>
            <div class="carousel-item">
              <img src="assets/images/client3.jpeg" class="d-block w-100 carousel-img" alt="Global Client 3">
            </div>
            <div class="carousel-item">
              <img src="assets/images/client4.jpeg" class="d-block w-100 carousel-img" alt="Global Client 4">
            </div>
            <div class="carousel-item">
              <img src="assets/images/client5.jpeg" class="d-block w-100 carousel-img" alt="Global Client 5">
            </div>
            <div class="carousel-item">
              <img src="assets/images/client6.jpeg" class="d-block w-100 carousel-img" alt="Global Client 6">
            </div>
            <div class="carousel-item">
              <img src="assets/images/client7.jpeg" class="d-block w-100 carousel-img" alt="Global Client 7">
            </div>
          </div>
          
          <!-- Navigation Controls -->
          <button class="carousel-control-prev custom-control" type="button" data-bs-target="#clientCarousel" data-bs-slide="prev">
            <span class="control-icon" aria-hidden="true">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
            </span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next custom-control" type="button" data-bs-target="#clientCarousel" data-bs-slide="next">
            <span class="control-icon" aria-hidden="true">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
            </span>
            <span class="visually-hidden">Next</span>
          </button>
          
        </div>
      </div>
    </section>

    <!-- Curated E-Commerce Collections -->
    <section class="curated-collections bg-white section-padding">
      <div class="container">
        <div class="section-header text-center mb-5 pb-3 reveal-up">
          <h2 class="cinzel-heading display-5">Sacred Artifacts</h2>
          <div class="ornate-divider mx-auto my-3"></div>
          <p class="font-montserrat text-muted-large">Lab-certified and energetically cleansed for your prosperity.</p>
        </div>

        <div class="row g-4">
          <div class="col-md-4 reveal-up stagger-1">
            <a href="store.php?category=gemstones" class="collection-card">
              <div class="card-image-wrapper">
                <div class="image-placeholder bg-gemstone"></div>
                <div class="card-overlay"></div>
              </div>
              <div class="card-info">
                <h3>Vedic Gemstones</h3>
                <span class="shop-link">View Collection</span>
              </div>
            </a>
          </div>
          <div class="col-md-4 reveal-up stagger-2">
            <a href="store.php?category=bracelets" class="collection-card">
              <div class="card-image-wrapper">
                <div class="image-placeholder bg-bracelet"></div>
                <div class="card-overlay"></div>
              </div>
              <div class="card-info">
                <h3>Healing Bracelets</h3>
                <span class="shop-link">View Collection</span>
              </div>
            </a>
          </div>
          <div class="col-md-4 reveal-up stagger-3">
            <a href="store.php?category=yantras" class="collection-card">
              <div class="card-image-wrapper">
                <div class="image-placeholder bg-yantra"></div>
                <div class="card-overlay"></div>
              </div>
              <div class="card-info">
                <h3>Vastu Yantras</h3>
                <span class="shop-link">View Collection</span>
              </div>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- The Master's Approach (Statement Section) -->
    <section class="master-statement section-padding bg-muted">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-lg-9 text-center reveal-up">
            <svg class="quote-icon mb-4" width="40" height="40" viewBox="0 0 24 24" fill="var(--clr-gold-main)"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
            <h2 class="cinzel-heading display-6 mb-5 statement-text">
              "True prosperity is not forced; it is allowed to flow when the architecture of your home aligns with the blueprint of your destiny."
            </h2>
            <div class="master-signature">
                <span class="name text-purple d-block fs-5 fw-bold font-cinzel">S. Ramesh</span>
                <span class="title text-muted-large font-montserrat">Founder & Chief Consultant</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Bento Grid Services Layout -->
    <section class="spiritual-services section-padding bg-dark-purple text-white">
      <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 reveal-up">
          <div>
            <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-2 d-block">Guidance</span>
            <h2 class="cinzel-heading display-5 mb-0">Consultation Services</h2>
          </div>
          <a href="services.php" class="link-luxury text-white d-none d-md-inline-flex">All Services <span class="arrow">→</span></a>
        </div>

        <div class="bento-grid">
          <!-- Main Large Block -->
          <div class="bento-item item-large glass-panel reveal-up stagger-1">
            <div class="bento-content">
              <span class="motif">❂</span>
              <h3 class="cinzel-heading text-gold mt-3 mb-3">Vedic Astrology & Numerology</h3>
              <p class="font-montserrat text-white-75 mb-4">A comprehensive analysis of your planetary positions and numerical vibrations to unlock clarity in career, relationships, and life purpose.</p>
              <a href="booking.php" class="btn-luxury-outline w-100 text-center">Schedule Reading</a>
            </div>
            <div class="bento-bg bg-astro"></div>
          </div>

          <!-- Top Right Block -->
          <div class="bento-item item-medium glass-panel reveal-up stagger-2">
            <div class="bento-content">
              <h3 class="cinzel-heading text-white mb-2">Vastu Architecture</h3>
              <p class="font-montserrat text-white-75 small mb-4">Structural energy mapping for homes and commercial workspaces.</p>
              <a href="services.php#vastu" class="link-luxury text-gold mt-auto">Learn More <span class="arrow">→</span></a>
            </div>
          </div>

          <!-- Bottom Right Block -->
          <div class="bento-item item-medium glass-panel reveal-up stagger-3">
            <div class="bento-content">
              <h3 class="cinzel-heading text-white mb-2">Vastu Fire Rituals</h3>
              <p class="font-montserrat text-white-75 small mb-4">Purification ceremonies designed to eliminate deeply rooted stagnant energies.</p>
              <a href="services.php#fire" class="link-luxury text-gold mt-auto">Learn More <span class="arrow">→</span></a>
            </div>
          </div>
        </div>
        
        <div class="mt-4 text-center d-block d-md-none reveal-up">
            <a href="services.php" class="link-luxury text-white">View All Services <span class="arrow">→</span></a>
        </div>
      </div>
    </section>

    <!-- Seamless Newsletter / CTA -->
    <section class="luxury-cta section-padding">
      <div class="container text-center reveal-up">
        <h2 class="cinzel-heading display-4 mb-4">Join The Inner Circle</h2>
        <p class="font-montserrat text-muted-large mb-5 mx-auto" style="max-width: 600px;">
          Subscribe to receive auspicious timing alerts, planetary transition updates, and exclusive access to limited gemstone collections.
        </p>
        <form class="bespoke-subscribe mx-auto">
          <input type="email" placeholder="Your Email Address" required>
          <button type="submit" class="btn-luxury-solid">Subscribe</button>
        </form>
      </div>
    </section>
  </main>

  <!-- Premium Dark Luxury Footer -->
  <footer class="bespoke-footer">
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

  <div class="subscribe-popup-overlay" id="subscribePopupOverlay">
  <div class="subscribe-popup-box">
    <div class="subscribe-popup-icon">&#10003;</div>
    <h3 class="cinzel-heading">Subscribed Successfully!</h3>
    <p class="font-montserrat">Thank you for joining The Inner Circle. Watch your inbox for auspicious updates.</p>
    <button class="btn-luxury-solid" id="closeSubscribePopup">Close</button>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/index.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>