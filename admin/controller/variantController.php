<?php
// File: admin/controller/variantController.php

require_once __DIR__ . '/../model/variantModel.php';
require_once __DIR__ . '/../model/productModel.php'; // Nếu cần dùng hàm getProductById()
require_once __DIR__ . '/../model/colorModel.php';
require_once __DIR__ . '/../model/sizeModel.php';
require_once __DIR__ . '/stringHelper.php'; // Nếu cần dùng hàm safeString() hoặc generateUCCID()

/**
 * Xử lý thêm biến thể.
 */
function processAddVariant($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = trim($_POST['product_id'] ?? '');
        $variants = $_POST['variants'] ?? [];

        if (empty($product_id)) {
            $errors['product'] = "Thiếu mã sản phẩm.";
        }

        // Kiểm tra nếu không có biến thể nào được gửi
        if (empty($variants)) {
            $errors['variant'] = "Chưa có biến thể nào được tạo.";
        }

        // Duyệt qua từng biến thể để kiểm tra và thêm
        foreach ($variants as $key => $variant) {
            // key có dạng "colorId_sizeId" hoặc "colorId_"
            $parts = explode('_', $key);
            $color_id = $parts[0] ?? null;
            $size_id = $parts[1] ?? null;
            $quantity = intval($variant['quantity'] ?? 0);

            if (empty($color_id)) {
                $errors['color_' . $key] = "Vui lòng chọn màu sắc.";
            }

            if ($quantity < 1) {
                $errors['quantity_' . $key] = "Số lượng không hợp lệ.";
            }

            if ($size_id === '') {
                $size_id = NULL;
            }

            if (isVariantExists($conn, $product_id, $color_id, $size_id)) {
                $errors['variant_' . $key] = "Biến thể màu $color_id và size $size_id đã tồn tại.";
            }

            // Nếu không có lỗi cho biến thể này thì thêm
            if (empty($errors)) {
                $status = ($quantity > 0) ? 1 : 0;
                addVariant($conn, $product_id, $color_id, $size_id, $quantity, $status);
            }
        }

        if (empty($errors)) {
            header("Location: detail.php?id=" . urlencode($product_id) . "&msg=Thêm biến thể thành công&type=success");
            exit;
        }
    }

    return $errors;
}






/**
 * Xử lý xoá biến thể.
 */
function processDeleteVariant($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['variant_id'], $_GET['product_id'])) {
        $variant_id = trim($_GET['variant_id']);
        $product_id = trim($_GET['product_id']);
        if (deleteVariant($conn, $variant_id)) {
            header("Location: productDetail.php?id=" . urlencode($product_id) . "&msg=Xoá biến thể thành công&type=success");
            exit;
        }
    }
    return ['general' => 'Xoá biến thể thất bại.'];
}


/**
 * Xử lý cập nhật số lượng cho biến thể thông qua form trên trang chi tiết sản phẩm.
 */
function processVariantQuantity($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $variant_id   = trim($_POST['variant_id'] ?? '');
        $product_id   = trim($_POST['product_id'] ?? '');
        $add_quantity = intval($_POST['add_quantity'] ?? 0);
        
        if ($variant_id && $add_quantity >= 0) {
            $variant = getVariantById($conn, $variant_id);
            if ($variant) {
                // Cộng thêm số lượng vào số lượng hiện có
                $currentQuantity = intval($variant['quantity']);
                $newQuantity = $currentQuantity + $add_quantity;

                // Xác định trạng thái: nếu số lượng mới bằng 0, thì status = 0 (Hết hàng), ngược lại là 1 (Còn hàng)
                $newStatus = ($newQuantity === 0) ? 0 : 1;

                // Chuẩn bị truy vấn cập nhật số lượng và trạng thái
                $stmt = $conn->prepare("
                    UPDATE product_variants 
                    SET quantity = ?, status = ? 
                    WHERE variant_id = ?
                ");
                if ($stmt === false) {
                    die("Lỗi chuẩn bị câu lệnh SQL: " . $conn->error);
                }
                // Gắn các tham số: quantity mới (int), status (int) và variant_id (string)
                $stmt->bind_param("iis", $newQuantity, $newStatus, $variant_id);
                if ($stmt->execute()) {
                    $stmt->close();
                    header("Location: detail.php?id=" . urlencode($product_id) . "&msg=Cập nhật số lượng thành công&type=success");
                    exit;
                }
                $stmt->close();
            }
        }
    }
    header("Location: detail.php?id=" . urlencode($_POST['product_id'] ?? '') . "&msg=Không thể cập nhật số lượng&type=danger");
    exit;
}

?>