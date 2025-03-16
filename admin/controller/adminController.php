<?php
// admin/controller/adminController.php

require_once __DIR__ . '/stringHelper.php';

/**
 * Tạo ID dạng UUID v4 cho admin.
 *
 * @return string UUID v4.
 */
function generateAdminID() {
    $data = random_bytes(16);
    // Thiết lập phiên bản 4
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Thiết lập biến thể
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
 * Kiểm tra xem tên đăng nhập đã tồn tại chưa (loại trừ admin hiện tại nếu cần).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $username Tên đăng nhập.
 * @param string|null $excludeId ID cần loại trừ.
 * @return bool True nếu tồn tại.
 */
function isUsernameExists($conn, $username, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM admin WHERE username = ? AND admin_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM admin WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/**
 * Lấy danh sách admin theo phân trang, hỗ trợ tìm kiếm theo username hoặc full_name.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param int $page Trang hiện tại.
 * @param int $limit Số admin mỗi trang.
 * @param string $search Từ khóa tìm kiếm.
 * @return array Dữ liệu gồm danh sách admin, tổng số trang, trang hiện tại, tổng admin.
 */
function getAdminsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);
    $searchParam = "%" . $search . "%";

    // Đếm tổng số admin
    $sqlCount = "SELECT COUNT(*) as total FROM admin WHERE username LIKE ? OR full_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param("ss", $searchParam, $searchParam);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $row = $result->fetch_assoc();
    $totalAdmins = (int)($row['total'] ?? 0);
    $totalPages = max(1, ceil($totalAdmins / $limit));

    // Đảm bảo trang hiện tại không vượt quá tổng số trang
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Lấy danh sách admin, kết hợp thông tin role (join với bảng role)
    $sql = "SELECT a.*, r.role_name 
            FROM admin a
            LEFT JOIN role r ON a.role_id = r.role_id
            WHERE a.username LIKE ? OR a.full_name LIKE ?
            ORDER BY a.username ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset);
    $stmt->execute();
    $admins = $stmt->get_result();

    return [
        'admins'      => $admins,
        'totalPages'  => $totalPages,
        'currentPage' => $page,
        'totalAdmins' => $totalAdmins
    ];
}

/**
 * Thêm admin mới.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $username Tên đăng nhập.
 * @param string $password Mật khẩu gốc (sẽ được mã hóa).
 * @param string $email Email.
 * @param string $fullName Họ tên.
 * @param string $roleId ID vai trò (có thể rỗng).
 * @return bool True nếu thêm thành công.
 */
function addAdmin($conn, $username, $password, $email, $fullName, $roleId) {
    $admin_id = generateAdminID();
    // Mã hóa mật khẩu bằng hàm password_hash
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    // Nếu roleId trống, ta có thể lưu dưới dạng NULL hoặc chuỗi rỗng tùy theo thiết kế CSDL.
    $roleId = $roleId !== "" ? $roleId : null;
    $sql = "INSERT INTO admin (admin_id, username, password, email, full_name, role_id)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $admin_id, $username, $hashedPassword, $email, $fullName, $roleId);
    return $stmt->execute();
}

/**
 * Xử lý thêm admin mới thông qua form.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return string Thông báo lỗi nếu có.
 */
function processAddAdmin($conn) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username        = trim($_POST['username']);
        $password        = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);
        $email           = trim($_POST['email']);
        $fullName        = trim($_POST['full_name']);
        $roleId          = isset($_POST['role_id']) ? trim($_POST['role_id']) : "";

        // Kiểm tra dữ liệu bắt buộc (role_id không bắt buộc)
        if (empty($username) || empty($password) || empty($confirmPassword) || empty($email) || empty($fullName)) {
            return "Các trường bắt buộc không được để trống.";
        }
        if ($password !== $confirmPassword) {
            return "Mật khẩu và nhập lại mật khẩu không khớp.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email không hợp lệ.";
        }
        if (isUsernameExists($conn, $username)) {
            return "Tên đăng nhập đã tồn tại.";
        }

        if (addAdmin($conn, $username, $password, $email, $fullName, $roleId)) {
            header("Location: index.php?msg=added");
            exit;
        } else {
            return "Thêm tài khoản thất bại.";
        }
    }
    return $error;
}

/**
 * Lấy thông tin chi tiết của admin theo ID.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $admin_id ID admin.
 * @return array|null Mảng thông tin admin.
 */
function getAdminById($conn, $admin_id) {
    $sql = "SELECT a.*, r.role_name FROM admin a LEFT JOIN role r ON a.role_id = r.role_id WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Cập nhật thông tin admin.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $admin_id ID admin cần chỉnh sửa.
 * @param string $username Tên đăng nhập.
 * @param string|null $password Mật khẩu mới (nếu cập nhật, nếu không thì để rỗng).
 * @param string $email Email.
 * @param string $fullName Họ tên.
 * @param string $roleId ID vai trò (có thể rỗng).
 * @return bool True nếu cập nhật thành công.
 */
function updateAdmin($conn, $admin_id, $username, $password, $email, $fullName, $roleId) {
    // Nếu có cập nhật mật khẩu thì mã hóa lại, nếu không thì không cập nhật trường này
    $roleId = $roleId !== "" ? $roleId : null;
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET username = ?, password = ?, email = ?, full_name = ?, role_id = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $hashedPassword, $email, $fullName, $roleId, $admin_id);
    } else {
        $sql = "UPDATE admin SET username = ?, email = ?, full_name = ?, role_id = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $email, $fullName, $roleId, $admin_id);
    }
    return $stmt->execute();
}

/**
 * Xử lý chỉnh sửa admin thông qua form.
 *
 * Nếu nhập mật khẩu mới, hệ thống sẽ cập nhật mật khẩu.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $admin_id ID admin cần chỉnh sửa.
 * @return string Thông báo lỗi nếu có.
 */
function processEditAdmin($conn, $admin_id) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']); // Nếu để rỗng thì không cập nhật mật khẩu
        $confirmPassword = trim($_POST['confirm_password']);
        $email    = trim($_POST['email']);
        $fullName = trim($_POST['full_name']);
        $roleId   = isset($_POST['role_id']) ? trim($_POST['role_id']) : "";

        // Kiểm tra các trường bắt buộc (role_id không bắt buộc)
        if (empty($username) || empty($email) || empty($fullName)) {
            return "Các trường bắt buộc không được để trống.";
        }
        if (!empty($password) && $password !== $confirmPassword) {
            return "Mật khẩu và nhập lại mật khẩu không khớp.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Email không hợp lệ.";
        }
        if (isUsernameExists($conn, $username, $admin_id)) {
            return "Tên đăng nhập đã tồn tại.";
        }

        if (updateAdmin($conn, $admin_id, $username, $password, $email, $fullName, $roleId)) {
            header("Location: index.php?msg=updated");
            exit;
        } else {
            return "Cập nhật tài khoản thất bại.";
        }
    }
    return $error;
}

/**
 * Xóa admin theo ID.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $admin_id ID admin cần xóa.
 * @return bool True nếu xóa thành công.
 */
function deleteAdmin($conn, $admin_id) {
    $sql = "DELETE FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $admin_id);
    return $stmt->execute();
}

/**
 * Lấy danh sách vai trò từ bảng role để hiển thị trong dropdown.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return array Danh sách vai trò.
 */
function getAllRoles($conn) {
    $sql = "SELECT role_id, role_name FROM role ORDER BY role_name ASC";
    $result = $conn->query($sql);
    $roles = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()){
            $roles[] = $row;
        }
    }
    return $roles;
}
?>