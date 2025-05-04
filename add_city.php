<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['city_name']);

    $sql = "INSERT INTO cities (name) VALUES ('$name')";

    if ($conn->query($sql) === TRUE) {
        header('Location: admin.html?status=city_added');
    } else {
        echo "خطأ: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>