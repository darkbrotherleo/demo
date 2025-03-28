<?php
require_once '../../includes/core/database.php';

header('Content-Type: application/json');

try {
    $db = new Database();
    $pdo = $db->connect();
    
    if ($pdo) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Kết nối cơ sở dữ liệu thành công'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Không thể kết nối cơ sở dữ liệu'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi kết nối: ' . $e->getMessage()
    ]);
}