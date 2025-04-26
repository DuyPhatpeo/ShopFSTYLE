<?php
// File: admin/controller/productController.php

require_once __DIR__ . '/../model/productModel.php';
require_once __DIR__ . '/../model/brandModel.php';
require_once __DIR__ . '/../model/categoryModel.php';
require_once __DIR__ . '/stringHelper.php'; // Gồm removeAccents() và safeString()

/**
 * Upload ảnh sản phẩm với tên: {safe(product_name)}-{position}.{ext}
 */
function uploadImage($file, $targetDir, $productName, $position)
{
    $result = ['success' => false, 'filename' => '', 'message' => ''];

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = "Không có tệp hợp lệ được tải lên.";
        return $result;
    }

    // Kiểm tra định dạng
    $allowed = ['jpg','jpeg','png','gif','webp'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        $result['message'] = "Chỉ chấp nhận định dạng: " . implode(', ', $allowed);
        return $result;
    }

    // Tạo folder nếu chưa có
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // Sinh tên file an toàn
    $safe = safeString($productName);
    $filename = "{$safe}-{$position}.{$ext}";
    $dest = rtrim($targetDir, '/') . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        $result['success']  = true;
        $result['filename'] = $filename;
    } else {
        $result['message'] = "Không thể lưu tệp lên server.";
    }

    return $result;
}

/**
 * Xử lý thêm sản phẩm và upload nhiều ảnh (không phân biệt chính/phụ)
 */
function processAddProduct($conn) {
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy và validate input
        $product_name   = trim($_POST['product_name'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $original_price = floatval($_POST['original_price'] ?? 0);
        $discount_price = floatval($_POST['discount_price'] ?? 0);
        $brand_id       = $_POST['brand_id'] ?? '';
        $category_id    = $_POST['category_id'] ?? '';
        $status         = (int)($_POST['status'] ?? 1);

        if ($product_name === '') {
            $errors['product_name'] = "Tên sản phẩm không được để trống.";
        } elseif (isProductNameExists($conn, $product_name)) {
            $errors['product_name'] = "Tên sản phẩm đã tồn tại.";
        }
        if ($original_price <= 0) {
            $errors['original_price'] = "Giá gốc phải lớn hơn 0.";
        }
        if ($discount_price < 0 || $discount_price > $original_price) {
            $errors['discount_price'] = "Giá giảm không hợp lệ.";
        }
        if ($brand_id === '') {
            $errors['brand_id'] = "Vui lòng chọn thương hiệu.";
        }
        if ($category_id === '') {
            $errors['category_id'] = "Vui lòng chọn danh mục.";
        }
        if (!in_array($status, [1,2], true)) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        // Nếu không có lỗi thì lưu sản phẩm
        if (empty($errors)) {
            $product_id = addProduct(
                $conn,
                $product_name,
                $description,
                $original_price,
                $discount_price,
                $brand_id,
                $category_id,
                $status
            );

            if ($product_id) {
                $targetDir = __DIR__ . "/../uploads/products";
                // Giả sử rằng bạn nhận được một mảng các giá trị từ form
                $mainImageIndex = isset($_POST['is_main']) ? (int)$_POST['is_main'] : 0;
                $positions = $_POST['positions'] ?? [];

                if (!empty($_FILES['content-imgs']['name']) && is_array($_FILES['content-imgs']['name'])) {
                    $total = count($_FILES['content-imgs']['name']);
                    for ($i = 0; $i < $total; $i++) {
                        if ($_FILES['content-imgs']['error'][$i] === UPLOAD_ERR_OK) {
                            $file = [
                                'name'     => $_FILES['content-imgs']['name'][$i],
                                'type'     => $_FILES['content-imgs']['type'][$i],
                                'tmp_name' => $_FILES['content-imgs']['tmp_name'][$i],
                                'error'    => $_FILES['content-imgs']['error'][$i],
                                'size'     => $_FILES['content-imgs']['size'][$i],
                            ];

                            // Gọi hàm upload ảnh
                            $up = uploadImage($file, $targetDir, $product_name, $positions[$i]);
                            if ($up['success']) {
                                // Đánh dấu ảnh chính: nếu thứ tự i+1 trùng với mainImageIndex
                                $is_main = ($i + 1 === $mainImageIndex) ? 1 : 0;

                                addImage(
                                    $conn,
                                    $product_id,
                                    $up['filename'],
                                    $positions[$i],
                                    1,   // status = 1 (active)
                                    $is_main   // Đánh dấu ảnh chính hoặc phụ
                                );
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

        // Xử lý ảnh mới nếu có
        $mainImageIndex = isset($_POST['is_main']) ? (int)$_POST['is_main'] : 0;
        $positions = $_POST['positions'] ?? [];
        $targetDir = __DIR__ . "/../uploads/products";
        $imagesToDelete = [];

        if (!empty($_FILES['content-imgs']['name']) && is_array($_FILES['content-imgs']['name'])) {
            $total = count($_FILES['content-imgs']['name']);
            for ($i = 0; $i < $total; $i++) {
                if ($_FILES['content-imgs']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'name'     => $_FILES['content-imgs']['name'][$i],
                        'type'     => $_FILES['content-imgs']['type'][$i],
                        'tmp_name' => $_FILES['content-imgs']['tmp_name'][$i],
                        'error'    => $_FILES['content-imgs']['error'][$i],
                        'size'     => $_FILES['content-imgs']['size'][$i],
                    ];

                    // Gọi hàm upload ảnh
                    $up = uploadImage($file, $targetDir, $product_name, $positions[$i]);
                    if ($up['success']) {
                        // Xóa ảnh cũ nếu có
                        if ($main_image && file_exists($targetDir . '/' . $main_image)) {
                            unlink($targetDir . '/' . $main_image); // Xóa ảnh cũ
                        }

                        // Đánh dấu ảnh chính: nếu thứ tự i+1 trùng với mainImageIndex
                        $is_main = ($i + 1 === $mainImageIndex) ? 1 : 0;

                        // Cập nhật thông tin ảnh mới vào database
                        addImage(
                            $conn,
                            $product_id,
                            $up['filename'],
                            $positions[$i],
                            1,   // status = 1 (active)
                            $is_main   // Đánh dấu ảnh chính hoặc phụ
                        );

                        // Cập nhật tên ảnh chính mới nếu có
                        if ($is_main) {
                            $main_image = $up['filename'];
                        }
                    }
                }
            }
        }

        // Nếu không có lỗi thì cập nhật sản phẩm
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