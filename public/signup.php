<?php
// signup.php
// Điểm vào của trang signup

// Kết nối các tệp cần thiết từ includes (nếu cần)
require_once '../includes/config/config.php';
require_once '../includes/helpers/validation.php';

// Bắt đầu phiên làm việc (nếu cần lưu thông báo lỗi)
session_start();

// Biến để hiển thị thông báo lỗi (nếu có)
$error = isset($_SESSION['signup_error']) ? $_SESSION['signup_error'] : '';
unset($_SESSION['signup_error']); // Xóa thông báo sau khi hiển thị

// Gọi header
include 'header.php';
?>

<!-- Nội dung chính của trang signup -->
<div class="signup-container">
    <h2>Sign Up</h2>
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
    <form action="scripts/process_signup.php" method="POST">
        <div class="form-group">
            <label for="username">Tên Tài Khoản:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mật Mã:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Gõ Lại Mật Mã:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn-signup">Đăng Ký</button>
    </form>
    <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
</div>
<?php
// Gọi footer
include 'footer.php';
?>