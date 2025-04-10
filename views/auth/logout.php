<?php
session_start();
session_unset();
session_destroy();

// Điều hướng người dùng về trang chính (index.php) sau khi đăng xuất
header('Location: ../home/index.php');
exit;
?>