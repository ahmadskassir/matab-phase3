<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['doctor_name']);
    $specialty = mysqli_real_escape_string($conn, $_POST['specialty']);
    $examination_fee = mysqli_real_escape_string($conn, $_POST['examination_fee']);
    $clinic_address = mysqli_real_escape_string($conn, $_POST['clinic_address']);
    $image = mysqli_real_escape_string($conn, $_POST['doctor_image']);

    $sql = "INSERT INTO doctors (name, specialty, examination_fee, clinic_address, image) 
            VALUES ('$name', '$specialty', '$examination_fee', '$clinic_address', '$image')";

    if ($conn->query($sql) === TRUE) {
        header('Location: admin.html?status=doctor_added');
    } else {
        echo "خطأ: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>