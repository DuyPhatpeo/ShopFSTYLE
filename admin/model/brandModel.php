<?php
// File: admin/model/brandModel.php

require_once __DIR__ . '/../controller/stringHelper.php';

function isBrandNameExists($conn, $brandName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM brand WHERE brand_name = ? AND brand_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $brandName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM brand WHERE brand_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $brandName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

function getBrandsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);

    $sqlCount = "SELECT COUNT(*) as total FROM brand WHERE brand_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $row = $result->fetch_assoc();
    $totalBrands = (int)($row['total'] ?? 0);
    $totalPages = max(1, ceil($totalBrands / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM brand
            WHERE brand_name LIKE ?
            ORDER BY brand_name ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    $brands = $stmt->get_result();

    return [
        'brands'      => $brands,
        'totalPages'  => $totalPages,
        'currentPage' => $page,
        'totalBrands' => $totalBrands
    ];
}

function addBrandWithImage($conn, $brandName, $status, $imageUrl) {
    $brand_id   = generateUCCID();
    $brand_slug = createSlug($brandName);

    $sql = "INSERT INTO brand (brand_id, brand_name, brand_slug, status, image_url)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $brand_id, $brandName, $brand_slug, $status, $imageUrl);
    return $stmt->execute();
}

function getBrandById($conn, $brand_id) {
    $sql = "SELECT * FROM brand WHERE brand_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $brand_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getBrandBySlug($conn, $slug) {
    $sql = "SELECT * FROM brand WHERE brand_slug = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $slug);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateBrand($conn, $brandName, $status, $imageUrl, $brand_id) {
    $brand_slug = createSlug($brandName);
    $sql = "UPDATE brand SET brand_name = ?, brand_slug = ?, status = ?, image_url = ? WHERE brand_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $brandName, $brand_slug, $status, $imageUrl, $brand_id);
    return $stmt->execute();
}

function deleteBrand($conn, $brand_id) {
    $currentBrand = getBrandById($conn, $brand_id);
    if ($currentBrand && !empty($currentBrand['image_url'])) {
        $physicalPath = __DIR__ . '/../../' . $currentBrand['image_url'];
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
    }
    $sql = "DELETE FROM brand WHERE brand_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $brand_id);
    return $stmt->execute();
}

function getAllBrands($conn) {
    $sql = "SELECT brand_id, brand_name, brand_slug FROM brand WHERE status = 1 ORDER BY brand_name ASC";
    $result = $conn->query($sql);
    $brands = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $brands[] = $row;
        }
    }
    return $brands;
}