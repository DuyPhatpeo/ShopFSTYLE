<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../model/orderModel.php';

if (!isset($_SESSION['customer'])) {
    header('Location: ' . USER_URL . '/views/auth/login.php');
    exit;
}
$customer_id = $_SESSION['customer']['customer_id'];
$orderModel = new OrderModel($conn);
$orders = $orderModel->getCustomerOrders($customer_id);

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="min-h-screen flex flex-col">
    <main class="container max-w-5xl mx-auto py-10 space-y-6 flex-grow">
        <h1 class="text-4xl font-bold text-gray-800">📜 Lịch sử đơn hàng</h1>

        <?php if (empty($orders)): ?>
        <div class="bg-yellow-100 text-yellow-800 p-6 rounded-lg shadow text-center">
            Bạn chưa có đơn hàng nào.
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($orders as $order): ?>
            <?php
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
                class="bg-white rounded-xl shadow p-6 hover:shadow-lg transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6 text-sm text-gray-700">
                    <span class="font-semibold text-indigo-600">#<?= htmlspecialchars($order['order_id']) ?></span>
                    <span><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                    <span><strong>Số mặt hàng:</strong> <?= $order['item_count'] ?></span>
                    <span><strong>Tổng cộng:</strong> <span
                            class="text-indigo-600 font-bold"><?= number_format($order['total_amount']) ?>đ</span></span>
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full <?= $st[1] ?>">
                        <?= $st[0] ?>
                    </span>
                </div>
                <div class="text-right">
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
</div>