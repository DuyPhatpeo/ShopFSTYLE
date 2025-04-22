<?php
// File: admin/controller/invoiceController.php

require_once '../../model/invoiceModel.php';
require_once('../../../model/orderModel.php');

/**
 * Lấy danh sách đơn hàng theo các filter từ URL
 *
 * @param mysqli $conn Kết nối CSDL
 * @return array Dữ liệu đơn hàng bao gồm: orders, totalPages, currentPage, totalOrders
 */
function getFilteredOrders($conn) {
    $orderModel = new OrderModel($conn);
    
    // Lấy các tham số từ URL
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $payment_method = $_GET['payment_method'] ?? '';
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = intval($_GET['limit'] ?? 10);
    $offset = ($page - 1) * $limit;
    
    // Xây dựng câu lệnh SQL
    $sql = "SELECT * FROM `order` WHERE 1=1";
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (order_id LIKE ? OR shipping_address LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= "ss";
    }
    
    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= "s";
    }
    
    if (!empty($payment_method)) {
        $sql .= " AND payment_method = ?";
        $params[] = $payment_method;
        $types .= "s";
    }
    
    // Đếm tổng số đơn hàng
    $countSql = "SELECT COUNT(*) as total FROM ($sql) as count_table";
    $stmt = $conn->prepare($countSql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $totalOrders = $stmt->get_result()->fetch_assoc()['total'];
    
    // Lấy danh sách đơn hàng
    $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $types .= "ii";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $orders = $stmt->get_result();
    
    // Tính tổng số trang
    $totalPages = ceil($totalOrders / $limit);
    
    return [
        'orders' => $orders,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalOrders' => $totalOrders
    ];
}

/**
 * Xử lý xoá đơn hàng nếu có yêu cầu từ URL
 *
 * @param mysqli $conn Kết nối CSDL
 */
function processDeleteOrder($conn) {
    if (isset($_GET['delete_order'])) {
        $order_id = $_GET['delete_order'];
        $orderModel = new OrderModel($conn);
        
        try {
            // Xóa chi tiết đơn hàng trước
            $sql = "DELETE FROM order_detail WHERE order_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $order_id);
            $stmt->execute();
            
            // Xóa đơn hàng
            $sql = "DELETE FROM `order` WHERE order_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $order_id);
            
            if ($stmt->execute()) {
                header("Location: index.php?success=1");
                exit;
            } else {
                throw new Exception("Lỗi khi xóa đơn hàng");
            }
        } catch (Exception $e) {
            header("Location: index.php?error=" . urlencode($e->getMessage()));
            exit;
        }
    }
}

// Bạn có thể mở rộng thêm các hàm xử lý khác như: processEditOrder(), processAddOrder()...