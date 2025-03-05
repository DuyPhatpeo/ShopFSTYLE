<?php 
require_once('includes/header.php');
require_once('../includes/db.php'); // Kết nối CSDL

// Kiểm tra kết nối CSDL
if (!$conn instanceof mysqli) {
    die("Kết nối CSDL thất bại: " . mysqli_connect_error());
}

// Lấy trang hiện tại và số dòng mỗi trang từ URL
$currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : 10;

// Đếm tổng số thương hiệu
$result = $conn->query("SELECT COUNT(*) as total FROM brand");
if (!$result) {
    die("Lỗi truy vấn: " . $conn->error);
}
$row = $result->fetch_assoc();
$totalBrands = (int)($row['total'] ?? 0);
$totalPages = max(1, ceil($totalBrands / $limit));

// Đảm bảo currentPage không vượt quá tổng số trang
$currentPage = min($currentPage, $totalPages);
$offset = ($currentPage - 1) * $limit;

// Lấy danh sách thương hiệu, sắp xếp theo tên tự động (tăng dần)
$query = "SELECT * FROM brand ORDER BY brand_name ASC LIMIT $limit OFFSET $offset";
$brands = $conn->query($query);
if (!$brands) {
    die("Lỗi truy vấn: " . $conn->error);
}
?>

<main class="p-6 mb-6">
    <div class="w-full">
        <!-- Tiêu đề + Nút thêm thương hiệu -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-2 sm:mb-0">Danh sách thương hiệu</h1>
            <a href="views/brand/add-brand.php"
                class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow-md transition flex items-center space-x-2">
                <img src="assets/icons/add.svg" class="w-6 h-6" alt="Add">
                <span>Thêm thương hiệu</span>
            </a>
        </div>

        <!-- Bảng hiển thị thương hiệu -->
        <div class="rounded-lg overflow-hidden max-h-[600px] overflow-y-auto transition-all duration-300">
            <table class="w-full border-collapse text-sm sm:text-base">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-indigo-500 text-white">
                        <th class="p-2 sm:p-3 text-left">STT</th>
                        <th class="p-2 sm:p-3 text-left">Mã thương hiệu</th>
                        <th class="p-2 sm:p-3 text-left">Tên thương hiệu</th>
                        <th class="p-2 sm:p-3 text-left">Trạng thái</th>
                        <th class="p-2 sm:p-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if ($brands->num_rows > 0) : ?>
                    <?php 
                        // STT bắt đầu từ số thứ tự dựa trên trang hiện tại
                        $stt = $offset + 1;
                        while ($brand = $brands->fetch_assoc()) : 
                            // Xác định màu nền xen kẽ dựa trên số thứ tự dòng
                            $rowClass = ($stt % 2 === 0) ? 'bg-gray-100' : 'bg-white';
                        ?>
                    <tr class="border-b <?= $rowClass ?> hover:bg-gray-200 transition">
                        <td class="p-2 sm:p-3"><?= $stt++ ?></td>
                        <td class="p-2 sm:p-3">#<?= htmlspecialchars($brand['brand_id']) ?></td>
                        <td class="p-2 sm:p-3 font-medium"><?= htmlspecialchars($brand['brand_name']) ?></td>
                        <td class="p-2 sm:p-3">
                            <?php 
                                        if ($brand['status'] == 1) {
                                            echo '<span class="px-1 sm:px-2 py-1 bg-green-200 text-green-800 font-semibold rounded-lg shadow-md">On</span>';
                                        } elseif ($brand['status'] == 2) {
                                            echo '<span class="px-1 sm:px-2 py-1 bg-red-200 text-red-800 font-semibold rounded-lg shadow-md">Off</span>';
                                        } else {
                                            echo '<span class="px-1 sm:px-2 py-1 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow-md">Unknown</span>';
                                        }
                                    ?>
                        </td>
                        <td class="p-2 sm:p-3 text-center flex justify-center gap-1">
                            <a href="edit.php?id=<?= urlencode($brand['brand_id']) ?>"
                                class="bg-blue-200 hover:bg-blue-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition">
                                <img src="assets/icons/edit.svg" class="w-5 h-5" alt="Edit">
                            </a>
                            <a href="delete.php?id=<?= urlencode($brand['brand_id']) ?>"
                                class="bg-red-200 hover:bg-red-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                onclick="return confirm('Bạn có chắc muốn xóa?');">
                                <img src="assets/icons/delete.svg" class="w-5 h-5" alt="Delete">
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="5" class="p-3 text-center text-gray-500">Không có thương hiệu nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- Phân trang và dropdown chọn số dòng -->
        <div class="grid grid-cols-1 sm:grid-cols-3 items-center mt-4 gap-4 mb-6">
            <!-- Cột trái: Dropdown chọn số dòng -->
            <div class="flex justify-start items-center">
                <form method="GET" id="limitForm" class="flex items-center">
                    <input type="hidden" name="page" value="<?= $currentPage ?>">
                    <select name="limit" id="limit" class="p-2 border rounded cursor-pointer"
                        onchange="this.form.submit()">
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