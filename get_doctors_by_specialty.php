<?php
require 'db_connect.php';

if (isset($_GET['specialty'])) {
    $specialty = mysqli_real_escape_string($conn, $_GET['specialty']);
    $sql = "SELECT id, name, specialty, image FROM doctors WHERE specialty = '$specialty'";
    $result = $conn->query($sql);

    $doctors = array();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($doctors, JSON_UNESCAPED_UNICODE);
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>