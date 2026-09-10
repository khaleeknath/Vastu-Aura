<?php
session_start();
include(__DIR__ . '/assets/config/db-conn.php');

$status = $_GET['status'] ?? '';
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM tbl_appointment WHERE 1=1";
if (!empty($status)) {
  $query .= " AND status = '$status'";
}

if (!empty($from)) {
  $query .= " AND preferred_date >= '$from'";
}

if (!empty($to)) {
  $query .= " AND preferred_date <= '$to'";
}

if (!empty($search)) {
  $query .= " AND (name LIKE '%$search%' OR mobile LIKE '%$search%')";
}

$query .= " ORDER BY id DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Appointments | VastuAura</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/admin-appointments.css">
</head>
<body>
  <button id="backToTop" class="back-to-top" aria-label="Back to top">↑</button>
  <div class="admin-layout">
  <?php include __DIR__ . '/admin-sidebar.php'; ?>

    <div class="admin-main">
      <header class="admin-topbar">
      <div class="hello-box">
  Hello 
  <span id="adminGreeting">
    <?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '' ?>
  </span>
</div>
        
      </header>

      <main class="admin-content">
        <section class="page-head">
          <div>
            <span class="eyebrow">Appointment Requests</span>
            <h1>Review, approve, or hold incoming consultations.</h1>
          </div>
        </section>

        <section class="filter-card">
        <form method="GET" class="row g-3">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Status</label>
              <select id="appointmentStatusFilter" name="status" class="form-select">
                <option value="">All Statuses</option>
                           <option value="pending" >Pending</option>
            <option value="approved" >Approved</option>
            <option value="rejected" >Rejected</option>
            <option value="completed" >Completed</option>
            <option value="cancelled" >Cancelled</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">From</label>
              <input id="appointmentDateFrom" name="from" type="date" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">To</label>
              <input id="appointmentDateTo" type="date" name="to" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Search</label>
              <input id="appointmentSearch" type="search" name="search" class="form-control" placeholder="Search client or request">
            </div>
          </div>
          <div class="col-md-3 d-flex align-items-end">
    <button class="btn btn-primary w-100">Filter</button>
</div>
</form>
        </section>

        <section class="table-card">
          <div id="appointmentMessage" class="message-box d-none"></div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Client</th>
                  <th>Mobile</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Status</th>
                  <th>Action</th>
                  <!-- <th>Comment</th> -->
                </tr>
              </thead>
              <tbody>
<?php
$sr = 1;
while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?= $sr++ ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['mobile']) ?></td>
    <td><?= htmlspecialchars($row['preferred_date']) ?></td>
    <td><?= htmlspecialchars($row['preferred_time']) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>

<td>
    <button type="button" class="btn btn-sm btn-outline-brand view-appointment"
            data-id="<?= $row['id'] ?>"
            data-name="<?= htmlspecialchars($row['name']) ?>"
            data-mobile="<?= htmlspecialchars($row['mobile']) ?>"
            data-date="<?= htmlspecialchars($row['preferred_date']) ?>"
            data-time="<?= htmlspecialchars($row['preferred_time']) ?>"
            data-status="<?= htmlspecialchars($row['status']) ?>"
            data-comment="<?= htmlspecialchars($row['comment'] ?? '') ?>">
        View
    </button>
</td>
    
</tr>
<?php } ?>
</tbody>
            </table>

            <div class="modal fade" id="appointmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Appointment Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="modalAppointmentId">
        <p class="mb-2"><strong>Client:</strong> <span id="modalName"></span></p>
        <p class="mb-2"><strong>Mobile:</strong> <span id="modalMobile"></span></p>
        <p class="mb-3"><strong>Date:</strong> <span id="modalDate"></span> &nbsp; <strong>Time:</strong> <span id="modalTime"></span></p>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <select id="modalStatus" class="form-select">
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <div class="mb-2">
          <label class="form-label">Comment</label>
          <textarea id="modalComment" class="form-control" rows="3" placeholder="Add comment..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="modalUpdateBtn" class="btn btn-success">Update &amp; Notify</button>
      </div>
    </div>
  </div>
</div>


           
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
  <script src="assets/js/admin-appointments.js"></script>
  
</body>
</html>
