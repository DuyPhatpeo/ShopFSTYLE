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

<div class="flex flex-col min-h-screen bg-gray-50">
    <main class="flex-grow container mx-auto px-6 py-16">
        <!-- Title -->
        <h1 class="text-5xl font-extrabold text-indigo-600 mb-12 text-center">
            📜 Lịch sử đơn hàng
        </h1>

        <?php if (empty($orders)): ?>
        <div class="max-w-md mx-auto bg-yellow-100 border-l-4 border-yellow-500 text-yellow-800 p-6 rounded-lg">
            Bạn chưa có đơn hàng nào.
        </div>
        <?php else: ?>
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($orders as $order): 
          $map = [
            'pending'    => ['Chờ xử lý',    'bg-yellow-100 text-yellow-800'],
            'processing' => ['Đang xử lý',   'bg-blue-100 text-blue-800'],
            'shipping'   => ['Đang giao',    'bg-indigo-100 text-indigo-800'],
            'completed'  => ['Hoàn thành',   'bg-green-100 text-green-800'],
            'cancelled'  => ['Đã hủy',       'bg-red-100 text-red-800'],
          ];
          [$label, $badgeClass] = $map[$order['status']] ?? [$order['status'], 'bg-gray-100 text-gray-800'];
        ?>
            <div
                class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 transition p-6 flex flex-col justify-between">
                <!-- Order Info -->
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Mã đơn</span>
                        <span class="text-indigo-600 font-semibold">#<?= htmlspecialchars($order['order_id']) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Ngày tạo</span>
                        <span class="text-gray-700"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Số mặt hàng</span>
                        <span class="text-gray-700"><?= $order['item_count'] ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Tổng cộng</span>
                        <span class="text-indigo-600 font-bold"><?= number_format($order['total_amount']) ?>₫</span>
                    </div>
                </div>

                <!-- Status & Action -->
                <div class="mt-6 flex items-center justify-between">
                    <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full <?= $badgeClass ?>">
                        <?= $label ?>
                    </span>
                    <a href="<?= USER_URL ?>/views/order_detail.php?id=<?= urlencode($order['order_id']) ?>"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white px-4 py-2 rounded-full text-sm font-medium opacity-90 group-hover:opacity-100 transition">
                        🔍 Xem chi tiết
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>