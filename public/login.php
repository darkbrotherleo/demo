<?php
// login.php
// Điểm vào của trang login

// Kết nối các tệp cần thiết từ includes
require_once '../includes/config/config.php';
require_once '../includes/helpers/validation.php';

// Bắt đầu phiên làm việc để hiển thị thông báo lỗi (nếu có)
session_start();

// Biến để hiển thị thông báo lỗi
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']); // Xóa thông báo sau khi hiển thị

// Gọi header
include 'header.php';
?>

<!-- Nội dung chính của trang login -->
<div class="login-container">
    <h2>Đăng nhập</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="scripts/process_login.php" method="POST">
        <div class="form-group">
            <label for="username">Tên đăng nhập:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Mật khẩu:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-login">Đăng nhập</button>
    </form>
    <p>Chưa có tài khoản? <a href="signup.php">Đăng ký ngay</a></p>
</div>

<?php
// Gọi footer
include 'footer.php';
?>