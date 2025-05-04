<?php
require 'db_connect.php';

$sql = "SELECT id, name, image FROM specialties";
$result = $conn->query($sql);

$specialties = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $specialties[] = $row;
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($specialties, JSON_UNESCAPED_UNICODE);

$conn->close();
?>