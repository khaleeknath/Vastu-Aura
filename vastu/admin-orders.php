<?php
// admin-orders.php
// Guards the page with an admin session check. Adjust the session key
// names below to match whatever your login script (admin-login.php) sets.
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
  <title>Admin Orders | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/admin-orders.css">
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
            <span class="eyebrow">Orders Management</span>
            <h1>Track and update storefront orders.</h1>
          </div>
        </section>

        <section class="filter-card">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select id="orderStatusFilter" class="form-select">
                <option value="">All Statuses</option>
                <option value="Pending">Pending</option>
                <option value="On Hold">On Hold</option>
                <option value="Dispatched">Dispatched</option>
                <option value="Cancelled">Cancelled</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">From</label>
              <input id="orderDateFrom" type="date" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">To</label>
              <input id="orderDateTo" type="date" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Product</label>
              <select id="orderProductFilter" class="form-select">
                <option value="">All Products</option>
                <!-- Populated dynamically from the backend, but static
                     fallbacks are fine to keep if you prefer them. -->
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Search</label>
              <input id="orderSearch" type="search" class="form-control" placeholder="Search customer or ID">
            </div>
          </div>
          <div class="row g-3 mt-1">
            <div class="col-md-2">
              <label class="form-label">Rows</label>
              <select id="orderPageSize" class="form-select">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
            <div class="col-md-10 d-flex align-items-end justify-content-end gap-2">
              <button id="orderFilterClear" class="btn btn-outline-brand" type="button">Clear Filters</button>
            </div>
          </div>
        </section>

        <section class="table-card">
          <div id="orderMessage" class="message-box d-none"></div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Order ID</th>
                  <th>Customer</th>
                  <th>Product</th>
                  <th>Amount</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="ordersTableBody"></tbody>
            </table>
          </div>

          <div class="pagination-bar d-flex justify-content-between align-items-center mt-3">
            <div id="ordersResultInfo" class="result-info"></div>
            <nav aria-label="Orders pagination">
              <ul id="ordersPagination" class="pagination mb-0"></ul>
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
  <script src="assets/js/admin-orders.js"></script>
</body>
</html>