<?php
    session_start();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ../auth/login.php');
        exit;
    }
    require_once __DIR__ . '/../../config/connect.php';

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id > 0) {
        try {
            // Xóa user
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Xóa người dùng thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa người dùng!';
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1451) {
                $_SESSION['error'] = 'Không thể xóa người dùng này vì họ đang có các dữ liệu liên quan trong hệ thống (lịch đặt sân, tin tức, thông báo, v.v.)!';
            } else {
                $_SESSION['error'] = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = 'ID người dùng không hợp lệ!';
    }
    header('Location: list-user.php');
    exit;
?>