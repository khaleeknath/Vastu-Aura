
<?php
session_start();

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
  <title>Contact Us | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/contact.css">
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
          <li class="nav-item"><a class="nav-link active" href="contact.php">Contact Us</a></li>
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
    <section class="hero-shell">
      <div class="container">
        <span class="eyebrow">Get In Touch</span>
        <h1>Let’s align your home, office, or client experience.</h1>
        <p>Reach out for consultation guidance, product recommendations, or partnership conversations.</p>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="contact-card">
              <h2>Send an enquiry</h2>
              <form id="contactForm" class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Full Name</label>
                  <input type="text" class="form-control" placeholder="Your full name" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input type="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" placeholder="+91">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Topic</label>
                  <select class="form-select">
                    <option>Consultation</option>
                    <option>Product Enquiry</option>
                    <option>Partnership</option>
                    <option>General Support</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Message</label>
                  <textarea class="form-control" rows="5" placeholder="Tell us what space you would like help with."></textarea>
                </div>
                <div class="col-12">
                  <button class="btn btn-brand" type="submit">Submit Enquiry</button>
                </div>
              </form>
              <div id="contactAlert" class="alert alert-success mt-4 d-none" role="alert"></div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="info-stack">
              <div class="info-card">
                <span>Studio</span>
                <h3>Vyomark House, Jaipur</h3>
                <p>Open Mon-Sat, 10 AM to 7 PM for scheduled consultations.</p>
              </div>
              <div class="info-card">
                <span>Contact</span>
                <h3>hello@vastuaura.com</h3>
                <p>+91 98765 43210<br>WhatsApp support for active clients.</p>
              </div>
              <div class="info-card">
                <span>Services</span>
                <h3>Home, office, retail and energy product guidance</h3>
                <p>Available for on-site and remote review sessions.</p>
              </div>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/contact.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>
