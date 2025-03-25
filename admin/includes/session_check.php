<?php
session_start();

// Thiết lập thời gian timeout (10 phút = 600 giây)
$timeout_duration = 600;

// Kiểm tra thời gian hoạt động cuối cùng nếu có
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Phiên đã hết hạn
    session_unset();
    session_destroy();
    // In ra mã HTML/JS để hiển thị thông báo và chuyển hướng
    echo "<script>
            alert('Phiên đăng nhập của bạn đã hết hạn!');
            window.location.href = '../auth/login.php';
          </script>";
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Cập nhật thời gian hoạt động cuối cùng

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>