<?php
// File: includes/db.php
$host = "localhost"; // hoặc địa chỉ IP của server MySQL
$username = "root"; // Tên người dùng MySQL
$password = ""; // Mật khẩu MySQL (nếu có)
$database = "fstyle_shop"; // Tên database

// Tạo kết nối
$conn = new mysqli($host, $username, $password, $database);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Đặt bộ mã hóa ký tự UTF-8
$conn->set_charset("utf8");

?>