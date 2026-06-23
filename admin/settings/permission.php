<?php
    session_start();
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: ../auth/login.php');
        exit;
    }
    // Chỉ admin mới được vào trang này
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] !== 'admin') {
        echo "<script>alert('Bạn không có quyền truy cập trang này!'); window.location.href='../dashboard/index.php';</script>";
        exit;
    }

    include __DIR__ . '/../common/header.php';
    require_once __DIR__ . '/../../config/connect.php';

    $roles = [
        'giang_vien' => 'Giảng Viên',
        'sinh_vien' => 'Sinh Viên'
    ];

    $modules = [
        'court_category' => [
            'name' => 'Quản lý Danh mục Sân',
            'icon' => 'bx-grid-alt',
            'permissions' => [
                'view_court_category' => 'Xem danh sách loại sân',
                'add_court_category' => 'Thêm loại sân mới',
                'edit_court_category' => 'Chỉnh sửa loại sân',
                'delete_court_category' => 'Xóa loại sân'
            ]
        ],
        'court' => [
            'name' => 'Quản lý Sân Thể Thao',
            'icon' => 'bx-football',
            'permissions' => [
                'view_court' => 'Xem danh sách sân thể thao',
                'add_court' => 'Thêm sân thể thao mới',
                'edit_court' => 'Chỉnh sửa sân thể thao',
                'delete_court' => 'Xóa sân thể thao'
            ]
        ],
        'booking' => [
            'name' => 'Đặt Lịch & Phê Duyệt',
            'icon' => 'bx-calendar-check',
            'permissions' => [
                'create_booking' => 'Tạo yêu cầu đặt sân',
                'cancel_booking' => 'Hủy đơn đặt sân',
                'approve_booking' => 'Phê duyệt đơn (Admin)',
                'view_history' => 'Xem lịch sử đặt sân',
                'view_canceled' => 'Xem đơn đã hủy'
            ]
        ],
        'user' => [
            'name' => 'Người Dùng',
            'icon' => 'bx-group',
            'permissions' => [
                'view_user' => 'Xem danh sách user',
                'add_user' => 'Thêm user',
                'edit_user' => 'Sửa user',
                'delete_user' => 'Xóa user'
            ]
        ],
        'system' => [
            'name' => 'Hệ Thống',
            'icon' => 'bx-cog',
            'permissions' => [
                'view_news' => 'Xem danh sách tin tức',
                'add_news' => 'Thêm tin tức mới',
                'edit_news' => 'Chỉnh sửa tin tức',
                'delete_news' => 'Xóa tin tức',
                'use_ai' => 'Sử dụng AI Assistant'
            ]
        ]
    ];

    // Load existing permissions from DB
    $dbPermissions = [];
    $sql_fetch = "SELECT role, permission_key, active FROM permissions";
    $res_fetch = $conn->query($sql_fetch);
    if ($res_fetch) {
        while ($row = $res_fetch->fetch_assoc()) {
            $dbPermissions[$row['role']][$row['permission_key']] = $row['active'];
        }
    }
?>

<!-- Custom CSS for Premium Academic-Sports Permissions UI -->
<style>
    .permission-card {
        background: #ffffff !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        border-radius: 20px !important;
        box-shadow: 0 4px 15px -3px rgba(15, 23, 42, 0.03), 0 4px 6px -2px rgba(15, 23, 42, 0.02) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        margin-bottom: 2rem;
        overflow: hidden;
    }
    .permission-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 15px 30px -10px rgba(15, 23, 42, 0.08) !important;
        border-color: rgba(37, 99, 235, 0.2) !important;
    }
    .permission-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%) !important;
        padding: 1.25rem 1.75rem !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .permission-title {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 800 !important;
        font-size: 1.15rem !important;
        letter-spacing: -0.5px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff !important;
    }
    .permission-title i {
        font-size: 1.4rem;
        background: rgba(255, 255, 255, 0.15);
        padding: 6px;
        border-radius: 8px;
    }
    .role-col {
        border-left: 1px solid rgba(226, 232, 240, 0.6) !important;
        text-align: center;
    }
    .table-permission th {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        text-transform: uppercase;
        font-size: 0.78rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.8px;
        color: #64748b !important;
        background-color: #f8fafc !important;
        padding: 1.1rem 1rem !important;
        border-bottom: 2px solid #e2e8f0 !important;
    }
    .table-permission td {
        vertical-align: middle;
        padding: 1.1rem 1rem !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .table-permission tr:last-child td {
        border-bottom: none !important;
    }
    .perm-name {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 600 !important;
        color: #334155 !important;
        font-size: 0.92rem !important;
    }
    
    /* IOS Toggle Switch Custom Sports Edition */
    .switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 26px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(15, 23, 42, 0.15);
    }
    input:checked + .slider {
        background-color: #10b981 !important; /* Turf Green for Authorized */
    }
    input:focus + .slider {
        box-shadow: 0 0 1px #10b981;
    }
    input:checked + .slider:before {
        transform: translateX(22px);
    }

    /* Save Bar - Premium Floating Glassmorphism design */
    .save-bar {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 999;
        display: none;
        animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    @keyframes slideUp {
        from { transform: translateY(50px) scale(0.95); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    .save-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        border: none !important;
        padding: 12px 32px !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 800 !important;
        font-size: 0.95rem !important;
        border-radius: 50px !important;
        color: #ffffff !important;
        box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.4) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        transition: all 0.3s ease !important;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .save-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 15px 30px -5px rgba(16, 185, 129, 0.5) !important;
    }
    .save-btn:active {
        transform: translateY(0) !important;
    }
</style>

<body>
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Sidebar -->
        <?php include __DIR__ . '/../common/menu-sidebar.php'; ?>

        <div class="layout-page">
            <!-- Navbar -->
            <?php include __DIR__ . '/../common/navbar.php'; ?>

            <div class="content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 class="fw-bold py-1 mb-0"><span class="text-muted fw-light">Hệ thống /</span> Phân quyền</h4>
                            <small class="text-muted">Cấu hình quyền hạn cho Giảng viên và Sinh viên</small>
                        </div>
                    </div>

                    <form id="permissionForm" method="POST">

                        <!-- Iterate Modules -->
                        <?php foreach ($modules as $modKey => $module): ?>
                        <div class="card permission-card">
                            <div class="permission-header">
                                <h5 class="permission-title">
                                    <i class='bx <?php echo $module['icon'] ?? 'bx-layer'; ?>'></i> <?php echo $module['name']; ?>
                                </h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover table-permission mb-0">
                                    <thead>
                                        <tr>
                                            <th width="40%">Chức năng</th>
                                            <?php foreach ($roles as $roleKey => $roleName): ?>
                                                <th width="30%" class="text-center role-col">
                                                    <?php echo $roleName; ?>
                                                </th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($module['permissions'] as $permKey => $permName): ?>
                                        <tr>
                                            <td class="perm-name"><?php echo $permName; ?></td>

                                            <?php foreach ($roles as $roleKey => $roleName): ?>
                                            <td class="text-center role-col">
                                                <label class="switch">
                                                    <?php
                                                        $isChecked = (isset($dbPermissions[$roleKey][$permKey]) && $dbPermissions[$roleKey][$permKey] == 1) ? 'checked' : '';
                                                    ?>
                                                    <input type="checkbox" name="perm[<?php echo $roleKey; ?>][<?php echo $permKey; ?>]" <?php echo $isChecked; ?> class="perm-checkbox">
                                                    <span class="slider"></span>
                                                </label>
                                            </td>
                                            <?php endforeach; ?>

                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- Floating Save Button -->
                        <div class="save-bar" id="saveBar">
                            <button type="submit" class="btn btn-primary rounded-pill save-btn no-spinner">
                                <i class='bx bx-save me-1'></i> Lưu thay đổi
                            </button>
                        </div>

                    </form>

                </div>
                <?php include __DIR__ . '/../common/footer.php'; ?>
            </div>
        </div>
    </div>
</div>

<!-- Core JS -->
<script src="../../assets/vendor/libs/jquery/jquery.js"></script>
<script src="../../assets/vendor/js/bootstrap.js"></script>
<script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="../../assets/vendor/js/menu.js"></script>
<script src="../../assets/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Show save button when any checkbox changes
        $('.perm-checkbox').on('change', function() {
            $('#saveBar').fadeIn();
        });

        // Form Submit via AJAX
        $('#permissionForm').on('submit', function(e) {
            e.preventDefault();
            
            var $btn = $(this).find('button[type="submit"]');
            var originalHtml = $btn.html();
            
            // Disable button and show spinner manually
            $btn.prop('disabled', true);
            $btn.html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang lưu...');

            $.ajax({
                url: 'save-permissions.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Cấu hình phân quyền đã được cập nhật.',
                            confirmButtonText: 'Đóng',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                        });
                        $('#saveBar').fadeOut();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: response.message || 'Không thể lưu cấu hình.',
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Đã xảy ra lỗi kết nối.',
                    });
                },
                complete: function() {
                    // Re-enable button and restore original state
                    $btn.prop('disabled', false);
                    $btn.html(originalHtml);
                }
            });
        });
    });
</script>

</body>
</html>
