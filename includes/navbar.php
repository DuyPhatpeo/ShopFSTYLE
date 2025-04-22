<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . "/../includes/config.php";
?>
<div class="bg-gray-800 text-white text-sm py-1 px-4 flex justify-between items-center">
    <div class="space-x-4 hidden md:flex">
        <a href="#" class="hover:bg-gray-700 px-2 py-1 rounded transition">Về FStyle</a>
        <a href="#" class="hover:bg-gray-700 px-2 py-1 rounded transition">84Rising*</a>
        <a href="#" class="hover:bg-gray-700 px-2 py-1 rounded transition">FStyle XPrint</a>
    </div>

    <div class="flex items-center space-x-4 text-xs">
        <a href="#" class="hover:bg-gray-700 px-2 py-1 rounded transition">FStyle Club</a>
        <a href="#" class="hover:bg-gray-700 px-2 py-1 rounded transition">Blog</a>
        <a href="#" class="hover:bg-gray-700 px-2 py-1 rounded transition">Trung tâm CSKH</a>

        <?php if (!isset($_SESSION['customer'])): ?>
        <?php else: ?>
        <a href="<?= USER_URL ?>/controller/authController.php?action=logout" class="text-red-500 hover:underline">Đăng
            xuất</a>
        <?php endif; ?>
    </div>
</div>

<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

        <!-- Mobile Header -->
        <div class="flex md:hidden justify-between items-center">
            <div class="flex items-center space-x-4">
                <button id="menu-toggle" class="text-blue-500 hover:text-blue-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <button id="mobile-search-btn" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/search.svg" alt="search" class="w-6 h-6" />
                </button>
            </div>
            <a href="<?= USER_URL ?>">
                <img src="<?= USER_URL ?>/assets/images/logo-fstyle.png" alt="logo" class="w-12 h-12 object-contain" />
            </a>
            <div class="flex items-center space-x-4">
                <a href="<?= USER_URL ?>/views/user/shopping-card.php" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/bag-shopping.svg" alt="bag-shopping" class="w-8 h-8" />
                </a>
                <?php if (isset($_SESSION['customer'])): ?>
                <a href="<?= USER_URL ?>/views/user/profile.php">
                    <img src="<?= USER_URL ?>/assets/icons/user.svg" alt="user"
                        class="w-8 h-8 p-1 rounded-full ring-2 ring-blue-500 bg-blue-100 transition-all duration-200" />
                </a>
                <?php else: ?>
                <a href="<?= USER_URL ?>/views/auth/login.php" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/user.svg" alt="user" class="w-8 h-8" />
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Desktop Header -->
        <div class="hidden md:flex justify-between items-center">
            <div class="flex items-center space-x-3 cursor-pointer">
                <a href="<?= USER_URL ?>    ">
                    <img src="<?= USER_URL ?>/assets/images/logo-fstyle.png" alt="logo"
                        class="w-20 lg:w-24 object-contain" />
                </a>
            </div>
            <nav class="hidden md:flex space-x-4 text-base lg:text-lg font-medium text-gray-700 items-center">
                <a href="#" class="text-gray-600 hover:text-black">Áo Sơ Mi</a>
                <a href="#" class="text-gray-600 hover:text-black">Mặc Hằng Ngày</a>
                <a href="#" class="text-gray-600 hover:text-black">Đồ Chạy Bộ</a>
                <a href="#" class="text-gray-600 hover:text-black">Quần Lót</a>
                <a href="#" class="text-gray-600 hover:text-black">Liên Hệ</a>
            </nav>
            <div class="flex items-center space-x-4">
                <form class="relative hidden lg:block" onsubmit="return false;">
                    <input id="desktop-search-input" type="text" placeholder="Tìm kiếm..."
                        class="px-4 py-2 w-48 lg:w-64 rounded-full border border-gray-300 focus:outline-none focus:border-gray-500 cursor-pointer" />
                    <button type="button"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-black">
                        <img src="<?= USER_URL ?>/assets/icons/search.svg" alt="search" class="w-6 h-6" />
                    </button>
                </form>
                <a href="<?= USER_URL ?>/views/cart.php" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/bag-shopping.svg" alt="bag-shopping" class="w-8 h-8" />
                </a>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['customer'])): ?>
                    <a href="<?= USER_URL ?>/views/account/info.php">
                        <img src="<?= USER_URL ?>/assets/icons/user.svg" alt="user"
                            class="w-8 h-8 p-1 rounded-full ring-2 ring-blue-500 bg-blue-100 transition-all duration-200" />
                    </a>
                    <span
                        class="text-gray-800 font-medium"><?= htmlspecialchars($_SESSION['customer']['full_name']) ?></span>
                    <?php else: ?>
                    <a href="<?= USER_URL ?>/views/auth/login.php"
                        class="bg-white text-gray-800 px-3 py-1 rounded hover:bg-gray-200 transition font-medium whitespace-nowrap">
                        Đăng nhập
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>