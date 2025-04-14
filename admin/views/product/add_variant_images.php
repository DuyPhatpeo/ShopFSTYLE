<?php 
$pageTitle = "Upload Ảnh Biến Thể";

// Kiểm tra session đăng nhập
include("../../includes/session_check.php");
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/variantController.php');


?>

<main class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><?= $pageTitle ?></h1>
        <a href="index.php"
            class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded transition duration-150">
            <span>Quay lại</span>
        </a>
    </div>

    <form action="../../controller/productController.php" method="POST" enctype="multipart/form-data"
        class="bg-white rounded shadow p-6 space-y-6">
        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
        <!-- Gửi tên sản phẩm để tạo slug nếu cần -->
        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['product_name'] ?? '') ?>">

        <!-- Mỗi dòng upload ảnh cho một biến thể -->
        <div id="variantImagesContainer">
            <div class="variant-image-item mb-4 border p-4 rounded">
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700">Mã màu (variant_color) <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="variant_color[]" class="w-full p-2 border rounded"
                        placeholder="Nhập mã màu tương ứng với biến thể">
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700">Tên màu (variant_color_name)</label>
                    <input type="text" name="variant_color_name[]" class="w-full p-2 border rounded"
                        placeholder="Nhập tên màu (để hiển thị)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ảnh</label>
                    <input type="file" name="variant_images[]" accept="image/*" class="w-full p-2 border rounded">
                </div>
            </div>
        </div>
        <!-- Nút thêm dòng upload ảnh -->
        <div class="mb-4">
            <button type="button" id="addVariantImageBtn" class="bg-green-500 text-white py-2 px-4 rounded">Thêm dòng
                ảnh</button>
        </div>
        <!-- Nút submit -->
        <div class="flex justify-end">
            <input type="hidden" name="action" value="process_add_variant_images">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">Hoàn
                Thành</button>
        </div>
    </form>
</main>

<script>
document.getElementById('addVariantImageBtn').addEventListener('click', function() {
    const container = document.getElementById('variantImagesContainer');
    const newItem = document.createElement('div');
    newItem.classList.add('variant-image-item', 'mb-4', 'border', 'p-4', 'rounded');
    newItem.innerHTML = `
        <div class="mb-2">
            <label class="block text-sm font-medium text-gray-700">Mã màu (variant_color) <span class="text-red-500">*</span></label>
            <input type="text" name="variant_color[]" class="w-full p-2 border rounded" placeholder="Nhập mã màu tương ứng với biến thể">
        </div>
        <div class="mb-2">
            <label class="block text-sm font-medium text-gray-700">Tên màu (variant_color_name)</label>
            <input type="text" name="variant_color_name[]" class="w-full p-2 border rounded" placeholder="Nhập tên màu (để hiển thị)">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Ảnh</label>
            <input type="file" name="variant_images[]" accept="image/*" class="w-full p-2 border rounded">
        </div>
    `;
    container.appendChild(newItem);
});
</script>

<?php 
include('../../includes/footer.php');
ob_end_flush();
?>