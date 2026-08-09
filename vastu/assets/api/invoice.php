<?php
session_start();

header("Content-Type: application/json");

include("../config/db-conn.php");

$user_id = $_SESSION['user_id'];

$order_id = intval($_GET['order_id']);

$sql = "
SELECT
o.id,
o.order_date,
o.total_amount,

u.first_name,
u.last_name,
u.email,

pay.payment_method,
pay.payment_status,

oi.quantity,
oi.price,

p.name

FROM tbl_orders o

INNER JOIN tbl_users u
ON u.id=o.user_id

INNER JOIN tbl_order_items oi
ON oi.order_id=o.id

INNER JOIN tbl_products p
ON p.id=oi.product_id

LEFT JOIN tbl_payments pay
ON pay.order_id=o.id

WHERE
o.id=?
AND
o.user_id=?
";

$stmt=$conn->prepare($sql);

$stmt->bind_param("ii",$order_id,$user_id);

$stmt->execute();

$result=$stmt->get_result();

$items=[];

$order=[];

while($row=$result->fetch_assoc()){

    if(empty($order)){

        $order=[
            "id"=>$row['id'],
            "date"=>$row['order_date'],
            "total"=>$row['total_amount'],
            "customer"=>$row['first_name']." ".$row['last_name'],
            "email"=>$row['email'],
            "payment_method"=>$row['payment_method'],
            "payment_status"=>$row['payment_status']
        ];
    }

    $items[]=[
        "name"=>$row['name'],
        "price"=>$row['price'],
        "qty"=>$row['quantity']
    ];
}

echo json_encode([
    "success"=>true,
    "order"=>$order,
    "items"=>$items
]);