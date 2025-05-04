<?php
require 'db_connect.php';

// تعريف الأيام (السبت إلى الأربعاء)
$days = [6, 0, 1, 2, 3]; // السبت (6)، الأحد (0)، الإثنين (1)، الثلاثاء (2)، الأربعاء (3)
$slots = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00']; // من 8 صباحًا إلى 2 عصرًا

$sql = "SELECT id FROM laboratories";
$result = $conn->query($sql);
$lab_ids = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lab_ids[] = $row['id'];
    }
}

foreach ($lab_ids as $lab_id) {
    foreach ($days as $day) {
        $date = date('Y-m-d', strtotime("next Saturday + $day days")); // تاريخ الأسبوع القادم
        foreach ($slots as $start_time) {
            $end_time = date('H:i:s', strtotime($start_time) + 3600); // مدة الموعد ساعة
            $sql = "INSERT INTO lab_appointments (lab_id, appointment_date, start_time, end_time, status) 
                    VALUES ('$lab_id', '$date', '$start_time', '$end_time', 'available')";
            $conn->query($sql);
        }
    }
}

$conn->close();
?>