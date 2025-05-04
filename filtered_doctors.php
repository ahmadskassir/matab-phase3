<?php
session_start(); 
require 'db_connect.php';

$isLoggedIn = isset($_SESSION['user_id']); 

$doctors = array();
$filter_type = '';
$filter_value = '';

if (isset($_GET['city'])) {
    $city = mysqli_real_escape_string($conn, $_GET['city']);
    $filter_type = 'شهر';
    $filter_value = $city;
    $sql = "SELECT id, name, specialty, image FROM doctors WHERE clinic_address LIKE '$city%'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }
} elseif (isset($_GET['specialty'])) {
    $specialty = mysqli_real_escape_string($conn, $_GET['specialty']);
    $filter_type = 'تخصص';
    $filter_value = $specialty;
    $sql = "SELECT id, name, specialty, image FROM doctors WHERE specialty = '$specialty'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <link rel="icon" type="image/x-icon" href="https://i.postimg.cc/J0dCfhLH/1111.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Almarai&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پزشکان - مطب</title>
    <style>
        body {
            font-family: 'Almarai', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }
        .header {
            background: linear-gradient(90deg, #007bff, #0d47a1);
            color: white;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            width: 100%;
            justify-content: space-between;
        }
        .logo {
            width: 60px;
            border-radius: 30px;
            object-fit: cover;
            cursor: pointer;
        }
        .title {
            color: white;
            flex-grow: 1;
            text-align: center;
            margin: 0;
            font-size: 1rem;
        }
        .profile-btn, .login-btn {
            margin-left: auto;
            padding: 10px 20px;
            background: #1a237e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 0.8rem;
        }
        .profile-btn:hover, .login-btn:hover {
            background: #0d47a1;
        }
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            padding: 30px 10%;
        }
        .grid-item {
            border-radius: 30px;
            overflow: hidden;
            text-align: center;
            transition: box-shadow 0.2s;
        }
        .grid-item:hover {
            box-shadow: 0px 2px 4px 2.5px #0d47a1;
            cursor: pointer;
        }
        .grid-img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            transition: transform 1s;
            border-bottom: 3px solid #0d47a1;
        }
        .grid-title {
            padding: 10px;
            color: #666;
        }
        .grid-title2 {
            padding: 5px;
            color: black;
        }
        .error {
            color: red;
            text-align: center;
            padding: 10px;
        }
        h2 {
            text-align: center;
            padding: 20px;
        }
        .back-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 1rem;
        }
        .back-btn:hover {
            background: #0d47a1;
        }
    </style>
    <script>
        // تمرير حالة تسجيل الدخول من PHP إلى JavaScript
        const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;

        function updateLoginButton() {
            const button = document.getElementById('authButton');
            if (isLoggedIn) {
                button.className = 'profile-btn';
                button.textContent = 'پروفایل';
                button.onclick = openProfilePage;
            } else {
                button.className = 'login-btn';
                button.textContent = 'ورود';
                button.onclick = openSigninPage;
            }
        }

        function openDoctorPage(doctorId) {
            window.location.href = `doctor_details.php?id=${doctorId}`;
        }

        function openSigninPage() {
            window.location.href = `login.php`;
        }

        function openProfilePage() {
            window.location.href = `profile.php`;
        }

        function openHomePage() {
            window.location.href = `index.php`;
        }

        window.addEventListener('DOMContentLoaded', updateLoginButton);
    </script>
</head>
<body>
    <header class="header">
        <a href="index.php">
            <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
        </a>
        <h4 class="title">مطب - با ما، درمان نزدیک‌تر از همیشه</h4>
        <button id="authButton" class="profile-btn"></button>
    </header>
    <h2>پزشکان بر اساس <?php echo htmlspecialchars($filter_type); ?>: <?php echo htmlspecialchars($filter_value); ?></h2>
    <div class="grid-container">
        <?php if (count($doctors) > 0): ?>
            <?php foreach ($doctors as $doctor): ?>
                <div class="grid-item" onclick="openDoctorPage(<?php echo $doctor['id']; ?>)">
                    <img src="<?php echo htmlspecialchars($doctor['image']); ?>" 
                         alt="<?php echo htmlspecialchars($doctor['name']); ?>" 
                         class="grid-img"
                         onerror="this.src='https://via.placeholder.com/150'">
                    <h3 class="grid-title2"><?php echo htmlspecialchars($doctor['name']); ?></h3>
                    <p class="grid-title"><?php echo htmlspecialchars($doctor['specialty']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="error">هیچ پزشکی با این معیار یافت نشد</p>
        <?php endif; ?>
    </div>
    <button class="back-btn" onclick="openHomePage()">بازگشت به صفحه اصلی</button>
</body>
</html>