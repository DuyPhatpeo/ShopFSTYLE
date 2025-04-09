<?php
// File: admin/model/promotionModel.php

function generatePromotionID() {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s-%s-%s-%s-%s', str_split(bin2hex($data), 4));
}

function isPromotionNameExists($conn, $promotionName, $excludeId = null) {
    $sql = "SELECT COUNT(*) as count FROM promotion WHERE promotion_name = ?" . ($excludeId ? " AND promotion_id != ?" : "");
    $stmt = $conn->prepare($sql);
    $excludeId ? $stmt->bind_param("ss", $promotionName, $excludeId) : $stmt->bind_param("s", $promotionName);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)$row['count'] > 0);
}

function isPromotionCodeExists($conn, $promotionCode, $excludeId = null) {
    $sql = "SELECT COUNT(*) as count FROM promotion WHERE promotion_code = ?" . ($excludeId ? " AND promotion_id != ?" : "");
    $stmt = $conn->prepare($sql);
    $excludeId ? $stmt->bind_param("ss", $promotionCode, $excludeId) : $stmt->bind_param("s", $promotionCode);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return ((int)$row['count'] > 0);
}

function getPromotionsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $search = trim($search);
    $searchParam = "%" . $search . "%";

    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM promotion WHERE promotion_name LIKE ?");
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $totalPromotions = (int)($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
    $totalPages = max(1, ceil($totalPromotions / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT * FROM promotion WHERE promotion_name LIKE ? ORDER BY start_date DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();

    return [
        'promotions'   => $stmt->get_result(),
        'totalPages'   => $totalPages,
        'currentPage'  => $page,
        'totalRecords' => $totalPromotions
    ];
}

function addPromotion($conn, $promotionName, $promotionCode, $description, $discountValue, $startDate, $endDate) {
    $promotion_id = generatePromotionID();
    $stmt = $conn->prepare("INSERT INTO promotion (promotion_id, promotion_name, promotion_code, description, discount_value, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdss", $promotion_id, $promotionName, $promotionCode, $description, $discountValue, $startDate, $endDate);
    return $stmt->execute();
}

function getPromotionById($conn, $promotion_id) {
    $stmt = $conn->prepare("SELECT * FROM promotion WHERE promotion_id = ?");
    $stmt->bind_param("s", $promotion_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updatePromotion($conn, $promotion_id, $promotionName, $promotionCode, $description, $discountValue, $startDate, $endDate) {
    $stmt = $conn->prepare("UPDATE promotion SET promotion_name = ?, promotion_code = ?, description = ?, discount_value = ?, start_date = ?, end_date = ? WHERE promotion_id = ?");
    $stmt->bind_param("sssdsss", $promotionName, $promotionCode, $description, $discountValue, $startDate, $endDate, $promotion_id);
    return $stmt->execute();
}

function deletePromotion($conn, $promotion_id) {
    $stmt = $conn->prepare("DELETE FROM promotion WHERE promotion_id = ?");
    $stmt->bind_param("s", $promotion_id);
    return $stmt->execute();
}