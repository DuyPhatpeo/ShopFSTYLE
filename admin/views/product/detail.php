<?php
$pageTitle = "Chi tiết sản phẩm";
include("../../includes/session_check.php");

ob_start();
include("../../includes/header.php");
require_once("../../../includes/db.php");
require_once("../../controller/productController.php");

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($product_id)) {
    die("ID sản phẩm không hợp lệ.");
}

$product = getProductById($conn, $product_id);
if (!$product) {
    die("Sản phẩm không tồn tại.");
}
?>

<main class="container mx-auto p-6">
    <!-- Tiêu đề và nút hành động -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-4xl font-bold text-blue-700">Chi Tiết Sản Phẩm</h1>
            <p class="text-lg text-gray-500">ID: <?= htmlspecialchars($product['product_id']) ?></p>
        </div>
        <div class="flex gap-2">
            <!-- Sửa -->
            <a href="edit.php?id=<?= urlencode($product['product_id']) ?>"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded flex items-center shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z" />
                </svg>
                <span class="hidden md:inline">Sửa</span>
            </a>

            <!-- Xoá -->
            <a href="delete.php?id=<?= urlencode($product['product_id']) ?>"
                onclick="return confirm('Bạn có chắc muốn xoá sản phẩm này?');"
                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded flex items-center shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path d="M3 6h18M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6" />
                    <path d="M10 11v6M14 11v6" />
                </svg>
                <span class="hidden md:inline">Xoá</span>
            </a>

            <!-- Quay lại -->
            <a href="index.php"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded flex items-center shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" viewBox="0 0 448 512" fill="currentColor">
                    <path
                        d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H109.2L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L9.4 233.4z" />
                </svg>
                <span class="hidden md:inline">Quay lại</span>
            </a>
        </div>
    </div>

    <!-- Thông tin chi tiết -->
    <div class="bg-white shadow-lg rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Thông tin -->
            <div class="space-y-3 text-lg text-gray-700">
                <p><strong>Tên sản phẩm:</strong> <?= htmlspecialchars($product['product_name']) ?></p>
                <p><strong>Giá gốc:</strong> <?= number_format($product['original_price'], 0, ',', '.') ?>₫</p>
                <p><strong>Giá giảm:</strong> <?= number_format($product['discount_price'], 0, ',', '.') ?>₫</p>
                <p><strong>Thương hiệu:</strong> <?= htmlspecialchars($product['brand_name'] ?? 'Chưa xác định') ?></p>
                <p><strong>Danh mục:</strong> <?= htmlspecialchars($product['category_name'] ?? 'Chưa xác định') ?></p>
                <p><strong>Trạng thái:</strong>
                    <?= $product['status'] == 1
                        ? '<span class="text-green-600">Hiển thị</span>'
                        : '<span class="text-gray-500">Ẩn</span>' ?>
                </p>
                <p><strong>Ngày tạo:</strong> <?= htmlspecialchars($product['created_at']) ?></p>
            </div>

            <!-- Ảnh -->
            <div class="flex justify-center items-center">
                <?php if (!empty($product['main_image'])): ?>
                <img src="../../../<?= htmlspecialchars($product['main_image']) ?>" alt="Ảnh sản phẩm"
                    class="max-h-64 object-contain border rounded-lg shadow">
                <?php else: ?>
                <span class="italic text-gray-400">Không có ảnh</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mô tả -->
        <div class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Mô tả sản phẩm</h2>
            <div class="bg-gray-100 border rounded p-4 text-gray-700 whitespace-pre-line max-h-96 overflow-auto">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
        </div>
    </div>
</main>

<?php
include("../../includes/footer.php");
ob_end_flush();
?>