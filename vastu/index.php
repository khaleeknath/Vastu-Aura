<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>VastuAura | Harmonious Spaces, Meaningful Living</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/index.css">
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
          <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="testimonials.php">Testimonials</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle user-dropdown" href="#" data-bs-toggle="dropdown">
        👋 Hi, <?= htmlspecialchars($_SESSION['name']) ?>
    </a>

    <ul class="dropdown-menu dropdown-menu-end vastu-dropdown">

        <li>
            <a class="dropdown-item" href="myorder.php">
                📦 My Orders
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="my-appointments.php">
                📅 My Appointments
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
    <a href="#"
       class="dropdown-item text-danger logout-btn"
       data-logout-url="assets/api/logout.php">
        🚪 Logout
    </a>
</li>

    </ul>
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
    <section class="hero-section">
      <div class="container">
        <div class="row align-items-center g-5">
          <div class="col-lg-7">
            <span class="eyebrow">Vastu Consultant Agency UI</span>
            <h1>Designing calm, aligned homes and workplaces with contemporary Vastu guidance.</h1>
            <p class="hero-copy">Discover ritual-led design recommendations, curated Vastu essentials, and a smooth booking experience built for modern clients.</p>
            <div class="hero-actions">
              <a class="btn btn-brand" href="booking.php">Book A Consultation</a>
              <a class="btn btn-outline-brand" href="store.php">Explore Store</a>
            </div>
            <div class="hero-stats">
              <div>
                <strong>1,200+</strong>
                <span>Spaces Aligned</span>
              </div>
              <div>
                <strong>4.9/5</strong>
                <span>Client Satisfaction</span>
              </div>
              <div>
                <strong>24 hrs</strong>
                <span>Average Response Window</span>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="hero-panel">
              <div class="panel-card">
                <span>North-East Focus</span>
                <strong>Light, clarity, and intuitive planning</strong>
              </div>
              <div class="panel-card accent">
                <span>Recommended</span>
                <strong>Brass energy enhancer set for entry balance</strong>
              </div>
              <div class="mini-grid">
                <div>
                  <h3>Store</h3>
                  <p>Products, cart, checkout, order status, invoice UI.</p>
                </div>
                <div>
                  <h3>Appointments</h3>
                  <p>Responsive consultation booking with prefilled fields.</p>
                </div>
                <div>
                  <h3>Admin Panel</h3>
                  <p>Orders, appointments, and admin management dashboards.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="section-heading">
          <span class="eyebrow">What We Offer</span>
          <h2>Three polished modules for your digital Vastu agency.</h2>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-xl-4">
            <article class="feature-card reveal-card">
              <span class="feature-icon">01</span>
              <h3>E-commerce Storefront</h3>
              <p>Showcase energy-balancing products with elegant listings, product details, cart flow, checkout, invoices, and order tracking.</p>
              <a href="store.php">Visit Store</a>
            </article>
          </div>
          <div class="col-md-6 col-xl-4">
            <article class="feature-card reveal-card">
              <span class="feature-icon">02</span>
              <h3>Appointment Booking</h3>
              <p>Offer guided consultation booking with prefilled client details, clean scheduling fields, and a confidence-building layout.</p>
              <a href="booking.php">Book Session</a>
            </article>
          </div>
          <div class="col-md-6 col-xl-4">
            <article class="feature-card reveal-card">
              <span class="feature-icon">03</span>
              <h3>Admin Experience</h3>
              <p>Manage store orders, appointment requests, and internal admin users with simulated filters, actions, and status updates.</p>
              <a href="admin-login.php">Open Admin</a>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section class="ritual-section">
      <div class="container">
        <div class="row g-4 align-items-center">
          <div class="col-lg-5">
            <div class="stone-panel">
              <span class="eyebrow">Why VastuAura</span>
              <h2>A restrained visual language rooted in warmth and trust.</h2>
              <p>The interface uses earthy neutrals, gold accents, layered cards, and subtle motion to mirror the calm precision expected from a Vastu consultant brand.</p>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="row g-4">
              <div class="col-md-6">
                <div class="detail-card">
                  <h3>Responsive First</h3>
                  <p>Every section adapts from stacked mobile cards to spacious desktop layouts without losing hierarchy.</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="detail-card">
                  <h3>Conversion-Led</h3>
                  <p>Primary actions stay prominent across store, booking, and admin journeys to keep users moving forward.</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="detail-card">
                  <h3>Modular Files</h3>
                  <p>Each page keeps its own CSS and JS so future enhancements remain isolated and easy to maintain.</p>
                </div>
              </div>
              <div class="col-md-6">
                <div class="detail-card">
                  <h3>Interactive UI</h3>
                  <p>Local storage, form simulations, animated reveals, and responsive components bring the frontend-only prototype to life.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="cta-banner">
          <div>
            <span class="eyebrow">Start Your Flow</span>
            <h2>Guide a visitor from discovery to purchase to consultation in one cohesive experience.</h2>
          </div>
          <div class="cta-actions">
            <a class="btn btn-brand" href="register.php">Create Account</a>
            <a class="btn btn-outline-brand" href="contact.php">Talk To Team</a>
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
  <script src="assets/js/index.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>
