<?php
require 'db_connect.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT id, name, specialty, image FROM doctors";
    $stmt = $conn->prepare($sql);
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