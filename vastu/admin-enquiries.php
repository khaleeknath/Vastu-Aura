<?php
// admin-enquiries.php
// Guards the page with the same admin session check used by admin-orders.php
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
  <title>Admin Enquiries | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Reuses the same admin table/filter styling as Orders -->
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
            <span class="eyebrow">Concierge & Support</span>
            <h1>Review and action customer enquiries.</h1>
          </div>
        </section>

        <section class="filter-card">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select id="enquiryStatusFilter" class="form-select">
                <option value="">All Statuses</option>
                <option value="new">New</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Enquiry Type</label>
              <select id="enquiryTypeFilter" class="form-select">
                <option value="">All Types</option>
                <option value="Consultation Details">Consultation Details</option>
                <option value="Sacred Store & Products">Sacred Store & Products</option>
                <option value="Commercial Partnership">Commercial Partnership</option>
                <option value="General Support">General Support</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">From</label>
              <input id="enquiryDateFrom" type="date" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">To</label>
              <input id="enquiryDateTo" type="date" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Search</label>
              <input id="enquirySearch" type="search" class="form-control" placeholder="Name, email or mobile">
            </div>
          </div>
          <div class="row g-3 mt-1">
            <div class="col-md-2">
              <label class="form-label">Rows</label>
              <select id="enquiryPageSize" class="form-select">
                <option value="10">10</option>
                <option value="25" selected>25</option>
                <option value="50">50</option>
                <option value="100">100</option>
              </select>
            </div>
            <div class="col-md-10 d-flex align-items-end justify-content-end gap-2">
              <button id="enquiryFilterClear" class="btn btn-outline-brand" type="button">Clear Filters</button>
            </div>
          </div>
        </section>

        <section class="table-card">
          <div id="enquiryMessage" class="message-box d-none"></div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Contact</th>
                  <th>Type</th>
                  <th>Message</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="enquiriesTableBody"></tbody>
            </table>
          </div>

          <div class="pagination-bar d-flex justify-content-between align-items-center mt-3">
            <div id="enquiriesResultInfo" class="result-info"></div>
            <nav aria-label="Enquiries pagination">
              <ul id="enquiriesPagination" class="pagination mb-0"></ul>
            </nav>
          </div>
        </section>
      </main>

      <footer class="admin-footer">
        © 2026 Developed &amp; Maintained by Vyomark Digital Solutions
      </footer>
    </div>
  </div>

  <!-- View Message Modal -->
  <div class="modal fade" id="enquiryViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Enquiry Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="enquiryViewBody">
          <!-- Populated by admin-enquiries.js -->
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/admin-enquiries.js"></script>
  <script src="assets/js/common-modal.js"></script>

</body>
</html>