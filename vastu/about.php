<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);
$cartCount = $_SESSION['cart_count'] ?? 0;
// Preserved Authentication Logic
// if (!isset($_SESSION['user_id'])) {
//   $_SESSION['error'] = "Please login First to Access Page";
//     header("Location: login.php");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Our Wisdom & Lineage | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bespoke E-Commerce Styles -->
  <link rel="stylesheet" href="./assets/css/about.css">
  <link rel="stylesheet" href="./assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>
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

  <main>
    <!-- Section 1: Editorial Portrait Hero -->
    <section class="about-editorial-hero section-padding pb-0">
      <div class="container mt-5">
        <div class="row g-5 align-items-center">
          <div class="col-lg-5 position-relative reveal-up">
            <div class="portrait-frame">
              <!-- Placeholder for S. Ramesh Portrait -->
              <div class="portrait-image" style="background-image: url('assets/images/about_img.png?auto=format&fit=crop&q=80');"></div>
              <div class="frame-accent"></div>
            </div>
          </div>
          <div class="col-lg-7 ps-lg-5 reveal-up stagger-1">
            <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">The Master Architect of Destiny</span>
            <h1 class="cinzel-heading display-3 mb-4 text-dark-900">S. Ramesh</h1>
            <div class="ornate-divider-horizontal mb-4"></div>
            <p class="font-montserrat text-muted-large mb-4">
              With decades of profound immersion into the sacred sciences of Vedic Astrology, Numerology, and Vastu Shastra, S. Ramesh has dedicated his life to decoding the energetic blueprints that govern our existence.
            </p>
            <p class="font-montserrat text-muted-large">
              Vastu Shakti Rahasya is the culmination of this lifelong pursuit—a sanctuary where ancient traditional wisdom is meticulously translated to harmonize the complexities of modern living.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 2: The Philosophy (Immersive Dark Section) -->
    <section class="philosophy-section section-padding bg-dark-purple mt-5">
      <div class="container text-center reveal-up">
        <svg class="quote-icon mb-4" width="40" height="40" viewBox="0 0 24 24" fill="var(--clr-gold-main)"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
        <h2 class="cinzel-heading display-5 text-white mb-5 mx-auto" style="max-width: 900px; line-height: 1.4;">
          "An aligned home is not merely about shifting furniture. It is the sacred act of tuning your physical environment to the frequency of your highest potential."
        </h2>
      </div>
      
      <div class="container mt-5 pt-4">
        <div class="row g-4 justify-content-center">
          <div class="col-lg-4 col-md-6 reveal-up stagger-1">
            <div class="philosophy-card glass-panel">
              <span class="card-numeral text-gold opacity-50 font-cinzel">I</span>
              <h3 class="cinzel-heading text-white mt-3 mb-3">Authentic Lineage</h3>
              <p class="font-montserrat text-white-75 mb-0 small">
                Every consultation, gemstone, and yantra is deeply rooted in the pure, unadulterated texts of Vedic science, ensuring remedies that hold genuine energetic power.
              </p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 reveal-up stagger-2">
            <div class="philosophy-card glass-panel">
              <span class="card-numeral text-gold opacity-50 font-cinzel">II</span>
              <h3 class="cinzel-heading text-white mt-3 mb-3">Energetic Purity</h3>
              <p class="font-montserrat text-white-75 mb-0 small">
                From lab-certified healing bracelets to precise Vastu fire rituals, we prioritize the uncompromised quality and spiritual activation of every element we offer.
              </p>
            </div>
          </div>
          <div class="col-lg-4 col-md-6 reveal-up stagger-3">
            <div class="philosophy-card glass-panel">
              <span class="card-numeral text-gold opacity-50 font-cinzel">III</span>
              <h3 class="cinzel-heading text-white mt-3 mb-3">Human-Centric Guidance</h3>
              <p class="font-montserrat text-white-75 mb-0 small">
                We replace fear-based superstition with compassionate, logical, and highly personalized architectural and astrological guidance.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 3: The Path of Mastery (Vertical Journey) -->
    <section class="lineage-timeline section-padding bg-white">
      <div class="container">
        <div class="text-center mb-5 pb-4 reveal-up">
          <span class="text-purple tracking-wide text-uppercase small font-montserrat fw-bold mb-2 d-block">The Evolution</span>
          <h2 class="cinzel-heading display-5">The Path to Rahasya</h2>
          <div class="ornate-divider mx-auto my-3"></div>
        </div>

        <div class="bespoke-timeline mt-5">
          <div class="timeline-line"></div>
          
          <div class="timeline-block reveal-up">
            <div class="timeline-node"></div>
            <div class="timeline-content">
              <h4 class="font-montserrat text-gold fw-bold text-uppercase tracking-wide small mb-2">The Calling</h4>
              <h3 class="cinzel-heading display-6 mb-3">Immersion in Sacred Texts</h3>
              <p class="font-montserrat text-muted-large">S. Ramesh began an intense, multi-decade study into the core Vedic scriptures, deciphering the mathematical and energetic correlations between human destiny and spatial geometry.</p>
            </div>
          </div>

          <div class="timeline-block reveal-up stagger-1">
            <div class="timeline-node"></div>
            <div class="timeline-content">
              <h4 class="font-montserrat text-gold fw-bold text-uppercase tracking-wide small mb-2">The Foundation</h4>
              <h3 class="cinzel-heading display-6 mb-3">Establishing the Practice</h3>
              <p class="font-montserrat text-muted-large">Transitioning from foundational study to active practice, providing highly sought-after residential and commercial Vastu audits that yielded profound transformations for early clients.</p>
            </div>
          </div>

          <div class="timeline-block reveal-up stagger-2">
            <div class="timeline-node"></div>
            <div class="timeline-content">
              <h4 class="font-montserrat text-gold fw-bold text-uppercase tracking-wide small mb-2">The Expansion</h4>
              <h3 class="cinzel-heading display-6 mb-3">Curating the Elements</h3>
              <p class="font-montserrat text-muted-large">Recognizing the need for authentic remedies, the practice expanded to source and energetically activate premium gemstones, yantras, and healing bracelets to support the architectural guidance.</p>
            </div>
          </div>

          <div class="timeline-block reveal-up stagger-3">
            <div class="timeline-node"></div>
            <div class="timeline-content">
              <h4 class="font-montserrat text-gold fw-bold text-uppercase tracking-wide small mb-2">The Legacy</h4>
              <h3 class="cinzel-heading display-6 mb-3">Vastu Shakti Rahasya</h3>
              <p class="font-montserrat text-muted-large">The culmination of the journey: a holistic, premium platform offering seamless access to expert consultations, authentic products, and spiritual alignment for a global clientele.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Section 4: Seamless CTA -->
    <section class="luxury-cta section-padding">
      <div class="container text-center reveal-up">
        <h2 class="cinzel-heading display-4 mb-4">Ready to Align Your Space?</h2>
        <p class="font-montserrat text-muted-large mb-5 mx-auto" style="max-width: 600px;">
          Connect with S. Ramesh to uncover the energetic blueprint of your home and chart a course toward abundance and tranquility.
        </p>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
          <a href="booking.php" class="btn-luxury-solid">Schedule a Consultation</a>
          <a href="store.php" class="btn-luxury-outline text-dark">Explore Sacred Artifacts</a>
        </div>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/about.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>