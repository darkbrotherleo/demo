<?php
require_once '../includes/core/database.php';

$database = new Database();
$pdo = $database->connect();

if (!$pdo) {
    die("Không thể kết nối đến cơ sở dữ liệu.");
}

// Kiểm tra xem đã nhận được SerialNumber và Code chưa
if (!isset($_GET['serial']) || !isset($_GET['code'])) {
    die("Thiếu thông tin cần thiết để chỉnh sửa.");
}

$serialNumber = $_GET['serial'];
$code = $_GET['code'];

// Xử lý form khi được gửi đi
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Lấy dữ liệu từ form
        $customerName = $_POST['customerName'] ?? '';
        $phoneNumber = $_POST['phoneNumber'] ?? '';
        $email = $_POST['email'] ?? '';
        $purchaseLocation = $_POST['purchaseLocation'] ?? '';
        $cityProvince = $_POST['cityProvince'] ?? '';
        $isChecked = isset($_POST['isChecked']) ? 1 : 0;
        
        // Cập nhật dữ liệu
        $updateStmt = $pdo->prepare("
            UPDATE checkproduct 
            SET CustomerName = :customerName,
                PhoneNumber = :phoneNumber,
                Email = :email,
                PurchaseLocation = :purchaseLocation,
                CityProvince = :cityProvince,
                IsChecked = :isChecked,
                updated_at = NOW()
            WHERE SerialNumber = :serialNumber AND Code = :code
        ");
        
        $updateStmt->bindParam(':customerName', $customerName);
        $updateStmt->bindParam(':phoneNumber', $phoneNumber);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->bindParam(':purchaseLocation', $purchaseLocation);
        $updateStmt->bindParam(':cityProvince', $cityProvince);
        $updateStmt->bindParam(':isChecked', $isChecked);
        $updateStmt->bindParam(':serialNumber', $serialNumber);
        $updateStmt->bindParam(':code', $code);
        
        $updateStmt->execute();
        
        $successMessage = "Cập nhật thành công!";
        header("Location: dashboard.php");
    } catch (PDOException $e) {
        $errorMessage = "Lỗi: " . $e->getMessage();
    }
}

// Lấy dữ liệu hiện tại của sản phẩm
$stmt = $pdo->prepare("
    SELECT * FROM checkproduct 
    WHERE SerialNumber = :serialNumber AND Code = :code
");
$stmt->bindParam(':serialNumber', $serialNumber);
$stmt->bindParam(':code', $code);
$stmt->execute();

$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Không tìm thấy sản phẩm với thông tin đã cung cấp.");
}

// Danh sách 64 tỉnh thành Việt Nam
$vietnamProvinces = [
    "An Giang", "Bà Rịa - Vũng Tàu", "Bắc Giang", "Bắc Kạn", "Bạc Liêu", 
    "Bắc Ninh", "Bến Tre", "Bình Định", "Bình Dương", "Bình Phước", 
    "Bình Thuận", "Cà Mau", "Cần Thơ", "Cao Bằng", "Đà Nẵng", 
    "Đắk Lắk", "Đắk Nông", "Điện Biên", "Đồng Nai", "Đồng Tháp", 
    "Gia Lai", "Hà Giang", "Hà Nam", "Hà Nội", "Hà Tĩnh", 
    "Hải Dương", "Hải Phòng", "Hậu Giang", "Hòa Bình", "Hưng Yên", 
    "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu", "Lâm Đồng", 
    "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An", 
    "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", "Quảng Bình", 
    "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng", 
    "Sơn La", "Tây Ninh", "Thái Bình", "Thái Nguyên", "Thanh Hóa", 
    "Thừa Thiên Huế", "Tiền Giang", "TP Hồ Chí Minh", "Trà Vinh", "Tuyên Quang", 
    "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"
];

// Danh sách các lựa chọn cho nơi mua hàng
$purchaseLocations = [
    "Website Happyskin",
    "Tiktok Happyskin",
    "Shopee Happyskin",
    "Lazada Happyskin",
    "Đại Lý Happyskin"
];
?>