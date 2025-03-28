<?php
// Kiểm tra thông báo popup
$popup_message = isset($_SESSION['popup_message']) ? $_SESSION['popup_message'] : null;
$popup_error = isset($_SESSION['popup_error']) ? $_SESSION['popup_error'] : null;

// Xóa thông báo sau khi hiển thị
unset($_SESSION['popup_message']);
unset($_SESSION['popup_error']);
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($popup_message): ?>
            alert(`<?php echo nl2br($popup_message); ?>`);
        <?php elseif ($popup_error): ?>
            alert(`<?php echo nl2br($popup_error); ?>`);
        <?php endif; ?>
    });
</script>
<!-- container_admin.php -->
<?php require_once '../admin/edit_product.php'; ?>