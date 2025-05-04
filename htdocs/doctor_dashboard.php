<?php
session_start();
require 'db_connect.php';

// بررسی اینکه آیا پزشک وارد شده است
if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit();
}

$doctor_id = (int)$_SESSION['doctor_id'];
$status_message = '';

// دریافت شناسه پزشک از جدول doctors بر اساس credential_id
$sql = "SELECT id FROM doctors WHERE credential_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// بررسی اینکه آیا پزشک در جدول doctors وجود دارد
if (!$doctor) {
    header("Location: doctor_login.php?error=حساب پزشک وجود ندارد");
    exit();
}

$doctor_id_in_doctors = $doctor['id'];

// پردازش تغییر وضعیت نوبت
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['booking_id']) && isset($_POST['action'])) {
    $booking_id = (int)$_POST['booking_id'];
    $action = $_POST['action'];

    // بررسی اینکه نوبت متعلق به پزشک است
    $sql = "SELECT id FROM bookings WHERE id = ? AND doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $doctor_id_in_doctors);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();

    if ($booking) {
        if ($action === 'confirm') {
            // به‌روزرسانی وضعیت نوبت به confirmed
            $sql = "UPDATE bookings SET status = 'confirmed' WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $booking_id);
            if ($stmt->execute()) {
                $status_message = "نوبت با موفقیت تأیید شد!";
            } else {
                $status_message = "خطایی در تأیید نوبت رخ داد.";
            }
        } elseif ($action === 'cancel') {
            // حذف کامل نوبت از جدول bookings
            $sql = "DELETE FROM bookings WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $booking_id);
            if ($stmt->execute()) {
                $status_message = "نوبت با موفقیت حذف شد!";
            } else {
                $status_message = "خطایی در حذف نوبت رخ داد.";
            }
        }
    } else {
        $status_message = "نوبت وجود ندارد یا متعلق به شما نیست.";
    }
    $stmt->close();
}

// دریافت نوبت‌های رزروشده
$sql = "SELECT b.id, b.booking_date, b.status, u.username, u.email 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        WHERE b.doctor_id = ? 
        ORDER BY b.booking_date DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("خطا در آماده‌سازی پرس‌وجو: " . $conn->error);
}
$stmt->bind_param("i", $doctor_id_in_doctors);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <link rel="icon" type="image/x-icon" href="https://i.postimg.cc/J0dCfhLH/1111.jpg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل پزشک - مطب</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .details-container {
            max-width: 1000px;
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
        h2 {
            color: #0d47a1;
            margin-bottom: 20px;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: right;
        }
        th {
            background-color: #0d47a1;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin: 0 5px;
        }
        .confirm-btn {
            background-color: #28a745;
            color: white;
        }
        .confirm-btn:hover {
            background-color: #218838;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
        }
        .cancel-btn:hover {
            background-color: #c82333;
        }
        .logout-btn {
            background-color: #0d47a1;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            width: 100%;
        }
        .logout-btn:hover {
            background-color: #0056b3;
        }
        .no-bookings {
            color: #666;
            text-align: center;
            margin-top: 20px;
        }
        .message {
            margin: 15px 0;
            text-align: center;
        }
        .success {
            color: green;
        }
        .error {
            color: red;
        }
    </style>
    <script>
        function logout() {
            window.location.href = 'logout.php';
        }
    </script>
</head>
<body>
    <div class="details-container">
        <div class="logo-container">
            <a href="index.php">
                <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
            </a>
            <h3 class="site-name">مطب - با ما، درمان نزدیک‌تر از همیشه</h3>
        </div>
        <h2>پنل پزشک - مدیریت نوبت‌ها</h2>
        <?php if ($status_message): ?>
            <p class="message <?php echo strpos($status_message, 'موفقیت') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($status_message); ?>
            </p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <p class="message error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php endif; ?>
        <?php if (empty($bookings)): ?>
            <p class="no-bookings">هیچ نوبت رزروشده‌ای وجود ندارد</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>نام بیمار</th>
                        <th>ایمیل بیمار</th>
                        <th>تاریخ و زمان نوبت</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['username']); ?></td>
                            <td><?php echo htmlspecialchars($booking['email']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                            <td>
                                <?php
                                switch ($booking['status']) {
                                    case 'pending':
                                        echo 'در انتظار تأیید';
                                        break;
                                    case 'confirmed':
                                        echo 'تأییدشده';
                                        break;
                                    case 'canceled':
                                        echo 'لغوشده';
                                        break;
                                    default:
                                        echo 'نامشخص';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ($booking['status'] == 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                        <input type="hidden" name="action" value="confirm">
                                        <button type="submit" class="action-btn confirm-btn">تأیید</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                        <input type="hidden" name="action" value="cancel">
                                        <button type="submit" class="action-btn cancel-btn">حذف</button>
                                    </form>
                                <?php else: ?>
                                    <span>-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <button class="logout-btn" onclick="logout()">خروج</button>
    </div>
</body>
</html>