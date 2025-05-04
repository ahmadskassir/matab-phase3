<?php
session_start(); 
require 'db_connect.php';


$lab_id = isset($_GET['id']) ? $_GET['id'] : null;
$lab = null;
$appointments = [];

if ($lab_id) {
   
    $sql = "SELECT * FROM laboratories WHERE id = '$lab_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $lab = $result->fetch_assoc();
    }

   
    $sql = "SELECT * FROM lab_appointments WHERE lab_id = '$lab_id' AND status = 'available' ORDER BY appointment_date, start_time";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['appointment_id']) && isset($_SESSION['user_id'])) {
    $appointment_id = $_POST['appointment_id'];
    $sql = "SELECT * FROM lab_appointments WHERE id = '$appointment_id' AND status = 'available'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
        $booking_date = $appointment['appointment_date'] . ' ' . $appointment['start_time'];
        $booking_type = 'lab';
        $target_name = $lab['name'];
        $user_id = $_SESSION['user_id'];
        $status = 'pending';

       
        $sql = "INSERT INTO bookings (user_id, booking_date, booking_type, target_name, status) 
                VALUES ('$user_id', '$booking_date', '$booking_type', '$target_name', '$status')";
        if ($conn->query($sql)) {
           
            $sql = "UPDATE lab_appointments SET status = 'booked' WHERE id = '$appointment_id'";
            $conn->query($sql);
            $booking_message = 'نوبت با موفقیت رزرو شد!';
        } else {
            $booking_message = 'خطایی در رزرو نوبت رخ داد، لطفاً دوباره امتحان کنید.';
        }
    } else {
        $booking_message = 'نوبت انتخاب‌شده در دسترس نیست.';
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_SESSION['user_id'])) {
    $booking_message = 'لطفاً برای رزرو نوبت وارد حساب کاربری خود شوید.';
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <link rel="icon" type="image/x-icon" href="https://i.postimg.cc/J0dCfhLH/1111.jpg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مطب - جزئیات آزمایشگاه</title>
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

        .lab-img {
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
        if ($lab) {
            echo '
                <img src="' . ($lab['image'] ?: 'placeholder.jpg') . '" alt="' . $lab['name'] . '" class="lab-img">
                <h2>' . $lab['name'] . '</h2>
                <p><strong>توضیحات:</strong> ' . ($lab['description'] ?: 'بدون توضیحات') . '</p>
                <p><strong>آدرس:</strong> ' . $lab['address'] . '</p>
            ';
        } else {
            echo '<p>آزمایشگاه یافت نشد</p>';
        }
        ?>

        <!-- قسم الحجز -->
        <div class="booking-section">
            <h3>رزرو نوبت</h3>
            <?php if (isset($booking_message)): ?>
                <p class="message <?php echo strpos($booking_message, 'موفقیت') !== false ? 'success' : 'error'; ?>">
                    <?php echo $booking_message; ?>
                </p>
            <?php endif; ?>
            <?php if ($lab && !empty($appointments)): ?>
                <form method="POST">
                    <select name="appointment_id" class="appointment-select" required>
                        <option value="">انتخاب نوبت</option>
                        <?php foreach ($appointments as $appointment): ?>
                            <option value="<?php echo $appointment['id']; ?>">
                                <?php echo $appointment['appointment_date'] . ' - ' . $appointment['start_time'] . ' تا ' . $appointment['end_time']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="book-btn">رزرو نوبت</button>
                </form>
            <?php else: ?>
                <p class="message error">هیچ نوبت آزادی در دسترس نیست</p>
            <?php endif; ?>
        </div>

        <a href="laboratory.php"><button class="back-btn">بازگشت به لیست آزمایشگاه‌ها</button></a>
    </div>
</body>
</html>

<?php
$conn->close();
?>