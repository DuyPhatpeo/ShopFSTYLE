<?php
// File: admin/model/variantModel.php

/**
 * Lấy danh sách biến thể của một sản phẩm với phân trang.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $product_id Mã sản phẩm.
 * @param int    $page Số trang hiện tại.
 * @param int    $limit Số bản ghi trên mỗi trang.
 * @return array Mảng gồm: 'variants' => kết quả truy vấn, 'totalPages', 'currentPage'
 */
function getVariantsWithPagination($conn, $product_id, $page = 1, $limit = 10) {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $offset = ($page - 1) * $limit;

    // Đếm tổng số biến thể của sản phẩm
    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM product_variants WHERE product_id = ?");
    $stmtCount->bind_param("s", $product_id);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result()->fetch_assoc();
    $totalVariants = (int)($resultCount['total'] ?? 0);
    $stmtCount->close();

    $totalPages = ceil($totalVariants / $limit);

    // Lấy danh sách biến thể của sản phẩm theo phân trang
    // Sắp xếp theo tên màu (color_name) tăng dần, nếu cùng tên màu thì sắp xếp theo variant_id giảm dần
    $stmt = $conn->prepare("SELECT pv.variant_id, pv.color_id, pv.size_id, pv.quantity, pv.status,
                                   c.color_name, s.size_name
                            FROM product_variants pv
                            LEFT JOIN color c ON pv.color_id = c.color_id
                            LEFT JOIN sizes s ON pv.size_id = s.size_id
                            WHERE pv.product_id = ?
                            ORDER BY c.color_name ASC, pv.variant_id DESC
                            LIMIT ? OFFSET ?");
    $stmt->bind_param("sii", $product_id, $limit, $offset);
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