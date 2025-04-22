<?php
class OrderModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Tạo đơn hàng mới
    public function createOrder($data) {
        $sql = "INSERT INTO `order` (order_id, customer_id, shipping_address, total_amount, payment_method, note, status) VALUES (?, ?, ?, ?, ?, ?, '1')";
        $stmt = $this->conn->prepare($sql);
        
        $stmt->bind_param("sssiss", 
            $data['order_id'],
            $data['customer_id'],
            $data['shipping_address'],
            $data['total_amount'],
            $data['payment_method'],
            $data['note']
        );
        
        if (!$stmt->execute()) {
            error_log("Error: " . $stmt->error);
            throw new Exception($stmt->error);
        }
        
        return $data['order_id'];
    }

    // Thêm chi tiết đơn hàng
    public function addOrderDetail($data) {
        if (empty($data['order_detail_id'])) {
            $data['order_detail_id'] = uniqid('order_detail_');
        }
    
        $stmt = $this->conn->prepare("
            INSERT INTO order_detail 
            (order_detail_id, order_id, variant_id, quantity, unit_price) 
            VALUES (?, ?, ?, ?, ?)
        ");
    
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            throw new Exception("Lỗi prepare SQL: " . $this->conn->error);
        }
        $stmt->bind_param("sssii", 
            $data['order_detail_id'],   
            $data['order_id'],          
            $data['variant_id'],        
            $data['quantity'],          
            $data['unit_price']       
        );
    
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            throw new Exception("Lỗi thực thi thêm chi tiết đơn hàng: " . $stmt->error);
        }
    
        return true;
    }
    

    // Lấy thông tin đơn hàng
    public function getOrder($order_id) {
        $stmt = $this->conn->prepare("SELECT *, o.status as order_status FROM `order` o
            LEFT JOIN customer c ON o.customer_id = c.customer_id
            WHERE o.order_id = ?");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Lấy chi tiết đơn hàng
    public function getOrderDetails($order_id) {
        $stmt = $this->conn->prepare("
            SELECT od.*, p.product_name, p.main_image, c.color_name, s.size_name 
            FROM order_detail od 
            LEFT JOIN product_variants pv ON od.variant_id = pv.variant_id
            LEFT JOIN color c ON pv.color_id = c.color_id
            LEFT JOIN sizes s ON pv.size_id = s.size_id
            LEFT JOIN product p ON pv.product_id = p.product_id
            WHERE od.order_id = ?
        ");
        $stmt->bind_param("s", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus($order_id, $status) {
        $stmt = $this->conn->prepare("UPDATE `order` SET status = ? WHERE order_id = ?");
        $stmt->bind_param("ss", $status, $order_id);
        return $stmt->execute();
    }

    // Lấy danh sách đơn hàng của khách hàng
    public function getCustomerOrders($customer_id) {
        $query = "SELECT o.*, COUNT(od.order_detail_id) as item_count
                 FROM `order` o
                 LEFT JOIN order_detail od ON o.order_id = od.order_id
                 WHERE o.customer_id = ?
                 GROUP BY o.order_id
                 ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
?> 