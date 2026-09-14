<?php
/**
 * admin-enquiries-list.php
 * Returns a filtered, paginated JSON list of contact_enquiries for the
 * admin panel table (admin-enquiries.php / admin-enquiries.js).
 *
 * GET params:
 *   status         (optional) new | in_progress | resolved
 *   enquiry_type   (optional) exact match against the enquiry_type enum
 *   date_from      (optional) YYYY-MM-DD
 *   date_to        (optional) YYYY-MM-DD
 *   search         (optional) matches full_name / email / mobile
 *   page           (optional) default 1
 *   page_size      (optional) default 25, max 100
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------
// 1. Admin auth guard (same session key used by admin-orders.php)
// ---------------------------------------------------------------
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// ---------------------------------------------------------------
// 2. DB connection
//    Replace with your shared connection include if you have one,
//    e.g. require_once __DIR__ . '/../config/db.php';
// ---------------------------------------------------------------
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'vastu_db';

$conn = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}
$conn->set_charset('utf8mb4');

// ---------------------------------------------------------------
// 3. Read + sanitize filters
// ---------------------------------------------------------------
$status       = $_GET['status'] ?? '';
$enquiryType  = $_GET['enquiry_type'] ?? '';
$dateFrom     = $_GET['date_from'] ?? '';
$dateTo       = $_GET['date_to'] ?? '';
$search       = trim($_GET['search'] ?? '');
$page         = max(1, (int)($_GET['page'] ?? 1));
$pageSize     = (int)($_GET['page_size'] ?? 25);
$pageSize     = $pageSize > 0 ? min($pageSize, 100) : 25;
$offset       = ($page - 1) * $pageSize;

$allowedStatuses = ['new', 'in_progress', 'resolved'];
$allowedTypes = [
    'Consultation Details',
    'Sacred Store & Products',
    'Commercial Partnership',
    'General Support'
];

$where  = [];
$params = [];
$types  = '';

if ($status !== '' && in_array($status, $allowedStatuses, true)) {
    $where[]  = 'status = ?';
    $params[] = $status;
    $types   .= 's';
}

if ($enquiryType !== '' && in_array($enquiryType, $allowedTypes, true)) {
    $where[]  = 'enquiry_type = ?';
    $params[] = $enquiryType;
    $types   .= 's';
}

if ($dateFrom !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
    $where[]  = 'DATE(created_at) >= ?';
    $params[] = $dateFrom;
    $types   .= 's';
}

if ($dateTo !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
    $where[]  = 'DATE(created_at) <= ?';
    $params[] = $dateTo;
    $types   .= 's';
}

if ($search !== '') {
    $where[]  = '(full_name LIKE ? OR email LIKE ? OR mobile LIKE ?)';
    $like     = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types   .= 'sss';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// ---------------------------------------------------------------
// 4. Total count (for pagination)
// ---------------------------------------------------------------
$countSql  = "SELECT COUNT(*) AS total FROM tbl_contact_enquiries $whereSql";
$countStmt = $conn->prepare($countSql);

if ($params) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRow = $countStmt->get_result()->fetch_assoc();
$total    = (int)($totalRow['total'] ?? 0);
$countStmt->close();

// ---------------------------------------------------------------
// 5. Fetch page of rows
// ---------------------------------------------------------------
$sql = "SELECT id, full_name, email, mobile, enquiry_type, message, status, created_at
        FROM tbl_contact_enquiries
        $whereSql
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

$limitTypes  = $types . 'ii';
$limitParams = array_merge($params, [$pageSize, $offset]);
$stmt->bind_param($limitTypes, ...$limitParams);
$stmt->execute();
$result = $stmt->get_result();

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = [
        'id'           => (int)$row['id'],
        'full_name'    => $row['full_name'],
        'email'        => $row['email'],
        'mobile'       => $row['mobile'],
        'enquiry_type' => $row['enquiry_type'],
        'message'      => $row['message'],
        'created_at'   => $row['created_at'],
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success'    => true,
    'data'       => $rows,
    'page'       => $page,
    'page_size'  => $pageSize,
    'total'      => $total,
    'total_pages'=> (int)ceil($total / $pageSize),
]);