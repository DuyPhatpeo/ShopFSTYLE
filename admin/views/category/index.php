<?php 
include("../../includes/header.php");
require_once('../../../includes/db.php'); // Kết nối CSDL

// Kiểm tra kết nối CSDL
if (!$conn instanceof mysqli) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Lấy dữ liệu từ URL
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Đếm tổng số danh mục có từ khóa tìm kiếm
$sqlCount = "SELECT COUNT(*) as total FROM category WHERE category_name LIKE ?";
$stmtCount = $conn->prepare($sqlCount);
$searchParam = "%" . $search . "%";
$stmtCount->bind_param("s", $searchParam);
$stmtCount->execute();
$result = $stmtCount->get_result();
$row = $result->fetch_assoc();
$totalCategories = (int)($row['total'] ?? 0);
$totalPages = max(1, ceil($totalCategories / $limit));

// Đảm bảo currentPage không vượt quá tổng số trang
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $limit;

// Lấy danh sách danh mục có từ khóa tìm kiếm và lấy tên danh mục cha thông qua LEFT JOIN
$sql = "SELECT c.*, p.category_name AS parent_name 
        FROM category c 
        LEFT JOIN category p ON c.parent_id = p.category_id 
        WHERE c.category_name LIKE ? 
        ORDER BY c.category_name ASC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $searchParam, $limit, $offset);
$stmt->execute();
$categories = $stmt->get_result();
?>

<main>
    <div class="container mx-auto p-6">
        <!-- Header: Tiêu đề và nút thêm danh mục -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">Danh sách danh mục</h1>
            <a href="add-category.php"
                class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow-md transition flex items-center space-x-2">
                <!-- SVG của nút thêm (add) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="white"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <!-- Chữ "Thêm danh mục" ẩn trên màn hình nhỏ -->
                <span class="hidden sm:inline">Thêm danh mục</span>
            </a>
        </div>

        <!-- Thanh tìm kiếm (căn lề phải) -->
        <div class="flex justify-end mb-4">
            <form method="GET" class="flex">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    class="p-2 border border-gray-300 rounded-l-lg focus:outline-none"
                    placeholder="Tìm kiếm thương hiệu...">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white p-2 border border-gray-300 rounded-r-lg">
                    <!-- SVG của nút tìm kiếm (search icon) -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="white" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </button>
            </form>
        </div>

        <!-- Bảng hiển thị danh mục -->
        <div class="rounded-lg overflow-hidden max-h-[600px] overflow-y-auto transition-all duration-300">
            <table class="w-full border-collapse text-sm sm:text-base">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-indigo-500 text-white">
                        <th class="p-2 sm:p-3 text-left">STT</th>
                        <th class="p-2 sm:p-3 text-left">Mã danh mục</th>
                        <th class="p-2 sm:p-3 text-left">Tên danh mục</th>
                        <th class="p-2 sm:p-3 text-left">Danh mục cha</th>
                        <th class="p-2 sm:p-3 text-left">Trạng thái</th>
                        <th class="p-2 sm:p-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if ($categories->num_rows > 0) : ?>
                    <?php 
                        $stt = $offset + 1;
                        while ($category = $categories->fetch_assoc()) : 
                            $rowClass = ($stt % 2 === 0) ? 'bg-gray-100' : 'bg-white';
                        ?>
                    <tr class="border-b <?= $rowClass ?> hover:bg-gray-200 transition">
                        <td class="p-2 sm:p-3"><?= $stt++ ?></td>
                        <td class="p-2 sm:p-3">#<?= htmlspecialchars($category['category_id']) ?></td>
                        <td class="p-2 sm:p-3 font-medium"><?= htmlspecialchars($category['category_name']) ?></td>
                        <td class="p-2 sm:p-3">
                            <?= htmlspecialchars($category['parent_name'] ?? 'Không có') ?>
                        </td>
                        <td class="p-2 sm:p-3">
                            <?php 
                                if ($category['status'] == 1) {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-green-200 text-green-800 font-semibold rounded-lg shadow-md">On</span>';
                                } elseif ($category['status'] == 2) {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-red-200 text-red-800 font-semibold rounded-lg shadow-md">Off</span>';
                                } else {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow-md">Unknown</span>';
                                }
                            ?>
                        </td>
                        <td class="p-2 sm:p-3 text-center flex justify-center gap-1">
                            <a href="edit-category.php?id=<?= urlencode($category['category_id']) ?>"
                                class="bg-blue-200 hover:bg-blue-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition">
                                <!-- SVG của nút edit -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                </svg>
                            </a>
                            <a href="delete-category.php?id=<?= urlencode($category['category_id']) ?>"
                                class="bg-red-200 hover:bg-red-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                onclick="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                <!-- SVG của nút delete -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M3 6h18"></path>
                                    <path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                                    <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path>
                                    <path d="M10 11v6"></path>
                                    <path d="M14 11v6"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="6" class="p-3 text-center text-gray-500">Không tìm thấy danh mục nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang và dropdown chọn số dòng -->
        <div class="grid grid-cols-1 sm:grid-cols-3 items-center mt-4 gap-4 mb-6">
            <!-- Dropdown chọn số dòng -->
            <div class="flex justify-start items-center">
                <form method="GET" id="limitForm" class="flex items-center">
                    <input type="hidden" name="page" value="<?= $currentPage ?>">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <select name="limit" id="limit" class="p-2 border rounded cursor-pointer"
                        onchange="this.form.submit()">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
            <!-- Phân trang -->
            <div class="flex justify-center">
                <?php
                require_once('../../includes/pagination.php');
                renderPagination($currentPage, $totalPages, $limit, $search);
                ?>
            </div>
        </div>
    </div>
</main>

<?php include('../../includes/footer.php'); ?>