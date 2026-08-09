<?php

session_start();
header("Content-Type: application/json");

include("../config/db-conn.php");

// Check Login
if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT

            o.id,
            o.total_amount,
            o.status AS order_status,
    DATE_FORMAT(o.order_date,'%d %b %Y, %h:%i %p') AS order_date,
            p.payment_method,
            p.payment_status,
            p.transaction_id

        FROM tbl_orders o

        LEFT JOIN tbl_payments p
            ON o.id = p.order_id

        WHERE o.user_id = ?

        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);

if(!$stmt){

    echo json_encode([
        "success"=>false,
        "message"=>$conn->error
    ]);
    exit;
}

$stmt->bind_param("i",$userId);
$stmt->execute();

$result = $stmt->get_result();

$orders = [];

while($row = $result->fetch_assoc()){

    $orders[] = $row;

}

echo json_encode([
    "success"=>true,
    "orders"=>$orders
]);

$stmt->close();
$conn->close();

?>