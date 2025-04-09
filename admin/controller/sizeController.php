<?php
require_once 'models/sizeModel.php';

function processAddSize($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sizeName = trim($_POST['size_name'] ?? '');

        if (empty($sizeName)) {
            $errors['size_name'] = "Tên size không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $sizeName)) {
            $errors['size_name'] = "Tên size không được chứa ký tự đặc biệt.";
        } elseif (isSizeNameExists($conn, $sizeName)) {
            $errors['size_name'] = "Tên size đã tồn tại.";
        }

        if (empty($errors)) {
            if (addSize($conn, $sizeName)) {
                header("Location: index.php?msg=Thêm size thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm size thất bại.";
            }
        }
    }
    return $errors;
}

function processDeleteSize($conn, $size_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteSize($conn, $size_id)) {
            header("Location: index.php?msg=Xóa size thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa size thất bại.";
        }
    }
    return $errors;
}