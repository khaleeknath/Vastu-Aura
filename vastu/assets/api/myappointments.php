<?php

session_start();
header("Content-Type: application/json");

include("../config/db-conn.php");

if (!isset($_SESSION['user_id'])) {

    echo json_encode([
        "success" => false,
        "message" => "Please login first."
    ]);

    exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT

            id,
            consultation_type,
            preferred_date,
            preferred_time,
            status,
            comment

        FROM tbl_appointment

        WHERE user_id=?

        ORDER BY preferred_date DESC,
                 preferred_time DESC";

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

$result=$stmt->get_result();

$appointments=[];

while($row=$result->fetch_assoc()){

    $appointments[]=$row;

}

echo json_encode([

    "success"=>true,

    "appointments"=>$appointments

]);

$stmt->close();
$conn->close();

?>