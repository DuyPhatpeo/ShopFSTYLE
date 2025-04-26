<?php
// Cập nhật OrderModel

class OrderModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Tạo đơn hàng mới
     * @return string|bool
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
     * Thêm 1 dòng vào order_detail
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
     * Giảm tồn kho
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

    /**
     * Lấy thông tin chi tiết đơn hàng
     */
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

    /**
     * Lấy chi tiết các sản phẩm trong đơn hàng (đã bỏ main_image)
     */
    public function getOrderDetails($order_id) {
        $stmt = $this->conn->prepare("
            SELECT od.*, p.product_name, c.color_name, s.size_name, pi.image_url AS image_url
            FROM order_detail od 
            LEFT JOIN product_variants pv ON od.variant_id = pv.variant_id 
            LEFT JOIN color c ON pv.color_id = c.color_id 
            LEFT JOIN sizes s ON pv.size_id = s.size_id 
            LEFT JOIN product p ON pv.product_id = p.product_id 
            LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_main = 1
            WHERE od.order_id = ?
        ");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus($order_id, $status) {
        $stmt = $this->conn->prepare("UPDATE `order` SET status = ? WHERE order_id = ?");
        $stmt->bind_param("ss", $status, $order_id);
        return $stmt->execute();
    }

    /**
     * Lấy danh sách đơn hàng của khách hàng
     */
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

    /**
     * Tăng số lượng sản phẩm vào kho (khi huỷ đơn hàng)
     */
    public function increaseStock($variant_id, $qty) {
        $sql = "
            UPDATE product_variants 
            SET quantity = quantity + ? 
            WHERE variant_id = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $qty, $variant_id);
        $stmt->execute();
        if ($stmt->error) {
            error_log("OrderModel::increaseStock error: " . $stmt->error);
        }
        return $stmt->affected_rows > 0;
    }

    /**
     * Huỷ đơn hàng và hoàn kho
     */
    public function cancelOrder($order_id) {
        $this->conn->begin_transaction();
    
        try {
            // Cập nhật trạng thái
            $updateStatus = $this->updateOrderStatus($order_id, 'cancelled');
            if (!$updateStatus) {
                throw new Exception("Không thể cập nhật trạng thái đơn hàng.");
            }
    
            // Lấy chi tiết sản phẩm
            $sql = "
                SELECT od.variant_id, od.quantity 
                FROM order_detail od
                WHERE od.order_id = ?
            ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            // Hoàn lại kho
            while ($row = $result->fetch_assoc()) {
                $success = $this->increaseStock($row['variant_id'], $row['quantity']);
                if (!$success) {
                    throw new Exception("Không thể hoàn kho cho variant_id: " . $row['variant_id']);
                }
            }
    
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("OrderModel::cancelOrder error: " . $e->getMessage());
            return false;
        }
    }
}
?>