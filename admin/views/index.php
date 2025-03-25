<?php
session_start(); // Khởi tạo session

// Thiết lập thời gian timeout (10 phút = 600 giây)
$timeout_duration = 600;

// Kiểm tra thời gian hoạt động cuối cùng nếu có
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    // Phiên đã hết hạn
    session_unset();
    session_destroy();
    header("Location: auth/login.php?msg=" . urlencode("Phiên đăng nhập của bạn đã hết hạn!") . "&type=failure");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time(); // Cập nhật thời gian hoạt động cuối cùng

// Kiểm tra đăng nhập
if (!isset($_SESSION['admin'])) {
    header("Location: views/auth/login.php");
    exit;
}

include('../includes/header.php');
?>
<!-- Container hiển thị thông báo flash từ URL parameters -->
<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main
    class="flex-1 flex flex-col items-center justify-center bg-gradient-to-r from-blue-400 via-purple-500 to-pink-500 min-h-screen py-10">
    <!-- Card trung tâm với hiệu ứng mờ nền -->
    <div class="bg-white bg-opacity-90 rounded-xl shadow-2xl p-10 max-w-xl w-full">
        <!-- Tiêu đề -->
        <h1 class="text-5xl font-extrabold text-center text-gray-800 mb-4">
            Chào mừng đến với trang quản trị viên của Shop FSTYLE
        </h1>
        <!-- Mô tả ngắn -->
        <p class="text-lg text-gray-700 text-center">
            Quản lý các danh mục, sản phẩm, thương hiệu và nhiều hơn thế nữa.
        </p>
    </div>
</main>

<?php include('../includes/footer.php'); ?>