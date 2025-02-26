<?php include ('includes/header.php'); ?>

<!-- Mobile Search Overlay (chỉ hiển thị trên mobile) -->
<div id="mobile-search"
    class="md:hidden fixed top-20 left-0 w-full bg-white shadow-lg transform -translate-y-full transition-transform duration-300 z-40">
    <div class="flex items-center p-4">
        <input type="text" placeholder="Tìm kiếm..."
            class="flex-grow px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:border-gray-500" />
        <button id="close-search" class="ml-4 text-gray-600 hover:text-black">
            <img src="assets/icons/close.svg" alt="close" class="w-6 h-6" />
        </button>
    </div>
</div>
<!-- Nút "Back to Top" -->
<button id="back-to-top" aria-label="Back to Top"
    class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg hidden transition-transform transform hover:scale-110 focus:outline-none">
    <img src="assets/icons/chevron-up.svg" alt="Back to Top" class="h-6 w-6" />
</button>

<!-- Banner Section -->
<section class="relative">
    <img src="https://via.placeholder.com/1920x600" alt="Banner"
        class="w-full h-[200px] sm:h-[300px] md:h-[400px] lg:h-[600px] object-cover" />
    <div
        class="absolute inset-0 flex flex-col justify-center items-start px-4 md:px-10 text-white text-center md:text-left">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-4 text-white">
            Áo Sơ Mi
        </h1>
        <p class="text-xl sm:text-2xl mb-4">Giảm 10% - Freeship đơn từ 200K</p>
        <button class="px-4 py-2 sm:px-6 sm:py-3 bg-black hover:bg-gray-800 text-white rounded-full">
            Mua Ngay
        </button>
    </div>
</section>

<!-- Product Sections -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h2 class="text-3xl font-bold mb-6">Mặc Hằng Ngày</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-6">
        <div class="bg-white p-4 shadow rounded">
            <img src="https://via.placeholder.com/300" alt="Product Image" class="w-full mb-4" />
            <h3 class="text-lg font-semibold">Áo Hoodie</h3>
            <p class="text-gray-600">Giá: 599,000đ</p>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <img src="https://via.placeholder.com/300" alt="Product Image" class="w-full mb-4" />
            <h3 class="text-lg font-semibold">Áo Thể Thao</h3>
            <p class="text-gray-600">Giá: 399,000đ</p>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <img src="https://via.placeholder.com/300" alt="Product Image" class="w-full mb-4" />
            <h3 class="text-lg font-semibold">Áo Sơ Mi</h3>
            <p class="text-gray-600">Giá: 499,000đ</p>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <img src="https://via.placeholder.com/300" alt="Product Image" class="w-full mb-4" />
            <h3 class="text-lg font-semibold">Đồ Lót Nam</h3>
            <p class="text-gray-600">Giá: 199,000đ</p>
        </div>
    </div>
</section>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h2 class="text-3xl font-bold mb-6">Sản Phẩm Mặc Hằng Ngày</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-6">
        <div class="bg-white p-4 shadow rounded">
            <img src="https://via.placeholder.com/300" alt="Product Image" class="w-full mb-4" />
            <h3 class="text-lg font-semibold">Áo Thun</h3>
            <p class="text-gray-600">Giá: 299,000đ</p>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <img src="https://via.placeholder.com/300" alt="Product Image" class="w-full mb-4" />
            <h3 class="text-lg font-semibold">Áo Khoác</h3>
            <p class="text-gray-600">Giá: 499,000đ</p>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <img src="https://via.placeholder.com/300" alt="Product Image" class="w-full mb-4" />
            <h3 class="text-lg font-semibold">Quần Short</h3>
            <p class="text-gray-600">Giá: 349,000đ</p>
        </div>
        <div class="bg-white p-4 shadow rounded">
            <img src="https://via.placeholder.com/300" alt="Product Image" class="w-full mb-4" />
            <h3 class="text-lg font-semibold">Quần Jogger</h3>
            <p class="text-gray-600">Giá: 399,000đ</p>
        </div>
    </div>
</section>

<?php include ('includes/footer.php'); ?>