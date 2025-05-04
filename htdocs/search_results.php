<?php
session_start();
require 'db_connect.php';

// check sign in
$is_logged_in = isset($_SESSION['user_id']);

//URL
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [
    'laboratories' => [],
    'clinics' => [],
    'imaging_centers' => [],
    'doctors' => [],
    'cities' => [],
    'specialties' => []
];

if ($search_query) {
    // البحث في المختبرات
    $sql = "SELECT id, name, image, description FROM laboratories WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results['laboratories'][] = $row;
        }
    }

    // البحث في العيادات
    $sql = "SELECT id, name, image, description FROM clinics WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results['clinics'][] = $row;
        }
    }

    // البحث في مراكز التصوير
    $sql = "SELECT id, name, image, description FROM imaging_centers WHERE name LIKE '%$search_query%' OR description LIKE '%$search_query%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results['imaging_centers'][] = $row;
        }
    }

    // البحث في الأطباء
    $sql = "SELECT id, name, image, specialty FROM doctors WHERE name LIKE '%$search_query%' OR specialty LIKE '%$search_query%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results['doctors'][] = $row;
        }
    }

    // البحث في المدن
    $sql = "SELECT id, name FROM cities WHERE name LIKE '%$search_query%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results['cities'][] = $row;
        }
    }

    // البحث في التخصصات
    $sql = "SELECT id, name, image FROM specialties WHERE name LIKE '%$search_query%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $results['specialties'][] = $row;
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
    <title>مطب - نتائج البحث</title>
    <style>
        body {
            font-family: 'Almarai', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Almarai', sans-serif; }
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
        }
        .title {
            color: white;
            flex-grow: 1;
            text-align: center;
            margin: 0;
        }
        .login-btn, .profile-btn {
            margin-left: auto;
            padding: 10px 20px;
            background: #1a237e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-btn:hover, .profile-btn:hover {
            background: #0d47a1;
        }
        .search-container {
            text-align: center;
            padding: 40px 20px;
            background-color: #e9ecef;
        }
        .search-box {
            width: 60%;
            padding: 12px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 25px;
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
        .grid-img:hover {
            transform: scale(1);
        }
        .grid-title {
            padding: 10px;
            color: #666;
        }
        .grid-title2 {
            padding: 5px;
            color: black;
        }
        .grid-container2 {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            padding: 30px 10%;
        }
        .grid-item2 {
            border-radius: 15px;
            overflow: hidden;
            text-align: center;
            transition: box-shadow 0.2s;
            transition-duration: 0.5s;
            cursor: pointer;
        }
        .grid-item2:hover {
            box-shadow: 0px 2px 4px 2.5px #0d47a1;
        }
        .grid-img2 {
            border-radius: 100px;
            object-fit: cover;
            width: 75px;
            height: 75px;
            transition: transform 1s;
        }
        .grid-img2:hover {
            transform: scale(1);
        }
        .grid-container3 {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            padding: 30px 10%;
        }
        .grid-item3 {
            border: 0px solid #ddd;
            border-radius: 15px;
            overflow: hidden;
            text-align: center;
            transition-duration: 0.5s;
            cursor: pointer;
        }
        .grid-item3:hover {
            background-color: #0d47a1;
        }
        .grid-title3 {
            padding: 5px;
            color: black;
            transition-duration: 0.5s;
        }
        .grid-title3:hover {
            color: white;
        }
        .error {
            color: red;
            text-align: center;
            padding: 10px;
        }
    </style>
    <script>
        // تمرير حالة تسجيل الدخول من PHP إلى JavaScript
        const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;

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

        function openSigninPage() {
            window.location.href = `login.php`;
        }

        function openProfilePage() {
            window.location.href = `profile.php`;
        }

        function openHomePage() {
            window.location.href = `index.php`;
        }

        function openDoctorPage(doctorId) {
            window.location.href = `doctor_details.php?id=${doctorId}`;
        }

        function openLaboratoryPage(labId) {
            window.location.href = `lab_details.php?id=${labId}`;
        }

        function openClinicPage(clinicId) {
            window.location.href = `clinic_details.php?id=${clinicId}`;
        }

        function openImagingPage(imagingId) {
            window.location.href = `imaging_details.php?id=${imagingId}`;
        }

        function openCityPage(cityName) {
            window.location.href = `filtered_doctors.php?city=${encodeURIComponent(cityName)}`;
        }

        function openSpecialtyPage(specialtyName) {
            window.location.href = `filtered_doctors.php?specialty=${encodeURIComponent(specialtyName)}`;
        }

        function handleSearch() {
            const searchInput = document.querySelector('.search-box').value.trim();
            if (searchInput) {
                window.location.href = `search_results.php?q=${encodeURIComponent(searchInput)}`;
            } else {
                alert('لطفاً كلمة للبحث أدخل');
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            updateLoginButton();
            // ربط زر البحث بدالة handleSearch
            document.querySelector('.search-container .login-btn').addEventListener('click', handleSearch);
        });
    </script>
</head>
<body>
    <header class="header">
        <a href="http://matab.rf.gd/index.php">
            <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
        </a>
        <h4 class="title">مطب - با ما، درمان نزدیک‌تر از همیشه</h4>
        <button id="authButton" class="login-btn"></button>
    </header>
    <div class="search-container">
        <input type="text" class="search-box" placeholder="نام پزشک، تخصص، بیماری، مرکز درمانی..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button class="login-btn">جستجو</button>
    </div>
    <h2 class="grid-title2">نتائج البحث لـ "<?php echo htmlspecialchars($search_query); ?>"</h2>

    <?php if (empty($results['laboratories']) && empty($results['clinics']) && empty($results['imaging_centers']) && empty($results['doctors']) && empty($results['cities']) && empty($results['specialties'])): ?>
        <p class="error">هیچ نتیجه‌ای برای جستجوی شما یافت نشد</p>
    <?php else: ?>
        <!-- المختبرات -->
        <?php if (!empty($results['laboratories'])): ?>
            <h2 class="grid-title2">آزمایشگاه‌ها</h2>
            <div class="grid-container">
                <?php foreach ($results['laboratories'] as $lab): ?>
                    <div class="grid-item" onclick="openLaboratoryPage(<?php echo $lab['id']; ?>)">
                        <img src="<?php echo $lab['image'] ?: 'https://via.placeholder.com/150'; ?>" alt="<?php echo htmlspecialchars($lab['name']); ?>" class="grid-img">
                        <h3 class="grid-title2"><?php echo htmlspecialchars($lab['name']); ?></h3>
                        <p class="grid-title"><?php echo htmlspecialchars($lab['description'] ?: 'بدون توضیحات'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- العيادات -->
        <?php if (!empty($results['clinics'])): ?>
            <h2 class="grid-title2">کلینیک‌ها</h2>
            <div class="grid-container">
                <?php foreach ($results['clinics'] as $clinic): ?>
                    <div class="grid-item" onclick="openClinicPage(<?php echo $clinic['id']; ?>)">
                        <img src="<?php echo $clinic['image'] ?: 'https://via.placeholder.com/150'; ?>" alt="<?php echo htmlspecialchars($clinic['name']); ?>" class="grid-img">
                        <h3 class="grid-title2"><?php echo htmlspecialchars($clinic['name']); ?></h3>
                        <p class="grid-title"><?php echo htmlspecialchars($clinic['description'] ?: 'بدون توضیحات'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- مراكز التصوير -->
        <?php if (!empty($results['imaging_centers'])): ?>
            <h2 class="grid-title2">مراکز تصویربرداری</h2>
            <div class="grid-container">
                <?php foreach ($results['imaging_centers'] as $imaging): ?>
                    <div class="grid-item" onclick="openImagingPage(<?php echo $imaging['id']; ?>)">
                        <img src="<?php echo $imaging['image'] ?: 'https://via.placeholder.com/150'; ?>" alt="<?php echo htmlspecialchars($imaging['name']); ?>" class="grid-img">
                        <h3 class="grid-title2"><?php echo htmlspecialchars($imaging['name']); ?></h3>
                        <p class="grid-title"><?php echo htmlspecialchars($imaging['description'] ?: 'بدون توضیحات'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- الأطباء -->
        <?php if (!empty($results['doctors'])): ?>
            <h2 class="grid-title2">پزشکان</h2>
            <div class="grid-container">
                <?php foreach ($results['doctors'] as $doctor): ?>
                    <div class="grid-item" onclick="openDoctorPage(<?php echo $doctor['id']; ?>)">
                        <img src="<?php echo $doctor['image'] ?: 'https://via.placeholder.com/150'; ?>" alt="<?php echo htmlspecialchars($doctor['name']); ?>" class="grid-img">
                        <h3 class="grid-title2"><?php echo htmlspecialchars($doctor['name']); ?></h3>
                        <p class="grid-title"><?php echo htmlspecialchars($doctor['specialty']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- التخصصات -->
        <?php if (!empty($results['specialties'])): ?>
            <h2 class="grid-title2">تخصص‌ها</h2>
            <div class="grid-container2">
                <?php foreach ($results['specialties'] as $specialty): ?>
                    <div class="grid-item2" onclick="openSpecialtyPage('<?php echo htmlspecialchars($specialty['name']); ?>')">
                        <img src="<?php echo $specialty['image'] ?: 'https://via.placeholder.com/75'; ?>" class="grid-img2">
                        <div class="grid-title2"><?php echo htmlspecialchars($specialty['name']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- المدن -->
        <?php if (!empty($results['cities'])): ?>
            <h2 class="grid-title2">شهرها</h2>
            <div class="grid-container3">
                <?php foreach ($results['cities'] as $city): ?>
                    <div class="grid-item3" onclick="openCityPage('<?php echo htmlspecialchars($city['name']); ?>')">
                        <div class="grid-title3"><?php echo htmlspecialchars($city['name']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>