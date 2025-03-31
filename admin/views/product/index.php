<?php 
$pageTitle = "Trang quản lý sản phẩm";

// Kiểm tra session đăng nhập
include("../../includes/session_check.php");

// Include header chung cho trang quản trị
include("../../includes/header.php");

// Kết nối CSDL và import các hàm phụ trợ sản phẩm
require_once('../../../includes/db.php');
require_once('../../controller/productController.php');

// Lấy giá trị filter từ URL (nếu có)
$page      = isset($_GET['page'])      ? (int)$_GET['page'] : 1;
$limit     = isset($_GET['limit'])     ? (int)$_GET['limit'] : 10;
$search    = isset($_GET['search'])    ? trim($_GET['search']) : "";
$brand     = isset($_GET['brand'])     ? trim($_GET['brand']) : "";
$category  = isset($_GET['category'])  ? trim($_GET['category']) : "";
$status    = isset($_GET['status'])    ? trim($_GET['status']) : "";
$sortPrice = isset($_GET['sortPrice']) ? trim($_GET['sortPrice']) : "";

// Gọi hàm lấy sản phẩm có phân trang với nhiều filter và sắp xếp theo giá
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

<main>
    <div class="container mx-auto p-6">
        <!-- Header: Tiêu đề và nút thêm sản phẩm -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">Danh Sách Sản Phẩm</h1>
            <a href="add.php"
                class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow-md transition flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="white"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span class="hidden sm:inline">Thêm sản phẩm</span>
            </a>
        </div>

        <!-- Form tìm kiếm và lọc sản phẩm -->
        <form method="GET" class="flex flex-wrap gap-4 mb-4">
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
            <button type="submit"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow transition duration-150">
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
            <table class="w-full table-fixed border-collapse text-sm sm:text-base">
                <thead class="sticky top-0 z-10 bg-indigo-500 text-white">
                    <tr>
                        <th class="p-2 sm:p-3 text-left" style="width: 50px;">STT</th>
                        <th class="p-2 sm:p-3 text-left">Tên sản phẩm</th>
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Thương hiệu</th>
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Danh mục</th>
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Giá</th>
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Giá khuyến mãi</th>
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Trạng thái</th>
                        <th class="p-2 sm:p-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if ($products->num_rows > 0) : ?>
                    <?php 
                            $stt = ($currentPage - 1) * $limit + 1;
                            while ($product = $products->fetch_assoc()) : 
                                $rowClass = ($stt % 2 === 0) ? 'bg-gray-100' : 'bg-white';
                        ?>
                    <tr class="border-b <?= $rowClass ?> hover:bg-gray-200 transition">
                        <td class="p-2 sm:p-3 align-middle"><?= $stt++ ?></td>
                        <td class="p-2 sm:p-3 align-middle"><?= htmlspecialchars($product['product_name']) ?></td>
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell">
                            <?= !empty($product['brand_name']) ? htmlspecialchars($product['brand_name']) : 'N/A' ?>
                        </td>
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell">
                            <?= !empty($product['category_name']) ? htmlspecialchars($product['category_name']) : 'N/A' ?>
                        </td>
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell">
                            <span class="font-bold text-lg text-blue-600">
                                <?= number_format($product['original_price'], 0, ',', '.') ?> VND
                            </span>
                        </td>
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell">
                            <?php 
                                    if (!empty($product['discount_price']) && (float)$product['discount_price'] > 0) {
                                        echo '<span class="font-bold text-lg text-green-600">' . number_format($product['discount_price'], 0, ',', '.') . ' VND</span>';
                                    } else {
                                        echo "-";
                                    }
                                ?>
                        </td>
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell">
                            <?php 
                                    if ($product['status'] == 1) {
                                        echo '<span class="px-2 py-1 bg-green-200 text-green-800 rounded">Hiển thị</span>';
                                    } elseif ($product['status'] == 2) {
                                        echo '<span class="px-2 py-1 bg-red-200 text-red-800 rounded">Ẩn</span>';
                                    } else {
                                        echo '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded">Unknown</span>';
                                    }
                                ?>
                        </td>
                        <td class="p-2 sm:p-3 text-center align-middle">
                            <div class="flex flex-col sm:flex-row gap-1 justify-center items-center">
                                <!-- Nút xem chi tiết -->
                                <a href="detail.php?id=<?= urlencode($product['product_id']) ?>"
                                    class="bg-blue-200 hover:bg-blue-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                    title="Xem chi tiết">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" y1="16" x2="12" y2="12" />
                                        <line x1="12" y1="8" x2="12" y2="8" />
                                    </svg>
                                </a>
                                <!-- Nút chỉnh sửa -->
                                <a href="edit.php?id=<?= urlencode($product['product_id']) ?>"
                                    class="bg-yellow-200 hover:bg-yellow-300 w-10 h-10 flex items-center justify-center rounded-lg shadow transition"
                                    title="Chỉnh sửa">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </a>
                                <!-- Nút xóa -->
                                <a href="delete.php?id=<?= urlencode($product['product_id']) ?>"
                                    class="bg-red-200 hover:bg-red-300 w-10 h-10 flex items-center justify-center rounded-lg shadow transition"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" title="Xóa">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path>
                                        <path d="M10 11v6"></path>
                                        <path d="M14 11v6"></path>
                                    </svg>
                                </a>
                                <!-- Nút thêm số lượng -->
                                <a href="update_quantity.php?id=<?= urlencode($product['product_id']) ?>"
                                    class="bg-purple-200 hover:bg-purple-300 w-10 h-10 flex items-center justify-center rounded-lg shadow transition"
                                    title="Thêm số lượng">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="p-3 text-center text-gray-500">Không tìm thấy sản phẩm nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang và dropdown chọn số dòng hiển thị -->
        <div class="grid grid-cols-1 sm:grid-cols-3 items-center mt-4 gap-4 mb-6">
            <div class="flex justify-start items-center">
                <form method="GET" id="limitForm" class="flex items-center">
                    <input type="hidden" name="page" value="<?= $currentPage ?>">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="brand" value="<?= htmlspecialchars($brand) ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                    <input type="hidden" name="sortPrice" value="<?= htmlspecialchars($sortPrice) ?>">
                    <select name="limit" id="limit" class="p-2 border rounded cursor-pointer"
                        onchange="this.form.submit()">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
            <div class="flex justify-center">
                <?php
                require_once('../../includes/pagination.php');
                renderPagination($currentPage, $totalPages, $limit, $search, $brand, $category, $status, $sortPrice);
                ?>
            </div>
        </div>
    </div>
</main>

<?php include('../../includes/footer.php'); ?>