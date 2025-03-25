<?php
// File: admin/views/product_variant/add_images.php

ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

$product_id = $_GET['product_id'] ?? '';
if (empty($product_id)) {
    echo "Sản phẩm không hợp lệ.";
    exit;
}

$sql = "SELECT variant_id FROM product_variants WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$variants = [];
while ($row = $result->fetch_assoc()) {
    $variants[] = $row['variant_id'];
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    processAddVariantImagesStep3($conn, $product_id, $_POST, $_FILES, $errors);
    if (empty($errors)) {
        header("Location: ../product/index.php");
        exit;
    }
}
?>
<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <!-- Nút quay lại -->
        <a href="../product_variant/add.php?product_id=<?= htmlspecialchars($product_id) ?>"
            class="flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 128 54.7 182.7c-12.5 12.5-12.5 32.8-45.3 0z" />
            </svg>
            <span>Quay lại</span>
        </a>
        <h1 class="text-3xl font-bold">Thêm ảnh cho biến thể</h1>
        <!-- Nút hoàn tất -->
        <a href="../product_variant/add_images.php?product_id=<?= htmlspecialchars($product_id) ?>"
            class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
            <span>Hoàn tất</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 ml-1" fill="currentColor">
                <path
                    d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 133.3 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l205.5 0L233.3 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
            </svg>
        </a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="text-red-500 mb-4">
        <?php foreach ($errors as $error): ?>
        <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <?php foreach ($variants as $variant_id): ?>
        <div class="mb-4">
            <label class="block font-medium">Ảnh cho biến thể: <?= htmlspecialchars($variant_id) ?></label>
            <input type="file" name="variant_images[<?= htmlspecialchars($variant_id) ?>][]" multiple>
        </div>
        <?php endforeach; ?>
        <div class="flex justify-end">
            <button type="submit"
                class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                <span>Hoàn tất</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 ml-1" fill="currentColor">
                    <path
                        d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 133.3 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l205.5 0L233.3 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z" />
                </svg>
            </button>
        </div>
    </form>
</main>

<?php
include("../../includes/footer.php");
ob_end_flush();
?>