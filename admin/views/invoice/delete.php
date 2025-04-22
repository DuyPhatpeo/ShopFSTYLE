<?php
require_once('../../../model/orderModel.php');
require_once('../../../includes/db.php');
// Lấy order_id từ URL
$order_id = $_GET['delete_order'] ?? '';

// Kiểm tra dữ liệu
if (empty($order_id)) {
    header("Location: index.php?error=Thiếu thông tin cần thiết");
    exit;
}

// Xóa đơn hàng
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