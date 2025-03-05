<?php 
require_once('includes/header.php'); 

// Lấy trang hiện tại và số dòng mỗi trang từ URL (mặc định là 1 và 10)
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

// Giả sử tổng số danh mục được lấy từ cơ sở dữ liệu
$totalCategories = 100; // Ví dụ có 100 danh mục
$totalPages = ceil($totalCategories / $limit);

// Tính chỉ số bắt đầu và kết thúc của danh mục hiển thị
$start = ($currentPage - 1) * $limit + 1;
$end = min($currentPage * $limit, $totalCategories);
?>
<main class="p-6 mb-6">
    <div class="w-full">
        <!-- Tiêu đề + Nút thêm danh mục (responsive) -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2 sm:mb-0">Danh mục sản phẩm</h1>
            <a href="add.php"
                class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow-md transition flex items-center space-x-2">
                <img src="assets/icons/add.svg" class="w-6 h-6" alt="Add">
                <span class="hidden sm:inline">Thêm danh mục</span>
            </a>
        </div>

        <!-- Bảng hiển thị danh mục (responsive) -->
        <div class="w-full overflow-x-auto">
            <div class="rounded-lg overflow-hidden max-h-[600px] overflow-y-auto transition-all duration-300">
                <table class="w-full border-collapse text-sm sm:text-base">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                            <th class="p-2 sm:p-3 text-left">Mã DM</th>
                            <th class="p-2 sm:p-3 text-left">Tên danh mục</th>
                            <th class="p-2 sm:p-3 text-left">Parent ID</th>
                            <th class="p-2 sm:p-3 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php 
                        for ($i = $start; $i <= $end; $i++) : 
                        ?>
                        <tr
                            class="border-b <?= $i % 2 === 0 ? 'bg-gray-100' : 'bg-white' ?> hover:bg-gray-200 transition">
                            <td class="p-2 sm:p-3">#<?= $i ?></td>
                            <td class="p-2 sm:p-3 font-medium">Danh mục <?= $i ?></td>
                            <td class="p-2 sm:p-3">Parent <?= ($i % 5) + 1 ?></td>
                            <td class="p-2 sm:p-3 text-center flex justify-center items-center gap-1">
                                <a href="details.php?id=<?= $i ?>"
                                    class="bg-green-200 hover:bg-green-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition">
                                    <img src="assets/icons/details.svg" class="w-4 sm:w-6 h-4 sm:h-6" alt="Details">
                                </a>
                                <a href="edit.php?id=<?= $i ?>"
                                    class="bg-blue-200 hover:bg-blue-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition">
                                    <img src="assets/icons/edit.svg" class="w-4 sm:w-6 h-4 sm:h-6" alt="Edit">
                                </a>
                                <a href="delete.php?id=<?= $i ?>"
                                    class="bg-red-200 hover:bg-red-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                    onclick="return confirm('Bạn có chắc muốn xóa danh mục này?');">
                                    <img src="assets/icons/delete.svg" class="w-4 sm:w-6 h-4 sm:h-6" alt="Delete">
                                </a>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 items-center mt-4 gap-4 mb-6">
            <div class="flex justify-start items-center">
                <form method="GET" id="limitForm" class="flex items-center">
                    <input type="hidden" name="page" value="<?= $currentPage ?>">
                    <select name="limit" id="limit" class="p-2 border rounded" onchange="this.form.submit()">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
            <div class="flex justify-center">
                <?php
                require_once('includes/pagination.php');
                renderPagination($currentPage, $totalPages, $limit);
                ?>
            </div>
        </div>
    </div>
</main>
<?php require_once('includes/footer.php'); ?>