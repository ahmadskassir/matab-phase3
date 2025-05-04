<?php
require 'db_connect.php';

// تعريف الأيام والشيفتات وفترات المواعيد
$days = [0, 2, 4]; // یکشنبه، سه‌شنبه، پنج‌شنبه (0 = یکشنبه، 2 = سه‌شنبه، 4 = پنج‌شنبه)
$shifts = ['morning', 'afternoon'];
$morning_slots = ['08:00:00', '09:00:00', '10:00:00', '11:00:00']; // شیفت صبح
$afternoon_slots = ['14:00:00', '15:00:00', '16:00:00', '17:00:00']; // شیفت عصر

// قائمة معرفات الأطباء من 1 إلى 9
$doctor_ids = range(41,41); // [1, 2, 3, 4, 5, 6, 7, 8, 9]

foreach ($doctor_ids as $doctor_id) {
    foreach ($days as $day) {
        $shift = $shifts[array_rand($shifts)]; // انتخاب تصادفی شیفت
        $date = date('Y-m-d', strtotime("next Sunday + $day days")); // تعیین تاریخ هفته آینده

        $slots = ($shift == 'morning') ? $morning_slots : $afternoon_slots;
        foreach ($slots as $start_time) {
            $end_time = date('H:i:s', strtotime($start_time) + 3600); // مدت زمان نوبت یک ساعت
            $sql = "INSERT INTO doctor_appointments (doctor_id, appointment_date, start_time, end_time, shift, status) 
                    VALUES ('$doctor_id', '$date', '$start_time', '$end_time', '$shift', 'available')";
            $conn->query($sql);
        }
    }

}
$conn->close();
?>