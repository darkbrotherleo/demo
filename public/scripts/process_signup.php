<?php
// public/scripts/process_signup.php
include_once '../../includes/core/database.php';
include_once '../../includes/helpers/validation.php';

// Kiểm tra xem lớp Database có tồn tại không
if (!class_exists('Database')) {
    die("Class 'Database' not found. Check if includes/config/database.php is loaded correctly.");
}

// Lấy dữ liệu từ form
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Kiểm tra dữ liệu đầu vào
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    header("Location: ../signup.php?error=All fields are required");
    exit();
}

if ($password !== $confirm_password) {
    header("Location: ../signup.php?error=Passwords do not match");
    exit();
}

if (!validateEmail($email)) {
    header("Location: ../signup.php?error=Invalid email format");
    exit();
}

// Kiểm tra username hoặc email đã tồn tại chưa
$db = (new Database())->connect();
$stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$stmt->execute([$username, $email]);
if ($stmt->fetch()) {
    header("Location: ../signup.php?error=Username or email already exists");
    exit();
}

// Mã hóa mật khẩu
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Bắt đầu transaction
    $db->beginTransaction();

    // Chèn vào bảng users
    $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, status, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$username, $email, $password_hash, 0]); // Status = 1 (Active)
    $user_id = $db->lastInsertId();

    // Gán vai trò mặc định (User = 2)
    $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
    $stmt->execute([$user_id, 2]);

    // Commit transaction
    $db->commit();

    // Chuyển hướng thành công
    header("Location: ../login.php?signup=success");
    exit();
} catch (PDOException $e) {
    // Rollback nếu lỗi
    $db->rollBack();
    // Ghi log lỗi
    file_put_contents('../../storage/logs/error.log', 
        date('Y-m-d H:i:s') . " - Signup Error: " . $e->getMessage() . "\n", 
        FILE_APPEND
    );
    header("Location: ../signup.php?error=Database error occurred");
    exit();
}
?>