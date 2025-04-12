<?php
$pageTitle = "Chi tiết sản phẩm";
include("../../includes/session_check.php");

ob_start();
include("../../includes/header.php");
require_once("../../../includes/db.php");
require_once("../../controller/productController.php");
require_once("../../controller/variantController.php"); // Import controller biến thể

// Lấy ID sản phẩm từ URL
$product_id = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($product_id)) {
    die("ID sản phẩm không hợp lệ.");
}

$product = getProductById($conn, $product_id);
if (!$product) {
    die("Sản phẩm không tồn tại.");
}

// Lấy danh sách biến thể của sản phẩm
// Nếu số biến thể không quá nhiều bạn có thể không dùng phân trang
// Ví dụ dưới đây có sử dụng phân trang với tham số variant_page để tách riêng phần phân trang của biến thể
$page    = isset($_GET['variant_page']) ? (int)$_GET['variant_page'] : 1;
$limit   = 10; // Số dòng mỗi trang (điều chỉnh theo nhu cầu)
$variantsData = getVariantsWithPagination($conn, $product_id, $page, $limit);
$variants     = $variantsData['variants'];
$totalPages   = $variantsData['totalPages'];
$currentPage  = $variantsData['currentPage'];
?>

<main class="container mx-auto p-6">
    <!-- Phần tiêu đề và các nút thao tác của sản phẩm -->
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

    <!-- Thông tin chi tiết sản phẩm -->
    <div class="bg-white shadow-lg rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Thông tin sản phẩm -->
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

            <!-- Ảnh sản phẩm -->
            <div class="flex justify-center items-center">
                <?php if (!empty($product['main_image'])): ?>
                <img src="../../../<?= htmlspecialchars($product['main_image']) ?>" alt="Ảnh sản phẩm"
                    class="max-h-64 object-contain border rounded-lg shadow">
                <?php else: ?>
                <span class="italic text-gray-400">Không có ảnh</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mô tả sản phẩm -->
        <div class="mt-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">Mô tả sản phẩm</h2>
            <div class="bg-gray-100 border rounded p-4 text-gray-700 whitespace-pre-line max-h-96 overflow-auto">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </div>
        </div>
    </div>

    <!-- Phần quản lý biến thể -->
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-3xl font-bold text-blue-700 mb-4">Biến thể của sản phẩm</h2>

        <!-- Nút Thêm biến thể mới -->
        <div class="mb-4">
            <a href="add_variant.php?product_id=<?= urlencode($product_id) ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center shadow">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden md:inline">Thêm biến thể</span>
            </a>
        </div>

        <?php if ($variants && $variants->num_rows > 0): ?>

        <table class="w-full table-auto border-collapse text-sm sm:text-base">
            <thead class="bg-indigo-500 text-white">
                <tr>
                    <th class="p-3 text-left">Màu Sắc</th>
                    <th class="p-3 text-left">Kích Thước</th>
                    <th class="p-3 text-left">Số Lượng</th>
                    <th class="p-3 text-left">Trạng Thái</th>
                    <th class="p-3 text-center">Hành động</th>
                </tr>
            </thead>
            <div class="rounded-lg overflow-x-auto max-h-[600px] overflow-y-auto transition-all duration-300">
                <tbody>
                    <?php 
                    $stt = ($currentPage - 1) * $limit + 1;
                    while($variant = $variants->fetch_assoc()):
                        // Lựa chọn màu cho dòng: xen kẽ giữa bg-gray-100 và bg-white.
                        $rowClass = ($stt % 2 === 0) ? 'bg-gray-100' : 'bg-white';
                    ?>
                    <tr class="<?= $rowClass ?> hover:bg-gray-200 transition">
                        <td class="p-3"><?= htmlspecialchars($variant['color_name'] ?? 'Chưa chọn') ?></td>
                        <td class="p-3"><?= htmlspecialchars($variant['size_name'] ?? 'Chưa chọn') ?></td>
                        <td class="p-3"><?= htmlspecialchars($variant['quantity']) ?></td>
                        <td class="p-3">
                            <?php 
                            // Nếu status = 1 thì coi như còn hàng, nếu = 0 thì hết hàng
                            echo $variant['status'] 
                                ? '<span class="px-2 py-1 bg-green-200 text-green-800 rounded">Còn hàng</span>' 
                                : '<span class="px-2 py-1 bg-red-200 text-red-800 rounded">Hết hàng</span>'; 
                            ?>
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex flex-col sm:flex-row items-center gap-1">
                                <!-- Form Thêm số lượng -->
                                <form method="POST" action="processAddQuantity.php"
                                    class="flex flex-col sm:flex-row gap-1">
                                    <input type="hidden" name="variant_id"
                                        value="<?= urlencode($variant['variant_id']) ?>">
                                    <input type="hidden" name="product_id" value="<?= urlencode($product_id) ?>">
                                    <input type="number" name="quantity_to_add" placeholder="Nhập số lượng"
                                        class="border p-1 rounded w-24" required>
                                    <button type="submit"
                                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                                        Thêm
                                    </button>
                                </form>
                                <!-- Nút Xoá biến thể -->
                                <a href="processVariantDelete.php?variant_id=<?= urlencode($variant['variant_id']) ?>&product_id=<?= urlencode($product_id) ?>"
                                    onclick="return confirm('Bạn có chắc muốn xoá biến thể này?');"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded">
                                    Xoá
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </div>
        </table>

        <!-- Phân trang biến thể -->
        <div class="mt-4">
            <nav class="flex justify-center">
                <ul class="inline-flex">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <li
                        class="<?= ($i == $currentPage) ? 'bg-blue-500 text-white' : 'bg-white text-blue-500' ?> border px-3 py-1">
                        <a href="?id=<?= urlencode($product_id) ?>&page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

        <?php else: ?>
        <div class="bg-gray-200 p-4 rounded">
            <p class="text-gray-700">Sản phẩm này chưa có biến thể.</p>
        </div>
        <?php endif; ?>
    </div>


</main>

<?php
include("../../includes/footer.php");
ob_end_flush();
?>