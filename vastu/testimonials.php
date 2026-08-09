
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
  <title>Testimonials | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/testimonials.css">
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
          <li class="nav-item"><a class="nav-link active" href="testimonials.php">Testimonials</a></li>
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
        <span class="eyebrow">Client Voices</span>
        <h1>Spaces transformed, routines softened, businesses energized.</h1>
        <p>Real stories from homeowners, entrepreneurs, and wellness brands who trusted VastuAura.</p>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="filter-bar">
          <button class="filter-btn active" data-filter="all">All</button>
          <button class="filter-btn" data-filter="residential">Residential</button>
          <button class="filter-btn" data-filter="office">Office</button>
          <button class="filter-btn" data-filter="retail">Retail</button>
        </div>
        <div class="row g-4" id="testimonialGrid">
          <div class="col-md-6 col-xl-4 testimonial-item" data-group="residential">
            <article class="testimonial-card">
              <span>Residential</span>
              <p>“Our home instantly felt calmer after the recommended entry changes and bedroom layout corrections.”</p>
              <h3>Rhea &amp; Arjun Mehta</h3>
              <small>Mumbai Apartment Consultation</small>
            </article>
          </div>
          <div class="col-md-6 col-xl-4 testimonial-item" data-group="office">
            <article class="testimonial-card">
              <span>Office</span>
              <p>“The new workstation plan and reception corrections brought a visible improvement in team focus.”</p>
              <h3>Sandeep Rao</h3>
              <small>Creative Agency Studio</small>
            </article>
          </div>
          <div class="col-md-6 col-xl-4 testimonial-item" data-group="retail">
            <article class="testimonial-card">
              <span>Retail</span>
              <p>“We appreciated how modern and practical the recommendations were. Nothing felt superstitious or forced.”</p>
              <h3>Naina Khanna</h3>
              <small>Boutique Wellness Store</small>
            </article>
          </div>
          <div class="col-md-6 col-xl-4 testimonial-item" data-group="residential">
            <article class="testimonial-card">
              <span>Residential</span>
              <p>“The remote consultation report was beautifully structured and easy to implement room by room.”</p>
              <h3>Devika Menon</h3>
              <small>Bengaluru Villa Review</small>
            </article>
          </div>
          <div class="col-md-6 col-xl-4 testimonial-item" data-group="office">
            <article class="testimonial-card">
              <span>Office</span>
              <p>“We used both the consultation and the product kit. The journey felt premium from start to finish.”</p>
              <h3>Keshav Batra</h3>
              <small>Co-working Lounge</small>
            </article>
          </div>
          <div class="col-md-6 col-xl-4 testimonial-item" data-group="retail">
            <article class="testimonial-card">
              <span>Retail</span>
              <p>“Appointments were easy to book, and the follow-up suggestions were exactly what our team needed.”</p>
              <h3>Ira Sethi</h3>
              <small>Luxury Home Fragrance Brand</small>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section class="metrics-shell">
      <div class="container">
        <div class="row g-4">
          <div class="col-md-4">
            <div class="metric-card">
              <strong>93%</strong>
              <span>Clients return for a second review or product order.</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="metric-card">
              <strong>48 hrs</strong>
              <span>Average turnaround for personalized digital reports.</span>
            </div>
          </div>
          <div class="col-md-4">
            <div class="metric-card">
              <strong>18 Cities</strong>
              <span>Projects delivered across remote and in-person engagements.</span>
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
  <script src="assets/js/testimonials.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>
