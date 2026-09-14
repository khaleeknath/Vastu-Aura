<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>The Atelier Login | Vastu Shakti Rahasya</title>
  
  <!-- Premium Typography -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700;800&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Core Framework -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Bespoke Styles -->
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="luxury-auth-page bg-muted">

  <main class="auth-gateway">
    <!-- Ambient Background Elements -->
    <div class="auth-bg-elements">
      <div class="ambient-glow glow-1"></div>
      <div class="ambient-glow glow-2"></div>
    </div>

    <div class="container h-100 d-flex align-items-center justify-content-center position-relative z-index-2">
      <div class="luxury-auth-card reveal-up active">
        
        <!-- Alerts Handling (Preserved PHP Backend Logic) -->
        <div class="auth-alerts">
            <?php if (isset($_SESSION['error'])): ?>
                <div id="loginMsg" class="luxury-alert alert-danger text-center">
                    <span class="me-2">✧</span> <?= $_SESSION['error']; ?>
                </div>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById("loginMsg");
                        if (msg) {
                            msg.style.opacity = "0";
                            setTimeout(() => msg.remove(), 500);
                        }
                    }, 5000);
                </script>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div id="login_error" class="luxury-alert alert-danger text-center">
                    <span class="me-2">✧</span> <?= htmlspecialchars($_SESSION['login_error']); ?>
                </div>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById("login_error");
                        if (msg) {
                            msg.style.opacity = "0";
                            setTimeout(() => msg.remove(), 500);
                        }
                    }, 5000);
                </script>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div id="success" class="luxury-alert alert-success text-center">
                    <span class="me-2">✧</span> <?= htmlspecialchars($_SESSION['success']); ?>
                </div>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById("success");
                        if (msg) {
                            msg.style.opacity = "0";
                            setTimeout(() => msg.remove(), 500);
                        }
                    }, 5000);
                </script>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
        </div>

        <div class="auth-card-inner bg-white">
          <!-- Header / Branding -->
          <div class="auth-header text-center border-bottom pb-4 mb-4">
            <a href="index.php" class="auth-brand-logo mb-3 d-inline-block">
                <img src="assets/images/logo.jpeg" alt="Vastu Shakti Rahasya" class="auth-logo-img">
            </a>
            <h1 class="cinzel-heading fs-3 text-dark-900 mb-2">Welcome to The Atelier</h1>
            <p class="font-montserrat text-muted small tracking-wide text-uppercase mb-0">Secure Client Access</p>
          </div>

          <!-- Form (Preserved Backend Endpoint & Field Names) -->
          <form id="loginForm" class="row g-4 font-montserrat" action="assets/api/login.php" method="POST">
            
            <div class="col-12">
              <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900">Mobile Number</label>
              <input id="loginPhone" type="tel" name="phone" class="form-control luxury-input" placeholder="Enter 10-digit mobile number"     oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="10" pattern="[6-9][0-9]{9}" required>
            </div>
            
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <label class="form-label text-uppercase tracking-wide small fw-bold text-dark-900 mb-0">Password</label>
                <!-- <a href="forgot-password.html" class="text-gold text-decoration-none small fw-medium">Forgot?</a> -->
              </div>
              <div class="input-group luxury-input-group">
                <input type="password" id="password" name="password" class="form-control luxury-input border-end-0" placeholder="Enter password" required>
                <span class="input-group-text bg-muted border-start-0" id="togglePassword">
                  <i class="bi bi-eye-fill text-muted"></i>
                </span>
              </div>
            </div>
            
            <div class="col-12 mt-5">
              <button class="btn-luxury-solid w-100" type="submit">Sign In to Dashboard</button>
            </div>

          </form>
        </div>

        <!-- Footer Links -->
        <div class="auth-footer text-center mt-4">
          <p class="font-montserrat text-white-50 small mb-0">
            Do not have an account yet? 
            <a href="register.php" class="text-gold text-decoration-none fw-bold ms-1">Create an Account</a>
          </p>
          <a href="index.php" class="font-montserrat text-white-50 small text-decoration-none mt-3 d-inline-block opacity-75 hover-opacity-100 transition-smooth">
            ← Return to Homepage
          </a>
        </div>

      </div>
    </div>
  </main>

  <script src="assets/js/login.js"></script>
</body>
</html>