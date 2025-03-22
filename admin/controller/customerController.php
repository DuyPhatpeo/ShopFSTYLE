<?php
// customerController.php

/**
 * Kiểm tra xem email đã tồn tại chưa (loại trừ khách hàng hiện tại nếu cần).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $email Email.
 * @param string|null $excludeId ID cần loại trừ.
 * @return bool True nếu tồn tại.
 */
function isEmailExists($conn, $email, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM customer WHERE email = ? AND customer_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM customer WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/**
 * Thêm khách hàng mới.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $email Email.
 * @param string $password Mật khẩu gốc (sẽ được mã hóa).
 * @param string $fullName Họ và tên.
 * @param string $phone Số điện thoại.
 * @param string $address Địa chỉ.
 * @param int $status Trạng thái (1: Hoạt động, 0: Khóa).
 * @return bool True nếu thêm thành công.
 */
function addCustomer($conn, $email, $password, $fullName, $phone, $address, $status = 1) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO customer (email, password, full_name, phone, address, status)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $email, $hashedPassword, $fullName, $phone, $address, $status);
    return $stmt->execute();
}

/**
 * Lấy danh sách khách hàng theo phân trang, hỗ trợ tìm kiếm theo email hoặc full_name.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param int $page Trang hiện tại.
 * @param int $limit Số khách hàng mỗi trang.
 * @param string $search Từ khóa tìm kiếm.
 * @return array Mảng gồm: 
 *               - 'customers': result set khách hàng,
 *               - 'totalPages': tổng số trang,
 *               - 'currentPage': trang hiện tại,
 *               - 'totalCustomers': tổng số khách hàng.
 */
function getCustomersWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);
    $searchParam = "%" . $search . "%";

    $sqlCount = "SELECT COUNT(*) as total FROM customer WHERE email LIKE ? OR full_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $stmtCount->bind_param("ss", $searchParam, $searchParam);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $rowCount = $resultCount->fetch_assoc();
    $totalCustomers = (int)($rowCount['total'] ?? 0);
    $totalPages = max(1, ceil($totalCustomers / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM customer 
            WHERE email LIKE ? OR full_name LIKE ?
            ORDER BY customer_id DESC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset);
    $stmt->execute();
    $customers = $stmt->get_result();

    return [
        'customers'      => $customers,
        'totalPages'     => $totalPages,
        'currentPage'    => $page,
        'totalCustomers' => $totalCustomers
    ];
}

/**
 * Lấy thông tin chi tiết khách hàng theo ID.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $customer_id ID khách hàng.
 * @return array|null Mảng thông tin khách hàng hoặc null nếu không tìm thấy.
 */
function getCustomerById($conn, $customer_id) {
    $sql = "SELECT * FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

/**
 * Cập nhật thông tin khách hàng.
 *
 * Nếu nhập mật khẩu mới thì sẽ mã hóa và cập nhật, còn không thì chỉ cập nhật các trường khác.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $customer_id ID khách hàng.
 * @param string $email Email.
 * @param string $fullName Họ và tên.
 * @param string $phone Số điện thoại.
 * @param string $address Địa chỉ.
 * @param int $status Trạng thái (1: Hoạt động, 0: Khóa).
 * @param string|null $newPassword Mật khẩu mới (nếu cập nhật).
 * @return bool True nếu cập nhật thành công.
 */
function updateCustomer($conn, $customer_id, $email, $fullName, $phone, $address, $status, $newPassword = null) {
    if (!empty($newPassword)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE customer SET email = ?, password = ?, full_name = ?, phone = ?, address = ?, status = ? WHERE customer_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssiss", $email, $hashedPassword, $fullName, $phone, $address, $status, $customer_id);
    } else {
        $sql = "UPDATE customer SET email = ?, full_name = ?, phone = ?, address = ?, status = ? WHERE customer_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssis", $email, $fullName, $phone, $address, $status, $customer_id);
    }
    return $stmt->execute();
}

/**
 * Xóa khách hàng theo ID.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $customer_id ID khách hàng cần xóa.
 * @return bool True nếu xóa thành công.
 */
function deleteCustomer($conn, $customer_id) {
    $sql = "DELETE FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Lỗi prepare statement: " . $conn->error);
        return false;
    }
    $stmt->bind_param("s", $customer_id);
    if (!$stmt->execute()) {
        error_log("Lỗi khi xóa khách hàng (ID: $customer_id): " . $stmt->error);
        return false;
    }
    return true;
}

/**
 * Xử lý form chỉnh sửa khách hàng.
 *
 * Các trường trong form gồm: email, full_name, phone, address, status, new_password, confirm_password.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $customer_id ID khách hàng cần chỉnh sửa.
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processEditCustomer($conn, $customer_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customerData = getCustomerById($conn, $customer_id);
        if (!$customerData) {
            $errors['general'] = "Không tìm thấy khách hàng.";
            return $errors;
        }
        
        $email           = isset($_POST['email']) ? trim($_POST['email']) : '';
        $fullName        = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $phone           = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $address         = isset($_POST['address']) ? trim($_POST['address']) : '';
        $status          = isset($_POST['status']) ? (int) $_POST['status'] : 1;
        $newPassword     = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
        $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } else {
            if (isEmailExists($conn, $email, $customer_id)) {
                $errors['email'] = "Email đã tồn tại.";
            }
        }
        if (empty($fullName)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        }
        if ($newPassword !== '' || $confirmPassword !== '') {
            if (strlen($newPassword) < 6) {
                $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
            }
            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = "Mật khẩu và xác nhận mật khẩu không khớp.";
            }
        }
        if (count($errors) === 0) {
            if (updateCustomer($conn, $customer_id, $email, $fullName, $phone, $address, $status, $newPassword)) {
                header("Location: index.php?msg=Cập nhật khách hàng thành công!");
                exit;
            } else {
                $errors['general'] = "Cập nhật khách hàng thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Xử lý form thêm khách hàng mới.
 *
 * Các trường trong form gồm: email, password, full_name, phone, address, status.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processAddCustomer($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';
        $fullName = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $phone    = isset($_POST['phone']) ? trim($_POST['phone']) : '';
        $address  = isset($_POST['address']) ? trim($_POST['address']) : '';
        $status   = isset($_POST['status']) ? (int) $_POST['status'] : 1;

        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } else {
            if (isEmailExists($conn, $email)) {
                $errors['email'] = "Email đã tồn tại.";
            }
        }
        if (empty($password)) {
            $errors['password'] = "Mật khẩu không được để trống.";
        } elseif (strlen($password) < 6) {
            $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
        }
        if (empty($fullName)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        }
        if (count($errors) === 0) {
            if (addCustomer($conn, $email, $password, $fullName, $phone, $address, $status)) {
                header("Location: index.php?msg=Thêm khách hàng thành công!");
                exit;
            } else {
                $errors['general'] = "Thêm khách hàng thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Xử lý xóa khách hàng.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $customer_id ID khách hàng cần xóa.
 */
function processDeleteCustomer($conn, $customer_id) {
    if (deleteCustomer($conn, $customer_id)) {
        header("Location: index.php?msg=Xóa khách hàng thành công!");
        exit;
    } else {
        header("Location: index.php?msg=Không thể xóa khách hàng!");
        exit;
    }
}
?>