<?php
session_start(); // Khởi động phiên

// Hủy toàn bộ dữ liệu phiên
session_destroy();

// Xóa cookie nếu website sử dụng cookie để ghi nhớ đăng nhập (ví dụ: cookie 'user_id')
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, '/'); // Đặt thời gian hết hạn trong quá khứ để xóa cookie
}

// Chuyển hướng người dùng đến trang đăng nhập hoặc trang chủ
header('Location: ../../public/login.php');
exit;
?>