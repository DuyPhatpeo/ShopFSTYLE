<?php
// File: admin/controller/variantController.php

require_once __DIR__ . '/../model/variantModel.php';
require_once __DIR__ . '/../model/colorModel.php';
require_once __DIR__ . '/../model/sizeModel.php';
require_once __DIR__ . '/../model/productModel.php'; // Nếu cần dùng hàm getProductById()
require_once __DIR__ . '/stringHelper.php'; // Nếu cần dùng hàm safeString() hoặc generateUCCID()

/**
 * Xử lý các hành động trên biến thể từ form.
 *
 * Các hành động được xác định qua trường hidden 'action' trong form:
 * - 'add_quantity': Thêm số lượng cho biến thể.
 * - 'delete_variant': Xoá biến thể.
 * - 'add_variant': Thêm biến thể mới.
 *
 * Sau khi xử lý, chuyển hướng lại trang hiện tại.
 *
 * @param mysqli $conn Kết nối CSDL.
 */
function handleVariantActions($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Thêm số lượng cho biến thể
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

        // Xoá biến thể
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

/**
 * Xử lý thêm biến thể.
 */
function processAddVariant($conn) {
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_id = trim($_POST['product_id'] ?? '');
        $variants = $_POST['variants'] ?? [];

        // Kiểm tra mã sản phẩm
        if (empty($product_id)) {
            $errors['product'] = "Thiếu mã sản phẩm.";
        }

        // Kiểm tra nếu không có biến thể nào được gửi
        if (empty($variants)) {
            $errors['variant'] = "Chưa có biến thể nào được tạo.";
        }

        // Duyệt qua từng biến thể
        foreach ($variants as $key => $variant) {
            $parts = explode('_', $key);
            $color_id = $parts[0] ?? null;
            $size_id  = $parts[1] ?? null;
            $quantity = intval($variant['quantity'] ?? 0);

            // Kiểm tra color_id (bắt buộc)
            if (empty($color_id)) {
                $errors['color_' . $key] = "Vui lòng chọn màu sắc.";
            }

            // Kiểm tra số lượng
            if ($quantity < 1) {
                $errors['quantity_' . $key] = "Số lượng không hợp lệ.";
            }

            // Nếu size_id rỗng, gán NULL
            if ($size_id === '') {
                $size_id = NULL;
            }

            // Kiểm tra xem biến thể đã tồn tại chưa
            if (!empty($product_id) && !empty($color_id) && isVariantExists($conn, $product_id, $color_id, $size_id)) {
                $errors['variant_' . $key] = "Biến thể màu $color_id và size " . ($size_id ?? 'Mặc định') . " đã tồn tại.";
            }

            // Nếu không có lỗi với biến thể này thì thêm
            if (empty($errors)) {
                $status = ($quantity > 0) ? 1 : 0;
                if (!addVariant($conn, $product_id, $color_id, $size_id, $quantity, $status)) {
                    $errors['add_' . $key] = "Không thể thêm biến thể màu $color_id, size " . ($size_id ?? 'Mặc định');
                }
            }
        }

        // Nếu không có lỗi nào, chuyển hướng về trang chi tiết sản phẩm
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
                $currentQuantity = intval($variant['quantity']);
                $newQuantity = $currentQuantity + $add_quantity;
                $newStatus = ($newQuantity === 0) ? 0 : 1;

                $stmt = $conn->prepare("
                    UPDATE product_variants 
                    SET quantity = ?, status = ? 
                    WHERE variant_id = ?
                ");
                if ($stmt === false) {
                    die("Lỗi chuẩn bị câu lệnh SQL: " . $conn->error);
                }
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