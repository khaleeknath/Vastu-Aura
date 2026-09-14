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
  <title>Consultations & Rituals | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bespoke E-Commerce Styles -->
  <link rel="stylesheet" href="./assets/css/services.css">
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
    <!-- Services Hero -->
    <section class="services-editorial-hero position-relative section-padding pb-0">
      <div class="container mt-5 pt-4 text-center z-index-2 reveal-up">
        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Guidance & Intervention</span>
        <h1 class="cinzel-heading display-2 mb-4 text-dark-900">Sacred Consultations</h1>
        <div class="ornate-divider mx-auto mb-5"></div>
        <p class="font-montserrat text-muted-large mx-auto mb-0" style="max-width: 700px;">
          Transform your residential, commercial, and personal energetic landscape through the ancient, unbroken sciences of architecture and cosmic alignment.
        </p>
      </div>
    </section>

    <!-- 1. Vedic Astrology Showcase -->
    <section id="astrology" class="service-showcase section-padding pt-5">
      <div class="container">
        <div class="row g-5 align-items-center">
          <div class="col-lg-6 position-relative reveal-up">
            <div class="showcase-image-wrapper">
              <div class="showcase-image" style="background-image: url('https://images.unsplash.com/photo-1534447677768-be436bb09401?auto=format&fit=crop&q=80');"></div>
              <div class="showcase-glow"></div>
            </div>
          </div>
          <div class="col-lg-6 ps-lg-5 reveal-up stagger-1">
            <span class="card-numeral text-gold opacity-50 font-cinzel d-block mb-2">01</span>
            <h2 class="cinzel-heading display-4 mb-4">Vedic Astrology</h2>
            <p class="font-montserrat text-muted-large mb-4">
              Your natal chart is the cosmic blueprint of your soul's journey. Through rigorous, classical Vedic calculations, S. Ramesh decodes planetary positions to offer profound clarity on your career trajectory, relationship dynamics, and life purpose.
            </p>
            <ul class="bespoke-list font-montserrat mb-5">
              <li><span class="list-bullet">✦</span> Natal Chart Formulation & Deep Analysis</li>
              <li><span class="list-bullet">✦</span> Planetary Transit (Dasha) Forecasting</li>
              <li><span class="list-bullet">✦</span> Prescriptive Gemstone Recommendations</li>
            </ul>
            <a href="booking.php?service=astrology" class="btn-luxury-solid">Schedule a Reading</a>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. Numerology Showcase (Reversed Layout) -->
    <section id="numerology" class="service-showcase section-padding bg-white">
      <div class="container">
        <div class="row g-5 align-items-center flex-column-reverse flex-lg-row">
          <div class="col-lg-6 pe-lg-5 reveal-up stagger-1">
            <span class="card-numeral text-gold opacity-50 font-cinzel d-block mb-2">02</span>
            <h2 class="cinzel-heading display-4 mb-4">Numerology</h2>
            <p class="font-montserrat text-muted-large mb-4">
              Numbers hold distinct vibrational frequencies that silently govern our daily lives. By analyzing your birth date and name, we uncover hidden potentials, identify energetic blockages, and align your numerical signature with prosperity.
            </p>
            <ul class="bespoke-list font-montserrat mb-5">
              <li><span class="list-bullet">✦</span> Name Correction & Frequency Alignment</li>
              <li><span class="list-bullet">✦</span> Business Branding & Naming Strategy</li>
              <li><span class="list-bullet">✦</span> Auspicious Date Selection for Major Events</li>
            </ul>
            <a href="booking.php?service=numerology" class="btn-luxury-solid">Book a Consultation</a>
          </div>
          <div class="col-lg-6 position-relative reveal-up">
            <div class="showcase-image-wrapper right-align">
              <div class="showcase-image" style="background-image: url('https://images.unsplash.com/photo-1606555198083-a419eb45e6eb?auto=format&fit=crop&q=80'); filter: sepia(0.2);"></div>
              <div class="showcase-glow"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 3. Vastu Fire Rituals -->
    <section id="vastu-fire" class="service-showcase section-padding bg-dark-purple text-white">
      <div class="container">
        <div class="row g-5 align-items-center">
          <div class="col-lg-6 position-relative reveal-up">
            <div class="showcase-image-wrapper">
              <div class="showcase-image" style="background-image: url('https://images.unsplash.com/photo-1542458428-2c2e0b5368a4?auto=format&fit=crop&q=80');"></div>
              <div class="showcase-glow glow-gold"></div>
            </div>
          </div>
          <div class="col-lg-6 ps-lg-5 reveal-up stagger-1">
            <span class="card-numeral text-gold opacity-50 font-cinzel d-block mb-2">03</span>
            <h2 class="cinzel-heading display-4 mb-4 text-white">Vastu Fire Rituals</h2>
            <p class="font-montserrat text-white-75 mb-4" style="line-height: 1.8; font-size: 1.1rem;">
              When structural corrections are impossible, the elements must be invoked. Vastu Fire (Agni) Rituals are powerful, ancient ceremonies designed to purge deeply rooted stagnant energy, neutralize architectural doshas, and invite abundance back into your space.
            </p>
            <ul class="bespoke-list font-montserrat mb-5 text-white-75">
              <li><span class="list-bullet text-gold">✦</span> Space Purification & Energy Clearing</li>
              <li><span class="list-bullet text-gold">✦</span> Neutralization of Severe Vastu Doshas</li>
              <li><span class="list-bullet text-gold">✦</span> Conducted by Certified Vedic Scholars</li>
            </ul>
            <a href="contact.php?inquiry=fire-ritual" class="btn-luxury-outline text-white">Inquire About Rituals</a>
          </div>
        </div>
      </div>
    </section>

    <!-- 4. The Preserved Content: Specialized Vastu Audits -->
    <section class="specialized-audits section-padding bg-muted">
      <div class="container">
        <div class="text-center mb-5 pb-3 reveal-up">
          <span class="text-purple tracking-wide text-uppercase small font-montserrat fw-bold mb-2 d-block">Structural Interventions</span>
          <h2 class="cinzel-heading display-5">Specialized Vastu Audits</h2>
          <p class="font-montserrat text-muted-large mt-3 mx-auto" style="max-width: 600px;">
            Comprehensive spatial energy mapping tailored to the specific needs of your property.
          </p>
        </div>

        <div class="row g-4">
          <!-- Item 1 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-1">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Residential Vastu</h3>
              <p class="font-montserrat text-muted mb-0 small">Harmonize your home's energy flow. We analyze layouts, entrances, and room placements to foster peace, health, and familial prosperity.</p>
            </div>
          </div>
          <!-- Item 2 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-2">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Commercial Vastu</h3>
              <p class="font-montserrat text-muted mb-0 small">Optimize your retail or commercial property to attract growth. We align entrances, cash counters, and inventory spaces for success.</p>
            </div>
          </div>
          <!-- Item 3 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-3">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Industrial Vastu</h3>
              <p class="font-montserrat text-muted mb-0 small">Structural alignment for manufacturing units and factories to ensure smooth operations, reduce friction, and maximize output efficiency.</p>
            </div>
          </div>
          <!-- Item 4 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-1">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Plot & Land Vastu</h3>
              <p class="font-montserrat text-muted mb-0 small">Expert soil, slope, and shape analysis before construction begins. Ensure the foundational energy of your property is auspicious.</p>
            </div>
          </div>
          <!-- Item 5 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-2">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Office Space Vastu</h3>
              <p class="font-montserrat text-muted mb-0 small">Enhance workplace productivity and leadership dynamics through strategic desk placements, directional facing, and cabin arrangements.</p>
            </div>
          </div>
          <!-- Item 6 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-3">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Interior Design Vastu</h3>
              <p class="font-montserrat text-muted mb-0 small">Micro-level adjustments focusing on color palettes, furniture placement, mirrors, and decor to balance the internal aura of a room.</p>
            </div>
          </div>
          <!-- Item 7 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-1">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Vastu Audit & Analysis</h3>
              <p class="font-montserrat text-muted mb-0 small">A comprehensive, multi-point digital or on-site inspection of your current property to identify exact energetic blockages or doshas.</p>
            </div>
          </div>
          <!-- Item 8 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-2">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Corrections & Remedies</h3>
              <p class="font-montserrat text-muted mb-0 small">Non-destructive, ritual-led remedies using brass elements, crystals, yantras, and pyramids to correct structural Vastu flaws.</p>
            </div>
          </div>
          <!-- Item 9 -->
          <div class="col-lg-4 col-md-6 reveal-up stagger-3">
            <div class="audit-card">
              <h3 class="cinzel-heading text-dark-900 fs-4 mb-3">Personalized Guidance</h3>
              <p class="font-montserrat text-muted mb-0 small">One-on-one virtual or in-person sessions tailored to your specific life goals, astrological charts, and spatial constraints.</p>
            </div>
          </div>
        </div>
        
        <div class="text-center mt-5 pt-3 reveal-up">
            <a href="booking.php" class="link-luxury">Request an Audit <span class="arrow">→</span></a>
        </div>
      </div>
    </section>

  </main>

  <!-- Premium Dark Luxury Footer -->
  <footer class="bespoke-footer border-top">
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
  <script src="assets/js/services.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>