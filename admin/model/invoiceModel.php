<?php
// File: admin/model/orderModel.php

require_once __DIR__ . '/../controller/stringHelper.php';

/**
 * Lấy danh sách đơn hàng có phân trang, tìm kiếm và lọc theo status, payment_method
 *
 * @param mysqli $conn Kết nối CSDL
 * @param int $page Trang hiện tại (mặc định 1)
 * @param int $limit Số bản ghi trên mỗi trang (mặc định 10)
 * @param string $search Chuỗi tìm kiếm (trong shipping_address, billing_address, order_id)
 * @param string|null $status Lọc theo trạng thái đơn hàng (ví dụ: pending, processing, completed,...)
 * @param string|null $paymentMethod Lọc theo phương thức thanh toán (ví dụ: cod, credit_card, paypal,...)
 *
 * @return array Mảng chứa danh sách đơn hàng, tổng số đơn hàng, tổng số trang và trang hiện tại
 */
function getOrdersWithPagination($conn, $page = 1, $limit = 10, $search = "", $status = null, $paymentMethod = null) {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $offset = ($page - 1) * $limit;

    // Điều kiện tìm kiếm: tìm trong shipping_address, billing_address hoặc order_id
    $searchParam = '%' . trim($search) . '%';
    $conditions = "(o.shipping_address LIKE ? OR o.billing_address LIKE ? OR o.order_id LIKE ?)";
    $paramTypes = "sss";
    $params = [$searchParam, $searchParam, $searchParam];

    // Lọc theo trạng thái
    if ($status !== null && $status !== '') {
        $conditions .= " AND o.status = ?";
        $paramTypes .= "s";
        $params[] = $status;
    }

    // Lọc theo phương thức thanh toán
    if ($paymentMethod !== null && $paymentMethod !== '') {
        $conditions .= " AND o.payment_method = ?";
        $paramTypes .= "s";
        $params[] = $paymentMethod;
    }

    // Đếm tổng số đơn hàng thỏa mãn điều kiện
    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM `order` o WHERE $conditions");
    $stmtCount->bind_param($paramTypes, ...$params);
    $stmtCount->execute();
    $totalOrders = (int)($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
    $totalPages  = max(1, ceil($totalOrders / $limit));
    $stmtCount->close();

    // Lấy danh sách đơn hàng với điều kiện và phân trang
    $sql = "SELECT o.*
            FROM `order` o
            WHERE $conditions
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $paramTypes .= "ii";
    $params[] = $limit;
    $params[] = $offset;
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $orders = $stmt->get_result();
    $stmt->close();

    return [
        'orders'      => $orders,
        'totalOrders' => $totalOrders,
        'totalPages'  => $totalPages,
        'currentPage' => $page
    ];
}

/**
 * Lấy chi tiết đơn hàng theo order_id
 *
 * @param mysqli $conn Kết nối CSDL
 * @param string $order_id Mã đơn hàng
 *
 * @return array|null Thông tin đơn hàng nếu tìm thấy, ngược lại trả về null
 */
function getOrderById($conn, $order_id) {
    $stmt = $conn->prepare("SELECT * FROM `order` WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $order;
}

/**
 * Cập nhật trạng thái đơn hàng
 *
 * @param mysqli $conn Kết nối CSDL
 * @param string $order_id Mã đơn hàng cần cập nhật
 * @param string $new_status Trạng thái mới
 *
 * @return bool True nếu cập nhật thành công, false nếu thất bại
 */
function updateOrderStatus($conn, $order_id, $new_status) {
    $stmt = $conn->prepare("UPDATE `order` SET status = ? WHERE order_id = ?");
    if (!$stmt) {
        error_log("Lỗi prepare: " . $conn->error);
        return false;
    }
    $stmt->bind_param("ss", $new_status, $order_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Xóa đơn hàng theo order_id
 *
 * @param mysqli $conn Kết nối CSDL
 * @param string $order_id Mã đơn hàng cần xóa
 *
 * @return bool True nếu xóa thành công, false nếu thất bại
 */
function deleteOrder($conn, $order_id) {
    $stmt = $conn->prepare("DELETE FROM `order` WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Đếm tổng số đơn hàng (dùng cho Dashboard hoặc thống kê)
 *
 * @param mysqli $conn Kết nối CSDL
 *
 * @return int Tổng số đơn hàng
 */
function countAllOrders($conn) {
    $result = $conn->query("SELECT COUNT(*) as total FROM `order`");
    return (int)($result->fetch_assoc()['total'] ?? 0);
}

/**
 * Thêm đơn hàng mới vào hệ thống
 *
 * @param mysqli $conn Kết nối CSDL
 * @param string $customer_id Mã khách hàng
 * @param string $shipping_address Địa chỉ giao hàng
 * @param string $billing_address Địa chỉ thanh toán
 * @param float $discount_amount Số tiền giảm giá
 * @param float $total_amount Tổng tiền đơn hàng
 * @param string $promotion Mã khuyến mãi (nếu có)
 * @param string $status Trạng thái đơn hàng
 * @param string $payment_method Phương thức thanh toán
 * @param string $note Ghi chú đơn hàng
 *
 * @return bool True nếu thêm thành công, false nếu thất bại
 */
function addOrder($conn, $customer_id, $shipping_address, $billing_address, $discount_amount, $total_amount, $promotion, $status, $payment_method, $note) {
    $order_id = generateUCCID(); // Tạo mã đơn hàng duy nhất
    $stmt = $conn->prepare("INSERT INTO `order` (order_id, customer_id, shipping_address, billing_address, discount_amount, total_amount, promotion, status, payment_method, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Lỗi prepare: " . $conn->error);
        return false;
    }
    $stmt->bind_param("ssssddssss", $order_id, $customer_id, $shipping_address, $billing_address, $discount_amount, $total_amount, $promotion, $status, $payment_method, $note);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}