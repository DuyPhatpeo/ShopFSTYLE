<?php 
$pageTitle = "Chỉnh sửa sản phẩm";

include("../../includes/session_check.php");
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

// Kiểm tra GET parameter 'id'
if (!isset($_GET['id'])) {
    header("Location: index.php?msg=ID sản phẩm không hợp lệ.&type=failure");
    exit;
}

$product_id = $_GET['id'];
$product = getProductById($conn, $product_id);
if (!$product) {
    header("Location: index.php?msg=Sản phẩm không tồn tại.&type=failure");
    exit;
}

// Lấy danh sách thương hiệu và danh mục cho dropdown
$allBrands = getAllBrands($conn);
$allCategories = getAllCategories($conn);

// Xử lý form khi submit
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Giả sử bạn có hàm processEditProduct() trong productController.php
    // Hàm này sẽ cập nhật sản phẩm dựa trên dữ liệu POST và file upload (nếu có)
    $errors = processEditProduct($conn, $product_id);
    if (empty($errors)) {
        header("Location: detail.php?id=" . urlencode($product_id) . "&msg=Cập nhật sản phẩm thành công!");
        exit;
    }
}
?>

<main class="container mx-auto p-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Chỉnh sửa sản phẩm</h1>
        <a href="detail.php?id=<?= urlencode($product_id) ?>"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded shadow transition">
            Quay lại chi tiết
        </a>
    </div>

    <?php if (!empty($errors['general'])): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="bg-white shadow rounded p-6">
        <div class="flex flex-wrap -mx-4 mb-6">
            <!-- Cột 1: Tên, Danh mục, Thương hiệu -->
            <div class="w-full md:w-1/2 px-4">
                <div class="mb-4">
                    <label for="product_name" class="block mb-1 font-medium">
                        Tên sản phẩm: <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="product_name" id="product_name" placeholder="Nhập tên sản phẩm"
                        class="w-full p-2 border <?= !empty($errors['product_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= htmlspecialchars($product['product_name']) ?>">
                    <?php if (!empty($errors['product_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['product_name']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="category_id" class="block mb-1 font-medium">
                        Danh mục: <span class="text-red-600">*</span>
                    </label>
                    <select name="category_id" id="category_id"
                        class="w-full p-2 border <?= !empty($errors['category_id']) ? 'border-red-500' : 'border-gray-300'; ?> rounded">
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category_id']) ?>"
                            <?= ($product['category_id'] === $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['category_id'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['category_id']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="brand_id" class="block mb-1 font-medium">
                        Thương hiệu: <span class="text-red-600">*</span>
                    </label>
                    <select name="brand_id" id="brand_id"
                        class="w-full p-2 border <?= !empty($errors['brand_id']) ? 'border-red-500' : 'border-gray-300'; ?> rounded">
                        <option value="">-- Chọn thương hiệu --</option>
                        <?php foreach ($allBrands as $b): ?>
                        <option value="<?= htmlspecialchars($b['brand_id']) ?>"
                            <?= ($product['brand_id'] === $b['brand_id']) ? 'selected' : '' ?>>
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
                <div class="mb-4">
                    <label for="original_price" class="block mb-1 font-medium">
                        Giá gốc (VND): <span class="text-red-600">*</span>
                    </label>
                    <input type="number" name="original_price" id="original_price" placeholder="Nhập giá gốc"
                        class="w-full p-2 border <?= !empty($errors['original_price']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= htmlspecialchars($product['original_price']) ?>">
                    <?php if (!empty($errors['original_price'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['original_price']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="discount_price" class="block mb-1 font-medium">
                        Giá khuyến mãi (VND):
                    </label>
                    <input type="number" name="discount_price" id="discount_price"
                        placeholder="Nhập giá khuyến mãi (nếu có)"
                        class="w-full p-2 border <?= !empty($errors['discount_price']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= htmlspecialchars($product['discount_price']) ?>">
                    <?php if (!empty($errors['discount_price'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['discount_price']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">
                        Trạng thái:
                        <span class="text-red-600">*</span>
                    </label>
                    <select name="status" id="status"
                        class="w-full p-2 border <?= !empty($errors['status']) ? 'border-red-500' : 'border-gray-300'; ?> rounded">
                        <option value="1" <?= ($product['status'] == 1) ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="2" <?= ($product['status'] == 2) ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['status']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- Phần mô tả sản phẩm -->
        <div class="mb-6">
            <label for="descriptionProduct" class="block mb-1 font-medium">Mô tả sản phẩm:</label>
            <textarea name="descriptionProduct" id="descriptionProduct" rows="8" placeholder="Nhập mô tả sản phẩm"
                class="w-full p-2 border border-gray-300 rounded"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>
        <div class="flex justify-end">
            <button type="submit"
                class="flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white py-2 px-6 rounded shadow transition duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                <span>Cập nhật sản phẩm</span>
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>