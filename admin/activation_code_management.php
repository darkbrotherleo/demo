<?php
// Fetch activation codes with pagination
$offset = ($page - 1) * $perPage;
$stmt = $pdo->prepare("SELECT * FROM activation_codes LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$codes = $stmt->fetchAll();

// Total count for pagination
$totalStmt = $pdo->query("SELECT COUNT(*) FROM activation_codes");
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $perPage);
?>
<h2>Quản lý mã kích hoạt</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Mã</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($codes as $code): ?>
            <tr>
                <td><?php echo htmlspecialchars($code['code']); ?></td>
                <td><?php echo $code['status'] ? 'Đã kích hoạt' : 'Chưa kích hoạt'; ?></td>
                <td>
                    <a href="edit_code.php?id=<?php echo $code['id']; ?>&role=<?php echo urlencode($_SESSION['role_id']); ?>" class="btn btn-sm btn-primary">Sửa</a>
                    <a href="delete_code.php?id=<?php echo $code['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Pagination -->
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="#" class="paginate-link <?php echo $i == $page ? 'active' : ''; ?>" data-section="activation_codes" data-page="<?php echo $i; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>
<a href="add_code.php" class="btn btn-success">Thêm mã mới</a>