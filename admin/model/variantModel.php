<?php
// File: admin/model/variantModel.php

require_once __DIR__ . '/../controller/stringHelper.php'; // Nếu cần hàm generateUCCID()

/**
 * Lấy danh sách biến thể của 1 sản phẩm với phân trang.
 * Trả về mảng gồm:
 * - variants: đối tượng mysqli_result chứa các dòng biến thể
 * - totalPages: tổng số trang
 * - currentPage: trang hiện tại
 */
function getVariantsWithPagination($conn, $product_id, $page = 1, $limit = 10, $colorId = null, $sizeId = null, $status = null, $sortBy = '', $sortOrder = 'ASC') {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $offset = ($page - 1) * $limit;

    // Build the conditions
    $conditions = "pv.product_id = ?";
    $paramTypes = "s";
    $params = [$product_id];

    if (!empty($colorId)) {
        $conditions .= " AND pv.color_id = ?";
        $paramTypes .= "s";
        $params[] = $colorId;
    }
    if (!empty($sizeId)) {
        $conditions .= " AND pv.size_id = ?";
        $paramTypes .= "s";
        $params[] = $sizeId;
    }
    if ($status !== null && $status !== "") {
        $conditions .= " AND pv.status = ?";
        $paramTypes .= "i";
        $params[] = $status;
    }

    // Xử lý thứ tự sắp xếp
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
    switch ($sortBy) {
        case 'color':
            $orderBy = "ORDER BY c.color_name $sortOrder";
            break;
        case 'size':
            $orderBy = "ORDER BY s.size_name $sortOrder";
            break;
        default:
            $orderBy = "ORDER BY pv.variant_id DESC";
    }

    // Đếm tổng
    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM product_variants pv WHERE $conditions");
    $stmtCount->bind_param($paramTypes, ...$params);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result()->fetch_assoc();
    $totalVariants = (int)($resultCount['total'] ?? 0);
    $stmtCount->close();

    $totalPages = ceil($totalVariants / $limit);

    // Lấy danh sách biến thể
    $stmt = $conn->prepare("
        SELECT pv.variant_id, pv.color_id, pv.size_id, pv.quantity, pv.status,
               c.color_name, s.size_name
        FROM product_variants pv
        LEFT JOIN color c ON pv.color_id = c.color_id
        LEFT JOIN sizes s ON pv.size_id = s.size_id
        WHERE $conditions
        $orderBy
        LIMIT ? OFFSET ?
    ");

    $paramTypesWithLimit = $paramTypes . "ii";
    $paramsWithLimit = array_merge($params, [$limit, $offset]);
    $stmt->bind_param($paramTypesWithLimit, ...$paramsWithLimit);
    $stmt->execute();
    $variants = $stmt->get_result();
    $stmt->close();

    return [
        'variants'    => $variants,
        'totalPages'  => $totalPages,
        'currentPage' => $page,
    ];
}

/**
 * Lấy thông tin của một biến thể theo variant_id.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $variant_id Mã biến thể.
 * @return array|null Thông tin biến thể (associative array) hoặc null nếu không tìm thấy.
 */
function getVariantById($conn, $variant_id) {
    $stmt = $conn->prepare("SELECT * FROM product_variants WHERE variant_id = ?");
    $stmt->bind_param("s", $variant_id);
    $stmt->execute();
    $variant = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $variant;
}

/**
 * Thêm biến thể mới.
 * Các trường lưu: variant_id, product_id, color_id, size_id, quantity, status, created_at (NOW()).
 */
function addVariant($conn, $product_id, $color_id, $size_id, $quantity, $status = 1) {
    // Sinh ra variant_id
    $variant_id = generateUCCID();

    // Chuẩn bị câu lệnh SQL
    $stmt = $conn->prepare("INSERT INTO product_variants (variant_id, product_id, color_id, size_id, quantity, status)
                            VALUES (?, ?, ?, ?, ?, ?)");

    // Kiểm tra lỗi trong quá trình chuẩn bị câu lệnh
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);
    }

    // Gắn tham số vào câu lệnh SQL
    $stmt->bind_param("ssssii", $variant_id, $product_id, $color_id, $size_id, $quantity, $status);

    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {
        // Đóng kết nối sau khi thực hiện xong
        $stmt->close();
        return $variant_id; // Trả về ID của biến thể vừa thêm
    }

    // Nếu có lỗi trong quá trình thực thi câu lệnh SQL
    $stmt->close();
    return false; // Trả về false nếu có lỗi
}

/**
 * Cập nhật số lượng cho biến thể, tự động chuyển status:
 * Nếu số lượng mới = 0 thì status = 0, ngược lại status = 1.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $variant_id Mã biến thể.
 * @param int    $newQuantity Số lượng mới.
 * @return bool Trả về true nếu cập nhật thành công.
 */
function updateVariantQuantity($conn, $variant_id, $newQuantity) {
    $newStatus = ($newQuantity === 0) ? 0 : 1;
    $stmt = $conn->prepare("UPDATE product_variants SET quantity = ?, status = ? WHERE variant_id = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("iis", $newQuantity, $newStatus, $variant_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

/**
 * Kiểm tra xem đã tồn tại biến thể của sản phẩm với cùng color và size chưa.
 */
function isVariantExists($conn, $product_id, $color_id, $size_id, $exclude_variant_id = null) {
    $sql = "SELECT COUNT(*) AS total FROM product_variants WHERE product_id = ? AND color_id = ? AND size_id = ?";
    $paramTypes = "sss";
    $params = [$product_id, $color_id, $size_id];
    if ($exclude_variant_id !== null) {
        $sql .= " AND variant_id != ?";
        $paramTypes .= "s";
        $params[] = $exclude_variant_id;
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($paramTypes, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ($result['total'] > 0);
}

/**
 * Xoá biến thể.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $variant_id Mã biến thể.
 * @return bool Trả về true nếu xoá thành công.
 */
function deleteVariant($conn, $variant_id) {
    $stmt = $conn->prepare("DELETE FROM product_variants WHERE variant_id = ?");
    $stmt->bind_param("s", $variant_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}
?>