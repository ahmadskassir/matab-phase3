<?php
session_start();
require 'db_connect.php';

$sql = "SELECT i.id, i.name, i.image, i.address, i.description, c.name as city_name 
        FROM imaging_centers i 
        JOIN cities c ON i.city_id = c.id
        ORDER BY c.name, i.name";
$result = $conn->query($sql);

$imaging_by_city = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $city = $row['city_name'];
        if (!isset($imaging_by_city[$city])) {
            $imaging_by_city[$city] = [];
        }
        $imaging_by_city[$city][] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مطب - مراکز تصویربرداری</title>
    <link rel="icon" type="image/x-icon" href="https://i.postimg.cc/J0dCfhLH/1111.jpg">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f4f4f4;
            width: 100%;
            min-height: 100vh;
            direction: rtl;
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
        }
        .title {
            color: white;
            flex-grow: 1;
            text-align: center;
            margin: 0;
        }
        .login-btn {
            margin-left: auto;
            padding: 10px 20px;
            background: #1a237e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .login-btn:hover {
            background: #0d47a1;
        }
        .container {
            width: 100%;
            padding: 2rem 0;
            display: flex;
            justify-content: center;
        }
        .clinics-grid-wrapper {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            padding: 0 1rem;
        }
        .city-section {
            margin-bottom: 3rem;
        }
        .city-title {
            color: #1a237e;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: right;
        }
        .clinics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        .clinics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        .clinic-card {
            width: 100%;
            background: #fff;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: box-shadow 0.2s ease;
            cursor: pointer;
        }
        .clinic-card:hover {
            box-shadow: 0px 2px 4px 2.5px #0d47a1;
        }
        .clinic-image {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 90px;
            border: 2px solid #0d47a1;
            display: block;
            margin: 1.5rem auto 0;
        }
        .clinic-details {
            padding: 1.5rem;
            text-align: center;
        }
        .clinic-name {
            color: #1a237e;
            margin-bottom: 0.5rem;
        }
        .bio {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
        }
        .clinic-address {
            color: #444;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        .book-btn {
            background: #1a237e;
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
        }
        .book-btn:hover {
            background: #0d47a1;
        }
        .error {
            color: red;
            text-align: center;
            padding: 10px;
        }
    </style>
    <script>
        function openImagingDetails(imagingId) {
            window.location.href = `imaging_details.php?id=${imagingId}`;
        }
    </script>
</head>
<body>
    <header class="header">
        <a href="index.php">
            <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
        </a>
        <h4 class="title">مطب - با ما، درمان نزدیک‌تر از همیشه</h4>
        <a href="<?php echo isset($_SESSION['user_id']) ? 'profile.php' : 'login.php'; ?>">
            <button class="login-btn"><?php echo isset($_SESSION['user_id']) ? 'پروفایل' : 'ورود'; ?></button>
        </a>
    </header>
    <div class="container">
        <div class="clinics-grid-wrapper">
            <?php if (!empty($imaging_by_city)): ?>
                <?php foreach ($imaging_by_city as $city => $centers): ?>
                    <div class="city-section">
                        <h2 class="city-title"><?php echo htmlspecialchars($city); ?></h2>
                        <div class="clinics-grid">
                            <?php foreach ($centers as $center): ?>
                                <div class="clinic-card" 
                                     data-id="<?php echo $center['id']; ?>"
                                     data-name="<?php echo $center['name']; ?>"
                                     onclick="openImagingDetails(<?php echo $center['id']; ?>)">
                                    <div class="clinic-details">
                                        <img src="<?php echo $center['image'] ?: 'placeholder.jpg'; ?>" class="clinic-image" alt="<?php echo $center['name']; ?>">
                                        <h2 class="clinic-name"><?php echo $center['name']; ?></h2>
                                        <h4 class="bio"><?php echo $center['description'] ?: 'بدون توضیحات'; ?></h4>
                                        <h3 class="clinic-address"><?php echo $center['address']; ?></h3>
                                        <button class="book-btn" onclick="openImagingDetails(<?php echo $center['id']; ?>)">مشاهده جزئیات و رزرو</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="error">هیچ مرکز تصویربرداری یافت نشد</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>