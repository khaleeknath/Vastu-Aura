<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Bespoke Authentication Styles -->
  <link rel="stylesheet" href="./assets/css/register.css">
  <link rel="stylesheet" href="./assets/css/common-modal.css">
</head>
<body class="luxury-auth-page">

  <div class="auth-gateway">
    <!-- Ambient Background Elements -->
    <div class="auth-bg-elements">
      <div class="ambient-glow glow-1"></div>
      <div class="ambient-glow glow-2"></div>
    </div>

    <!-- Luxury Auth Card -->
    <div class="luxury-auth-card-wide reveal-up">
      <div class="auth-card-inner bg-dark-800">
        
        <!-- Brand / Header -->
        <div class="text-center mb-5">
          <a href="index.php" class="text-decoration-none">
            <h1 class="cinzel-heading text-white mb-1 fs-3">VASTU SHAKTI <span class="text-gold">RAHASYA</span></h1>
          </a>
          <p class="font-montserrat text-white-50 small tracking-wide text-uppercase mt-2">Create Your Profile</p>
        </div>

        <!-- Alerts -->
        <?php if (isset($_SESSION['register_error'])): ?>
            <div class="auth-alerts" id="registerMsg">
                <div class="luxury-alert alert-danger text-center">
                    <?= htmlspecialchars($_SESSION['register_error']); ?>
                </div>
            </div>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById("registerMsg");
                    if (msg) {
                        msg.style.opacity = "0";
                        setTimeout(() => msg.remove(), 500);
                    }
                }, 5000);
            </script>
            <?php unset($_SESSION['register_error']); ?>
        <?php endif; ?>

        <!-- Registration Form -->
        <form id="registerForm" class="row g-4 font-montserrat" method="POST" action="assets/api/register.php">
          
          <div class="col-md-6">
            <label class="form-label text-white-50 small text-uppercase tracking-wide">First Name</label>
            <input id="registerFirstName" name="FirstName" maxlength="30" type="text" class="form-control luxury-input" required>
          </div>
          
          <div class="col-md-6">
            <label class="form-label text-white-50 small text-uppercase tracking-wide">Last Name</label>
            <input id="registerLastName" name="LastName"  maxlength="30" type="text" class="form-control luxury-input" required>
          </div>
          
          <div class="col-12">
            <label class="form-label text-white-50 small text-uppercase tracking-wide">Email</label>
            <input id="registerEmail" name="email" type="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}" class="form-control luxury-input" required>
          </div>
          
          <div class="col-12">
            <label class="form-label text-white-50 small text-uppercase tracking-wide">Mobile Number</label>
            <input id="registerPhone" name="mobileNumber" type="tel" class="form-control luxury-input" placeholder="Enter 10-digit number"     oninput="this.value = this.value.replace(/[^0-9]/g, '')"   maxlength="10" pattern="[6-9][0-9]{9}" required>
          </div>

          <div class="col-12">
              <label class="form-label text-white-50 small text-uppercase tracking-wide">Password</label>
              <div class="input-group luxury-input-group">
                <input type="password" id="password" name="password" class="form-control luxury-input" required>
                <span class="input-group-text" id="togglePassword">
                  <i class="bi bi-eye-fill text-white-50"></i>
                </span>
              </div>
          </div>

          <div class="col-12 mt-5">
            <button class="btn-luxury-solid w-100" type="submit">Create Account</button>
          </div>

          <!-- Auth Footer Links -->
          <div class="col-12 text-center mt-4">
              <p class="text-white-50 small mb-2">Already have an account? <a href="login.php" class="text-gold text-decoration-none hover-opacity-100 transition-smooth">Sign In</a></p>
              <p class="text-white-50 small mb-0"><a href="index.php" class="text-white-50 text-decoration-none hover-opacity-100 transition-smooth">&larr; Return to Atelier</a></p>
          </div>
          
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/register.js"></script>
</body>
</html>