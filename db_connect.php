<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'sql111.infinityfree.com';
$username = 'if0_38896706';
$password = '9UEI7iubd2JscFr';
$database = 'if0_38896706_Matab_uni_isfahan';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    header('Content-Type: application/json; charset=utf-8');
    die(json_encode(['error' => 'خطا در اتصال به پایگاه داده: ' . $conn->connect_error], JSON_UNESCAPED_UNICODE));
}

// ضبط ترميز الاتصال لدعم الفارسية
$conn->set_charset("utf8mb4");
?>