<?php
class OrderModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Tạo đơn hàng mới (không transaction)
     * @return string $order_id
     */
    public function createOrder($data) {
        $sql = "
            INSERT INTO `order` 
              (order_id, customer_id, shipping_address, total_amount, payment_method, note, status) 
            VALUES (?, ?, ?, ?, ?, ?, 'pending')
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssiss", 
            $data['order_id'],
            $data['customer_id'],
            $data['shipping_address'],
            $data['total_amount'],
            $data['payment_method'],
            $data['note']
        );
        if (!$stmt->execute()) {
            error_log("OrderModel::createOrder error: " . $stmt->error);
            return false;
        }
        return $data['order_id'];
    }

    /**
     * Thêm 1 dòng vào order_detail (không commit/rollback ở đây)
     * @return bool
     */
    public function addOrderDetail($data) {
        if (empty($data['order_detail_id'])) {
            $data['order_detail_id'] = uniqid('OD_');
        }
        $sql = "
            INSERT INTO order_detail 
              (order_detail_id, order_id, variant_id, quantity, unit_price) 
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param(
            "sssii",
            $data['order_detail_id'],
            $data['order_id'],
            $data['variant_id'],
            $data['quantity'],
            $data['unit_price']
        );
        if (!$stmt->execute()) {
            error_log("OrderModel::addOrderDetail error: " . $stmt->error);
            return false;
        }
        return true;
    }

    /**
     * Giảm tồn kho, trả về true nếu thành công, false nếu không đủ hoặc lỗi
     */
    public function decreaseStock($variant_id, $qty) {
        $sql = "
            UPDATE product_variants 
            SET quantity = quantity - ? 
            WHERE variant_id = ? AND quantity >= ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isi", $qty, $variant_id, $qty);
        $stmt->execute();
        if ($stmt->error) {
            error_log("OrderModel::decreaseStock error: " . $stmt->error);
        }
        return $stmt->affected_rows > 0;
    }

    /* ------------------ các method khác giữ nguyên ------------------ */

    public function getOrder($order_id) {
        $stmt = $this->conn->prepare("
            SELECT *, o.status as order_status 
            FROM `order` o
            LEFT JOIN customer c ON o.customer_id = c.customer_id
            WHERE o.order_id = ?
        ");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getOrderDetails($order_id) {
        $stmt = $this->conn->prepare("
            SELECT od.*, p.product_name, p.main_image, c.color_name, s.size_name 
            FROM order_detail od 
            LEFT JOIN product_variants pv ON od.variant_id = pv.variant_id
            LEFT JOIN color c          ON pv.color_id = c.color_id
            LEFT JOIN sizes s          ON pv.size_id = s.size_id
            LEFT JOIN product p        ON pv.product_id = p.product_id
            WHERE od.order_id = ?
        ");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateOrderStatus($order_id, $status) {
        $stmt = $this->conn->prepare("UPDATE `order` SET status = ? WHERE order_id = ?");
        $stmt->bind_param("ss", $status, $order_id);
        return $stmt->execute();
    }

    public function getCustomerOrders($customer_id) {
        $sql = "
            SELECT o.*, COUNT(od.order_detail_id) as item_count
            FROM `order` o
            LEFT JOIN order_detail od ON o.order_id = od.order_id
            WHERE o.customer_id = ?
            GROUP BY o.order_id
            ORDER BY o.created_at DESC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}