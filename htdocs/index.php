<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <link rel="icon" type="image/x-icon" href="https://i.postimg.cc/J0dCfhLH/1111.jpg">
    <link href="https://fonts.googleapis.com/css2?family=Almarai&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مطب</title>
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
        .login-btn{
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
        .login-btn:hover {
            background: #0d47a1;
        }
        .login-btn2, .profile-btn {
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
        .login-btn2:hover, .profile-btn:hover {
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
        .image-gallery {
            display: flex;
            justify-content: space-between;
            padding: 30px 15%;
            text-align: center;
            color: #5d5d5d;
        }
        .image-gallery2 {
            display: flex;
            justify-content: space-between;
            padding: 30px 15%;
            text-align: center;
            color: #5d5d5d;
        }
        .gallery-img {
            width: 75px;
            height: 75px;
            object-fit: cover;
            transition: transform 1s;
        }
        .gallery-img:hover {
            transform: scale(1.1);
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
        .grid-title3 {
            padding: 5px;
            color: black;
            transition-duration: 0.5s;
        }
        .grid-title3:hover {
            color: white;
        }
        .B {
            color: black;
        }
        .error {
            color: red;
            text-align: center;
            padding: 10px;
        }
    </style>
    <script>
        const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;

        function updateLoginButton() {
            console.log('تنفيذ updateLoginButton، حالة تسجيل الدخول:', isLoggedIn); // للتصحيح
            const button = document.getElementById('authButton');
            if (isLoggedIn) {
                button.className = 'profile-btn';
                button.textContent = 'پروفایل';
                button.onclick = openProfilePage;
            } else {
                button.className = 'login-btn2';
                button.textContent = 'ورود';
                button.onclick = openSigninPage;
            }
        }

        async function loadDoctors() {
    console.log('جلب الأطباء من: get_doctors.php');
    try {
        
        const responseDefault = await fetch('get_doctors.php', { method: 'GET', headers: { 'Accept': 'application/json' } });
        if (!responseDefault.ok) {
            throw new Error(`خطای شبکه: ${responseDefault.status} ${responseDefault.statusText}`);
        }
        const defaultDoctors = await responseDefault.json();
        const firstEightDoctors = defaultDoctors.slice(0, 8);

        
        const responseTop = await fetch('get_top_doctors_by_bookings.php', { method: 'GET', headers: { 'Accept': 'application/json' } });
        if (!responseTop.ok) {
            throw new Error(`خطای شبکه: ${responseTop.status} ${responseTop.statusText}`);
        }
        const topDoctors = await responseTop.json();

        
        const topDoctorsFiltered = topDoctors.filter(doctor => !firstEightDoctors.some(firstDoctor => firstDoctor.id === doctor.id)).slice(0, 8);

        
        const doctorsToDisplay = [...firstEightDoctors, ...topDoctorsFiltered];

        const doctorsContainer = document.getElementById("doctorsContainer");
        doctorsContainer.innerHTML = '';
        if (doctorsToDisplay.length > 0) {
            doctorsToDisplay.forEach(doctor => {
                const card = document.createElement('div');
                card.className = 'grid-item';
                card.dataset.id = doctor.id;
                card.dataset.name = doctor.name;
                card.innerHTML = `
                    <img src="${doctor.image}" 
                         alt="${doctor.name}" 
                         class="grid-img"
                         onerror="this.src='https://via.placeholder.com/150'">
                    <h3 class="grid-title2">${doctor.name}</h3>
                    <p class="grid-title">${doctor.specialty}</p>
                `;
                card.addEventListener('click', () => {
                    openDoctorPage(doctor.id);
                });
                doctorsContainer.appendChild(card);
            });
        } else {
            doctorsContainer.innerHTML = '<p>هیچ پزشکی موجود نیست</p>';
        }
    } catch (error) {
        console.error("خطا در بارگذاری پزشکان:", error);
        document.getElementById("doctorsContainer").innerHTML = `
            <p class="error">خطا در بارگذاری پزشکان: ${error.message || 'خطای ناشناخته'}</p>
        `;
    }
}

        async function loadCities() {
            console.log('جلب المدن');
            try {
                const response = await fetch('get_cities.php', { method: 'GET', headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error(`خطای شبکه: ${response.status} ${response.statusText}`);
                }
                const cities = await response.json();
                console.log('استجابة المدن:', cities);
                const citysContainer = document.getElementById("citysContainer");
                citysContainer.innerHTML = '';
                if (Array.isArray(cities) && cities.length > 0) {
                    cities.forEach(city => {
                        const cityDiv = document.createElement('div');
                        cityDiv.className = 'grid-item3';
                        cityDiv.dataset.id = city.id;
                        cityDiv.dataset.name = city.name;
                        cityDiv.innerHTML = `<div class="grid-title3">${city.name}</div>`;
                        cityDiv.addEventListener('click', () => {
                            console.log('النقر على المدينة:', city.name);
                            window.location.href = `filtered_doctors.php?city=${encodeURIComponent(city.name)}`;
                        });
                        citysContainer.appendChild(cityDiv);
                    });
                } else {
                    citysContainer.innerHTML = '<p>هیچ شهری موجود نیست</p>';
                }
            } catch (error) {
                console.error("خطا در بارگذاری شهرها:", error);
                document.getElementById("citysContainer").innerHTML = `
                    <p class="error">خطا در بارگذاری شهرها: ${error.message || 'خطای ناشناخته'}</p>
                `;
            }
        }

        async function loadSpecialties() {
            console.log('جلب التخصصات');
            try {
                const response = await fetch('get_specialties.php', { method: 'GET', headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error(`خطای شبکه: ${response.status} ${response.statusText}`);
                }
                const specialties = await response.json();
                console.log('استجابة التخصصات:', specialties);
                const SpecialtiesContainer = document.getElementById("SpecialtiesContainer");
                SpecialtiesContainer.innerHTML = '';
                if (Array.isArray(specialties) && specialties.length > 0) {
                    specialties.forEach(specialty => {
                        const specialtyDiv = document.createElement('div');
                        specialtyDiv.className = 'grid-item2';
                        specialtyDiv.dataset.id = specialty.id;
                        specialtyDiv.dataset.name = specialty.name;
                        specialtyDiv.innerHTML = `
                            <img src="${specialty.image}" class="grid-img2" onerror="this.src='https://via.placeholder.com/75'">
                            <div class="grid-title2">${specialty.name}</div>
                        `;
                        specialtyDiv.addEventListener('click', () => {
                            console.log('النقر على التخصص:', specialty.name);
                            window.location.href = `filtered_doctors.php?specialty=${encodeURIComponent(specialty.name)}`;
                        });
                        SpecialtiesContainer.appendChild(specialtyDiv);
                    });
                } else {
                    SpecialtiesContainer.innerHTML = '<p>هیچ تخصصی موجود نیست</p>';
                }
            } catch (error) {
                console.error("خطا در بارگذاری تخصص‌ها:", error);
                document.getElementById("SpecialtiesContainer").innerHTML = `
                    <p class="error">خطا در بارگذاری تخصص‌ها: ${error.message || 'خطای ناشناخته'}</p>
                `;
            }
        }

        function openDoctorPage(doctorId) {
            console.log('فتح صفحة الطبيب:', doctorId);
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

        function openClinicPage() {
            window.location.href = `clinic.php`;
        }

        function openImagingPage() {
            window.location.href = `imaging.php`;
        }

        function openLaboratoryPage() {
            window.location.href = `laboratory.php`;
        }

        function handleSearch() {
            const searchInput = document.querySelector('.search-box').value.trim();
            if (searchInput) {
                window.location.href = `search_results.php?q=${encodeURIComponent(searchInput)}`;
            } else {
                alert('لطفاً كلمة للبحث أدخل');
            }
        }

        async function loadAllData() {
            console.log('بدء تحميل جميع البيانات');
            try {
                await Promise.all([
                    loadDoctors(),
                    loadCities(),
                    loadSpecialties()
                ]);
                console.log('تم تحميل جميع البيانات بنجاح');
            } catch (error) {
                console.error("خطا في تحميل البيانات:", error);
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            updateLoginButton();
            loadAllData();
      
            document.querySelector('.login-btn').addEventListener('click', handleSearch);
        });
    </script>
</head>
<body>
    <header class="header">
        <a href="http://matab.rf.gd/index.php">
            <img src="https://i.postimg.cc/J0dCfhLH/1111.jpg" alt="لوگو" class="logo">
        </a>
        <h4 class="title">مطب - با ما، درمان نزدیک‌تر از همیشه</h4>
        <button id="authButton" class="login-btn2"></button>
    </header>
    <div class="search-container">
        <input type="text" class="search-box" placeholder="نام پزشک، تخصص، بیماری، مرکز درمانی...">
        <button class="login-btn">جستجو</button>
    </div>
    <div class="image-gallery">
        <img src="https://i.postimg.cc/5yF47ks4/images-removebg-preview.png" alt="آزمایشگاه" class="gallery-img" onclick="openLaboratoryPage()">
        <img src="https://cdn-icons-png.flaticon.com/512/5717/5717514.png" alt="کلینیک" class="gallery-img" onclick="openClinicPage()">
        <img src="https://i.postimg.cc/mrX1KJyC/images-1-removebg-preview.png" alt="مرکز تصویربرداری" class="gallery-img" onclick="openImagingPage()">
    </div>
    <div class="image-gallery">
        <h4 class="B">آزمایشگاه</h4>
        <h4 class="B">کلینیک</h4>
        <h4 class="B">تصویربرداری</h4>
    </div>
    <h2 class="grid-title2">تخصص‌ها</h2>
    <div class="grid-container2" id="SpecialtiesContainer"></div>
    <h2 class="grid-title2">شهرها</h2>
    <div class="grid-container3" id="citysContainer"></div>
    <h2 class="grid-title2">برترین پزشکان</h2>
    <div class="grid-container" id="doctorsContainer"></div>
</body>
</html>