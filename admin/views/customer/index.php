<?php 
$pageTitle = "Trang quản lý khách hàng";

include("../../includes/session_check.php");
include("../../includes/header.php");
require_once('../../../includes/db.php'); // Kết nối CSDL
require_once('../../controller/customerController.php'); // Controller cho Customer

// Lấy giá trị từ URL hoặc gán giá trị mặc định
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Gọi hàm lấy dữ liệu danh sách khách hàng có phân trang
$data = getCustomersWithPagination($conn, $page, $limit, $search);
$customers      = $data['customers'];
$totalPages     = $data['totalPages'];
$currentPage    = $data['currentPage'];
$totalCustomers = $data['totalCustomers'];
?>

<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>
<main>
    <div class="container mx-auto p-6">
        <!-- Header: Tiêu đề và nút thêm khách hàng -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-800">Danh Sách Khách Hàng</h1>
            <!-- Nút thêm khách hàng nếu cần -->
        </div>

        <!-- Thanh tìm kiếm -->
        <div class="flex justify-end mb-4">
            <form method="GET" class="flex">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    class="p-2 border border-gray-300 rounded-l-lg focus:outline-none" placeholder="Tìm khách hàng...">
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

        <!-- Bảng hiển thị danh sách khách hàng với khả năng kéo ngang -->
        <div class="rounded-lg overflow-x-auto max-h-[600px] overflow-y-auto transition-all duration-300">
            <table class="w-full border-collapse text-sm sm:text-base">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-indigo-500 text-white">
                        <!-- Ẩn cột STT, Điện thoại, Địa chỉ, Trạng thái trên mobile -->
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">STT</th>
                        <th class="p-2 sm:p-3 text-left">Email</th>
                        <th class="p-2 sm:p-3 text-left">Họ tên</th>
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Điện thoại</th>
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Địa chỉ</th>
                        <th class="p-2 sm:p-3 text-left hidden sm:table-cell">Trạng thái</th>
                        <th class="p-2 sm:p-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if ($customers->num_rows > 0) : ?>
                    <?php 
                            // Tính số thứ tự bắt đầu dựa vào trang hiện tại
                            $stt = ($currentPage - 1) * $limit + 1;
                            while ($customer = $customers->fetch_assoc()) : 
                                // Màu nền xen kẽ
                                $rowClass = ($stt % 2 === 0) ? 'bg-gray-100' : 'bg-white';
                        ?>
                    <tr class="border-b <?= $rowClass ?> hover:bg-gray-200 transition">
                        <!-- Cột STT chỉ hiển thị trên desktop -->
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell"><?= $stt++ ?></td>
                        <!-- Email và Họ tên hiển thị trên mọi kích thước -->
                        <td class="p-2 sm:p-3 align-middle"><?= htmlspecialchars($customer['email']) ?></td>
                        <td class="p-2 sm:p-3 align-middle"><?= htmlspecialchars($customer['full_name']) ?></td>
                        <!-- Ẩn cột Điện thoại -->
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell">
                            <?= htmlspecialchars($customer['phone']) ?>
                        </td>
                        <!-- Ẩn cột Địa chỉ -->
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell">
                            <?= htmlspecialchars($customer['address']) ?>
                        </td>
                        <!-- Trạng thái ẩn trên mobile -->
                        <td class="p-2 sm:p-3 align-middle hidden sm:table-cell">
                            <?php 
                                if ($customer['status'] == 1) {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-green-200 text-green-800 font-semibold rounded-lg shadow-md">Hoạt động</span>';
                                } elseif ($customer['status'] == 0) {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-red-200 text-red-800 font-semibold rounded-lg shadow-md">Khóa</span>';
                                } else {
                                    echo '<span class="px-1 sm:px-2 py-1 bg-gray-200 text-gray-800 font-semibold rounded-lg shadow-md">Unknown</span>';
                                }
                            ?>
                        </td>
                        <!-- Cột Hành động luôn hiển thị -->
                        <td class="p-2 sm:p-3 text-center align-middle">
                            <div class="flex flex-col sm:flex-row gap-1 justify-center items-center">
                                <!-- Nút xem chi tiết -->
                                <a href="detail.php?id=<?= urlencode($customer['customer_id']) ?>"
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
                                <a href="edit.php?id=<?= urlencode($customer['customer_id']) ?>"
                                    class="bg-yellow-200 hover:bg-yellow-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                    title="Chỉnh sửa">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </a>
                                <!-- Nút xóa -->
                                <a href="delete.php?id=<?= urlencode($customer['customer_id']) ?>"
                                    class="bg-red-200 hover:bg-red-300 w-10 h-10 flex items-center justify-center rounded-lg shadow-md transition"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa khách hàng này?');" title="Xóa">
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
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else : ?>
                    <tr>
                        <td colspan="7" class="p-3 text-center text-gray-500">Không tìm thấy khách hàng nào.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Phân trang và dropdown chọn số dòng -->
        <div class="grid grid-cols-1 sm:grid-cols-3 items-center mt-4 gap-4 mb-6">
            <div class="flex justify-start items-center">
                <form method="GET" id="limitForm" class="flex items-center">
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