<?php
// File: admin/controller/categoryController.php

require_once __DIR__ . '/../model/categoryModel.php';

function processAddCategory($conn) {
    $errors = [];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['category_name'] ?? '');
        $parent = $_POST['parent_id'] ?? null;
        $status = (int)($_POST['status'] ?? 1);

        // Kiểm tra tên danh mục
        if (empty($name)) {
            $errors['category_name'] = "Tên danh mục không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $name)) {
            $errors['category_name'] = "Tên danh mục không hợp lệ.";
        } elseif (isCategoryNameExists($conn, $name)) {
            $errors['category_name'] = "Tên danh mục đã tồn tại.";
        }

        // Kiểm tra trạng thái
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        // Xử lý ảnh nếu có
        $imageUrl = handleCategoryImageUpload($conn);

        // Nếu không có lỗi, tiến hành thêm danh mục
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

        // Kiểm tra tên danh mục
        if (empty($name)) {
            $errors['category_name'] = "Tên danh mục không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $name)) {
            $errors['category_name'] = "Tên danh mục không hợp lệ.";
        } elseif (isCategoryNameExists($conn, $name, $category_id)) {
            $errors['category_name'] = "Tên danh mục đã tồn tại.";
        }

        // Lấy ảnh hiện tại nếu có
        $current = getCategoryById($conn, $category_id);
        $imageUrl = $current['image_url'] ?? null;

        // Xử lý ảnh nếu có
        $imageUrl = handleCategoryImageUpload($conn, $imageUrl);

        // Nếu không có lỗi, tiến hành cập nhật danh mục
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

function handleCategoryImageUpload($conn, $currentImageUrl = null) {
    if (!empty($_FILES['image']['name'])) {
        $folder = __DIR__ . '/../uploads/categories/';
        if (!is_dir($folder)) mkdir($folder, 0755, true);

        // Lấy tên danh mục và tạo slug từ đó
        $categoryName = trim($_POST['category_name'] ?? '');
        $slug = createSlug($categoryName); // Tạo slug từ tên danh mục
        
        // Tạo tên file ảnh mới, thêm slug vào tên file
        $filename = 'category-' . $slug . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        
        // Nếu có ảnh cũ và ảnh cũ tồn tại, xóa ảnh cũ
        if ($currentImageUrl && file_exists(__DIR__ . '/../../' . $currentImageUrl)) {
            unlink(__DIR__ . '/../../' . $currentImageUrl);
        }

        // Di chuyển ảnh mới vào thư mục
        if (move_uploaded_file($_FILES['image']['tmp_name'], $folder . $filename)) {
            return 'admin/uploads/categories/' . $filename;
        } else {
            throw new Exception("Tải ảnh thất bại.");
        }
    }

    // Nếu không có ảnh mới, giữ lại ảnh cũ
    return $currentImageUrl;
}


?>