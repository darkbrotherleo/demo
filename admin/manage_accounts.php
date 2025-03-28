<?php
// Fetch users with pagination
$offset = ($page - 1) * $perPage;
$stmt = $pdo->prepare("SELECT * FROM users LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$users = $stmt->fetchAll();

// Total count for pagination
$totalStmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $perPage);
?>
<h2>Quản lý người dùng</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Tên đăng nhập</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['role']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>&role=<?php echo urlencode($_SESSION['role_id']); ?>" class="btn btn-sm btn-primary">Sửa</a>
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="#" class="paginate-link <?php echo $i == $page ? 'active' : ''; ?>" data-section="users" data-page="<?php echo $i; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>
<a href="add_user.php" class="btn btn-success">Thêm người dùng mới</a>