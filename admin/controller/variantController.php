<?php
// File: admin/controller/variantController.php

require_once __DIR__ . '/../model/variantModel.php';
require_once __DIR__ . '/../model/colorModel.php';
require_once __DIR__ . '/../model/sizeModel.php';

/**
 * Xử lý các hành động trên biến thể từ form.
 *
 * Các hành động được xác định qua trường hidden 'action' trong form:
 * - 'add_quantity': Thêm số lượng cho biến thể.
 * - 'delete_variant': Xoá biến thể.
 *
 * Sau khi xử lý, chuyển hướng lại trang hiện tại.
 *
 * @param mysqli $conn Kết nối CSDL.
 */
function handleVariantActions($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Xử lý thêm số lượng cho biến thể
        if (isset($_POST['action']) && $_POST['action'] === 'add_quantity') {
            $variant_id      = trim($_POST['variant_id']);
            $quantity_to_add = max(0, intval($_POST['quantity_to_add']));
            
            // Lấy thông tin biến thể hiện tại
            $variant = getVariantById($conn, $variant_id);
            if ($variant) {
                $newQuantity = $variant['quantity'] + $quantity_to_add;
                if (updateVariantQuantity($conn, $variant_id, $newQuantity)) {
                    $_SESSION['success'] = "Thêm số lượng thành công.";
                } else {
                    $_SESSION['error'] = "Cập nhật số lượng thất bại.";
                }
            } else {
                $_SESSION['error'] = "Không tìm thấy biến thể.";
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }

        // Xử lý xoá biến thể
        if (isset($_POST['action']) && $_POST['action'] === 'delete_variant') {
            $variant_id = trim($_POST['variant_id']);
            if (deleteVariant($conn, $variant_id)) {
                $_SESSION['success'] = "Đã xoá biến thể thành công.";
            } else {
                $_SESSION['error'] = "Xoá biến thể thất bại.";
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}
?>