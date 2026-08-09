<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Management | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/admin-admins.css">
</head>
<body>
  <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>
  <div class="admin-layout">
    <aside class="admin-sidebar">
      <a class="logo-mark" href="index.php">VastuAura</a>
      <nav>
        <a href="admin-orders.html">Orders</a>
        <a href="admin-appointments.php">Appointments</a>
        <a class="active" href="admin-admins.html">Admins</a>
        <a href="#" id="sidebarLogout">Logout</a>
      </nav>
    </aside>

    <div class="admin-main">
      <header class="admin-topbar">
        <div class="hello-box">Hello <span id="adminGreeting">Admin Anaya</span></div>
        <div class="topbar-actions">
          <a class="logo-mark small" href="index.php">VastuAura</a>
          <button id="topbarLogout" class="btn btn-outline-brand" type="button">Logout</button>
        </div>
      </header>

      <main class="admin-content">
        <section class="page-head">
          <div>
            <span class="eyebrow">Admins Management</span>
            <h1>Manage admin access for the agency team.</h1>
          </div>
          <button id="toggleAdminForm" class="btn btn-brand" type="button">Add Admin</button>
        </section>

        <section id="addAdminPanel" class="form-card d-none">
          <form id="addAdminForm" class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Name</label>
              <input id="newAdminName" type="text" class="form-control" placeholder="Admin name" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Email</label>
              <input id="newAdminEmail" type="email" class="form-control" placeholder="admin@example.com" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Role</label>
              <select id="newAdminRole" class="form-select">
                <option>Operations Admin</option>
                <option>Store Admin</option>
                <option>Support Admin</option>
              </select>
            </div>
            <div class="col-12">
              <button class="btn btn-brand" type="submit">Save Admin</button>
            </div>
          </form>
        </section>

        <section class="table-card">
          <div id="adminMessage" class="message-box d-none"></div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="adminsTableBody"></tbody>
            </table>
          </div>
        </section>
      </main>

      <footer class="admin-footer">
        © 2026 Developed &amp; Maintained by Vyomark Digital Solutions
      </footer>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/admin-admins.js"></script>
</body>
</html>
