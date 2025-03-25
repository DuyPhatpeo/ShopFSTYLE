<?php 
// File: admin/views/product_variant/add.php

ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

$product_id = $_GET['product_id'] ?? '';
if (empty($product_id)) {
    echo "Sản phẩm không hợp lệ.";
    exit;
}
$colors = getAllColors($conn);
$sizes  = getAllSizes($conn);

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    processAddVariantsStep2($conn, $product_id, $_POST, $_FILES, $errors);
    if (isset($_FILES['variant_images'])) {
        processAddVariantImagesStep3($conn, $product_id, $_POST, $_FILES, $errors);
    }
    if (empty($errors)) {
        header("Location: ../product/index.php");
        exit;
    }
}
?>
<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <!-- Nút quay lại -->
        <a href="../product/add.php"
            class="flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 128 54.7 182.7c-12.5 12.5-12.5 32.8-45.3 0z" />
            </svg>
            <span>Quay lại</span>
        </a>
        <h1 class="text-3xl font-bold">Thêm biến thể cho sản phẩm</h1>
        <!-- Nút tiếp tục -->
        <a href="../product_variant/add.php?product_id=<?= htmlspecialchars($product_id) ?>"
            class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
            <span>Tiếp tục</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 ml-1" fill="currentColor">
                <path
                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 133.3 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l205.5 0L233.3 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
            </svg>
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="text-red-500 mb-4">
        <?php foreach($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block font-medium">Nhập thông tin biến thể (nhiều dòng):</label>
            <div id="variantsContainer">
                <div class="variant-row flex items-center gap-4 mb-2">
                    <select name="variant_color[]" class="border p-2">
                        <option value="">-- Chọn màu --</option>
                        <?php foreach ($colors as $color): ?>
                        <option value="<?= htmlspecialchars($color['color_id']) ?>">
                            <?= htmlspecialchars($color['color_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="variant_size[]" class="border p-2">
                        <option value="">-- Chọn kích thước --</option>
                        <?php foreach ($sizes as $size): ?>
                        <option value="<?= htmlspecialchars($size['size_id']) ?>">
                            <?= htmlspecialchars($size['size_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" name="variant_quantity[]" class="border p-2 w-20" placeholder="Số lượng"
                        min="0">
                    <button type="button" class="remove-variant bg-red-500 text-white p-1 rounded">X</button>
                </div>
            </div>
            <button type="button" id="addVariantRow" class="bg-blue-500 text-white p-2 rounded mt-2">
                Thêm dòng biến thể
            </button>
        </div>

        <div class="mb-4">
            <label class="block font-medium">Hoặc, nếu sản phẩm không có biến thể, nhập số lượng chung:</label>
            <input type="number" name="default_quantity" class="border p-2 w-40" placeholder="Số lượng chung" min="0">
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                <span>Tiếp tục</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 ml-1" fill="currentColor">
                    <path
                        d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 133.3 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l205.5 0L233.3 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
                </svg>
            </button>
        </div>
    </form>
</main>

<script>
document.getElementById('addVariantRow').addEventListener('click', function() {
    var container = document.getElementById('variantsContainer');
    var newRow = container.querySelector('.variant-row').cloneNode(true);
    newRow.querySelectorAll('select, input').forEach(function(input) {
        input.value = "";
    });
    container.appendChild(newRow);
});
document.getElementById('variantsContainer').addEventListener('click', function(e) {
    if (e.target && e.target.classList.contains('remove-variant')) {
        var container = document.getElementById('variantsContainer');
        if (container.querySelectorAll('.variant-row').length > 1) {
            e.target.closest('.variant-row').remove();
        }
    }
});
</script>

<?php 
include("../../includes/footer.php");
ob_end_flush();
?>