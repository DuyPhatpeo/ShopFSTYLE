<?php
    include_once __DIR__ . "/../includes/config.php";
?>

<!-- Header -->
<header class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Mobile Header -->
        <div class="flex md:hidden justify-between items-center">
            <!-- Menu & Search -->
            <div class="flex items-center space-x-4">
                <button id="menu-toggle" class="text-gray-600 hover:text-black focus:outline-none">
                    <img src="<?= USER_URL ?>/assets/icons/menu.svg" alt="menu" class="w-8 h-8" />
                </button>
                <button id="mobile-search-btn" class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/search.svg" alt="search" class="w-6 h-6" />
                </button>
            </div>

            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <a href="<?= USER_URL ?>/views/home/index.php">
                    <img src="<?= USER_URL ?>/assets/images/logo-fstyle.png" alt="logo"
                        class="w-12 h-12 object-contain" />
                </a>
            </div>


            <!-- Cart & Profile -->
            <div class="flex items-center space-x-4">
                <button class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/bag-shopping.svg" alt="bag-shopping" class="w-8 h-8" />
                </button>
                <button class="text-gray-600 hover:text-black profile-btn">
                    <img src="<?= USER_URL ?>/assets/icons/user.svg" alt="user" class="w-8 h-8" />
                </button>
            </div>
        </div>

        <!-- Desktop Header -->
        <div class="hidden md:flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-3 cursor-pointer">
                <a href="<?= USER_URL ?>/views/home/index.php">
                    <img src="<?= USER_URL ?>/assets/images/logo-fstyle.png" alt="logo"
                        class="w-20 lg:w-24 object-contain" />
                </a>
            </div>
            <!-- Navigation Menu -->
            <nav class="hidden md:flex space-x-4 text-base lg:text-lg font-medium text-gray-700 items-center">
                <a href="#" class="text-gray-600 hover:text-black">Áo Sơ Mi</a>
                <a href="#" class="text-gray-600 hover:text-black">Mặc Hằng Ngày</a>
                <a href="#" class="text-gray-600 hover:text-black">Đồ Chạy Bộ</a>
                <a href="#" class="text-gray-600 hover:text-black">Quần Lót</a>
                <a href="#" class="text-gray-600 hover:text-black">Liên Hệ</a>
            </nav>
            <!-- Search & Icons -->
            <div class="flex items-center space-x-4">
                <!-- Ô input tìm kiếm desktop: Bấm vào để mở modal -->
                <form class="relative hidden lg:block" onsubmit="return false;">
                    <input id="desktop-search-input" type="text" placeholder="Tìm kiếm..." class="px-4 py-2 w-48 lg:w-64 rounded-full border border-gray-300 
                               focus:outline-none focus:border-gray-500 cursor-pointer" />
                    <button type="button"
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-black">
                        <img src="<?= USER_URL ?>/assets/icons/search.svg" alt="search" class="w-6 h-6" />
                    </button>
                </form>
                <!-- Icon tìm kiếm cho md trở xuống -->
                <button id="desktop-search-btn" class="block lg:hidden text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/search.svg" alt="search" class="w-6 h-6" />
                </button>
                <button class="text-gray-600 hover:text-black">
                    <img src="<?= USER_URL ?>/assets/icons/bag-shopping.svg" alt="bag-shopping" class="w-8 h-8" />
                </button>
                <button class="text-gray-600 hover:text-black profile-btn">
                    <img src="<?= USER_URL ?>/assets/icons/user.svg" alt="user" class="w-8 h-8" />
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <nav id="mobile-menu" class="md:hidden fixed inset-y-0 left-0 w-64 bg-white shadow-lg transform -translate-x-full 
                   transition-transform duration-300 z-50">
            <button id="close-menu"
                class="absolute top-4 right-4 text-gray-600 hover:text-black text-2xl focus:outline-none">
                <img src="<?= USER_URL ?>/assets/icons/close.svg" alt="close" class="w-6 h-6" />
            </button>
            <ul class="space-y-4 py-10 px-6">
                <li>
                    <a href="#" class="block text-gray-800 text-lg hover:bg-gray-800 hover:text-white 
                               rounded-lg px-4 py-2 transition duration-300">
                        Áo Sơ Mi
                    </a>
                </li>
                <li>
                    <a href="#" class="block text-gray-800 text-lg hover:bg-gray-800 hover:text-white 
                               rounded-lg px-4 py-2 transition duration-300">
                        Mặc Hằng Ngày
                    </a>
                </li>
                <li>
                    <a href="#" class="block text-gray-800 text-lg hover:bg-gray-800 hover:text-white 
                               rounded-lg px-4 py-2 transition duration-300">
                        Đồ Chạy Bộ
                    </a>
                </li>
                <li>
                    <a href="#" class="block text-gray-800 text-lg hover:bg-gray-800 hover:text-white 
                               rounded-lg px-4 py-2 transition duration-300">
                        Quần Lót
                    </a>
                </li>
                <li>
                    <a href="#" class="block text-gray-800 text-lg hover:bg-gray-800 hover:text-white 
                               rounded-lg px-4 py-2 transition duration-300">
                        Liên Hệ
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>