<?php
// File: model/customerModel.php

// Hàm tạo UUID phiên bản đơn giản
function generateUUID() {
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

// Tạo mã xác nhận ngẫu nhiên 6 chữ số
function generateVerificationCode() {
    return rand(100000, 999999);
}

// Kiểm tra email đã tồn tại
function checkEmailExists($conn, $email) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM customer WHERE email = ?");
    if (!$stmt) return false;
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result(); 
    $row = $result->fetch_assoc();
    $stmt->close();
    return $row && $row['count'] > 0;
}

// Thêm khách hàng vào cơ sở dữ liệu
function addCustomer($conn, $customer) {
    $sql = "INSERT INTO customer 
            (customer_id, full_name, email, password, phone, address, verify_code, is_verified, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param("sssssssii", 
        $customer['customer_id'],
        $customer['full_name'],
        $customer['email'],
        $customer['password'],
        $customer['phone'],
        $customer['address'],
        $customer['verify_code'],
        $customer['is_verified'],
        $customer['status']
    );
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Lấy thông tin khách hàng theo email (cho đăng nhập)
function getCustomerByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ?");
    if (!$stmt) return false;
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Xác nhận mã và cập nhật trạng thái kích hoạt tài khoản
function verifyCustomerCode($conn, $email, $code) {
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ? AND verify_code = ?");
    if (!$stmt) return false;
    $stmt->bind_param("ss", $email, $code);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $stmt->close();

    if ($customer) {
        $update = $conn->prepare("UPDATE customer SET is_verified = 1, verify_code = NULL, status = 1 WHERE email = ?");
        $update->bind_param("s", $email);
        return $update->execute();
    }
    return false;
}

?>