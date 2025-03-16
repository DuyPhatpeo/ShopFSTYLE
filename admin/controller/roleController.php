<?php
// admin/controller/roleController.php

require_once __DIR__ . '/stringHelper.php';

/**
 * Tạo ID dạng UUID v4 (hoặc bạn có thể dùng generateUCCID() nếu muốn).
 *
 * @return string
 */
function generateRoleID() {
    // Ở đây mình tái sử dụng logic UUID, bạn có thể thay đổi thành logic generateUCCID() nếu thích
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

/**
 * Kiểm tra xem tên role đã tồn tại chưa (có thể loại trừ ID hiện tại khi chỉnh sửa).
 *
 * @param mysqli $conn
 * @param string $roleName
 * @param string|null $excludeId
 * @return bool
 */
function isRoleNameExists($conn, $roleName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM role WHERE role_name = ? AND role_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $roleName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM role WHERE role_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $roleName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/**
 * Lấy danh sách role theo phân trang, hỗ trợ tìm kiếm theo tên.
 *
 * @param mysqli $conn
 * @param int $page
 * @param int $limit
 * @param string $search
 * @return array
 */
function getRolesWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);

    // Đếm tổng số role
    $sqlCount = "SELECT COUNT(*) as total FROM role WHERE role_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $row = $result->fetch_assoc();
    $totalRoles = (int)($row['total'] ?? 0);
    $totalPages = max(1, ceil($totalRoles / $limit));

    // Đảm bảo trang hiện tại không vượt quá tổng số trang
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Lấy danh sách role
    $sql = "SELECT * FROM role
            WHERE role_name LIKE ?
            ORDER BY role_name ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    $roles = $stmt->get_result();

    return [
        'roles'      => $roles,
        'totalPages' => $totalPages,
        'currentPage'=> $page,
        'totalRoles' => $totalRoles
    ];
}

/**
 * Thêm role mới.
 *
 * @param mysqli $conn
 * @param string $roleName
 * @param int $status
 * @return bool
 */
function addRole($conn, $roleName, $status) {
    $role_id = generateRoleID();
    $sql = "INSERT INTO role (role_id, role_name, status)
            VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $role_id, $roleName, $status);
    return $stmt->execute();
}

/**
 * Xử lý thêm role từ form.
 *
 * @param mysqli $conn
 * @return string Thông báo lỗi nếu có.
 */
function processAddRole($conn) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $roleName = trim($_POST['role_name']);
        $status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        // Kiểm tra dữ liệu
        if (empty($roleName)) {
            return "Tên vai trò không được để trống.";
        }
        if (!preg_match("/^[\\p{L}\\p{N}\\s]+$/u", $roleName)) {
            return "Tên vai trò không được chứa ký tự đặc biệt.";
        }
        if (isRoleNameExists($conn, $roleName)) {
            return "Tên vai trò đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            return "Trạng thái không hợp lệ.";
        }

        if (addRole($conn, $roleName, $status)) {
            header("Location: index.php?msg=added");
            exit;
        } else {
            return "Thêm vai trò thất bại.";
        }
    }
    return $error;
}

/**
 * Lấy thông tin role theo ID.
 *
 * @param mysqli $conn
 * @param string $role_id
 * @return array|null
 */
function getRoleById($conn, $role_id) {
    $sql = "SELECT * FROM role WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $role_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Xử lý chỉnh sửa role.
 *
 * @param mysqli $conn
 * @param string $role_id
 * @return string Thông báo lỗi nếu có.
 */
function processEditRole($conn, $role_id) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $roleName = trim($_POST['role_name']);
        $status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        // Kiểm tra dữ liệu
        if (empty($roleName)) {
            return "Tên vai trò không được để trống.";
        }
        if (!preg_match("/^[\\p{L}\\p{N}\\s]+$/u", $roleName)) {
            return "Tên vai trò không được chứa ký tự đặc biệt.";
        }
        if (isRoleNameExists($conn, $roleName, $role_id)) {
            return "Tên vai trò đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            return "Trạng thái không hợp lệ.";
        }

        $sql = "UPDATE role
                SET role_name = ?, status = ?
                WHERE role_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $roleName, $status, $role_id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=updated");
            exit;
        } else {
            return "Cập nhật vai trò thất bại.";
        }
    }
    return $error;
}

/**
 * Xóa role theo ID.
 *
 * @param mysqli $conn
 * @param string $role_id
 * @return bool
 */
function deleteRole($conn, $role_id) {
    $sql = "DELETE FROM role WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $role_id);
    return $stmt->execute();
}

/**
 * Lấy chi tiết vai trò theo ID (nếu cần hiển thị detail).
 *
 * @param mysqli $conn
 * @param string $role_id
 * @return array|null
 */
function getRoleDetail($conn, $role_id) {
    // Có thể giống hệt getRoleById, nhưng bạn tách riêng nếu muốn
    $sql = "SELECT * FROM role WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $role_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}