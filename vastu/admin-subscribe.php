<?php
// admin-subscribers.php
// Guards the page with the same admin session check used across the admin panel
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: admin-login.php');
    exit;
}

$adminName = $_SESSION['name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Subscribers | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Reuses the same admin table/filter styling as Orders & Enquiries -->
  <link rel="stylesheet" href="assets/css/admin-orders.css">
  <?php include 'common-modal.php'; ?>

</head>
<body>
  <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>
  <div class="admin-layout">

  <?php include __DIR__ . '/admin-sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
        <div class="hello-box">Hello <span id="adminGreeting"><?php echo htmlspecialchars($adminName, ENT_QUOTES, 'UTF-8'); ?></span></div>
      </header>

      <main class="admin-content">
        <section class="page-head">
          <div>
            <span class="eyebrow">Newsletter</span>
            <h1>Manage store & newsletter subscribers.</h1>
          </div>
        </section>

        <section class="filter-card">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select id="subStatusFilter" class="form-select">
                <option value="">All Statuses</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">From</label>
              <input id="subDateFrom" type="date" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">To</label>
              <input id="subDateTo" type="date" class="form-control">
            </div>
            <div class="col-md-5">
              <label class="form-label">Search</label>
              <input id="subSearch" type="search" class="form-control" placeholder="Search name or email">
            </div>
          </div>
          <div class="row g-3 mt-1">
            <div class="col-md-2">
              <label class="form-label">Rows</label>
              <select id="subPageSize" class="form-select">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
            <div class="col-md-10 d-flex align-items-end justify-content-end gap-2">
              <button id="subFilterClear" class="btn btn-outline-brand" type="button">Clear Filters</button>
            </div>
          </div>
        </section>

        <section class="table-card">
          <div id="subMessage" class="message-box d-none"></div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Subscribed On</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="subscribersTableBody"></tbody>
            </table>
          </div>

          <div class="pagination-bar d-flex justify-content-between align-items-center mt-3">
            <div id="subResultInfo" class="result-info"></div>
            <nav aria-label="Subscribers pagination">
              <ul id="subPagination" class="pagination mb-0"></ul>
            </nav>
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
  <script src="assets/js/admin-subscribe.js"></script>
  <script src="assets/js/common-modal.js"></script>

</body>
</html>