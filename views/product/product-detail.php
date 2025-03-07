<?php 
    include ('../../includes/header.php'); 
    include ('../../views/user/login-register.php'); 
    include ('../../views/search.php'); 
    include_once __DIR__ . "/../../includes/config.php";

?>


<!-- Nút "Back to Top" -->
<button id="back-to-top" aria-label="Back to Top"
    class="fixed bottom-6 right-6 bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg hidden transition-transform transform hover:scale-110 focus:outline-none">
    <img src="<?= USER_URL ?>/assets/icons/chevron-up.svg" alt="Back to Top" class="h-6 w-6" />
</button>


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

<?php include ('../../includes/footer.php'); ?>