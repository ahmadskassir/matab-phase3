<?php
require 'db_connect.php';

$sql = "SELECT id, name FROM cities";
$result = $conn->query($sql);

$cities = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($cities, JSON_UNESCAPED_UNICODE);

$conn->close();
?>