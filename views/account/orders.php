<?php
// file: views/account/orders.php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../model/orderModel.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    header('Location: ' . USER_URL . '/views/auth/login.php');
    exit;
}
$customer_id = $_SESSION['customer']['customer_id'];
$orderModel = new OrderModel($conn);
$orders = $orderModel->getCustomerOrders($customer_id);

include_once __DIR__ . '/../../includes/header.php';
?>

<main class="container max-w-5xl mx-auto py-10 space-y-6">
    <h1 class="text-4xl font-bold text-gray-800">📜 Lịch sử đơn hàng</h1>

    <?php if (empty($orders)): ?>
    <div class="bg-yellow-100 text-yellow-800 p-6 rounded-lg shadow">
        Bạn chưa có đơn hàng nào.
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($orders as $order): ?>
        <?php
                    // Xác định badge trạng thái
                    $map = [
                        'pending'    => ['Chờ xử lý', 'bg-yellow-100 text-yellow-800'],
                        'processing' => ['Đang xử lý', 'bg-blue-100 text-blue-800'],
                        'shipping'   => ['Đang giao', 'bg-indigo-100 text-indigo-800'],
                        'completed'  => ['Hoàn thành', 'bg-green-100 text-green-800'],
                        'cancelled'  => ['Đã hủy', 'bg-red-100 text-red-800'],
                    ];
                    $st = $map[$order['status']] ?? [$order['status'], 'bg-gray-100 text-gray-800'];
                ?>
        <div
            class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition flex flex-wrap items-center justify-between">
            <div class="w-full sm:w-auto flex-1 flex flex-col sm:flex-row sm:space-x-6 space-y-2 sm:space-y-0">
                <span class="font-medium text-indigo-600">#<?= htmlspecialchars($order['order_id']) ?></span>
                <span class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                <span class="text-gray-700"><strong>Số mặt hàng:</strong> <?= $order['item_count'] ?></span>
                <span class="text-indigo-600 font-semibold"><strong>Tổng cộng:</strong>
                    <?= number_format($order['total_amount']) ?>đ</span>
                <span
                    class="inline-block px-2 py-1 text-xs font-semibold rounded-full <?= $st[1] ?>"><?= $st[0] ?></span>
            </div>
            <div class="mt-4 sm:mt-0 flex-shrink-0">
                <a href="<?= USER_URL ?>/views/order_detail.php?id=<?= urlencode($order['order_id']) ?>"
                    class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                    Xem chi tiết
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>