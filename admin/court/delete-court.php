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
        // Kiểm tra xem sân có tồn tại không
        $checkStmt = $conn->prepare("SELECT court_name FROM courts WHERE id = ?");
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $court = $result->fetch_assoc();
            $checkStmt->close();

            // Xóa sân
            $stmt = $conn->prepare("DELETE FROM courts WHERE id = ?");
            $stmt->bind_param('i', $id);

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Xóa sân "' . htmlspecialchars($court['court_name']) . '" thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra khi xóa sân!';
            }
            $stmt->close();
        } else {
            $checkStmt->close();
            $_SESSION['error'] = 'Sân không tồn tại!';
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1451) {
            $_SESSION['error'] = 'Không thể xóa sân này vì sân đang có các lịch đặt sân liên quan!';
        } else {
            $_SESSION['error'] = 'Lỗi cơ sở dữ liệu: ' . $e->getMessage();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = 'Lỗi hệ thống: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'ID sân không hợp lệ!';
}

header('Location: list-court.php');
exit;
?>