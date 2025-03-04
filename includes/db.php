<?php
$servername = "localhost"; // hoặc 127.0.0.1
$username = "root"; // Tên đăng nhập mặc định của XAMPP là "root"
$password = ""; // XAMPP mặc định không có mật khẩu
$dbname = "fstyle_shop"; // Tên database của bạn

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>