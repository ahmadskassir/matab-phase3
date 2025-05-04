<?php
require 'db_connect.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT d.id, d.name, d.specialty, d.image, COUNT(b.id) as booking_count
            FROM doctors d
            LEFT JOIN doctor_appointments b ON d.id = b.doctor_id AND b.status = 'booked'
            GROUP BY d.id, d.name, d.specialty, d.image
            ORDER BY booking_count DESC, d.id ASC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("خطأ في تحضير الاستعلام: " . $conn->error);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'specialty' => $row['specialty'],
            'image' => $row['image']
        ];
    }

    echo json_encode($doctors);
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'خطأ في جلب الأطباء: ' . $e->getMessage()]);
}

$conn->close();
?>