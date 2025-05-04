<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    
    $sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "نام کاربری یا ایمیل قبلاً استفاده شده است";
    } else {
        //pass
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // insert to database
        $sql = "INSERT INTO users (username, email, password, full_name, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $email, $hashed_password, $full_name, $phone, $address);

        if ($stmt->execute()) {
            
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: index.php");
            exit();
        } else {
            $error = "خطأ أثناء التسجيل، حاول مرة أخرى";
        }
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <link rel="icon" type="image/x-icon" href="https://i.postimg.cc/J0dCfhLH/1111.jpg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ثبت‌نام - مطب</title>
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
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-top: 6px;
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
        function openHomePage() {
            window.location.href = 'index.php';
        }
    </script>
</head>
<body>
    <div class="register-container">
        <div class="logo-container">
            <a href="index.php">
                <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
            </a>
            <h3 class="site-name">مطب - با ما، درمان نزدیک‌تر از همیشه</h3>
        </div>
        <form method="post">
            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <div class="form-group">
                <input type="text" name="username" placeholder="نام کاربری" required>
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="ایمیل" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="رمز عبور" required>
            </div>
            <div class="form-group">
                <input type="text" name="full_name" placeholder="نام کامل" required>
            </div>
            <div class="form-group">
                <input type="text" name="phone" placeholder="شماره تلفن" pattern="[0-9]{10,15}" title="شماره تلفن باید بین 10 إلى 15 رقماً">
            </div>
            <div class="form-group">
                <input type="text" name="address" placeholder="آدرس">
            </div>
            <button type="submit" name="register" class="register-btn">ثبت‌نام</button>
            <a href="login.php" class="B2">ورود</a>
        </form>
    </div>
</body>
</html>