<?php
/**
 * Hàm tạo ID dạng UUID v4 cho size.
 *
 * @return string
 */
function generateSizeID() {
    $data = random_bytes(16);
    // Phiên bản 4
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Variant
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return sprintf('%s-%s-%s-%s-%s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}

/**
 * Kiểm tra xem tên size đã tồn tại chưa (có thể loại trừ ID hiện tại khi chỉnh sửa).
 *
 * @param mysqli $conn
 * @param string $sizeName
 * @param string|null $excludeId
 * @return bool
 */
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

/**
 * Lấy danh sách size theo phân trang, hỗ trợ tìm kiếm theo tên.
 *
 * @param mysqli $conn
 * @param int $page
 * @param int $limit
 * @param string $search
 * @return array
 */
function getSizesWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);

    // Đếm tổng số size
    $sqlCount = "SELECT COUNT(*) as total FROM sizes WHERE size_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $rowCount = $resultCount->fetch_assoc();
    $totalSizes = (int)($rowCount['total'] ?? 0);
    $totalPages = max(1, ceil($totalSizes / $limit));

    // Đảm bảo trang hiện tại không vượt quá tổng số trang
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Lấy danh sách size
    $sql = "SELECT * FROM sizes
            WHERE size_name LIKE ?
            ORDER BY size_name ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
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

/**
 * Thêm size mới.
 *
 * @param mysqli $conn
 * @param string $sizeName
 * @return bool
 */
function addSize($conn, $sizeName) {
    $size_id = generateSizeID();
    $sql = "INSERT INTO sizes (size_id, size_name) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $size_id, $sizeName);
    return $stmt->execute();
}

/**
 * Xử lý thêm size từ form.
 * Nếu có lỗi: trả về mảng lỗi (không redirect).
 * Nếu thành công: redirect về trang danh sách size.
 *
 * @param mysqli $conn
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processAddSize($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sizeName = trim($_POST['size_name'] ?? '');

        // Kiểm tra trường tên
        if (empty($sizeName)) {
            $errors['size_name'] = "Tên size không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $sizeName)) {
            $errors['size_name'] = "Tên size không được chứa ký tự đặc biệt.";
        } elseif (isSizeNameExists($conn, $sizeName)) {
            $errors['size_name'] = "Tên size đã tồn tại.";
        }

        if (empty($errors)) {
            if (addSize($conn, $sizeName)) {
                header("Location: index.php?msg=Thêm size thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm size thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Lấy thông tin size theo ID.
 *
 * @param mysqli $conn
 * @param string $size_id
 * @return array|null
 */
function getSizeById($conn, $size_id) {
    $sql = "SELECT * FROM sizes WHERE size_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $size_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}



/**
 * Xóa size theo ID.
 *
 * @param mysqli $conn
 * @param string $size_id
 * @return bool
 */
function deleteSize($conn, $size_id) {
    $sql = "DELETE FROM sizes WHERE size_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $size_id);
    return $stmt->execute();
}

/**
 * Xử lý xóa size từ form.
 * Nếu có lỗi: trả về mảng lỗi (không redirect).
 * Nếu thành công: redirect về trang danh sách size.
 *
 * @param mysqli $conn
 * @param string $size_id
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processDeleteSize($conn, $size_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteSize($conn, $size_id)) {
            header("Location: index.php?msg=Xóa size thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa size thất bại.";
        }
    }
    return $errors;
}