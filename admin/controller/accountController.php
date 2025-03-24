<?php
// File: admin/controller/accountController.php

require_once __DIR__ . '/stringHelper.php';
require_once __DIR__ . '/roleController.php'; // Để sử dụng các hàm liên quan đến role

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
 * Kiểm tra xem email đã tồn tại chưa (loại trừ admin hiện tại nếu cần).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $email Email.
 * @param string|null $excludeId ID cần loại trừ.
 * @return bool True nếu tồn tại.
 */
function isEmailExists($conn, $email, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM admin WHERE email = ? AND admin_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM admin WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
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

    $sqlCount = "SELECT COUNT(*) as total FROM admin WHERE username LIKE ? OR full_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param("ss", $searchParam, $searchParam);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $row = $result->fetch_assoc();
    $totalAdmins = (int)($row['total'] ?? 0);
    $totalPages = max(1, ceil($totalAdmins / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

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
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $roleId = $roleId !== "" ? $roleId : null;
    $sql = "INSERT INTO admin (admin_id, username, password, email, full_name, role_id)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $admin_id, $username, $hashedPassword, $email, $fullName, $roleId);
    return $stmt->execute();
}

/**
 * Xử lý thêm admin mới qua form.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processAddAdmin($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username        = trim($_POST['username']);
        $password        = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);
        $email           = trim($_POST['email']);
        $fullName        = trim($_POST['full_name']);
        $roleId          = isset($_POST['role_id']) ? trim($_POST['role_id']) : "";

        if (empty($username)) {
            $errors['username'] = "Tên đăng nhập không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}]+$/u", $username)) {
            $errors['username'] = "Tên đăng nhập chỉ được chứa chữ và số (không khoảng trắng).";
        } else {
            if (isUsernameExists($conn, $username)) {
                $errors['username'] = "Tên đăng nhập đã tồn tại.";
            }
        }
        if (empty($password)) {
            $errors['password'] = "Mật khẩu không được để trống.";
        } elseif (strlen($password) < 6) {
            $errors['password'] = "Mật khẩu phải có tối thiểu 6 ký tự.";
        }
        if (empty($confirmPassword)) {
            $errors['confirm_password'] = "Vui lòng xác nhận lại mật khẩu.";
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = "Mật khẩu và nhập lại mật khẩu không khớp.";
        }
        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } else {
            if (isEmailExists($conn, $email)) {
                $errors['email'] = "Email đã tồn tại.";
            }
        }
        if (empty($fullName)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        } elseif (!preg_match("/^[\p{L}\s]+$/u", $fullName)) {
            $errors['full_name'] = "Họ và tên không được chứa số hoặc ký tự đặc biệt.";
        }
        if (empty($roleId)) {
            $errors['role_id'] = "Vui lòng chọn vai trò.";
        }
        if (count($errors) === 0) {
            if (addAdmin($conn, $username, $password, $email, $fullName, $roleId)) {
                header("Location: index.php?msg=Thêm tài khoản thành công!");
                exit;
            } else {
                $errors['general'] = "Thêm tài khoản thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Lấy thông tin chi tiết của admin theo ID.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $admin_id ID admin.
 * @return array|null Mảng thông tin admin hoặc null nếu không tìm thấy.
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
 * Tại trang edit, không cho phép thay đổi username.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $admin_id ID admin.
 * @param string $email Email.
 * @param string $fullName Họ tên.
 * @param string $roleId ID vai trò.
 * @param string|null $password Mật khẩu mới (nếu cập nhật).
 * @return bool True nếu cập nhật thành công.
 */
function updateAdmin($conn, $admin_id, $email, $fullName, $roleId, $password = null) {
    $roleId = $roleId !== "" ? $roleId : null;
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET email = ?, full_name = ?, role_id = ?, password = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $email, $fullName, $roleId, $hashedPassword, $admin_id);
    } else {
        $sql = "UPDATE admin SET email = ?, full_name = ?, role_id = ? WHERE admin_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $email, $fullName, $roleId, $admin_id);
    }
    return $stmt->execute();
}

/**
 * Xử lý chỉnh sửa admin qua form.
 * Tại trang edit, trường username không được thay đổi.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $admin_id ID admin cần chỉnh sửa.
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processEditAdmin($conn, $admin_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $adminData = getAdminById($conn, $admin_id);
        if (!$adminData) {
            $errors['general'] = "Không tìm thấy tài khoản admin.";
            return $errors;
        }
        // Username không thay đổi nên không cần kiểm tra.
        // Lấy dữ liệu từ form (email, full name, role, mật khẩu nếu có cập nhật)
        $email    = trim($_POST['email']);
        $fullName = trim($_POST['full_name']);
        $roleId   = isset($_POST['role_id']) ? trim($_POST['role_id']) : "";
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);

        // Kiểm tra email
        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } else {
            if (isEmailExists($conn, $email, $admin_id)) {
                $errors['email'] = "Email đã tồn tại.";
            }
        }
        // Kiểm tra họ tên
        if (empty($fullName)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        } elseif (!preg_match("/^[\p{L}\s]+$/u", $fullName)) {
            $errors['full_name'] = "Họ và tên không được chứa số hoặc ký tự đặc biệt.";
        }
        // Kiểm tra vai trò
        if (empty($roleId)) {
            $errors['role_id'] = "Vui lòng chọn vai trò.";
        }
        // Kiểm tra mật khẩu nếu có nhập
        if (!empty($password) || !empty($confirmPassword)) {
            if (empty($password)) {
                $errors['password'] = "Mật khẩu không được để trống.";
            } elseif (strlen($password) < 6) {
                $errors['password'] = "Mật khẩu phải có tối thiểu 6 ký tự.";
            }
            if (empty($confirmPassword)) {
                $errors['confirm_password'] = "Vui lòng xác nhận lại mật khẩu.";
            } elseif ($password !== $confirmPassword) {
                $errors['confirm_password'] = "Mật khẩu và nhập lại mật khẩu không khớp.";
            }
        }
        if (count($errors) === 0) {
            if (updateAdmin($conn, $admin_id, $email, $fullName, $roleId, $password)) {
                header("Location: index.php?msg=Cập nhật tài khoản thành công!");
                exit;
            } else {
                $errors['general'] = "Cập nhật tài khoản thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Xóa admin theo ID.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $admin_id ID admin cần xóa.
 * @return bool True nếu xóa thành công, False nếu có lỗi.
 */
function deleteAdmin($conn, $admin_id) {
    $sql = "DELETE FROM admin WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Lỗi prepare statement: " . $conn->error);
        return false;
    }
    $stmt->bind_param("s", $admin_id);
    if (!$stmt->execute()) {
        error_log("Lỗi khi xóa admin (ID: $admin_id): " . $stmt->error);
        return false;
    }
    return true;
}

/**
 * Xử lý xóa admin từ URL.
 *
 * @param mysqli $conn Kết nối CSDL.
 */
function processDeleteAdmin($conn) {
    if (isset($_GET['delete_admin']) && !empty($_GET['delete_admin'])) {
        $admin_id = $_GET['delete_admin'];
        if (deleteAdmin($conn, $admin_id)) {
            header("Location: index.php?msg=Xóa tài khoản thành công!");
            exit;
        } else {
            header("Location: index.php?msg=Không thể xóa tài khoản!");
            exit;
        }
    }
}

/**
 * Lấy danh sách vai trò từ bảng role.
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


/**
 * Kiểm tra thông tin đăng nhập admin dựa trên email hoặc username.
 * 
 * @param mysqli $conn Kết nối CSDL.
 * @param string $identifier Email hoặc username.
 * @param string $password Mật khẩu người dùng nhập.
 * @return array|false Trả về mảng thông tin admin nếu đăng nhập thành công, ngược lại trả về false.
 */
function loginAdmin($conn, $identifier, $password) {
    $sql = "SELECT * FROM admin WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    // Nếu tồn tại bản ghi
    if ($admin = $result->fetch_assoc()) {
        // So sánh mật khẩu đã mã hóa trong DB với mật khẩu người dùng nhập
        if (password_verify($password, $admin['password'])) {
            return $admin; // Thành công
        }
    }
    return false; // Thất bại
}
?>