<?php
// File: admin/controller/orderController.php

require_once '../../model/invoiceModel.php';

/**
 * Lấy danh sách đơn hàng theo các filter từ URL
 *
 * @param mysqli $conn Kết nối CSDL
 * @return array Dữ liệu đơn hàng bao gồm: orders, totalPages, currentPage, totalOrders
 */
function getFilteredOrders($conn) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $search = isset($_GET['search']) ? trim($_GET['search']) : "";
    $status = isset($_GET['status']) ? trim($_GET['status']) : "";
    $paymentMethod = isset($_GET['payment_method']) ? trim($_GET['payment_method']) : "";

    return getOrdersWithPagination($conn, $page, $limit, $search, $status, $paymentMethod);
}

/**
 * Xử lý xoá đơn hàng nếu có yêu cầu từ URL
 *
 * @param mysqli $conn Kết nối CSDL
 */
function processDeleteOrder($conn) {
    if (isset($_GET['delete_order']) && !empty($_GET['delete_order'])) {
        $order_id = $_GET['delete_order'];
        if (deleteOrder($conn, $order_id)) {
            header("Location: index.php?msg=Xóa đơn hàng thành công!");
            exit;
        } else {
            header("Location: index.php?msg=Không thể xóa đơn hàng!");
            exit;
        }
    }
}

// Bạn có thể mở rộng thêm các hàm xử lý khác như: processEditOrder(), processAddOrder()...