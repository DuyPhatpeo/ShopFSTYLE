<?php
$pageTitle = "Chi tiết đơn hàng";

include("../../includes/session_check.php");

ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../../model/orderModel.php');
function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die();
}  
// Lấy order_id từ URL
$order_id = $_GET['id'] ?? '';
if (empty($order_id)) {
    die("ID đơn hàng không hợp lệ.");
}

// Lấy thông tin đơn hàng
$orderModel = new OrderModel($conn);
$order = $orderModel->getOrder($order_id);
$orderDetails = $orderModel->getOrderDetails($order_id);
// dd($order);
if (!$order) {
    die("Đơn hàng không tồn tại.");
}

// Xử lý cập nhật trạng thái nếu có
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    if (!empty($status)) {
        try {
            $orderModel->updateOrderStatus($order_id, $status);
            header("Location: detail.php?id=" . $order_id . "&success=1");
            exit;
        } catch (Exception $e) {
            $error = "Có lỗi xảy ra: " . $e->getMessage();
        }
    }
}
?>

<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <!-- Header với tiêu đề và các nút hành động -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Chi Tiết Đơn Hàng #<?= $order_id ?></h1>
        <div class="inline-flex gap-2">
            <!-- Nút Quay lại -->
            <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center"
                title="Quay lại">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                    <path
                        d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 224 214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
                </svg>
                <span class="hidden md:inline-block">Quay lại</span>
            </a>
        </div>
    </div>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">Cập nhật trạng thái đơn hàng thành công!</span>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $error ?></span>
        </div>
    <?php endif; ?>

    <!-- Thông tin đơn hàng -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white shadow-md rounded p-6">
            <h2 class="text-xl font-semibold mb-4">Thông tin đơn hàng</h2>
            <div class="space-y-2">
                <p><strong>Mã đơn hàng:</strong> <?= $order['order_id'] ?></p>
                <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                <p><strong>Trạng thái:</strong> 
                    <span class="px-2 py-1 rounded <?= getStatusBadgeClass($order['order_status']) ?>">
                        <?= getStatusText($order['order_status']) ?>
                    </span>
                </p>
                <p><strong>Phương thức thanh toán:</strong> <?= $order['payment_method'] ?></p>
                <p><strong>Tổng tiền:</strong> <?= number_format($order['total_amount'], 0, ',', '.') ?>đ</p>
            </div>
        </div>

        <div class="bg-white shadow-md rounded p-6">
            <h2 class="text-xl font-semibold mb-4">Thông tin giao hàng</h2>
            <div class="space-y-2">
                <p><strong>Họ tên:</strong> <?= $order['full_name'] ?></p>
                <p><strong>Điện thoại:</strong> <?= $order['phone'] ?></p>
                <p><strong>Địa chỉ:</strong> <?= $order['address'] ?></p>
                <p><strong>Ghi chú:</strong> <?= $order['note'] ?: 'Không có' ?></p>
            </div>
        </div>
    </div>

    <!-- Danh sách sản phẩm -->
    <div class="bg-white shadow-md rounded p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Sản phẩm đã đặt</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Màu sắc</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kích thước</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($orderDetails as $index => $detail): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $index + 1 ?></td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="<?= BASE_URL .'/' . $detail['main_image'] ?>" alt="<?= $detail['product_name'] ?>" 
                                         class="h-10 w-10 rounded-full object-cover mr-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $detail['product_name'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $detail['color_name'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $detail['size_name'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= number_format($detail['unit_price'], 0, ',', '.') ?>đ</td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= $detail['quantity'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?= number_format($detail['unit_price'] * $detail['quantity'], 0, ',', '.') ?>đ</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-right font-semibold">Tổng tiền:</td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Form cập nhật trạng thái -->
    <div class="bg-white shadow-md rounded p-6">
        <h2 class="text-xl font-semibold mb-4">Cập nhật trạng thái</h2>
        <form method="POST" class="space-y-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái mới</label>
                <select id="status" name="status" required
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="pending" <?= $order['order_status'] == 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                    <option value="processing" <?= $order['order_status'] == 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                    <option value="shipping" <?= $order['order_status'] == 'shipping' ? 'selected' : '' ?>>Đã giao hàng</option>
                    <option value="completed" <?= $order['order_status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="cancelled" <?= $order['order_status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            <button type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Cập nhật
            </button>
        </form>
    </div>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();

// Hàm lấy class cho badge trạng thái
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'processing':
            return 'bg-blue-100 text-blue-800';
        case 'shipping':
            return 'bg-purple-100 text-purple-800';
        case 'completed':
            return 'bg-green-100 text-green-800';
        case 'cancelled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

// Hàm lấy text hiển thị trạng thái
function getStatusText($status) {
    switch ($status) {
        case 'pending':
            return 'Chờ xử lý';
        case 'processing':
            return 'Đang xử lý';
        case 'shipping':
            return 'Đang giao hàng';
        case 'completed':
            return 'Hoàn thành';
        case 'cancelled':
            return 'Đã hủy';
        default:
            return 'Không xác định';
    }
}
?> 