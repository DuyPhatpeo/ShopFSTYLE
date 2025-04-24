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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-7xl mx-auto p-6 space-y-8 text-[17px] md:text-[18px]">
    <h1 class="text-3xl font-bold text-gray-800">
        Chi tiết đơn hàng <span class="text-indigo-600">#<?= htmlspecialchars($order_id) ?></span>
    </h1>

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
                        <p class="text-sm text-gray-500 mt-1">
                            Màu: <?= htmlspecialchars($item['color_name']) ?>
                            <?= $item['size_name'] ? ' | Size: ' . htmlspecialchars($item['size_name']) : '' ?>
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
                <div class="flex justify-between items-center">
                    <dt class="text-gray-600">Trạng thái:</dt>
                    <dd id="order-status" class="font-medium">
                        <?php
                        $statusMap = [
                            'pending'    => ['🕒 Chờ xử lý', 'bg-yellow-100 text-yellow-800'],
                            'processing' => ['🔄 Đang xử lý', 'bg-blue-100 text-blue-800'],
                            'shipping'   => ['🚚 Đang giao', 'bg-indigo-100 text-indigo-800'],
                            'completed'  => ['✅ Hoàn thành', 'bg-green-100 text-green-800'],
                            'cancelled'  => ['❌ Đã hủy', 'bg-red-100 text-red-800'],
                        ];
                        $statusKey   = $order['order_status'];
                        $statusText  = $statusMap[$statusKey][0] ?? $statusKey;
                        $statusClass = $statusMap[$statusKey][1] ?? 'bg-gray-100 text-gray-800';
                        echo "<span class='text-sm font-medium px-3 py-1 rounded-full $statusClass'>$statusText</span>";
                        ?>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Ngày đặt:</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Thanh toán:</dt>
                    <dd><?= $order['payment_method'] === 'cod' ? 'COD' : 'Chuyển khoản' ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Địa chỉ:</dt>
                    <dd class="text-right"><?= nl2br(htmlspecialchars($order['shipping_address'])) ?></dd>
                </div>
            </dl>
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Tạm tính:</span>
                    <span><?= number_format($order['total_amount']) ?>đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phí vận chuyển:</span>
                    <span>0đ</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span>Tổng cộng:</span>
                    <span><?= number_format($order['total_amount']) ?>đ</span>
                </div>
            </div>

            <?php if ($order['order_status'] === 'pending'): ?>
            <button id="btn-cancel"
                class="w-full py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-lg font-semibold">
                Hủy đơn hàng
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('btn-cancel');
    if (!btn) return;

    btn.addEventListener('click', function() {
        Swal.fire({
            title: 'Xác nhận',
            text: 'Bạn chắc chắn muốn hủy đơn hàng này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Có, hủy!',
            cancelButtonText: 'Không'
        }).then((result) => {
            if (!result.isConfirmed) return;

            btn.disabled = true;
            btn.textContent = 'Đang hủy...';

            fetch('../controller/orderController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'cancel_order',
                        order_id: '<?= htmlspecialchars($order_id) ?>'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            title: 'Đã hủy!',
                            text: 'Đơn hàng đã được hủy thành công.',
                            icon: 'success'
                        });

                        const statusEl = document.getElementById('order-status');
                        statusEl.innerHTML =
                            "<span class='text-sm font-medium px-3 py-1 rounded-full bg-red-100 text-red-800'>❌ Đã hủy</span>";
                        btn.remove();
                    } else {
                        Swal.fire({
                            title: 'Lỗi',
                            text: data.message,
                            icon: 'error'
                        });
                        btn.disabled = false;
                        btn.textContent = 'Hủy đơn hàng';
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        title: 'Lỗi',
                        text: 'Lỗi khi hủy đơn. Vui lòng thử lại.',
                        icon: 'error'
                    });
                    btn.disabled = false;
                    btn.textContent = 'Hủy đơn hàng';
                });
        });
    });
});
</script>

<?php include('../includes/footer.php'); ?>