<?php
// File: admin/controller/brandController.php

require_once __DIR__ . '/../model/brandModel.php';

/**
 * Xử lý thêm thương hiệu mới thông qua form.
 */
function processAddBrand($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brandName = trim($_POST['brand_name'] ?? '');
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        if (empty($brandName)) {
            $errors['brand_name'] = "Tên thương hiệu không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $brandName)) {
            $errors['brand_name'] = "Tên thương hiệu không được chứa ký tự đặc biệt.";
        } elseif (isBrandNameExists($conn, $brandName)) {
            $errors['brand_name'] = "Tên thương hiệu đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        $imageUrl = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = __DIR__ . '/../uploads/brands/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $safeName  = safeString($brandName);
            $filename  = 'brand_' . $safeName . '_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath  = $targetDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = 'admin/uploads/brands/' . $filename;
            } else {
                $errors['image'] = "Upload ảnh không thành công.";
            }
        }

        if (empty($errors)) {
            if (addBrandWithImage($conn, $brandName, $status, $imageUrl)) {
                header("Location: index.php?msg=Thêm thương hiệu thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm thương hiệu thất bại.";
            }
        }
    }
    return $errors;
}

function processEditBrand($conn, $brand_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brandName = trim($_POST['brand_name'] ?? '');
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        if (empty($brandName)) {
            $errors['brand_name'] = "Tên thương hiệu không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $brandName)) {
            $errors['brand_name'] = "Tên thương hiệu không được chứa ký tự đặc biệt.";
        } elseif (isBrandNameExists($conn, $brandName, $brand_id)) {
            $errors['brand_name'] = "Tên thương hiệu đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        $imageUrl = null;
        if (!empty($_FILES['image']['name'])) {
            $currentBrand = getBrandById($conn, $brand_id);
            if ($currentBrand && !empty($currentBrand['image_url'])) {
                $oldImagePath = __DIR__ . '/../../' . $currentBrand['image_url'];
                if (file_exists($oldImagePath)) unlink($oldImagePath);
            }
            $targetDir = __DIR__ . '/../uploads/brands/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $safeName  = safeString($brandName);
            $filename  = 'brand_' . $safeName . '_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath  = $targetDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = 'admin/uploads/brands/' . $filename;
            } else {
                $errors['image'] = "Upload ảnh mới không thành công.";
            }
        } else {
            $currentBrand = getBrandById($conn, $brand_id);
            $imageUrl = $currentBrand['image_url'] ?? null;
        }

        if (empty($errors)) {
            if (updateBrand($conn, $brandName, $status, $imageUrl, $brand_id)) {
                header("Location: index.php?msg=Cập nhật thương hiệu thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật thương hiệu thất bại.";
            }
        }
    }
    return $errors;
}

function processDeleteBrand($conn, $brand_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteBrand($conn, $brand_id)) {
            header("Location: index.php?msg=Xóa thương hiệu thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa thương hiệu thất bại.";
        }
    }
    return $errors;
}