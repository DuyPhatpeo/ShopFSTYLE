<?php
// file : model/cartModel.php
class CartModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Tạo giỏ hàng mới cho khách hàng
    public function createCart($customer_id) {
        $cart_id = uniqid('cart_');
        $stmt = $this->conn->prepare("INSERT INTO cart (cart_id, customer_id) VALUES (?, ?)");
        $stmt->bind_param("ss", $cart_id, $customer_id);
        return $stmt->execute() ? $cart_id : false;
    }

    // Lấy giỏ hàng của khách hàng
    public function getCart($customer_id) {
        $stmt = $this->conn->prepare("SELECT * FROM cart WHERE customer_id = ?");
        $stmt->bind_param("s", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Thêm hoặc cập nhật số lượng sản phẩm trong giỏ hàng
    public function addToCart($cart_id, $variant_id, $quantity = 1) {
        // Kiểm tra tồn kho
        $stock = $this->getStockByVariant($variant_id);
        if ($quantity > $stock) return false;

        // Kiểm tra xem đã có trong giỏ chưa
        $stmt = $this->conn->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND variant_id = ?");
        $stmt->bind_param("ss", $cart_id, $variant_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();

        if ($existing) {
            $newQty = $existing['quantity'] + $quantity;
            if ($newQty > $stock) return false;
            $stmt = $this->conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
            $stmt->bind_param("is", $newQty, $existing['cart_item_id']);
        } else {
            $cart_item_id = uniqid('item_');
            $stmt = $this->conn->prepare("INSERT INTO cart_items (cart_item_id, cart_id, variant_id, quantity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $cart_item_id, $cart_id, $variant_id, $quantity);
        }
        return $stmt->execute();
    }

    // Cập nhật số lượng sản phẩm trong giỏ (tăng/giảm)
    public function updateCartItem($cart_item_id, $change) {
        // Lấy thông tin hiện tại
        $stmt = $this->conn->prepare("SELECT quantity, variant_id FROM cart_items WHERE cart_item_id = ?");
        $stmt->bind_param("s", $cart_item_id);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();
        if (!$item) return false;

        $newQty = $item['quantity'] + $change;
        if ($newQty < 1) {
            return $this->removeFromCart($cart_item_id);
        }

        // Kiểm tra tồn kho
        $stock = $this->getStockByVariant($item['variant_id']);
        if ($newQty > $stock) return false;

        $stmt = $this->conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
        $stmt->bind_param("is", $newQty, $cart_item_id);
        return $stmt->execute();
    }

    // Xóa sản phẩm khỏi giỏ hàng
    public function removeFromCart($cart_item_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart_items WHERE cart_item_id = ?");
        $stmt->bind_param("s", $cart_item_id);
        return $stmt->execute();
    }

    // Lấy tất cả sản phẩm trong giỏ hàng
    public function getCartItems($cart_id) {
        $sql = "SELECT ci.*, p.product_name, p.original_price, p.discount_price,
                        c.color_name, s.size_name, pv.quantity AS stock_quantity
                 FROM cart_items ci
                 JOIN product_variants pv ON ci.variant_id = pv.variant_id
                 JOIN product p ON pv.product_id = p.product_id
                 JOIN color c ON pv.color_id = c.color_id
                 LEFT JOIN sizes s ON pv.size_id = s.size_id
                 WHERE ci.cart_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $cart_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Xóa toàn bộ giỏ hàng
    public function clearCart($cart_id) {
        $stmt = $this->conn->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->bind_param("s", $cart_id);
        return $stmt->execute();
    }

    // Truy vấn tồn kho của biến thể
    public function getStockByVariant($variant_id) {
        $stmt = $this->conn->prepare("SELECT quantity FROM product_variants WHERE variant_id = ?");
        $stmt->bind_param("s", $variant_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        return $res ? (int)$res['quantity'] : 0;
    }
    // Lấy các sản phẩm đã được chọn từ giỏ hàng
    public function getSelectedItems($cart_id, $selected_items) {
        $placeholders = str_repeat('?,', count($selected_items) - 1) . '?'; // Chuẩn bị câu truy vấn
        $sql = "SELECT ci.*, p.product_name, p.original_price, p.discount_price,
                    c.color_name, s.size_name, pv.quantity AS stock_quantity
                FROM cart_items ci
                JOIN product_variants pv ON ci.variant_id = pv.variant_id
                JOIN product p ON pv.product_id = p.product_id
                JOIN color c ON pv.color_id = c.color_id
                LEFT JOIN sizes s ON pv.size_id = s.size_id
                WHERE ci.cart_id = ? AND ci.cart_item_id IN ($placeholders)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(str_repeat('s', count($selected_items) + 1), $cart_id, ...$selected_items);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}