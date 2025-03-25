<?php
// File: admin/controller/productController.php

/*==================*
 *  Utility Functions
 *==================*/

/**
 * Sinh UUID v4 đơn giản.
 */
function generateUUID() {
    $data = random_bytes(16);
    // Version 4
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Variant is 10xxxxxx
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return sprintf(
        '%s-%s-%s-%s-%s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}

/**
 * Kiểm tra xem tên sản phẩm đã tồn tại hay chưa.
 */
function isProductNameExists($conn, $productName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM product WHERE product_name = ? AND product_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $productName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM product WHERE product_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $productName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/*==================*
 *  STEP 1: Add Product
 *==================*/

/**
 * Tạo sản phẩm (Bước 1).
 * Các trường: product_name, brand_id, category_id, original_price, discount_price, descriptionProduct.
 * Lưu ý: bảng product không chứa trường số lượng.
 */
function processAddProductStep1($conn, $postData, $fileData, &$errors) {
    $productName   = trim($postData['product_name'] ?? '');
    $description   = trim($postData['descriptionProduct'] ?? '');
    $originalPrice = trim($postData['original_price'] ?? '');
    $discountPrice = trim($postData['discount_price'] ?? '');
    $brandId       = trim($postData['brand_id'] ?? '');
    $categoryId    = trim($postData['category_id'] ?? '');

    if (empty($productName)) {
        $errors['product_name'] = "Tên sản phẩm không được để trống.";
    } elseif (isProductNameExists($conn, $productName)) {
        $errors['product_name'] = "Tên sản phẩm đã tồn tại.";
    }
    if (empty($brandId)) {
        $errors['brand_id'] = "Vui lòng chọn thương hiệu.";
    } else {
        $checkSql = "SELECT brand_id FROM brand WHERE brand_id = ? LIMIT 1";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->bind_param("s", $brandId);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        if ($resCheck->num_rows === 0) {
            $errors['brand_id'] = "Thương hiệu này không tồn tại.";
        }
    }
    if (empty($categoryId)) {
        $errors['category_id'] = "Vui lòng chọn danh mục.";
    } else {
        $checkSql = "SELECT category_id FROM category WHERE category_id = ? LIMIT 1";
        $stmtCheck = $conn->prepare($checkSql);
        $stmtCheck->bind_param("s", $categoryId);
        $stmtCheck->execute();
        $resCheck = $stmtCheck->get_result();
        if ($resCheck->num_rows === 0) {
            $errors['category_id'] = "Danh mục này không tồn tại.";
        }
    }
    if (empty($originalPrice) || !is_numeric($originalPrice) || (float)$originalPrice <= 0) {
        $errors['original_price'] = "Giá gốc phải là số > 0.";
    }
    if ($discountPrice === '') {
        $discountPrice = 0;
    } elseif (!is_numeric($discountPrice) || (float)$discountPrice < 0) {
        $errors['discount_price'] = "Giá khuyến mãi phải là số >= 0.";
    }
    if (!empty($errors)) {
        return null;
    }
    $originalPrice = (float)$originalPrice;
    $discountPrice = (float)$discountPrice;
    $status    = "1";
    $createdBy = "1"; // Ví dụ admin_id là "1"
    $product_id = generateUUID();

    $sql = "INSERT INTO product (
                product_id, product_name, description, original_price, discount_price,
                brand_id, category_id, created_by, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssddssss",
        $product_id,
        $productName,
        $description,
        $originalPrice,
        $discountPrice,
        $brandId,
        $categoryId,
        $createdBy,
        $status
    );
    if (!$stmt->execute()) {
        $errors['general'] = "Không thể thêm sản phẩm: " . $conn->error;
        return null;
    }
    return $product_id;
}

/*==================*
 *  STEP 2: Add Variants
 *==================*/

/**
 * Thêm biến thể cho sản phẩm (Bước 2).
 * Nếu có dữ liệu từ variant_color, variant_size, variant_quantity thì thêm từng dòng,
 * ngược lại dùng default_quantity để tạo biến thể mặc định.
 */
function processAddVariantsStep2($conn, $product_id, $postData, $fileData, &$errors) {
    $colors     = $postData['variant_color'] ?? [];
    $sizes      = $postData['variant_size'] ?? [];
    $quantities = $postData['variant_quantity'] ?? [];
    
    if (!empty($colors) && !empty($sizes) && !empty($quantities)) {
        $count = count($colors);
        for ($i = 0; $i < $count; $i++) {
            $colorId = trim($colors[$i] ?? '');
            $sizeId  = trim($sizes[$i] ?? '');
            $qty     = trim($quantities[$i] ?? '');
            if ($colorId === '' || $sizeId === '') {
                $errors['variants'] = "Biến thể thứ " . ($i+1) . " thiếu màu hoặc kích thước.";
                continue;
            }
            if (!is_numeric($qty) || (int)$qty < 0) {
                $errors['variants'] = "Biến thể thứ " . ($i+1) . " số lượng phải >= 0.";
                continue;
            }
            addProductVariant($conn, $product_id, [
                'color_id' => $colorId,
                'size_id'  => $sizeId,
                'quantity' => (int)$qty,
                'status'   => "1"
            ]);
        }
    } else {
        $defaultQuantity = trim($postData['default_quantity'] ?? '');
        if ($defaultQuantity === '' || !is_numeric($defaultQuantity) || (int)$defaultQuantity < 0) {
            $errors['default_quantity'] = "Số lượng chung phải là số >= 0.";
            return;
        }
        addProductVariant($conn, $product_id, [
            'color_id' => "",
            'size_id'  => "",
            'quantity' => (int)$defaultQuantity,
            'status'   => "1"
        ]);
    }
}

/**
 * Thêm một biến thể vào bảng product_variants.
 */
function addProductVariant($conn, $product_id, $variantData) {
    $variant_id = generateUUID();
    $status     = $variantData['status'] ?? "1";
    $color_id   = $variantData['color_id'] ?? "";
    $size_id    = $variantData['size_id'] ?? "";
    $quantity   = (int)($variantData['quantity'] ?? 0);
    
    $sql = "INSERT INTO product_variants (
                variant_id, product_id, color_id, size_id, quantity, status
            ) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis",
        $variant_id,
        $product_id,
        $color_id,
        $size_id,
        $quantity,
        $status
    );
    if ($stmt->execute()) {
        return $variant_id;
    }
    return null;
}

/*==================*
 *  STEP 3: Add Variant Images
 *==================*/

/**
 * Xử lý upload ảnh cho biến thể.
 * Dữ liệu file: variant_images[variant_id][].
 */
function processAddVariantImagesStep3($conn, $product_id, $postData, $fileData, &$errors) {
    $sql = "SELECT variant_id FROM product_variants WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $variant_id = $row['variant_id'];
        if (isset($fileData['variant_images']['name'][$variant_id])) {
            $files = [];
            foreach ($fileData['variant_images']['name'][$variant_id] as $k => $v) {
                $files[] = [
                    'name'     => $fileData['variant_images']['name'][$variant_id][$k],
                    'type'     => $fileData['variant_images']['type'][$variant_id][$k],
                    'tmp_name' => $fileData['variant_images']['tmp_name'][$variant_id][$k],
                    'error'    => $fileData['variant_images']['error'][$variant_id][$k],
                    'size'     => $fileData['variant_images']['size'][$variant_id][$k],
                ];
            }
            uploadVariantImages($conn, $variant_id, $files);
        }
    }
}

/**
 * Upload nhiều ảnh cho một biến thể.
 */
function uploadVariantImages($conn, $variant_id, $files) {
    $imageIds = [];
    foreach ($files as $file) {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $targetDir = __DIR__ . '/../uploads/variants/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension  = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename   = 'variant_' . uniqid() . '.' . $extension;
            $targetFile = $targetDir . $filename;
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $imagePath = 'admin/uploads/variants/' . $filename;
                $image_id  = addImage($conn, $imagePath, "");
                if ($image_id) {
                    addVariantImage($conn, $variant_id, $image_id);
                    $imageIds[] = $image_id;
                }
            }
        }
    }
    return $imageIds;
}

/**
 * Thêm ảnh vào bảng images.
 */
function addImage($conn, $path, $caption = "") {
    $image_id = generateUUID();
    $sql = "INSERT INTO images (image_id, image_url, caption) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $image_id, $path, $caption);
    if ($stmt->execute()) {
        return $image_id;
    }
    return null;
}

/**
 * Liên kết ảnh với biến thể trong bảng variant_images.
 */
function addVariantImage($conn, $variant_id, $image_id) {
    $sql = "INSERT INTO variant_images (variant_id, image_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $variant_id, $image_id);
    return $stmt->execute();
}

/*==================*
 *  Lấy dữ liệu cho Dropdown & Pagination
 *==================*/

function getProductById($conn, $product_id) {
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getAllBrands($conn) {
    $sql = "SELECT brand_id, brand_name FROM brand WHERE status = 1 ORDER BY brand_name ASC";
    $result = $conn->query($sql);
    $brands = [];
    while ($row = $result->fetch_assoc()){
        $brands[] = $row;
    }
    return $brands;
}

function getAllCategories($conn) {
    $sql = "SELECT category_id, category_name FROM category WHERE status = 1 ORDER BY category_name ASC";
    $result = $conn->query($sql);
    $categories = [];
    while ($row = $result->fetch_assoc()){
        $categories[] = $row;
    }
    return $categories;
}

function getAllColors($conn) {
    $sql = "SELECT color_id, color_name, color_code FROM colors ORDER BY color_name ASC";
    $result = $conn->query($sql);
    $colors = [];
    while ($row = $result->fetch_assoc()){
        $colors[] = $row;
    }
    return $colors;
}

function getAllSizes($conn) {
    $sql = "SELECT size_id, size_name FROM sizes ORDER BY size_name ASC";
    $result = $conn->query($sql);
    $sizes = [];
    while ($row = $result->fetch_assoc()){
        $sizes[] = $row;
    }
    return $sizes;
}

function getProductsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page  = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $search = trim($search);
    $likeParam = "%" . $search . "%";
    
    $sqlCount = "SELECT COUNT(*) as total FROM product WHERE product_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param("s", $likeParam);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $rowCount = $resultCount->fetch_assoc();
    $total = (int)($rowCount['total'] ?? 0);
    
    $totalPages = max(1, ceil($total / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;
    
    $sql = "
        SELECT p.*,
               b.brand_name,
               c.category_name
        FROM product p
        LEFT JOIN brand b ON p.brand_id = b.brand_id
        LEFT JOIN category c ON p.category_id = c.category_id
        WHERE p.product_name LIKE ?
        ORDER BY p.product_name ASC
        LIMIT ? OFFSET ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $likeParam, $limit, $offset);
    $stmt->execute();
    $products = $stmt->get_result();
    
    return [
        'products'    => $products,
        'totalPages'  => $totalPages,
        'currentPage' => $page,
        'total'       => $total
    ];
}
?>