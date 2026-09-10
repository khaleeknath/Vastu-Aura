<?php
session_start();
include(__DIR__ . '/assets/config/db-conn.php');

if (!isset($_SESSION['name'])) {
    header("Location: index.php");
    exit;
}

$category_id = $_GET['category_id'] ?? '';
$status      = $_GET['status'] ?? '';
$search      = $_GET['search'] ?? '';

// ---- Build product listing (prepared statement, no raw concatenation) ----
$query  = "SELECT p.*, c.name AS category_name
           FROM tbl_products p
           LEFT JOIN tbl_categories c ON c.id = p.category_id
           WHERE 1=1";
$types  = "";
$params = [];

if ($category_id !== '') {
    $query   .= " AND p.category_id = ?";
    $types   .= "i";
    $params[] = $category_id;
}
if ($status !== '') {
    $query   .= " AND p.status = ?";
    $types   .= "s";
    $params[] = $status;
}
if ($search !== '') {
    $query   .= " AND p.name LIKE ?";
    $types   .= "s";
    $params[] = "%$search%";
}
$query .= " ORDER BY p.id DESC";

$stmt = mysqli_prepare($conn, $query);
if ($types !== "") {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// ---- Categories for filter dropdown + Add Product modal ----
$catResult    = mysqli_query($conn, "SELECT * FROM tbl_categories ORDER BY name ASC");
$allCategories = [];
while ($c = mysqli_fetch_assoc($catResult)) {
    $allCategories[] = $c;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Inventory | VastuAura</title>
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
          <span id="adminGreeting"><?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '' ?></span>
        </div>
        
      </header>

      <main class="admin-content">
        <section class="page-head">
          <div>
            <span class="eyebrow">Inventory</span>
            <h1>Manage categories and products.</h1>
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-brand" id="openAddCategory">+ Add Category</button>
            <button type="button" class="btn btn-primary" id="openAddProduct">+ Add Product</button>
          </div>
        </section>

        <section class="filter-card">
          <form method="GET" class="row g-3">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Category</label>
                <select id="productCategoryFilter" name="category_id" class="form-select">
                  <option value="">All Categories</option>
                  <?php foreach ($allCategories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($category_id !== '' && (int)$category_id === (int)$cat['id']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($cat['name']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Status</label>
                <select id="productStatusFilter" name="status" class="form-select">
                  <option value="">All Statuses</option>
                  <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Search</label>
                <input id="productSearch" type="search" name="search" class="form-control" placeholder="Search product name" value="<?= htmlspecialchars($search) ?>">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filter</button>
              </div>
            </div>
          </form>
        </section>

        <section class="table-card">
          <div id="productMessage" class="message-box d-none"></div>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Image</th>
                  <th>Name</th>
                  <th>Category</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
<?php
$sr = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $imgPath = !empty($row['image']) ? 'assets/uploads/products/' . htmlspecialchars($row['image']) : '';
?>
<tr>
    <td><?= $sr++ ?></td>
    <td>
      <?php if ($imgPath): ?>
        <img src="<?= $imgPath ?>" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
      <?php else: ?>
        <span class="text-muted">—</span>
      <?php endif; ?>
    </td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['category_name'] ?? '—') ?></td>
    <td>₹<?= htmlspecialchars($row['price']) ?></td>
    <td><?= htmlspecialchars($row['stock']) ?></td>
    <td>
      <span class="badge <?= $row['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
        <?= htmlspecialchars(ucfirst($row['status'])) ?>
      </span>
    </td>
    <td>
        <button type="button" class="btn btn-sm btn-outline-brand view-product"
                data-id="<?= $row['id'] ?>"
                data-name="<?= htmlspecialchars($row['name']) ?>"
                data-description="<?= htmlspecialchars($row['description'] ?? '') ?>"
                data-details="<?= htmlspecialchars($row['details'] ?? '') ?>"
                data-price="<?= htmlspecialchars($row['price']) ?>"
                data-stock="<?= htmlspecialchars($row['stock']) ?>"
                data-status="<?= htmlspecialchars($row['status']) ?>"
                data-category-id="<?= htmlspecialchars($row['category_id']) ?>"
                data-image="<?= htmlspecialchars($row['image'] ?? '') ?>">
            View / Edit
        </button>
    </td>
</tr>
<?php } ?>
              </tbody>
            </table>
          </div>
        </section>
      </main>

      <footer class="admin-footer">
        © 2026 Developed &amp; Maintained by Vyomark Digital Solutions
      </footer>
    </div>
  </div>

  <!-- Add Category Modal -->
  <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label class="form-label">Category Name</label>
          <input type="text" id="newCategoryName" class="form-control" placeholder="e.g. Crystals">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="saveCategoryBtn" class="btn btn-success">Save Category</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Product Modal -->
  <div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="addProductForm" enctype="multipart/form-data">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-select" required>
                  <option value="">Select category</option>
                  <?php foreach ($allCategories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" min="0" name="price" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Stock</label>
                <input type="number" min="0" name="stock" class="form-control" required>
              </div>
              <div class="col-md-12">
                <label class="form-label">Short Description</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
              </div>
              <div class="col-md-12">
                <label class="form-label">Details</label>
                <textarea name="details" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-md-12">
                <label class="form-label">Product Image</label>
                <input type="file" name="image" accept="image/*" class="form-control">
              </div>
              <div class="col-12">
                <p class="text-muted mb-0">New products are saved as <strong>Inactive</strong> by default. Activate them from the product list once ready.</p>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="saveProductBtn" class="btn btn-success">Save Product</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View / Edit Product Modal -->
  <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Product Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="modalProductId">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Product Name</label>
              <input type="text" id="modalName" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Category</label>
              <select id="modalCategoryId" class="form-select">
                <?php foreach ($allCategories as $cat): ?>
                  <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Price</label>
              <input type="number" step="0.01" min="0" id="modalPrice" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Stock</label>
              <input type="number" min="0" id="modalStock" class="form-control">
            </div>
            <div class="col-md-12">
              <label class="form-label">Short Description</label>
              <textarea id="modalDescription" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-12">
              <label class="form-label">Details</label>
              <textarea id="modalDetails" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select id="modalProductStatus" class="form-select">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Replace Image (optional)</label>
              <input type="file" id="modalImage" accept="image/*" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger me-auto" id="modalDeleteBtn">Delete</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" id="modalUpdateBtn" class="btn btn-success">Save Changes</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script src="assets/js/admin-products.js"></script>
</body>
</html>