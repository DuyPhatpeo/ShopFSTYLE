<?php 
require_once('includes/header.php'); 

// Lấy trang hiện tại và số dòng mỗi trang từ URL (mặc định là 1 và 10)
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;

// Giả sử tổng số sản phẩm được lấy từ cơ sở dữ liệu
$totalProducts = 100; // Ví dụ có 100 sản phẩm
$totalPages = ceil($totalProducts / $limit);

// Tính chỉ số bắt đầu và kết thúc của sản phẩm hiển thị
$start = ($currentPage - 1) * $limit + 1;
$end = min($currentPage * $limit, $totalProducts);
?>
<main class="p-6 mb-6">
    <div class="w-full">
        <!-- Tiêu đề + Nút thêm sản phẩm (responsive) -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2 sm:mb-0">Danh sách sản phẩm</h1>
            <a href="add.php"
                class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow-md transition flex items-center space-x-2">
                <img src="assets/icons/add.svg" class="w-6 h-6" alt="Add">
                <span class="hidden sm:inline">Thêm sản phẩm</span>
            </a>
        </div>

        <!-- Bảng hiển thị sản phẩm (responsive) -->
        <div class="w-full overflow-x-auto">
            <!-- Container bọc bảng với bo góc -->
            <div class="rounded-lg overflow-hidden max-h-[600px] overflow-y-auto transition-all duration-300">
                <table class="w-full border-collapse text-sm sm:text-base">
                    <!-- Fix header luôn hiển thị -->
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
                            <th class="p-2 sm:p-3 text-left">Mã SP</th>
                            <th class="p-2 sm:p-3 text-left">Tên sản phẩm</th>
                            <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Mô tả</th>
                            <th class="p-2 sm:p-3 text-left">Giá gốc</th>
                            <th class="p-2 sm:p-3 text-left">Giá giảm</th>
                            <th class="p-2 sm:p-3 text-left hidden md:table-cell">Thương hiệu</th>
                            <th class="p-2 sm:p-3 text-left">Trạng thái</th>
                            <th class="p-2 sm:p-3 text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php 
                        // Vòng lặp giả sử dữ liệu sản phẩm từ $start đến $end
                        for ($i = $start; $i <= $end; $i++) : 
                            // Gán trạng thái theo chỉ số (modulo 3)
                            if($i % 3 == 1) {
                                $status = "Đang bán";
                                $statusClasses = "bg-green-200 text-green-800";
                            } elseif($i % 3 == 2) {
                                $status = "Sắp bán";
                                $statusClasses = "bg-yellow-200 text-yellow-800";
                            } else {
                                $status = "Hết hàng";
                                $statusClasses = "bg-red-200 text-red-800";
                            }
                        ?>
                        <tr
                            class="border-b <?= $i % 2 === 0 ? 'bg-gray-100' : 'bg-white' ?> hover:bg-gray-200 transition">
                            <td class="p-2 sm:p-3">#<?= $i ?></td>
                            <td class="p-2 sm:p-3 font-medium">Sản phẩm <?= $i ?></td>
                            <td class="p-2 sm:p-3 hidden sm:table-cell">Mô tả sản phẩm <?= $i ?></td>
                            <td class="p-2 sm:p-3 text-red-500 font-semibold">
                                <?= number_format(200000 * $i, 0, ',', '.') ?>đ</td>
                            <td class="p-2 sm:p-3 text-green-500 font-semibold">
                                <?= number_format(150000 * $i, 0, ',', '.') ?>đ</td>
                            <td class="p-2 sm:p-3 hidden md:table-cell">Thương hiệu <?= $i ?></td>
                            <td class="p-2 sm:p-3">
                                <span
                                    class="px-1 sm:px-2 py-1 <?= $statusClasses ?> font-semibold rounded-lg shadow-md"><?= $status ?></span>
                            </td>
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
                                    onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                    <img src="assets/icons/delete.svg" class="w-4 sm:w-6 h-4 sm:h-6" alt="Delete">
                                </a>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Phân trang và dropdown chọn số dòng -->
        <div class="grid grid-cols-1 sm:grid-cols-3 items-center mt-4 gap-4 mb-6">
            <!-- Cột trái: Dropdown chọn số dòng -->
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
            <!-- Cột giữa: Phân trang -->
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