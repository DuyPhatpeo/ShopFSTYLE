<?php
// File: admin/controller/productController.php

require_once __DIR__ . '/../model/productModel.php';
require_once __DIR__ . '/../model/brandModel.php';
require_once __DIR__ . '/../model/categoryModel.php';
require_once __DIR__ . '/stringHelper.php'; // Gồm removeAccents() và safeString()

/**
 * Hàm xử lý upload ảnh chính cho sản phẩm.
 * Ảnh sẽ được lưu với tên: {safe(product_name)}-main.{ext}
 */
function uploadMainImage($product_name, $fileData) {
    if (!empty($fileData['name'])) {
        $folder = __DIR__ . '/../uploads/products/';
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        $ext = pathinfo($fileData['name'], PATHINFO_EXTENSION);
        // Tạo tên file an toàn dựa trên product_name
        $safeName = safeString($product_name);
        $filename = "{$safeName}-main." . $ext;
        $target = $folder . $filename;
        if (move_uploaded_file($fileData['tmp_name'], $target)) {
            return 'admin/uploads/products/' . $filename;
        }
    }
    return null;
}

/**
 * Xử lý thêm sản phẩm mới.
 * Lấy dữ liệu từ form POST gồm: product_name, description, original_price, discount_price, brand_id, category_id, status.
 * Ảnh chính được upload theo tên: {safe(product_name)}-main.{ext}
 * Khi thành công, chuyển hướng về trang danh sách sản phẩm.
 */
/**
 * Xử lý thêm sản phẩm mới.
 * Lấy dữ liệu từ form POST gồm: product_name, description, original_price, discount_price, brand_id, category_id, status.
 * Ảnh chính được upload theo tên: {safe(product_name)}-main.{ext}
 * Khi thành công, chuyển hướng về trang thêm biến thể.
 */
function processAddProduct($conn) {
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_name   = trim($_POST['product_name'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $original_price = floatval($_POST['original_price'] ?? 0);
        $discount_price = floatval($_POST['discount_price'] ?? 0);
        $brand_id       = $_POST['brand_id'] ?? '';
        $category_id    = $_POST['category_id'] ?? '';
        $status         = (int)($_POST['status'] ?? 1);

        // Validate
        if (empty($product_name)) {
            $errors['product_name'] = "Tên sản phẩm không được để trống.";
        }
        if (!empty($product_name) && isProductNameExists($conn, $product_name)) {
            $errors['product_name'] = "Tên sản phẩm đã tồn tại.";
        }
        if ($original_price <= 0) {
            $errors['original_price'] = "Giá gốc phải lớn hơn 0.";
        }
        if ($discount_price < 0 || $discount_price > $original_price) {
            $errors['discount_price'] = "Giá giảm không hợp lệ.";
        }
        if (empty($brand_id)) {
            $errors['brand_id'] = "Vui lòng chọn thương hiệu.";
        }
        if (empty($category_id)) {
            $errors['category_id'] = "Vui lòng chọn danh mục.";
        }
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        // // Upload ảnh chính nếu có
        // $main_image = null;
        // if (empty($errors) && !empty($_FILES['main_image']['name'])) {
        //     $main_image = uploadMainImage($product_name, $_FILES['main_image']);
        //     if (!$main_image) {
        //         $errors['main_image'] = "Tải ảnh thất bại.";
        //     }
        // }

        // Nếu không có lỗi
        if (empty($errors)) {
            $product_id = addProduct($conn, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status);

            if ($product_id) {
                // Xử lý upload nhiều ảnh phụ
                if (isset($_FILES['content-imgs']) && is_array($_FILES['content-imgs']['name'])) {
                    $total = count($_FILES['content-imgs']['name']);
                    $targetDir = "../../uploads/products/";
                    for ($i = 0; $i < $total; $i++) {
                        if ($_FILES['content-imgs']['error'][$i] == 0) {
                            $file = [
                                'name' => $_FILES['content-imgs']['name'][$i],
                                'type' => $_FILES['content-imgs']['type'][$i],
                                'tmp_name' => $_FILES['content-imgs']['tmp_name'][$i],
                                'error' => $_FILES['content-imgs']['error'][$i],
                                'size' => $_FILES['content-imgs']['size'][$i],
                            ];
                            $uploadResult = uploadImage($file, $targetDir, uniqid('img_'));
                            
                            if ($uploadResult['success']) {
                                $image_url = $uploadResult['filename'];
                                $position = $i + 1;
                                $is_main = 0;
                                $status_img = 1;

                                addImage($conn, $product_id, $image_url, $position, $status_img, $is_main);
                            }
                        }
                    }
                }

                header("Location: index.php?msg=Thêm sản phẩm thành công&type=success");
                exit;
            }
        }
    }

    return $errors;
}

function uploadImage($file, $targetDir, $id)
{
    $result = ['success' => false, 'message' => '', 'filename' => ''];

    if (isset($file) && $file['error'] == 0) {
        $originalFileName = basename($file["name"]);
        $fileTmpName = $file["tmp_name"];
        $fileSize = $file["size"];
        $fileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($fileType, $allowedTypes)) {
            $result['message'] = "Chỉ các định dạng JPG, JPEG, PNG và GIF được chấp nhận.";
            return $result;
        }
        $finalFileName = $id . '.' . $fileType;
        $filePath = $targetDir . $finalFileName;

        if (move_uploaded_file($fileTmpName, $filePath)) {
            $result['success'] = true;
            $result['filename'] = $finalFileName;
        } else {
            $result['message'] = "Có lỗi xảy ra khi tải tệp lên.";
        }
    } else {
        $result['message'] = "Không có tệp nào được tải lên hoặc có lỗi trong quá trình tải.";
    }

    return $result;
}

/**
 * Xử lý cập nhật thông tin sản phẩm.
 * Nếu có upload ảnh mới, sẽ xoá ảnh cũ và cập nhật ảnh mới.
 */
function processEditProduct($conn, $product_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $product_name   = trim($_POST['product_name'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $original_price = floatval($_POST['original_price'] ?? 0);
        $discount_price = floatval($_POST['discount_price'] ?? 0);
        $brand_id       = $_POST['brand_id'] ?? '';
        $category_id    = $_POST['category_id'] ?? '';
        $status         = (int)($_POST['status'] ?? 1);

        // Validate dữ liệu
        if (empty($product_name)) {
            $errors['product_name'] = "Tên sản phẩm không được để trống.";
        }
        // Sau dòng: if (empty($product_name)) { ... }
        if (!empty($product_name) && isProductNameExists($conn, $product_name, $product_id)) {
            $errors['product_name'] = "Tên sản phẩm đã tồn tại.";
        }
        if ($original_price <= 0) {
            $errors['original_price'] = "Giá gốc phải lớn hơn 0.";
        }
        if ($discount_price < 0 || $discount_price > $original_price) {
            $errors['discount_price'] = "Giá giảm không hợp lệ.";
        }
        if (empty($brand_id)) {
            $errors['brand_id'] = "Vui lòng chọn thương hiệu.";
        }
        if (empty($category_id)) {
            $errors['category_id'] = "Vui lòng chọn danh mục.";
        }
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }
        
        // Lấy thông tin sản phẩm hiện có
        $product = getProductById($conn, $product_id);
        $main_image = $product['main_image'] ?? null;
        
        // Nếu có upload ảnh mới, xử lý thay thế ảnh cũ
        if (empty($errors) && !empty($_FILES['main_image']['name'])) {
            if (!empty($main_image) && file_exists(__DIR__ . '/../../' . $main_image)) {
                unlink(__DIR__ . '/../../' . $main_image);
            }
            $new_image = uploadMainImage($product_name, $_FILES['main_image']);
            if ($new_image) {
                $main_image = $new_image;
            } else {
                $errors['main_image'] = "Tải ảnh thất bại.";
            }
        }
        
        if (empty($errors)) {
            if (updateProduct($conn, $product_id, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image)) {
                header("Location: index.php?msg=Cập nhật sản phẩm thành công&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật sản phẩm thất bại, vui lòng thử lại.";
            }
        }
    }
    return $errors;
}

/**
 * Xử lý xóa sản phẩm, bao gồm xoá ảnh vật lý nếu có.
 */
function processDeleteProduct($conn, $product_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteProduct($conn, $product_id)) {
            header("Location: index.php?msg=Xóa sản phẩm thành công&type=success");
            exit;
        }
    }
    return ['general' => 'Xóa sản phẩm thất bại.'];
}

/**
 * Lấy chi tiết sản phẩm theo product_id.
 */
function getProductDetail($conn, $product_id) {
    return getProductById($conn, $product_id);
}

/**
 * Lấy danh sách sản phẩm với phân trang và filter.
 */
function getPaginatedProducts($conn, $page, $limit, $search, $brandId, $categoryId, $status) {
    return getProductsWithPagination($conn, $page, $limit, $search, $brandId, $categoryId, $status);
}
?>