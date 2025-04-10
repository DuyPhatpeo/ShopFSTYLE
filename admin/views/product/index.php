<?php
// File: admin/product/index.php

$pageTitle = "Trang quản lý sản phẩm";

// Kiểm tra session đăng nhập
include("../../includes/session_check.php");

// Include header chung cho trang quản trị
include("../../includes/header.php");

// Kết nối CSDL và import các hàm, controller sản phẩm
require_once('../../../includes/db.php'); // Đây phải khởi tạo biến $conn (mysqli connection)
require_once('../../controller/productController.php');
// Trong controller, chúng ta sẽ sử dụng hàm getProductsWithPagination() và các hàm hỗ trợ khác

// Lấy giá trị filter từ URL (nếu có)
$page      = isset($_GET['page'])      ? (int)$_GET['page']      : 1;
$limit     = isset($_GET['limit'])     ? (int)$_GET['limit']     : 10;
$search    = isset($_GET['search'])    ? trim($_GET['search'])    : "";
$brand     = isset($_GET['brand'])     ? trim($_GET['brand'])     : "";
$category  = isset($_GET['category'])  ? trim($_GET['category'])  : "";
$status    = isset($_GET['status'])    ? trim($_GET['status'])    : "";
$sortPrice = isset($_GET['sortPrice']) ? trim($_GET['sortPrice']) : "";

// Gọi hàm lấy sản phẩm có phân trang với filter và sắp xếp theo giá
$data = getProductsWithPagination($conn, $page, $limit, $search, $brand, $category, $status, $sortPrice);

$products      = $data['products'];
$totalPages    = $data['totalPages'];
$currentPage   = $data['currentPage'];
$totalProducts = $data['totalProducts'];

// Lấy danh sách thương hiệu và danh mục cho dropdown filter
$allBrands     = getAllBrands($conn);
$allCategories = getAllCategories($conn);
?>

<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="py-6">
    <div class="container mx-auto p-6">
        <!-- Header: Tiêu đề và nút thêm sản phẩm -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Danh Sách Sản Phẩm</h1>
            <a href="add.php"
                class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow transition flex items-center gap-2">
                <!-- Icon thêm -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm sản phẩm</span>
            </a>
        </div>

        <!-- Thông báo số sản phẩm -->
        <p class="text-sm text-gray-600 mb-4">Tổng cộng: <strong><?= $totalProducts ?></strong> sản phẩm</p>

        <!-- Form tìm kiếm và lọc sản phẩm -->
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <div>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    class="p-2 border border-gray-300 rounded focus:outline-none" placeholder="Tìm sản phẩm...">
            </div>
            <div>
                <select name="brand" class="p-2 border border-gray-300 rounded focus:outline-none">
                    <option value="">Tất cả thương hiệu</option>
                    <?php foreach ($allBrands as $b) : ?>
                    <option value="<?= htmlspecialchars($b['brand_id']) ?>"
                        <?= $brand === $b['brand_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['brand_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="category" class="p-2 border border-gray-300 rounded focus:outline-none">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($allCategories as $cat) : ?>
                    <option value="<?= htmlspecialchars($cat['category_id']) ?>"
                        <?= $category === $cat['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <select name="status" class="p-2 border border-gray-300 rounded focus:outline-none">
                    <option value="">Tất cả trạng thái</option>
                    <option value="1" <?= $status === "1" ? 'selected' : '' ?>>Hiển thị</option>
                    <option value="2" <?= $status === "2" ? 'selected' : '' ?>>Ẩn</option>
                </select>
            </div>
            <div>
                <select name="sortPrice" class="p-2 border border-gray-300 rounded focus:outline-none">
                    <option value="">Sắp xếp theo giá</option>
                    <option value="asc" <?= $sortPrice === "asc" ? 'selected' : '' ?>>Giá tăng dần</option>
                    <option value="desc" <?= $sortPrice === "desc" ? 'selected' : '' ?>>Giá giảm dần</option>
                </select>
            </div>
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <input type="hidden" name="page" value="1">
            <button type="submit"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded shadow transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="white" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span>Tìm kiếm</span>
            </button>
        </form>

        <!-- Bảng hiển thị sản phẩm -->
        <div class="rounded-lg overflow-hidden max-h-[600px] overflow-y-auto transition-all duration-300">
            <table class="w-full table-auto border-collapse">
                <thead class="bg-indigo-500 text-white">
                    <tr>
                        <th class="p-3 text-left">STT</th>
                        <th class="p-3 text-left">Tên sản phẩm</th>
                        <th class="p-3 text-left">Ảnh</th>
                        <th class="p-3 text-left hidden sm:table-cell">Thương hiệu</th>
                        <th class="p-3 text-left hidden sm:table-cell">Danh mục</th>
                        <th class="p-3 text-left hidden sm:table-cell">Giá</th>
                        <th class="p-3 text-left hidden sm:table-cell">Giá KM</th>
                        <th class="p-3 text-center hidden sm:table-cell">Trạng thái</th>
                        <th class="p-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)) : ?>
                    <?php 
                            $stt = ($currentPage - 1) * $limit + 1;
                            foreach ($products as $product) : 
                                $rowClass = ($stt % 2 === 0) ? 'bg-gray-100' : 'bg-white';
                        ?>
                    <tr class="<?= $rowClass ?> hover:bg-gray-200 transition">
                        <td class="p-3"><?= $stt++ ?></td>
                        <td class="p-3"><?= htmlspecialchars($product['product_name']) ?></td>
                        <td class="p-3">
                            <?php if (!empty($product['image_path'])): ?>
                            <img src="../../../uploads/products/<?= htmlspecialchars($product['image_path']) ?>"
                                alt="Ảnh sản phẩm" class="w-16 h-16 object-cover rounded shadow">
                            <?php else: ?>
                            <span class="text-gray-400 italic">Không có ảnh</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 hidden sm:table-cell">
                            <?= !empty($product['brand_name']) ? htmlspecialchars($product['brand_name']) : 'N/A' ?>
                        </td>
                        <td class="p-3 hidden sm:table-cell">
                            <?= !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'N/A' ?>
                        </td>
                        <td class="p-3 hidden sm:table-cell">
                            <span class="font-bold text-blue-600">
                                <?= number_format($product['original_price'], 0, ',', '.') ?> VND
                            </span>
                        </td>
                        <td class="p-3 hidden sm:table-cell">
                            <?php if (!empty($product['discount_price']) && (float)$product['discount_price'] > 0) : ?>
                            <span class="font-bold text-green-600">
                                <?= number_format($product['discount_price'], 0, ',', '.') ?> VND
                            </span>
                            <?php else: ?>
                            -
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-center hidden sm:table-cell">
                            <?php if ($product['status'] == 1): ?>
                            <span class="px-2 py-1 bg-green-200 text-green-800 rounded">Hiển thị</span>
                            <?php elseif ($product['status'] == 2): ?>
                            <span class="px-2 py-1 bg-red-200 text-red-800 rounded">Ẩn</span>
                            <?php else: ?>
                            <span class="px-2 py-1 bg-gray-200 text-gray-800 rounded">Unknown</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex flex-col sm:flex-row items-center gap-1">
                                <a href="detail.php?id=<?= urlencode($product['product_id']) ?>"
                                    class="bg-blue-200 hover:bg-blue-300 p-2 rounded shadow" title="Xem chi tiết">
                                    <!-- Icon chi tiết -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12" y2="8"></line>
                                    </svg>
                                </a>
                                <a href="edit.php?id=<?= urlencode($product['product_id']) ?>"
                                    class="bg-yellow-200 hover:bg-yellow-300 p-2 rounded shadow" title="Chỉnh sửa">
                                    <!-- Icon chỉnh sửa -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </a>
                                <a href="delete.php?id=<?= urlencode($product['product_id']) ?>"
                                    class="bg-red-200 hover:bg-red-300 p-2 rounded shadow"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" title="Xóa">
                                    <!-- Icon xóa -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path>
                                        <path d="M10 11v6"></path>
                                        <path d="M14 11v6"></path>
                                    </svg>
                                </a>
                                <a href="update_quantity.php?id=<?= urlencode($product['product_id']) ?>"
                                    class="bg-purple-200 hover:bg-purple-300 p-2 rounded shadow"
                                    title="Cập nhật số lượng">
                                    <!-- Icon cập nhật số lượng -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" viewBox="0 0 24 24">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="p-3 text-center text-gray-500">Không tìm thấy sản phẩm nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang và lựa chọn số dòng hiển thị -->
        <div class="flex flex-col sm:flex-row items-center justify-between mt-4">
            <div>
                <form method="GET" id="limitForm" class="flex items-center">
                    <input type="hidden" name="page" value="<?= $currentPage ?>">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="brand" value="<?= htmlspecialchars($brand) ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                    <input type="hidden" name="sortPrice" value="<?= htmlspecialchars($sortPrice) ?>">
                    <select name="limit" class="p-2 border rounded cursor-pointer" onchange="this.form.submit()">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
            <div class="mt-4 sm:mt-0">
                <?php 
                    // Giả sử bạn đã có hàm renderPagination() trong file includes/pagination.php
                    require_once('../../includes/pagination.php');
                    renderPagination($currentPage, $totalPages, $limit, $search, $brand, $category, $status, $sortPrice);
                ?>
            </div>
            <div class="text-sm text-gray-600">
                Trang <strong><?= $currentPage ?></strong> trên <strong><?= $totalPages ?></strong>
            </div>
        </div>
    </div>
</main>

<?php include("../../includes/footer.php"); ?>