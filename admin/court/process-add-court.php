<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';

// Lấy dữ liệu từ form
$court_code    = trim($_POST['court_code'] ?? '');
$court_name    = trim($_POST['court_name'] ?? '');
$category_id   = intval($_POST['category_id'] ?? 0);
$area          = trim($_POST['area'] ?? '');
$surface_type  = trim($_POST['surface_type'] ?? '');
$capacity      = intval($_POST['capacity'] ?? 0);
$open_time     = trim($_POST['open_time'] ?? '06:00');
$close_time    = trim($_POST['close_time'] ?? '22:00');
$status        = trim($_POST['status'] ?? 'trong');
$is_active     = 1; // Mặc định luôn là hoạt động khi tạo mới
$facilities    = trim($_POST['facilities'] ?? '');

// Xử lý upload ảnh
$image_url = '';
if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "../../uploads/courts/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . '_' . basename($_FILES["image_url"]["name"]);
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFile)) {
        $image_url = "uploads/courts/" . $fileName;
    }
}

// Validate
$errors = [];
if ($court_code === '') $errors[] = 'Mã sân không được để trống.';
if ($court_name === '') $errors[] = 'Tên sân không được để trống.';
if ($category_id <= 0) $errors[] = 'Vui lòng chọn loại sân.';

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header('Location: add-court.php');
    exit;
}

// Xử lý facilities: chuyển đổi chuỗi thành JSON
if ($facilities !== '') {
    $test = json_decode($facilities);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $arr = array_map('trim', explode(',', $facilities));
        $arr = array_filter($arr, function($item) { return $item !== ''; });
        $facilities = json_encode(array_values($arr), JSON_UNESCAPED_UNICODE);
    }
} else {
    $facilities = json_encode([]);
}

// Lưu vào DB
try {
    $stmt = $conn->prepare("INSERT INTO courts (court_code, court_name, category_id, area, surface_type, capacity, open_time, close_time, status, is_active, image_url, facilities) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssississsiss",
        $court_code,
        $court_name,
        $category_id,
        $area,
        $surface_type,
        $capacity,
        $open_time,
        $close_time,
        $status,
        $is_active,
        $image_url,
        $facilities
    );
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = 'Thêm sân thành công!';
    header('Location: list-court.php');
    exit;
} catch (Exception $e) {
    $_SESSION['errors'] = ['Lỗi lưu dữ liệu: ' . $e->getMessage()];
    $_SESSION['old'] = $_POST;
    header('Location: add-court.php');
    exit;
}


// Xử lý upload ảnh
$image_url = '';
if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK) {
    $targetDir = "../../uploads/courts/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . '_' . basename($_FILES["image_url"]["name"]);
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFile)) {
        $image_url = "uploads/courts/" . $fileName;
    }
}