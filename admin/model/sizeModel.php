<?php
require_once __DIR__ . '/../controller/stringHelper.php';

function isSizeNameExists($conn, $sizeName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM sizes WHERE size_name = ? AND size_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $sizeName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM sizes WHERE size_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $sizeName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

function getSizesWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);
    $searchParam = "%" . $search . "%";

    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM sizes WHERE size_name LIKE ?");
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $totalSizes = (int)($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
    $totalPages = max(1, ceil($totalSizes / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT * FROM sizes WHERE size_name LIKE ? ORDER BY size_name ASC LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    $sizes = $stmt->get_result();

    return [
        'sizes'       => $sizes,
        'totalPages'  => $totalPages,
        'currentPage' => $page,
        'totalSizes'  => $totalSizes
    ];
}

function addSize($conn, $sizeName) {
    $size_id = generateUCCID();
    $sql = "INSERT INTO sizes (size_id, size_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $size_id, $sizeName);
    return $stmt->execute();
}

function getSizeById($conn, $size_id) {
    $sql = "SELECT * FROM sizes WHERE size_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $size_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function deleteSize($conn, $size_id) {
    $sql = "DELETE FROM sizes WHERE size_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $size_id);
    return $stmt->execute();
}
function getAllSizes($conn) {
    $sql = "SELECT * FROM sizes ORDER BY size_name ASC";
    $result = $conn->query($sql);
    $sizes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $sizes[] = $row;
        }
    }
    return $sizes;
}