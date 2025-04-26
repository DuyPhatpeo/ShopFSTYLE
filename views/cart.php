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

$customer_id    = $_SESSION['customer']['customer_id'];
$cartModel      = new CartModel($conn);
$customerModel  = new CustomerModel($conn);

// Lấy thông tin khách hàng
$customer = $customerModel->getById($customer_id);

// Lấy giỏ hàng hoặc tạo mới
$cart = $cartModel->getCart($customer_id);
if (!$cart) {
    $cart_id = $cartModel->createCart($customer_id);
    $cart    = ['cart_id' => $cart_id];
}

// Lấy sản phẩm trong giỏ, đã bao gồm trường image_url
$items = $cartModel->getCartItems($cart['cart_id']);
?>

<div class="container mx-auto py-12 px-4">
    <h1 class="text-4xl font-extrabold text-gray-800 mb-8">Giỏ hàng của bạn</h1>

    <form id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Danh sách sản phẩm -->
        <div class="lg:col-span-2 space-y-6">
            <?php if (empty($items)): ?>
            <div class="flex flex-col items-center justify-center py-16 bg-white rounded-2xl shadow-lg">
                <p class="text-gray-500 text-lg mb-6">Giỏ hàng của bạn đang trống</p>
                <a href="<?= USER_URL ?>"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Tiếp tục mua sắm
                </a>
            </div>
            <?php else: ?>
            <?php foreach ($items as $item):
                    $original = $item['original_price'];
                    $discount = $item['discount_price'];
                    $price    = $discount ?: $original;
                    $subtotal = $price * $item['quantity'];
                    $isOut    = ($item['stock_quantity'] < $item['quantity']);
                ?>
            <div
                class="bg-white rounded-2xl shadow-lg p-6 flex items-center space-x-6 relative <?= $isOut ? 'opacity-50' : 'hover:shadow-xl transition'; ?>">
                <input type="checkbox" name="selected_items[]" value="<?= $item['cart_item_id'] ?>"
                    class="select-item h-5 w-5 text-indigo-600" data-subtotal="<?= $subtotal ?>"
                    <?= $isOut ? 'disabled' : 'checked' ?> />

                <?php if ($isOut): ?>
                <span class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                    Hết hàng
                </span>
                <?php endif; ?>

                <!-- Hình sản phẩm -->
                <div
                    class="w-28 h-28 flex items-center justify-center overflow-hidden rounded-lg bg-gray-100 shadow-sm">
                    <img src="<?= USER_URL ?>/admin/uploads/products/<?= htmlspecialchars($item['image_url']) ?>"
                        alt="<?= htmlspecialchars($item['product_name']) ?>"
                        class="object-contain w-full h-full transition-transform duration-300 hover:scale-105" />
                </div>

                <div class="flex-1">
                    <h3 class="text-xl font-semibold text-gray-800"><?= htmlspecialchars($item['product_name']) ?></h3>

                    <?php if ($discount): ?>
                    <p class="text-gray-400 line-through"><?= number_format($original) ?>đ</p>
                    <p class="text-lg font-bold text-red-600"><?= number_format($discount) ?>đ</p>
                    <?php else: ?>
                    <p class="text-lg font-bold text-gray-800"><?= number_format($original) ?>đ</p>
                    <?php endif; ?>

                    <p class="text-gray-500 mt-1">
                        Màu: <?= htmlspecialchars($item['color_name']) ?>
                        <?php if (!empty($item['size_name'])): ?> | Size:
                        <?= htmlspecialchars($item['size_name']) ?><?php endif; ?>
                    </p>

                    <div class="flex items-center justify-between mt-4">
                        <div class="flex items-center border rounded-lg overflow-hidden">
                            <button type="button" onclick="updateQuantity('<?= $item['cart_item_id'] ?>', -1)"
                                class="px-3 py-1 hover:bg-gray-100 transition">-</button>
                            <span class="px-4" data-quantity="<?= $item['quantity'] ?>"><?= $item['quantity'] ?></span>
                            <button type="button" onclick="updateQuantity('<?= $item['cart_item_id'] ?>', 1)"
                                class="px-3 py-1 hover:bg-gray-100 transition"
                                <?= $isOut ? 'disabled' : '' ?>>+</button>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-800"><?= number_format($subtotal) ?>đ</p>
                            <button type="button" onclick="removeItem('<?= $item['cart_item_id'] ?>')"
                                class="text-red-500 text-sm hover:text-red-700 transition">Xóa</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Form thanh toán -->
        <div class="bg-white rounded-2xl shadow-lg p-6 space-y-6">
            <h2 class="text-2xl font-semibold text-gray-800">Thông tin thanh toán</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 mb-2">Họ và tên</label>
                    <input type="text" name="fullname" value="<?= htmlspecialchars($customer['full_name']) ?>"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Số điện thoại</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Địa chỉ giao hàng</label>
                    <textarea name="shipping_address"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required><?= htmlspecialchars($customer['address']) ?></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 mb-2">Ghi chú</label>
                    <textarea name="note"
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <div class="border-t pt-4 space-y-3">
                <div class="flex justify-between">
                    <span>Tạm tính:</span>
                    <span id="display-total" class="font-medium">0đ</span>
                </div>
                <div class="flex justify-between">
                    <span>Phí vận chuyển:</span>
                    <span class="font-medium">0đ</span>
                </div>
                <div class="flex justify-between text-xl font-bold">
                    <span>Tổng cộng:</span>
                    <span id="display-grandtotal">0đ</span>
                </div>
            </div>

            <input type="hidden" name="payment_method" value="cod">
            <input type="hidden" name="total_amount" id="total_amount" value="0">
            <input type="hidden" name="discount_amount" value="0">
            <input type="hidden" name="status" value="0">
            <button type="submit"
                class="w-full py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Đặt hàng</button>
        </div>
    </form>
</div>

<script>
// Tính lại tổng tiền
function recalcTotal() {
    const checkboxes = document.querySelectorAll('.select-item');
    let total = 0;
    checkboxes.forEach(cb => {
        if (cb.checked) total += parseInt(cb.dataset.subtotal, 10);
    });
    document.getElementById('display-total').textContent = new Intl.NumberFormat().format(total) + 'đ';
    document.getElementById('display-grandtotal').textContent = new Intl.NumberFormat().format(total) + 'đ';
    document.getElementById('total_amount').value = total;
}

window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.select-item').forEach(cb => cb.addEventListener('change', recalcTotal));
    recalcTotal();
});

// Cập nhật số lượng sản phẩm
function updateQuantity(cartItemId, change) {
    const formData = new FormData();
    formData.append('action', 'update');
    formData.append('cart_item_id', cartItemId);
    formData.append('quantity', change);

    fetch('<?= USER_URL ?>/controller/cartController.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => data.status === 'success' ? location.reload() : alert(data.message));
}

// Xóa sản phẩm khỏi giỏ
function removeItem(cartItemId) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('cart_item_id', cartItemId);

    fetch('<?= USER_URL ?>/controller/cartController.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => data.status === 'success' ? location.reload() : alert(data.message));
}

// Xử lý submit đơn hàng
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'checkout');

    if (![...formData.keys()].includes('selected_items[]')) {
        alert('Vui lòng chọn ít nhất một sản phẩm để đặt hàng.');
        return;
    }

    fetch('<?= USER_URL ?>/controller/orderController.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Đặt hàng thành công!');
                window.location.href = 'order_detail.php?id=' + data.order_id;
            } else alert(data.message);
        });
});
</script>

<?php include('../includes/footer.php'); ?>