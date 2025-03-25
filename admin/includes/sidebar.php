<?php
// (Tuỳ chọn) Gọi file config hoặc các hàm bạn cần
include_once(__DIR__ . '/../../includes/config.php');

// Hàm kiểm tra xem URL hiện tại có chứa chuỗi URL truyền vào hay không
function activeClass($url) {
    return strpos($_SERVER['REQUEST_URI'], $url) !== false 
        ? 'bg-indigo-100 text-indigo-600' 
        : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-200';
}
?>

<!-- SIDEBAR -->
<aside id="sidebar" class="fixed top-0 left-0 z-40 w-72 bg-white h-full p-6 shadow-md
              transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out
              flex flex-col justify-between">

    <!-- Logo hiển thị ở desktop (md trở lên) -->
    <div>
        <div class="mb-10 hidden md:block">
            <a href="<?= ADMIN_URL ?>/views/index.php">
                <img src="<?= ADMIN_URL ?>/assets/images/logo-fstyle.png" alt="FSTYLE ADMIN"
                    class="w-32 object-contain mx-auto">
            </a>
        </div>

        <!-- Menu: Quản lý chung -->
        <div class="mb-4 px-2 sm:px-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">Quản lý chung</h2>
            <nav class="space-y-2">
                <!-- Dashboard -->
                <a href="<?= ADMIN_URL ?>/views/dashboard/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/dashboard/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z" />
                    </svg>
                    Dashboard
                </a>
                <!-- Hóa đơn -->
                <a href="<?= ADMIN_URL ?>/views/invoice/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/invoice/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 384 512"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 
                                 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0z
                                 M64 80c0-8.8 7.2-16 16-16l64 0c8.8 0 16 7.2 16 16s-7.2 16-16 16L80 96
                                 c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16l64 0c8.8 0 16 7.2 16 16
                                 s-7.2 16-16 16l-64 0c-8.8 0-16-7.2-16-16z" />
                    </svg>
                    Hóa đơn
                </a>
            </nav>
        </div>

        <!-- Menu: Người dùng -->
        <div class="mb-4 px-2 sm:px-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">Người dùng</h2>
            <nav class="space-y-2">
                <!-- Người dùng -->
                <a href="<?= ADMIN_URL ?>/views/customer/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/customer/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 640 512"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M144 0a80 80 0 1 1 0 160A80 80 0 1 1 144 0zM512 0a80 80 0 1 1 0 160A80 80 
                                 0 1 1 512 0zM0 298.7C0 239.8 47.8 192 106.7 192l42.7 0c15.9 0 31 
                                 3.5 44.6 9.7c-1.3 7.2-1.9 14.7-1.9 22.3c0 38.2 16.8 72.5 43.3 96
                                 c-.2 0-.4 0-.7 0L21.3 320C9.6 320 0 310.4 0 298.7zM405.3 320
                                 c-.2 0-.4 0-.7 0c26.6-23.5 43.3-57.8 43.3-96c0-7.6-.7-15-1.9-22.3
                                 c13.6-6.3 28.7-9.7 44.6-9.7l42.7 0C592.2 192 640 239.8 640 298.7
                                 c0 11.8-9.6 21.3-21.3 21.3l-213.3 0zM224 224a96 96 0 1 1 192 0
                                 96 96 0 1 1 -192 0zM128 485.3C128 411.7 187.7 352 261.3 352l117.3 0
                                 C452.3 352 512 411.7 512 485.3c0 14.7-11.9 26.7-26.7 26.7l-330.7 0
                                 c-14.7 0-26.7-11.9-26.7-26.7z" />
                    </svg>
                    Người dùng
                </a>
                <!-- Tài khoản -->
                <a href="<?= ADMIN_URL ?>/views/account/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/account/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M96 128a128 128 0 1 0 256 0A128 128 0 1 0 96 128zm94.5 
                                 200.2l18.6 31L175.8 483.1l-36-146.9c-2-8.1-9.8-13.4-17.9-11.3
                                 C51.9 342.4 0 405.8 0 481.3c0 17 13.8 30.7 30.7 30.7h386.6
                                 c17 0 30.7-13.8 30.7-30.7 0-75.5-51.9-138.9-121.9-156.4c-8.1-2-15.9 
                                 3.3-17.9 11.3l-36 146.9L238.9 359.2l18.6-31c6.4-10.7-1.3-24.2-13.7-24.2
                                 h-19.7c-12.4 0-20.1 13.6-13.7 24.2z" />
                    </svg>
                    Tài khoản
                </a>
                <!-- Role -->
                <a href="<?= ADMIN_URL ?>/views/role/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/role/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M352 320c88.4 0 160-71.6 160-160c0-15.3-2.2-30.1-6.2-44.2
                                 c-3.1-10.8-16.4-13.2-24.3-5.3l-76.8 76.8c-3 3-7.1 4.7-11.3 4.7L336 192
                                 c-8.8 0-16-7.2-16-16l0-57.4c0-4.2 1.7-8.3 4.7-11.3l76.8-76.8c7.9-7.9
                                 5.4-21.2-5.3-24.3C382.1 2.2 367.3 0 352 0C263.6 0 192 71.6 192 160
                                 c0 19.1 3.4 37.5 9.5 54.5L19.9 396.1C7.2 408.8 0 426.1 0 444.1
                                 C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L297.5 310.5
                                 c17 6.2 35.4 9.5 54.5 9.5z" />
                    </svg>
                    Role
                </a>
            </nav>
        </div>

        <!-- Menu: Sản phẩm & Nội dung -->
        <div class="mb-4 px-2 sm:px-4">
            <h2 class="text-sm font-semibold text-gray-500 uppercase mb-2">Sản phẩm &amp; Nội dung</h2>
            <nav class="space-y-2">
                <!-- Thương hiệu -->
                <a href="<?= ADMIN_URL ?>/views/brand/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/brand/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M91.7 96C106.3 86.8 116 70.5 116 52C116 23.3 92.7 0 64 0S12 
                                 23.3 12 52c0 16.7 7.8 31.5 20 41v3 352 64h64v-64h373.6c14.6 0 
                                 26.4-11.8 26.4-26.4 0-3.7-.8-7.3-2.3-10.7L432 272l61.7-138.9
                                 c1.5-3.4 2.3-7 2.3-10.7 0-14.6-11.8-26.4-26.4-26.4H91.7z" />
                    </svg>
                    Thương hiệu
                </a>
                <!-- Danh mục -->
                <a href="<?= ADMIN_URL ?>/views/category/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/category/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M10 3H3v7h7V3zm11 0h-7v7h7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z" />
                    </svg>
                    Danh mục
                </a>
                <!-- Sản phẩm -->
                <a href="<?= ADMIN_URL ?>/views/product/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/product/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 640 512"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M211.8 0c7.8 0 14.3 5.7 16.7 13.2C240.8 51.9 277.1 80 
                                 320 80s79.2-28.1 91.5-66.8C413.9 5.7 420.4 0 428.2 0l12.6 0
                                 c22.5 0 44.2 7.9 61.5 22.3L628.5 127.4c6.6 5.5 10.7 13.5 11.4 22.1
                                 s-2.1 17.1-7.8 23.6l-56 64c-11.4 13.1-31.2 14.6-44.6 3.5L480 197.7
                                 480 448c0 35.3-28.7 64-64 64l-192 0c-35.3 0-64-28.7-64-64
                                 l0-250.3-51.5 42.9c-13.3 11.1-33.1 9.6-44.6-3.5l-56-64c-5.7-6.5
                                 -8.5-15-7.8-23.6s4.8-16.6 11.4-22.1L137.7 22.3C155 7.9 176.7 0 
                                 199.2 0l12.6 0z" />
                    </svg>
                    Sản phẩm
                </a>
                <!-- Banner -->
                <a href="<?= ADMIN_URL ?>/views/banner/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/banner/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 512 512"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320
                                 c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 
                                 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6
                                 L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80
                                 c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0
                                 c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192
                                 a48 48 0 1 0 0-96 48 48 0 1 0 0 96z" />
                    </svg>
                    Banner
                </a>
                <!-- Khuyến mãi -->
                <a href="<?= ADMIN_URL ?>/views/promotion/index.php" class="group flex items-center gap-3 p-3 rounded-md font-bold text-base sm:text-lg 
                          transition-colors duration-300 <?= activeClass('/views/promotion/') ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 576 512"
                        class="w-5 h-5 sm:w-6 sm:h-6 transition-colors duration-300 group-hover:fill-indigo-600">
                        <path d="M0 128C0 92.7 28.7 64 64 64l448 0c35.3 0 64 28.7 64 64l0 
                                 64c0 8.8-7.4 15.7-15.7 18.6C541.5 217.1 528 235 528 256
                                 s13.5 38.9 32.3 45.4c8.3 2.9 15.7 9.8 15.7 18.6l0 64
                                 c0 35.3-28.7 64-64 64L64 448c-35.3 0-64-28.7-64-64l0-64
                                 c0-8.8 7.4-15.7 15.7-18.6C34.5 294.9 48 277 48 256
                                 s-13.5-38.9-32.3-45.4C7.4 207.7 0 200.8 0 192l0-64z" />
                    </svg>
                    Khuyến mãi
                </a>
            </nav>
        </div>
    </div>
</aside>
<!-- END SIDEBAR -->