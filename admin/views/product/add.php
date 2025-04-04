<?php 
$pageTitle = "Trang thêm sản phẩm";

// Kiểm tra session đăng nhập
include("../../includes/session_check.php");

// Bật output buffering
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');               // Kết nối CSDL
require_once('../../controller/productController.php'); // File controller sản phẩm

// Lấy danh sách thương hiệu và danh mục cho dropdown
$allBrands = getAllBrands($conn);
$allCategories = getAllCategories($conn);

// Xử lý thêm sản phẩm nếu form được submit
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = processAddProductStep1($conn, $_POST, $_FILES, $errors);
    if ($product_id) {
        // Nếu thêm thành công, chuyển sang trang thêm biến thể (bước 2)
        header("Location: add_variant.php?pid=" . urlencode($product_id));
        exit;
    }
}
?>

<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Thêm Sản Phẩm</h1>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <!-- Hiển thị lỗi chung nếu có -->
    <?php if (!empty($errors['general'])): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <!-- Form thêm sản phẩm -->
    <form method="POST" action="" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        <div class="flex flex-wrap -mx-4 mb-6">
            <!-- Cột 1: Tên, Danh mục, Thương hiệu -->
            <div class="w-full md:w-1/2 px-4">
                <!-- Tên sản phẩm -->
                <div class="mb-4">
                    <label for="product_name" class="block mb-1 font-medium">
                        Tên sản phẩm: <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="product_name" id="product_name" placeholder="Nhập tên sản phẩm"
                        class="w-full p-2 border <?= !empty($errors['product_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : '' ?>">
                    <?php if (!empty($errors['product_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['product_name']) ?></p>
                    <?php endif; ?>
                </div>
                <!-- Chọn danh mục -->
                <div class="mb-4">
                    <label for="category_id" class="block mb-1 font-medium">
                        Danh mục: <span class="text-red-600">*</span>
                    </label>
                    <select name="category_id" id="category_id"
                        class="w-full p-2 border <?= !empty($errors['category_id']) ? 'border-red-500' : 'border-gray-300'; ?> rounded">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category_id']) ?>"
                            <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['category_id'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['category_id']) ?></p>
                    <?php endif; ?>
                </div>
                <!-- Chọn thương hiệu -->
                <div class="mb-4">
                    <label for="brand_id" class="block mb-1 font-medium">
                        Thương hiệu: <span class="text-red-600">*</span>
                    </label>
                    <select name="brand_id" id="brand_id"
                        class="w-full p-2 border <?= !empty($errors['brand_id']) ? 'border-red-500' : 'border-gray-300'; ?> rounded">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($allBrands as $b): ?>
                        <option value="<?= htmlspecialchars($b['brand_id']) ?>"
                            <?= (isset($_POST['brand_id']) && $_POST['brand_id'] == $b['brand_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($b['brand_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['brand_id'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['brand_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Cột 2: Giá, Giá khuyến mãi, Trạng thái -->
            <div class="w-full md:w-1/2 px-4">
                <!-- Giá gốc -->
                <div class="mb-4">
                    <label for="original_price" class="block mb-1 font-medium">
                        Giá gốc (VND): <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="original_price" id="original_price" placeholder="Nhập giá gốc"
                        class="w-full p-2 border <?= !empty($errors['original_price']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['original_price']) ? htmlspecialchars($_POST['original_price']) : '' ?>">
                    <?php if (!empty($errors['original_price'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['original_price']) ?></p>
                    <?php endif; ?>
                </div>
                <!-- Giá khuyến mãi -->
                <div class="mb-4">
                    <label for="discount_price" class="block mb-1 font-medium">
                        Giá khuyến mãi (VND):
                    </label>
                    <input type="number" name="discount_price" id="discount_price"
                        placeholder="Nhập giá khuyến mãi (nếu có)"
                        class="w-full p-2 border <?= !empty($errors['discount_price']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['discount_price']) ? htmlspecialchars($_POST['discount_price']) : '' ?>">
                    <?php if (!empty($errors['discount_price'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['discount_price']) ?></p>
                    <?php endif; ?>
                </div>
                <!-- Trạng thái sản phẩm -->
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">
                        Trạng thái:
                        <span class="text-red-600">*</span>
                    </label>
                    <select name="status" id="status"
                        class="w-full p-2 border <?= !empty($errors['status']) ? 'border-red-500' : 'border-gray-300'; ?> rounded">
                        <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>
                            Hiển thị</option>
                        <option value="2" <?= (isset($_POST['status']) && $_POST['status'] == 2) ? 'selected' : '' ?>>Ẩn
                        </option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['status']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Phần mô tả sản phẩm (textarea) -->
        <div class="mb-6">
            <label for="descriptionProduct" class="block mb-1 font-medium">Mô tả sản phẩm:</label>
            <textarea name="descriptionProduct" id="descriptionProduct" rows="8" placeholder="Nhập mô tả sản phẩm"
                class="w-full p-2 border border-gray-300 rounded"><?= isset($_POST['descriptionProduct']) ? htmlspecialchars($_POST['descriptionProduct']) : '' ?></textarea>
        </div>

        <!-- Nút submit -->
        <div class="flex justify-end items-center">
            <button type="submit"
                class="flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded shadow transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                <span>Thêm sản phẩm</span>
            </button>
        </div>
    </form>
</main>

<!-- (Nếu không sử dụng upload ảnh, bạn có thể xoá phần JS preview ảnh) -->
<script>
document.getElementById('image').addEventListener('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var preview = document.getElementById('imagePreview');
            preview.src = event.target.result;
            preview.classList.remove('hidden');
            document.getElementById('uploadPlaceholder').style.display = 'none';
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('uploadPlaceholder').style.display = 'flex';
        document.getElementById('imagePreview').classList.add('hidden');
    }
});

var uploadArea = document.getElementById('uploadArea');
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
    var dt = e.dataTransfer;
    var files = dt.files;
    document.getElementById('image').files = files;
    var event = new Event('change');
    document.getElementById('image').dispatchEvent(event);
});
</script>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>