<?php 
include("../../includes/header.php");
require_once('../../../includes/db.php');               // Kết nối CSDL
require_once('../../controller/productController.php');  // Controller product (chứa hàm getProductsWithPagination)

// Lấy giá trị từ URL hoặc gán giá trị mặc định
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Gọi hàm lấy dữ liệu sản phẩm có phân trang, tìm kiếm
  // tổng số sản phẩm
?>

<main>
    <div class="container mx-auto p-6">
        <!-- Header: Tiêu đề và nút thêm sản phẩm -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">
                Danh Sách Sản Phẩm
            </h1>
            <a href="add.php"
                class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow-md transition flex items-center space-x-2">
                <!-- Icon Thêm -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="white"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span class="hidden sm:inline">Thêm sản phẩm</span>
            </a>
        </div>

        <!-- Thanh tìm kiếm -->
        <div class="flex justify-end mb-4">
            <form method="GET" class="flex">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    class="p-2 border border-gray-300 rounded-l-lg focus:outline-none" placeholder="Tìm sản phẩm...">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white p-2 border border-gray-300 rounded-r-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="white" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Bảng hiển thị sản phẩm -->
        <div class="rounded-lg overflow-hidden max-h-[600px] overflow-y-auto transition-all duration-300">
            <table class="w-full border-collapse text-sm sm:text-base">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-indigo-500 text-white">
                        <th class="p-2 sm:p-3 text-left">STT</th>
                        <th class="p-2 sm:p-3 text-left whitespace-nowrap">Mã sản phẩm</th>
                        <th class="p-2 sm:p-3 text-center">Ảnh</th>
                        <th class="p-2 sm:p-3 text-left">Tên sản phẩm</th>
                        <th class="p-2 sm:p-3 text-left">Thương hiệu</th>
                        <th class="p-2 sm:p-3 text-left">Danh mục</th>
                        <th class="p-2 sm:p-3 text-right">Giá gốc</th>
                        <th class="p-2 sm:p-3 text-right">Giá giảm</th>
                        <th class="p-2 sm:p-3 text-left">Trạng thái</th>
                        <th class="p-2 sm:p-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if ($products->num_rows > 0): ?>
                    <?php
                        // Tính số thứ tự bắt đầu dựa vào trang hiện tại
                        $stt = ($currentPage - 1) * $limit + 1;
                        while ($prod = $products->fetch_assoc()):
                            $rowClass = ($stt % 2 === 0) ? 'bg-gray-100' : 'bg-white';
                            
                            // Nếu có ảnh chính, bạn có thể lấy từ cột hoặc JOIN bảng productimage.
                            // Ở đây minh họa để rỗng, bạn có thể thay thế bằng $prod['main_image_url'] nếu có.
                            $mainImageUrl = "";
                    ?>
                    <tr class="border-b <?= $rowClass ?> hover:bg-gray-200 transition">
                        <td class="p-2 sm:p-3 align-middle"><?= $stt++ ?></td>
                        <td class="p-2 sm:p-3 align-middle whitespace-nowrap">
                            #<?= htmlspecialchars($prod['product_id']) ?>
                        </td>
                        <!-- Ảnh sản phẩm -->
                        <td class="p-2 sm:p-3 text-center align-middle">
                            <?php if (!empty($mainImageUrl)): ?>
                            <img src="<?= htmlspecialchars($mainImageUrl) ?>"
                                alt="<?= htmlspecialchars($prod['product_name']) ?>"
                                class="w-20 h-20 object-cover rounded mx-auto">
                            <?php else: ?>
                            <span class="text-gray-500 text-xs">No image</span>
                            <?php endif; ?>
                        </td>
                        <!-- Tên sản phẩm -->
                        <td class="p-2 sm:p-3 font-medium align-middle">
                            <?= htmlspecialchars($prod['product_name']) ?>
                        </td>
                        <!-- Thương hiệu -->
                        <td class="p-2 sm:p-3 align-middle">
                            <?= htmlspecialchars($prod['brand_name'] ?? 'N/A') ?>
                        </td>
                        <!-- Danh mục -->
                        <td class="p-2 sm:p-3 align-middle">
                            <?= htmlspecialchars($prod['category_name'] ?? 'N/A') ?>
                        </td>
                        <!-- Giá gốc -->
                        <td class="p-2 sm:p-3 align-middle text-right">
                            <?= number_format($prod['original_price'], 2) ?>
                        </td>
                        <!-- Giá giảm -->
                        <td class="p-2 sm:p-3 align-middle text-right">
                            <?= number_format($prod['discount_price'], 2) ?>
                        </td>
                        <!-- Trạng thái -->
                        <td class="p-2 sm:p-3 align-middle">
                            <?php 
                                if ($prod['status'] == 1) {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-green-200 text-green-800 font-semibold rounded-lg shadow-md">On</span>';
                                } elseif ($prod['status'] == 2) {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-red-200 text-red-800 font-semibold rounded-lg shadow-md">Off</span>';
                                } else {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow-md">Unknown</span>';
                                }
                            ?>
                        </td>
                        <!-- Hành động -->
                        <td class="p-2 sm:p-3 text-center align-middle">
                            <div class="inline-flex gap-1">
                                <!-- Nút Chi tiết -->
                                <a href="detail.php?id=<?= urlencode($prod['product_id']) ?>"
                                    class="bg-gray-200 hover:bg-gray-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                    title="Chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </a>
                                <!-- Nút Sửa -->
                                <a href="edit.php?id=<?= urlencode($prod['product_id']) ?>"
                                    class="bg-blue-200 hover:bg-blue-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                    title="Sửa">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </a>
                                <!-- Nút Xoá -->
                                <a href="delete.php?id=<?= urlencode($prod['product_id']) ?>"
                                    class="bg-red-200 hover:bg-red-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                    title="Xoá" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path>
                                        <path d="M10 11v6"></path>
                                        <path d="M14 11v6"></path>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="10" class="p-3 text-center text-gray-500">
                            Không tìm thấy sản phẩm nào.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang và dropdown chọn số dòng -->
        <div class="grid grid-cols-1 sm:grid-cols-3 items-center mt-4 gap-4 mb-6">
            <!-- Chọn số dòng hiển thị -->
            <div class="flex justify-start items-center">
                <form method="GET" id="limitForm" class="flex items-center">
                    <!-- Giữ lại page & search khi thay đổi limit -->
                    <input type="hidden" name="page" value="<?= $currentPage ?>">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <select name="limit" id="limit" class="p-2 border rounded cursor-pointer"
                        onchange="this.form.submit()">
                        <option value="10" <?= $limit == 10  ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $limit == 20  ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $limit == 50  ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>

            <!-- Khu vực phân trang (giữa) -->
            <div class="flex justify-center">
                <?php
                // Giả sử bạn có hàm renderPagination giống như ở trang danh mục
                require_once('../../includes/pagination.php');
                renderPagination($currentPage, $totalPages, $limit, $search);
                ?>
            </div>

            <!-- Hiển thị tổng số sản phẩm -->
            <div class="text-right text-gray-600">
                Tổng: <?= $totalItems ?> sản phẩm
            </div>
        </div>
    </div>
</main>

<?php include('../../includes/footer.php'); ?>