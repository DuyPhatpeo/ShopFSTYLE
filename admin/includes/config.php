<?php
    // Lấy đường dẫn thư mục hiện tại
    $base_path = str_replace('\\', '/', dirname($_SERVER['PHP_SELF']));

    // Đảm bảo chỉ có đúng `/admin`
    if (strpos($base_path, "/admin/views") !== false) {
        $base_path = substr($base_path, 0, strpos($base_path, "/admin/views") + 6);
    } elseif (strpos($base_path, "/admin") === false) {
        $base_path .= "/admin"; 
    }
?>