<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';

$court_id = intval($_GET['court_id'] ?? 0);
$date = $_GET['date'] ?? '';
$start_time = $_GET['start_time'] ?? '';
$end_time = $_GET['end_time'] ?? '';

if ($court_id <= 0 || !$date || !$start_time || !$end_time) {
    echo json_encode(['available' => false, 'message' => 'Thông tin không đầy đủ']);
    exit;
}

$stmt = $conn->prepare("
    SELECT booking_code
    FROM bookings
    WHERE court_id = ?
    AND booking_date = ?
    AND status NOT IN ('da_huy', 'tu_choi')
    AND start_time < ?
    AND end_time > ?
");
$stmt->bind_param("isss", $court_id, $date, $end_time, $start_time);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['available' => false, 'message' => 'Sân đã được đặt trong khoảng thời gian này.']);
} else {
    echo json_encode(['available' => true]);
}

$stmt->close();
$conn->close();
