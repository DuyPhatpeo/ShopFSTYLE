<?php
// File: admin/controller/promotionController.php
require_once __DIR__ . '/../model/promotionModel.php';

function processAddPromotion($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $promotionName  = trim($_POST['promotion_name'] ?? '');
        $promotionCode  = trim($_POST['promotion_code'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $discountValue  = isset($_POST['discount_value']) ? (float)$_POST['discount_value'] : 0;
        $startDate      = $_POST['start_date'] ?? '';
        $endDate        = $_POST['end_date'] ?? '';

        if (empty($promotionName)) {
            $errors['promotion_name'] = "Tên khuyến mãi không được để trống.";
        } elseif (isPromotionNameExists($conn, $promotionName)) {
            $errors['promotion_name'] = "Tên khuyến mãi đã tồn tại.";
        }

        if (empty($promotionCode)) {
            $errors['promotion_code'] = "Mã khuyến mãi không được để trống.";
        } elseif (isPromotionCodeExists($conn, $promotionCode)) {
            $errors['promotion_code'] = "Mã khuyến mãi đã tồn tại.";
        }

        if ($discountValue < 0) {
            $errors['discount_value'] = "Giá trị giảm giá không hợp lệ.";
        }

        if (!empty($startDate) && !empty($endDate) && strtotime($startDate) > strtotime($endDate)) {
            $errors['date_range'] = "Ngày bắt đầu phải trước hoặc bằng ngày kết thúc.";
        }

        if (empty($errors)) {
            if (addPromotion($conn, $promotionName, $promotionCode, $description, $discountValue, $startDate, $endDate)) {
                header("Location: index.php?msg=Thêm khuyến mãi thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm khuyến mãi thất bại.";
            }
        }
    }
    return $errors;
}

function processEditPromotion($conn, $promotion_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $promotionName  = trim($_POST['promotion_name'] ?? '');
        $promotionCode  = trim($_POST['promotion_code'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $discountValue  = isset($_POST['discount_value']) ? (float)$_POST['discount_value'] : 0;
        $startDate      = $_POST['start_date'] ?? '';
        $endDate        = $_POST['end_date'] ?? '';

        if (empty($promotionName)) {
            $errors['promotion_name'] = "Tên khuyến mãi không được để trống.";
        } elseif (isPromotionNameExists($conn, $promotionName, $promotion_id)) {
            $errors['promotion_name'] = "Tên khuyến mãi đã tồn tại.";
        }

        if (empty($promotionCode)) {
            $errors['promotion_code'] = "Mã khuyến mãi không được để trống.";
        } elseif (isPromotionCodeExists($conn, $promotionCode, $promotion_id)) {
            $errors['promotion_code'] = "Mã khuyến mãi đã tồn tại.";
        }

        if ($discountValue < 0) {
            $errors['discount_value'] = "Giá trị giảm giá không hợp lệ.";
        }

        if (!empty($startDate) && !empty($endDate) && strtotime($startDate) > strtotime($endDate)) {
            $errors['date_range'] = "Ngày bắt đầu phải trước hoặc bằng ngày kết thúc.";
        }

        if (empty($errors)) {
            if (updatePromotion($conn, $promotion_id, $promotionName, $promotionCode, $description, $discountValue, $startDate, $endDate)) {
                header("Location: index.php?msg=Cập nhật khuyến mãi thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật khuyến mãi thất bại.";
            }
        }
    }
    return $errors;
}

function processDeletePromotion($conn, $promotion_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deletePromotion($conn, $promotion_id)) {
            header("Location: index.php?msg=Xóa khuyến mãi thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa khuyến mãi thất bại.";
        }
    }
    return $errors;
}