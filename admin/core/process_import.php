<?php
session_start();
require_once '../../includes/core/database.php'; // Sửa đường dẫn

// Bật báo cáo lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 0); // Không hiển thị lỗi trực tiếp cho người dùng
ini_set('log_errors', 1);
ini_set('error_log', './php_errors.log');

// Tăng giới hạn bộ nhớ và thời gian thực thi
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300); // 300 giây (5 phút)

// Bắt đầu đo thời gian
$start_time = microtime(true);

try {
    // Kiểm tra phương thức POST và file tải lên
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csv_file'])) {
        throw new Exception("Yêu cầu không hợp lệ hoặc không có file được tải lên");
    }

    $file = $_FILES['csv_file'];

    // Kiểm tra lỗi tải file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => "File vượt quá dung lượng tối đa cho phép",
            UPLOAD_ERR_FORM_SIZE => "File vượt quá giới hạn MAX_FILE_SIZE",
            UPLOAD_ERR_PARTIAL => "File chỉ được tải lên một phần",
            UPLOAD_ERR_NO_FILE => "Không có file nào được tải lên",
            UPLOAD_ERR_NO_TMP_DIR => "Thiếu thư mục tạm",
            UPLOAD_ERR_CANT_WRITE => "Không thể ghi file lên đĩa",
            UPLOAD_ERR_EXTENSION => "Tải file bị dừng bởi phần mở rộng"
        ];
        throw new Exception("Lỗi khi tải file: " . ($error_messages[$file['error']] ?? "Lỗi không xác định"));
    }

    // 3. Kiểm tra định dạng file (bao gồm MIME type)
    //if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
    //    throw new Exception("Vui lòng tải lên file CSV");
    //}
    //$finfo = finfo_open(FILEINFO_MIME_TYPE);
    //$mime = finfo_file($finfo, $file['tmp_name']);
    //finfo_close($finfo);
    //if (!in_array($mime, ['text/csv', 'text/plain'])) {
    //    throw new Exception("File không phải định dạng CSV hợp lệ");
    //}

    // Kiểm tra phần mở rộng file
    if (pathinfo($file['name'], PATHINFO_EXTENSION) !== 'csv') {
        throw new Exception("Vui lòng tải lên file CSV");
    }

    // Kiểm tra bằng cách đọc nội dung file
    $handle = fopen($file['tmp_name'], 'r');
    if ($handle !== false) {
        // Đọc dòng đầu tiên để kiểm tra định dạng CSV
        $first_line = fgetcsv($handle);
        fclose($handle);
        if ($first_line === false) {
            throw new Exception("File không phải định dạng CSV hợp lệ");
        }
    } else {
        throw new Exception("Không thể đọc file");
    }

    // Mở file CSV
    $handle = fopen($file['tmp_name'], 'r');
    if ($handle === false) {
        throw new Exception("Không thể mở file CSV: " . $file['tmp_name']);
    }

    // Kết nối cơ sở dữ liệu
    $db = new Database();
    $pdo = $db->connect();
    if (!$pdo) {
        throw new Exception("Không thể kết nối đến cơ sở dữ liệu");
    }

    // Bắt đầu giao dịch
    $pdo->beginTransaction();

    // Bỏ qua 2 dòng đầu (cột và tiêu đề)
    fgetcsv($handle); // Bỏ dòng cột
    fgetcsv($handle); // Bỏ dòng tiêu đề

    // Chuẩn bị câu lệnh SQL với kiểm tra trùng lặp
    $sql = "INSERT INTO checkproduct (
                Code, SerialNumber, CustomerName, PhoneNumber, Email, 
                PurchaseLocation, CityProvince, IsChecked, CheckIP, CheckTime, 
                created_at, updated_at
            ) VALUES (?, ?, '', '', '', '', '', 0, '', NULL, NOW(), NOW())
            ON DUPLICATE KEY UPDATE updated_at = NOW()";

    $stmt = $pdo->prepare($sql);

    // Biến đếm
    $total_rows = 0; // Tổng số dòng trong file
    $imported_rows = 0; // Số dòng đã import thành công
    $duplicate_rows = 0; // Số dòng bị trùng lặp

    // Biến xử lý theo từng khối (chunk)
    $chunkSize = 1000; // Số dòng xử lý mỗi lần
    $currentChunk = []; // Lưu trữ dữ liệu tạm thời

    // Đọc và chèn dữ liệu
    while (($data = fgetcsv($handle)) !== false) {
        $total_rows++;

        // Kiểm tra số lượng cột
        if (count($data) < 2) {
            error_log("Dòng $total_rows: Dữ liệu không hợp lệ (thiếu cột).");
            continue; // Bỏ qua dòng này
        }

        // Kiểm tra dữ liệu trống
        $serial = trim($data[0]);
        $code = trim($data[1]);

        if (empty($serial) || empty($code)) {
            error_log("Dòng $total_rows: SerialNumber hoặc Code bị trống.");
            continue; // Bỏ qua dòng này
        }

        // Thêm dòng vào khối hiện tại
        $currentChunk[] = [$code, $serial];

        // Nếu đủ chunkSize, thực thi batch insert
        if (count($currentChunk) === $chunkSize) {
            processBatch($pdo, $stmt, $currentChunk, $imported_rows, $duplicate_rows);
            $currentChunk = []; // Reset khối dữ liệu
        }
    }

    // Xử lý các dòng còn lại trong khối cuối cùng
    if (!empty($currentChunk)) {
        processBatch($pdo, $stmt, $currentChunk, $imported_rows, $duplicate_rows);
    }

    // Đóng file CSV
    fclose($handle);

    // Commit giao dịch
    $pdo->commit();

    // Tính thời gian thực hiện
    $end_time = microtime(true);
    $execution_time = round($end_time - $start_time, 2);

    // Lưu thông báo vào session
    $_SESSION['message'] = "Quá trình import hoàn tất:<br>" .
        "- Tổng số code trong file: $total_rows<br>" .
        "- Số dòng đã import thành công: $imported_rows<br>" .
        "- Số dòng bị trùng lặp: $duplicate_rows<br>" .
        "- Thời gian thực hiện: {$execution_time}s";

} catch (Exception $e) {
    // Rollback nếu có lỗi
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Đóng file nếu đang mở
    if (isset($handle) && is_resource($handle)) {
        fclose($handle);
    }

    // Ghi log lỗi
    error_log("Lỗi nhập dữ liệu: " . $e->getMessage() . "\n" . $e->getTraceAsString());

    // Gửi thông báo lỗi tới người dùng
    $_SESSION['error'] = "Nhập dữ liệu thất bại: " . $e->getMessage();
    header("Location: ../../admin/dashboard.php");
    exit();
}

// Chuyển hướng sau khi hoàn tất
header("Location: ../../admin/dashboard.php");
exit();

/**
 * Hàm xử lý batch insert
 */
function processBatch($pdo, $stmt, $batchData, &$imported_rows, &$duplicate_rows) {
    try {
        foreach ($batchData as $row) {
            $stmt->execute($row);

            // Kiểm tra xem dòng có bị trùng hay không
            if ($stmt->rowCount() === 1) {
                $imported_rows++; // Dòng mới được chèn
            } else {
                $duplicate_rows++; // Dòng bị trùng lặp
            }
        }
    } catch (Exception $e) {
        error_log("Lỗi khi thực thi batch insert: " . $e->getMessage());
    }
}


