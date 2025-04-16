<?php
// File: admin/order/index.php

$pageTitle = "Quản lý đơn hàng";

// Kiểm tra session đăng nhập
include("../../includes/session_check.php");

// Include header chung cho trang quản trị
include("../../includes/header.php");

// Kết nối CSDL và import controller đơn hàng
require_once('../../../includes/db.php'); // Khởi tạo biến $conn (mysqli connection)
require_once('../../controller/invoiceController.php');

// Xử lý xoá đơn hàng nếu có yêu cầu (nếu cần)
processDeleteOrder($conn);

// Lấy danh sách đơn hàng dựa trên filter từ URL
$data = getFilteredOrders($conn);
$orders      = $data['orders'];
$totalPages  = $data['totalPages'];
$currentPage = $data['currentPage'];
$totalOrders = $data['totalOrders'];
?>

<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="py-6">
    <div class="container mx-auto p-6">
        <!-- Header: Tiêu đề và nút thêm đơn hàng (nếu cần) -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Danh Sách Đơn Hàng</h1>
            <!-- Ví dụ nút thêm đơn hàng (nếu cần) -->
            <!-- <a href="add.php"
                class="bg-green-700 hover:bg-green-800 text-white p-2 rounded-lg shadow transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Thêm đơn hàng</span>
            </a> -->
        </div>

        <!-- Thông báo số đơn hàng -->
        <p class="text-sm text-gray-600 mb-4">Tổng cộng: <strong><?= $totalOrders ?></strong> đơn hàng</p>

        <!-- Form tìm kiếm và lọc đơn hàng -->
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <div>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? "") ?>"
                    class="p-2 border border-gray-300 rounded focus:outline-none"
                    placeholder="Tìm kiếm (địa chỉ, mã đơn hàng)...">
            </div>
            <div>
                <select name="status" class="p-2 border border-gray-300 rounded focus:outline-none">
                    <option value="">Tất cả trạng thái</option>
                    <option value="pending"
                        <?= (isset($_GET['status']) && $_GET['status'] == "pending") ? "selected" : "" ?>>Chờ xác nhận
                    </option>
                    <option value="processing"
                        <?= (isset($_GET['status']) && $_GET['status'] == "processing") ? "selected" : "" ?>>Đang xử lý
                    </option>
                    <option value="shipping"
                        <?= (isset($_GET['status']) && $_GET['status'] == "shipping") ? "selected" : "" ?>>Đang giao
                        hàng</option>
                    <option value="completed"
                        <?= (isset($_GET['status']) && $_GET['status'] == "completed") ? "selected" : "" ?>>Hoàn thành
                    </option>
                    <option value="cancelled"
                        <?= (isset($_GET['status']) && $_GET['status'] == "cancelled") ? "selected" : "" ?>>Đã huỷ
                    </option>
                </select>
            </div>
            <div>
                <select name="payment_method" class="p-2 border border-gray-300 rounded focus:outline-none">
                    <option value="">Tất cả PTTT</option>
                    <option value="cod"
                        <?= (isset($_GET['payment_method']) && $_GET['payment_method'] == "cod") ? "selected" : "" ?>>
                        Thanh toán khi nhận hàng</option>
                    <option value="credit_card"
                        <?= (isset($_GET['payment_method']) && $_GET['payment_method'] == "credit_card") ? "selected" : "" ?>>
                        Thẻ tín dụng</option>
                    <option value="paypal"
                        <?= (isset($_GET['payment_method']) && $_GET['payment_method'] == "paypal") ? "selected" : "" ?>>
                        Paypal</option>
                </select>
            </div>
            <input type="hidden" name="limit" value="<?= $_GET['limit'] ?? 10 ?>">
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

        <!-- Bảng hiển thị đơn hàng -->
        <div class="rounded-lg overflow-x-auto max-h-[600px] overflow-y-auto transition-all duration-300">
            <table class="w-full table-auto border-collapse text-sm sm:text-base">
                <thead class="bg-indigo-500 text-white">
                    <tr>
                        <th class="p-3 text-left">STT</th>
                        <th class="p-3 text-left">Mã đơn hàng</th>
                        <th class="p-3 text-left">Địa chỉ giao hàng</th>
                        <th class="p-3 text-left hidden sm:table-cell">Địa chỉ thanh toán</th>
                        <th class="p-3 text-right hidden sm:table-cell">Tổng tiền</th>
                        <th class="p-3 text-center hidden sm:table-cell">PTTT</th>
                        <th class="p-3 text-center hidden sm:table-cell">Trạng thái</th>
                        <th class="p-3 text-center hidden sm:table-cell">Ngày tạo</th>
                        <th class="p-3 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders && $orders->num_rows > 0) : ?>
                    <?php $stt = ($currentPage - 1) * ($_GET['limit'] ?? 10) + 1; ?>
                    <?php while ($order = $orders->fetch_assoc()) : ?>
                    <tr class="<?= ($stt % 2 === 0) ? 'bg-gray-100' : 'bg-white' ?> hover:bg-gray-200 transition">
                        <td class="p-3"><?= $stt++ ?></td>
                        <td class="p-3"><?= htmlspecialchars($order['order_id']) ?></td>
                        <td class="p-3"><?= htmlspecialchars($order['shipping_address']) ?></td>
                        <td class="p-3 hidden sm:table-cell"><?= htmlspecialchars($order['billing_address']) ?></td>
                        <td class="p-3 text-right hidden sm:table-cell">
                            <span class="font-bold text-blue-600">
                                <?= number_format($order['total_amount'], 0, ',', '.') ?> VND
                            </span>
                        </td>
                        <td class="p-3 text-center hidden sm:table-cell">
                            <?= htmlspecialchars($order['payment_method']) ?></td>
                        <td class="p-3 text-center hidden sm:table-cell">
                            <?php
                                    switch ($order['status']) {
                                        case 'pending':
                                            echo '<span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded">Chờ xác nhận</span>';
                                            break;
                                        case 'processing':
                                            echo '<span class="px-2 py-1 bg-blue-200 text-blue-800 rounded">Đang xử lý</span>';
                                            break;
                                        case 'shipping':
                                            echo '<span class="px-2 py-1 bg-indigo-200 text-indigo-800 rounded">Đang giao</span>';
                                            break;
                                        case 'completed':
                                            echo '<span class="px-2 py-1 bg-green-200 text-green-800 rounded">Hoàn thành</span>';
                                            break;
                                        case 'cancelled':
                                            echo '<span class="px-2 py-1 bg-red-200 text-red-800 rounded">Đã huỷ</span>';
                                            break;
                                        default:
                                            echo '<span class="px-2 py-1 bg-gray-200 text-gray-800 rounded">Unknown</span>';
                                            break;
                                    }
                                    ?>
                        </td>
                        <td class="p-3 text-center hidden sm:table-cell">
                            <?= htmlspecialchars(date("d/m/Y H:i", strtotime($order['created_at']))) ?>
                        </td>
                        <td class="p-3 text-center">
                            <div class="flex flex-col sm:flex-row items-center gap-1">
                                <a href="detail.php?id=<?= urlencode($order['order_id']) ?>"
                                    class="bg-blue-200 hover:bg-blue-300 p-2 rounded-lg shadow" title="Xem chi tiết">
                                    <!-- Icon chi tiết -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="16" x2="12" y2="12"></line>
                                        <line x1="12" y1="8" x2="12" y2="8"></line>
                                    </svg>
                                </a>
                                <a href="edit.php?id=<?= urlencode($order['order_id']) ?>"
                                    class="bg-yellow-200 hover:bg-yellow-300 p-2 rounded-lg shadow"
                                    title="Chỉnh sửa trạng thái">
                                    <!-- Icon chỉnh sửa -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" viewBox="0 0 24 24">
                                        <path d="M12 20h9"></path>
                                        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                    </svg>
                                </a>
                                <a href="delete.php?delete_order=<?= urlencode($order['order_id']) ?>"
                                    class="bg-red-200 hover:bg-red-300 p-2 rounded-lg shadow"
                                    onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');" title="Xóa">
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
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="p-3 text-center text-gray-500">Không tìm thấy đơn hàng nào.</td>
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
                    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search'] ?? "") ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status'] ?? "") ?>">
                    <input type="hidden" name="payment_method"
                        value="<?= htmlspecialchars($_GET['payment_method'] ?? "") ?>">
                    <select name="limit" class="p-2 border rounded cursor-pointer" onchange="this.form.submit()">
                        <option value="10" <?= ((int)($_GET['limit'] ?? 10)) === 10 ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= ((int)($_GET['limit'] ?? 10)) === 20 ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= ((int)($_GET['limit'] ?? 10)) === 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ((int)($_GET['limit'] ?? 10)) === 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </form>
            </div>
            <div class="mt-4 sm:mt-0">
                <?php 
                    // Giả sử bạn đã có hàm renderPagination() trong file includes/pagination.php
                    require_once('../../includes/pagination.php');
                    renderPagination($currentPage, $totalPages, $_GET['limit'] ?? 10, $_GET['search'] ?? "", $_GET['status'] ?? "", $_GET['payment_method'] ?? "");
                ?>
            </div>
            <div class="text-sm text-gray-600">
                Trang <strong><?= $currentPage ?></strong> trên <strong><?= $totalPages ?></strong>
            </div>
        </div>
    </div>
</main>

<?php include("../../includes/footer.php"); ?>