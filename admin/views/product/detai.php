<?php 
$pageTitle = "Chi tiết sản phẩm";

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
$productDetail = getProductDetail($conn, $product_id);
if (!$productDetail) {
    header("Location: index.php?msg=Sản phẩm không tồn tại.&type=failure");
    exit;
}

$product = $productDetail['product'];
$variants = $productDetail['variants'];
?>

<main class="container mx-auto p-6">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
        <h1 class="text-3xl font-bold"><?= htmlspecialchars($product['product_name']) ?></h1>
        <a href="edit.php?id=<?= urlencode($product['product_id']) ?>"
            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded shadow transition">
            Chỉnh sửa sản phẩm
        </a>
    </div>

    <div class="mb-6 p-4 bg-gray-100 rounded">
        <p><strong>Giá gốc:</strong> <?= number_format($product['original_price'], 0, ',', '.') ?> VND</p>
        <p><strong>Giá khuyến mãi:</strong>
            <?= (!empty($product['discount_price']) && (float)$product['discount_price'] > 0) ? number_format($product['discount_price'], 0, ',', '.') . " VND" : "-" ?>
        </p>
        <p><strong>Trạng thái:</strong>
            <?= ($product['status'] == 1) ? '<span class="bg-green-200 text-green-800 px-2 py-1 rounded">Hiển thị</span>' : '<span class="bg-red-200 text-red-800 px-2 py-1 rounded">Ẩn</span>' ?>
        </p>
        <p><strong>Mô tả:</strong></p>
        <div class="p-2 border rounded bg-white"><?= htmlspecialchars($product['description']) ?></div>
    </div>

    <div>
        <h2 class="text-2xl font-semibold mb-4">Các biến thể sản phẩm</h2>
        <?php if (!empty($variants)): ?>
        <?php foreach ($variants as $variant): ?>
        <div class="mb-6 p-4 border rounded">
            <p><strong>Màu:</strong> <?= htmlspecialchars($variant['color_id']) ?>
                <!-- Bạn có thể thay thế bằng tên màu nếu có mối liên hệ với bảng colors -->
            </p>
            <p><strong>Kích cỡ:</strong>
                <?= !empty($variant['size_id']) ? htmlspecialchars($variant['size_id']) : 'Không có kích cỡ' ?></p>
            <p><strong>Số lượng:</strong> <?= htmlspecialchars($variant['quantity']) ?></p>
            <?php if (!empty($variant['images'])): ?>
            <div class="flex flex-wrap gap-2 mt-2">
                <?php foreach ($variant['images'] as $img): ?>
                <img src="../../../<?= htmlspecialchars($img['image_url']) ?>"
                    alt="<?= htmlspecialchars($img['caption']) ?>" class="w-24 h-24 object-cover border rounded">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p>Không có biến thể nào.</p>
        <?php endif; ?>
    </div>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>