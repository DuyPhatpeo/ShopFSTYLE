<?php
require_once __DIR__ . '/stringHelper.php';

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
    $sql         = "SELECT * FROM colors WHERE color_name LIKE ? ORDER BY color_name ASC LIMIT ? OFFSET ?";
    $stmt        = $conn->prepare($sql);
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

function processAddColor($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $colorName = trim($_POST['color_name'] ?? '');
        $colorCode = trim($_POST['color_code'] ?? '');
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        
        if (empty($colorName)) {
            $errors['color_name'] = "Tên màu không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $colorName)) {
            $errors['color_name'] = "Tên màu không được chứa ký tự đặc biệt.";
        } elseif (isColorNameExists($conn, $colorName)) {
            $errors['color_name'] = "Tên màu đã tồn tại.";
        }
        
        if (empty($colorCode)) {
            $errors['color_code'] = "Mã màu không được để trống.";
        } 
        // Cho phép nhập 1 mã hoặc 2 mã màu cách nhau bằng dấu phẩy (có hoặc không có khoảng trắng)
        elseif (!preg_match("/^#[0-9A-Fa-f]{6}(,\s*#[0-9A-Fa-f]{6})?$/", $colorCode)) {
            $errors['color_code'] = "Mã màu không hợp lệ. Ví dụ: #FFFFFF hoặc #FF0000, #00FF00";
        }
        
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }
        
        if (empty($errors)) {
            if (addColor($conn, $colorName, $colorCode, $status)) {
                header("Location: index.php?msg=Thêm màu thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm màu thất bại.";
            }
        }
    }
    return $errors;
}

function getColorById($conn, $color_id) {
    $sql = "SELECT * FROM colors WHERE color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $color_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function processEditColor($conn, $color_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $colorName = trim($_POST['color_name'] ?? '');
        $colorCode = trim($_POST['color_code'] ?? '');
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        
        if (empty($colorName)) {
            $errors['color_name'] = "Tên màu không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $colorName)) {
            $errors['color_name'] = "Tên màu không được chứa ký tự đặc biệt.";
        } elseif (isColorNameExists($conn, $colorName, $color_id)) {
            $errors['color_name'] = "Tên màu đã tồn tại.";
        }
        
        if (empty($colorCode)) {
            $errors['color_code'] = "Mã màu không được để trống.";
        } 
        // Cho phép 1 mã màu hoặc 2 mã màu
        elseif (!preg_match("/^#[0-9A-Fa-f]{6}(,\s*#[0-9A-Fa-f]{6})?$/", $colorCode)) {
            $errors['color_code'] = "Mã màu không hợp lệ. Ví dụ: #FFFFFF hoặc #FF0000, #00FF00";
        }
        
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }
        
        if (empty($errors)) {
            $sql = "UPDATE colors SET color_name = ?, color_code = ?, status = ? WHERE color_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssis", $colorName, $colorCode, $status, $color_id);
            if ($stmt->execute()) {
                header("Location: index.php?msg=Cập nhật màu thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật màu thất bại.";
            }
        }
    }
    return $errors;
}

function deleteColor($conn, $color_id) {
    $sql = "DELETE FROM colors WHERE color_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $color_id);
    return $stmt->execute();
}

function processDeleteColor($conn, $color_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteColor($conn, $color_id)) {
            header("Location: index.php?msg=Xóa màu thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa màu thất bại.";
        }
    }
    return $errors;
}
?>