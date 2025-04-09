<?php
// File: admin/controller/brandController.php
require_once __DIR__ . '/../model/BrandModel.php';
require_once __DIR__ . '/stringHelper.php';

function processAddBrand($conn) {
    $errors = [];
    $model = new BrandModel($conn);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brandName = trim($_POST['brand_name'] ?? '');
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        if (empty($brandName)) {
            $errors['brand_name'] = "Tên thương hiệu không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $brandName)) {
            $errors['brand_name'] = "Tên thương hiệu không được chứa ký tự đặc biệt.";
        } elseif ($model->isBrandNameExists($brandName)) {
            $errors['brand_name'] = "Tên thương hiệu đã tồn tại.";
        }

        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        $imageUrl = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = __DIR__ . '/../uploads/brands/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
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
            if ($model->addBrand($brandName, $status, $imageUrl)) {
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
    $model = new BrandModel($conn);
    $brand = $model->getBrandById($brand_id);

    if (!$brand) {
        header("Location: index.php?msg=Thương hiệu không tồn tại&type=danger");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brandName = trim($_POST['brand_name'] ?? '');
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        if (empty($brandName)) {
            $errors['brand_name'] = "Tên thương hiệu không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $brandName)) {
            $errors['brand_name'] = "Tên thương hiệu không được chứa ký tự đặc biệt.";
        } elseif ($model->isBrandNameExists($brandName, $brand_id)) {
            $errors['brand_name'] = "Tên thương hiệu đã tồn tại.";
        }

        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        $imageUrl = $brand['image_url'];
        if (!empty($_FILES['image']['name'])) {
            $targetDir = __DIR__ . '/../uploads/brands/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $safeName  = safeString($brandName);
            $filename  = 'brand_' . $safeName . '_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath  = $targetDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                if (!empty($brand['image_url']) && file_exists(__DIR__ . '/../../' . $brand['image_url'])) {
                    unlink(__DIR__ . '/../../' . $brand['image_url']);
                }
                $imageUrl = 'admin/uploads/brands/' . $filename;
            } else {
                $errors['image'] = "Upload ảnh không thành công.";
            }
        }

        if (empty($errors)) {
            if ($model->updateBrand($brand_id, $brandName, $status, $imageUrl)) {
                header("Location: index.php?msg=Cập nhật thương hiệu thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật thương hiệu thất bại.";
            }
        }
    }

    return [$errors, $brand];
}

function processDeleteBrand($conn, $brand_id) {
    $model = new BrandModel($conn);
    if ($model->deleteBrand($brand_id)) {
        header("Location: index.php?msg=Xóa thương hiệu thành công!&type=success");
    } else {
        header("Location: index.php?msg=Xóa thương hiệu thất bại.&type=danger");
    }
    exit;
}

function getBrandsWithPagination($conn, $page, $limit, $search) {
    $model = new BrandModel($conn);
    return $model->getBrandsWithPagination($page, $limit, $search);
}