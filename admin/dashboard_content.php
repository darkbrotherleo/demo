<?php
require_once '../includes/core/database.php';

// Kết nối database
$database = new Database();
$pdo = $database->connect();

if (!$pdo) {
    echo "Không thể kết nối đến cơ sở dữ liệu.";
    exit();
}

// Cấu hình phân trang
$perPageOptions = [10, 20, 30, 50, 100];
$perPage = isset($_GET['perPage']) && in_array((int)$_GET['perPage'], $perPageOptions) ? (int)$_GET['perPage'] : 10;
if ($perPage < 1) $perPage = 10;

// Lấy tổng số bản ghi
$totalStmt = $pdo->prepare("SELECT COUNT(*) FROM checkproduct");
$totalStmt->execute();
$totalRows = $totalStmt->fetchColumn();

// Tính tổng số trang
$totalPages = ceil($totalRows / $perPage);

// Xử lý trang hiện tại
$currentPage = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
if ($totalPages > 0 && $currentPage > $totalPages) {
    $currentPage = $totalPages;
}
$offset = ($currentPage - 1) * $perPage;

// Lấy dữ liệu cho trang hiện tại
$stmt = $pdo->prepare("SELECT SerialNumber, Code, CustomerName, PhoneNumber, Email, PurchaseLocation, CityProvince, IsChecked 
                       FROM checkproduct 
                       LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll();
?>

<!-- HTML của bảng và phân trang -->
<?php if ($totalRows == 0): ?>
    <p>Không có dữ liệu để hiển thị.</p>
<?php else: ?>
    <table class="data-table">
        <tr>
            <th>Mã Kích Hoạt</th>
            <th>Mã Bảo Hành</th>
            <th>Tên Khách Hàng</th>
            <th>Số Điện Thoại</th>
            <th>Email</th>
            <th>Nơi Mua Hàng</th>
            <th>Nơi Sống</th>
            <th>Trạng Thái</th>
            <th><i class="fa-solid fa-sliders-up"></i></th>
        </tr>
        <?php foreach ($results as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['SerialNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['Code']); ?></td>
                <td><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                <td><?php echo htmlspecialchars($row['PhoneNumber']); ?></td>
                <td><?php echo htmlspecialchars($row['Email']); ?></td>
                <td><?php echo htmlspecialchars($row['PurchaseLocation']); ?></td>
                <td><?php echo htmlspecialchars($row['CityProvince']); ?></td>
                <td><?php echo $row['IsChecked'] == 1 ? '<i class="fa-solid fa-square-check"></i>' : '<i class="fa-solid fa-square-xmark"></i>'; ?></td>
                <td><a href="edit_product.php?serial=<?php echo urlencode($row['SerialNumber']); ?>&code=<?php echo urlencode($row['Code']); ?>">
                    <i class="fas fa-cog"></i></a></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Phân trang -->
    <div class="pagination">
        <?php
        $sidePages = 1;
        $startPage = max(1, $currentPage - $sidePages);
        $endPage = min($totalPages, $currentPage + $sidePages);

        // URL dẫn về dashboard_content.php với data-page
        $baseUrl = "dashboard_content.php?perPage=$perPage&page=";

        if ($currentPage > 1) {
            echo '<a href="#" data-page="' . $baseUrl .  '1" class="paginate-link"><i class="fa-solid fa-backward-fast"></i></a>';
            echo '<a href="#" data-page="' . $baseUrl . ($currentPage - 1) . '" class="paginate-link"><i class="fa-solid fa-backward"></i></a>';
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            $activeClass = ($i == $currentPage) ? 'active' : '';
            echo '<a href="#" data-page="' . $baseUrl . $i . '" class="paginate-link ' . $activeClass . '">' . $i . '</a>';
        }

        if ($endPage < $totalPages) {
            echo '<span>...</span>';
        }

        if ($currentPage < $totalPages) {
            echo '<a href="#" data-page="' . $baseUrl . ($currentPage + 1) . '" class="paginate-link"><i class="fa-solid fa-forward"></i></a>';
            echo '<a href="#" data-page="' . $baseUrl . $totalPages . '" class="paginate-link"><i class="fa-solid fa-forward-fast"></i></a>';
        }
        ?>
    </div>
<?php endif; ?>