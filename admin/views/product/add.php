<?php
ob_start();
$pageTitle = "Trang thêm sản phẩm";
include("../../includes/session_check.php");
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');
require_once('../../controller/brandController.php');
require_once('../../controller/categoryController.php');

$allBrands = getAllBrands($conn);
$allCategories = getAllCategories($conn);

$errors = [];
$product_id = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = processAddProduct($conn);
    if (is_array($result) && !empty($result)) {
        $errors = $result;
    }
}
?>

<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Thêm Sản Phẩm
        </h1>
        <a href="index.php"
            class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded transition duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 448 512" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H109.2L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L9.4 233.4z" />
            </svg>
            <span class="hidden md:inline">Quay lại</span>
        </a>
    </div>

    <?php if (!empty($errors['general'])): ?>
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data" class="bg-white rounded shadow p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label for="product_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Tên sản phẩm <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="product_name" name="product_name" placeholder="Nhập tên sản phẩm"
                        class="w-full p-3 border rounded focus:outline-none focus:ring focus:border-blue-300 <?= !empty($errors['product_name']) ? 'border-red-500' : 'border-gray-300'; ?>"
                        value="<?= isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : '' ?>">
                    <?php if (!empty($errors['product_name'])): ?>
                    <p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['product_name']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Danh mục <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id"
                        class="w-full p-3 border rounded focus:outline-none focus:ring focus:border-blue-300 <?= !empty($errors['category_id']) ? 'border-red-500' : 'border-gray-300'; ?>">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category_id']) ?>"
                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['category_id'])): ?>
                    <p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['category_id']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Thương hiệu <span class="text-red-500">*</span>
                    </label>
                    <select id="brand_id" name="brand_id"
                        class="w-full p-3 border rounded focus:outline-none focus:ring focus:border-blue-300 <?= !empty($errors['brand_id']) ? 'border-red-500' : 'border-gray-300'; ?>">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($allBrands as $b): ?>
                        <option value="<?= htmlspecialchars($b['brand_id']) ?>"
                            <?= (isset($_POST['brand_id']) && $_POST['brand_id'] == $b['brand_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($b['brand_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['brand_id'])): ?>
                    <p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['brand_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="original_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Giá gốc (VND) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="original_price" name="original_price" placeholder="Nhập giá gốc"
                        class="w-full p-3 border rounded focus:outline-none focus:ring focus:border-blue-300 <?= !empty($errors['original_price']) ? 'border-red-500' : 'border-gray-300'; ?>"
                        value="<?= isset($_POST['original_price']) ? htmlspecialchars($_POST['original_price']) : '' ?>">
                    <?php if (!empty($errors['original_price'])): ?>
                    <p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['original_price']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="discount_price" class="block text-sm font-medium text-gray-700 mb-1">
                        Giá khuyến mãi (VND)
                    </label>
                    <input type="number" id="discount_price" name="discount_price"
                        placeholder="Nhập giá khuyến mãi (nếu có)"
                        class="w-full p-3 border rounded focus:outline-none focus:ring focus:border-blue-300 <?= !empty($errors['discount_price']) ? 'border-red-500' : 'border-gray-300'; ?>"
                        value="<?= isset($_POST['discount_price']) ? htmlspecialchars($_POST['discount_price']) : '' ?>">
                    <?php if (!empty($errors['discount_price'])): ?>
                    <p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['discount_price']) ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Trạng thái <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status"
                        class="w-full p-3 border rounded focus:outline-none focus:ring focus:border-blue-300">
                        <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : ''; ?>>
                            Hiển thị</option>
                        <option value="2" <?= (isset($_POST['status']) && $_POST['status'] == 2) ? 'selected' : ''; ?>>
                            Ẩn</option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                    <p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['status']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- <div>
            <label for="main_image" class="block text-sm font-medium text-gray-700 mb-1">
                Ảnh chính <span class="text-red-500">*</span>
            </label>
            <div id="uploadArea"
                class="relative border-2 border-dashed border-gray-300 rounded-lg h-72 flex items-center justify-center hover:border-blue-400 transition duration-150 overflow-hidden">
                <div id="uploadPlaceholder"
                    class="absolute flex flex-col items-center justify-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                        viewBox="0 0 48 48" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            d="M28 8H20v8H8v20h32V16H28V8z" />
                    </svg>
                    <p class="mt-2 text-gray-600 text-sm">Chọn ảnh hoặc kéo thả vào đây</p>
                </div>
                <img id="imagePreview" src="#" alt="Xem trước ảnh" class="hidden object-contain w-full h-full" />
                <input type="file" id="main_image" name="main_image" accept="image/*"
                    class="absolute inset-0 opacity-0 cursor-pointer">
            </div>
            <?php if (!empty($errors['main_image'])): ?>
            <p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['main_image']) ?></p>
            <?php endif; ?>
        </div> -->
        <div class="mb-4">
            <label for="content-imgs" class="block text-sm font-medium text-gray-700 mb-2">Chọn ảnh</label>
            <input type="file" id="content-imgs" name="content-imgs[]" accept="image/*" multiple
                onchange="previewImagesAdd2(event)"
                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <?php if (isset($messages['images'])): ?>
            <small class="text-red-500 mt-2 block text-xs"><?= htmlspecialchars($messages['images']) ?></small>
            <?php endif; ?>
        </div>

        <!-- Mô tả sản phẩm -->
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Mô tả sản phẩm <span class="text-red-500">*</span>
            </label>
            <textarea id="description" name="description" rows="5" placeholder="Nhập mô tả chi tiết cho sản phẩm"
                class="w-full p-3 border rounded focus:outline-none focus:ring focus:border-blue-300 <?= !empty($errors['description']) ? 'border-red-500' : 'border-gray-300'; ?>"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
            <?php if (!empty($errors['description'])): ?>
            <p class="mt-1 text-xs text-red-500"><?= htmlspecialchars($errors['description']) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white py-2 px-6 rounded shadow transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                <span>Hoàn thành sản phẩm</span>
            </button>
        </div>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainInput = document.getElementById('main_image');
    const imagePreview = document.getElementById('imagePreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const uploadArea = document.getElementById('uploadArea');

    mainInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                uploadPlaceholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            uploadPlaceholder.style.display = 'flex';
            imagePreview.classList.add('hidden');
        }
    });

    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('bg-gray-100');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('bg-gray-100');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('bg-gray-100');
        const files = e.dataTransfer.files;
        mainInput.files = files;
        mainInput.dispatchEvent(new Event('change'));
    });
});
</script>

<?php 
include('../../includes/footer.php');
ob_end_flush();
?>