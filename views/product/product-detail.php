<?php 
    include_once __DIR__ . "/../../includes/config.php";
    include ('../../includes/header.php'); 
    include ('../user/login-register.php');
    include ('../../includes/search.php');
?>
<!-- Chi tiết sản phẩm -->
<main class="max-w-7xl mx-auto p-6 bg-white shadow-md mt-6 rounded-lg">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Hình ảnh sản phẩm -->
        <div class="flex">
            <div
                class="flex flex-col space-y-2 mr-4 overflow-y-auto max-h-96 scrollbar-thin scrollbar-thumb-blue-500 scrollbar-track-gray-100 scrollbar-w-2">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product1.png"
                    alt="Sản phẩm 1" class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product2.png"
                    alt="Sản phẩm 2"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product3.png"
                    alt="Sản phẩm 3"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product4.png"
                    alt="Sản phẩm 4"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product5.png"
                    alt="Sản phẩm 5"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product2.png"
                    alt="Sản phẩm 2"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product3.png"
                    alt="Sản phẩm 3"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product4.png"
                    alt="Sản phẩm 4"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
                <img onclick="changeImage(this)" src="<?= USER_URL ?>/assets/images/public/product/product5.png"
                    alt="Sản phẩm 5"
                    class="w-20 h-20 cursor-pointer border border-gray-300 hover:border-blue-500 opacity-50">
            </div>
            <div class="relative">
                <img id="mainImage" src="<?= USER_URL ?>/assets/images/public/product/product1.png" alt="Sản phẩm"
                    class="w-full h-auto object-cover cursor-pointer rounded-lg" onclick="openModal(this.src)">
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="p-4">
            <h2 class="text-2xl font-bold mb-2">Quần dài kaki ECC Pants</h2>
            <p class="text-gray-600 mb-2">⭐⭐⭐⭐⭐ (4.9/5)</p>

            <!-- Giá khuyến mãi (màu xanh) -->
            <p class="text-xl text-blue-500 font-bold mb-1">599.000đ</p>
            <div class="flex items-center space-x-2 mb-4">
                <!-- Giá gốc được gạch ngang -->
                <p class="text-lg text-gray-500 line-through">699.000đ</p>
                <!-- Phần trăm giảm (màu xanh) -->
                <span class="bg-blue-500 text-white text-sm font-semibold px-2 py-1 rounded">-14%</span>
            </div>


            <p class="mb-2">Màu sắc:</p>
            <div class="flex space-x-2 mb-4">
                <span class="color-swatch w-16 h-8 bg-black border border-gray-400 rounded-full cursor-pointer"
                    onclick="selectColor(this)"></span>
                <span class="color-swatch w-16 h-8 bg-gray-500 border border-gray-400 rounded-full cursor-pointer"
                    onclick="selectColor(this)"></span>
                <span
                    class="color-swatch w-16 h-8 bg-red-500 border border-gray-400 rounded-full cursor-pointer out-of-stock"></span>
                <span class="color-swatch w-16 h-8 bg-blue-500 border border-gray-400 rounded-full cursor-pointer"
                    onclick="selectColor(this)"></span>
                <span
                    class="color-swatch w-16 h-8 bg-green-500 border border-gray-400 rounded-full cursor-pointer out-of-stock"></span>
            </div>

            <!-- Header cho phần size: Chọn size & Hướng dẫn chọn size -->
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-700 font-semibold">Kích thước:</span>
                <button id="openSizeGuide" class="text-blue-500 cursor-pointer">Hướng dẫn chọn size</button>
            </div>

            <!-- Modal hướng dẫn chọn size -->
            <div id="sizeGuideModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50 p-4">
                <div class="bg-white p-4 rounded-lg w-full max-w-[1200px] border border-gray-300 shadow-lg">
                    <div class="flex justify-between items-center border-b pb-2 mb-4">
                        <h2 class="text-lg font-semibold">Bảng size</h2>
                        <button id="closeSizeGuide" class="text-gray-500 hover:text-red-500 text-xl"
                            aria-label="Đóng bảng size">&times;</button>
                    </div>

                    <!-- Table on large screens (hidden on small screens) -->
                    <div class="hidden md:grid grid-cols-6 gap-2 text-center">
                        <div class="bg-blue-500 text-white font-semibold p-2">Size</div>
                        <div class="bg-blue-500 text-white font-semibold p-2">M</div>
                        <div class="bg-blue-500 text-white font-semibold p-2">L</div>
                        <div class="bg-blue-500 text-white font-semibold p-2">XL</div>
                        <div class="bg-blue-500 text-white font-semibold p-2">2XL</div>
                        <div class="bg-blue-500 text-white font-semibold p-2">3XL</div>

                        <div class="font-semibold p-2">Chiều cao</div>
                        <div class="p-2">1m55 - 1m66</div>
                        <div class="p-2">1m66 - 1m72</div>
                        <div class="p-2">1m72 - 1m77</div>
                        <div class="p-2">1m77 - 1m83</div>
                        <div class="p-2">1m83 - 1m89</div>

                        <div class="font-semibold p-2">Cân nặng</div>
                        <div class="p-2">55kg - 61kg</div>
                        <div class="p-2">62kg - 68kg</div>
                        <div class="p-2">69kg - 75kg</div>
                        <div class="p-2">76kg - 84kg</div>
                        <div class="p-2">85kg - 90kg</div>

                        <div class="font-semibold p-2">Dài quần</div>
                        <div class="p-2">94</div>
                        <div class="p-2">96</div>
                        <div class="p-2">98</div>
                        <div class="p-2">100</div>
                        <div class="p-2">102</div>

                        <div class="font-semibold p-2">1/2 Vòng mông</div>
                        <div class="p-2">50</div>
                        <div class="p-2">52</div>
                        <div class="p-2">54</div>
                        <div class="p-2">56</div>
                        <div class="p-2">58</div>

                        <div class="font-semibold p-2">1/2 Rộng đùi</div>
                        <div class="p-2">32</div>
                        <div class="p-2">33</div>
                        <div class="p-2">34</div>
                        <div class="p-2">35</div>
                        <div class="p-2">36</div>
                    </div>

                    <!-- Table on small screens (hidden on large screens) -->
                    <div class="md:hidden">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="bg-blue-500 text-white">
                                    <th class="p-2">Size</th>
                                    <th class="p-2">M</th>
                                    <th class="p-2">L</th>
                                    <th class="p-2">XL</th>
                                    <th class="p-2">2XL</th>
                                    <th class="p-2">3XL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b">
                                    <td class="p-2 font-semibold">Chiều cao</td>
                                    <td class="p-2">1m55 - 1m66</td>
                                    <td class="p-2">1m66 - 1m72</td>
                                    <td class="p-2">1m72 - 1m77</td>
                                    <td class="p-2">1m77 - 1m83</td>
                                    <td class="p-2">1m83 - 1m89</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-2 font-semibold">Cân nặng</td>
                                    <td class="p-2">55kg - 61kg</td>
                                    <td class="p-2">62kg - 68kg</td>
                                    <td class="p-2">69kg - 75kg</td>
                                    <td class="p-2">76kg - 84kg</td>
                                    <td class="p-2">85kg - 90kg</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-2 font-semibold">Dài quần</td>
                                    <td class="p-2">94</td>
                                    <td class="p-2">96</td>
                                    <td class="p-2">98</td>
                                    <td class="p-2">100</td>
                                    <td class="p-2">102</td>
                                </tr>
                                <tr class="border-b">
                                    <td class="p-2 font-semibold">1/2 Vòng mông</td>
                                    <td class="p-2">50</td>
                                    <td class="p-2">52</td>
                                    <td class="p-2">54</td>
                                    <td class="p-2">56</td>
                                    <td class="p-2">58</td>
                                </tr>
                                <tr>
                                    <td class="p-2 font-semibold">1/2 Rộng đùi</td>
                                    <td class="p-2">32</td>
                                    <td class="p-2">33</td>
                                    <td class="p-2">34</td>
                                    <td class="p-2">35</td>
                                    <td class="p-2">36</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Nút chọn size -->
            <div class="flex space-x-4 mt-4">
                <button class="size-button px-4 py-2 border rounded-lg hover:bg-gray-200 cursor-pointer"
                    data-size="S">S</button>
                <button class="size-button px-4 py-2 border rounded-lg hover:bg-gray-200 cursor-pointer"
                    data-size="M">M</button>
                <button class="size-button px-4 py-2 border rounded-lg hover:bg-gray-200 cursor-pointer"
                    data-size="L">L</button>
                <button class="size-button px-4 py-2 border rounded-lg hover:bg-gray-200 cursor-pointer disabled-size"
                    data-size="XL" disabled>XL</button>
            </div>

            <!-- Số lượng và nút thêm vào giỏ hàng chung một hàng -->
            <div class="flex items-center mt-4 space-x-4">
                <!-- Chọn số lượng sản phẩm kiểu pill (chỉ viền, không nền) -->
                <div class="flex items-center border border-gray-300 rounded-full p-1 space-x-2 w-32 justify-center">
                    <button id="decreaseQty"
                        class="group px-3 py-1 rounded-full hover:bg-transparent transition-colors duration-200 focus:outline-none">
                        <!-- Minus SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 group-hover:text-blue-500"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <span id="productQty" class="text-xl font-semibold text-gray-800">1</span>
                    <button id="increaseQty"
                        class="group px-3 py-1 rounded-full hover:bg-transparent transition-colors duration-200 focus:outline-none">
                        <!-- Plus SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600 group-hover:text-blue-500"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <line x1="12" y1="5" x2="12" y2="19" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <line x1="5" y1="12" x2="19" y2="12" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
                <!-- Nút thêm vào giỏ hàng -->
                <button
                    class="flex items-center justify-center bg-blue-600 text-white py-3 px-6 hover:bg-blue-700 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 inline-block mr-2 fill-current" fill="currentColor">
                        <path
                            d="M0 24C0 10.7 10.7 0 24 0L69.5 0c22 0 41.5 12.8 50.6 32l411 0c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3l-288.5 0 5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5L488 336c13.3 0 24 10.7 24 24s-10.7 24-24 24l-288.3 0c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5L24 48C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z" />
                    </svg>
                    Thêm vào giỏ hàng
                </button>
            </div>
        </div>

    </div>
</main>

<!-- Mô tả sản phẩm (bên ngoài main) -->
<section class="max-w-7xl mx-auto p-6 bg-gray-100 mt-6">
    <h3 class="text-xl font-bold">Mô tả sản phẩm</h3>
    <p class="mt-2 text-gray-700">Quần dài kaki ECC Pants mang lại sự thoải mái và phong cách hiện đại, phù hợp với
        nhiều dịp khác nhau.</p>
</section>

<!-- ĐÁNH GIÁ SẢN PHẨM -->
<section class="max-w-7xl mx-auto p-6 bg-white mt-6 shadow-md rounded-md">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-2xl font-bold">ĐÁNH GIÁ SẢN PHẨM</h3>
            <div class="flex items-center mt-2">
                <span class="text-3xl font-bold text-yellow-500">4.9</span>
                <div class="ml-2 flex">
                    <!-- 5 sao vàng -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                    <!-- Lặp lại 4 lần nữa -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                        class="h-5 w-5 text-yellow-500 fill-current">
                        <path
                            d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                    </svg>
                </div>
                <span class="ml-2 text-gray-500">Dựa trên 7 đánh giá từ khách hàng</span>
            </div>
        </div>
        <!-- Sắp xếp / Filter -->
        <div class="mt-4 sm:mt-0 flex space-x-4 items-center">
            <input type="text" placeholder="Tìm kiếm đánh giá"
                class="border p-2 rounded-md text-sm focus:outline-none" />
            <select class="border p-2 rounded-md text-sm focus:outline-none">
                <option value="">Tất cả sao</option>
                <option value="5">5 sao</option>
                <option value="4">4 sao</option>
                <option value="3">3 sao</option>
                <option value="2">2 sao</option>
                <option value="1">1 sao</option>
            </select>
            <select class="border p-2 rounded-md text-sm focus:outline-none">
                <option value="newest">Mới nhất</option>
                <option value="oldest">Cũ nhất</option>
            </select>
        </div>
    </div>

    <!-- Danh sách đánh giá -->
    <div class="mt-6 space-y-4">
        <div class="p-4 border rounded-lg">
            <div class="flex items-center justify-between">
                <div class="font-semibold">Phong Vẹn</div>
                <div class="text-sm text-gray-500">26/02/2025</div>
            </div>
            <div class="mt-1 flex">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current mr-1">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
                <!-- Lặp lại 4 sao tương tự -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current mr-1">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current mr-1">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"
                    class="h-4 w-4 text-yellow-500 fill-current">
                    <path
                        d="M287.9 17.8c7.8 15.8 15.6 31.5 23.4 47.3l52.2 105.8 116.8 17c17.4 2.5 24.4 23.9 11.8 36.2l-84.5 82.4 19.9 116.2c3 17.3-15.3 30.5-30.7 22.3L288 396.6l-104.5 55c-15.4 8.2-33.7-5-30.7-22.3l19.9-116.2-84.5-82.4c-12.6-12.2-5.6-33.6 11.8-36.2l116.8-17 52.2-105.8c7.8-15.8 30.6-15.8 38.4 0z" />
                </svg>
            </div>
            <p class="mt-2 text-gray-700">Quần mặc ổn, form đẹp, chuẩn size. Hài lòng!</p>
        </div>
        <!-- Có thể thêm nhiều đánh giá khác tùy ý -->
    </div>
</section>

<!-- Modal hiển thị ảnh lớn -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-80 hidden flex items-center justify-center z-50">
    <button id="prevImage" class="absolute left-4 text-white text-3xl">&#10094;</button>
    <img id="modalImage" class="max-w-full max-h-full">
    <button id="nextImage" class="absolute right-4 text-white text-3xl">&#10095;</button>
    <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-3xl">&times;</button>
    <div id="modalHint">Chạm vào ảnh để phóng to &amp; giữ chuột trái để di chuyển</div>
</div>


<?php include ('../../includes/footer.php'); ?>