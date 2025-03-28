<?php
session_start();
// Initialize variables
$verificationStatus = $_SESSION['verification_status'] ?? '';
$verificationMessage = $_SESSION['verification_message'] ?? '';
$customerName = '';
$customerPhone = '';
$customerEmail = '';
$purchaseLocation = '';
$customerProvince = '';
$customerDistrict = '';
$productCode = '';

// Clear session data after use
unset($_SESSION['verification_status']);
unset($_SESSION['verification_message']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm Tra Sản Phẩm Chính Hãng | Emmié by happySkin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./public/assets/css/check.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">
        <a href="../demo/index.php"><img src="./uploads/images/emmie.png" alt="Emmié by happySkin Logo"></a>
        </div>
        <div class="nav-links">
            <a href="../demo/index.php">Trang chủ</a>
            <a href="https://www.happyskin.vn/skinstore">Sản phẩm</a>
            <a href="https://www.happyskin.vn/sale-bat-chap">Khuyến mãi</a>
            <a href="https://www.happyskin.vn/gioi-thieu">Về chúng tôi</a>
            <a href="https://www.happyskin.vn/lien-he">Liên hệ</a>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="banner">
        <div class="banner-content">

            <img src="./uploads/images/Banner.png" alt="Emmié Product Verification" class="banner-image">
            
            <div class="qr-code">
                Tem Bạc Chứa Code Xác Thực
            </div>
            <img src="./uploads/images/tem.png" alt="Code Xác Thực">
            <div class="verification-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Tìm mã xác thực</h3>
                    <p>Tìm và cào lớp phủ bạc trên nhãn tem (hình bên trên) để lấy mã CODE xác thực.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Nhập mã vào form</h3>
                    <p>Điền đầy đủ thông tin cá nhân và nhập mã CODE vào ô kiểm tra bên dưới.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Xác nhận và nhận voucher</h3>
                    <p>Nhận kết quả xác thực và mã voucher ưu đãi khi mua sản phẩm tiếp theo.</p>
                </div>
            </div>
            
            <?php if (!empty($verificationStatus)): ?>
            <div class="notification-frame">
                <?php if ($verificationStatus === 'success'): ?>
                    <strong>THÔNG BÁO KÍCH HOẠT MÃ CHƯA KÍCH HOẠT:</strong><br>
                    <p style="color:red;font-weight:600;">Sản Phẩm Chính Hãng Của Emmié by HappySkin.</p>
                    <p style="color:red;font-weight:600;">Emmié gửi tặng Quý Khách Hàng mã giảm thêm 5% tối đa 15k khi mua sản phẩm Emmié tại website: <a href="https://happyskin.vn">happyskin.vn</a></p>
                    <em style="font-weight:600;">* Mã voucher giảm giá là mã code tại lớp tráng bạc của tem.</em>
                <?php elseif ($verificationStatus === 'warning'): ?>
                    <strong>THÔNG BÁO MÃ ĐÃ KÍCH HOẠT RỒI:</strong><br>
                    <p style="color:green;font-weight:600;">Sản Phẩm Đã Kiểm Tra Xác Thực Chính Hãng Trước Đó.</p>
                <?php else: ?>
                    <strong>THÔNG BÁO LỖI:</strong><br>
                    <p><?php echo $verificationMessage; ?></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="verification-form-container">
                <form method="POST" action="./public/scripts/process_check.php" class="verification-form">
                    <input type="text" name="customer_name" class="form-control" placeholder="Họ và tên" value="<?php echo htmlspecialchars($customerName); ?>" required>
                    <input type="tel" name="customer_phone" class="form-control" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($customerPhone); ?>" required>
                    <input type="email" name="customer_email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($customerEmail); ?>">
                    
                    <select name="purchase_location" class="form-control" required>
                        <option value="">Nơi mua hàng</option>
                        <option value="Website Happyskin" <?php echo ($purchaseLocation == "Website Happyskin") ? "selected" : ""; ?>>Website Happyskin</option>
                        <option value="Shopee Happyskin" <?php echo ($purchaseLocation == "Shopee Happyskin") ? "selected" : ""; ?>>Shopee Happyskin</option>
                        <option value="Lazada Happyskin" <?php echo ($purchaseLocation == "Lazada Happyskin") ? "selected" : ""; ?>>Lazada Happyskin</option>
                        <option value="Tiki Happyskin" <?php echo ($purchaseLocation == "Tiki Happyskin") ? "selected" : ""; ?>>Tiki Happyskin</option>
                        <option value="Tiktok Happyskin" <?php echo ($purchaseLocation == "Tiktok Happyskin") ? "selected" : ""; ?>>Tiktok Happyskin</option>
                        <option value="Đại lý chính hãng" <?php echo ($purchaseLocation == "Đại lý chính hãng") ? "selected" : ""; ?>>Đại lý chính hãng</option>
                        <option value="Khác" <?php echo ($purchaseLocation == "Khác") ? "selected" : ""; ?>>Khác</option>
                    </select>

                    <select name="calc_shipping_provinces" id="calc_shipping_provinces" class="form-control" required>
                        <option value="">Tỉnh / Thành phố</option>
                    </select>
                    <input type="hidden" name="calc_shipping_provinces_text" id="calc_shipping_provinces_text" value="<?php echo htmlspecialchars($customerProvince); ?>">
                    <select name="calc_shipping_district" id="calc_shipping_district" class="form-control" required>
                        <option value="">Quận / Huyện</option>
                    </select>

                    <input type="text" name="product_code" class="form-control" placeholder="Nhập mã CODE" value="<?php echo htmlspecialchars($productCode); ?>" required>
                    <button type="submit" name="verify" class="btn-verify">Kiểm tra</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="footer">
        <p>Copyright © 2024 Emmié by Happyskin - Phân phối độc quyền bởi Happyskin Việt Nam - Công ty chủ quản : IBP Holdings. All rights reserved.</p>
    </div>

    <!-- JavaScript libraries and code for province/district selection -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>
    <script src='https://cdn.jsdelivr.net/gh/vietblogdao/js/districts.min.js'></script>
    <script src='./public/assets/js/check.js'></script>
</body>
</html>