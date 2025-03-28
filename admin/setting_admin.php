<div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Thông báo -->
                <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo $successMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $errorMessage; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <!-- Card Cài đặt -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center">
                        <i class="fas fa-cogs setting-icon"></i>
                        <span>Cài Đặt Hệ Thống</span>
                        <?php if ($settings['no_index']): ?>
                        <span class="badge bg-warning text-dark noindex-badge">
                            <i class="fas fa-robot"></i> NoIndex Đang Bật
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Tên Website</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" 
                                       value="<?php echo htmlspecialchars($settings['site_name']); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="site_description" class="form-label">Mô tả Website</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="3"
                                ><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email_contact" class="form-label">Email Liên Hệ</label>
                                <input type="email" class="form-control" id="email_contact" name="email_contact" 
                                       value="<?php echo htmlspecialchars($settings['email_contact']); ?>">
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="no_index" name="no_index"
                                           <?php echo $settings['no_index'] ? 'checked' : ''; ?>
                                           data-bs-toggle="tooltip" data-bs-placement="right" 
                                           title="Khi bật, robots.txt sẽ chặn các công cụ tìm kiếm lập chỉ mục trang web">
                                    <label class="form-check-label" for="no_index">
                                        <strong>Không cho phép lập chỉ mục (NoIndex)</strong>
                                    </label>
                                </div>
                                <div class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle"></i> Khi bật tùy chọn này, các công cụ tìm kiếm sẽ không lập chỉ mục website của bạn.
                                    Các meta tag sau sẽ được thêm vào trang: <code>&lt;meta name="robots" content="noindex, nofollow"&gt;</code>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" name="save_settings" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu Cài Đặt
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Hướng dẫn sử dụng -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-question-circle setting-icon"></i>
                        Hướng dẫn thêm meta tag vào trang
                    </div>
                    <div class="card-body">
                        <p>Để áp dụng cài đặt NoIndex vào trang web của bạn, hãy thêm đoạn mã sau vào phần Head của các trang:</p>
                        
                        <div class="bg-light p-3 rounded mb-3">
                            <pre class="mb-0"><code>&lt;<?php
// Đọc file cấu hình
$settings = include '../includes/core/database.php';

// Thêm meta no-index nếu được bật
if ($settings['no_index']) {
    echo 'meta name="robots" content="noindex, nofollow"';
}
?></code></pre>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb"></i> <strong>Mẹo:</strong> Đặt đoạn mã này vào tệp header chung để tự động áp dụng cho tất cả các trang.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>