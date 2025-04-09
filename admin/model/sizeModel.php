<?php
function generateSizeID() {
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
    $size_id = generateSizeID();
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