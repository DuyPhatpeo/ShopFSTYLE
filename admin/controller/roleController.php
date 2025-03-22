<?php
// File: admin/controller/roleController.php

require_once __DIR__ . '/stringHelper.php';

/**
 * Tạo ID dạng UUID v4 (hoặc bạn có thể dùng generateUCCID() nếu muốn).
 *
 * @return string
 */
function generateRoleID() {
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
 * Kiểm tra xem tên role đã tồn tại chưa (loại trừ ID hiện tại khi chỉnh sửa).
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
        'roles'       => $roles,
        'totalPages'  => $totalPages,
        'currentPage' => $page,
        'totalRoles'  => $totalRoles
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
    $sql = "INSERT INTO role (role_id, role_name, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $role_id, $roleName, $status);
    return $stmt->execute();
}

/**
 * Xử lý thêm role từ form.
 * Nếu có lỗi: trả về mảng lỗi (không redirect).
 * Nếu thành công: redirect về trang danh sách role.
 *
 * @param mysqli $conn
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processAddRole($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $roleName = trim($_POST['role_name'] ?? '');
        $status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        // Kiểm tra trường tên
        if (empty($roleName)) {
            $errors['role_name'] = "Tên vai trò không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $roleName)) {
            $errors['role_name'] = "Tên vai trò không được chứa ký tự đặc biệt.";
        } elseif (isRoleNameExists($conn, $roleName)) {
            $errors['role_name'] = "Tên vai trò đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        if (empty($errors)) {
            if (addRole($conn, $roleName, $status)) {
                header("Location: index.php?msg=Thêm vai trò thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm vai trò thất bại.";
            }
        }
    }
    return $errors;
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
 * Xử lý chỉnh sửa role từ form.
 * Nếu có lỗi: trả về mảng lỗi (không redirect).
 * Nếu thành công: redirect về trang danh sách role.
 *
 * @param mysqli $conn
 * @param string $role_id
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processEditRole($conn, $role_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $roleName = trim($_POST['role_name'] ?? '');
        $status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        // Kiểm tra trường tên
        if (empty($roleName)) {
            $errors['role_name'] = "Tên vai trò không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $roleName)) {
            $errors['role_name'] = "Tên vai trò không được chứa ký tự đặc biệt.";
        } elseif (isRoleNameExists($conn, $roleName, $role_id)) {
            $errors['role_name'] = "Tên vai trò đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            $errors['status'] = "Trạng thái không hợp lệ.";
        }

        if (empty($errors)) {
            $sql = "UPDATE role SET role_name = ?, status = ? WHERE role_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sis", $roleName, $status, $role_id);
            if ($stmt->execute()) {
                header("Location: index.php?msg=Cập nhật vai trò thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật vai trò thất bại.";
            }
        }
    }
    return $errors;
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
 * Xử lý xóa role từ form.
 * Nếu có lỗi: trả về mảng lỗi (không redirect).
 * Nếu thành công: redirect về trang danh sách role.
 *
 * @param mysqli $conn
 * @param string $role_id
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processDeleteRole($conn, $role_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteRole($conn, $role_id)) {
            header("Location: index.php?msg=Xóa vai trò thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa vai trò thất bại.";
        }
    }
    return $errors;
}

/**
 * Lấy chi tiết vai trò theo ID.
 *
 * @param mysqli $conn
 * @param string $role_id
 * @return array|null
 */
function getRoleDetail($conn, $role_id) {
    $sql = "SELECT * FROM role WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $role_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
?>