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
include('../config/db-conn.php');
require_once('stock-ledger.php');


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
    $newStatus = trim($_POST['status'] ?? '');

    // -----------------------------------------
    // Validate order ID
    // -----------------------------------------
    if ($orderId <= 0) {
        http_response_code(422);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid order ID.'
        ]);

        return;
    }

    // -----------------------------------------
    // Validate status
    // -----------------------------------------
    if (!in_array($newStatus, $allowedStatuses, true)) {
        http_response_code(422);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid status value.'
        ]);

        return;
    }

    // -----------------------------------------
    // Get current order status
    // -----------------------------------------
    $orderStmt = $conn->prepare("
        SELECT id, status
        FROM tbl_orders
        WHERE id = ?
    ");

    if ($orderStmt === false) {
        error_log(
            "[order-status] Order SELECT prepare failed: "
            . $conn->error
        );

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Database error.'
        ]);

        return;
    }

    $orderStmt->bind_param('i', $orderId);

    if (!$orderStmt->execute()) {
        error_log(
            "[order-status] Order SELECT execute failed: "
            . $orderStmt->error
        );

        $orderStmt->close();

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Could not fetch order.'
        ]);

        return;
    }

    $order = $orderStmt->get_result()->fetch_assoc();

    $orderStmt->close();

    // -----------------------------------------
    // Check order exists
    // -----------------------------------------
    if (!$order) {
        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'Order not found.'
        ]);

        return;
    }

    $currentStatus = trim($order['status']);

    error_log(
        "[order-status] Order #{$orderId} | "
        . "Current: {$currentStatus} | "
        . "New: {$newStatus}"
    );

    // -----------------------------------------
    // Nothing to change
    // -----------------------------------------
    if ($currentStatus === $newStatus) {
        echo json_encode([
            'success' => true,
            'message' => 'Order status is already ' . $newStatus . '.'
        ]);

        return;
    }

    // -----------------------------------------
    // Determine whether stock needs deduction
    // -----------------------------------------
    $needsStockDeduction =
        ($newStatus === 'Dispatched' && $currentStatus !== 'Dispatched');

    error_log(
        "[order-status] Stock deduction required: "
        . ($needsStockDeduction ? 'YES' : 'NO')
    );

    // -----------------------------------------
    // Start transaction
    // -----------------------------------------
    mysqli_begin_transaction($conn);

    try {

        // =========================================
        // DEDUCT STOCK WHEN ORDER IS DISPATCHED
        // =========================================
        if ($needsStockDeduction) {

            // -----------------------------------------
            // Get order items
            // -----------------------------------------
            $itemStmt = $conn->prepare("
                SELECT
                    oi.product_id,
                    oi.quantity,
                    oi.price,
                    p.name,
                    p.stock
                FROM tbl_order_items oi
                INNER JOIN tbl_products p
                    ON p.id = oi.product_id
                WHERE oi.order_id = ?
            ");

            if ($itemStmt === false) {
                throw new Exception(
                    "Failed to prepare order items query: "
                    . $conn->error
                );
            }

            $itemStmt->bind_param('i', $orderId);

            if (!$itemStmt->execute()) {
                throw new Exception(
                    "Failed to fetch order items: "
                    . $itemStmt->error
                );
            }

            $result = $itemStmt->get_result();

            $items = [];

            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }

            $itemStmt->close();

            // -----------------------------------------
            // Make sure order has items
            // -----------------------------------------
            if (empty($items)) {
                throw new Exception(
                    "No products found for order #{$orderId}."
                );
            }

            // =========================================
            // CHECK STOCK FIRST
            // =========================================
            foreach ($items as $item) {

                $productId = (int)$item['product_id'];
                $quantity  = (int)$item['quantity'];
                $stock     = (int)$item['stock'];
                $productName = $item['name'];

                error_log(
                    "[order-status] Product #{$productId} "
                    . "{$productName} | "
                    . "Stock: {$stock} | "
                    . "Required: {$quantity}"
                );

                if ($stock < $quantity) {

                    throw new Exception(
                        "Insufficient stock for product: "
                        . $productName
                        . ". Available: "
                        . $stock
                        . ", Required: "
                        . $quantity
                    );
                }
            }

            // =========================================
            // DEDUCT STOCK
            // =========================================
            foreach ($items as $item) {

                $productId = (int)$item['product_id'];
                $quantity  = (int)$item['quantity'];

                // -----------------------------------------
                // Update product stock
                // -----------------------------------------
                $stockStmt = $conn->prepare("
                    UPDATE tbl_products
                    SET stock = stock - ?
                    WHERE id = ?
                      AND stock >= ?
                ");

                if ($stockStmt === false) {
                    throw new Exception(
                        "Failed to prepare stock update: "
                        . $conn->error
                    );
                }

                $stockStmt->bind_param(
                    'iii',
                    $quantity,
                    $productId,
                    $quantity
                );

                if (!$stockStmt->execute()) {

                    $error = $stockStmt->error;

                    $stockStmt->close();

                    throw new Exception(
                        "Failed to update stock for product #"
                        . $productId
                        . ": "
                        . $error
                    );
                }

                // Make sure stock was actually updated
                if ($stockStmt->affected_rows === 0) {

                    $stockStmt->close();

                    throw new Exception(
                        "Stock update failed for product #"
                        . $productId
                        . ". Insufficient stock."
                    );
                }

                $stockStmt->close();

                // -----------------------------------------
                // Record stock movement
                // -----------------------------------------
                $adminId = (int)($_SESSION['user_id'] ?? 0);

                recordStockMovement(
                    $conn,
                    $productId,
                    'OUT',
                    $quantity,
                    "Order #{$orderId} dispatched",
                    $adminId
                );

                error_log(
                    "[order-status] Stock deducted successfully | "
                    . "Product: {$productId} | "
                    . "Quantity: {$quantity}"
                );
            }
        }

        // =========================================
        // UPDATE ORDER STATUS
        // =========================================
        $statusStmt = $conn->prepare("
            UPDATE tbl_orders
            SET status = ?
            WHERE id = ?
        ");

        if ($statusStmt === false) {
            throw new Exception(
                "Failed to prepare status update: "
                . $conn->error
            );
        }

        $statusStmt->bind_param(
            'si',
            $newStatus,
            $orderId
        );

        if (!$statusStmt->execute()) {

            $error = $statusStmt->error;

            $statusStmt->close();

            throw new Exception(
                "Failed to update order status: "
                . $error
            );
        }

        $statusStmt->close();

        // =========================================
        // COMMIT
        // =========================================
        mysqli_commit($conn);

        error_log(
            "[order-status] Order #{$orderId} successfully "
            . "changed from {$currentStatus} to {$newStatus}"
        );

        echo json_encode([
            'success' => true,
            'message' => 'Order status updated successfully.'
        ]);

    } catch (Throwable $e) {

        // -----------------------------------------
        // Rollback everything
        // -----------------------------------------
        mysqli_rollback($conn);

        error_log(
            "[order-status] ERROR for order #{$orderId}: "
            . $e->getMessage()
        );

        http_response_code(409);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);

        return;
    }
}