<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . "/../includes/config.php";
?>
<div class="bg-gray-800 text-white text-sm py-1 px-4 flex justify-between items-center">
    <!-- Trái -->
    <div class="space-x-4 hidden md:flex">
        <a href="#" class="hover:underline">Về FStyle</a>
        <a href="#" class="hover:underline">84Rising*</a>
        <a href="#" class="hover:underline">FStyle XPrint</a>
    </div>

    <!-- Phải -->
    <div class="flex items-center space-x-4 text-xs">
        <a href="#" class="hover:underline">FStyle Club</a>
        <a href="#" class="hover:underline">Blog</a>
        <a href="#" class="hover:underline">Trung tâm CSKH</a>
        <?php if (!isset($_SESSION['customer'])): ?>
        <!-- Hiện khi chưa đăng nhập -->
        <a href="<?= USER_URL ?>/views/auth/login.php"
            class="bg-white text-gray-800 px-3 py-1 rounded hover:bg-gray-200 transition font-medium whitespace-nowrap">
            Đăng nhập
        </a>
        <?php else: ?>
        <!-- Khi đã đăng nhập: hiển thị nút Đăng xuất bên trái icon user -->
        <a href="../../controller/authController.php?action=logout" class="text-red-500 hover:underline">Đăng xuất</a>

        <span class="text-gray-800 font-medium"><?= htmlspecialchars($_SESSION['customer']['full_name']) ?></span>
        <?php endif; ?>
    </div>
</div>

<!-- Main Header -->
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
            <div class="flex items-center space-x-2">
                <a href="<?= USER_URL ?>/views/home/index.php">
                    <img src="<?= USER_URL ?>/assets/images/logo-fstyle.png" alt="logo"
                        class="w-12 h-12 object-contain" />
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="<?= USER_URL ?>/views/user/shopping-card.php" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/bag-shopping.svg" alt="bag-shopping" class="w-8 h-8" />
                </a>
                <?php if (isset($_SESSION['customer'])): ?>
                <!-- Nếu đã đăng nhập, hiển thị icon user (có thể dùng để mở dropdown thông tin) -->
                <button id="profile-btn" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/user.svg" alt="user" class="w-8 h-8" />
                </button>
                <?php else: ?>
                <!-- Nếu chưa đăng nhập, hiển thị nút đăng nhập -->
                <a href="<?= USER_URL ?>/views/auth/login.php" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/user.svg" alt="user" class="w-8 h-8" />
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Desktop Header -->
        <div class="hidden md:flex justify-between items-center">
            <div class="flex items-center space-x-3 cursor-pointer">
                <a href="<?= USER_URL ?>/views/home/index.php">
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
                <button id="desktop-search-btn" class="block lg:hidden text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/search.svg" alt="search" class="w-6 h-6" />
                </button>
                <a href="<?= USER_URL ?>/views/user/shopping-card.php" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/bag-shopping.svg" alt="bag-shopping" class="w-8 h-8" />
                </a>
                <div class="flex items-center space-x-4">
                    <?php if (isset($_SESSION['customer'])): ?>
                    <button id="profile-btn" class="text-gray-600 hover:text-black">
                        <img src="<?= USER_URL ?>/assets/icons/user.svg" alt="user" class="w-8 h-8" />
                    </button>
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

        <!-- User Info Dropdown (nếu cần) -->
        <div id="user-info-dropdown"
            class="hidden absolute right-0 mt-2 w-48 bg-white border border-gray-200 shadow-lg rounded-lg text-sm text-gray-800">
            <div class="px-4 py-2">
                <span class="font-semibold"><?= htmlspecialchars($_SESSION['customer']['full_name'] ?? '') ?></span>
            </div>
            <div class="px-4 py-2 border-t">
                <a href="<?= USER_URL ?>/views/user/profile.php" class="block hover:bg-gray-100 px-2 py-1">Thông tin cá
                    nhân</a>
            </div>
            <div class="px-4 py-2 border-t">
                <a href="<?= USER_URL ?>/logout.php" class="block hover:bg-gray-100 px-2 py-1">Đăng xuất</a>
            </div>
        </div>
    </div>
</header>

<style>
#user-info-dropdown {
    display: none;
}

#user-info-dropdown.show {
    display: block;
}
</style>

<script>
document.getElementById('profile-btn') && document.getElementById('profile-btn').addEventListener('click', function() {
    var dropdown = document.getElementById('user-info-dropdown');
    dropdown.classList.toggle('show');
});

// Đóng dropdown khi nhấn ngoài
document.addEventListener('click', function(e) {
    var dropdown = document.getElementById('user-info-dropdown');
    var profileBtn = document.getElementById('profile-btn');
    if (dropdown && profileBtn && !profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.remove('show');
    }
});
</script>