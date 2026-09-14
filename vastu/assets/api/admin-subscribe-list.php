<?php
/**
 * admin-subscribers-list.php
 * Returns a filtered, paginated JSON list of tbl_subscribe rows for the
 * admin panel table (admin-subscribers.php / admin-subscribers.js).
 *
 * GET params:
 *   status      (optional) Active | Inactive
 *   date_from   (optional) YYYY-MM-DD
 *   date_to     (optional) YYYY-MM-DD
 *   search      (optional) matches name / email
 *   page        (optional) default 1
 *   page_size   (optional) default 25, max 100
 */

session_start();
header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------
// 1. Admin auth guard
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
$status    = $_GET['status'] ?? '';
$dateFrom  = $_GET['date_from'] ?? '';
$dateTo    = $_GET['date_to'] ?? '';
$search    = trim($_GET['search'] ?? '');
$page      = max(1, (int)($_GET['page'] ?? 1));
$pageSize  = (int)($_GET['page_size'] ?? 25);
$pageSize  = $pageSize > 0 ? min($pageSize, 100) : 25;
$offset    = ($page - 1) * $pageSize;

$allowedStatuses = ['Active', 'Inactive'];

$where  = [];
$params = [];
$types  = '';

if ($status !== '' && in_array($status, $allowedStatuses, true)) {
    $where[]  = 'status = ?';
    $params[] = $status;
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
    $where[]  = '(name LIKE ? OR email LIKE ?)';
    $like     = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

// ---------------------------------------------------------------
// 4. Total count (for pagination)
// ---------------------------------------------------------------
$countSql  = "SELECT COUNT(*) AS total FROM tbl_subscribe $whereSql";
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
$sql = "SELECT id, email, status, created_at
        FROM tbl_subscribe
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
        'id'         => (int)$row['id'],
        'email'      => $row['email'],
        'status'     => $row['status'],
        'created_at' => $row['created_at'],
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success'     => true,
    'data'        => $rows,
    'page'        => $page,
    'page_size'   => $pageSize,
    'total'       => $total,
    'total_pages' => (int)ceil($total / $pageSize),
]);