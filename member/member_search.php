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
                        <input type="text" name="customer_name" class="form-control" placeholder="Họ và tên" value="<?php echo htmlspecialchars($codeData['CustomerName']); ?>" required>
                        <input type="tel" name="customer_phone" class="form-control" placeholder="Số điện thoại" value="<?php echo htmlspecialchars($codeData['PhoneNumber']); ?>" required>
                        <input type="email" name="customer_email" class="form-control" placeholder="Email" value="<?php echo htmlspecialchars($codeData['Email']); ?>">
                        
                        <select name="purchase_location" class="form-control" required>
                            <option value="">Nơi mua hàng</option>
                            <option value="Website Happyskin" <?php echo ($codeData['PurchaseLocation'] == "Website Happyskin") ? "selected" : ""; ?>>Website Happyskin</option>
                            <option value="Shopee Happyskin" <?php echo ($codeData['PurchaseLocation'] == "Shopee Happyskin") ? "selected" : ""; ?>>Shopee Happyskin</option>
                            <option value="Lazada Happyskin" <?php echo ($codeData['PurchaseLocation'] == "Lazada Happyskin") ? "selected" : ""; ?>>Lazada Happyskin</option>
                            <option value="Tiki Happyskin" <?php echo ($codeData['PurchaseLocation'] == "Tiki Happyskin") ? "selected" : ""; ?>>Tiki Happyskin</option>
                            <option value="Tiktok Happyskin" <?php echo ($codeData['PurchaseLocation'] == "Tiktok Happyskin") ? "selected" : ""; ?>>Tiktok Happyskin</option>
                            <option value="Đại lý chính hãng" <?php echo ($codeData['PurchaseLocation'] == "Đại lý chính hãng") ? "selected" : ""; ?>>Đại lý chính hãng</option>
                            <option value="Khác" <?php echo ($codeData['PurchaseLocation'] == "Khác") ? "selected" : ""; ?>>Khác</option>
                        </select>

                        <select name="calc_shipping_provinces" id="calc_shipping_provinces" class="form-control" required>
                            <option value="">Tỉnh / Thành phố</option>
                        </select>
                        <input type="hidden" name="calc_shipping_provinces_text" id="calc_shipping_provinces_text" value="<?php echo htmlspecialchars($codeData['CityProvince']); ?>">
                        <select name="calc_shipping_district" id="calc_shipping_district" class="form-control" required>
                            <option value="">Quận / Huyện</option>
                        </select>

                        <button type="submit" name="update" class="btn-verify">Cập nhật</button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Form Thêm Mới -->
                <div class="verification-form-container">
                    <form method="POST" action="" class="verification-form">
                        <input type="text" name="new_code" class="form-control" placeholder="Mã Code" required>
                        <input type="text" name="new_serial_number" class="form-control" placeholder="Mã Kích Hoạt (Serial Number)" required>
                        <button type="submit" name="add_new" class="btn-verify">Thêm mới</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>