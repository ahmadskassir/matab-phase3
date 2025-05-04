<?php
// تعطيل إشعارات PHP لتجنب إخراج إضافي
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);

// تعيين رأس JSON مع ترميز UTF-8
header('Content-Type: application/json; charset=utf-8');

// تضمين ملف الاتصال بقاعدة البيانات
require 'db_connect.php';

// التحقق من وجود user_id
if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'معرف المستخدم غير متوفر'], JSON_UNESCAPED_UNICODE);
    exit();
}

// تنظيف وتحويل user_id إلى عدد صحيح
$user_id = intval($_GET['user_id']);

// استعلام قاعدة البيانات
$sql = "SELECT username AS name, email, national_id, insurance_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// إرسال الاستجابة
if ($user) {
    echo json_encode($user, JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['error' => 'المستخدم غير موجود'], JSON_UNESCAPED_UNICODE);
}

// إغلاق الموارد
$stmt->close();
$conn->close();
?>