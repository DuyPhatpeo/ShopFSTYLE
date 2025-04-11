<?php
// File: admin/model/productModel.php

require_once __DIR__ . '/../controller/stringHelper.php';


/**
 * Lấy danh sách sản phẩm với phân trang và lọc tìm kiếm.
 * Các filter: tìm theo product_name, brand_id, category_id, status.
 */
function getProductsWithPagination($conn, $page = 1, $limit = 10, $search = "", $brandId = null, $categoryId = null, $status = null) {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $offset = ($page - 1) * $limit;
    $searchParam = '%' . trim($search) . '%';

    // Xây dựng điều kiện truy vấn
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

    // Đếm tổng số sản phẩm thỏa điều kiện
    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM product p WHERE $conditions");
    $stmtCount->bind_param($paramTypes, ...$params);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result()->fetch_assoc();
    $totalProducts = (int)($resultCount['total'] ?? 0);
    $totalPages = max(1, ceil($totalProducts / $limit));
    $stmtCount->close();

    // Lấy danh sách sản phẩm kèm thông tin brand và category
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
 * Thêm sản phẩm mới.
 * Lưu các trường: product_id, product_name, description, original_price, discount_price,
 * brand_id, category_id, status, main_image, created_at (NOW()).
 */
function addProduct($conn, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image) {
    $product_id = generateUCCID();
    $stmt = $conn->prepare("INSERT INTO product (product_id, product_name, description, original_price, discount_price, brand_id, category_id, status, main_image, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    // Các kiểu: s(product_id), s(product_name), s(description), d(original_price), d(discount_price),
    // s(brand_id), s(category_id), i(status), s(main_image)
    $stmt->bind_param("sssddssis", $product_id, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image);
    return $stmt->execute();
}

/**
 * Lấy chi tiết sản phẩm theo product_id.
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
 * Cập nhật thông tin sản phẩm.
 * Cập nhật các trường: product_name, description, original_price, discount_price,
 * brand_id, category_id, status, main_image.
 */
function updateProduct($conn, $product_id, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image) {
    $stmt = $conn->prepare("UPDATE product 
                            SET product_name = ?, description = ?, original_price = ?, discount_price = ?, brand_id = ?, category_id = ?, status = ?, main_image = ?
                            WHERE product_id = ?");
    // Các kiểu: s(product_name), s(description), d(original_price), d(discount_price),
    // s(brand_id), s(category_id), i(status), s(main_image), s(product_id)
    $stmt->bind_param("ssddssiss", $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $main_image, $product_id);
    return $stmt->execute();
}

/**
 * Xóa sản phẩm (và xóa ảnh vật lý nếu có)
 */
function deleteProduct($conn, $product_id) {
    $product = getProductById($conn, $product_id);
    if ($product && !empty($product['main_image'])) {
        $physicalPath = __DIR__ . '/../../' . $product['main_image'];
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
    }
    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    return $stmt->execute();
}