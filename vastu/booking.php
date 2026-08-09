<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please login First to Access Page.";
    header("Location: login.php");
    exit;
}

$name = $_SESSION['user']['first_name'] ?? '';
$lname = $_SESSION['user']['last_name'] ?? '';
$fullName = trim($name . ' ' . $lname);

$email = $_SESSION['email'] ?? '';
$phone = $_SESSION['phone'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Appointment | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/booking.css">
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
        <div class="row g-4 align-items-end">
          <div class="col-lg-7">
            <span class="eyebrow">Appointment Booking</span>
            <h1>Schedule a consultation with prefilled client details.</h1>
            <p>Use the client account state to streamline appointment creation while preserving a clean, calming booking experience.</p>
          </div>
          <div class="col-lg-5">
            <div class="info-card">
              <h3>Consultation Types</h3>
              <p>Home alignment, office layout, retail energy review, and room-specific advisory sessions.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="section-shell">
      <div class="container">
        <div class="row g-4">
          <div class="col-lg-7">
            <div class="booking-card">
              <h2>Request Appointment</h2>
              <form id="bookingForm" method="POST" >
                <div class="col-md-6">
                  <label class="form-label">Name</label>
                  <input  type="text" class="form-control" name="name" value="<?= htmlspecialchars($fullName) ?>" required>               
                 </div>
                <div class="col-md-6">
                  <label class="form-label">Email</label>
                  <input id="bookingEmail" name="email" type="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Mobile Number</label>
                  <input id="bookingPhone" name="mobile" type="tel" class="form-control"  value="<?= htmlspecialchars($phone) ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Preferred Time</label>
                  <input id="bookingTime" name="preferred_time" type="time" class="form-control" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Address</label>
                  <textarea id="bookingAddress" name="address" class="form-control" rows="4" placeholder="Enter property address" required></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Preferred Date</label>
                  <input id="bookingDate" name="preferred_date" type="date" class="form-control" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Consultation Type</label>
                  <select class="form-select" name="consultation_type">
                    <option>Home Consultation</option>
                    <option>Office Consultation</option>
                    <option>Retail Review</option>
                    <option>Virtual Assessment</option>
                  </select>
                </div>
                <button class="btn btn-brand" type="button" id="openConfirmModal">Submit Booking</button>
              </form>
              <div id="bookingAlert" class="alert alert-success mt-4 d-none"></div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="steps-card">
              <h2>What Happens Next</h2>
              <div class="step-item">
                <strong>1. Request Received</strong>
                <p>Your preferred date and time are recorded in the UI preview.</p>
              </div>
              <div class="step-item">
                <strong>2. Team Review</strong>
                <p>Admin panel users can approve, hold, or cancel requests visually.</p>
              </div>
              <div class="step-item">
                <strong>3. Confirmation</strong>
                <p>The client sees a clean confirmation message without backend dependency.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>


  <!-- Booking Confirmation Modal -->
<div class="modal fade" id="confirmBookingModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Confirm Appointment
                </h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p>Are you sure you want to book this appointment?</p>

                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <td id="confirmName"></td>
                    </tr>

                    <tr>
                        <th>Email</th>
                        <td id="confirmEmail"></td>
                    </tr>

                    <tr>
                        <th>Mobile</th>
                        <td id="confirmMobile"></td>
                    </tr>

                    <tr>
                        <th>Date</th>
                        <td id="confirmDate"></td>
                    </tr>

                    <tr>
                        <th>Time</th>
                        <td id="confirmTime"></td>
                    </tr>

                    <tr>
                        <th>Consultation</th>
                        <td id="confirmType"></td>
                    </tr>

                    <tr>
                        <th>Address</th>
                        <td id="confirmAddress"></td>
                    </tr>

                </table>

            </div>

            <div class="modal-footer">

                <button type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Edit
                </button>

                <button type="button"
                    id="confirmSubmit"
                    class="btn btn-success">
                    Confirm Booking
                </button>

            </div>

        </div>
    </div>
</div>

  <!-- Success BookingF Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">

            <div class="modal-body">

                <div style="font-size:70px;color:#28a745;">
                    ✅
                </div>

                <h3 class="mt-3">Booking Successful!</h3>

                <p class="text-muted">
                    Thank you for booking with <strong>VastuAura</strong>.
                    <br>
                    Our team will review your request and contact you shortly.
                </p>

                <button class="btn btn-success mt-3"
                        data-bs-dismiss="modal">
                    OK
                </button>

            </div>

        </div>
    </div>
</div>

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
  <script src="assets/js/booking.js"></script>
  <script src="assets/js/common-modal.js"></script>

</body>
</html>
