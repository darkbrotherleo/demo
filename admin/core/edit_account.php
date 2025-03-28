<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT role_id FROM roles WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$role = $stmt->fetch();

if (!$role || $role['role_id'] != 1) {
    echo "Bạn không có quyền truy cập!";
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT role_id FROM roles WHERE user_id = ?");
$stmt->execute([$id]);
$current_role = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = isset($_POST['status']) ? 1 : 0;
    $role_id = $_POST['role_id'];

    $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    $stmt = $pdo->prepare("INSERT INTO roles (user_id, role_id) VALUES (?, ?) 
                           ON DUPLICATE KEY UPDATE role_id = ?");
    $stmt->execute([$id, $role_id, $role_id]);

    header('Location: manage_accounts.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh Sửa Tài Khoản</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form method="POST">
        <label for="id">ID:</label>
        <input type="text" id="id" value="<?= $user['id'] ?>" disabled><br>
        <label for="username">Tên Tài Khoản:</label>
        <input type="text" id="username" value="<?= $user['username'] ?>" disabled><br>
        <label for="email">Email:</label>
        <input type="email" id="email" value="<?= $user['email'] ?>" disabled><br>
        <label for="status">Trạng Thái:</label>
        <input type="checkbox" id="status" name="status" <?= $user['status'] ? 'checked' : '' ?>> Kích Hoạt<br>
        <label for="role_id">Vai Trò:</label><br>
        <input type="radio" id="admin" name="role_id" value="1" <?= $current_role['role_id'] == 1 ? 'checked' : '' ?>> Admin
        <input type="radio" id="member" name="role_id" value="2" <?= $current_role['role_id'] == 2 ? 'checked' : '' ?>> Member<br>
        <label for="created_at">Ngày Tạo:</label>
        <input type="text" id="created_at" value="<?= $user['created_at'] ?>" disabled><br>
        <label for="updated_at">Ngày Cập Nhật:</label>
        <input type="text" id="updated_at" value="<?= $user['updated_at'] ?>" disabled><br>
        <button type="submit">Lưu</button>
    </form>
</body>
</html>