<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    header('Location: auth/login.php');
    exit;
}

include('../includes/header.php');
include('../includes/search.php');
require_once '../includes/db.php';
require_once '../model/cartModel.php';
require_once '../model/customerModel.php';

$customer_id = $_SESSION['customer']['customer_id'];
$cartModel = new CartModel($conn);
$customerModel = new CustomerModel($conn);

// Lấy thông tin khách hàng
$customer = $customerModel->getById($customer_id);
function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die();
}   
// Lấy giỏ hàng
$cart = $cartModel->getCart($customer_id);
if (!$cart) {
    $cart_id = $cartModel->createCart($customer_id);
    $cart = ['cart_id' => $cart_id];
}
// Lấy sản phẩm trong giỏ
$items = $cartModel->getCartItems($cart['cart_id']);
$total = 0;
// dd($items);
// dd($items);
foreach ($items as $item) {
    $price = $item['discount_price'] ?: $item['original_price'];
    $total +=  $price * $item['quantity'];
}
?>

<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-8">Giỏ hàng</h1>

    <?php if (empty($items)): ?>
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg mb-4">Giỏ hàng của bạn đang trống</p>
        <a href="<?= USER_URL ?>" class="text-blue-600 hover:text-blue-800">Tiếp tục mua sắm</a>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Danh sách sản phẩm -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="space-y-4">
                    <?php foreach ($items as $item): ?>
                    <div class="flex items-center space-x-4 border-b pb-4">
                        <img src="../<?php echo $item['main_image']; ?>" 
                             alt="<?php echo $item['product_name']; ?>"
                             class="w-24 h-24 object-cover rounded" />
                        <div class="flex-1">
                            <h3 class="font-semibold"><?php echo $item['product_name']; ?></h3>
                            <p class="text-gray-500">
                                Màu: <?php echo $item['color_name']; ?>
                                <?php if (!empty($item['size_name'])): ?>
                                | Size: <?php echo $item['size_name']; ?>
                                <?php endif; ?>
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center space-x-2">
                                    <button onclick="updateQuantity('<?php echo $item['cart_item_id']; ?>', -1)" 
                                            class="px-2 py-1 border rounded">-</button>
                                    <span class="px-2" id="quantity_current" data-quantity="<?php echo $item['quantity']; ?>"><?php echo $item['quantity']; ?></span>
                                    <button onclick="updateQuantity('<?php echo $item['cart_item_id']; ?>', 1)" 
                                            class="px-2 py-1 border rounded">+</button>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold"><?php echo number_format($item['quantity'] * ($item['discount_price'] ?: $item['original_price'])); ?>đ</p>
                                    <button onclick="removeItem('<?php echo $item['cart_item_id']; ?>')" 
                                            class="text-red-500 text-sm">Xóa</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Form thanh toán -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Thông tin thanh toán</h2>
                <form id="checkout-form" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-1">Họ và tên</label>
                        <input type="text" name="fullname" value="<?php echo htmlspecialchars($customer['full_name']); ?>" 
                               class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" 
                               class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" 
                               class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Địa chỉ giao hàng</label>
                        <textarea name="shipping_address" class="w-full p-2 border rounded" required><?php echo htmlspecialchars($customer['address']); ?></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="note" class="w-full p-2 border rounded"></textarea>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?php echo number_format($total); ?>đ</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span>0đ</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Tổng cộng:</span>
                            <span><?php echo number_format($total); ?>đ</span>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" value="cod">
                    <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
                    <input type="hidden" name="discount_amount" value="0">
                    <input type="hidden" name="status" value="0">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        Đặt hàng
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// Cập nhật số lượng sản phẩm
function updateQuantity(cartItemId, change) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('cart_item_id', cartItemId);
    formData.append('quantity', change);
    formData.append('current_quantity', document.getElementById('quantity_current').getAttribute('data-quantity'));

    fetch('<?= USER_URL ?>/controller/cartController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

// Xóa sản phẩm khỏi giỏ hàng
function removeItem(cartItemId) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này?')) return;

    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('cart_item_id', cartItemId);

    fetch('<?= USER_URL ?>/controller/cartController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

// Xử lý form thanh toán
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    formData.append('action', 'checkout');

    fetch('<?= USER_URL ?>/controller/orderController.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Đặt hàng thành công!');
            window.location.href = 'order_detail.php?id=' + data.order_id;
        } else {
            alert(data.message);
        }
    });
});
</script>

<?php include('../includes/footer.php'); ?> 