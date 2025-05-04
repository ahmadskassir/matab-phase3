<?php
header('Content-Type: application/json; charset=utf-8');
require 'db_connect.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['user_id'])) {
    echo json_encode(['error' => 'معرف المستخدم غير متوفر']);
    exit();
}

$user_id = intval($input['user_id']);
$name = $input['name'] ?? '';
$national_id = $input['national_id'] ?? '';
$insurance_number = $input['insurance_number'] ?? '';
$email = $input['email'] ?? '';

$sql = "UPDATE users SET username = ?, email = ?, national_id = ?, insurance_number = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $email, $national_id, $insurance_number, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'فشل تحديث الملف الشخصي: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>