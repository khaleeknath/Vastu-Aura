<?php
session_start();

if (isset($_SESSION['error'])) {
?>
    <div id="loginMsg" class="alert alert-danger text-center">
        <?= $_SESSION['error']; ?>
    </div>

    <script>
        // hide after 5 seconds
        setTimeout(() => {
            const msg = document.getElementById("loginMsg");
            if (msg) {
                msg.style.transition = "0.5s";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 500);
            }
        }, 5000);
    </script>
<?php
    unset($_SESSION['error']);
}

if (isset($_SESSION['login_error'])) {
  ?>
      <div id="login_error" class="alert alert-danger text-center">
          <?= htmlspecialchars($_SESSION['login_error']); ?>
      </div>
      <script>
        // hide after 5 seconds
        setTimeout(() => {
            const msg = document.getElementById("login_error");
            if (msg) {
                msg.style.transition = "0.5s";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 500);
            }
        }, 5000);
    </script>
  <?php
      unset($_SESSION['login_error']);
  }



  if (isset($_SESSION['success'])) {
    ?>
        <div id="success" class="alert alert-success text-center">
            <?= htmlspecialchars($_SESSION['success']); ?>
        </div>
        <script>
          // hide after 5 seconds
          setTimeout(() => {
              const msg = document.getElementById("success");
              if (msg) {
                  msg.style.transition = "0.5s";
                  msg.style.opacity = "0";
                  setTimeout(() => msg.remove(), 500);
              }
          }, 5000);
      </script>
    <?php
        unset($_SESSION['success']);
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
  <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>
  <main class="auth-shell">
    <div class="auth-panel">
      <div class="auth-brand">
        <a class="logo-mark" href="index.php">VastuAura</a>
        <span class="eyebrow">Client Login</span>
        <h1>Welcome back to your aligned dashboard.</h1>
        <p>Log in to continue shopping, book consultations faster, and track order or appointment activity.</p>
        <div class="auth-links">
          <a href="register.php">Create Account</a>
          <a href="forgot-password.html">Forgot Password</a>
        </div>
      </div>
      <div class="auth-card">
        <h2>Login</h2>
        <form id="loginForm" class="row g-3" action="assets/api/login.php" method="POST">
          <!-- <div class="col-12">
            <label class="form-label">Email</label>
            <input id="loginEmail" type="email" class="form-control" value="aarav@example.com" required>
          </div> -->

          <div class="col-12">
            <label class="form-label">Phone</label>
            <input id="loginPhone" type="tel" name="phone" class="form-control"  placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[6-9][0-9]{9}"  required>
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
          <!-- <div class="col-12">
            <label class="form-label">First Name</label>
            <input id="loginName" type="text" class="form-control" value="Aarav" required>
          </div> -->
          
          <div class="col-12">
            <button class="btn btn-brand w-100" type="submit">Login</button>
          </div>
        </form>
        <div id="loginAlert" class="alert alert-success mt-4 d-none"></div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/login.js"></script>
</body>
</html>
