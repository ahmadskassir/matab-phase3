<?php
session_start();
require 'db_connect.php';

// التحقق مما إذا كان الطبيب قد سجل الدخول مسبقًا
if (isset($_SESSION['doctor_id'])) {
    header("Location: doctor_dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, password FROM doctor_credentials WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        $error = "خطأ في تحضير الاستعلام: " . $conn->error;
    } else {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();

        if ($doctor && password_verify($password, $doctor['password'])) {
            $_SESSION['doctor_id'] = $doctor['id'];
            header("Location: doctor_dashboard.php");
            exit();
        } else {
            $error = "ایمیل یا رمز عبور اشتباه است";
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
    <title>ورود پزشک - مطب</title>
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
        .login-container {
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
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 6px;
        }
        .login-btn {
            background-color: #0d47a1;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        .login-btn:hover {
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
        function openHomePage() {
            window.location.href = 'index.php';
        }
    </script>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <a href="http://matab.ct.ws">
                <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
            </a>
            <h3 class="site-name">مطب - با ما، درمان نزدیک‌تر از همیشه</h3>
        </div>
        <form method="post">
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <div class="form-group">
                <input type="email" name="email" placeholder="ایمیل" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="رمز عبور" required>
            </div>
            <button type="submit" name="login" class="login-btn">ورود</button>
            <a href="doctor_register.php" class="B2">ثبت نام پزشک</a>
        </form>
    </div>
</body>
</html>