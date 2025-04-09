<?php
// models/customerModel.php

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

function addCustomer($conn, $email, $password, $fullName, $phone, $address, $status = 1) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO customer (email, password, full_name, phone, address, status)
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $email, $hashedPassword, $fullName, $phone, $address, $status);
    return $stmt->execute();
}

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

function getCustomerById($conn, $customer_id) {
    $sql = "SELECT * FROM customer WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

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