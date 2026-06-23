# Hướng Dẫn Cấu Hình Gửi Email (PHPMailer + Gmail SMTP)

File cấu hình: `admin/booking/send-mail-admin.php`

---

## 1. Vị Trí Cấu Hình

Có **3 chỗ** trong file cần thay đổi email khi chuyển sang tài khoản khác:

### Chỗ 1 — Email admin nhận thông báo đơn mới (dòng 17)
```php
function sendBookingNotificationToAdmin($bookingData) {
    return sendEmail(
        recipientEmail: 'duclong1619@gmail.com',  // ← ĐỔI EMAIL ADMIN TẠI ĐÂY
        recipientName: 'Admin',
        ...
    );
}
```

### Chỗ 2 — Tài khoản SMTP gửi đi (dòng 205-206)
```php
$mail->Username = 'duclong1619@gmail.com';       // ← ĐỔI EMAIL GỬI ĐI
$mail->Password = 'hgui vlby xcvv ahhx';         // ← ĐỔI APP PASSWORD
```

### Chỗ 3 — Tên hiển thị người gửi (dòng 212)
```php
$mail->setFrom('duclong1619@gmail.com', 'Hệ thống Quản lý Đặt Sân');
//              ↑ ĐỔI EMAIL               ↑ ĐỔI TÊN HIỂN THỊ
```

> **Lưu ý:** Chỗ 2 và Chỗ 3 phải dùng **cùng một email** (email đăng nhập SMTP = email setFrom).

---

## 2. Flow Gửi Mail Khi Admin Phê Duyệt

Khi admin bấm **"Phê duyệt"** tại trang `admin/approve/approve-room.php`, hệ thống gọi `handle-approve-room.php` và thực hiện:

```
Admin bấm Phê duyệt
        ↓
handle-approve-room.php
        ↓
UPDATE bookings SET status = 'da_duyet'
        ↓
require_once 'send-mail-admin.php'
        ↓
sendApprovalNotificationToUser($booking)   ← Hàm gửi mail về người dùng
        ↓
sendEmail(
    recipientEmail: $bookingData['user_email'],   ← Lấy email từ bảng users
    recipientName:  $bookingData['user_name'],
    messageType:    'approval'
)
        ↓
Email gửi đến người đặt sân
```

**Hàm chịu trách nhiệm:** `sendApprovalNotificationToUser()` — dòng 45 trong `send-mail-admin.php`

```php
function sendApprovalNotificationToUser($bookingData) {
    return sendEmail(
        recipientEmail: $bookingData['user_email'],  // Email lấy từ DB, không cần sửa
        recipientName: $bookingData['user_name'],
        bookingData: $bookingData,
        messageType: 'approval'
    );
}
```

---

## 3. Tất Cả Loại Email Hệ Thống Gửi

| Hàm | Trigger | Người nhận | Email đích |
|---|---|---|---|
| `sendBookingNotificationToAdmin()` | Có đơn Đặt Sân mới | Admin | Hard-coded dòng 17 |
| `sendPendingNotificationToUser()` | Đặt Sân thành công | Người đặt | `$bookingData['user_email']` |
| `sendApprovalNotificationToUser()` | **Admin phê duyệt** | **Người đặt** | `$bookingData['user_email']` |
| `sendRejectionNotificationToUser()` | Admin từ chối | Người đặt | `$bookingData['user_email']` |
| `sendCancellationNotificationToUser()` | Đơn bị hủy | Người đặt | `$bookingData['user_email']` |

> Email của người đặt (`user_email`) được lấy tự động từ bảng `users` trong DB — **không cần sửa trong code**.
> Chỉ có email Admin (dòng 17) là hard-coded cần sửa thủ công.

---

## 4. Lấy App Password Gmail (Bắt Buộc)

Gmail **không cho phép** dùng mật khẩu thông thường để gửi qua SMTP. Phải tạo **App Password**:

### Bước 1 — Bật xác minh 2 bước
1. Truy cập [https://myaccount.google.com/security](https://myaccount.google.com/security)
2. Mục **"Đăng nhập vào Google"** → bật **"Xác minh 2 bước"**

### Bước 2 — Tạo App Password
1. Truy cập [https://myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
2. Chọn ứng dụng: **"Thư"** (Mail)
3. Chọn thiết bị: **"Khác"** → đặt tên ví dụ `quan-ly-dat-san`
4. Nhấn **"Tạo"** → Google cấp mật khẩu 16 ký tự dạng `xxxx xxxx xxxx xxxx`
5. Sao chép vào `$mail->Password` (dòng 206)

---

## 5. Đổi Sang Nhà Cung Cấp Khác

Sửa tại hàm `sendEmail()` dòng 203–208:

### Outlook / Hotmail
```php
$mail->Host       = 'smtp.office365.com';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;
$mail->Username   = 'your@outlook.com';
$mail->Password   = 'your-password';
```

### Yahoo Mail
```php
$mail->Host       = 'smtp.mail.yahoo.com';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SSL;
$mail->Port       = 465;
$mail->Username   = 'your@yahoo.com';
$mail->Password   = 'your-app-password';
```