<?php
require_once __DIR__ . '/../controller/stringHelper.php';


function isColorNameExists($conn, $colorName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM color WHERE color_name = ? AND color_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $colorName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM color WHERE color_name = ?";
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
    $sqlCount    = "SELECT COUNT(*) as total FROM color WHERE color_name LIKE ?";
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

    $sql = "SELECT * FROM color WHERE color_name LIKE ? ORDER BY color_name ASC LIMIT ? OFFSET ?";
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
    $color_id = generateUCCID();
    $sql = "INSERT INTO color (color_id, color_name, color_code, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $color_id, $colorName, $colorCode, $status);
    return $stmt->execute();
}

function getColorById($conn, $color_id) {
    $sql = "SELECT * FROM color WHERE color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $color_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateColor($conn, $color_id, $colorName, $colorCode, $status) {
    $sql = "UPDATE color SET color_name = ?, color_code = ?, status = ? WHERE color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $colorName, $colorCode, $status, $color_id);
    return $stmt->execute();
}

function deleteColor($conn, $color_id) {
    $sql = "DELETE FROM color WHERE color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $color_id);
    return $stmt->execute();
}
function getAllColors($conn) {
    $sql = "SELECT * FROM color ORDER BY color_name ASC";
    $result = $conn->query($sql);
    $colors = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $colors[] = $row;
        }
    }
    return $colors;
}
function getColorNameById($conn, $color_id) {
    // Prepare SQL statement to get color name
    $query = "SELECT color_name FROM colors WHERE color_id = :color_id LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':color_id', $color_id, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if color name exists and return it
    if ($result && isset($result['color_name'])) {
        return $result['color_name'];
    } else {
        return false; // Return false if color name not found
    }
}
?>