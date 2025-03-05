<?php
    include_once __DIR__ . "/../includes/config.php";
?>

<nav class="bg-white shadow-md p-6 flex justify-between items-center relative">
    <!-- Nút mở sidebar trên mobile -->
    <button id="menu-toggle" class="md:hidden text-gray-700 focus:outline-none">
        <img src="<?= $base_path ?>/assets/icons/menu.svg" class="w-6 h-6 text-indigo-600" alt="Menu" />
    </button>

    <!-- Logo trên mobile -->
    <div class="absolute left-1/2 transform -translate-x-1/2 md:hidden">
        <img src="<?= $base_path ?>/assets/images/logo-fstyle.png" alt="FSTYLE ADMIN" class="h-10">
    </div>

    <!-- Icon Profile -->
    <div class="ml-auto flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" class="w-8 h-8 text-gray-700">
            <path
                d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512h388.6c16.4 0 29.7-13.3 29.7-29.7 0-98.5-79.8-178.3-178.3-178.3h-91.4z" />
        </svg>
    </div>

</nav>