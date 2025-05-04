<?php
header('Content-Type: application/json; charset=utf-8');
require 'db_connect.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'معرف المستخدم غير متوفر']);
    exit();
}

$user_id = intval($_GET['user_id']);
$sql = "SELECT username AS name, email, national_id, insurance_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'المستخدم غير موجود']);
}

$stmt->close();
$conn->close();
?><?php
header('Content-Type: application/json; charset=utf-8');
require 'db_connect.php';

if (!isset($_GET['user_id'])) {
    echo json_encode(['error' => 'معرف المستخدم غير متوفر']);
    exit();
}

$user_id = intval($_GET['user_id']);
$sql = "SELECT username AS name, email, national_id, insurance_number FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    echo json_encode($user);
} else {
    echo json_encode(['error' => 'المستخدم غير موجود']);
}

$stmt->close();
$conn->close();
?>