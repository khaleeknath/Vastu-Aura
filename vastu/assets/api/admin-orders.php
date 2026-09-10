<?php
// assets/api/admin-orders.php
// JSON API consumed by assets/js/admin-orders.js
//
// Actions:
//   GET  ?action=list          -> paginated / filtered order list
//   POST action=update_status  -> updates a single order's status
//
// Uses the $conn mysqli instance from config/db-conn.php.

session_start();
header('Content-Type: application/json; charset=utf-8');

include(__DIR__ . '/../config/db-conn.php');

// ---- Auth guard ----
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not authenticated.']);
    exit;
}

$allowedStatuses = ['Pending', 'On Hold', 'Dispatched', 'Cancelled'];
// Values stored in tbl_orders.status; adjust casing here if your DB
// stores lowercase values like 'pending' instead of 'Pending'.

$action = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? ($_POST['action'] ?? '')
    : ($_GET['action'] ?? 'list');

try {
    switch ($action) {
        case 'list':
            handleList($conn, $allowedStatuses);
            break;

        case 'update_status':
            handleUpdateStatus($conn, $allowedStatuses);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
    // error_log($e->getMessage()); // uncomment to log details server-side
}

// ---------------------------------------------------------------------
// Handlers
// ---------------------------------------------------------------------

function handleList(mysqli $conn, array $allowedStatuses): void
{
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $perPage = (int)($_GET['per_page'] ?? 25);
    $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;
    $offset  = ($page - 1) * $perPage;

    $status   = trim($_GET['status'] ?? '');
    $dateFrom = trim($_GET['date_from'] ?? '');
    $dateTo   = trim($_GET['date_to'] ?? '');
    $product  = trim($_GET['product'] ?? '');
    $search   = trim($_GET['search'] ?? '');

    // $where holds SQL fragments with positional "?" placeholders.
    // $params / $types must stay in the exact same left-to-right order
    // the placeholders appear in, since mysqli has no named params.
    $where  = ['1 = 1'];
    $params = [];
    $types  = '';

    if ($status !== '' && in_array($status, $allowedStatuses, true)) {
        $where[] = 'o.status = ?';
        $params[] = $status;
        $types .= 's';
    }

    if ($dateFrom !== '') {
        $where[] = 'o.order_date >= ?';
        $params[] = $dateFrom . ' 00:00:00';
        $types .= 's';
    }

    if ($dateTo !== '') {
        $where[] = 'o.order_date <= ?';
        $params[] = $dateTo . ' 23:59:59';
        $types .= 's';
    }

    if ($product !== '') {
        $where[] = 'o.id IN (
            SELECT oi2.order_id FROM tbl_order_items oi2
            JOIN tbl_products p2 ON p2.id = oi2.product_id
            WHERE p2.name = ?
        )';
        $params[] = $product;
        $types .= 's';
    }

    if ($search !== '') {
        $where[] = '(
            CONCAT(u.first_name, " ", COALESCE(u.last_name, "")) LIKE ?
            OR u.email LIKE ?
            OR CAST(o.id AS CHAR) LIKE ?
        )';
        $like   = '%' . $search . '%';
        $likeId = $search . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $likeId;
        $types .= 'sss';
    }

    $whereSql = implode(' AND ', $where);

    // ---- Total count (distinct orders, since joins can duplicate rows) ----
    $countSql = "
        SELECT COUNT(DISTINCT o.id) AS total
        FROM tbl_orders o
        JOIN tbl_users u ON u.id = o.user_id
        WHERE $whereSql
    ";
    $countStmt = $conn->prepare($countSql);
    if ($types !== '') {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $countRow = $countStmt->get_result()->fetch_row();
    $total = (int)($countRow[0] ?? 0);
    $totalPages = $total > 0 ? (int)ceil($total / $perPage) : 1;
    $countStmt->close();

    // ---- Page of results ----
    $listSql = "
        SELECT
            o.id,
            o.total_amount,
            o.status,
            o.order_date,
            CONCAT(u.first_name, ' ', COALESCE(u.last_name, '')) AS customer_name,
            u.email,
            GROUP_CONCAT(DISTINCT p.name ORDER BY p.name SEPARATOR ', ') AS products
        FROM tbl_orders o
        JOIN tbl_users u ON u.id = o.user_id
        LEFT JOIN tbl_order_items oi ON oi.order_id = o.id
        LEFT JOIN tbl_products p ON p.id = oi.product_id
        WHERE $whereSql
        GROUP BY o.id
        ORDER BY o.order_date DESC
        LIMIT ? OFFSET ?
    ";
    $listParams = $params;
    $listParams[] = $perPage;
    $listParams[] = $offset;
    $listTypes = $types . 'ii';

    $listStmt = $conn->prepare($listSql);
    $listStmt->bind_param($listTypes, ...$listParams);
    $listStmt->execute();
    $orders = $listStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $listStmt->close();

    // Distinct product list for the filter dropdown (only sent once,
    // the JS caches it after the first successful response).
    $productsResult = $conn->query('SELECT DISTINCT name FROM tbl_products ORDER BY name');
    $products = array_column($productsResult->fetch_all(MYSQLI_ASSOC), 'name');

    echo json_encode([
        'success' => true,
        'orders'  => $orders,
        'products' => $products,
        'pagination' => [
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'total_pages'  => $totalPages,
        ],
    ]);
}

function handleUpdateStatus(mysqli $conn, array $allowedStatuses): void
{
    $orderId = (int)($_POST['order_id'] ?? 0);
    $status  = trim($_POST['status'] ?? '');

    if ($orderId <= 0) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
        return;
    }

    if (!in_array($status, $allowedStatuses, true)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
        return;
    }

    $stmt = $conn->prepare('UPDATE tbl_orders SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $orderId);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected === 0) {
        // Either the order doesn't exist, or the status was already the same.
        $check = $conn->prepare('SELECT id FROM tbl_orders WHERE id = ?');
        $check->bind_param('i', $orderId);
        $check->execute();
        $exists = $check->get_result()->fetch_row();
        $check->close();

        if (!$exists) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Order not found.']);
            return;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Order status updated.']);
}

// function handleUpdateStatus(mysqli $conn, array $allowedStatuses): void
// {
//     $orderId = (int)($_POST['order_id'] ?? 0);
//     $status  = trim($_POST['status'] ?? '');

//     if ($orderId <= 0) {
//         http_response_code(422);
//         echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
//         return;
//     }

//     if (!in_array($status, $allowedStatuses, true)) {
//         http_response_code(422);
//         echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
//         return;
//     }

//     // Fetch current order state first — we need payment_method and the
//     // stock_deducted flag to decide whether this transition should touch stock.
//     $orderStmt = $conn->prepare(
//         'SELECT status, payment_method, stock_deducted FROM tbl_orders WHERE id = ?'
//     );
//     $orderStmt->bind_param('i', $orderId);
//     $orderStmt->execute();
//     $order = $orderStmt->get_result()->fetch_assoc();
//     $orderStmt->close();

//     if (!$order) {
//         http_response_code(404);
//         echo json_encode(['success' => false, 'message' => 'Order not found.']);
//         return;
//     }

//     $isCod            = strtoupper($order['payment_method'] ?? '') === 'Cash on Delivery';
//     $movingToDispatch = $status === 'Dispatched' && $order['status'] !== 'Dispatched';
//     $needsStockDeduct = $isCod && $movingToDispatch && (int)$order['stock_deducted'] === 0;

//     $adminId = (int)($_SESSION['user_id'] ?? 0);

//     mysqli_begin_transaction($conn);
//     try {
//         if ($needsStockDeduct) {
//             $itemsStmt = $conn->prepare(
//                 'SELECT product_id, quantity FROM tbl_order_items WHERE order_id = ?'
//             );
//             $itemsStmt->bind_param('i', $orderId);
//             $itemsStmt->execute();
//             $items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
//             $itemsStmt->close();

//             if (empty($items)) {
//                 throw new Exception("No line items found for order #{$orderId}; refusing to dispatch.");
//             }

//             foreach ($items as $item) {
//                 // Will throw (and roll back everything, including the status
//                 // update below) if any single product doesn't have enough stock.
//                 recordStockMovement(
//                     $conn,
//                     (int)$item['product_id'],
//                     'OUT',
//                     (int)$item['quantity'],
//                     "Order #{$orderId} dispatched (COD)",
//                     $adminId
//                 );
//             }
//         }

//         // $setStockFlag = $needsStockDeduct ? ', stock_deducted = 1' : '';
//         // $updateStmt = $conn->prepare(
//         //     "UPDATE tbl_orders SET status = ? {$setStockFlag} WHERE id = ?"
//         // );
//         // $updateStmt->bind_param('si', $status, $orderId);
//         // $updateStmt->execute();
//         // $updateStmt->close();

//         mysqli_commit($conn);
//     } catch (Throwable $e) {
//         mysqli_rollback($conn);
//         http_response_code(409);
//         echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        
//         return;
//     }

//     echo json_encode(['success' => true, 'message' => 'Order status updated.']);
// }