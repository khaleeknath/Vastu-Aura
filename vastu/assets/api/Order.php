<?php
session_start();
include('../config/db-conn.php');
require_once('razorpay-config.php');
require_once('mailer.php');
require_once('stock-ledger.php');
header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Please login first."]);
    exit;
}
$name = $_SESSION['user']['first_name'] ?? '';
$lname = $_SESSION['user']['last_name'] ?? '';
$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);
$paymentMethod = $data['payment_method'] ?? '';

error_log("[mailer] Order data Php: " . print_r($data, true));

$isOnlinePayment = in_array($paymentMethod, ['Card Payment', 'UPI']);
$verifiedAmount = null;
$razorpay_payment_id = null;

if ($isOnlinePayment) {
    $razorpay_payment_id = $data['razorpay_payment_id'] ?? '';
    $razorpay_order_id   = $data['razorpay_order_id'] ?? '';
    $razorpay_signature  = $data['razorpay_signature'] ?? '';

    if (!$razorpay_payment_id || !$razorpay_order_id || !$razorpay_signature) {
        echo json_encode(["success" => false, "message" => "Payment details missing."]);
        exit;
    }

    if (empty($_SESSION['pending_cart_order']) ||
        $_SESSION['pending_cart_order']['razorpay_order_id'] !== $razorpay_order_id) {
        echo json_encode(["success" => false, "message" => "Order mismatch. Please retry."]);
        exit;
    }

    $expectedSignature = hash_hmac(
        'sha256',
        $razorpay_order_id . '|' . $razorpay_payment_id,
        RAZORPAY_KEY_SECRET
    );

    if (!hash_equals($expectedSignature, $razorpay_signature)) {
        echo json_encode(["success" => false, "message" => "Payment verification failed."]);
        exit;
    }

    $verifiedAmount = $_SESSION['pending_cart_order']['amount'];
    unset($_SESSION['pending_cart_order']);
}

mysqli_begin_transaction($conn);

try {
    // Get Cart Items (unchanged — used only for order processing/totals)
    $sql = "
    SELECT c.product_id, c.quantity, p.price
    FROM tbl_cart c
    INNER JOIN tbl_products p ON p.id = c.product_id
    WHERE c.user_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart = $stmt->get_result();

    if ($cart->num_rows == 0) {
        throw new Exception("Cart is empty.");
    }

    $total = 0;
    $items = [];
    while ($row = $cart->fetch_assoc()) {
        $total += $row['price'] * $row['quantity'];
        $items[] = $row;
    }

    $shipping = 150;
    $grandTotal = $total + $shipping;

    // Guard against the cart changing between payment and this insert
    if ($isOnlinePayment && abs($grandTotal - $verifiedAmount) > 0.01) {
        throw new Exception("Cart changed since payment. Contact support with payment ID: " . $razorpay_payment_id);
    }

    // Insert Order
    $stmt = $conn->prepare("INSERT INTO tbl_orders(user_id,total_amount,order_date) VALUES(?,?,NOW())");
    $stmt->bind_param("id", $user_id, $grandTotal);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert Order Items
    $stmt = $conn->prepare("INSERT INTO tbl_order_items (order_id,product_id,quantity,price) VALUES(?,?,?,?)");
    foreach ($items as $item) {
        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    // Payment
    $paymentStatus = $isOnlinePayment ? "Paid" : "Pending";
    $transactionId = $isOnlinePayment ? $razorpay_payment_id : NULL;

    $stmt = $conn->prepare("
        INSERT INTO tbl_payments (order_id,payment_method,payment_status,transaction_id,paid_at)
        VALUES(?,?,?,?,NOW())
    ");
    $stmt->bind_param("isss", $order_id, $paymentMethod, $paymentStatus, $transactionId);
    $stmt->execute();


    // Deduct stock ONLY when payment is actually confirmed successful.
    // COD orders stay "Pending" here — their stock gets deducted later,
    // when the payment is actually collected/confirmed (see mark-order-paid.php).
    if ($paymentStatus === "Paid") {
        foreach ($items as $item) {
            recordStockMovement(
                $conn,
                (int)$item['product_id'],
                'OUT',
                (int)$item['quantity'],
                "Order #{$order_id}",
                $user_id
            );
        }
    }


    // Clear Cart
    $stmt = $conn->prepare("DELETE FROM tbl_cart WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    mysqli_commit($conn);

    // ---- Separate query, just for the email: fetch the order's items WITH product names ----
    // Pulling from tbl_order_items (the permanent record just inserted) rather than the cart,
    // since the cart is now cleared and this table reflects exactly what was ordered.
    $emailItems = [];
    $itemStmt = $conn->prepare("
        SELECT oi.product_id, oi.quantity, oi.price, p.name
        FROM tbl_order_items oi
        INNER JOIN tbl_products p ON p.id = oi.product_id
        WHERE oi.order_id = ?
    ");
    $itemStmt->bind_param("i", $order_id);
    $itemStmt->execute();
    $itemResult = $itemStmt->get_result();
    while ($row = $itemResult->fetch_assoc()) {
        $emailItems[] = $row;
    }

    // Send order confirmation email AFTER order is successfully committed
    $emailData = [
        'order_id'            => $order_id,
        'name'                => $_SESSION['name'],
        'email'               => $data['email'] ?? '',
        'items'               => $emailItems,
        'order_date'          => date('Y-m-d H:i:s'),
        'payment_method'      => $paymentMethod,
        'razorpay_payment_id' => $razorpay_payment_id,
        'total'               => $total,
        'shipping'            => $shipping,
        'grand_total'         => $grandTotal
    ];

   

    $emailSent = sendOrderConfirmationEmail($emailData);

    echo json_encode(["success" => true,  "order_id" => $order_id, "email_sent" => $emailSent, "message" => "Order placed successfully."]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    if ($isOnlinePayment) {
        error_log("Order insert failed after successful payment {$razorpay_payment_id}: " . $e->getMessage());
    }
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}