<?php
session_start();
// Kiểm tra xem có thông báo nào không
$message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';

// Xóa thông báo sau khi hiển thị
unset($_SESSION['message']);
unset($_SESSION['error']);
?>

<div class="content-header">
    <h2>Import Dữ Liệu Sản Phẩm</h2>
</div>

<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h3>Upload File CSV</h3>
        </div>
        <div class="card-body">
            <form action="./core/process_import.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="csv_file">Chọn file CSV:</label>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                </div>
                <div class="form-info">
                    <p>Lưu ý:</p>
                    <ul>
                        <li>File CSV phải có tiêu đề: SerialNumber,Code</li>
                        <li>Dữ liệu phải được định dạng UTF-8</li>
                        <li>Kích thước tối đa: 10MB</li>
                        <li><a href="../public/assets/templates/sample_import.csv" download class="btn btn-secondary">Tải file mẫu</a></li>
                    </ul>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>

                <!-- Hiển thị thông báo -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-success">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
