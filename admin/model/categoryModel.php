<?php
// File: admin/model/categoryModel.php

require_once __DIR__ . '/../controller/stringHelper.php';

function generateUCCID() {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return sprintf('%s-%s-%s-%s-%s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}

function isCategoryNameExists($conn, $categoryName, $excludeId = null) {
    $sql = $excludeId 
        ? "SELECT COUNT(*) as count FROM category WHERE category_name = ? AND category_id != ?"
        : "SELECT COUNT(*) as count FROM category WHERE category_name = ?";
    $stmt = $conn->prepare($sql);
    $excludeId 
        ? $stmt->bind_param("ss", $categoryName, $excludeId)
        : $stmt->bind_param("s", $categoryName);
    $stmt->execute();
    return ($stmt->get_result()->fetch_assoc()['count'] ?? 0) > 0;
}

function getCategoriesWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $search = trim($search);
    $searchParam = "%" . $search . "%";

    // Count total
    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM category WHERE category_name LIKE ?");
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $totalCategories = (int)($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
    $totalPages = max(1, ceil($totalCategories / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Get categories
    $sql = "SELECT c.*, p.category_name as parent_name
            FROM category c
            LEFT JOIN category p ON c.parent_id = p.category_id
            WHERE c.category_name LIKE ?
            ORDER BY c.category_name ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    return [
        'categories' => $stmt->get_result(),
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalCategories' => $totalCategories
    ];
}

function addCategoryWithImage($conn, $categoryName, $parentId, $status, $imageUrl) {
    $category_id = generateUCCID();
    $stmt = $conn->prepare("INSERT INTO category (category_id, category_name, parent_id, status, image_url)
                            VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $category_id, $categoryName, $parentId, $status, $imageUrl);
    return $stmt->execute();
}

function getCategoryById($conn, $category_id) {
    $stmt = $conn->prepare("SELECT * FROM category WHERE category_id = ?");
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateCategory($conn, $category_id, $categoryName, $parentId, $status, $imageUrl) {
    $stmt = $conn->prepare("UPDATE category
                            SET category_name = ?, parent_id = ?, status = ?, image_url = ?
                            WHERE category_id = ?");
    $stmt->bind_param("ssiss", $categoryName, $parentId, $status, $imageUrl, $category_id);
    return $stmt->execute();
}

function deleteCategory($conn, $category_id) {
    $stmt = $conn->prepare("SELECT category_id FROM category WHERE parent_id = ?");
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($child = $result->fetch_assoc()) {
        if (!deleteCategory($conn, $child['category_id'])) return false;
    }

    $current = getCategoryById($conn, $category_id);
    if ($current && !empty($current['image_url'])) {
        $physicalPath = __DIR__ . '/../../' . $current['image_url'];
        if (file_exists($physicalPath)) unlink($physicalPath);
    }

    $stmt = $conn->prepare("DELETE FROM category WHERE category_id = ?");
    $stmt->bind_param("s", $category_id);
    return $stmt->execute();
}