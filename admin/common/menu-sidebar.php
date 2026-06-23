<?php
require_once __DIR__ . '/../../helpers/helpers.php';
$userRole = $_SESSION['user_role'] ?? '';
$userSessionId = $_SESSION['user_id'] ?? 0;

// Function to check if a menu item is active
function is_active($keywords) {
    if (!is_array($keywords)) {
        $keywords = [$keywords];
    }
    $current_url = $_SERVER['REQUEST_URI'];
    foreach ($keywords as $keyword) {
        if (stripos($current_url, $keyword) !== false) {
            return 'active';
        }
    }
    return '';
}

// Function to check if a submenu should be open
function is_open($keywords) {
    if (!is_array($keywords)) {
        $keywords = [$keywords];
    }
    $current_url = $_SERVER['REQUEST_URI'];
    foreach ($keywords as $keyword) {
        if (stripos($current_url, $keyword) !== false) {
            return 'active open';
        }
    }
    return '';
}
?>
<style>
/* --- Sidebar Main --- */
@media (min-width: 1200px) {
    .layout-menu {
        position: relative; /* Ensure absolute children are relative to this */
        display: flex;
        flex-direction: column; /* Main axis vertical */
        height: 100% !important;
    }
}

/* Chiều rộng sidebar trên mobile */
@media (max-width: 1199.98px) {
    .layout-menu {
        width: 280px !important;
    }
}

.menu-inner {
    flex-grow: 1; /* Allow menu to take available space */
    overflow-y: auto; /* Scrollable menu items */
    overflow-x: hidden;
    padding-bottom: 100px; /* Space for the role box */
}

/* --- Brand/Logo Area --- */
.app-brand {
    padding: 1rem 1.5rem;
    height: auto !important; /* Override template defaults if needed */
    margin-bottom: 0.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    position: relative; /* Add this to anchor the close button correctly */
}
.brand-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.brand-logo-circle {
    background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    padding: 8px;
    border-radius: 14px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border: 1.5px solid #c7d2fe;
}
.brand-title {
    font-size: 14px;
    font-weight: 800;
    color: #334155;
    margin-top: 4px;
}
.brand-subtitle {
    font-size: 10px;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
}

/* --- Menu Headers --- */
.menu-header {
    margin-top: 1rem;
    margin-bottom: 0.25rem;
    padding: 0 1rem;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: flex-start !important;
}
.menu-header::before {
    content: '';
    display: block;
    width: 10px;
    height: 2px;
    background-color: #e2e8f0;
    margin-right: 10px;
}

/* --- Menu Items --- */
.menu-item {
    margin: 0.1rem 0.5rem; /* Reduced margin to prevent overflow */
    width: calc(100% - 1rem); /* Explicit width calculation */
}

.menu-link {
    display: flex;
    align-items: center;
    padding: 0.7rem 1rem;
    color: #64748b;
    text-decoration: none;
    border-radius: 10px;
    transition: all 0.2s ease;
    white-space: nowrap;
    position: relative;
    font-size: 0.9rem;
    font-weight: 500;
}

.menu-icon {
    flex-shrink: 0;
    font-size: 1.25rem;
    margin-right: 0.8rem;
    text-align: center;
    width: 1.3rem;
    color: #94a3b8;
}

/* Hover & Active */
.menu-item:not(.active) .menu-link:hover {
    background-color: #eef2ff;
    color: #4f46e5;
}
.menu-item:not(.active) .menu-link:hover .menu-icon {
    color: #4f46e5;
}

.menu-item.active > .menu-link {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: #fff !important;
    font-weight: 700;
    box-shadow: 0 6px 16px -4px rgba(79,70,229,0.35);
}
.menu-item.active > .menu-link .menu-icon {
    color: #fff !important;
}

/* --- Submenu --- */
.menu-sub {
    padding: 0;
    margin: 0;
    display: none;
    background: transparent;
}
.menu-item.open > .menu-sub {
    display: block;
}

.menu-sub .menu-item {
    margin: 0.1rem 0.5rem 0.1rem 0.5rem;
}
.menu-sub .menu-link {
    padding-left: 3rem;
    font-size: 0.875rem;
    color: #64748b;
}
.menu-sub .menu-item.active .menu-link {
    background: #eef2ff;
    color: #4f46e5 !important;
    font-weight: 600;
    border-left: 3px solid #4f46e5;
    border-radius: 0 8px 8px 0;
    box-shadow: none;
}
.menu-sub .menu-item.active .menu-link:before {
    display: none;
}

/* --- Toggles --- */
/* Hide any template-provided arrows that might be floating around */
.menu-item.menu-toggle::after {
    display: none !important;
}

.menu-toggle > .menu-link:after {
    content: "";
    display: block;
    position: absolute;
    right: 1.2rem;
    top: 50%;
    width: 6px;
    height: 6px;
    border-bottom: 2px solid #cbd5e1;
    border-right: 2px solid #cbd5e1;
    transform: translateY(-50%) rotate(-45deg);
    transition: all 0.3s ease;
}

.menu-item.open > .menu-link:after {
    transform: translateY(-70%) rotate(45deg);
    border-color: #4f46e5;
}

.menu-item.active > .menu-link:after {
    border-color: #fff !important;
}

/* Ensure non-toggles do not show any arrow from our custom rules */
.menu-item:not(.menu-toggle) > .menu-link:after {
    display: none !important;
}

/* --- Role Box (Premium Static Design) --- */
.role-box-container {
    position: absolute;
    bottom: 20px;
    left: 0;
    width: 100%;
    padding: 0 1.2rem;
    pointer-events: none;
}
.role-box-card {
    pointer-events: auto;
    background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%);
    border: 1px solid #c7d2fe;
    border-left: 4px solid #4f46e5;
    border-radius: 14px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 8px 24px -8px rgba(79, 70, 229, 0.2);
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    position: relative;
    overflow: hidden;
}

.role-box-card::before {
    content: '';
    position: absolute;
    top: -50%; right: -50%;
    width: 100%; height: 100%;
    background: radial-gradient(circle, rgba(79,70,229,0.06) 0%, transparent 70%);
    z-index: 0;
}

.role-box-card:hover {
    box-shadow: 0 12px 30px -8px rgba(79, 70, 229, 0.3);
    border-color: #a5b4fc;
}

.role-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: #fff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
    z-index: 1;
}

.role-info {
    z-index: 1;
    display: flex;
    flex-direction: column;
}

.role-info span {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #6366f1;
    font-weight: 700;
    margin-bottom: 2px;
}

.role-info h6 {
    margin: 0;
    font-size: 15px;
    font-weight: 800;
    color: #1e293b;
    line-height: 1.2;
}


/* --- Badge --- */
.badge-notif {
    background-color: #ff3d1f;
    color: white;
    font-size: 0.7rem;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: auto;
}

/* --- Close Button for Mobile --- */
.close-menu-btn {
    position: absolute;
    right: 15px; /* Cố định ở góc phải */
    top: 15px; 
    color: #566a7f;
    cursor: pointer;
    background: #f3f4f6;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    z-index: 9999; /* Đảm bảo luôn nằm trên cùng */
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.close-menu-btn:hover {
    background: #e2e8f0;
    color: #ef4444;
}

.close-menu-btn i {
    font-size: 1.2rem;
}
</style>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <!-- Brand -->
    <div class="app-brand demo">
        <a href="../dashboard/index.php" class="brand-wrapper">
            <div class="brand-logo-circle">
                <img src="../../assets/img/logo/sports_logo.png" alt="Logo" width="50" height="50" />
            </div>
            <span class="brand-title">Quản lý Đặt Sân</span>
        </a>

        <!-- Close button for mobile -->
        <a href="javascript:void(0);" class="layout-menu-toggle close-menu-btn d-xl-none">
            <i class="bx bx-x"></i>
        </a>
    </div>

    <!-- Scrollable Menu Area -->
    <ul class="menu-inner">

        <!-- Dashboard -->
        <li class="menu-item <?php echo is_active('dashboard'); ?>">
            <a href="../dashboard/index.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>

        <!-- User Management -->
        <?php if (checkPermission($conn, $userRole, 'view_user')): ?>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Người dùng</span>
        </li>
        <li class="menu-item menu-toggle <?php echo is_open(['user', 'list-user', 'add-user']); ?>">
            <a href="javascript:void(0);" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Quản lý người dùng">Quản lý người dùng</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo is_active('list-user.php'); ?>">
                    <a href="../user/list-user.php" class="menu-link">
                        <div data-i18n="Danh sách">Danh sách người dùng</div>
                    </a>
                </li>
                <?php if (checkPermission($conn, $userRole, 'add_user')): ?>
                <li class="menu-item <?php echo is_active('add-user.php'); ?>">
                    <a href="../user/add-user.php" class="menu-link">
                        <div data-i18n="Thêm mới">Thêm người dùng</div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <li class="menu-item <?php echo is_active('permission.php'); ?>">
            <a href="../settings/permission.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="Phân quyền">Phân quyền</div>
            </a>
        </li>
        <?php endif; ?>
        <!-- Court & Booking -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Sân & Đặt Lịch</span>
        </li>

        <!-- Court Category Management -->
        <?php if (checkPermission($conn, $userRole, 'view_court_category')): ?>
        <li class="menu-item menu-toggle <?php echo is_open(['court-type', 'list-type-court']); ?>">
            <a href="javascript:void(0);" class="menu-link">
                <i class="menu-icon tf-icons bx bx-category"></i>
                <div data-i18n="Quản lý danh mục sân">Quản lý loại sân</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo is_active('list-type-court.php'); ?>">
                    <a href="../court-type/list-type-court.php" class="menu-link">
                        <div data-i18n="Loại sân">Loại sân</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Court Management -->
        <?php if (checkPermission($conn, $userRole, 'view_court')): ?>
        <li class="menu-item menu-toggle <?php echo is_open(['list-court', 'calendar-room', '/court/']); ?>">
            <a href="javascript:void(0);" class="menu-link">
                <i class="menu-icon tf-icons bx bx-football"></i>
                <div data-i18n="Quản lý sân">Quản lý sân</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item <?php echo is_active('list-court.php'); ?>">
                    <a href="../court/list-court.php" class="menu-link">
                        <div data-i18n="Danh sách sân">Danh sách sân</div>
                    </a>
                </li>
                <li class="menu-item <?php echo is_active('calendar-room.php'); ?>">
                    <a href="../booking/calendar-room.php" class="menu-link">
                        <div data-i18n="Lịch sân">Xem lịch sân</div>
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>

        <?php
            // Calculate Pending Count
            $pending_count = 0;
            if (isset($conn)) {
                $checkTable = $conn->query("SHOW TABLES LIKE 'bookings'");
                if($checkTable && $checkTable->num_rows > 0) {
                     $sql_pending = "SELECT COUNT(*) as total FROM bookings WHERE status = 'cho_duyet'";
                     $result_pending = $conn->query($sql_pending);
                     if ($result_pending && $row = $result_pending->fetch_assoc()) {
                         $pending_count = $row['total'];
                     }
                }
            }
        ?>
        <?php if (checkPermission($conn, $userRole, 'approve_booking')): ?>
        <li class="menu-item <?php echo is_active('approve-room.php'); ?>">
            <a href="../approve/approve-room.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-check-shield"></i>
                <div data-i18n="Phê duyệt">Phê duyệt đặt sân</div>
                <?php if ($pending_count > 0): ?>
                    <span class="badge-notif"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <?php endif; ?>

        <?php if (checkPermission($conn, $userRole, 'view_canceled')): ?>
        <li class="menu-item <?php echo is_active('list-canceled-bookings.php'); ?>">
            <a href="../cancel/list-canceled-bookings.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-x-circle"></i>
                <div data-i18n="Đơn đã hủy">Đơn đã hủy</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (checkPermission($conn, $userRole, 'view_history')): ?>
        <li class="menu-item <?php echo is_active('history-booking.php'); ?>">
            <a href="../history/history-booking.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-history"></i>
                <div data-i18n="Lịch sử">Lịch sử đặt sân</div>
            </a>
        </li>
        <?php endif; ?>

        <!-- System -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Hệ thống</span>
        </li>
        <?php if (checkPermission($conn, $userRole, 'view_news')): ?>
        <li class="menu-item <?php echo is_active('list-news.php'); ?>">
            <a href="../news/list-news.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-news"></i>
                <div data-i18n="Tin tức">Tin tức</div>
            </a>
        </li>
        <?php endif; ?>

        <?php if (checkPermission($conn, $userRole, 'use_ai')): ?>
        <li class="menu-item <?php echo is_active('ai-assistant.php'); ?>">
            <a href="../ai-assistant/ai-assistant.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bot"></i>
                <div data-i18n="Trợ lý AI">Trợ lý AI</div>
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Fixed Role Box -->
    <div class="role-box-container">
        <div class="role-box-card">
            <div class="role-icon">
                <i class="bx bx-id-card"></i>
            </div>
            <div class="role-info">
                <span>Bạn đang là</span>
                <h6>
                    <?php
                        $role_map = [
                            'admin' => 'Admin',
                            'giang_vien' => 'Giảng Viên',
                            'sinh_vien' => 'Sinh Viên'
                        ];
                        echo isset($role_map[$_SESSION['user_role']]) ? $role_map[$_SESSION['user_role']] : '';
                    ?>
                </h6>
            </div>
        </div>
    </div>

</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Menu toggle logic
    const menuToggles = document.querySelectorAll('.menu-toggle > .menu-link');

    menuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const menuItem = this.parentElement;
            menuItem.classList.toggle('open');
        });
    });

    // Fix: Force navigation for Child Links (in case template JS blocks them)
    // We target links inside .menu-sub that have a real href
    const childLinks = document.querySelectorAll('.menu-sub .menu-link');
    childLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href !== 'javascript:void(0);' && href !== '#') {
                // If the link is not just a placeholder, allow standard navigation.
                // Forces browser to go to that URL, overriding any preventDefault from other scripts.
                window.location.href = href;
            }
        });
    });

    // Keep active menu open
    const activeItem = document.querySelector('.menu-item.active');
    if (activeItem) {
        // If nested, open parent
        const parentSub = activeItem.closest('.menu-sub');
        if (parentSub) {
            parentSub.parentElement.classList.add('open');
        }
    }

    // Close menu logic for mobile
    const closeBtn = document.querySelector('.close-menu-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (window.Helpers && typeof window.Helpers.toggleCollapsed === 'function') {
                window.Helpers.toggleCollapsed();
            } else {
                // Fallback logic to remove the expanded class from html
                document.documentElement.classList.remove('layout-menu-expanded');
            }
        });
    }
});
</script>