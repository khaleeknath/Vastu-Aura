<?php
session_start();
$current_page = basename($_SERVER['PHP_SELF']);

// Preserved Authentication Logic
if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please login First to Access Page.";
    header("Location: login.php");
    exit;
}

// Preserved Prefill Logic
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
  <title>Book a Consultation | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  
  <!-- Bespoke E-Commerce Styles -->
  <link rel="stylesheet" href="assets/css/booking.css">
  <link rel="stylesheet" href="assets/css/common-modal.css">
  <?php include 'common-modal.php'; ?>

  <!-- Preserved Map & Location Styles, adapted slightly for luxury theme -->
  <style>
    .location-card {
      background: #fbf9f6;
      border: 1px solid #e2ddd3;
      border-radius: 4px;
      padding: 1.5rem;
      margin-top: 1.5rem;
    }
    .location-controls {
      display: flex;
      flex-wrap: wrap;
      gap: .5rem;
      margin-bottom: 1rem;
      position: relative;
    }
    .location-controls input[type="text"] {
      flex: 1 1 220px;
    }
    #locationSuggestions {
      position: absolute;
      top: 100%;
      left: 0;
      width: 100%;
      max-height: 260px;
      overflow-y: auto;
      background: #fff;
      border: 1px solid #e2ddd3;
      box-shadow: 0 8px 20px rgba(0,0,0,0.12);
      z-index: 1000;
      margin-top: 4px !important;
    }
    #locationSuggestions .list-group-item {
      cursor: pointer;
      border: none;
      border-bottom: 1px solid #f0ece4;
      text-align: left;
    }
    #locationSuggestions .list-group-item:hover { background: #f5f1ea; }
    #vastuMap {
      width: 100%;
      height: 340px;
      border-radius: 4px;
      margin-bottom: 1rem;
      z-index: 0;
      border: 1px solid #e2ddd3;
    }
    .price-summary {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }
    .price-summary .pill {
      background: #fff;
      border: 1px solid #e2ddd3;
      border-radius: 4px;
      padding: .75rem 1rem;
      flex: 1 1 160px;
      text-align: center;
    }
    .price-summary .pill .label {
      display: block;
      font-size: .8rem;
      color: #7a7368;
      margin-bottom: .25rem;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .price-summary .pill .value {
      font-size: 1.1rem;
      font-weight: 700;
      color: #2c2620;
    }
    .shop-marker { font-size: 24px; text-align: center; line-height: 30px; }
  </style>
</head>
<body class="luxury-ecomm bg-muted">

  <!-- Progress Bar for Scroll -->
  <div class="scroll-progress-bar" id="scrollProgress"></div>

  <!-- Redesigned High-End Navbar -->
  <nav class="navbar navbar-expand-lg fixed-top bespoke-navbar" id="siteNavbar">
    <div class="container-fluid px-4 px-xl-5">
      <a class="navbar-brand brand-logo" href="index.php">
        <div class="logo-wrapper">
            <img src="assets/images/logo.jpeg" alt="Vastu Shakti Rahasya" class="logo-img">
            <div class="logo-text d-none d-xl-flex flex-column justify-content-center">
                <span class="logo-title">VASTU SHAKTI</span>
                <span class="logo-subtitle">R A H A S Y A</span>
            </div>
        </div>
      </a>
      <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#siteNav" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="siteNav">
        <ul class="navbar-nav mx-auto nav-links-premium">
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'index.php') ? 'active' : '' ?>" href="index.php">The Atelier</a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= ($current_page == 'about.php') ? 'active' : '' ?>" href="about.php">S. Ramesh</a>
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
        
        <div class="nav-actions d-flex align-items-center gap-3 mt-3 mt-lg-0">
            <a href="cart.php" class="cart-icon-btn" aria-label="Shopping Cart">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </a>
          <?php if (isset($_SESSION['user_id'])): ?>
            <div class="nav-item dropdown">
              <a class="nav-link dropdown-toggle user-trigger" href="#" data-bs-toggle="dropdown">
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
            <a class="btn-auth-premium" href="login.php">Sign In</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

  <main>
    <!-- Booking Editorial Hero -->
    <section class="booking-editorial-hero position-relative section-padding pb-0">
      <div class="container mt-5 pt-4 text-center z-index-2 reveal-up">
        <span class="text-gold tracking-wide text-uppercase small font-montserrat fw-bold mb-3 d-block">Reserve Your Session</span>
        <h1 class="cinzel-heading display-2 mb-4 text-dark-900">Schedule an Alignment</h1>
        <div class="ornate-divider mx-auto mb-5"></div>
      </div>
    </section>

    <!-- Main Booking Interface -->
    <section class="booking-interface section-padding pt-4">
      <div class="container">
        <div class="row g-5">
          
          <!-- Left Column: The Journey & Information -->
          <div class="col-lg-5 pe-lg-5 reveal-up stagger-1">
            <h2 class="cinzel-heading display-6 mb-4">The Consultation Journey</h2>
            <p class="font-montserrat text-muted-large mb-5">
              Secure your private session with our experts. Whether assessing a commercial property or decoding your natal chart, we ensure a seamless and profoundly transformative experience.
            </p>
            
            <div class="journey-steps">
              <div class="journey-step mb-4 d-flex">
                <div class="step-numeral text-gold font-cinzel me-4 opacity-50">I</div>
                <div>
                  <h4 class="cinzel-heading text-dark-900 fs-5 mb-2">Submit Your Request</h4>
                  <p class="font-montserrat text-muted small mb-0">Provide your prefilled details and select a preferred window. Your information is held in strict confidence.</p>
                </div>
              </div>
              <div class="journey-step mb-4 d-flex">
                <div class="step-numeral text-gold font-cinzel me-4 opacity-50">II</div>
                <div>
                  <h4 class="cinzel-heading text-dark-900 fs-5 mb-2">Location & Pricing</h4>
                  <p class="font-montserrat text-muted small mb-0">Map your property to calculate accurate distance-based pricing and reserve your spot via secure payment.</p>
                </div>
              </div>
              <div class="journey-step d-flex">
                <div class="step-numeral text-gold font-cinzel me-4 opacity-50">III</div>
                <div>
                  <h4 class="cinzel-heading text-dark-900 fs-5 mb-2">Final Confirmation</h4>
                  <p class="font-montserrat text-muted small mb-0">You will receive an official itinerary and preparation guidelines prior to your session.</p>
                </div>
              </div>
            </div>
            
            <div class="help-box mt-5 p-4 bg-white border border-light">
                <h5 class="cinzel-heading text-dark-900 mb-2">Require Assistance?</h5>
                <p class="font-montserrat text-muted small mb-0">If you are unsure which consultation type suits your current situation, please <a href="contact.php" class="text-gold text-decoration-underline">contact our concierge</a>.</p>
            </div>
          </div>

          <!-- Right Column: The Premium Form -->
          <div class="col-lg-7 reveal-up stagger-2">
            <div class="luxury-form-card bg-white p-4 p-md-5">
              <h3 class="cinzel-heading text-dark-900 mb-4 pb-2 border-bottom">Reservation Details</h3>
              
              <form id="bookingForm" method="POST" class="row g-4 font-montserrat">
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Full Name</label>
                  <input type="text" class="form-control luxury-input" name="name" value="<?= htmlspecialchars($fullName) ?>" required>               
                </div>
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Email Address</label>
                  <input id="bookingEmail" name="email" type="email" class="form-control luxury-input" value="<?= htmlspecialchars($email) ?>" data-original="<?= htmlspecialchars($email) ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Mobile Number</label>
                  <input id="bookingPhone" name="mobile" type="tel" class="form-control luxury-input" value="<?= htmlspecialchars($phone) ?>" required>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Consultation Type</label>
                  <select class="form-select luxury-input" name="consultation_type" required>
                    <option value="" disabled selected>Select a service...</option>
                    <option>Home Consultation</option>
                    <option>Office Consultation</option>
                    <option>Retail Review</option>
                    <option>Virtual Assessment</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Preferred Date</label>
                  <input id="bookingDate" name="preferred_date" type="date" class="form-control luxury-input" required>
                </div>
                
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Preferred Time</label>
                  <!-- Preserved Backend Logic: Dynamic Select -->
                  <select id="preferred_time" class="form-select luxury-input" name="preferred_time" required>
                    <option disabled value="">-- Select Preferred Time --</option>
                  </select>
                  <small id="noSlotsMsg" class="text-danger d-none">No slots left for this date. Please select a different date.</small>
                </div>

                <!-- Preserved Backend Logic: Property details driving pricing -->
                <div class="col-md-6">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Property Type</label>
                  <select class="form-select luxury-input" name="property_type" id="propertyType">
                    <option value="Flat">Flat / Residential Apartment</option>
                    <option value="Commercial">Commercial / Office / Shop</option>
                  </select>
                </div>
                <div class="col-md-6" id="bhkField">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">BHK Type</label>
                  <select class="form-select luxury-input" name="bhk_type" id="bhkType" required>
                    <option value="1bhk">1 BHK</option>
                    <option value="2bhk">2 BHK</option>
                    <option value="3bhk">3 BHK</option>
                  </select>
                </div>
                <div class="col-md-6 d-none" id="sqftField">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Built-up Area (sq.ft)</label>
                  <input type="number" class="form-control luxury-input" name="sqft" id="sqftInput" min="1" placeholder="e.g. 1200">
                </div>
                
                <div class="col-12">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Flat / House No., Wing, Floor</label>
                  <input type="text" class="form-control luxury-input" name="unit_number" id="unitNumber" placeholder="e.g. Flat 402, B Wing, 4th Floor">
                </div>
                
                <div class="col-12">
                  <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Full Address</label>
                  <textarea id="bookingAddress" name="address" class="form-control luxury-input" rows="3" placeholder="Enter the complete address for the session..." required></textarea>
                </div>

                <!-- Preserved Backend Logic: Location & Map block -->
                <div class="col-12">
                  <div class="location-card">
                    <h4 class="cinzel-heading fs-5 mb-3">Set Property Location</h4>
                    <p class="font-montserrat small text-muted mb-3">
                      Search for your building or society to calculate your accurate distance-based pricing. (Drag the pin on the map if needed).
                    </p>

                    <div class="location-controls">
                      <button type="button" id="useMyLocationBtn" class="btn btn-outline-secondary font-montserrat small">📍 Use My Current Location</button>
                      <input type="text" id="locationSearchInput" class="form-control luxury-input" placeholder="Search an address...">
                      <button type="button" id="locationSearchBtn" class="btn-luxury-outline text-dark border-1 px-3 py-1">Search</button>
                    </div>

                    <div id="vastuMap"></div>

                    <div class="price-summary mb-3">
                      <div class="pill">
                        <span class="label font-montserrat">Distance</span>
                        <span class="value font-cinzel" id="distanceValue">--</span>
                      </div>
                      <div class="pill border-gold">
                        <span class="label font-montserrat text-gold">Total Charge</span>
                        <span class="value font-cinzel" id="amountValue">--</span>
                      </div>
                    </div>
                    
                    <div class="alert alert-warning font-montserrat small py-2 mb-0">
                      <strong>💳 Booking Payment Required:</strong> To confirm, you must pay <strong>50% of the total charge</strong> as an advance payment on the next screen.
                    </div>

                    <!-- Hidden fields submitted with the booking -->
                    <input type="hidden" name="client_lat" id="clientLat">
                    <input type="hidden" name="client_lng" id="clientLng">
                    <input type="hidden" name="distance_km" id="distanceKm">
                    <input type="hidden" name="estimated_amount" id="estimatedAmount">
                  </div>
                </div>
                
                <div class="col-12 mt-4">
                  <button class="btn-luxury-solid w-100" type="button" id="openConfirmModal">Review & Submit Request</button>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </section>
  </main>

  <!-- Premium Booking Confirmation Modal -->
  <div class="modal fade luxury-modal" id="confirmBookingModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content bg-dark-900 border-gold rounded-0">
              <div class="modal-header border-0 py-4 px-4 px-md-5 d-flex justify-content-between align-items-center">
                  <h4 class="modal-title cinzel-heading text-gold mb-0">Verify Reservation</h4>
                  <button type="button" class="btn-close btn-close-white opacity-50" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body p-4 p-md-5 pt-0">
                  <p class="font-montserrat text-white-75 mb-4 border-bottom border-light pb-4">Please ensure all details are accurate before proceeding to payment.</p>
                  
                  <div class="table-responsive">
                    <table class="table table-borderless luxury-confirm-table mb-0 font-montserrat text-white">
                        <tbody>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide" style="width: 40%;">Client Name</th>
                                <td id="confirmName" class="text-end"></td>
                            </tr>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Contact Email</th>
                                <td id="confirmEmail" class="text-end"></td>
                            </tr>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Mobile Number</th>
                                <td id="confirmMobile" class="text-end"></td>
                            </tr>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Scheduled Date</th>
                                <td id="confirmDate" class="text-end text-gold"></td>
                            </tr>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Scheduled Time</th>
                                <td id="confirmTime" class="text-end text-gold"></td>
                            </tr>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Service Required</th>
                                <td id="confirmType" class="text-end"></td>
                            </tr>
                            <!-- Fields preserved from backend modal -->
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Property Details</th>
                                <td id="confirmPropertyDetails" class="text-end"></td>
                            </tr>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Unit / Flat</th>
                                <td id="confirmUnit" class="text-end"></td>
                            </tr>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Address</th>
                                <td id="confirmAddress" class="text-end"></td>
                            </tr>
                            <tr>
                                <th class="text-white-50 text-uppercase small tracking-wide">Distance from Shop</th>
                                <td id="confirmDistance" class="text-end"></td>
                            </tr>
                            <tr>
                                <th class="text-gold text-uppercase small tracking-wide">Advance Due (50%)</th>
                                <td id="confirmAmount" class="text-end fs-5 fw-bold text-gold"></td>
                            </tr>
                        </tbody>
                    </table>
                  </div>

                  <div class="form-check mt-4 font-montserrat text-white-75">
                    <input type="checkbox" class="form-check-input bg-transparent border-light" id="agreeTerms">
                    <label class="form-check-label small" for="agreeTerms">
                      I agree to the <a href="terms.php" target="_blank" class="text-gold">Terms and Conditions</a>, including that the payment is <strong>non-refundable</strong>.
                    </label>
                  </div>
                  <small id="agreeError" class="text-danger d-none font-montserrat">Please accept the Terms and Conditions before confirming your booking.</small>
              </div>
              
              <div class="modal-footer border-top border-light py-4 px-4 px-md-5 bg-dark-800">
                  <button type="button" class="btn-luxury-outline text-white border-0" data-bs-dismiss="modal">Modify</button>
                  <button type="button" id="confirmSubmit" class="btn-luxury-solid" disabled>Pay & Confirm</button>
              </div>
          </div>
      </div>
  </div>

  <!-- Premium Success Modal -->
  <div class="modal fade luxury-modal" id="successModal" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content bg-white border-0 rounded-0 text-center p-5 shadow-lg">
              <div class="modal-body p-0">
                  <div class="success-icon text-gold mb-4">
                      <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
                      </svg>
                  </div>
                  <h3 class="cinzel-heading text-dark-900 mb-3">Reservation Successful</h3>
                  <p class="font-montserrat text-muted mb-4">
                      Thank you for choosing Vastu Shakti Rahasya.<br>
                      Our concierge will review your request and contact you shortly.
                  </p>
                  <button class="btn-luxury-outline text-dark w-100" data-bs-dismiss="modal">Close</button>
              </div>
          </div>
      </div>
  </div>

  <!-- Preserved Backend Logic: Email Change Confirmation Modal -->
  <div class="modal fade luxury-modal" id="emailChangeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-0">
        <div class="modal-header border-0 bg-muted">
          <h5 class="modal-title font-cinzel text-dark-900">Update Registered Email?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body font-montserrat">
          <p>You originally registered with <strong id="emailOld" class="text-gold"></strong>, but entered <strong id="emailNew" class="text-gold"></strong> for this booking.</p>
          <p class="mb-0">Do you want to replace your account's registered email with this new one?</p>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-outline-secondary font-montserrat" id="emailChangeCancel">No, keep old email</button>
          <button type="button" class="btn btn-dark font-montserrat" id="emailChangeConfirm">Yes, update it</button>
        </div>
      </div>
    </div>
  </div>

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
  <!-- Preserved Backend Scripts required for Map & Razorpay -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  
  <script src="assets/js/booking.js"></script>
  <script src="assets/js/common-modal.js"></script>
</body>
</html>