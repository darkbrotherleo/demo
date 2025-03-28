<?php require_once '../admin/core/edit.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Sản Phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../admin/assets/css/edit.css">
</head>
<body>
    <div class="container">
        <h1>Chỉnh Sửa Thông Tin Sản Phẩm</h1>
        
        <?php if ($successMessage): ?>
        <div class="message success">
            <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
        <div class="message error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $errorMessage; ?>
        </div>
        <?php endif; ?>
        
        <div class="readonly-info">
            <p><strong>Mã Kích Hoạt:</strong> <?php echo htmlspecialchars($product['SerialNumber']); ?></p>
            <p><strong>Mã Bảo Hành:</strong> <?php echo htmlspecialchars($product['Code']); ?></p>
            <p><strong>Địa Chỉ IP:</strong> <?php echo htmlspecialchars($product['CheckIP'] ?? 'N/A'); ?></p>
            <p><strong>Thời Gian Kích Hoạt:</strong> <?php echo htmlspecialchars($product['CheckTime'] ?? 'N/A'); ?></p>
            <p><strong>Thời Gian Tạo:</strong> <?php echo htmlspecialchars($product['created_at'] ?? 'N/A'); ?></p>
            <p><strong>Cập Nhật Lần Cuối:</strong> <?php echo htmlspecialchars($product['updated_at'] ?? 'N/A'); ?></p>
        </div>
        
        <form id="editProductForm" method="POST" action="">
            <div class="form-group">
                <label for="customerName">Tên Khách Hàng:</label>
                <input type="text" id="customerName" name="customerName" value="<?php echo htmlspecialchars($product['CustomerName'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phoneNumber">Số Điện Thoại:</label>
                <input type="tel" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($product['PhoneNumber'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($product['Email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="purchaseLocation">Nơi Mua Hàng:</label>
                <select id="purchaseLocation" name="purchaseLocation">
                    <?php foreach ($purchaseLocations as $location): ?>
                        <option value="<?php echo $location; ?>" <?php echo ($product['PurchaseLocation'] === $location) ? 'selected' : ''; ?>>
                            <?php echo $location; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="cityProvince">Nơi Cư Ngụ:</label>
                <select id="cityProvince" name="cityProvince">
                    <?php foreach ($vietnamProvinces as $province): ?>
                        <option value="<?php echo $province; ?>" <?php echo ($product['CityProvince'] === $province) ? 'selected' : ''; ?>>
                            <?php echo $province; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group checkbox-container">
                <input type="checkbox" id="isChecked" name="isChecked" <?php echo ($product['IsChecked'] == 1) ? 'checked' : ''; ?>>
                <label for="isChecked">Đã Kích Hoạt</label>
            </div>
            
            <div class="buttons">
                <button type="button" class="btn-cancel" onclick="goBack()">Hủy Bỏ</button>
                <button type="submit" class="btn-save">Lưu Thay Đổi</button>
            </div>
        </form>
    </div>
    
    <script src="../admin/assets/js/edit.js"></script>
</body>
</html>
