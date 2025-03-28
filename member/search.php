<?php
session_start();
require_once '../includes/core/database.php';

// Khởi tạo kết nối cơ sở dữ liệu
$database = new Database();
$pdo = $database->connect();

if (!$pdo) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}

// Kiểm tra bảng
$tableCheck = $pdo->query("SHOW TABLES LIKE 'checkproduct'");
if ($tableCheck->rowCount() == 0) {
    echo "Bảng checkproduct không tồn tại.";
    exit();
}

// Khởi tạo biến
$searchCode = isset($_POST['searchCode']) ? trim($_POST['searchCode']) : '';
$searchMessage = '';
$codeExists = false;
$codeData = null;

// Xử lý tìm kiếm mã Code
if ($searchCode) {
    $stmt = $pdo->prepare("SELECT * FROM checkproduct WHERE Code = :code");
    $stmt->bindValue(':code', $searchCode, PDO::PARAM_STR);
    $stmt->execute();
    $codeData = $stmt->fetch();

    if ($codeData) {
        $codeExists = true;
        $searchMessage = 'Đã tìm thấy mã Code.';
    } else {
        $searchMessage = 'Mã Code không tồn tại. Vui lòng nhập thông tin để thêm mới.';
    }
} else {
    $searchMessage = 'Vui lòng nhập mã Code để tìm kiếm.';
}

// Xử lý thêm mới mã Code
if (isset($_POST['add_new'])) {
    $newCode = trim($_POST['new_code'] ?? '');
    $newSerialNumber = trim($_POST['new_serial_number'] ?? '');

    if (empty($newCode) || empty($newSerialNumber)) {
        $searchMessage = 'Vui lòng điền đầy đủ thông tin.';
        $searchCode = $newCode;
    } else {
        try {
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM checkproduct WHERE Code = :code");
            $checkStmt->bindValue(':code', $newCode, PDO::PARAM_STR);
            $checkStmt->execute();
            if ($checkStmt->fetchColumn() > 0) {
                $searchMessage = 'Mã Code đã tồn tại. Vui lòng nhập mã khác.';
                $searchCode = $newCode;
            } else {
                $stmt = $pdo->prepare("INSERT INTO checkproduct (Code, SerialNumber, IsChecked, created_at) VALUES (:code, :serial, 0, NOW())");
                $stmt->bindValue(':code', $newCode, PDO::PARAM_STR);
                $stmt->bindValue(':serial', $newSerialNumber, PDO::PARAM_STR);
                $stmt->execute();
                $searchMessage = 'Thêm mã Code thành công!';
                $searchCode = '';
            }
        } catch (PDOException $e) {
            $searchMessage = 'Lỗi khi thêm mới: ' . $e->getMessage();
            $searchCode = $newCode;
            error_log("Error adding new code: " . $e->getMessage());
        }
    }
}

// Xử lý cập nhật mã Code
if (isset($_POST['update'])) {
    $updateCode = trim($_POST['code'] ?? '');
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $purchaseLocation = trim($_POST['purchase_location'] ?? '');
    $customerProvince = trim($_POST['calc_shipping_provinces_text'] ?? '');
    $customerDistrict = trim($_POST['calc_shipping_district'] ?? '');

    if (empty($customerName) || empty($customerPhone) || empty($customerProvince)) {
        $searchMessage = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
    } else {
        try {
            // Kiểm tra giá trị IsChecked hiện tại
            $checkStmt = $pdo->prepare("SELECT IsChecked FROM checkproduct WHERE Code = :code");
            $checkStmt->bindValue(':code', $updateCode, PDO::PARAM_STR);
            $checkStmt->execute();
            $currentIsChecked = $checkStmt->fetchColumn();

            // Xây dựng truy vấn UPDATE
            $updateParams = [
                ':name' => $customerName,
                ':phone' => $customerPhone,
                ':email' => $customerEmail,
                ':location' => $purchaseLocation,
                ':province' => $customerProvince,
                ':district' => $customerDistrict,
                ':code' => $updateCode
            ];

            // Nếu IsChecked = 0, cập nhật thành 1; nếu đã là 1 thì giữ nguyên
            if ($currentIsChecked == 0) {
                $stmt = $pdo->prepare(
                    "UPDATE checkproduct SET 
                    CustomerName = :name, 
                    PhoneNumber = :phone, 
                    Email = :email, 
                    PurchaseLocation = :location, 
                    CityProvince = :province, 
                    DistrictProvide = :district, 
                    IsChecked = 1, 
                    updated_at = NOW() 
                    WHERE Code = :code"
                );
            } else {
                $stmt = $pdo->prepare(
                    "UPDATE checkproduct SET 
                    CustomerName = :name, 
                    PhoneNumber = :phone, 
                    Email = :email, 
                    PurchaseLocation = :location, 
                    CityProvince = :province, 
                    DistrictProvide = :district, 
                    updated_at = NOW() 
                    WHERE Code = :code"
                );
            }

            $stmt->execute($updateParams);

            // Cập nhật thông báo
            $searchMessage = 'Cập nhật thông tin thành công!';
            if ($currentIsChecked == 0) {
                $searchMessage .= ' Mã Code đã được kích hoạt.';
            }

            // Cập nhật lại dữ liệu để hiển thị
            $stmt = $pdo->prepare("SELECT * FROM checkproduct WHERE Code = :code");
            $stmt->bindValue(':code', $updateCode, PDO::PARAM_STR);
            $stmt->execute();
            $codeData = $stmt->fetch();
            $searchCode = $updateCode;
            $codeExists = true;
        } catch (PDOException $e) {
            $searchMessage = 'Lỗi khi cập nhật: ' . $e->getMessage();
            error_log("Error updating code: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm Kiếm Mã Code | Emmié by happySkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff6b8b;
            --background-color: #fff5f7;
            --text-color: #333;
            --border-color: #ffe0e6;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: white;
            padding: 15px 5%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo img {
            height: 40px;
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 500;
            white-space: nowrap;
            padding: 5px 0;
        }

        .nav-links a:hover {
            color: var(--primary-color);
        }

        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
            background-color: white;
        }

        .search-form {
            display: flex;
            align-items: center;
            max-width: 600px;
            width: 90%;
        }

        .search-form input[type="text"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px 0 0 5px;
            font-size: 16px;
            outline: none;
        }

        .search-form button {
            padding: 12px 20px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-form button:hover {
            background-color: #ff4d73;
        }

        .result-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 5%;
            background-color: white;
            text-align: center;
        }

        .verification-form-container {
            background-color: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
            margin: 30px auto;
        }

        .verification-form {
            text-align: center;
        }

        .verification-form input, .verification-form select {
            display: block;
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 16px;
        }

        .verification-form input:focus, .verification-form select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 5px rgba(255,107,139,0.3);
        }

        .btn-verify {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 10px;
        }

        .btn-verify:hover {
            background-color: #ff4d73;
        }

        .message {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background-color: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        .message.error {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .header { flex-direction: column; padding: 15px 3%; }
            .logo { margin-bottom: 10px; }
            .nav-links { justify-content: center; margin-top: 10px; }
            .nav-links a { margin: 0 10px; font-size: 14px; }
            .search-container { height: 150px; }
            .verification-form-container { padding: 20px; }
            .footer { padding: 15px 10px; font-size: 12px; }
        }

        @media (max-width: 480px) {
            .search-form input[type="text"] { padding: 10px; font-size: 14px; }
            .search-form button { padding: 10px 15px; font-size: 14px; }
            .verification-form-container { width: 95%; padding: 15px; }
            .btn-verify { padding: 10px 20px; font-size: 14px; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Emmié by happySkin Logo">
        </div>
        <div class="nav-links">
            <a href="#">Trang chủ</a>
            <a href="#">Sản phẩm</a>
            <a href="#">Khuyến mãi</a>
            <a href="#">Về chúng tôi</a>
            <a href="#">Liên hệ</a>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-container">
        <form method="POST" action="" class="search-form">
            <input type="text" name="searchCode" placeholder="Nhập mã Code để tìm kiếm" value="<?php echo htmlspecialchars($searchCode); ?>" required>
            <button type="submit">Tìm</button>
        </form>
    </div>

    <!-- Result Section -->
    <div class="result-container">
        <?php if ($searchMessage): ?>
            <div class="message <?php echo $codeExists ? 'success' : 'error'; ?>">
                <?php echo $searchMessage; ?>
            </div>
        <?php endif; ?>

        <?php if ($searchCode): ?>
            <?php if ($codeExists): ?>
                <!-- Form Cập Nhật -->
                <div class="verification-form-container">
                    <form method="POST" action="" class="verification-form">
                        <input type="hidden" name="code" value="<?php echo htmlspecialchars($codeData['Code']); ?>">
                        <input type="text" name="customer_name" class="form-control" placeholder="Họ và tên" value="<?php echo htmlspecialchars($codeData['CustomerName'] ?? ''); ?>" required>
                        <input type="tel" name="customer_phone" class="form-control" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($codeData['PhoneNumber'] ?? ''); ?>" required>
                        <input type="email" name="customer_email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($codeData['Email'] ?? ''); ?>">
                        
                        <select name="purchase_location" class="form-control" required>
                            <option value="">Nơi mua hàng</option>
                            <option value="Website Happyskin" <?php echo ($codeData['PurchaseLocation'] ?? '') == "Website Happyskin" ? "selected" : ""; ?>>Website Happyskin</option>
                            <option value="Shopee Happyskin" <?php echo ($codeData['PurchaseLocation'] ?? '') == "Shopee Happyskin" ? "selected" : ""; ?>>Shopee Happyskin</option>
                            <option value="Lazada Happyskin" <?php echo ($codeData['PurchaseLocation'] ?? '') == "Lazada Happyskin" ? "selected" : ""; ?>>Lazada Happyskin</option>
                            <option value="Tiki Happyskin" <?php echo ($codeData['PurchaseLocation'] ?? '') == "Tiki Happyskin" ? "selected" : ""; ?>>Tiki Happyskin</option>
                            <option value="Tiktok Happyskin" <?php echo ($codeData['PurchaseLocation'] ?? '') == "Tiktok Happyskin" ? "selected" : ""; ?>>Tiktok Happyskin</option>
                            <option value="Đại lý chính hãng" <?php echo ($codeData['PurchaseLocation'] ?? '') == "Đại lý chính hãng" ? "selected" : ""; ?>>Đại lý chính hãng</option>
                            <option value="Khác" <?php echo ($codeData['PurchaseLocation'] ?? '') == "Khác" ? "selected" : ""; ?>>Khác</option>
                        </select>

                        <select name="calc_shipping_provinces" id="calc_shipping_provinces" class="form-control" required>
                            <option value="">Tỉnh / Thành phố</option>
                        </select>
                        <input type="hidden" name="calc_shipping_provinces_text" id="calc_shipping_provinces_text" value="<?php echo htmlspecialchars($codeData['CityProvince'] ?? ''); ?>">
                        <select name="calc_shipping_district" id="calc_shipping_district" class="form-control" required>
                            <option value="">Quận / Huyện</option>
                        </select>
                        <input type="hidden" id="saved_district" value="<?php echo htmlspecialchars($codeData['DistrictProvide'] ?? ''); ?>">

                        <button type="submit" name="update" class="btn-verify">Cập nhật</button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Form Thêm Mới -->
                <div class="verification-form-container">
                    <form method="POST" action="" class="verification-form">
                        <input type="text" name="new_code" class="form-control" placeholder="Mã Code" value="<?php echo htmlspecialchars($searchCode); ?>" required>
                        <input type="text" name="new_serial_number" class="form-control" placeholder="Mã Kích Hoạt (Serial Number)" value="<?php echo htmlspecialchars($_POST['new_serial_number'] ?? ''); ?>" required>
                        <button type="submit" name="add_new" class="btn-verify">Thêm mới</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Copyright © 2024 Emmié by Happyskin - Phân phối độc quyền bởi Happyskin Việt Nam - Công ty chủ quản : IBP Holdings. All rights reserved.</p>
    </div>

    <!-- JavaScript -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
    <script src='https://cdn.jsdelivr.net/gh/vietblogdao/js/districts.min.js'></script>
    <script>
    $(document).ready(function() {
        var $provinceSelect = $('select[name="calc_shipping_provinces"]');
        var $districtSelect = $('select[name="calc_shipping_district"]');
        var $provinceText = $('#calc_shipping_provinces_text');
        var savedProvince = $provinceText.val();
        var savedDistrict = $('#saved_district').val();

        // Tạo danh sách tỉnh/thành phố
        var stc = '';
        c.forEach(function(province, index) {
            index += 1;
            stc += '<option value="' + index + '" data-text="' + province + '">' + province + '</option>';
        });
        $provinceSelect.html('<option value="">Tỉnh / Thành phố</option>' + stc);

        // Chọn tỉnh/thành phố từ cơ sở dữ liệu
        if (savedProvince) {
            $provinceSelect.find('option').each(function() {
                if ($(this).data('text') === savedProvince) {
                    $(this).prop('selected', true);
                    $provinceText.val(savedProvince);

                    // Tạo danh sách quận/huyện dựa trên tỉnh đã chọn
                    var index = $(this).val() - 1;
                    var districtOptions = '';
                    if (index >= 0 && arr[index]) {
                        arr[index].forEach(function(district) {
                            districtOptions += '<option value="' + district + '">' + district + '</option>';
                        });
                    }
                    $districtSelect.html('<option value="">Quận / Huyện</option>' + districtOptions);

                    // Chọn quận/huyện từ cơ sở dữ liệu
                    if (savedDistrict) {
                        $districtSelect.find('option').each(function() {
                            if ($(this).val() === savedDistrict) {
                                $(this).prop('selected', true);
                            }
                        });
                    }
                }
            });
        }

        // Xử lý khi thay đổi tỉnh/thành phố
        $provinceSelect.on('change', function() {
            var selectedProvinceText = $(this).find('option:selected').data('text');
            $provinceText.val(selectedProvinceText);
            localStorage.setItem('address_1_saved', selectedProvinceText);

            var index = $(this).val() - 1;
            var districtOptions = '';
            if (index >= 0 && arr[index]) {
                arr[index].forEach(function(district) {
                    districtOptions += '<option value="' + district + '">' + district + '</option>';
                });
            }
            $districtSelect.html('<option value="">Quận / Huyện</option>' + districtOptions);
        });

        // Xử lý khi thay đổi quận/huyện
        $districtSelect.on('change', function() {
            var selectedDistrict = $(this).find('option:selected').val();
            localStorage.setItem('address_2_saved', selectedDistrict);
            localStorage.setItem('district', $(this).html());
        });
    });
    </script>
</body>
</html>