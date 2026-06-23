-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2026 at 02:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quan_ly_dat_san`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_code` varchar(30) NOT NULL COMMENT 'Mã đặt sân duy nhất',
  `court_id` int(11) NOT NULL COMMENT 'ID sân được đặt',
  `user_id` int(11) NOT NULL COMMENT 'ID người đặt',
  `booking_date` date NOT NULL COMMENT 'Ngày sử dụng sân',
  `start_time` time NOT NULL COMMENT 'Giờ bắt đầu',
  `end_time` time NOT NULL COMMENT 'Giờ kết thúc',
  `purpose` varchar(500) DEFAULT NULL COMMENT 'Mục đích sử dụng',
  `participants` int(11) DEFAULT NULL COMMENT 'Số người tham dự',
  `contact_phone` varchar(15) DEFAULT NULL COMMENT 'Số điện thoại liên hệ',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú',
  `total_price` decimal(10,2) DEFAULT 0.00 COMMENT 'Tổng tiền thuê sân',
  `payment_status` enum('chua_thanh_toan','da_thanh_toan','hoan_tien') DEFAULT 'chua_thanh_toan' COMMENT 'Trạng thái thanh toán',
  `status` enum('cho_duyet','da_duyet','tu_choi','da_huy','hoan_thanh') DEFAULT 'cho_duyet' COMMENT 'Trạng thái đặt sân',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `rejected_by` int(11) DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `canceled_by` int(11) DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Lịch đặt sân của người dùng';

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_code`, `court_id`, `user_id`, `booking_date`, `start_time`, `end_time`, `purpose`, `participants`, `contact_phone`, `notes`, `total_price`, `payment_status`, `status`, `approved_by`, `approved_at`, `rejection_reason`, `rejected_by`, `rejected_at`, `cancel_reason`, `canceled_by`, `canceled_at`, `created_at`, `updated_at`) VALUES
(1, 'BK20260519001', 1, 2, '2026-05-20', '08:00:00', '10:00:00', 'Thi đấu giao hữu', 10, '0362494015', NULL, 300000.00, 'chua_thanh_toan', 'da_duyet', 1, '2026-05-26 00:15:20', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(2, 'BK20260519002', 3, 3, '2026-05-20', '14:00:00', '16:00:00', 'Luyện tập câu lạc bộ bóng rổ', 15, '0912345678', 'Cần mở điều hòa', 200000.00, 'chua_thanh_toan', 'cho_duyet', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(3, 'BK20260519003', 4, 4, '2026-05-21', '07:00:00', '08:00:00', 'Luyện tập cá nhân', 2, '0987654321', NULL, 60000.00, 'da_thanh_toan', 'da_duyet', 1, '2026-05-26 00:15:20', NULL, NULL, NULL, NULL, NULL, NULL, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(4, 'BK20260526022805346', 8, 1, '2026-05-27', '06:00:00', '22:00:00', 'chơi', 10, '0362494015', 'không', 0.00, 'chua_thanh_toan', 'da_huy', NULL, NULL, NULL, NULL, NULL, 'vvv', 1, '2026-05-26 00:31:20', '2026-05-26 00:28:05', '2026-05-26 00:31:20'),
(5, 'BK20260622023226423', 8, 1, '2026-06-22', '09:00:00', '11:00:00', 'chơi', 10, '0362494015', 'wwwwwwwww', 0.00, 'chua_thanh_toan', 'da_duyet', 1, '2026-06-23 00:33:23', NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-22 00:32:26', '2026-06-23 00:33:23');

-- --------------------------------------------------------

--
-- Table structure for table `courts`
--

CREATE TABLE `courts` (
  `id` int(11) NOT NULL,
  `court_code` varchar(20) NOT NULL COMMENT 'Mã sân (ví dụ BD01)',
  `court_name` varchar(100) NOT NULL COMMENT 'Tên sân',
  `category_id` int(11) NOT NULL COMMENT 'ID loại sân',
  `area` varchar(100) DEFAULT NULL COMMENT 'Khu vực (ví dụ: Khu thể thao A)',
  `surface_type` varchar(50) DEFAULT NULL COMMENT 'Loại mặt sân (cỏ nhân tạo, gỗ, bê tông...)',
  `capacity` int(11) DEFAULT NULL COMMENT 'Số người tối đa',
  `open_time` time DEFAULT '06:00:00' COMMENT 'Giờ mở cửa',
  `close_time` time DEFAULT '22:00:00' COMMENT 'Giờ đóng cửa',
  `facilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Danh sách thiết bị (JSON)' CHECK (json_valid(`facilities`)),
  `status` enum('trong','dang_su_dung','bao_tri') DEFAULT 'trong' COMMENT 'Trạng thái sân',
  `image_url` varchar(255) DEFAULT NULL COMMENT 'Ảnh sân',
  `is_active` tinyint(1) DEFAULT 1 COMMENT 'Sân còn hoạt động',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Danh sách các sân thể thao';

--
-- Dumping data for table `courts`
--

INSERT INTO `courts` (`id`, `court_code`, `court_name`, `category_id`, `area`, `surface_type`, `capacity`, `open_time`, `close_time`, `facilities`, `status`, `image_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'BD01', 'Sân bóng đá 5 người - 01', 1, 'Khu thể thao A', 'Cỏ nhân tạo', 10, '06:00:00', '22:00:00', '[\"Khung thành\",\"Lưới\",\"Đèn chiếu sáng\",\"Phòng thay đồ\"]', 'trong', NULL, 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(2, 'BD02', 'Sân bóng đá 5 người - 02', 1, 'Khu thể thao A', 'Cỏ nhân tạo', 10, '06:00:00', '22:00:00', '[\"Khung thành\",\"Lưới\",\"Đèn chiếu sáng\"]', 'trong', NULL, 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(3, 'BR01', 'Sân bóng rổ - 01', 2, 'Nhà thi đấu', 'Gỗ', 20, '07:00:00', '21:00:00', '[\"Bảng rổ\",\"Vạch kẻ sân\",\"Đèn chiếu sáng\",\"Máy lạnh\"]', 'trong', NULL, 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(4, 'CL01', 'Sân cầu lông - 01', 3, 'Nhà thi đấu', 'Gỗ', 4, '06:00:00', '22:00:00', '[\"Lưới cầu lông\",\"Đèn chiếu sáng\"]', 'trong', NULL, 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(5, 'CL02', 'Sân cầu lông - 02', 3, 'Nhà thi đấu', 'Gỗ', 4, '06:00:00', '22:00:00', '[\"Lưới cầu lông\",\"Đèn chiếu sáng\"]', 'bao_tri', NULL, 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(6, 'TN01', 'Sân tennis - 01', 4, 'Khu thể thao B', 'Bê tông', 4, '06:00:00', '20:00:00', '[\"Lưới tennis\",\"Đèn chiếu sáng\",\"Ghế nghỉ\"]', 'trong', NULL, 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(7, 'DA01', 'Sân đa năng - 01', 7, 'Khu thể thao B', 'Nhựa tổng hợp', 30, '07:00:00', '21:00:00', '[\"Đèn chiếu sáng\",\"Khán đài nhỏ\",\"Loa thông báo\"]', 'trong', NULL, 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(8, 'TD0011', 'Sân thể dục', 1, 'nhà A', 'cỏ', 11, '06:00:00', '22:00:00', '1111', 'trong', '', 1, '2026-05-26 00:21:33', '2026-05-26 00:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `court_categories`
--

CREATE TABLE `court_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL COMMENT 'Tên loại sân',
  `description` text DEFAULT NULL COMMENT 'Mô tả loại sân',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Loại sân (bóng đá, bóng rổ, cầu lông...)';

--
-- Dumping data for table `court_categories`
--

INSERT INTO `court_categories` (`id`, `category_name`, `description`, `created_at`) VALUES
(1, 'Sân bóng đá', 'Sân bóng đá 5 người, 7 người hoặc 11 người', '2026-05-26 00:15:18'),
(2, 'Sân bóng rổ', 'Sân bóng rổ tiêu chuẩn trong nhà', '2026-05-26 00:15:18'),
(3, 'Sân cầu lông', 'Sân cầu lông đơn hoặc đôi', '2026-05-26 00:15:18'),
(4, 'Sân tennis', 'Sân tennis tiêu chuẩn', '2026-05-26 00:15:18'),
(5, 'Sân bóng chuyền', 'Sân bóng chuyền trong nhà hoặc ngoài trời', '2026-05-26 00:15:18'),
(6, 'Sân bơi', 'Hồ bơi tiêu chuẩn', '2026-05-26 00:15:18'),
(7, 'Sân đa năng', 'Sân có thể sử dụng cho nhiều môn thể thao', '2026-05-26 00:15:18');

-- --------------------------------------------------------

--
-- Table structure for table `court_maintenance`
--

CREATE TABLE `court_maintenance` (
  `id` int(11) NOT NULL,
  `court_id` int(11) NOT NULL COMMENT 'Sân đang bảo trì',
  `start_date` date DEFAULT NULL COMMENT 'Ngày bắt đầu',
  `end_date` date DEFAULT NULL COMMENT 'Ngày kết thúc',
  `reason` varchar(500) DEFAULT NULL COMMENT 'Lý do',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết',
  `status` enum('dang_bao_tri','hoan_thanh','huy') DEFAULT 'dang_bao_tri',
  `reported_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Lịch bảo trì sân';

--
-- Dumping data for table `court_maintenance`
--

INSERT INTO `court_maintenance` (`id`, `court_id`, `start_date`, `end_date`, `reason`, `description`, `status`, `reported_by`, `created_at`, `updated_at`) VALUES
(0, 5, '2026-05-19', '2026-05-25', 'Thay mặt sân gỗ bị hỏng', NULL, 'dang_bao_tri', 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Tiêu đề',
  `content` text NOT NULL COMMENT 'Nội dung',
  `image_url` varchar(255) DEFAULT NULL COMMENT 'Ảnh đại diện',
  `created_by` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('hien_thi','an') DEFAULT 'hien_thi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tin tức / thông báo';

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `content`, `image_url`, `created_by`, `created_at`, `updated_at`, `status`) VALUES
(3, 'Kịch tính Champions League: Siêu phẩm móc bóng quyết định tấm vé vào Chung kết', '<p>Đêm qua, trận Bán kết lượt về cúp C1 châu Âu đã diễn ra vô cùng kịch tính và giàu cảm xúc. Với quyết tâm lội ngược dòng, đội chủ nhà đã dồn lên tấn công ngay từ những phút đầu tiên và tạo ra liên tiếp các cơ hội nguy hiểm trước khung thành đối phương.</p>\r\n<h4>Bước ngoặt phút bù giờ quyết định</h4>\r\n<p>Bước ngoặt của trận đấu chỉ xuất hiện vào phút bù giờ thứ 3 của hiệp 2. Từ một quả tạt bóng chính xác bên hành lang cánh phải, tiền đạo mang áo số 9 đã thực hiện cú dứt điểm ngẫu hứng: bay người móc bóng (xe đạp chổng ngược) đưa bóng găm thẳng vào góc chữ A của khung thành, đánh bại hoàn toàn thủ môn đội bạn.</p>\r\n<p>Bàn thắng siêu phẩm không chỉ ấn định tỷ số trận đấu mà còn chính thức đưa đội nhà giành tấm vé lịch sử bước vào trận Chung kết vào tháng sau tại Luân Đôn. Đây chắc chắn sẽ là một trong những ứng cử viên nặng ký cho giải thưởng Puskas năm nay.</p>', 'uploads/news/soccer_match.png', 1, '2026-05-26 07:46:08', '2026-05-26 07:46:08', 'hien_thi'),
(4, 'Siêu sao NBA thiết lập kỷ lục mới với cú Triple-Double lịch sử', '<p>Giải bóng rổ nhà nghề Mỹ NBA tiếp tục chứng kiến màn trình diễn không tưởng của siêu sao hàng đầu giải đấu. Trong trận derby bang vô cùng căng thẳng, anh đã một mình gồng gánh toàn đội vượt qua những thời khắc khó khăn nhất.</p>\r\n<ul>\r\n  <li>Ghi tổng cộng 52 điểm trong cả trận đấu.</li>\r\n  <li>Có 15 lần kiến tạo cơ hội ghi điểm cho đồng đội.</li>\r\n  <li>Giành 12 pha bắt bóng bật bảng thành công (Rebound).</li>\r\n</ul>\r\n<h4>Cú úp rổ uy lực phá vỡ thế bế tắc</h4>\r\n<p>Điểm nhấn lớn nhất là cú úp rổ (slam dunk) sấm sét ở hiệp phụ thứ 2, làm rung chuyển cả rổ đấu và khiến toàn bộ khán giả tại nhà thi đấu vỡ òa cảm xúc. Với chiến thắng nghẹt thở này, đội bóng của anh đã chính thức củng cố vị trí dẫn đầu bảng xếp hạng miền Tây và sẵn sàng bước vào vòng Play-offs kịch tính phía trước.</p>', 'uploads/news/basketball_dunk.png', 1, '2026-05-26 07:46:08', '2026-05-26 07:46:08', 'hien_thi'),
(5, 'Thần đồng Quần vợt 19 tuổi đăng quang ngôi vô địch Roland Garros', '<p>Giải quần vợt đất nện danh giá Roland Garros năm nay đã khép lại với chức vô địch đơn nam đầy bất ngờ thuộc về tay vợt trẻ mới 19 tuổi. Đối đầu với cựu vương giàu kinh nghiệm trong trận chung kết kéo dài hơn 4 giờ đồng hồ, tay vợt trẻ đã chứng minh bản lĩnh thép và thể lực bền bỉ phi thường.</p>\r\n<h4>Vũ khí giao bóng sấm sét và những cú thuận tay hiểm hóc</h4>\r\n<p>Sở hữu những cú giao bóng uy lực với tốc độ trung bình lên đến 210 km/h, tay vợt trẻ đã liên tiếp bẻ giao bóng của đối thủ ở những game quyết định. Sự bền bỉ cùng chiến thuật linh hoạt đã giúp anh giành chiến thắng chung cuộc sau 5 set đấu nghẹt thở với các tỷ số lần lượt là 6-4, 3-6, 7-5, 2-6, 6-3.</p>\r\n<p>Với chiến tích này, anh chính thức ghi tên mình vào lịch sử giải đấu với tư cách là nhà vô địch nam trẻ tuổi nhất trong vòng hai thập kỷ qua, báo hiệu một kỷ nguyên mới của làng quần vợt thế giới.</p>', 'uploads/news/tennis_serve.png', 1, '2026-05-26 07:46:08', '2026-05-26 07:46:08', 'hien_thi'),
(6, 'Giải Marathon Thế giới: Kỷ lục thế giới mới được thiết lập đầy thuyết phục', '<p>Tại giải chạy việt dã quốc tế Berlin Marathon diễn ra sáng nay, vận động viên huyền thoại người Kenya đã một lần nữa làm nên lịch sử khi bảo vệ thành công ngôi vô địch và phá vỡ kỷ lục thế giới cũ do chính mình nắm giữ.</p>\r\n<h4>Bứt tốc ngoạn mục tại vạch đích</h4>\r\n<p>Trong suốt 40 km đầu tiên, nhóm dẫn đầu bám đuổi nhau vô cùng gắt gao. Tuy nhiên, khi chỉ cách vạch đích 2 km cuối cùng, vận động viên 34 tuổi đã có màn bứt tốc ngoạn mục, bỏ xa đối thủ bám đuổi phía sau và băng qua vạch đích trong tiếng reo hò của hàng vạn khán giả.</p>\r\n<p>Thành tích mới thiết lập là 2 giờ 00 phút 35 giây, rút ngắn hơn 30 giây so với kỷ lục cũ. Đây được xem là cột mốc vĩ đại đưa giới hạn chịu đựng của con người lên một tầm cao mới.</p>', 'uploads/news/sprinters_finish.png', 1, '2026-05-26 07:46:08', '2026-05-26 07:46:08', 'hien_thi'),
(7, 'Kình ngư Việt Nam tỏa sáng rực rỡ tại giải vô địch bơi lội thế giới', '<p>Niềm tự hào thể thao nước nhà đã được thắp sáng tại đấu trường quốc tế khi kình ngư trẻ của Việt Nam xuất sắc giành tấm Huy chương Vàng lịch sử ở cự ly bơi bướm 200m nam tại Giải vô địch Bơi lội Thế giới đang diễn ra ở Nhật Bản.</p><p>Cú lội ngược dòng ấn tượng dưới làn nước xanh</p><p>Xuất phát ở làn bơi số 4 bên cạnh các đối thủ cực mạnh đến từ Mỹ và Úc, đại diện Việt Nam đã giữ nhịp thở và phân phối sức vô cùng hợp lý. Ở 50m cuối cùng, bằng những cú quạt nước đầy mạnh mẽ và kỹ thuật nhô người bướm hoàn hảo, anh đã bứt phá ngoạn mục để cán đích đầu tiên với thành tích 1 phút 53 giây 88.</p><p>Chiến thắng này không chỉ giúp anh mang về tấm huy chương vàng quý giá mà còn chính thức đạt chuẩn tham dự Thế vận hội Olympic diễn ra vào năm tới.</p>', 'uploads/news/swimming_butterfly.png', 1, '2026-05-26 07:46:08', '2026-05-26 21:59:20', 'hien_thi');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Người nhận',
  `booking_id` int(11) DEFAULT NULL COMMENT 'Liên quan đến booking nào',
  `type` enum('duyet','tu_choi','nhac_nho','thay_doi','huy') NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_sent_email` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Thông báo người dùng';

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `role` enum('admin','giang_vien','sinh_vien') NOT NULL,
  `permission_key` varchar(100) NOT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `role`, `permission_key`, `active`, `created_at`, `updated_at`) VALUES
(1, 'giang_vien', 'view_court_category', 1, '2026-05-26 00:15:20', NULL),
(2, 'giang_vien', 'add_court_category', 1, '2026-05-26 00:15:20', NULL),
(3, 'giang_vien', 'edit_court_category', 1, '2026-05-26 00:15:20', NULL),
(4, 'giang_vien', 'delete_court_category', 1, '2026-05-26 00:15:20', NULL),
(5, 'sinh_vien', 'view_court_category', 1, '2026-05-26 00:15:20', NULL),
(6, 'sinh_vien', 'add_court_category', 0, '2026-05-26 00:15:20', NULL),
(7, 'sinh_vien', 'edit_court_category', 0, '2026-05-26 00:15:20', NULL),
(8, 'sinh_vien', 'delete_court_category', 0, '2026-05-26 00:15:20', NULL),
(9, 'giang_vien', 'view_court', 1, '2026-05-26 00:15:20', NULL),
(10, 'giang_vien', 'add_court', 1, '2026-05-26 00:15:20', NULL),
(11, 'giang_vien', 'edit_court', 1, '2026-05-26 00:15:20', NULL),
(12, 'giang_vien', 'delete_court', 1, '2026-05-26 00:15:20', NULL),
(13, 'sinh_vien', 'view_court', 1, '2026-05-26 00:15:20', NULL),
(14, 'sinh_vien', 'add_court', 0, '2026-05-26 00:15:20', NULL),
(15, 'sinh_vien', 'edit_court', 0, '2026-05-26 00:15:20', NULL),
(16, 'sinh_vien', 'delete_court', 0, '2026-05-26 00:15:20', NULL),
(17, 'giang_vien', 'create_booking', 1, '2026-05-26 00:15:20', NULL),
(18, 'giang_vien', 'cancel_booking', 1, '2026-05-26 00:15:20', NULL),
(19, 'giang_vien', 'approve_booking', 0, '2026-05-26 00:15:20', NULL),
(20, 'giang_vien', 'view_history', 1, '2026-05-26 00:15:20', NULL),
(21, 'sinh_vien', 'create_booking', 1, '2026-05-26 00:15:20', NULL),
(22, 'sinh_vien', 'cancel_booking', 1, '2026-05-26 00:15:20', NULL),
(23, 'sinh_vien', 'approve_booking', 0, '2026-05-26 00:15:20', NULL),
(24, 'sinh_vien', 'view_history', 1, '2026-05-26 00:15:20', NULL),
(25, 'giang_vien', 'view_user', 1, '2026-05-26 00:15:20', NULL),
(26, 'giang_vien', 'add_user', 1, '2026-05-26 00:15:20', NULL),
(27, 'giang_vien', 'edit_user', 1, '2026-05-26 00:15:20', NULL),
(28, 'giang_vien', 'delete_user', 1, '2026-05-26 00:15:20', NULL),
(29, 'sinh_vien', 'view_user', 1, '2026-05-26 00:15:20', NULL),
(30, 'sinh_vien', 'add_user', 0, '2026-05-26 00:15:20', NULL),
(31, 'sinh_vien', 'edit_user', 0, '2026-05-26 00:15:20', NULL),
(32, 'sinh_vien', 'delete_user', 0, '2026-05-26 00:15:20', NULL),
(33, 'giang_vien', 'view_news', 1, '2026-05-26 00:15:20', NULL),
(34, 'giang_vien', 'add_news', 1, '2026-05-26 00:15:20', NULL),
(35, 'giang_vien', 'edit_news', 1, '2026-05-26 00:15:20', NULL),
(36, 'giang_vien', 'delete_news', 1, '2026-05-26 00:15:20', NULL),
(37, 'sinh_vien', 'view_news', 1, '2026-05-26 00:15:20', NULL),
(38, 'sinh_vien', 'add_news', 0, '2026-05-26 00:15:20', NULL),
(39, 'sinh_vien', 'edit_news', 0, '2026-05-26 00:15:20', NULL),
(40, 'sinh_vien', 'delete_news', 0, '2026-05-26 00:15:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('admin','giang_vien','sinh_vien') NOT NULL,
  `department` varchar(100) DEFAULT NULL COMMENT 'Khoa hoặc phòng ban',
  `student_code` varchar(20) DEFAULT NULL COMMENT 'Mã sinh viên',
  `employee_code` varchar(20) DEFAULT NULL COMMENT 'Mã giảng viên',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Thông tin người dùng';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `avatar`, `full_name`, `email`, `phone`, `role`, `department`, `student_code`, `employee_code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, 'System Administrator', 'admin@system.com', '0123456789', 'admin', 'Ban Quản trị', '', '', 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(2, 'sv_long', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, 'Phạm Đức Long', 'long@student.edu.vn', '0362494015', 'sinh_vien', 'Khoa CNTT', 'SV001001', '', 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(3, 'sv_an', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, 'Nguyễn Văn An', 'an@student.edu.vn', '0912345678', 'sinh_vien', 'Khoa Kinh tế', 'SV001002', '', 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20'),
(4, 'gv_hung', '7c4a8d09ca3762af61e59520943dc26494f8941b', NULL, 'Trần Văn Hùng', 'hung@teacher.edu.vn', '0987654321', 'giang_vien', 'Khoa CNTT', '', 'GV001', 1, '2026-05-26 00:15:20', '2026-05-26 00:15:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `fk_booking_court` (`court_id`),
  ADD KEY `fk_booking_user` (`user_id`),
  ADD KEY `fk_booking_approved_by` (`approved_by`);

--
-- Indexes for table `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `court_code` (`court_code`),
  ADD KEY `fk_courts_category` (`category_id`);

--
-- Indexes for table `court_categories`
--
ALTER TABLE `court_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `court_maintenance`
--
ALTER TABLE `court_maintenance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_maintenance_court` (`court_id`),
  ADD KEY `fk_maintenance_user` (`reported_by`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notify_user` (`user_id`),
  ADD KEY `fk_notify_booking` (`booking_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role`,`permission_key`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `courts`
--
ALTER TABLE `courts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `court_categories`
--
ALTER TABLE `court_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `court_maintenance`
--
ALTER TABLE `court_maintenance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_booking_court` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`),
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `courts`
--
ALTER TABLE `courts`
  ADD CONSTRAINT `fk_courts_category` FOREIGN KEY (`category_id`) REFERENCES `court_categories` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `court_maintenance`
--
ALTER TABLE `court_maintenance`
  ADD CONSTRAINT `fk_maintenance_court` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`),
  ADD CONSTRAINT `fk_maintenance_user` FOREIGN KEY (`reported_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notify_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `fk_notify_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
