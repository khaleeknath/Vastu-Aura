    <?php
    const STOCK_CHANGE_TYPES = ['IN', 'OUT', 'ADJUSTMENT'];

    function recordStockMovement(mysqli $conn, int $productId, string $changeType, int $quantity, string $reason, int $createdBy): array
    {
        if (!in_array($changeType, STOCK_CHANGE_TYPES, true)) {
            throw new InvalidArgumentException('Invalid change type: ' . $changeType);
        }
        if ($changeType !== 'ADJUSTMENT' && $quantity <= 0) {
            throw new InvalidArgumentException('Quantity must be positive for IN/OUT');
        }

        $delta = match ($changeType) {
            'IN'         => abs($quantity),
            'OUT'        => -abs($quantity),
            'ADJUSTMENT' => $quantity,
        };

        $lockStmt = mysqli_prepare($conn, "SELECT stock FROM tbl_products WHERE id = ? FOR UPDATE");
        if (!$lockStmt) {
            throw new Exception("Prepare failed (lock): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($lockStmt, 'i', $productId);
        if (!mysqli_stmt_execute($lockStmt)) {
            throw new Exception("Execute failed (lock): " . mysqli_stmt_error($lockStmt));
        }
        $product = mysqli_fetch_assoc(mysqli_stmt_get_result($lockStmt));

        if (!$product) {
            throw new Exception("Product #{$productId} not found");
        }

        $currentStock = (int)$product['stock'];
        $newStock = $currentStock + $delta;

        if ($newStock < 0) {
            throw new Exception("Insufficient stock for product #{$productId}: only {$currentStock} available");
        }

        $insertStmt = mysqli_prepare($conn, "INSERT INTO tbl_stock_ledger (product_id, change_type, quantity, reason, created_by) VALUES (?, ?, ?, ?, ?)");
        if (!$insertStmt) {
            throw new Exception("Prepare failed (ledger insert): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($insertStmt, 'isisi', $productId, $changeType, $quantity, $reason, $createdBy);
        if (!mysqli_stmt_execute($insertStmt)) {
            throw new Exception("Execute failed (ledger insert): " . mysqli_stmt_error($insertStmt));
        }

        $updateStmt = mysqli_prepare($conn, "UPDATE tbl_products SET stock = ? WHERE id = ?");
        if (!$updateStmt) {
            throw new Exception("Prepare failed (stock update): " . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($updateStmt, 'ii', $newStock, $productId);
        if (!mysqli_stmt_execute($updateStmt)) {
            throw new Exception("Execute failed (stock update): " . mysqli_stmt_error($updateStmt));
        }

        return ['new_stock' => $newStock];
    }

    function recordStockMovementStandalone(mysqli $conn, int $productId, string $changeType, int $quantity, string $reason, int $createdBy): array
    {
        mysqli_begin_transaction($conn);
        try {
            $result = recordStockMovement($conn, $productId, $changeType, $quantity, $reason, $createdBy);
            mysqli_commit($conn);
            return ['success' => true, 'message' => 'Stock updated', 'new_stock' => $result['new_stock']];
        } catch (Exception $e) {
            mysqli_rollback($conn);
            return ['success' => false, 'message' => $e->getMessage(), 'new_stock' => null];
        }
    }