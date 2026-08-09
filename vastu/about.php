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
  <title>About Us | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/about.css">
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
          <li class="nav-item"><a class="nav-link active" href="about.php">About Us</a></li>
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
    <section class="hero-shell">
      <div class="container">
        <span class="eyebrow">About Our Practice</span>
        <div class="row align-items-end g-4">
          <div class="col-lg-7">
            <h1>We blend traditional Vastu thinking with practical modern interiors.</h1>
          </div>
          <div class="col-lg-5">
            <p>VastuAura helps homeowners, founders, and wellness-led brands create spaces that feel grounded, directional, and intentional.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="row g-4 align-items-center">
          <div class="col-lg-5">
            <div class="portrait-panel">
              <span>Est. 2016</span>
              <h2>Human-centered guidance over one-size-fits-all prescriptions.</h2>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="story-grid">
              <article class="story-card">
                <h3>Our Mission</h3>
                <p>Make Vastu consultation approachable through calm communication, actionable steps, and a client journey that feels premium at every touchpoint.</p>
              </article>
              <article class="story-card">
                <h3>Our Approach</h3>
                <p>We study orientation, energy flow, room usage, and lifestyle patterns before recommending changes that fit your real environment.</p>
              </article>
              <article class="story-card">
                <h3>Our Promise</h3>
                <p>Clarity, warmth, and clean execution across consultations, product recommendations, and follow-up documentation.</p>
              </article>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="timeline-shell">
      <div class="container">
        <div class="section-heading">
          <span class="eyebrow">Journey</span>
          <h2>How VastuAura grew into a multi-service agency.</h2>
        </div>
        <div class="timeline-list">
          <div class="timeline-item">
            <span>2016</span>
            <div>
              <h3>Started with residential consultations</h3>
              <p>Focused on home layouts, entrances, bedroom energy, and color harmonization.</p>
            </div>
          </div>
          <div class="timeline-item">
            <span>2020</span>
            <div>
              <h3>Expanded into remote assessments</h3>
              <p>Introduced digital reports and online sessions for clients across India and overseas.</p>
            </div>
          </div>
          <div class="timeline-item">
            <span>2024</span>
            <div>
              <h3>Launched Vastu product curation</h3>
              <p>Added carefully selected accessories and remedies aligned with consultant recommendations.</p>
            </div>
          </div>
          <div class="timeline-item">
            <span>2026</span>
            <div>
              <h3>Built a complete digital-first experience</h3>
              <p>Unified website, store, booking flow, and admin dashboard into one polished frontend system.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="values-shell">
      <div class="container">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="value-card">
              <h3>Precision</h3>
              <p>Recommendations are mapped to actual room usage, family needs, and spatial constraints.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="value-card">
              <h3>Calm</h3>
              <p>We design around clarity and ease, so the journey feels supportive rather than overwhelming.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="value-card">
              <h3>Continuity</h3>
              <p>Consultation, purchase, appointment tracking, and admin operations all feel connected.</p>
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
  <script src="assets/js/about.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>
