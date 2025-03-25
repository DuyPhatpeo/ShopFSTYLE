<?php
// File: admin/your-upload-endpoint.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    $uploadDir = __DIR__ . '/../uploads/'; // Thư mục uploads ở gốc dự án

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $filename = time() . '_' . basename($file['name']);
    $uploadFile = $uploadDir . $filename;
    
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt)) {
        http_response_code(400);
        echo json_encode([
            'error' => [
                'message' => 'Chỉ cho phép upload file ảnh (jpg, jpeg, png, gif, webp).'
            ]
        ]);
        exit;
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
        // Giả sử URL của site là http://localhost/ShopFSTYLE
        $url = '/ShopFSTYLE/admin/uploads/' . $filename;
        echo json_encode([
            'url' => $url
        ]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode([
            'error' => [
                'message' => 'Không thể tải ảnh lên.'
            ]
        ]);
        exit;
    }
}

http_response_code(400);
echo json_encode([
    'error' => [
        'message' => 'Yêu cầu không hợp lệ.'
    ]
]);
?>