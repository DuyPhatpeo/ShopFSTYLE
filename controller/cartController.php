<?php
header('Content-Type: application/json');

require_once '../model/cartModel.php';
require_once '../model/productModel.php';
require_once '../includes/db.php';
function dd($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    die();
}   
try {
    session_start();
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['customer'])) {
        echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập!']);
        exit;
    }

    $customer_id = $_SESSION['customer']['customer_id'];
    $cartModel = new CartModel($conn);
    $productModel = new ProductModel($conn);

    // Lấy giỏ hàng hiện tại hoặc tạo mới
    $cart = $cartModel->getCart($customer_id);
    if (!$cart) {
        $cart_id = $cartModel->createCart($customer_id);
        $cart = ['cart_id' => $cart_id];
    }

    // Xử lý các thao tác với giỏ hàng
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $size_id = $_POST['size_id'] ?? '';
            $color_id = $_POST['color_id'] ?? '';
            $product_id = $_POST['product_id'] ?? '';
            $quantity = intval($_POST['quantity'] ?? 1);

            if (!$size_id || !$color_id || !$product_id) {
                echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin sản phẩm']);
                exit;
            }


            // Kiểm tra số lượng tồn kho
            $variant = $productModel->getProductVariantsBySizeAndColor($size_id, $color_id, $product_id);
            if (!$variant) {
                echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy sản phẩm']);
                exit;
            }

            $variant_id = $variant['variant_id'];
            // Kiểm tra số lượng tồn kho
            if (!isset($variant['quantity']) || $variant['quantity'] < $quantity) {
                echo json_encode(['status' => 'error', 'message' => 'Số lượng không đủ']);
                exit;
            }
            // dd($variant_id);
            if ($cartModel->addToCart($cart['cart_id'], $variant_id, $quantity)) {
                echo json_encode(['status' => 'success', 'message' => 'Đã thêm vào giỏ hàng']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Thêm vào giỏ hàng thất bại']);
            }
            break;

        case 'update':
            $cart_item_id = $_POST['cart_item_id'] ?? '';
            $quantity = intval($_POST['quantity'] ?? 1);
            if (!$cart_item_id) {
                echo json_encode(['status' => 'error', 'message' => 'Thông tin không hợp lệ']);
                exit;
            }
            if ($cartModel->updateCartItem($cart_item_id, $quantity)) {
                echo json_encode(['status' => 'success', 'message' => 'Đã cập nhật giỏ hàng']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Cập nhật thất bại']);
            }
            break;

        case 'remove':
            $cart_item_id = $_POST['cart_item_id'] ?? '';

            if (!$cart_item_id) {
                echo json_encode(['status' => 'error', 'message' => 'Thiếu thông tin sản phẩm']);
                exit;
            }

            if ($cartModel->removeFromCart($cart_item_id)) {
                echo json_encode(['status' => 'success', 'message' => 'Đã xóa sản phẩm']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Xóa thất bại']);
            }
            break;

        case 'clear':
            if ($cartModel->clearCart($cart['cart_id'])) {
                echo json_encode(['status' => 'success', 'message' => 'Đã xóa giỏ hàng']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Xóa giỏ hàng thất bại']);
            }
            break;

        default:
            // Lấy danh sách sản phẩm trong giỏ
            $items = $cartModel->getCartItems($cart['cart_id']);
            $total = 0;

            foreach ($items as &$item) {
                $price = $item['discount_price'] ?: $item['original_price'];
                $item['subtotal'] = $price * $item['quantity'];
                $total += $item['subtotal'];
            }

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'items' => $items,
                    'total' => $total
                ]
            ]);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
