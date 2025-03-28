<?php
// File: admin/controller/promotionController.php

/**
 * Tạo ID dạng UUID v4.
 *
 * @return string
 */
function generatePromotionID() {
    $data = random_bytes(16);
    // Phiên bản UUID v4
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s-%s-%s-%s-%s', str_split(bin2hex($data), 4));
}

/**
 * Kiểm tra xem promotion_name đã tồn tại chưa (loại trừ ID hiện tại khi chỉnh sửa).
 * Có thể mở rộng để kiểm tra cả promotion_code nếu muốn.
 *
 * @param mysqli $conn
 * @param string $promotionName
 * @param string|null $excludeId
 * @return bool
 */
function isPromotionNameExists($conn, $promotionName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count 
                FROM promotion 
                WHERE promotion_name = ? 
                  AND promotion_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $promotionName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count 
                FROM promotion 
                WHERE promotion_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $promotionName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/**
 * (Tùy chọn) Kiểm tra xem promotion_code đã tồn tại chưa (loại trừ ID hiện tại khi chỉnh sửa).
 *
 * @param mysqli $conn
 * @param string $promotionCode
 * @param string|null $excludeId
 * @return bool
 */
function isPromotionCodeExists($conn, $promotionCode, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count 
                FROM promotion 
                WHERE promotion_code = ? 
                  AND promotion_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $promotionCode, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count 
                FROM promotion 
                WHERE promotion_code = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $promotionCode);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/**
 * Lấy danh sách promotion theo phân trang, hỗ trợ tìm kiếm theo tên khuyến mãi.
 *
 * @param mysqli $conn
 * @param int $page
 * @param int $limit
 * @param string $search
 * @return array
 */
function getPromotionsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);

    // Đếm tổng số promotion
    $sqlCount = "SELECT COUNT(*) as total 
                 FROM promotion 
                 WHERE promotion_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $row = $result->fetch_assoc();
    $totalPromotions = (int)($row['total'] ?? 0);
    $totalPages = max(1, ceil($totalPromotions / $limit));

    // Đảm bảo trang hiện tại không vượt quá tổng số trang
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Lấy danh sách promotion
    $sql = "SELECT * 
            FROM promotion
            WHERE promotion_name LIKE ?
            ORDER BY start_date DESC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    $promotions = $stmt->get_result();

    return [
        'promotions'   => $promotions,
        'totalPages'   => $totalPages,
        'currentPage'  => $page,
        'totalRecords' => $totalPromotions
    ];
}

/**
 * Thêm promotion mới.
 *
 * @param mysqli $conn
 * @param string $promotionName
 * @param string $promotionCode
 * @param string $description
 * @param float  $discountValue
 * @param string $startDate
 * @param string $endDate
 * @return bool
 */
function addPromotion($conn, $promotionName, $promotionCode, $description, $discountValue, $startDate, $endDate) {
    $promotion_id = generatePromotionID();
    $sql = "INSERT INTO promotion (
                promotion_id, 
                promotion_name, 
                promotion_code, 
                description, 
                discount_value, 
                start_date, 
                end_date
            ) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssdss",
        $promotion_id,
        $promotionName,
        $promotionCode,
        $description,
        $discountValue,
        $startDate,
        $endDate
    );
    return $stmt->execute();
}

/**
 * Xử lý thêm promotion từ form.
 * - Kiểm tra validate cơ bản.
 * - Nếu thành công thì redirect về trang danh sách promotion.
 * - Nếu lỗi thì trả về mảng lỗi.
 *
 * @param mysqli $conn
 * @return array
 */
function processAddPromotion($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $promotionName  = trim($_POST['promotion_name'] ?? '');
        $promotionCode  = trim($_POST['promotion_code'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $discountValue  = isset($_POST['discount_value']) ? (float)$_POST['discount_value'] : 0;
        $startDate      = $_POST['start_date'] ?? '';
        $endDate        = $_POST['end_date'] ?? '';

        // Kiểm tra trường promotion_name
        if (empty($promotionName)) {
            $errors['promotion_name'] = "Tên khuyến mãi không được để trống.";
        } elseif (isPromotionNameExists($conn, $promotionName)) {
            $errors['promotion_name'] = "Tên khuyến mãi đã tồn tại.";
        }

        // Kiểm tra trường promotion_code (nếu cần)
        if (empty($promotionCode)) {
            $errors['promotion_code'] = "Mã khuyến mãi không được để trống.";
        } elseif (isPromotionCodeExists($conn, $promotionCode)) {
            $errors['promotion_code'] = "Mã khuyến mãi đã tồn tại.";
        }

        // Kiểm tra discount_value (tùy logic kinh doanh)
        if ($discountValue < 0) {
            $errors['discount_value'] = "Giá trị giảm giá không hợp lệ.";
        }

        // Kiểm tra ngày (tùy logic, ví dụ start_date <= end_date)
        if (!empty($startDate) && !empty($endDate)) {
            if (strtotime($startDate) > strtotime($endDate)) {
                $errors['date_range'] = "Ngày bắt đầu phải trước hoặc bằng ngày kết thúc.";
            }
        }

        if (empty($errors)) {
            if (addPromotion($conn, $promotionName, $promotionCode, $description, $discountValue, $startDate, $endDate)) {
                header("Location: index.php?msg=Thêm khuyến mãi thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm khuyến mãi thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Lấy thông tin promotion theo ID.
 *
 * @param mysqli $conn
 * @param string $promotion_id
 * @return array|null
 */
function getPromotionById($conn, $promotion_id) {
    $sql = "SELECT * FROM promotion WHERE promotion_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $promotion_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Xử lý chỉnh sửa promotion từ form.
 * - Kiểm tra validate.
 * - Nếu thành công thì redirect về trang danh sách promotion.
 * - Nếu lỗi thì trả về mảng lỗi.
 *
 * @param mysqli $conn
 * @param string $promotion_id
 * @return array
 */
function processEditPromotion($conn, $promotion_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $promotionName  = trim($_POST['promotion_name'] ?? '');
        $promotionCode  = trim($_POST['promotion_code'] ?? '');
        $description    = trim($_POST['description'] ?? '');
        $discountValue  = isset($_POST['discount_value']) ? (float)$_POST['discount_value'] : 0;
        $startDate      = $_POST['start_date'] ?? '';
        $endDate        = $_POST['end_date'] ?? '';

        // Kiểm tra trường promotion_name
        if (empty($promotionName)) {
            $errors['promotion_name'] = "Tên khuyến mãi không được để trống.";
        } elseif (isPromotionNameExists($conn, $promotionName, $promotion_id)) {
            $errors['promotion_name'] = "Tên khuyến mãi đã tồn tại.";
        }

        // Kiểm tra trường promotion_code
        if (empty($promotionCode)) {
            $errors['promotion_code'] = "Mã khuyến mãi không được để trống.";
        } elseif (isPromotionCodeExists($conn, $promotionCode, $promotion_id)) {
            $errors['promotion_code'] = "Mã khuyến mãi đã tồn tại.";
        }

        // Kiểm tra discount_value
        if ($discountValue < 0) {
            $errors['discount_value'] = "Giá trị giảm giá không hợp lệ.";
        }

        // Kiểm tra ngày
        if (!empty($startDate) && !empty($endDate)) {
            if (strtotime($startDate) > strtotime($endDate)) {
                $errors['date_range'] = "Ngày bắt đầu phải trước hoặc bằng ngày kết thúc.";
            }
        }

        if (empty($errors)) {
            $sql = "UPDATE promotion 
                    SET 
                        promotion_name = ?, 
                        promotion_code = ?, 
                        description = ?, 
                        discount_value = ?, 
                        start_date = ?, 
                        end_date = ?
                    WHERE 
                        promotion_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "sssdsss",
                $promotionName,
                $promotionCode,
                $description,
                $discountValue,
                $startDate,
                $endDate,
                $promotion_id
            );

            if ($stmt->execute()) {
                header("Location: index.php?msg=Cập nhật khuyến mãi thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật khuyến mãi thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Xóa promotion theo ID.
 *
 * @param mysqli $conn
 * @param string $promotion_id
 * @return bool
 */
function deletePromotion($conn, $promotion_id) {
    $sql = "DELETE FROM promotion WHERE promotion_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $promotion_id);
    return $stmt->execute();
}

/**
 * Xử lý xóa promotion từ form.
 * - Nếu thành công thì redirect.
 * - Nếu lỗi thì trả về mảng lỗi.
 *
 * @param mysqli $conn
 * @param string $promotion_id
 * @return array
 */
function processDeletePromotion($conn, $promotion_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deletePromotion($conn, $promotion_id)) {
            header("Location: index.php?msg=Xóa khuyến mãi thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa khuyến mãi thất bại.";
        }
    }
    return $errors;
}

/**
 * Lấy chi tiết khuyến mãi theo ID (nếu muốn tách riêng).
 *
 * @param mysqli $conn
 * @param string $promotion_id
 * @return array|null
 */
function getPromotionDetail($conn, $promotion_id) {
    return getPromotionById($conn, $promotion_id);
}