<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once __DIR__ . '/../../config/connect.php';
    require_once __DIR__ . '/../../helpers/helpers.php';

    // Lấy user id từ SESSION an toàn
    $userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    $userLoginId = getUserById($conn, $userId);

    // --- 1. Lấy thông tin thống kê thời gian thực ---
    // Sân đang hoạt động
    $activeCourtsCount = 0;
    $sqlCourts = "SELECT COUNT(*) as cnt FROM courts WHERE is_active = 1";
    $resCourts = $conn->query($sqlCourts);
    if ($resCourts && $row = $resCourts->fetch_assoc()) {
        $activeCourtsCount = $row['cnt'];
    }

    // Đơn đặt sân hôm nay
    $todayBookingsCount = 0;
    $sqlToday = "SELECT COUNT(*) as cnt FROM bookings WHERE booking_date = CURDATE() AND status != 'da_huy'";
    $resToday = $conn->query($sqlToday);
    if ($resToday && $row = $resToday->fetch_assoc()) {
        $todayBookingsCount = $row['cnt'];
    }

    // Số đơn đang chờ duyệt
    $pendingCount = 0;
    $sqlPending = "SELECT COUNT(*) as cnt FROM bookings WHERE status = 'cho_duyet'";
    $resPending = $conn->query($sqlPending);
    if ($resPending && $row = $resPending->fetch_assoc()) {
        $pendingCount = $row['cnt'];
    }

    // --- 2. Lấy danh sách 3 đơn đặt sân chờ duyệt mới nhất ---
    $latestPendingBookings = [];
    $sqlLatestPending = "SELECT b.id, b.booking_date, b.start_time, b.end_time, u.full_name, c.court_name
                         FROM bookings b
                         JOIN users u ON b.user_id = u.id
                         JOIN courts c ON b.court_id = c.id
                         WHERE b.status = 'cho_duyet'
                         ORDER BY b.created_at DESC
                         LIMIT 3";
    $resLatestPending = $conn->query($sqlLatestPending);
    if ($resLatestPending) {
        while ($row = $resLatestPending->fetch_assoc()) {
            $latestPendingBookings[] = $row;
        }
    }

    // --- 3. Lời chào động theo giờ ---
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $hour = intval(date('H'));
    $greeting = 'Chào buổi sáng';
    $sports_emoji = '🌅'; 
    
    if ($hour >= 5 && $hour < 12) {
        $greeting = 'Chào buổi sáng';
        $sports_emoji = '🌅';
    } elseif ($hour >= 12 && $hour < 18) {
        $greeting = 'Chào buổi chiều';
        $sports_emoji = '☀️';
    } else {
        $greeting = 'Chào buổi tối';
        $sports_emoji = '🌙';
    }

    // Mảng emoji thể thao ngẫu nhiên để tăng tính tương tác sinh động
    $sports_emojis = ['⚽', '🏀', '🎾', '🏸', '🏐'];
    $selected_emoji = $sports_emojis[$userId % count($sports_emojis)];

    // --- 4. Hàm fallback initials tạo avatar chữ viết tắt chuyên nghiệp ---
    if (!function_exists('getNavbarInitials')) {
        function getNavbarInitials($name) {
            $parts = explode(' ', trim($name));
            $initials = '';
            if (count($parts) >= 2) {
                $initials .= mb_substr($parts[0], 0, 1, 'UTF-8');
                $initials .= mb_substr($parts[count($parts) - 1], 0, 1, 'UTF-8');
            } elseif (count($parts) == 1) {
                $initials .= mb_substr($parts[0], 0, 2, 'UTF-8');
            }
            return mb_strtoupper($initials, 'UTF-8');
        }
    }
?>
<style>
/* Hiệu ứng nhấp nháy cho chấm xanh lá hoạt động */
.stat-pulse-green {
    width: 6px;
    height: 6px;
    background-color: var(--sp-emerald);
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 6px var(--sp-emerald);
    animation: stat-pulse-anim 2s infinite;
}

/* Hiệu ứng nhấp nháy cho chấm vàng chờ duyệt */
.stat-pulse-orange {
    width: 6px;
    height: 6px;
    background-color: var(--sp-orange);
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 6px var(--sp-orange);
    animation: stat-pulse-anim 2s infinite;
}

@keyframes stat-pulse-anim {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.3); }
}

/* Nảy nhẹ cho icon bóng/emoji */
@keyframes bounce-anim {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-4px); }
}
.sports-emoji-bounce {
    display: inline-block;
    animation: bounce-anim 3s infinite ease-in-out;
}

/* Viền phát sáng nhấp nháy cho nút chờ duyệt khẩn cấp */
@keyframes pulse-border-anim {
    0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
    50% { box-shadow: 0 0 0 6px rgba(245, 158, 11, 0); }
}
.pulse-border-warning {
    animation: pulse-border-anim 2s infinite;
}

/* Hiệu ứng phát sáng nhẹ cho badge chuông thông báo */
@keyframes pulse-badge-anim {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.bell-badge-pulse {
    animation: pulse-badge-anim 1.5s infinite alternate;
}

/* Style cho chuông thông báo */
.dropdown-notifications .nav-link i {
    transition: transform 0.2s;
}
.dropdown-notifications .nav-link:hover i {
    transform: rotate(15deg) scale(1.1);
}

.avatar-notification {
    background: var(--sp-primary-light);
    color: var(--sp-primary);
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Tinh chỉnh avatar online */
.avatar-online::after {
    background-color: var(--sp-emerald) !important;
    box-shadow: 0 0 0 2px #fff !important;
}

/* Thêm hiệu ứng hover bóng bẩy cho các widget chỉ số nhanh */
.stats-quick-pill {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.stats-quick-pill:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md) !important;
}

/* Ghi đè CSS để navbar kéo dài toàn chiều ngang */
.layout-navbar {
    margin: 0 !important;
    border-radius: 0 !important;
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;
    border-bottom: 1px solid var(--sl-200) !important;
    width: 100% !important;
    max-width: 100% !important;
    padding: 0.75rem 1.5rem !important;
    background: rgba(255, 255, 255, 0.9) !important;
    backdrop-filter: blur(16px) !important;
    -webkit-backdrop-filter: blur(16px) !important;
    box-shadow: 0 1px 10px rgba(15, 23, 42, 0.04) !important;
}
</style>
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme"
    id="layout-navbar" style="z-index: 999;">
    <!-- Nút Menu thu gọn trên mobile -->
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm" style="color: var(--sl-600);"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-between w-100" id="navbar-collapse">
        <!-- PHẦN BÊN TRÁI: LỜI CHÀO ĐỘNG & BỘ CHỈ SỐ NHANH (Ẩn trên thiết bị rất nhỏ dưới md) -->
        <div class="navbar-nav align-items-center d-none d-md-flex">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-5 fw-bold text-dark mb-0"><?php echo $greeting; ?>, <?php echo htmlspecialchars($userLoginId['full_name']); ?>!</span>
                        <span class="sports-emoji-bounce" style="font-size: 1.25rem;"><?php echo $sports_emoji; ?></span>
                        <span class="sports-emoji-bounce" style="font-size: 1.25rem; animation-delay: 0.5s;"><?php echo $selected_emoji; ?></span>
                    </div>
                    <small class="text-muted" style="font-size: 0.76rem; font-weight: 500;">Hệ thống quản lý đặt sân & phòng thể chất</small>
                </div>

                <!-- Thanh phân tách dọc -->
                <div style="width: 1px; height: 32px; background-color: var(--sl-200); margin: 0 4px;"></div>

                <!-- Bảng Widget chỉ số thể thao nhanh -->
                <div class="d-flex align-items-center gap-2">
                    <a href="../court/list-court.php" class="text-decoration-none" title="Xem danh sách sân">
                        <span class="badge stats-quick-pill d-flex align-items-center gap-1.5 py-2 px-3 text-success" style="background-color: var(--sp-emerald-light); border: 1px solid #a7f3d0; border-radius: 30px; font-size: 0.74rem; font-weight: 700;">
                            <span class="stat-pulse-green"></span>
                            <i class="bx bx-football" style="font-size: 0.9rem;"></i> <?php echo $activeCourtsCount; ?> Danh sách sân
                        </span>
                    </a>
                    
                    <a href="../history/history-booking.php" class="text-decoration-none" title="Xem lịch đặt sân hôm nay">
                        <span class="badge stats-quick-pill d-flex align-items-center gap-1.5 py-2 px-3 text-primary" style="background-color: var(--sp-primary-light); border: 1px solid #c7d2fe; border-radius: 30px; font-size: 0.74rem; font-weight: 700;">
                            <i class="bx bx-calendar" style="font-size: 0.9rem;"></i> <?php echo $todayBookingsCount; ?> Lượt đặt hôm nay
                        </span>
                    </a>

                    <?php if ($pendingCount > 0): ?>
                        <a href="../approve/approve-room.php" class="text-decoration-none" title="Duyệt các yêu cầu đặt sân">
                            <span class="badge stats-quick-pill pulse-border-warning d-flex align-items-center gap-1.5 py-2 px-3 text-warning" style="background-color: #fffbeb; border: 1px solid #fde68a; border-radius: 30px; font-size: 0.74rem; font-weight: 700;">
                                <span class="stat-pulse-orange"></span>
                                <i class="bx bx-time" style="font-size: 0.9rem;"></i> <?php echo $pendingCount; ?> Chờ phê duyệt
                            </span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- PHẦN BÊN PHẢI: TRUNG TÂM THÔNG BÁO & THÔNG TIN TÀI KHOẢN -->
        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- CHUÔNG THÔNG BÁO THÔNG MINH -->
            <li class="nav-item dropdown-notifications dropdown me-3">
                <a class="nav-link dropdown-toggle hide-arrow position-relative" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-bell bx-sm" style="color: var(--sl-500); font-size: 1.35rem;"></i>
                    <?php if ($pendingCount > 0): ?>
                        <span class="badge rounded-pill bg-danger position-absolute bell-badge-pulse" style="top: -2px; right: -2px; font-size: 0.65rem; padding: 0.25em 0.45em; border: 2px solid #fff; box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);">
                            <?php echo $pendingCount; ?>
                        </span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0" style="width: 330px; border: 1px solid var(--sl-200); box-shadow: var(--shadow-lg); border-radius: var(--r-md); overflow: hidden;">
                    <li class="dropdown-menu-header border-bottom p-3" style="background: linear-gradient(135deg, var(--sp-primary) 0%, #6366f1 100%);">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-white fw-bold" style="font-size: 0.95rem;">Yêu cầu đặt sân mới</h6>
                            <span class="badge bg-white text-primary rounded-pill" style="font-size: 0.72rem; font-weight: 700; padding: 4px 8px;">
                                <?php echo $pendingCount; ?> đơn chờ
                            </span>
                        </div>
                    </li>
                    <li class="dropdown-notifications-list scrollable-container" style="max-height: 270px; overflow-y: auto;">
                        <ul class="list-group list-group-flush m-0">
                            <?php if (empty($latestPendingBookings)): ?>
                                <li class="list-group-item list-group-item-action text-center py-4 px-3" style="background: transparent;">
                                    <i class="bx bx-check-circle text-success mb-2" style="font-size: 2.2rem;"></i>
                                    <p class="text-muted mb-0 fw-semibold" style="font-size: 0.85rem;">Đã phê duyệt tất cả đơn đặt sân!</p>
                                    <small class="text-muted" style="font-size: 0.75rem;">Không có yêu cầu chờ duyệt mới.</small>
                                </li>
                            <?php else: ?>
                                <?php foreach ($latestPendingBookings as $bk): ?>
                                    <li class="list-group-item list-group-item-action p-3 border-bottom position-relative" style="transition: background 0.2s;">
                                        <a href="../approve/approve-room.php" class="d-flex text-decoration-none" style="color: inherit;">
                                            <div class="flex-shrink-0 me-3">
                                                <div class="avatar-notification d-flex align-items-center justify-content-center bg-label-warning text-warning rounded-circle" style="width: 36px; height: 36px;">
                                                    <i class="bx bx-time" style="font-size: 1.15rem;"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-0.5 fw-bold" style="font-size: 0.85rem; color: var(--sl-800);"><?php echo htmlspecialchars($bk['full_name']); ?></h6>
                                                <p class="mb-1 text-muted" style="font-size: 0.78rem; line-height: 1.35; letter-spacing: -0.1px;">
                                                    Đặt <strong><?php echo htmlspecialchars($bk['court_name']); ?></strong>
                                                </p>
                                                <small class="text-muted d-flex align-items-center gap-1.5" style="font-size: 0.7rem;">
                                                    <i class="bx bx-calendar text-primary" style="font-size: 0.85rem;"></i><?php echo date('d/m', strtotime($bk['booking_date'])); ?> 
                                                    <i class="bx bx-time-five text-primary" style="font-size: 0.85rem; margin-left: 2px;"></i><?php echo date('H:i', strtotime($bk['start_time'])); ?> - <?php echo date('H:i', strtotime($bk['end_time'])); ?>
                                                </small>
                                            </div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="dropdown-menu-footer border-top text-center p-2" style="background: var(--sl-50);">
                        <a href="../approve/approve-room.php" class="dropdown-item py-1.5 text-primary fw-bold text-center" style="font-size: 0.8rem; background: transparent !important; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                            Xem tất cả yêu cầu phê duyệt <i class="bx bx-chevron-right" style="font-size: 1.1rem; vertical-align: middle;"></i>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- PROFILE WIDGET & DROPDOWN -->
            <li class="nav-item d-flex flex-column align-items-end me-3 d-none d-sm-flex">
                <span class="fw-bold text-dark text-capitalize" style="font-size: 0.9rem;"><?php echo htmlspecialchars($userLoginId['full_name']); ?></span>
                <small class="text-muted" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--sp-primary) !important;">
                    <?php
                        $role_map = [
                            'admin' => 'Quản trị viên',
                            'giang_vien' => 'Giảng Viên',
                            'sinh_vien' => 'Sinh Viên'
                        ];
                        echo isset($role_map[$_SESSION['user_role']]) ? $role_map[$_SESSION['user_role']] : 'Người dùng';
                    ?>
                </small>
            </li>

            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online" style="width: 40px; height: 40px;">
                        <?php 
                        $hasAvatar = !empty($userLoginId['avatar']) && file_exists(__DIR__ . '/../../assets/' . $userLoginId['avatar']);
                        if ($hasAvatar): 
                        ?>
                            <img src="../../assets/<?php echo htmlspecialchars($userLoginId['avatar']); ?>" alt class="w-px-40 h-px-40 rounded-circle object-fit-cover" style="border: 2px solid var(--sp-primary-light); box-shadow: 0 0 10px rgba(79, 70, 229, 0.15);" />
                        <?php else: ?>
                            <div class="avatar-initial rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--sp-primary) 0%, #6366f1 100%); font-size: 0.88rem; border: 2px solid #fff; box-shadow: var(--shadow-md);">
                                <?php echo getNavbarInitials($userLoginId['full_name'] ?? 'AD'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-header border-bottom d-flex align-items-center gap-3 p-3">
                        <div class="avatar avatar-online" style="width: 40px; height: 40px;">
                            <?php if ($hasAvatar): ?>
                                <img src="../../assets/<?php echo htmlspecialchars($userLoginId['avatar']); ?>" alt class="w-px-40 h-px-40 rounded-circle object-fit-cover" />
                            <?php else: ?>
                                <div class="avatar-initial rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--sp-primary) 0%, #6366f1 100%); font-size: 0.88rem;">
                                    <?php echo getNavbarInitials($userLoginId['full_name'] ?? 'AD'); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fw-bold text-dark" style="font-size: 0.88rem;"><?php echo htmlspecialchars($userLoginId['full_name']); ?></span>
                            <small class="text-muted" style="font-size: 0.72rem;"><?php echo htmlspecialchars($userLoginId['email'] ?? ''); ?></small>
                        </div>
                    </li>
                    <li>
                         <a class="dropdown-item d-flex align-items-center" href="../user/profile-user.php" style="padding: 10px 16px !important;">
                             <i class="bx bx-user me-2" style="font-size: 1.15rem; color: var(--sp-primary);"></i>
                             <span class="align-middle fw-medium">Chỉnh sửa thông tin</span>
                         </a>
                     </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="../auth/logout.php" style="padding: 10px 16px !important; color: var(--sp-red) !important;">
                            <i class="bx bx-power-off me-2" style="font-size: 1.15rem;"></i>
                            <span class="align-middle fw-bold">Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>