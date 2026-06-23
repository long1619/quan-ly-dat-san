<?php
/**
 * Lấy dữ liệu từ database để cung cấp context cho AI
 */

function getDatabaseContext($conn, $query) {
    $context = [];

    // Phân tích câu hỏi để quyết định query gì
    $queryLower = mb_strtolower($query, 'UTF-8');

    // 1. Thông tin về sân
    if (containsKeywords($queryLower, ['sân', 'court', 'trống', 'available', 'sức chứa', 'capacity'])) {
        $context['rooms'] = getRoomsData($conn, $queryLower);
        $context['room_types'] = getRoomTypes($conn);
    }

    // 2. Thông tin về đặt sân
    if (containsKeywords($queryLower, ['đặt', 'booking', 'lịch', 'schedule', 'đơn', 'chờ duyệt', 'pending'])) {
        $context['bookings'] = getBookingsData($conn, $queryLower);
    }

    // 3. Thống kê tổng quan
    if (containsKeywords($queryLower, ['bao nhiêu', 'số lượng', 'thống kê', 'tổng', 'count'])) {
        $context['statistics'] = getStatistics($conn);
    }

    // 4. Người dùng
    if (containsKeywords($queryLower, ['người dùng', 'user', 'giảng viên', 'sinh viên'])) {
        $context['users'] = getUsersData($conn, $queryLower);
    }

    return $context;
}

/**
 * Kiểm tra câu hỏi có chứa từ khóa không
 */
function containsKeywords($text, $keywords) {
    foreach ($keywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Lấy thông tin sân
 */
function getRoomsData($conn, $query) {
    $sql = "SELECT r.*, rt.category_name, rt.description as type_description
            FROM courts r
            LEFT JOIN court_categories rt ON r.category_id = rt.id
            WHERE 1=1";

    // Thêm điều kiện lọc dựa vào câu hỏi
    if (strpos($query, 'trống') !== false || strpos($query, 'available') !== false) {
        $today = date('Y-m-d');
        if (strpos($query, 'hôm nay') !== false || strpos($query, 'today') !== false) {
            $sql .= " AND r.status != 'bao_tri' AND r.id NOT IN (
                SELECT b.court_id FROM bookings b
                WHERE b.booking_date = '$today'
                AND b.status = 'da_duyet'
                AND (b.start_time <= '22:00:00' AND b.end_time >= '06:00:00')
            )";
        } else {
            $sql .= " AND r.status = 'trong'";
        }
    }

    if (preg_match('/(\d+)\s*(người|person)/', $query, $matches)) {
        $capacity = intval($matches[1]);
        $sql .= " AND r.capacity >= $capacity";
    }

    $sql .= " ORDER BY r.court_code LIMIT 100";

    $result = mysqli_query($conn, $sql);
    $rooms = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rooms[] = [
                'court_code' => $row['court_code'],
                'court_name' => $row['court_name'],
                'capacity' => $row['capacity'],
                'status' => $row['status'],
                'category_name' => $row['category_name'],
                'area' => $row['area'] ?? 'N/A',
                'surface_type' => $row['surface_type'] ?? 'N/A',
                'description' => $row['description'] ?? '',
                'status_text' => getStatusText($row['status'])
            ];
        }
    }

    return $rooms;
}

/**
 * Lấy danh mục sân
 */
function getRoomTypes($conn) {
    $sql = "SELECT * FROM court_categories ORDER BY category_name";
    $result = mysqli_query($conn, $sql);
    $types = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $types[] = [
                'id' => $row['id'],
                'category_name' => $row['category_name'],
                'description' => $row['description'] ?? ''
            ];
        }
    }

    return $types;
}

/**
 * Lấy thông tin đặt sân
 */
function getBookingsData($conn, $query) {
    $sql = "SELECT b.*, r.court_code, r.court_name, u.full_name as user_name, u.email
            FROM bookings b
            JOIN courts r ON b.court_id = r.id
            JOIN users u ON b.user_id = u.id
            WHERE 1=1";

    // Lọc theo trạng thái
    if (strpos($query, 'chờ duyệt') !== false || strpos($query, 'pending') !== false) {
        $sql .= " AND b.status = 'cho_duyet'";
    } elseif (strpos($query, 'đã duyệt') !== false || strpos($query, 'approved') !== false) {
        $sql .= " AND b.status = 'da_duyet'";
    }

    $sql .= " ORDER BY b.booking_date DESC, b.start_time DESC LIMIT 20";

    $result = mysqli_query($conn, $sql);
    $bookings = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bookings[] = [
                'id' => $row['id'],
                'court_code' => $row['court_code'],
                'court_name' => $row['court_name'],
                'user_name' => $row['user_name'],
                'booking_date' => $row['booking_date'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'purpose' => $row['purpose'],
                'status' => $row['status'],
                'status_text' => getBookingStatusText($row['status'])
            ];
        }
    }

    return $bookings;
}

/**
 * Lấy thống kê
 */
function getStatistics($conn) {
    $stats = [];

    // Tổng số sân
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM courts");
    $stats['total_courts'] = mysqli_fetch_assoc($result)['total'] ?? 0;

    // Sân trống
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM courts WHERE status = 'trong' AND is_active = 1");
    $stats['available_courts'] = mysqli_fetch_assoc($result)['total'] ?? 0;

    // Tính toán số sân trống trong ngày hôm nay (6h-22h)
    $today = date('Y-m-d');
    $sql_today = "SELECT COUNT(*) as total FROM courts r
                  WHERE r.is_active = 1
                  AND r.status != 'bao_tri'
                  AND r.id NOT IN (
                      SELECT b.court_id FROM bookings b
                      WHERE b.booking_date = '$today'
                      AND b.status = 'da_duyet'
                      AND (
                          (b.start_time <= '22:00:00' AND b.end_time >= '06:00:00')
                      )
                  )";
    $result_today = mysqli_query($conn, $sql_today);
    $stats['available_today_6h_22h'] = mysqli_fetch_assoc($result_today)['total'] ?? 0;

    // Đơn chờ duyệt
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status = 'cho_duyet'");
    $stats['pending_bookings'] = mysqli_fetch_assoc($result)['total'] ?? 0;

    // Đơn đã duyệt
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status = 'da_duyet'");
    $stats['approved_bookings'] = mysqli_fetch_assoc($result)['total'] ?? 0;

    // Tổng người dùng
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
    $stats['total_users'] = mysqli_fetch_assoc($result)['total'] ?? 0;

    // Sân được đặt nhiều nhất
    $sql = "SELECT r.court_code, r.court_name, COUNT(b.id) as booking_count
            FROM courts r
            LEFT JOIN bookings b ON r.id = b.court_id
            GROUP BY r.id
            ORDER BY booking_count DESC
            LIMIT 5";
    $result = mysqli_query($conn, $sql);
    $stats['most_booked_courts'] = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $stats['most_booked_courts'][] = $row;
        }
    }

    return $stats;
}

/**
 * Lấy thông tin người dùng
 */
function getUsersData($conn, $query) {
    $sql = "SELECT id, full_name, email, role, phone FROM users WHERE 1=1";

    if (strpos($query, 'giảng viên') !== false) {
        $sql .= " AND role = 'giang_vien'";
    } elseif (strpos($query, 'sinh viên') !== false) {
        $sql .= " AND role = 'sinh_vien'";
    }

    $sql .= " ORDER BY full_name LIMIT 20";

    $result = mysqli_query($conn, $sql);
    $users = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = [
                'id' => $row['id'],
                'full_name' => $row['full_name'],
                'email' => $row['email'],
                'role' => $row['role'],
                'phone' => $row['phone'] ?? 'N/A'
            ];
        }
    }

    return $users;
}

/**
 * Chuyển đổi status sang text
 */
function getStatusText($status) {
    $statusMap = [
        'trong' => 'Còn trống',
        'dang_su_dung' => 'Đang sử dụng',
        'bao_tri' => 'Bảo trì'
    ];
    return $statusMap[$status] ?? $status;
}

function getBookingStatusText($status) {
    $statusMap = [
        'cho_duyet' => 'Chờ duyệt',
        'da_duyet' => 'Đã duyệt',
        'tu_choi' => 'Đã từ chối',
        'da_huy' => 'Đã hủy',
        'hoan_thanh' => 'Hoàn thành'
    ];
    return $statusMap[$status] ?? $status;
}