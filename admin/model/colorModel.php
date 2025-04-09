<?php
require_once __DIR__ . '/../controller/stringHelper.php';

function generateColorID() {
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

function isColorNameExists($conn, $colorName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM colors WHERE color_name = ? AND color_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $colorName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM colors WHERE color_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $colorName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

function getColorsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);
    $sqlCount    = "SELECT COUNT(*) as total FROM colors WHERE color_name LIKE ?";
    $stmtCount   = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $result      = $stmtCount->get_result();
    $row         = $result->fetch_assoc();
    $totalColors = (int)($row['total'] ?? 0);
    $totalPages  = max(1, ceil($totalColors / $limit));
    $page        = min($page, $totalPages);
    $offset      = ($page - 1) * $limit;

    $sql = "SELECT * FROM colors WHERE color_name LIKE ? ORDER BY color_name ASC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    $colors = $stmt->get_result();
    
    return [
        'colors'      => $colors,
        'totalPages'  => $totalPages,
        'currentPage' => $page,
        'totalColors' => $totalColors
    ];
}

function addColor($conn, $colorName, $colorCode, $status) {
    $color_id = generateColorID();
    $sql = "INSERT INTO colors (color_id, color_name, color_code, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $color_id, $colorName, $colorCode, $status);
    return $stmt->execute();
}

function getColorById($conn, $color_id) {
    $sql = "SELECT * FROM colors WHERE color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $color_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateColor($conn, $color_id, $colorName, $colorCode, $status) {
    $sql = "UPDATE colors SET color_name = ?, color_code = ?, status = ? WHERE color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $colorName, $colorCode, $status, $color_id);
    return $stmt->execute();
}

function deleteColor($conn, $color_id) {
    $sql = "DELETE FROM colors WHERE color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $color_id);
    return $stmt->execute();
}
?>