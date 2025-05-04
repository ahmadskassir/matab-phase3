<?php
session_start();
require 'db_connect.php';

// جلب قائمة المدن من قاعدة البيانات
$sql = "SELECT id, name FROM cities ORDER BY name";
$result = $conn->query($sql);
$cities = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cities[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مطب - پنل مدیریت</title>
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
        .container {
            width: 100%;
            padding: 2rem;
            display: flex;
            justify-content: center;
        }
        .admin-container {
            max-width: 800px;
            width: 100%;
            background: #fff;
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #1a237e;
            margin-bottom: 20px;
            text-align: right;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: right;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #444;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            margin-top: 6px;
            font-size: 0.9rem;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .admin-btn {
            background: #1a237e;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            margin-top: 10px;
            transition: background 0.3s ease;
        }
        .admin-btn:hover {
            background: #0d47a1;
        }
        .section {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="index.php">
            <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
        </a>
        <h4 class="title">مطب - با ما، درمان نزدیک‌تر از همیشه</h4>
    </header>
    <div class="container">
        <div class="admin-container">
            <!-- فرم افزودن پزشک -->
            <div class="section">
                <h2>افزودن پزشک</h2>
                <form action="add_doctor.php" method="post">
                    <div class="form-group">
                        <label for="doctor_name">نام کامل پزشک</label>
                        <input type="text" name="doctor_name" id="doctor_name" placeholder="نام پزشک" required>
                    </div>
                    <div class="form-group">
                        <label for="specialty">تخصص</label>
                        <input type="text" name="specialty" id="specialty" placeholder="تخصص" required>
                    </div>
                    <div class="form-group">
                        <label for="examination_fee">هزینه ویزیت</label>
                        <input type="number" name="examination_fee" id="examination_fee" placeholder="هزینه ویزیت (ریال)" required>
                    </div>
                    <div class="form-group">
                        <label for="clinic_address">آدرس کلینیک</label>
                        <input type="text" name="clinic_address" id="clinic_address" placeholder="آدرس کلینیک" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_image">لینک تصویر پزشک</label>
                        <input type="url" name="doctor_image" id="doctor_image" placeholder="لینک تصویر" required>
                    </div>
                    <button type="submit" class="admin-btn">افزودن پزشک</button>
                </form>
            </div>

            <!-- فرم افزودن تخصص -->
            <div class="section">
                <h2>افزودن تخصص</h2>
                <form action="add_specialty.php" method="post">
                    <div class="form-group">
                        <label for="specialty_name">نام تخصص</label>
                        <input type="text" name="specialty_name" id="specialty_name" placeholder="نام تخصص" required>
                    </div>
                    <div class="form-group">
                        <label for="specialty_image">لینک تصویر تخصص</label>
                        <input type="url" name="specialty_image" id="specialty_image" placeholder="لینک تصویر" required>
                    </div>
                    <button type="submit" class="admin-btn">افزودن تخصص</button>
                </form>
            </div>

            <!-- فرم افزودن شهر -->
            <div class="section">
                <h2>افزودن شهر</h2>
                <form action="add_city.php" method="post">
                    <div class="form-group">
                        <label for="city_name">نام شهر</label>
                        <input type="text" name="city_name" id="city_name" placeholder="نام شهر" required>
                    </div>
                    <button type="submit" class="admin-btn">افزودن شهر</button>
                </form>
            </div>

            <!-- فرم افزودن آزمایشگاه -->
            <div class="section">
                <h2>افزودن آزمایشگاه</h2>
                <form action="add_laboratory.php" method="post">
                    <div class="form-group">
                        <label for="lab_name">نام آزمایشگاه</label>
                        <input type="text" name="lab_name" id="lab_name" placeholder="نام آزمایشگاه" required>
                    </div>
                    <div class="form-group">
                        <label for="lab_image">لینک تصویر آزمایشگاه</label>
                        <input type="url" name="lab_image" id="lab_image" placeholder="لینک تصویر" required>
                    </div>
                    <div class="form-group">
                        <label for="lab_address">آدرس آزمایشگاه</label>
                        <input type="text" name="lab_address" id="lab_address" placeholder="آدرس آزمایشگاه" required>
                    </div>
                    <div class="form-group">
                        <label for="lab_description">توضیحات</label>
                        <textarea name="lab_description" id="lab_description" placeholder="توضیحات"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="lab_city">شهر</label>
                        <select name="lab_city" id="lab_city" required>
                            <option value="">انتخاب شهر</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city['id']; ?>"><?php echo htmlspecialchars($city['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="admin-btn">افزودن آزمایشگاه</button>
                </form>
            </div>

            <!-- فرم افزودن کلینیک -->
            <div class="section">
                <h2>افزودن کلینیک</h2>
                <form action="add_clinic.php" method="post">
                    <div class="form-group">
                        <label for="clinic_name">نام کلینیک</label>
                        <input type="text" name="clinic_name" id="clinic_name" placeholder="نام کلینیک" required>
                    </div>
                    <div class="form-group">
                        <label for="clinic_image">لینک تصویر کلینیک</label>
                        <input type="url" name="clinic_image" id="clinic_image" placeholder="لینک تصویر" required>
                    </div>
                    <div class="form-group">
                        <label for="clinic_address">آدرس کلینیک</label>
                        <input type="text" name="clinic_address" id="clinic_address" placeholder="آدرس کلینیک" required>
                    </div>
                    <div class="form-group">
                        <label for="clinic_description">توضیحات</label>
                        <textarea name="clinic_description" id="clinic_description" placeholder="توضیحات"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="clinic_city">شهر</label>
                        <select name="clinic_city" id="clinic_city" required>
                            <option value="">انتخاب شهر</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city['id']; ?>"><?php echo htmlspecialchars($city['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="admin-btn">افزودن کلینیک</button>
                </form>
            </div>

            <!-- فرم افزودن مرکز تصویربرداری -->
            <div class="section">
                <h2>افزودن مرکز تصویربرداری</h2>
                <form action="add_imaging_center.php" method="post">
                    <div class="form-group">
                        <label for="imaging_name">نام مرکز</label>
                        <input type="text" name="imaging_name" id="imaging_name" placeholder="نام مرکز" required>
                    </div>
                    <div class="form-group">
                        <label for="imaging_image">لینک تصویر مرکز</label>
                        <input type="url" name="imaging_image" id="imaging_image" placeholder="لینک تصویر" required>
                    </div>
                    <div class="form-group">
                        <label for="imaging_address">آدرس مرکز</label>
                        <input type="text" name="imaging_address" id="imaging_address" placeholder="آدرس مرکز" required>
                    </div>
                    <div class="form-group">
                        <label for="imaging_description">توضیحات</label>
                        <textarea name="imaging_description" id="imaging_description" placeholder="توضیحات"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="imaging_city">شهر</label>
                        <select name="imaging_city" id="imaging_city" required>
                            <option value="">انتخاب شهر</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo $city['id']; ?>"><?php echo htmlspecialchars($city['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="admin-btn">افزودن مرکز تصویربرداری</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>