<?php
// file: controller/orderController.php
session_start();
require_once '../includes/db.php';
require_once '../model/orderModel.php';
require_once '../model/cartModel.php';
require_once '../model/customerModel.php';

header('Content-Type: application/json');
if (!isset($_SESSION['customer'])) {
    echo json_encode(['status'=>'error','message'=>'Vui lòng đăng nhập']);
    exit;
}
$customer_id = $_SESSION['customer']['customer_id'];
$orderModel  = new OrderModel($conn);
$cartModel   = new CartModel($conn);

$action = $_POST['action'] ?? '';
if ($action !== 'checkout') {
    echo json_encode(['status'=>'error','message'=>'Action không hợp lệ']);
    exit;
}

// 1. Lấy giỏ & selected_items[]
$cart     = $cartModel->getCart($customer_id);
$selected = $_POST['selected_items'] ?? [];
if (!$cart || empty($selected)) {
    echo json_encode(['status'=>'error','message'=>'Vui lòng chọn ít nhất một sản phẩm.']);
    exit;
}
$items = $cartModel->getSelectedItems($cart['cart_id'], $selected);
if (empty($items)) {
    echo json_encode(['status'=>'error','message'=>'Không tìm thấy sản phẩm đã chọn.']);
    exit;
}

// 2. Lấy dữ liệu từ form — CHÚ Ý phải trùng name trong <input>
$shipping = $_POST['shipping_address'] ?? '';    // trước bạn dùng ['address']
$totalAmt = $_POST['total_amount'] ?? 0;         // trước bạn không đưa vào orderData
$payment  = $_POST['payment_method'] ?? 'cod';
$note     = $_POST['note'] ?? '';

$conn->begin_transaction();
try {
    // 3. Tạo order
    $order_id = uniqid('ORD_');
    $orderData = [
        'order_id'         => $order_id,
        'customer_id'      => $customer_id,
        'shipping_address' => $shipping,
        'total_amount'     => $totalAmt,
        'payment_method'   => $payment,
        'note'             => $note
    ];
    if (!$orderModel->createOrder($orderData)) {
        throw new Exception("Tạo order thất bại.");
    }

    // 4. Duyệt từng item
    foreach ($items as $it) {
        $detail = [
            'order_detail_id' => uniqid('OD_'),
            'order_id'        => $order_id,
            'variant_id'      => $it['variant_id'],
            'quantity'        => $it['quantity'],
            'unit_price'      => $it['discount_price'] ?: $it['original_price']
        ];
        if (!$orderModel->addOrderDetail($detail)) {
            throw new Exception("Lỗi thêm chi tiết cho variant {$it['variant_id']}.");
        }
        // trừ kho nhưng không throw
        $orderModel->decreaseStock($it['variant_id'], $it['quantity']);
        // xoá khỏi cart
        $cartModel->removeFromCart($it['cart_item_id']);
    }

    $conn->commit();
    echo json_encode(['status'=>'success','order_id'=>$order_id]);
} catch (Exception $e) {
    $conn->rollback();
    // gửi luôn $e->getMessage() về client để debug
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}// Xử lý hủy đơn hàng
if ($action === 'cancel_order') {
    $order_id = $_POST['order_id'] ?? '';
    if (!$order_id) {
        echo json_encode(['status'=>'error','message'=>'Order ID không hợp lệ']);
        exit;
    }
    // Lấy và kiểm tra quyền
    $order = $orderModel->getOrder($order_id);
    if (!$order || $order['customer_id'] !== $customer_id) {
        echo json_encode(['status'=>'error','message'=>'Không tìm thấy đơn hoặc không quyền hủy']);
        exit;
    }
    if ($order['order_status'] !== 'pending') {
        echo json_encode(['status'=>'error','message'=>'Chỉ hủy đơn chờ xử lý']);
        exit;
    }
    // Hủy đơn
    if ($orderModel->cancelOrder($order_id)) {
        echo json_encode(['status'=>'success','message'=>'Đã hủy đơn thành công']);
    } else {
        echo json_encode(['status'=>'error','message'=>'Hủy đơn thất bại']);
    }
    exit;
}

// Nếu không phải action nào
echo json_encode(['status'=>'error','message'=>'Action không hợp lệ']);
exit;