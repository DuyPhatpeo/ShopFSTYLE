<?php
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
    function dd($data) {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die();
    }  
    // Thêm sản phẩm vào giỏ hàng
    public function addToCart($cart_id, $variant_id, $quantity = 1) {
        // dd($variant_id);
        // Kiểm tra xem sản phẩm đã có trong giỏ chưa
        $stmt = $this->conn->prepare("SELECT * FROM cart_items WHERE cart_id = ? AND variant_id = ?");
        $stmt->bind_param("ss", $cart_id, $variant_id);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        // dd($existing);
        if ($existing) {
            // Cập nhật số lượng nếu đã có
            $new_quantity = $existing['quantity'] + $quantity;
            $stmt = $this->conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
            $stmt->bind_param("is", $new_quantity, $existing['cart_item_id']);
        } else {
            // Thêm mới nếu chưa có
            $cart_item_id = uniqid('item_');
            $stmt = $this->conn->prepare("INSERT INTO cart_items (cart_item_id, cart_id, variant_id, quantity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $cart_item_id, $cart_id, $variant_id, $quantity);
        }
        return $stmt->execute();
    }

    // Cập nhật số lượng sản phẩm trong giỏ
    public function updateCartItem($cart_item_id, $quantity) {
        // Cập nhật
        $stmt = $this->conn->prepare("
            UPDATE cart_items 
            SET quantity = quantity + ? 
            WHERE cart_item_id = ? AND quantity + ? >= 1
        ");
        $stmt->bind_param("isi", $quantity, $cart_item_id, $quantity);
        $stmt->execute();
    
        // Xoá nếu về 0 (optional)
        $stmt = $this->conn->prepare("
            DELETE FROM cart_items 
            WHERE cart_item_id = ? AND quantity <= 0
        ");
        $stmt->bind_param("s", $cart_item_id);
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
        $query = "SELECT ci.*, p.product_name, p.original_price, p.discount_price, p.main_image,
                         c.color_name, s.size_name, pv.quantity as stock
                  FROM cart_items ci
                  JOIN product_variants pv ON ci.variant_id = pv.variant_id
                  JOIN product p ON pv.product_id = p.product_id
                  JOIN color c ON pv.color_id = c.color_id
                  LEFT JOIN sizes s ON pv.size_id = s.size_id
                  WHERE ci.cart_id = ?";
        $stmt = $this->conn->prepare($query);
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
}
?> 