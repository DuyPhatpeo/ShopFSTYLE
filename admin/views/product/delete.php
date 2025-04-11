<?php
$pageTitle = "Trang xoá sản phẩm";

include("../../includes/session_check.php");
ob_start();
include("../../includes/header.php");

require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

// Kiểm tra ID sản phẩm
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

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (processDeleteProduct($conn, $product_id)) {
        header("Location: index.php?msg=Đã xoá sản phẩm thành công!&type=success");
        exit;
    } else {
        header("Location: index.php?msg=Không thể xoá sản phẩm!&type=failure");
        exit;
    }
}
?>

<main class="container mx-auto p-6">
    <!-- Tiêu đề + nút quay lại -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-4xl font-bold text-red-700">Xoá Sản Phẩm</h1>
            <p class="text-lg text-gray-500">ID: <?= htmlspecialchars($product['product_id']) ?></p>
        </div>
        <a href="index.php?msg=Hủy"
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 448 512">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 
                416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 224 214.6 118.6c12.5-12.5 12.5-32.8 
                0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <!-- Hộp xác nhận xoá -->
    <div class="bg-white shadow-lg rounded-lg p-6">
        <p class="text-xl text-gray-800 mb-4">
            Bạn có chắc chắn muốn <strong class="text-red-600">xoá sản phẩm</strong> với các thông tin sau:
        </p>

        <!-- Thông tin sản phẩm -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="space-y-3 text-lg text-gray-700">
                <p><strong>Tên sản phẩm:</strong> <?= htmlspecialchars($product['product_name']) ?></p>
                <p><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <p><strong>Giá gốc:</strong> <?= number_format($product['original_price'], 0, ',', '.') ?>₫</p>
                <p><strong>Giá giảm:</strong> <?= number_format($product['discount_price'], 0, ',', '.') ?>₫</p>
                <p><strong>Thương hiệu:</strong> <?= htmlspecialchars($product['brand_name']) ?></p>
                <p><strong>Danh mục:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                <p><strong>Trạng thái:</strong>
                    <?= $product['status'] == 1 ? '<span class="text-green-600">Hiển thị</span>' : '<span class="text-gray-500">Ẩn</span>' ?>
                </p>
                <p><strong>Ngày tạo:</strong> <?= htmlspecialchars($product['created_at']) ?></p>
            </div>

            <div class="flex justify-center items-center">
                <?php if (!empty($product['main_image'])): ?>
                <img src="../../../<?= htmlspecialchars($product['main_image']) ?>"
                    alt="<?= htmlspecialchars($product['product_name']) ?>"
                    class="max-h-64 object-contain border rounded-lg shadow">
                <?php else: ?>
                <span class="italic text-gray-400">Không có ảnh</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Nút xác nhận xoá -->
        <form method="POST" action="">
            <div class="flex justify-end items-center space-x-4">
                <a href="index.php" class="px-4 py-2 rounded border border-gray-400 text-gray-700 hover:bg-gray-100">
                    Huỷ
                </a>
                <button type="submit"
                    class="bg-red-700 hover:bg-red-800 text-white px-4 py-2 rounded flex items-center shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 
                              0 011 1v1H9V4a1 1 0 011-1z" />
                    </svg>
                    <span>Xác nhận xoá</span>
                </button>
            </div>
        </form>
    </div>
</main>

<?php
include("../../includes/footer.php");
ob_end_flush();
?>