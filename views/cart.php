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

// Lấy giỏ hàng hoặc tạo mới
$cart = $cartModel->getCart($customer_id);
if (!$cart) {
    $cart_id = $cartModel->createCart($customer_id);
    $cart = ['cart_id' => $cart_id];
}

// Lấy sản phẩm trong giỏ
$items = $cartModel->getCartItems($cart['cart_id']);
?>

<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-8">Giỏ hàng</h1>

    <?php if (empty($items)): ?>
    <div class="text-center py-12">
        <p class="text-gray-500 text-lg mb-4">Giỏ hàng của bạn đang trống</p>
        <a href="<?= USER_URL ?>" class="text-blue-600 hover:text-blue-800">Tiếp tục mua sắm</a>
    </div>
    <?php else: ?>
    <form id="checkout-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Danh sách sản phẩm -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="space-y-4">
                    <?php foreach ($items as $item): 
                            $price = $item['discount_price'] ?: $item['original_price'];
                            $subtotal = $price * $item['quantity'];
                            $isOut = ($item['stock_quantity'] < $item['quantity']);
                        ?>
                    <div
                        class="relative flex items-center space-x-4 border-b pb-4 <?php echo $isOut ? 'opacity-50' : ''; ?>">
                        <!-- Checkbox chọn sản phẩm -->
                        <div class="flex-shrink-0">
                            <input type="checkbox" name="selected_items[]" value="<?= $item['cart_item_id'] ?>"
                                class="select-item" data-subtotal="<?= $subtotal ?>"
                                <?= $isOut ? 'disabled' : 'checked' ?> />
                        </div>
                        <!-- Nhãn "Hết hàng" -->
                        <?php if ($isOut): ?>
                        <div
                            class="absolute top-2 right-2 bg-gray-800 bg-opacity-75 text-white text-xs font-bold px-2 py-1 rounded">
                            Hết hàng
                        </div>
                        <?php endif; ?>

                        <img src="../<?= htmlspecialchars($item['main_image']) ?>"
                            alt="<?= htmlspecialchars($item['product_name']) ?>"
                            class="w-24 h-24 object-cover rounded" />
                        <div class="flex-1">
                            <h3 class="font-semibold"><?= htmlspecialchars($item['product_name']) ?></h3>
                            <p class="text-gray-500">
                                Màu: <?= htmlspecialchars($item['color_name']) ?>
                                <?php if (!empty($item['size_name'])): ?>
                                | Size: <?= htmlspecialchars($item['size_name']) ?>
                                <?php endif; ?>
                            </p>
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="updateQuantity('<?= $item['cart_item_id'] ?>', -1)"
                                        class="px-2 py-1 border rounded">-</button>
                                    <span class="px-2"
                                        data-quantity="<?= $item['quantity'] ?>"><?= $item['quantity'] ?></span>
                                    <button type="button" onclick="updateQuantity('<?= $item['cart_item_id'] ?>', 1)"
                                        class="px-2 py-1 border rounded" <?= $isOut ? 'disabled' : '' ?>>+</button>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold"><?= number_format($subtotal) ?>đ</p>
                                    <button type="button" onclick="removeItem('<?= $item['cart_item_id'] ?>')"
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
                <div class="space-y-4">
                    <div>
                        <label class="block text-gray-700 mb-1">Họ và tên</label>
                        <input type="text" name="fullname" value="<?= htmlspecialchars($customer['full_name']) ?>"
                            class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>"
                            class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>"
                            class="w-full p-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Địa chỉ giao hàng</label>
                        <textarea name="shipping_address" class="w-full p-2 border rounded"
                            required><?= htmlspecialchars($customer['address']) ?></textarea>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Ghi chú</label>
                        <textarea name="note" class="w-full p-2 border rounded"></textarea>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between mb-2">
                            <span>Tạm tính:</span>
                            <span id="display-total">0đ</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span>0đ</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Tổng cộng:</span>
                            <span id="display-grandtotal">0đ</span>
                        </div>
                    </div>
                    <input type="hidden" name="payment_method" value="cod">
                    <input type="hidden" name="total_amount" id="total_amount" value="0">
                    <input type="hidden" name="discount_amount" value="0">
                    <input type="hidden" name="status" value="0">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                        Đặt hàng
                    </button>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
// Hàm tính lại tổng
function recalcTotal() {
    const checkboxes = document.querySelectorAll('.select-item');
    let total = 0;
    checkboxes.forEach(cb => {
        if (cb.checked) {
            total += parseInt(cb.dataset.subtotal, 10);
        }
    });
    document.getElementById('display-total').textContent = new Intl.NumberFormat().format(total) + 'đ';
    document.getElementById('display-grandtotal').textContent = new Intl.NumberFormat().format(total) + 'đ';
    document.getElementById('total_amount').value = total;
}

// Gắn sự kiện recalc khi load và khi thay đổi checkbox
window.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.select-item').forEach(cb => cb.addEventListener('change', recalcTotal));
    recalcTotal();
});

// Cập nhật số lượng sản phẩm trong giỏ
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
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                location.reload();
            } else {
                alert(data.message);
            }
        });
}

// Xử lý submit đơn hàng
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form); // FormData only includes checked checkboxes
    formData.append('action', 'checkout');

    if (!formData.has('selected_items[]')) {
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
            } else {
                alert(data.message);
            }
        });
});
</script>

<?php include('../includes/footer.php'); ?>