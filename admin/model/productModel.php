<?php
// File: admin/model/productModel.php

require_once __DIR__ . '/../controller/stringHelper.php';

/**
 * Lấy danh sách sản phẩm có phân trang, tìm kiếm, lọc theo brand, category, status
 */
function getProductsWithPagination($conn, $page = 1, $limit = 10, $search = "", $brandId = null, $categoryId = null, $status = null) {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $offset = ($page - 1) * $limit;
    $searchParam = '%' . trim($search) . '%';

    $conditions = "p.product_name LIKE ?";
    $paramTypes = "s";
    $params = [$searchParam];

    if ($brandId) {
        $conditions .= " AND p.brand_id = ?";
        $paramTypes .= "s";
        $params[] = $brandId;
    }

    if ($categoryId) {
        $conditions .= " AND p.category_id = ?";
        $paramTypes .= "s";
        $params[] = $categoryId;
    }

    if ($status !== null && $status !== '') {
        $conditions .= " AND p.status = ?";
        $paramTypes .= "i";
        $params[] = $status;
    }

    // Đếm tổng số sản phẩm
    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM product p WHERE $conditions");
    $stmtCount->bind_param($paramTypes, ...$params);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result()->fetch_assoc();
    $totalProducts = (int)($resultCount['total'] ?? 0);
    $totalPages = max(1, ceil($totalProducts / $limit));
    $stmtCount->close();

    // Lấy danh sách sản phẩm
    $sql = "SELECT p.*, b.brand_name, c.category_name 
            FROM product p
            LEFT JOIN brand b ON p.brand_id = b.brand_id
            LEFT JOIN category c ON p.category_id = c.category_id
            WHERE $conditions
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $paramTypesWithLimit = $paramTypes . "ii";
    $paramsWithLimit = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($paramTypesWithLimit, ...$paramsWithLimit);
    $stmt->execute();
    $products = $stmt->get_result();
    $stmt->close();

    return [
        'products'      => $products,
        'totalPages'    => $totalPages,
        'currentPage'   => $page,
        'totalProducts' => $totalProducts
    ];
}

/**
 * Kiểm tra tên sản phẩm đã tồn tại chưa (loại trừ sản phẩm hiện tại)
 */
function isProductNameExists($conn, $product_name, $exclude_id = null) {
    $sql = "SELECT COUNT(*) FROM product WHERE product_name = ?";
    $params = [$product_name];

    if ($exclude_id !== null) {
        $sql .= " AND product_id != ?";
        $params[] = $exclude_id;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", ...$params);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['COUNT(*)'] > 0;
}

/**
 * Thêm sản phẩm mới
 */
function addProduct($conn, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image) {
    $product_id = generateUCCID();
    $stmt = $conn->prepare("INSERT INTO product (product_id, product_name, description, original_price, discount_price, brand_id, category_id, status, main_image, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssddssis", $product_id, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image);
    
    if ($stmt->execute()) {
        return $product_id;
    }
    return false;
}

/**
 * Lấy chi tiết sản phẩm
 */
function getProductById($conn, $product_id) {
    $stmt = $conn->prepare("SELECT p.*, b.brand_name, c.category_name 
                            FROM product p
                            LEFT JOIN brand b ON p.brand_id = b.brand_id
                            LEFT JOIN category c ON p.category_id = c.category_id
                            WHERE p.product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $product;
}

/**
 * Lấy danh sách ảnh phụ theo product_id
 */
function getProductImages($conn, $product_id) {
    $stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY position ASC");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

/**
 * Cập nhật sản phẩm
 */
function updateProduct($conn, $product_id, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image) {
    $stmt = $conn->prepare("UPDATE product 
                            SET product_name = ?, description = ?, original_price = ?, discount_price = ?, brand_id = ?, category_id = ?, status = ?, main_image = ?
                            WHERE product_id = ?");
    $stmt->bind_param("ssddssiss", $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image, $product_id);
    return $stmt->execute();
}

/**
 * Thêm ảnh phụ cho sản phẩm
 */
function addProductImages($conn, $product_id, $imageUrls) {
    $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_url, position, status) VALUES (?, ?, ?, 1)");
    foreach ($imageUrls as $position => $url) {
        $stmt->bind_param("ssi", $product_id, $url, $position);
        $stmt->execute();
    }
    $stmt->close();
}

/**
 * Xóa ảnh phụ (cả vật lý + DB)
 */
function deleteProductImages($conn, $product_id) {
    $stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $path = __DIR__ . '/../../' . $row['image_url'];
        if (file_exists($path)) {
            unlink($path);
        }
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $stmt->close();
}

/**
 * Xóa sản phẩm (và ảnh)
 */
function deleteProduct($conn, $product_id) {
    $product = getProductById($conn, $product_id);
    if ($product && !empty($product['main_image'])) {
        $mainImagePath = __DIR__ . '/../../' . $product['main_image'];
        if (file_exists($mainImagePath)) {
            unlink($mainImagePath);
        }
    }

    deleteProductImages($conn, $product_id);

    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    return $stmt->execute();
}