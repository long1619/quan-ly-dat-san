<?php
session_start();
require_once __DIR__ . '/../../config/connect.php'; // Kết nối trả về biến $conn (MySQLi)

// Lấy dữ liệu từ form
$category_name = trim($_POST['category_name'] ?? '');
$description = trim($_POST['description'] ?? '');

// Validate
$errors = [];
if ($category_name === '') {
    $errors[] = 'Tên Loại sân không được để trống.';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header('Location: add-type-court.php');
    exit;
}

// Lưu vào DB bằng MySQLi
try {
    // Chuẩn bị câu lệnh
    $stmt = $conn->prepare("INSERT INTO court_categories (category_name, description, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $category_name, $description);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = 'Thêm Loại sân thành công!';
    header('Location: list-type-court.php');
    exit;
} catch (Exception $e) {
    $_SESSION['errors'] = ['Lỗi lưu dữ liệu: ' . $e->getMessage()];
    $_SESSION['old'] = $_POST;
    header('Location: add-type-court.php');
    exit;
}