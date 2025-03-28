<?php
session_start();
require_once '../../includes/core/database.php';

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

$db = new Database();
$pdo = $db->connect();

$productCode = trim($_POST['product_code'] ?? '');
$customerName = trim($_POST['customer_name'] ?? '');
$customerPhone = trim($_POST['customer_phone'] ?? '');
$customerEmail = trim($_POST['customer_email'] ?? '');
$purchaseLocation = trim($_POST['purchase_location'] ?? '');
$customerProvince = trim($_POST['calc_shipping_provinces_text'] ?? '');
$customerDistrict = trim($_POST['calc_shipping_district'] ?? '');
$checkIP = getClientIP();

if (empty($customerName) || empty($customerPhone) || empty($productCode) || empty($customerProvince)) {
    $_SESSION['verification_status'] = 'error';
    $_SESSION['verification_message'] = 'Vui lòng điền đầy đủ thông tin bắt buộc';
} else {
    try {
        $stmt = $pdo->prepare("SELECT Code, IsChecked FROM checkproduct WHERE Code = ?");
        $stmt->execute([$productCode]);
        $result = $stmt->fetch();

        if (!$result) {
            $_SESSION['verification_status'] = 'error';
            $_SESSION['verification_message'] = 'Mã code không tồn tại trên hệ thống';
        } else {
            if ($result['IsChecked'] == 1) {
                $_SESSION['verification_status'] = 'warning';
                $_SESSION['verification_message'] = 'Code đã được kích hoạt trước đó';
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE checkproduct SET 
                    CustomerName = ?,
                    PhoneNumber = ?,
                    Email = ?,
                    PurchaseLocation = ?,
                    CityProvince = ?,
                    DistrictProvide = ?,
                    IsChecked = ?,
                    CheckIP = ?,
                    updated_at = NOW()
                    WHERE Code = ?"
                );
                $stmt->execute([
                    $customerName,
                    $customerPhone,
                    $customerEmail,
                    $purchaseLocation,
                    $customerProvince,
                    $customerDistrict,
                    1,
                    $checkIP,
                    $productCode
                ]);
                
                $_SESSION['verification_status'] = 'success';
                $_SESSION['verification_message'] = 'Xác nhận sản phẩm chính hãng! Mã voucher của bạn: EM' . rand(1000, 9999);
            }
        }
    } catch (PDOException $e) {
        $_SESSION['verification_status'] = 'error';
        $_SESSION['verification_message'] = 'Lỗi hệ thống: ' . $e->getMessage();
        error_log("Error: " . $e->getMessage());
    }
}

// Redirect back to check.php
header("Location: ../../index.php");
exit;