<?php
session_start();

if (isset($_SESSION['register_error'])) {
?>
    <div class="alert alert-danger text-center" id="registerMsg">
        <?= htmlspecialchars($_SESSION['register_error']); ?>
    </div>

    <script>
        setTimeout(() => {
            const msg = document.getElementById("registerMsg");
            if (msg) {
                msg.style.transition = "opacity 0.5s";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 500);
            }
        }, 5000);
    </script>
<?php
    unset($_SESSION['register_error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
  <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>
  <main class="auth-shell">
    <div class="auth-panel">
      <div class="auth-brand">
        <a class="logo-mark" href="index.php">VastuAura</a>
        <span class="eyebrow">Create Account</span>
        <h1>Join a smoother shopping and booking experience.</h1>
        <p>Create a client profile to access quicker checkout, prefilled consultation requests, and a personalized navbar state across the site.</p>
        <div class="auth-links">
          <a href="login.php">Already have an account?</a>
          <a href="booking.php">Go to Booking</a>
        </div>
      </div>
      <div class="auth-card">
        <h2>Register</h2>
        <form id="registerForm" class="row g-3" method="POST" action="assets/api/register.php">
          <div class="col-md-6">
            <label class="form-label">First Name</label>
            <input id="registerFirstName" name="FirstName" type="text" class="form-control" placeholder="FirstName" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Name</label>
            <input id="registerLastName" name="LastName" type="text" class="form-control" placeholder="LastName" required>
          </div>
          <div class="col-12">
            <label class="form-label">Email</label>
            <input id="registerEmail" name="email" type="email" class="form-control" placeholder="Email" required>
          </div>
          <div class="col-12">
            <label class="form-label">Mobile Number</label>
            <input id="registerPhone" name="mobileNumber" type="tel" class="form-control" placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[6-9][0-9]{9}" required>
          </div>



          <div class="col-12">
              <label class="form-label">Password</label>

              <div class="input-group">
                <input type="password" id="password"  name="password"  class="form-control" placeholder="Password">
                  <span class="input-group-text" id="togglePassword">
                    <i class="bi bi-eye-fill"></i>
                  </span>
              </div>
            </div>
          <div class="col-12">
            <button class="btn btn-brand w-100" type="submit">Create Account</button>
          </div>
        </form>
        <div id="registerAlert" class="alert alert-success mt-4 d-none"></div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/register.js"></script>
</body>
</html>
