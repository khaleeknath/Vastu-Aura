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
  <title>Concierge & Support | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- Bespoke E-Commerce Styles -->
  <link rel="stylesheet" href="assets/css/contact.css">
  <link rel="stylesheet" href="assets/css/common-modal.css">
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
              <span id="cartCount" class="cart-badge"> <?= $cartCount ?></span>
          </a>
        </div>

      </div>
    </div>
  </nav>

  <main>
    <!-- Contact Editorial Hero -->
    <section class="contact-editorial-hero position-relative section-padding pb-0">
      <div class="hero-bg-elements">
        <div class="ambient-glow glow-1"></div>
      </div>
      <div class="container mt-5 pt-4 text-center z-index-2 reveal-up">
        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Concierge & Support</span>
        <h1 class="cinzel-heading display-2 mb-4 text-dark-900">Reach the Atelier</h1>
        <div class="ornate-divider mx-auto mb-4"></div>
        <p class="font-montserrat text-muted-large mx-auto mb-5" style="max-width: 700px;">
          Whether you seek profound consultation guidance, details regarding our sacred artifacts, or wish to discuss a commercial partnership, our concierge is here to assist you.
        </p>
      </div>
    </section>

    <!-- Main Contact Area -->
    <section class="contact-interface section-padding pt-4">
      <div class="container">
        <div class="row g-5">
          
          <!-- Left Column: The Premium Form -->
          <div class="col-lg-7 reveal-up stagger-1">
            <div class="luxury-form-card bg-white p-4 p-md-5">
              <h2 class="cinzel-heading text-dark-900 mb-4 pb-2 border-bottom">Direct Enquiry</h2>
              
              <form id="contactForm" class="row g-4 font-montserrat">
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Full Name</label>
                  <input type="text" name="full_name" class="form-control luxury-input" placeholder="Your full name" required>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Email Address</label>
                  <input type="email" name="email" class="form-control luxury-input" placeholder="name@example.com" required>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Mobile Number</label>
                  <input type="tel" name="mobile" class="form-control luxury-input" placeholder="+91">
                </div>
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Nature of Enquiry</label>
                  <select name="enquiry_type" class="form-select luxury-input" required>
                    <option value="" disabled selected>Select a topic...</option>
                    <option>Consultation Details</option>
                    <option>Sacred Store & Products</option>
                    <option>Commercial Partnership</option>
                    <option>General Support</option>
                  </select>
                </div>
                
                <div class="col-12">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Your Message</label>
                  <textarea name="message" class="form-control luxury-input" rows="5" placeholder="Tell us how we can assist you..." required></textarea>
                </div>
                
                <div class="col-12 mt-5">
                  <button class="btn-luxury-solid w-100" type="submit">Submit Enquiry</button>
                </div>
              </form>
              
              <!-- Refined Alert Box -->
              <div id="contactAlert" class="luxury-alert mt-4 d-none">
                 <span class="text-gold me-2">✦</span> 
                 <span class="alert-message"></span>
              </div>
            </div>
          </div>

          <!-- Right Column: Information Cards -->
          <div class="col-lg-5 pe-lg-4 reveal-up stagger-2">
            <div class="contact-info-stack d-flex flex-column gap-4">
              
              <!-- Info Card 1 -->
              <div class="contact-info-card bg-dark-900 text-white p-4 p-md-5 position-relative overflow-hidden">
                <div class="card-glow-overlay"></div>
                <div class="position-relative z-index-2">
                  <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">The Studio</span>
                  <h3 class="cinzel-heading text-white fs-3 mb-2">Vyomark House, Jaipur</h3>
                  <p class="font-montserrat text-white-75 mb-0">
                    Open Mon-Sat, 10 AM to 7 PM.<br>Available strictly for scheduled on-site consultations.
                  </p>
                </div>
              </div>

              <!-- Info Card 2 -->
              <div class="contact-info-card bg-white p-4 p-md-5 border">
                <span class="text-purple tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Direct Communication</span>
                <h3 class="cinzel-heading text-dark-900 fs-4 mb-2">hello@vastushaktirahasya.com</h3>
                <p class="font-montserrat text-muted mb-0">
                  <strong class="text-dark-900">+91 98765 43210</strong><br>
                  Dedicated WhatsApp concierge support available for our active consultation clients.
                </p>
              </div>

              <!-- Info Card 3 -->
              <div class="contact-info-card bg-white p-4 p-md-5 border">
                <span class="text-purple tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Global Reach</span>
                <h3 class="cinzel-heading text-dark-900 fs-4 mb-2">Remote Sessions</h3>
                <p class="font-montserrat text-muted mb-4">
                  S. Ramesh conducts extensive virtual energy mapping and astrological chart readings for our international clientele.
                </p>
                <a href="booking.php" class="link-luxury">Book a Virtual Session <span class="arrow">→</span></a>
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
  <script src="assets/js/contact.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>