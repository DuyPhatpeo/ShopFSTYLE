<!-- ======================== START: Product List Section ======================== -->
<section class="w-full py-10 relative">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">SẢN PHẨM MẶC HẰNG NGÀY</h2>
            <a href="#" class="text-sm font-semibold underline">Xem Thêm</a>
        </div>

        <!-- CSS: Xóa mũi tên mặc định của Swiper -->
        <style>
        .swiper-button-prev::after,
        .swiper-button-next::after {
            content: none !important;
        }
        </style>

        <!-- Nút điều hướng Swiper (sử dụng Unicode) -->
        <div class="absolute top-1/2 -translate-y-1/2 left-2 z-10">
            <div
                class="swiper-button-prev inline-flex items-center justify-center bg-gray-800 hover:bg-gray-600 text-white rounded-full shadow-lg cursor-pointer p-2 text-2xl">
                ❮
            </div>
        </div>
        <div class="absolute top-1/2 -translate-y-1/2 right-2 z-10">
            <div
                class="swiper-button-next inline-flex items-center justify-center bg-gray-800 hover:bg-gray-600 text-white rounded-full shadow-lg cursor-pointer p-2 text-2xl">
                ❯
            </div>
        </div>

        <!-- Swiper Slider -->
        <div class="swiper productSwiper">
            <div class="swiper-wrapper">
                <!-- Product 1 (Có giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px]">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product1.png" alt="Sản phẩm 1"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3 cursor-pointer" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.9★ (21)
                        </div>
                        <!-- Overlay chọn size -->
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Thông tin sản phẩm: Dòng chọn màu lên trên tên sản phẩm -->
                    <div class="flex-grow flex flex-col justify-start">
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-gray-800 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-green-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 1
                        </a>
                        <p class="text-sm sm:text-base font-bold mb-0">
                            599.000đ <span class="text-gray-500 line-through text-xs sm:text-sm">699.000đ</span>
                            <span class="ml-2 text-red-500 font-bold text-xs sm:text-sm">-14%</span>
                        </p>
                    </div>
                </div>

                <!-- Product 2 (Có giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px]">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product2.png" alt="Sản phẩm 2"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3 cursor-pointer" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.8★ (63)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-black rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-200 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-blue-400 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 2
                        </a>
                        <p class="text-sm sm:text-base font-bold mb-0">
                            359.000đ <span class="text-gray-500 line-through text-xs sm:text-sm">399.000đ</span>
                            <span class="ml-2 text-red-500 font-bold text-xs sm:text-sm">-10%</span>
                        </p>
                    </div>
                </div>

                <!-- Product 3 (Không giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px]">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product3.png" alt="Sản phẩm 3"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3 cursor-pointer" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.8★ (173)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <!-- Dòng chọn màu nằm trên tên sản phẩm -->
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-gray-800 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-blue-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 3
                        </a>
                        <p class="text-sm sm:text-base font-bold mb-0">161.000đ</p>
                    </div>
                </div>

                <!-- Product 4 (Có giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px] cursor-pointer"
                    onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product4.png" alt="Sản phẩm 4"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.8★ (243)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <!-- Dòng chọn màu -->
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-red-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-yellow-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-green-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 4
                        </a>
                        <p class="text-sm sm:text-base font-bold mb-0">
                            254.000đ <span class="text-gray-500 line-through text-xs sm:text-sm">299.000đ</span>
                            <span class="ml-2 text-red-500 font-bold text-xs sm:text-sm">-15%</span>
                        </p>
                    </div>
                </div>

                <!-- Product 5 (Có giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px] cursor-pointer"
                    onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product5.png" alt="Sản phẩm 5"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.8★ (24)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <!-- Dòng chọn màu -->
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-purple-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-pink-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 5
                        </a>
                        <p class="text-sm sm:text-base font-bold mb-0">
                            249.000đ <span class="text-gray-500 line-through text-xs sm:text-sm">299.000đ</span>
                            <span class="ml-2 text-red-500 font-bold text-xs sm:text-sm">-17%</span>
                        </p>
                    </div>
                </div>

                <!-- Product 6 (Có giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px] cursor-pointer"
                    onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product6.png" alt="Sản phẩm 6"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.9★ (58)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 6
                        </a>
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-indigo-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-300 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-orange-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <p class="text-sm sm:text-base font-bold mb-0">
                            499.000đ <span class="text-gray-500 line-through text-xs sm:text-sm">599.000đ</span>
                            <span class="ml-2 text-red-500 font-bold text-xs sm:text-sm">-17%</span>
                        </p>
                    </div>
                </div>

                <!-- Product 7 (Có giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px] cursor-pointer"
                    onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product7.png" alt="Sản phẩm 7"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.7★ (39)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-teal-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-red-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 7
                        </a>
                        <p class="text-sm sm:text-base font-bold mb-0">
                            799.000đ <span class="text-gray-500 line-through text-xs sm:text-sm">899.000đ</span>
                            <span class="ml-2 text-red-500 font-bold text-xs sm:text-sm">-11%</span>
                        </p>
                    </div>
                </div>

                <!-- Product 8 (Có giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px] cursor-pointer"
                    onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product1.png" alt="Sản phẩm 8"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.9★ (21)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 8
                        </a>
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-gray-800 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-green-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <p class="text-sm sm:text-base font-bold mb-0">
                            599.000đ <span class="text-gray-500 line-through text-xs sm:text-sm">699.000đ</span>
                            <span class="ml-2 text-red-500 font-bold text-xs sm:text-sm">-14%</span>
                        </p>
                    </div>
                </div>

                <!-- Product 9 (Có giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px] cursor-pointer"
                    onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product2.png" alt="Sản phẩm 9"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.8★ (63)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 9
                        </a>
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-black rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-200 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-blue-400 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <p class="text-sm sm:text-base font-bold mb-0">
                            359.000đ <span class="text-gray-500 line-through text-xs sm:text-sm">399.000đ</span>
                            <span class="ml-2 text-red-500 font-bold text-xs sm:text-sm">-10%</span>
                        </p>
                    </div>
                </div>

                <!-- Product 10 (Không giảm giá) -->
                <div class="swiper-slide flex flex-col h-[500px] sm:h-[600px] cursor-pointer"
                    onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                    <div class="relative group cursor-pointer"
                        onclick="location.href='<?= USER_URL ?>/views/product/product-detail.php'">
                        <img src="<?= USER_URL ?>/assets/images/public/product/product3.png" alt="Sản phẩm 10"
                            class="w-full h-[400px] sm:h-[500px] object-cover rounded-lg mb-3" />
                        <div class="absolute top-2 left-2 bg-blue-600 text-white px-2 py-1 rounded text-xs">
                            4.8★ (173)
                        </div>
                        <div
                            class="absolute inset-0 opacity-0 pointer-events-none group-hover:opacity-100 group-hover:pointer-events-auto flex items-center justify-center transition duration-300 ease-in-out">
                            <div class="bg-white/80 p-3 w-[220px] sm:w-[260px] rounded-lg text-gray-800 shadow-md">
                                <p class="font-semibold mb-1 text-lg">Thêm nhanh vào giỏ hàng +</p>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">M</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">L</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">2XL</button>
                                    <button
                                        class="flex items-center justify-center w-12 h-9 bg-white text-gray-800 rounded-md transition hover:bg-black hover:text-white">3XL</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow flex flex-col justify-start">
                        <a href="<?= USER_URL ?>/views/product/product-detail.php"
                            class="text-sm sm:text-base font-semibold mb-1 cursor-pointer hover:text-blue-500 transition-colors duration-200">
                            Sản phẩm 10
                        </a>
                        <div class="flex space-x-1 mb-1">
                            <span
                                class="w-7 h-5 bg-gray-800 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-gray-500 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                            <span
                                class="w-7 h-5 bg-green-600 rounded-full border transition duration-200 hover:ring-2 hover:ring-gray-500 cursor-pointer"></span>
                        </div>
                        <p class="text-sm sm:text-base font-bold mb-0">161.000đ</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- ======================== END: Product List Section ======================== -->

<!-- SwiperJS Initialization -->
<script>
var swiper = new Swiper(".productSwiper", {
    slidesPerView: 2,
    spaceBetween: 15,
    loop: true,
    slidesPerGroup: 2,
    breakpoints: {
        640: {
            slidesPerView: 2,
            slidesPerGroup: 2
        },
        768: {
            slidesPerView: 3,
            slidesPerGroup: 3
        },
        1024: {
            slidesPerView: 5,
            slidesPerGroup: 5
        },
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
});
</script>