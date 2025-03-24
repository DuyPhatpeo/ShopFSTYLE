<?php
// Khởi tạo session và lấy tên admin (nếu có)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$adminName = isset($_SESSION['admin']['full_name']) ? $_SESSION['admin']['full_name'] : '';
?>

<!-- Navbar cố định -->
<nav class="fixed top-0 left-0 right-0 bg-white shadow-md p-4 flex items-center justify-between z-30">
    <!-- Bên trái: Nút mở sidebar trên mobile, nút về trang chủ & Logo desktop -->
    <div class="flex items-center space-x-4">
        <!-- Nút mở sidebar trên mobile -->
        <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none">
            <img src="<?= ADMIN_URL ?>/assets/icons/menu.svg" alt="Menu" class="w-6 h-6">
        </button>
        <!-- Nút về trang chủ (xuất hiện bên cạnh nút menu) -->
        <a href="<?= ADMIN_URL ?>/views/index.php" class="text-gray-700 hover:text-indigo-600 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" class="w-6 h-6">
                <path
                    d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
            </svg>
        </a>
        <!-- Logo desktop (ẩn trên mobile) -->
        <div class="hidden md:block">
            <img src="<?= ADMIN_URL ?>/assets/images/logo-fstyle.png" alt="FSTYLE ADMIN" class="h-10">
        </div>
    </div>

    <!-- Logo mobile (chỉ hiển thị trên mobile) -->
    <div class="absolute left-1/2 transform -translate-x-1/2 md:hidden">
        <img src="<?= ADMIN_URL ?>/assets/images/logo-fstyle.png" alt="FSTYLE ADMIN" class="h-10">
    </div>

    <!-- Bên phải: Tên tài khoản, Icon Profile, Nút Logout -->
    <div class="flex items-center space-x-4">
        <!-- Tên tài khoản (ẩn trên mobile) -->
        <?php if (!empty($adminName)): ?>
        <span class="hidden md:inline text-gray-700 font-medium">
            <?= htmlspecialchars($adminName) ?>
        </span>
        <?php endif; ?>

        <!-- Icon Profile -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 448 512" class="w-8 h-8 text-gray-700">
            <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3 
               0 498.7 13.3 512 29.7 512h388.6c16.4 0 29.7-13.3 29.7-29.7 
               0-98.5-79.8-178.3-178.3-178.3h-91.4z" />
        </svg>

        <!-- Nút Logout -->
        <a href="auth/logout.php" title="Đăng xuất" class="hover:text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512"
                class="w-8 h-8 text-red-500">
                <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 
                 406.1c-6.4 6.4-15 9.9-24 9.9-18.7 0-33.9-15.2-33.9-33.9l0-62.1-128 
                 0c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 
                 15.2-33.9 33.9-33.9 9 0 17.6 3.6 24 9.9zM160 96L96 96c-17.7 0-32 
                 14.3-32 32l0 256c0 17.7 14.3 32 32 32l64 0c17.7 0 32 14.3 32 
                 32s-14.3 32-32 32l-64 0c-53 0-96-43-96-96L0 128C0 75 43 32 
                 96 32l64 0c17.7 0 32 14.3 32 32s-14.3 32-32 32z" />
            </svg>
        </a>
    </div>
</nav>