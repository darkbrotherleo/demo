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
?>