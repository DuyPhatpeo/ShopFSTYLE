<?php
    include_once __DIR__ . "/../includes/config.php";
?>


<aside id="sidebar"
    class="fixed md:relative z-40 w-72 bg-white h-screen p-6 shadow-md transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col justify-between">
    <div>
        <div class="mb-10 hidden md:block">
            <img src="<?= $base_path ?>/assets/images/logo-fstyle.png" alt="FSTYLE ADMIN"
                class="w-32 object-contain mx-auto">
        </div>
        <nav class="space-y-2">
            <a href="<?= $base_path ?>/views/index.php"
                class="flex items-center gap-3 p-3 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-200 font-bold text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z" />
                </svg>
                Dashboard
            </a>

            <a href="<?= $base_path ?>/views/users.php"
                class="flex items-center gap-3 p-3 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-200 font-bold text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" class="w-6 h-6">
                    <path
                        d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zM178.3 304C79.8 304 0 383.8 0 482.3 0 498.7 13.3 512 29.7 512h388.6c16.4 0 29.7-13.3 29.7-29.7 0-98.5-79.8-178.3-178.3-178.3h-91.4z" />
                </svg>
                Người dùng
            </a>
            <a href="<?= $base_path ?>/views/accounts.php"
                class="flex items-center gap-3 p-3 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-200 font-bold text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-6 h-6">
                    <path
                        d="M96 128a128 128 0 1 0 256 0A128 128 0 1 0 96 128zm94.5 200.2l18.6 31L175.8 483.1l-36-146.9c-2-8.1-9.8-13.4-17.9-11.3C51.9 342.4 0 405.8 0 481.3c0 17 13.8 30.7 30.7 30.7h386.6c17 0 30.7-13.8 30.7-30.7 0-75.5-51.9-138.9-121.9-156.4-8.1-2-15.9 3.3-17.9 11.3l-36 146.9L238.9 359.2l18.6-31c6.4-10.7-1.3-24.2-13.7-24.2h-19.7c-12.4 0-20.1 13.6-13.7 24.2z" />
                </svg>
                Tài khoản
            </a>
            <a href="<?= $base_path ?>/views/brand/index.php"
                class="flex items-center gap-3 p-3 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-200 font-bold text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor" class="w-6 h-6">
                    <path
                        d="M91.7 96C106.3 86.8 116 70.5 116 52C116 23.3 92.7 0 64 0S12 23.3 12 52c0 16.7 7.8 31.5 20 41v3 352 64h64v-64h373.6c14.6 0 26.4-11.8 26.4-26.4 0-3.7-.8-7.3-2.3-10.7L432 272l61.7-138.9c1.5-3.4 2.3-7 2.3-10.7 0-14.6-11.8-26.4-26.4-26.4H91.7z" />
                </svg>
                Thương hiệu
            </a>
            <a href="<?= $base_path ?>/views/category.php"
                class="flex items-center gap-3 p-3 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-200 font-bold text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M10 3H3v7h7V3zm11 0h-7v7h7V3zM3 14h7v7H3v-7zm11 0h7v7h-7v-7z" />
                </svg>
                Danh mục sản phẩm
            </a>
            <a href="<?= $base_path ?>/views/product.php"
                class="flex items-center gap-3 p-3 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-200 font-bold text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M21 7l-9-5-9 5 9 5 9-5zm-9 7L3 9v10l9 5 9-5V9l-9 5z" />
                </svg>
                Sản phẩm
            </a>
            <a href="<?= $base_path ?>/views/banner.php"
                class="flex items-center gap-3 p-3 rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-200 font-bold text-lg">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                    <path d="M4 4h16v2H4V4zm0 4h16v2H4V8zm0 4h10v2H4v-2zm0 4h16v2H4v-2z" />
                </svg>
                Banner
            </a>
        </nav>
    </div>
</aside>