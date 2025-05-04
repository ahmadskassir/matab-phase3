<?php
session_start();
require 'db_connect.php';

// التحقق مما إذا كان الطبيب قد سجل الدخول مسبقًا
if (isset($_SESSION['doctor_id'])) {
    header("Location: doctor_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $specialty = trim($_POST['specialty']);
    $clinic_address = trim($_POST['clinic_address']);
    $image = trim($_POST['image']);
    $examination_fee = (int)$_POST['examination_fee'];
    $schedule = isset($_POST['schedule']) ? $_POST['schedule'] : []; // جدول زمني للأيام والشيفتات

    // التحقق من صحة البريد الإلكتروني
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "ایمیل نامعتبر است";
    } elseif (strlen($password) < 6) {
        $error = "رمز عبور باید حداقل 6 کاراکتر باشد";
    } elseif (empty($image) || !filter_var($image, FILTER_VALIDATE_URL)) {
        $error = "لطفاً یک لینک معتبر برای تصویر وارد کنید";
    } elseif (empty($schedule)) {
        $error = "لطفاً حداقل یک روز و شیفت را انتخاب کنید";
    } else {
        // التحقق مما إذا كان البريد الإلكتروني مستخدمًا مسبقًا
        $sql = "SELECT id FROM doctor_credentials WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "این ایمیل قبلاً ثبت شده است";
        } else {
            // إذا لم يكن هناك خطأ، متابعة عملية التسجيل
            // تشفير كلمة المرور
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // إدخال بيانات تسجيل الدخول في جدول doctor_credentials
            $sql = "INSERT INTO doctor_credentials (email, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $email, $hashed_password);
            if ($stmt->execute()) {
                $credential_id = $conn->insert_id;

                // إدخال بيانات الطبيب مع رابط الصورة في جدول doctors
                $sql = "INSERT INTO doctors (name, specialty, clinic_address, image, examination_fee, credential_id) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssi", $name, $specialty, $clinic_address, $image, $examination_fee, $credential_id);
                if ($stmt->execute()) {
                    // الحصول على معرف الطبيب من جدول doctors
                    $doctor_id = $conn->insert_id;

                    // إضافة المواعيد بناءً على الأيام والشيفتات المختارة باستخدام معرف الطبيب
                    $morning_slots = ['08:00:00', '09:00:00', '10:00:00', '11:00:00'];
                    $afternoon_slots = ['14:00:00', '15:00:00', '16:00:00', '17:00:00'];
                    $day_mapping = [
                        'saturday' => 6,   // شنبه
                        'sunday' => 0,     // یکشنبه
                        'monday' => 1,     // دوشنبه
                        'tuesday' => 2,    // سه‌شنبه
                        'wednesday' => 3   // چهارشنبه
                    ];

                    $has_valid_schedule = false;
                    foreach ($schedule as $day => $shifts) {
                        if (isset($day_mapping[$day]) && !empty($shifts)) {
                            $has_valid_schedule = true;
                            $day_offset = $day_mapping[$day];
                            $date = date('Y-m-d', strtotime("next Saturday + $day_offset days"));
                            foreach ($shifts as $shift) {
                                $slots = ($shift == 'morning') ? $morning_slots : $afternoon_slots;
                                foreach ($slots as $start_time) {
                                    $end_time = date('H:i:s', strtotime($start_time) + 3600);
                                    $sql = "INSERT INTO doctor_appointments (doctor_id, appointment_date, start_time, end_time, shift, status) 
                                            VALUES (?, ?, ?, ?, ?, 'available')";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("issss", $doctor_id, $date, $start_time, $end_time, $shift);
                                    if (!$stmt->execute()) {
                                        $error = "خطأ في إضافة المواعيد: " . $conn->error;
                                        break 3; // الخروج من الحلقات في حالة الخطأ
                                    }
                                }
                            }
                        }
                    }

                    // التحقق من وجود جدول زمني صالح
                    if (!$has_valid_schedule) {
                        $error = "لطفاً حداقل یک روز و شیفت را انتخاب کنید";
                    }

                    // إذا لم يحدث خطأ، الانتقال إلى لوحة التحكم
                    if (!isset($error)) {
                        $_SESSION['doctor_id'] = $credential_id;
                        header("Location: doctor_dashboard.php");
                        exit();
                    }
                } else {
                    $error = "خطا در ثبت اطلاعات پزشک: " . $conn->error;
                }
            } else {
                $error = "خطا در ثبت اطلاعات ورود: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <link rel="icon" type="image/x-icon" href="https://i.postimg.cc/J0dCfhLH/1111.jpg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت نام پزشک - مطب</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 400px;
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
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }
        input, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 6px;
        }
        .schedule-group {
            margin-bottom: 20px;
            text-align: right;
        }
        .schedule-group label {
            display: block;
            margin-bottom: 10px;
        }
        .checkbox-group {
            display: flex;
            justify-content: space-between;
            margin-top: 5px;
        }
        .register-btn {
            background-color: #0d47a1;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .register-btn:hover {
            background-color: #0056b3;
        }
        .B2 {
            font-size: 15px;
            color: #0056b3;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
        }
        .B2:hover {
            text-decoration: underline;
        }
        .error {
            color: red;
            margin-bottom: 20px;
        }
    </style>
    <script>
        // انتقال إلى الصفحة الرئيسية
        function openHomePage() {
            window.location.href = 'index.php';
        }
    </script>
</head>
<body>
    <div class="register-container">
        <div class="logo-container">
            <a href="http://matab.rf.gd">
                <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
            </a>
            <h3 class="site-name">مطب - با ما، درمان نزدیک‌تر از همیشه</h3>
        </div>
        <form method="post">
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <div class="form-group">
                <input type="text" name="name" placeholder="نام کامل" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="ایمیل" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="رمز عبور" required>
            </div>
            <div class="form-group">
                <input type="text" name="specialty" placeholder="تخصص" required>
            </div>
            <div class="form-group">
                <textarea name="clinic_address" placeholder="آدرس کلینیک" required></textarea>
            </div>
            <div class="form-group">
                <input type="url" name="image" placeholder="لینک تصویر" required>
            </div>
            <div class="form-group">
                <input type="number" name="examination_fee" placeholder="هزینه ویزیت (ریال)" required>
            </div>
            <div class="schedule-group">
                <label>روزهای کاری و شیفت‌ها:</label>
                <div class="form-group">
                    <label>شنبه:</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="schedule[saturday][morning]" value="morning"> صبح</label>
                        <label><input type="checkbox" name="schedule[saturday][afternoon]" value="afternoon"> عصر</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>یکشنبه:</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="schedule[sunday][morning]" value="morning"> صبح</label>
                        <label><input type="checkbox" name="schedule[sunday][afternoon]" value="afternoon"> عصر</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>دوشنبه:</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="schedule[monday][morning]" value="morning"> صبح</label>
                        <label><input type="checkbox" name="schedule[monday][afternoon]" value="afternoon"> عصر</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>سه‌شنبه:</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="schedule[tuesday][morning]" value="morning"> صبح</label>
                        <label><input type="checkbox" name="schedule[tuesday][afternoon]" value="afternoon"> عصر</label>
                    </div>
                </div>
                <div class="form-group">
                    <label>چهارشنبه:</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="schedule[wednesday][morning]" value="morning"> صبح</label>
                        <label><input type="checkbox" name="schedule[wednesday][afternoon]" value="afternoon"> عصر</label>
                    </div>
                </div>
            </div>
            <button type="submit" class="register-btn">ثبت نام</button>
            <a href="doctor_login.php" class="B2">ورود پزشک</a>
        </form>
    </div>
</body>
</html>