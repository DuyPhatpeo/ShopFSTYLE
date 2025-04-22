<?php
session_start();
require_once '../includes/db.php';
require_once '../model/orderModel.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    header('Location: auth/login.php');
    exit;
}

// Kiểm tra order_id
if (!isset($_GET['id'])) {
    header('Location: home/index.php');
    exit;
}

$order_id = $_GET['id'];
$customer_id = $_SESSION['customer']['customer_id'];
$orderModel = new OrderModel($conn);

// Lấy thông tin đơn hàng
$order = $orderModel->getOrder($order_id);

// Kiểm tra quyền truy cập
if (!$order || $order['customer_id'] != $customer_id) {
    header('Location: home/index.php');
    exit;
}

// Lấy chi tiết đơn hàng
$orderDetails = $orderModel->getOrderDetails($order_id);

// Include header sau khi đã xử lý xong các header redirect
include('../includes/header.php');
include('../includes/search.php');
?>

<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-8">Chi tiết đơn hàng #<?php echo $order_id; ?></h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Thông tin đơn hàng -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="space-y-4">
                    <?php foreach ($orderDetails as $item): ?>
                    <div class="flex items-center space-x-4 border-b pb-4">
                        <img src="../<?php echo htmlspecialchars($item['main_image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                             class="w-24 h-24 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-semibold"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                            <p class="text-gray-500">
                                Màu: <?php echo htmlspecialchars($item['color_name']); ?>
                                <?php if ($item['size_name']): ?>
                                | Size: <?php echo htmlspecialchars($item['size_name']); ?>
                                <?php endif; ?>
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <div>
                                    <span class="text-gray-500">Số lượng:</span>
                                    <span class="font-semibold"><?php echo $item['quantity']; ?></span>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold"><?php echo number_format($item['unit_price'] * $item['quantity']); ?>đ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Thông tin giao hàng -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Thông tin đơn hàng</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-1">Trạng thái</label>
                        <p class="font-semibold">
                            <?php
                            $status = [
                                'pending' => 'Đang chờ xử lý',
                                'processing' => 'Đang xử lý',
                                'shipping' => 'Đang giao hàng',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy'
                            ];
                            echo $status[$order['order_status']] ?? $order['order_status'];
                            ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Ngày đặt hàng</label>
                        <p><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></p>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Phương thức thanh toán</label>
                        <p>
                            <?php
                            $payment_methods = [
                                'cod' => 'Thanh toán khi nhận hàng',
                                'bank' => 'Chuyển khoản ngân hàng'
                            ];
                            echo $payment_methods[$order['payment_method']] ?? $order['payment_method'];
                            ?>
                        </p>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Địa chỉ giao hàng</label>
                        <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                    </div>
                    <?php if ($order['note']): ?>
                    <div>
                        <label class="block text-gray-700 mb-1">Ghi chú</label>
                        <p><?php echo nl2br(htmlspecialchars($order['note'])); ?></p>
                    </div>
                    <?php endif; ?>
                    <div class="border-t pt-4">
                        <div class="flex justify-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($order['total_amount']); ?>đ</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span>0đ</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Tổng cộng:</span>
                            <span><?php echo number_format($order['total_amount']); ?>đ</span>
                        </div>
                    </div>
                    <?php if ($order['status'] == 'pending'): ?>
                    <button onclick="cancelOrder('<?php echo $order_id; ?>')" 
                            class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700">
                        Hủy đơn hàng
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Hủy đơn hàng
function cancelOrder(orderId) {
    if (!confirm('Bạn có chắc muốn hủy đơn hàng này?')) return;

    const formData = new FormData();
    formData.append('action', 'cancel');
    formData.append('order_id', orderId);

    fetch('controller/orderController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Đã hủy đơn hàng thành công!');
            location.reload();
        } else {
            alert(data.message);
        }
    });
}
</script>

<?php include('../includes/footer.php'); ?> 