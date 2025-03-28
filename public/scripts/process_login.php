<?php
// public/scripts/process_login.php

// Bật hiển thị lỗi để debug (có thể tắt trên production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include các tệp cần thiết
include_once '../../includes/core/database.php';
include_once '../../includes/helpers/validation.php';

// Bắt đầu phiên làm việc
session_start();

// Kiểm tra sự tồn tại của file database.php
if (!file_exists('../../includes/core/database.php')) {
    die("File database.php not found at " . realpath('../../includes/core/'));
}

// Kiểm tra lớp Database có tồn tại không
if (!class_exists('Database')) {
    die("Class 'Database' not found. Check if includes/core/database.php is loaded correctly.");
}

// Lấy dữ liệu từ form
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Kiểm tra dữ liệu đầu vào
if (empty($username) || empty($password)) {
    $_SESSION['login_error'] = "Cả tên đăng nhập và mật khẩu đều bắt buộc";
    header("Location: ../login.php");
    exit();
}

try {
    // Kết nối cơ sở dữ liệu
    $db = (new Database())->connect();

    // Chuẩn bị truy vấn để tìm người dùng
    $stmt = $db->prepare("SELECT id, password_hash FROM users WHERE username = ? AND status = 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Kiểm tra xem người dùng có tồn tại không
    if (!$user) {
        $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng";
        header("Location: ../login.php");
        exit();
    }

    // Xác minh mật khẩu
    if (!password_verify($password, $user['password_hash'])) {
        $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng";
        header("Location: ../login.php");
        exit();
    }

    // Thiết lập biến session khi đăng nhập thành công
    $_SESSION['user_id'] = $user['id'];

    // Chuyển hướng đến trang chính
    header("Location: ../index.php?=".$role."");
    exit();
} catch (PDOException $e) {
    // Ghi log lỗi
    file_put_contents('../../storage/logs/error.log', 
        date('Y-m-d H:i:s') . " - Lỗi đăng nhập: " . $e->getMessage() . "\n", 
        FILE_APPEND
    );
    $_SESSION['login_error'] = "Đã xảy ra lỗi. Vui lòng thử lại sau.";
    header("Location: ../login.php");
    exit();
}
?>