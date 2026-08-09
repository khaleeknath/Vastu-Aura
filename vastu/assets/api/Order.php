<?php
session_start();
include('../config/db-conn.php');

header("Content-Type: application/json");

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "success"=>false,
        "message"=>"Please login first."
    ]);
    exit;
}

$user_id = $_SESSION['user_id'];

$data = json_decode(file_get_contents("php://input"), true);

$paymentMethod = $data['payment_method'];

mysqli_begin_transaction($conn);

try{

    // Get Cart Items
    $sql = "
    SELECT
        c.product_id,
        c.quantity,
        p.price
    FROM tbl_cart c
    INNER JOIN tbl_products p
        ON p.id=c.product_id
    WHERE c.user_id=?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$user_id);
    $stmt->execute();

    $cart = $stmt->get_result();

    if($cart->num_rows==0){
        throw new Exception("Cart is empty.");
    }

    $total=0;

    $items=[];

    while($row=$cart->fetch_assoc()){

        $total += $row['price'] * $row['quantity'];

        $items[]=$row;
    }

    // Shipping
    $shipping = 150;

    $grandTotal = $total + $shipping;

    // Insert Order
    $stmt = $conn->prepare("
    INSERT INTO tbl_orders(user_id,total_amount,order_date)
    VALUES(?,?,NOW())
    ");

    $stmt->bind_param("id",$user_id,$grandTotal);
    $stmt->execute();

    $order_id = $conn->insert_id;

    // Insert Order Items

    $stmt = $conn->prepare("
    INSERT INTO tbl_order_items
    (order_id,product_id,quantity,price)
    VALUES(?,?,?,?)
    ");

    foreach($items as $item){

        $stmt->bind_param(
            "iiid",
            $order_id,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        );

        $stmt->execute();
    }

    // Payment

    $paymentStatus =
        $paymentMethod=="Cash on Delivery"
        ? "Pending"
        : "Paid";

    $transactionId =
        $paymentMethod=="Cash on Delivery"
        ? NULL
        : uniqid("TXN");

    $stmt=$conn->prepare("
    INSERT INTO tbl_payments
    (
        order_id,
        payment_method,
        payment_status,
        transaction_id,
        paid_at
    )
    VALUES(?,?,?,?,NOW())
    ");

    $stmt->bind_param(
        "isss",
        $order_id,
        $paymentMethod,
        $paymentStatus,
        $transactionId
    );

    $stmt->execute();

    // Clear Cart

    $stmt=$conn->prepare("
    DELETE FROM tbl_cart
    WHERE user_id=?
    ");

    $stmt->bind_param("i",$user_id);

    $stmt->execute();

    mysqli_commit($conn);

    echo json_encode([
        "success"=>true,
        "order_id"=>$order_id,
        "message"=>"Order placed successfully."
    ]);

}catch(Exception $e){

    mysqli_rollback($conn);

    echo json_encode([
        "success"=>false,
        "message"=>$e->getMessage()
    ]);
}