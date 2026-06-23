<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Đăng Nhập | Hệ Thống Đặt Sân HUMG</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/logo/sports_logo.png" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="../../assets/vendor/fonts/boxicons.css" />

    <style>
        :root {
            --primary: #10b981; /* Premium Emerald Turf Green */
            --primary-hover: #059669;
            --primary-light: #ecfdf5;
            --secondary: #2563eb; /* High Performance Athletic Blue */
            --dark: #0f172a;
            --gray: #64748b;
            --light-gray: #f8fafc;
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-display: 'Outfit', sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-main);
            color: var(--dark);
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        .login-container {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* LEFT VISUAL SIDE */
        .visual-side {
            width: 60%;
            position: relative;
            background-image: url('../../assets/img/backgrounds/sports_login_bg.png');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            color: white;
            overflow: hidden;
        }

        /* Overlay with high-end dark gradient and glassmorphism hint */
        .visual-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(5, 150, 105, 0.45) 50%, rgba(15, 23, 42, 0.85) 100%);
            z-index: 1;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand-logo-container {
            width: 55px;
            height: 55px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            animation: float 4s ease-in-out infinite;
        }

        .brand-logo-container img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .brand-name {
            font-family: var(--font-display);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .brand-sub {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .hero-section {
            margin-top: auto;
            margin-bottom: auto;
            max-width: 580px;
        }

        .hero-badge {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
            border: 1px solid rgba(52, 211, 153, 0.3);
            padding: 6px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 24px;
            backdrop-filter: blur(5px);
            animation: fadeIn 0.8s ease-out;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: 42px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
            background: linear-gradient(to right, #ffffff, #e2e8f0, #a7f3d0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .hero-desc {
            font-size: 16px;
            line-height: 1.6;
            color: rgba(226, 232, 240, 0.85);
            margin-bottom: 35px;
            animation: slideUp 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Sports indicators */
        .sport-badges {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            animation: slideUp 1.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .sport-item {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 15px 10px;
            text-align: center;
            backdrop-filter: blur(8px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sport-item:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(16, 185, 129, 0.5);
            box-shadow: 0 12px 24px rgba(16, 185, 129, 0.15);
        }

        .sport-icon {
            font-size: 26px;
            color: #34d399;
            margin-bottom: 8px;
            display: inline-block;
        }

        .sport-label {
            font-size: 13px;
            font-weight: 700;
            color: white;
            display: block;
        }

        .visual-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 25px;
        }

        /* RIGHT FORM SIDE */
        .form-side {
            width: 40%;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px 80px;
            position: relative;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 35px;
        }

        .form-welcome {
            font-size: 14px;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
            display: block;
        }

        .form-title {
            font-family: var(--font-display);
            font-size: 28px;
            font-weight: 800;
            color: var(--dark);
        }

        .form-desc {
            font-size: 14px;
            color: var(--gray);
            margin-top: 8px;
        }

        /* Custom Input Group styling */
        .input-wrapper {
            margin-bottom: 24px;
            position: relative;
        }

        .input-label-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .input-label {
            font-size: 13.5px;
            font-weight: 600;
            color: #334155;
        }

        .input-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 20px;
            pointer-events: none;
            transition: color 0.3s;
        }

        .custom-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            font-family: var(--font-main);
            font-size: 14.5px;
            color: var(--dark);
            background-color: white;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-input::placeholder {
            color: #94a3b8;
        }

        .custom-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
            background-color: white;
        }

        .custom-input:focus ~ .input-icon {
            color: var(--primary);
        }

        .input-password-toggle {
            position: absolute;
            right: 16px;
            color: #94a3b8;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.3s;
            background: none;
            border: none;
            display: flex;
            align-items: center;
        }

        .input-password-toggle:hover {
            color: var(--dark);
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, #059669 100%);
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* Custom Alert box */
        .error-alert {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.05);
            animation: shake 0.5s ease-in-out;
        }

        .error-alert-icon {
            color: #ef4444;
            font-size: 22px;
            margin-top: 1px;
        }

        .error-alert-content {
            font-size: 13.5px;
            color: #991b1b;
            font-weight: 500;
            line-height: 1.4;
        }

        .error-alert-title {
            font-weight: 700;
            margin-bottom: 4px;
            color: #7f1d1d;
        }

        .field-error {
            color: #ef4444;
            font-size: 12px;
            font-weight: 600;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }

        /* Responsive Layout */
        @media (max-width: 1200px) {
            .visual-side {
                width: 50%;
                padding: 40px;
            }
            .form-side {
                width: 50%;
                padding: 40px 50px;
            }
            .hero-title {
                font-size: 34px;
            }
        }

        @media (max-width: 900px) {
            body {
                overflow-y: auto;
            }
            .login-container {
                flex-direction: column;
                min-height: 100vh;
            }
            .visual-side {
                width: 100%;
                min-height: 380px;
                padding: 30px;
            }
            .form-side {
                width: 100%;
                padding: 40px 30px;
                min-height: calc(100vh - 380px);
                box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.05);
            }
            .sport-badges {
                display: none; /* Hide on mobile to conserve space */
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Visual Side -->
        <div class="visual-side">
            <div class="visual-content">
                <!-- Header Brand Info -->
                <div class="brand-header">
                    <div class="brand-logo-container">
                        <img src="../../assets/img/logo/sports_logo.png" alt="University Logo" />
                    </div>
                    <div>
                        <div class="brand-name">Hệ Thống Đặt Sân</div>
                        <div class="brand-sub">Trường Đại Học Mỏ - Địa Chất</div>
                    </div>
                </div>

                <!-- Hero Headline -->
                <div class="hero-section">
                    <span class="hero-badge">Professional Arena</span>
                    <h1 class="hero-title">Sân chơi chuyên nghiệp,<br />Trải nghiệm đỉnh cao</h1>
                    <p class="hero-desc">
                        Hệ thống đăng ký và quản lý đặt sân thể thao hiện đại, trực quan, hỗ trợ giảng viên và sinh viên tối đa trong các hoạt động thể chất và giao lưu thi đấu.
                    </p>

                    <!-- Sports Types Indicators -->
                    <div class="sport-badges">
                        <div class="sport-item">
                            <i class="bx bx-football sport-icon"></i>
                            <span class="sport-label">Bóng Đá</span>
                        </div>
                        <div class="sport-item">
                            <i class="bx bx-basketball sport-icon"></i>
                            <span class="sport-label">Bóng Rổ</span>
                        </div>
                        <div class="sport-item">
                            <i class="bx bx-tennis-ball sport-icon"></i>
                            <span class="sport-label">Cầu Lông</span>
                        </div>
                        <div class="sport-item">
                            <i class="bx bx-trophy sport-icon"></i>
                            <span class="sport-label">Tennis / Đa Năng</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Footer -->
                <div class="visual-footer">
                    <span>© 2026 HUMG Sports Center. All rights reserved.</span>
                    <span>Version 2.0</span>
                </div>
            </div>
        </div>

        <!-- Form Side -->
        <div class="form-side">
            <div class="form-header">
                <span class="form-welcome">Chào mừng bạn quay lại!</span>
                <h2 class="form-title">Đăng Nhập Hệ Thống</h2>
                <p class="form-desc">Vui lòng nhập tài khoản HUMG của bạn để tiếp tục sử dụng dịch vụ.</p>
            </div>

            <!-- Error Alerts -->
            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="error-alert">
                <i class="bx bx-error-circle error-alert-icon"></i>
                <div class="error-alert-content">
                    <div class="error-alert-title">Lỗi Đăng Nhập!</div>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <div>• <?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>

            <!-- Form -->
            <form id="formAuthentication" action="process_login.php" method="POST">
                <!-- Username -->
                <div class="input-wrapper">
                    <div class="input-label-row">
                        <label for="email" class="input-label">Tên đăng nhập hoặc Email</label>
                    </div>
                    <div class="input-container">
                        <i class="bx bx-user input-icon"></i>
                        <input
                            type="text"
                            class="custom-input"
                            id="email"
                            name="email-username"
                            placeholder="Nhập tên đăng nhập hoặc email"
                            value="<?= isset($_SESSION['email_username']) ? htmlspecialchars($_SESSION['email_username']) : '' ?>"
                            required
                            autofocus
                        />
                    </div>
                    <?php if (isset($_SESSION['errors_field']['email'])): ?>
                    <div class="field-error">
                        <i class="bx bx-x-circle"></i>
                        <?= htmlspecialchars($_SESSION['errors_field']['email']) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="input-wrapper">
                    <div class="input-label-row">
                        <label for="password" class="input-label">Mật khẩu</label>
                    </div>
                    <div class="input-container">
                        <i class="bx bx-lock-alt input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            class="custom-input"
                            name="password"
                            placeholder="Nhập mật khẩu của bạn"
                            required
                        />
                        <button type="button" class="input-password-toggle" id="togglePassword">
                            <i class="bx bx-hide" id="eyeIcon"></i>
                        </button>
                    </div>
                    <?php if (isset($_SESSION['errors_field']['password'])): ?>
                    <div class="field-error">
                        <i class="bx bx-x-circle"></i>
                        <?= htmlspecialchars($_SESSION['errors_field']['password']) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">
                    <span>Đăng Nhập Ngay</span>
                    <i class="bx bx-right-arrow-alt" style="font-size: 20px;"></i>
                </button>
            </form>

            <?php
            unset($_SESSION['errors_field']);
            unset($_SESSION['email_username']);
            ?>
        </div>
    </div>

    <!-- Script to toggle password visibility -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const password = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (togglePassword && password && eyeIcon) {
                togglePassword.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    
                    if (type === 'text') {
                        eyeIcon.classList.remove('bx-hide');
                        eyeIcon.classList.add('bx-show');
                    } else {
                        eyeIcon.classList.remove('bx-show');
                        eyeIcon.classList.add('bx-hide');
                    }
                });
            }
        });
    </script>
</body>
</html>