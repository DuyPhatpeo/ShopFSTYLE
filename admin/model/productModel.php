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

    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM product p WHERE $conditions");
    $stmtCount->bind_param($paramTypes, ...$params);
    $stmtCount->execute();
    $totalProducts = (int)($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
    $totalPages = max(1, ceil($totalProducts / $limit));
    $stmtCount->close();

    $sql = "SELECT p.*, b.brand_name, c.category_name 
            FROM product p
            LEFT JOIN brand b ON p.brand_id = b.brand_id
            LEFT JOIN category c ON p.category_id = c.category_id
            WHERE $conditions
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $paramTypes .= "ii";
    $params[] = $limit;
    $params[] = $offset;
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $products = $stmt->get_result();
    $stmt->close();

    return [
        'products' => $products,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalProducts' => $totalProducts
    ];
}

/**
 * Kiểm tra tên sản phẩm đã tồn tại chưa (loại trừ ID hiện tại nếu có)
 */
function isProductNameExists($conn, $product_name, $exclude_id = null) {
    $product_slug = createSlug($product_name);
    $sql = "SELECT COUNT(*) as count FROM product WHERE product_slug = ?";
    $params = [$product_slug];
    $types = "s";

    if ($exclude_id !== null) {
        $sql .= " AND product_id != ?";
        $params[] = $exclude_id;
        $types .= "s";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['count'] ?? 0;
    $stmt->close();
    return $count > 0;
}

/**
 * Thêm sản phẩm mới
 */
function addProduct($conn, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status) {
    $product_slug = createSlug($product_name);
    $product_id = generateUCCID();
    
    $stmt = $conn->prepare("INSERT INTO product 
        (product_id, product_name, description, original_price, discount_price, brand_id, category_id, status, product_slug, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $stmt->bind_param("sssddssis", $product_id, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $product_slug);
    
    $success = $stmt->execute();
    $stmt->close();
    
    return $success ? $product_id : false;
}
function addImage($conn, $product_id, $image_url, $position, $status, $is_main) {
    $image_id = generateUCCID();
    
    $stmt = $conn->prepare("INSERT INTO product_images (image_id, product_id, image_url, position, status, is_main) VALUES (?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssiii",$image_id, $product_id, $image_url, $position, $status, $is_main);
    
    $success = $stmt->execute();
    $stmt->close();
    
    return $success ? $product_id : false;
}
/**
 * Cập nhật sản phẩm
 */
function updateProduct($conn, $product_id, $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status) {
    $product_slug = createSlug($product_name);
    $stmt = $conn->prepare("UPDATE product 
        SET product_name=?, description=?, original_price=?, discount_price=?, brand_id=?, category_id=?, status=?, product_slug=? 
        WHERE product_id=?");
    $stmt->bind_param("ssddssisss", $product_name, $description, $original_price, $discount_price, $brand_id, $category_id, $status, $product_slug, $product_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

/**
 * Lấy chi tiết sản phẩm theo product_slug hoặc ID
 */
function getProductBySlug($conn, $product_slug) {
    $stmt = $conn->prepare("SELECT p.*, b.brand_name, c.category_name 
        FROM product p
        LEFT JOIN brand b ON p.brand_id = b.brand_id
        LEFT JOIN category c ON p.category_id = c.category_id
        WHERE p.product_slug = ?");
    $stmt->bind_param("s", $product_slug);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $product;
}

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
 * Quản lý ảnh phụ của sản phẩm
 */
function getProductImages($conn, $product_id) {
    $stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY position ASC");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

function addProductImages($conn, $product_id, $imageUrls) {
    $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_url, position, status) VALUES (?, ?, ?, 1)");
    foreach ($imageUrls as $position => $url) {
        $stmt->bind_param("ssi", $product_id, $url, $position);
        $stmt->execute();
    }
    $stmt->close();
}

function deleteProductImages($conn, $product_id) {
    $stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $file = __DIR__ . '/../../' . $row['image_url'];
        if (file_exists($file)) unlink($file);
    }
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $stmt->execute();
    $stmt->close();
}

/**
 * Xóa sản phẩm (bao gồm ảnh chính và ảnh phụ)
 */
function deleteProduct($conn, $product_id) {
    $product = getProductById($conn, $product_id);

    deleteProductImages($conn, $product_id);

    $stmt = $conn->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param("s", $product_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}