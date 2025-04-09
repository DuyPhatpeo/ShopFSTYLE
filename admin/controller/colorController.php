<?php
require_once __DIR__ . '/../model/colorModel.php';

function processAddColor($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $colorName = trim($_POST['color_name'] ?? '');
        $colorCode = trim($_POST['color_code'] ?? '');
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        if (empty($colorName)) {
            $errors['color_name'] = "Tên màu không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $colorName)) {
            $errors['color_name'] = "Tên màu không được chứa ký tự đặc biệt.";
        } elseif (isColorNameExists($conn, $colorName)) {
            $errors['color_name'] = "Tên màu đã tồn tại.";
        }

        if (empty($colorCode)) {
            $errors['color_code'] = "Mã màu không được để trống.";
        } elseif (!preg_match("/^#[0-9A-Fa-f]{6}(,\s*#[0-9A-Fa-f]{6})?$/", $colorCode)) {
            $errors['color_code'] = "Mã màu không hợp lệ. Ví dụ: #FFFFFF hoặc #FF0000, #00FF00";
        }

        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        if (empty($errors)) {
            if (addColor($conn, $colorName, $colorCode, $status)) {
                header("Location: index.php?msg=Thêm màu thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm màu thất bại.";
            }
        }
    }
    return $errors;
}

function processEditColor($conn, $color_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $colorName = trim($_POST['color_name'] ?? '');
        $colorCode = trim($_POST['color_code'] ?? '');
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        if (empty($colorName)) {
            $errors['color_name'] = "Tên màu không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $colorName)) {
            $errors['color_name'] = "Tên màu không được chứa ký tự đặc biệt.";
        } elseif (isColorNameExists($conn, $colorName, $color_id)) {
            $errors['color_name'] = "Tên màu đã tồn tại.";
        }

        if (empty($colorCode)) {
            $errors['color_code'] = "Mã màu không được để trống.";
        } elseif (!preg_match("/^#[0-9A-Fa-f]{6}(,\s*#[0-9A-Fa-f]{6})?$/", $colorCode)) {
            $errors['color_code'] = "Mã màu không hợp lệ. Ví dụ: #FFFFFF hoặc #FF0000, #00FF00";
        }

        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        if (empty($errors)) {
            if (updateColor($conn, $color_id, $colorName, $colorCode, $status)) {
                header("Location: index.php?msg=Cập nhật màu thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật màu thất bại.";
            }
        }
    }
    return $errors;
}

function processDeleteColor($conn, $color_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteColor($conn, $color_id)) {
            header("Location: index.php?msg=Xóa màu thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa màu thất bại.";
        }
    }
    return $errors;
}
?>