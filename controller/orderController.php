<?php
session_start();
require_once '../includes/db.php';
require_once '../model/orderModel.php';
require_once '../model/cartModel.php';
require_once '../model/customerModel.php';

header('Content-Type: application/json');
function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die();
}  
// Kiểm tra đăng nhập
if (!isset($_SESSION['customer'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$customer_id = $_SESSION['customer']['customer_id'];
$orderModel = new OrderModel($conn);
$cartModel = new CartModel($conn);
$customerModel = new CustomerModel($conn);

// Lấy action từ request
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'checkout':
        // Lấy thông tin giỏ hàng
        $cart = $cartModel->getCart($customer_id);
        if (!$cart) {
            echo json_encode(['status' => 'error', 'message' => 'Giỏ hàng trống']);
            exit;
        }

        // Lấy thông tin khách hàng
        $customer = $customerModel->getCustomer($customer_id);
        if (!$customer) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy thông tin khách hàng']);
            exit;
        }

        // Validate dữ liệu
        $required_fields = ['fullname', 'email', 'phone', 'shipping_address', 'payment_method'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                echo json_encode(['status' => 'error', 'message' => 'Vui lòng điền đầy đủ thông tin']);
                exit;
            }
        }

        // Tạo đơn hàng
        $order_data = [
            'order_id' => uniqid('order_'),
            'customer_id' => $customer_id,
            'shipping_address' => $_POST['shipping_address'],
            'total_amount' => $_POST['total_amount'],
            'payment_method' => $_POST['payment_method'],
            'note' => $_POST['note'] ?? ''
        ];
        
        try {
            // Tạo đơn hàng
            $order_id = $orderModel->createOrder($order_data);
            
            // Thêm chi tiết đơn hàng
            $items = $cartModel->getCartItems($cart['cart_id']);
            foreach ($items as $item) {
                $orderModel->addOrderDetail([
                    'order_detail_id' => uniqid('order_detail_'),
                    'order_id' => $order_id,
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['discount_price'] ?: $item['original_price']
                ]);
            }
            
            // Xóa giỏ hàng
            $cartModel->clearCart($cart['cart_id']);
            echo json_encode(['status' => 'success', 'message' => 'Đặt hàng thành công', 'order_id' => $order_id]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
        break;

    case 'cancel':
        $order_id = $_POST['order_id'] ?? 0;
        if (!$order_id) {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng']);
            exit;
        }

        // Kiểm tra quyền truy cập
        $order = $orderModel->getOrder($order_id);
        if (!$order || $order['customer_id'] != $customer_id) {
            echo json_encode(['status' => 'error', 'message' => 'Bạn không có quyền hủy đơn hàng này']);
            exit;
        }

        // Kiểm tra trạng thái đơn hàng
        if ($order['status'] != 'pending') {
            echo json_encode(['status' => 'error', 'message' => 'Chỉ có thể hủy đơn hàng đang chờ xử lý']);
            exit;
        }

        // Hủy đơn hàng
        if ($orderModel->updateOrderStatus($order_id, 'cancelled')) {
            echo json_encode(['status' => 'success', 'message' => 'Đã hủy đơn hàng thành công']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Có lỗi xảy ra khi hủy đơn hàng']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Action không hợp lệ']);
        break;
}
?> 