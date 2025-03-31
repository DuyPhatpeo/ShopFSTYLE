<?php 
$pageTitle = "Thêm ảnh cho biến thể sản phẩm";

include("../../includes/session_check.php");
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

// Kiểm tra GET parameter 'pid'
if (!isset($_GET['pid'])) {
    header("Location: index.php?msg=ID sản phẩm không hợp lệ.&type=failure");
    exit;
}

$product_id = $_GET['pid'];
$productDetail = getProductDetail($conn, $product_id);
if (!$productDetail) {
    header("Location: index.php?msg=Sản phẩm không tồn tại.&type=failure");
    exit;
}

$variants = $productDetail['variants'];
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    processAddVariantImagesStep3($conn, $product_id, $_POST, $_FILES, $errors);
    if (empty($errors)) {
        header("Location: index.php?msg=Thêm ảnh cho biến thể thành công!&type=success");
        exit;
    }
}
?>

<main class="container mx-auto p-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">Thêm ảnh cho biến thể</h1>
        <a href="index.php"
            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded shadow transition duration-150">
            Quay lại danh sách sản phẩm
        </a>
    </div>

    <div class="mb-6 p-4 bg-gray-100 rounded">
        <p class="text-lg">Sản phẩm: <strong><?= htmlspecialchars($productDetail['product']['product_name']) ?></strong>
        </p>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
        <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data" class="bg-white shadow-lg rounded p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Chọn ảnh cho từng biến thể</h2>

        <?php foreach ($variants as $variant): ?>
        <div class="mb-6 border p-4 rounded">
            <p class="font-medium mb-2">
                Biến thể: Màu - <strong><?= htmlspecialchars($variant['color_id']) ?></strong>,
                Kích cỡ -
                <strong><?= $variant['size_id'] ? htmlspecialchars($variant['size_id']) : 'Không có kích cỡ' ?></strong>
            </p>
            <div class="mb-2">
                <input type="file" name="variant_images[<?= htmlspecialchars($variant['variant_id']) ?>][]" multiple
                    accept="image/*" class="w-full">
            </div>
        </div>
        <?php endforeach; ?>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white py-2 px-6 rounded shadow transition duration-150">
                Lưu ảnh biến thể
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>