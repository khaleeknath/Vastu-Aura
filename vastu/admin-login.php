<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/admin-login.css">
</head>
<body>
  <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>
  <main class="admin-auth-shell">
    <div class="admin-auth-card">
      <a class="logo-mark" href="index.php">VastuAura</a>
      <span class="eyebrow">Admin Access</span>
      <h1>Sign in to manage orders, appointments, and admin accounts.</h1>
      <form id="adminLoginForm" class="row g-3" action="assets/api/login.php" method="POST">
        <div class="col-12">
          <label class="form-label">Admin Mobile</label>
          <input id="phone" type="text" name="phone" placeholder="Enter 10-digit mobile number" maxlength="10" pattern="[6-9][0-9]{9}" class="form-control" value="" required>
        </div>
        <!-- <div class="col-12">
          <label class="form-label">Email</label>
          <input id="adminEmail" type="email" class="form-control" value="admin@vastuaura.com" required>
        </div> -->
        <div class="col-12">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" value="" required>
        </div>
        <div class="col-12">
          <button class="btn btn-brand w-100" type="submit">Login To Admin Panel</button>
        </div>
      </form>
      <div id="adminLoginAlert" class="alert alert-success mt-4 d-none"></div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/admin-login.js"></script>
</body>
</html>
