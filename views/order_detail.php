<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    header('Location: auth/login.php');
    exit;
}

require_once '../includes/db.php';
require_once '../model/orderModel.php';

if (!isset($_GET['id'])) {
    header('Location: home/index.php');
    exit;
}

$order_id    = $_GET['id'];
$customer_id = $_SESSION['customer']['customer_id'];
$orderModel  = new OrderModel($conn);
$order       = $orderModel->getOrder($order_id);

// Nếu đơn không tồn tại hoặc không phải của khách hiện tại
if (!$order || $order['customer_id'] !== $customer_id) {
    header('Location: home/index.php');
    exit;
}

// Lấy chi tiết đơn (có main_image)
$orderDetails = $orderModel->getOrderDetails($order_id);

include('../includes/header.php');
include('../includes/search.php');
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="max-w-7xl mx-auto px-4 md:px-6 py-8 space-y-10 text-[17px] md:text-[18px]">
    <h1 class="text-3xl font-bold text-gray-800">
        Chi tiết đơn hàng <span class="text-indigo-600">#<?= htmlspecialchars($order_id) ?></span>
    </h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Danh sách sản phẩm -->
        <div class="lg:col-span-2 space-y-6">
            <?php foreach ($orderDetails as $item): ?>
            <div class="flex items-center bg-white rounded-xl shadow-md overflow-hidden">
                <div class="w-40 h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
                    <img src="<?= USER_URL ?>/admin/uploads/products/<?= htmlspecialchars($item['image_url']) ?>"
                        alt="<?= htmlspecialchars($item['product_name']) ?>"
                        class="object-contain w-full h-full transition-transform duration-300 hover:scale-105">
                </div>
                <div class="flex-1 px-5 py-3 flex flex-col justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($item['product_name']) ?>
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">
                            Màu: <?= htmlspecialchars($item['color_name']) ?>
                            <?= $item['size_name'] ? ' | Size: ' . htmlspecialchars($item['size_name']) : '' ?>
                        </p>
                    </div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-gray-600">Số lượng: <strong><?= $item['quantity'] ?></strong></span>
                        <span class="text-lg font-semibold text-indigo-600">
                            <?= number_format($item['unit_price'] * $item['quantity']) ?>đ
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Thông tin đơn hàng -->
        <div class="bg-white rounded-xl shadow-md p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">Thông tin đơn hàng</h2>

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
            ?>

            <dl class="space-y-4 text-[16px]">
                <div class="flex justify-between items-center">
                    <dt class="text-gray-600">Trạng thái:</dt>
                    <dd id="order-status">
                        <span class="text-sm font-medium px-3 py-1 rounded-full <?= $statusClass ?>">
                            <?= $statusText ?>
                        </span>
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Ngày đặt:</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Thanh toán:</dt>
                    <dd><?= $order['payment_method'] === 'cod' ? 'COD (Khi nhận hàng)' : 'Chuyển khoản' ?></dd>
                </div>
                <div>
                    <dt class="text-gray-600 mb-1">Địa chỉ:</dt>
                    <dd class="text-right text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                        <?= nl2br(htmlspecialchars($order['shipping_address'])) ?>
                    </dd>
                </div>
            </dl>

            <div class="border-t pt-4 space-y-2 text-[16px]">
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
                class="w-full py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-lg font-semibold shadow">
                ❌ Hủy đơn hàng
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// SweetAlert2 xử lý hủy đơn
document.getElementById('btn-cancel')?.addEventListener('click', function() {
    const button = this;
    Swal.fire({
        title: 'Xác nhận hủy đơn hàng?',
        text: 'Bạn chắc chắn muốn hủy đơn hàng này?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Có, hủy!',
        cancelButtonText: 'Không'
    }).then((result) => {
        if (!result.isConfirmed) return;

        button.disabled = true;
        button.innerHTML =
            `<svg class="animate-spin h-5 w-5 mr-2 inline-block text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Đang hủy...`;

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
                    Swal.fire('Đã hủy!', 'Đơn hàng đã được hủy.', 'success');
                    document.getElementById('order-status').innerHTML =
                        "<span class='text-sm font-medium px-3 py-1 rounded-full bg-red-100 text-red-800'>❌ Đã hủy</span>";
                    button.remove();
                } else {
                    Swal.fire('Lỗi', data.message, 'error');
                    button.disabled = false;
                    button.textContent = '❌ Hủy đơn hàng';
                }
            })
            .catch(() => {
                Swal.fire('Lỗi', 'Không thể hủy đơn, thử lại sau.', 'error');
                button.disabled = false;
                button.textContent = '❌ Hủy đơn hàng';
            });
    });
});
</script>

<?php include('../includes/footer.php'); ?>