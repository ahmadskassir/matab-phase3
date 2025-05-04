<?php
session_start(); // بدء الجلسة للتحقق من تسجيل الدخول
require 'db_connect.php';

// التحقق من وجود معرف الطبيب
$doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : null; // تحويل إلى عدد صحيح للأمان
$doctor = null;
$appointments = [];

if ($doctor_id) {
    // جلب تفاصيل الطبيب باستخدام استعلام معد
    $sql = "SELECT * FROM doctors WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
    }
    $stmt->close();

    // جلب المواعيد المتاحة
    $sql = "SELECT * FROM doctor_appointments WHERE doctor_id = ? AND status = 'available' ORDER BY appointment_date, start_time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmt->close();
}

// معالجة طلب الحجز
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointment_id']) && isset($_SESSION['user_id'])) {
    $appointment_id = (int)$_POST['appointment_id'];
    $user_id = (int)$_SESSION['user_id'];

    // التحقق من أن الموعد متاح
    $sql = "SELECT * FROM doctor_appointments WHERE id = ? AND status = 'available'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
        $booking_date = $appointment['appointment_date'] . ' ' . $appointment['start_time'];
        $booking_type = 'doctor';
        $target_name = $doctor['name'];
        $status = 'pending';

        // إدخال الحجز في جدول bookings مع doctor_id
        $sql = "INSERT INTO bookings (user_id, doctor_id, booking_date, booking_type, target_name, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissss", $user_id, $doctor_id, $booking_date, $booking_type, $target_name, $status);
        
        if ($stmt->execute()) {
            // تحديث حالة الموعد إلى محجوز
            $sql = "UPDATE doctor_appointments SET status = 'booked' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $booking_message = 'نوبت با موفقیت رزرو شد!';
        } else {
            $booking_message = 'هنگام رزرو خطایی رخ داد، لطفاً دوباره امتحان کنید';
        }
        $stmt->close();
    } else {
        $booking_message = 'زمان نوبت در دسترس نیست';
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['user_id'])) {
    $booking_message = 'لطفا برای رزرو نوبت وارد سايت شوید.';
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <link rel="icon" type="image/x-icon" href="https://i.postimg.cc/J0dCfhLH/1111.jpg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مطب</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .details-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .logo-container {
            margin-bottom: 30px;
        }

        .logo {
            width: 100px;
            height: 100px;
            margin-bottom: 15px;
        }

        .site-name {
            color: black;
            margin: 10px 0;
        }

        .doctor-img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        h2 {
            color: #0d47a1;
            margin-bottom: 20px;
        }

        p {
            margin: 10px 0;
            color: #333;
            text-align: right;
        }

        .back-btn {
            background-color: #0d47a1;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .back-btn:hover {
            background-color: #0056b3;
        }

        .booking-section {
            margin-top: 30px;
            text-align: right;
        }

        .booking-section h3 {
            color: #0d47a1;
            margin-bottom: 15px;
        }

        .appointment-select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .book-btn {
            background-color: #0d47a1;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .book-btn:hover {
            background-color: #0056b3;
        }

        .message {
            margin: 15px 0;
            color: #333;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="details-container">
        <div class="logo-container">
            <a href="index.php">
                <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
            </a>
            <h3 class="site-name">مطب - با ما، درمان نزدیک‌تر از همیشه</h3>
        </div>

        <?php
        if ($doctor) {
            echo '
                <img src="' . htmlspecialchars($doctor['image']) . '" alt="' . htmlspecialchars($doctor['name']) . '" class="doctor-img" onerror="this.src=\'placeholder.jpg\'">
                <h2>' . htmlspecialchars($doctor['name']) . '</h2>
                <p><strong>تخصص:</strong> ' . htmlspecialchars($doctor['specialty']) . '</p>
                <p><strong>هزینه ویزیت:</strong> ' . htmlspecialchars($doctor['examination_fee']) . ' ریال</p>
                <p><strong>آدرس کلینیک:</strong> ' . htmlspecialchars($doctor['clinic_address']) . '</p>
            ';
        } else {
            echo '<p>پزشک یافت نشد</p>';
        }
        ?>

        <!-- قسم الحجز -->
        <div class="booking-section">
            <h3>رزرو نوبت</h3>
            <?php if (isset($booking_message)): ?>
                <p class="message <?php echo strpos($booking_message, 'نجاح') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($booking_message); ?>
                </p>
            <?php endif; ?>
            <?php if ($doctor && !empty($appointments)): ?>
                <form method="POST">
                    <select name="appointment_id" class="appointment-select" required>
                        <option value="">انتخاب نوبت</option>
                        <?php foreach ($appointments as $appointment): ?>
                            <option value="<?php echo htmlspecialchars($appointment['id']); ?>">
                                <?php echo htmlspecialchars($appointment['appointment_date'] . ' - ' . $appointment['start_time'] . ' تا ' . $appointment['end_time'] . ' (' . ($appointment['shift'] == 'morning' ? 'صبح' : 'عصر') . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="book-btn">رزرو نوبت</button>
                </form>
            <?php else: ?>
                <p class="message error">هیچ نوبت آزادی در دسترس نیست</p>
            <?php endif; ?>
        </div>

        <a href="index.php"><button class="back-btn">بازگشت به صفحه اصلی</button></a>
    </div>
</body>
</html>

<?php
$conn->close();
?>