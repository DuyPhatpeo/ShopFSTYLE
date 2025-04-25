<?php
// File: admin/controller/promotionController.php
require_once __DIR__ . '/../model/promotionModel.php';

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
                    $targetDir = '../uploads/products/';
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