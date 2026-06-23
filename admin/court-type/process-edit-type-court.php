<?php
session_start();
require_once __DIR__ . '/../../config/connect.php';

// Lấy dữ liệu từ form
$id = intval($_POST['id'] ?? 0);
$category_name = trim($_POST['category_name'] ?? '');
$description = trim($_POST['description'] ?? '');

// Validate
$errors = [];
if ($category_name === '') {
    $errors[] = 'Tên Loại sân không được để trống.';
}

if ($id <= 0) {
    $errors[] = 'ID Loại sân không hợp lệ.';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $_POST;
    header('Location: edit-type-court.php?id=' . $id);
    exit;
}

// Cập nhật vào DB
try {
    $stmt = $conn->prepare("UPDATE court_categories SET category_name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $category_name, $description, $id);
    $stmt->execute();
    $stmt->close();

    $_SESSION['success'] = 'Cập nhật Loại sân thành công!';
    header('Location: list-type-court.php');
    exit;
} catch (Exception $e) {
    $_SESSION['errors'] = ['Lỗi lưu dữ liệu: ' . $e->getMessage()];
    $_SESSION['old'] = $_POST;
    header('Location: edit-type-court.php?id=' . $id);
    exit;
}