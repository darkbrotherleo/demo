<?php
// dashboard.php

session_start();

// Include header và menu
include './admin/core/process_search.php';
include 'header_admin.php';
include 'menu_admin.php';

?>

<!-- Container để hiển thị nội dung động -->
<div id="admin-container"></div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Thư viện SweetAlert -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (isset($popup_message)): ?>
            // Hiển thị thông báo thành công
            Swal.fire({
                title: 'Import thành công!',
                text: '<?php echo nl2br($popup_message); ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        <?php elseif (isset($popup_error)): ?>
            // Hiển thị thông báo lỗi
            Swal.fire({
                title: 'Lỗi!',
                text: '<?php echo nl2br($popup_error); ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>
    });
</script>

<?php
// Include footer
include 'footer_admin.php';
?>