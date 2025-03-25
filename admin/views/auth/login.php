<?php
session_start(); // Khởi tạo session

// Kết nối CSDL và load controller tài khoản
require_once('../../../includes/db.php');              // Kết nối CSDL
require_once('../../controller/accountController.php');  // File controller tài khoản

$error = ''; // Biến lưu lỗi đăng nhập

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['email']); // Có thể là email hoặc username
    $password   = trim($_POST['pass']);

    // Gọi hàm loginAdmin từ accountController
    $admin = loginAdmin($conn, $identifier, $password);

    if ($admin) {
        $_SESSION['admin'] = $admin;                  
        // Chuyển hướng sang index với thông báo thành công (thông qua URL parameters)
        header("Location: ../../views/index.php?msg=" . urlencode("Đăng nhập thành công!") . "&type=success");
        exit;
    } else {
        $error = "Tài khoản hoặc mật khẩu không đúng.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FSTYLE ADMIN | Trang đăng nhập</title>
    <link rel="icon" type="image/png" href="../../../assets/images/logo-fstyle.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-ku+8gk93fUwl8MdZ3c55ojcT31mxKyXyAVt+b6GHn5+7aZQ/b+g9Qak09+ap1GJ3DlWrwrPqE6Ns1E9+gj7RAg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="min-h-screen bg-gradient-to-r from-blue-400 to-blue-600 flex flex-col justify-between">
    <div class="flex-grow flex items-center justify-center">
        <!-- Thông báo lỗi nếu có -->
        <?php if ($error): ?>
        <div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50">
            <div class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                <?= $error ?>
            </div>
        </div>
        <?php endif; ?>

        <div
            class="w-full max-w-4xl mx-auto grid grid-cols-1 lg:grid-cols-2 overflow-hidden bg-white rounded-lg shadow-2xl">
            <!-- Hình ảnh (chỉ hiển thị trên desktop) -->
            <div class="hidden lg:block">
                <img src="../../assets/images/login_banner.svg" alt="Login Image" class="w-full h-full object-cover">
            </div>
            <!-- Form đăng nhập -->
            <div class="p-10">
                <div class="mb-8 text-center">
                    <h1 class="text-4xl font-bold text-gray-800">Admin - Đăng nhập</h1>
                    <p class="mt-2 text-gray-600">Xin chào, vui lòng nhập thông tin đăng nhập</p>
                </div>
                <form action="" method="post" id="loginForm" class="space-y-6">
                    <div class="relative">
                        <input type="text" name="email" placeholder="Username"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200">
                        <i class="fas fa-user absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <span id="email-error"
                            class="text-red-500 text-sm mt-1 h-5 block opacity-0 transition-opacity duration-200">
                            Username không được để trống
                        </span>
                    </div>
                    <div class="relative">
                        <input type="password" name="pass" placeholder="Mật khẩu"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 transition duration-200">
                        <i class="fas fa-lock absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <span id="password-error"
                            class="text-red-500 text-sm mt-1 h-5 block opacity-0 transition-opacity duration-200">
                            Mật khẩu không được để trống
                        </span>
                    </div>
                    <button type="submit" name="login"
                        class="w-full bg-orange-500 text-white py-3 rounded-lg hover:bg-orange-600 transition transform hover:scale-105 duration-200">
                        Đăng nhập
                    </button>
                </form>
            </div>
        </div>
    </div>
    <!-- Footer chỉ chứa dòng chữ, không có nền -->
    <footer class="text-center py-4">
        <p class="text-gray-700">&copy; <?= date("Y") ?> Shop Fstyle</p>
        <p class="text-gray-600 text-sm">Trần Duy Phát</p>
    </footer>

    <script src="<?= ADMIN_URL ?>/assets/js/notifications.js"></script>
</body>

</html>