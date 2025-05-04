<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['specialty_name']);
    $image = mysqli_real_escape_string($conn, $_POST['specialty_image']);

    $sql = "INSERT INTO specialties (name, image) VALUES ('$name', '$image')";

    if ($conn->query($sql) === TRUE) {
        header('Location: admin.html?status=specialty_added');
    } else {
        echo "خطأ: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>