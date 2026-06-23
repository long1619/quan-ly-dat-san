<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}
include __DIR__ . '/../common/header.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../config/connect.php';

$userRole = $_SESSION['user_role'] ?? '';

// Kiểm tra quyền thêm sân
if (!checkPermission($conn, $userRole, 'add_court')) {
    echo "<script>alert('Bạn không có quyền thêm sân!'); window.location.href='../dashboard/index.php';</script>";
    exit;
}

// Lấy lỗi và dữ liệu cũ nếu có
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

// Lấy danh sách loại sân
$court_categories = [];
$result = $conn->query("SELECT id, category_name FROM court_categories ORDER BY category_name ASC");
while ($row = $result->fetch_assoc()) {
    $court_categories[] = $row;
}
?>
<style>
.avatar {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    border-radius: 8px;
}

.bg-label-primary {
    background-color: #e7f1ff;
    color: #2563eb;
}

.bg-label-success {
    background-color: #e8f5e9;
    color: #2e7d32;
}

.bg-label-warning {
    background-color: #fff3cd;
    color: #856404;
}

.bg-label-danger {
    background-color: #ffebee;
    color: #c62828;
}

.schedule-item {
    display: flex;
    align-items: center;
    padding: 16px;
    border-left: 4px solid #2563eb;
    background: #f8f9fa;
    border-radius: 4px;
    margin-bottom: 12px;
}

.schedule-item.orange {
    border-left-color: #ff9800;
}

.schedule-item.green {
    border-left-color: #10b981;
}

.schedule-item-content {
    flex: 1;
}

.schedule-item-time {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
}

.schedule-item-location {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    color: #999;
    margin-bottom: 4px;
}

.schedule-item-title {
    font-size: 13px;
    color: #666;
}

.schedule-item-status {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    white-space: nowrap;
}

.chart-container {
    position: relative;
    height: 300px;
    margin-bottom: 20px;
}

.doughnut-container {
    position: relative;
    height: 320px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include __DIR__ . '/../common/menu-sidebar.php'; ?>

            <!-- Layout container -->
            <div class="layout-page">
                <?php include __DIR__ . '/../common/navbar.php'; ?>

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl grow container-p-y">
                        <!-- Header -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h4 class="mb-1">Thêm mới sân</h4>
                            </div>
                        </div>

                        <!-- Hiển thị lỗi validate -->
                        <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <!-- DataTable Card -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <form id="formAddCourt" method="POST" action="process-add-court.php"
                                    enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="mb-3 col-md-6">
                                            <label for="court_code" class="form-label">Mã sân <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="court_code" name="court_code"
                                                value="<?= htmlspecialchars($old['court_code'] ?? '') ?>" placeholder="VD: BD01" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="court_name" class="form-label">Tên sân <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" id="court_name" name="court_name"
                                                value="<?= htmlspecialchars($old['court_name'] ?? '') ?>" placeholder="VD: Sân bóng đá 5 người - 01" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="category_id" class="form-label">Loại sân <span class="text-danger">*</span></label>
                                            <select class="form-select" id="category_id" name="category_id">
                                                <option value="">Chọn loại sân</option>
                                                <?php foreach ($court_categories as $cat): ?>
                                                <option value="<?= $cat['id'] ?>"
                                                    <?= (isset($old['category_id']) && $old['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($cat['category_name']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="area" class="form-label">Khu vực</label>
                                            <input class="form-control" type="text" id="area" name="area"
                                                value="<?= htmlspecialchars($old['area'] ?? '') ?>" placeholder="VD: Khu thể thao A" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="surface_type" class="form-label">Loại mặt sân</label>
                                            <input class="form-control" type="text" id="surface_type" name="surface_type"
                                                value="<?= htmlspecialchars($old['surface_type'] ?? '') ?>" placeholder="VD: Cỏ nhân tạo, Gỗ, Bê tông" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="capacity" class="form-label">Sức chứa tối đa</label>
                                            <input class="form-control" type="number" id="capacity" name="capacity"
                                                value="<?= htmlspecialchars($old['capacity'] ?? '') ?>" min="1" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="open_time" class="form-label">Giờ mở cửa</label>
                                            <input class="form-control" type="time" id="open_time" name="open_time"
                                                value="<?= htmlspecialchars($old['open_time'] ?? '06:00') ?>" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="close_time" class="form-label">Giờ đóng cửa</label>
                                            <input class="form-control" type="time" id="close_time" name="close_time"
                                                value="<?= htmlspecialchars($old['close_time'] ?? '22:00') ?>" />
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <label for="status" class="form-label">Trạng thái</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="trong"
                                                    <?= (isset($old['status']) && $old['status']=='trong') ? 'selected' : '' ?>>
                                                    Trống</option>
                                                <option value="bao_tri"
                                                    <?= (isset($old['status']) && $old['status']=='bao_tri') ? 'selected' : '' ?>>
                                                    Bảo trì</option>
                                            </select>
                                        </div>
                                        <!-- Hoạt động mặc định là 1, đã bỏ input theo yêu cầu -->
                                        <div class="mb-3 col-md-6">
                                            <label for="image_url" class="form-label">Ảnh sân</label>
                                            <input class="form-control" type="file" id="image_url" name="image_url"
                                                accept="image/*" onchange="previewCourtImage(event)" />
                                            <div style="margin-top:10px;">
                                                <img id="court-image-preview" src="" alt="Preview"
                                                    style="max-width:100%; max-height:200px; display:none; border-radius:8px;">
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <label for="facilities" class="form-label">Danh sách thiết bị / tiện ích</label>
                                            <textarea class="form-control" id="facilities" name="facilities"
                                                rows="2"><?= htmlspecialchars($old['facilities'] ?? '') ?></textarea>
                                            <small class="text-muted">Ví dụ: Khung thành, Lưới, Đèn chiếu sáng, Phòng thay đồ</small>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary me-2">Lưu</button>
                                        <button type="reset" class="btn btn-outline-secondary">Hủy</button>
                                    </div>
                                </form>
                                <script>
                                function previewCourtImage(event) {
                                    const input = event.target;
                                    const preview = document.getElementById('court-image-preview');
                                    if (input.files && input.files[0]) {
                                        const reader = new FileReader();
                                        reader.onload = function(e) {
                                            preview.src = e.target.result;
                                            preview.style.display = 'block';
                                        }
                                        reader.readAsDataURL(input.files[0]);
                                    }
                                }
                                </script>
                            </div>
                        </div>
                        <!-- /DataTable Card -->
                    </div>
                    <!-- / Content -->
                    <!-- Footer -->
                    <?php include __DIR__ . '/../common/footer.php'; ?>
                    <!-- / Footer -->
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout container -->
        </div>
        <!-- Overlay -->
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="../../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../../assets/vendor/libs/popper/popper.js"></script>
    <script src="../../assets/vendor/js/bootstrap.js"></script>
    <script src="../../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="../../assets/vendor/js/menu.js"></script>
    <!-- Main JS -->
    <script src="../../assets/js/main.js"></script>
</body>

</html>