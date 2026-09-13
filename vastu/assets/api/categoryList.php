<?php
session_start();
header('Content-Type: application/json');
include('../config/db-conn.php');

$sql = "SELECT id, name FROM tbl_categories ORDER BY name ASC";
$result =  mysqli_query($conn, $sql);

$categories = [];
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

echo json_encode(['data' => $categories]);