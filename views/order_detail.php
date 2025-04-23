<?php
session_start();
require_once '../includes/db.php';
require_once '../model/orderModel.php';

if (!isset($_SESSION['customer'])) {
    header('Location: auth/login.php'); exit;
}
if (!isset($_GET['id'])) {
    header('Location: home/index.php'); exit;
}
$order_id = $_GET['id'];
$customer_id = $_SESSION['customer']['customer_id'];
$orderModel = new OrderModel($conn);
$order = $orderModel->getOrder($order_id);
if (!$order || $order['customer_id'] != $customer_id) {
    header('Location: home/index.php'); exit;
}
$orderDetails = $orderModel->getOrderDetails($order_id);
include('../includes/header.php');
include('../includes/search.php');
?>

<div class="max-w-7xl mx-auto p-6 space-y-8">
    <h1 class="text-3xl font-bold text-gray-800">Chi tiết đơn hàng <span
            class="text-indigo-600">#<?php echo $order_id; ?></span></h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Items -->
        <div class="lg:col-span-2 space-y-4">
            <?php foreach ($orderDetails as $item): ?>
            <div class="flex bg-white shadow rounded-lg overflow-hidden">
                <img src="../<?= htmlspecialchars($item['main_image']) ?>" alt="" class="w-32 h-32 object-cover">
                <div class="flex-1 p-4 flex flex-col justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900"><?= htmlspecialchars($item['product_name']) ?>
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Màu:
                            <?= htmlspecialchars($item['color_name']) ?><?= $item['size_name'] ? ' | Size: '.htmlspecialchars($item['size_name']) : '' ?>
                        </p>
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-gray-600">Số lượng: <strong><?= $item['quantity'] ?></strong></span>
                        <span
                            class="text-lg font-bold text-indigo-600"><?= number_format($item['unit_price'] * $item['quantity']) ?>đ</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary -->
        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <h2 class="text-2xl font-semibold text-gray-800">Thông tin đơn hàng</h2>
            <dl class="space-y-4">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Trạng thái:</dt>
                    <dd class="font-medium"><?php
            $map = ['pending'=>'Chờ xử lý','processing'=>'Đang xử lý','shipping'=>'Đang giao','completed'=>'Hoàn thành','cancelled'=>'Đã hủy'];
            echo $map[$order['order_status']] ?? $order['order_status'];
          ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Ngày đặt:</dt>
                    <dd><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Thanh toán:</dt>
                    <dd><?php echo $order['payment_method']=='cod' ? 'COD' : 'Chuyển khoản'; ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Địa chỉ:</dt>
                    <dd class="text-right"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></dd>
                </div>
            </dl>
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tạm tính:</span>
                    <span><?php echo number_format($order['total_amount']); ?>đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phí vận chuyển:</span>
                    <span>0đ</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span>Tổng cộng:</span>
                    <span><?php echo number_format($order['total_amount']); ?>đ</span>
                </div>
            </div>
            <?php if ($order['order_status']=='pending'): ?>
            <button onclick="cancelOrder('<?= $order_id ?>')"
                class="w-full py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">Hủy đơn hàng</button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function cancelOrder(id) {
    if (!confirm('Bạn chắc chắn hủy đơn?')) return;
    fetch('controller/orderController.php', {
        method: 'POST',
        body: new URLSearchParams({
            action: 'cancel',
            order_id: id
        })
    }).then(r => r.json()).then(j => {
        if (j.status === 'success') location.reload();
        else alert(j.message);
    });
}
</script>

<?php include('../includes/footer.php'); ?>