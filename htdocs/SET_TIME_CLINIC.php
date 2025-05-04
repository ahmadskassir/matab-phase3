<?php
require 'db_connect.php';

// تعريف الأيام (السبت إلى الأربعاء)
$days = [6, 0, 1, 2, 3]; // شنبه، یکشنبه، دوشنبه، سه‌شنبه، چهارشنبه
$slots = ['08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00']; // من 8 صباحًا إلى 2 عصرًا

$sql = "SELECT id FROM clinics";
$result = $conn->query($sql);
$clinic_ids = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $clinic_ids[] = $row['id'];
    }
}

foreach ($clinic_ids as $clinic_id) {
    foreach ($days as $day) {
        $date = date('Y-m-d', strtotime("next Saturday + $day days")); // تاريخ الأسبوع القادم
        foreach ($slots as $start_time) {
            $end_time = date('H:i:s', strtotime($start_time) + 3600); // مدة الموعد ساعة
            $sql = "INSERT INTO clinic_appointments (clinic_id, appointment_date, start_time, end_time, status) 
                    VALUES ('$clinic_id', '$date', '$start_time', '$end_time', 'available')";
            $conn->query($sql);
        }
    }
}

$conn->close();
?>