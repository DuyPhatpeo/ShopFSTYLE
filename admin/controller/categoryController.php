<?php
// File: admin/controller/categoryController.php

require_once __DIR__ . '/../model/categoryModel.php';

function processAddCategory($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['category_name'] ?? '');
        $parent = $_POST['parent_id'] ?? null;
        $status = (int)($_POST['status'] ?? 1);

        if (empty($name)) {
            $errors['category_name'] = "Tên danh mục không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $name)) {
            $errors['category_name'] = "Tên danh mục không hợp lệ.";
        } elseif (isCategoryNameExists($conn, $name)) {
            $errors['category_name'] = "Tên danh mục đã tồn tại.";
        }

        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        $imageUrl = null;
        if (empty($errors) && !empty($_FILES['image']['name'])) {
            $folder = __DIR__ . '/../uploads/categories/';
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            $filename = 'category_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $folder . $filename)) {
                $imageUrl = 'admin/uploads/categories/' . $filename;
            } else {
                $errors['image'] = "Tải ảnh thất bại.";
            }
        }

        if (empty($errors) && addCategoryWithImage($conn, $name, $parent, $status, $imageUrl)) {
            header("Location: index.php?msg=Thêm danh mục thành công&type=success");
            exit;
        }
    }
    return $errors;
}

function processEditCategory($conn, $category_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['category_name'] ?? '');
        $parent = $_POST['parent_id'] ?? null;
        $status = (int)($_POST['status'] ?? 1);

        if (empty($name)) {
            $errors['category_name'] = "Tên danh mục không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $name)) {
            $errors['category_name'] = "Tên danh mục không hợp lệ.";
        } elseif (isCategoryNameExists($conn, $name, $category_id)) {
            $errors['category_name'] = "Tên danh mục đã tồn tại.";
        }

        $current = getCategoryById($conn, $category_id);
        $imageUrl = $current['image_url'] ?? null;

        if (empty($errors) && !empty($_FILES['image']['name'])) {
            if (!empty($imageUrl) && file_exists(__DIR__ . '/../../' . $imageUrl)) {
                unlink(__DIR__ . '/../../' . $imageUrl);
            }
            $folder = __DIR__ . '/../uploads/categories/';
            if (!is_dir($folder)) mkdir($folder, 0755, true);
            $filename = 'category_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $folder . $filename)) {
                $imageUrl = 'admin/uploads/categories/' . $filename;
            } else {
                $errors['image'] = "Tải ảnh thất bại.";
            }
        }

        if (empty($errors) && updateCategory($conn, $category_id, $name, $parent, $status, $imageUrl)) {
            header("Location: index.php?msg=Cập nhật thành công&type=success");
            exit;
        }
    }
    return $errors;
}

function processDeleteCategory($conn, $category_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteCategory($conn, $category_id)) {
            header("Location: index.php?msg=Xóa danh mục thành công&type=success");
            exit;
        }
    }
    return ['general' => 'Xóa danh mục thất bại.'];
}