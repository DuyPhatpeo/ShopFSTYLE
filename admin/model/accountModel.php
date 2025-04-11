<?php
// File: admin/model/accountModel.php

require_once __DIR__ . '/../controller/stringHelper.php';


function isUsernameExists($conn, $username, $excludeId = null) {
    $sql = $excludeId 
        ? "SELECT COUNT(*) as count FROM admin WHERE username = ? AND admin_id != ?"
        : "SELECT COUNT(*) as count FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $excludeId ? $stmt->bind_param("ss", $username, $excludeId) : $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'] > 0;
}

function isEmailExists($conn, $email, $excludeId = null) {
    $sql = $excludeId 
        ? "SELECT COUNT(*) as count FROM admin WHERE email = ? AND admin_id != ?"
        : "SELECT COUNT(*) as count FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $excludeId ? $stmt->bind_param("ss", $email, $excludeId) : $stmt->bind_param("s", $email);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['count'] > 0;
}

function getAdminsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $searchParam = "%" . trim($search) . "%";

    $stmtCount = $conn->prepare("SELECT COUNT(*) as total FROM admin WHERE username LIKE ? OR full_name LIKE ?");
    $stmtCount->bind_param("ss", $searchParam, $searchParam);
    $stmtCount->execute();
    $totalAdmins = (int)($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
    $totalPages = max(1, ceil($totalAdmins / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("
        SELECT a.*, r.role_name 
        FROM admin a 
        LEFT JOIN role r ON a.role_id = r.role_id 
        WHERE a.username LIKE ? OR a.full_name LIKE ?
        ORDER BY a.username ASC
        LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $searchParam, $searchParam, $limit, $offset);
    $stmt->execute();
    return [
        'admins' => $stmt->get_result(),
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalAdmins' => $totalAdmins
    ];
}

function addAdmin($conn, $username, $password, $email, $fullName, $roleId) {
    $admin_id = generateUCCID();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $roleId = $roleId !== "" ? $roleId : null;
    $stmt = $conn->prepare("
        INSERT INTO admin (admin_id, username, password, email, full_name, role_id)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $admin_id, $username, $hashedPassword, $email, $fullName, $roleId);
    return $stmt->execute();
}

function getAdminById($conn, $admin_id) {
    $stmt = $conn->prepare("
        SELECT a.*, r.role_name FROM admin a 
        LEFT JOIN role r ON a.role_id = r.role_id 
        WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateAdmin($conn, $admin_id, $email, $fullName, $roleId, $password = null) {
    $roleId = $roleId !== "" ? $roleId : null;
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            UPDATE admin SET email = ?, full_name = ?, role_id = ?, password = ? WHERE admin_id = ?");
        $stmt->bind_param("sssss", $email, $fullName, $roleId, $hashedPassword, $admin_id);
    } else {
        $stmt = $conn->prepare("
            UPDATE admin SET email = ?, full_name = ?, role_id = ? WHERE admin_id = ?");
        $stmt->bind_param("ssss", $email, $fullName, $roleId, $admin_id);
    }
    return $stmt->execute();
}

function deleteAdmin($conn, $admin_id) {
    $stmt = $conn->prepare("DELETE FROM admin WHERE admin_id = ?");
    if (!$stmt) {
        error_log("Lỗi prepare: " . $conn->error);
        return false;
    }
    $stmt->bind_param("s", $admin_id);
    return $stmt->execute();
}

function getAllRoles($conn) {
    $roles = [];
    $result = $conn->query("SELECT role_id, role_name FROM role ORDER BY role_name ASC");
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
    return $roles;
}

function loginAdmin($conn, $identifier, $password) {
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $identifier, $identifier);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    return ($admin && password_verify($password, $admin['password'])) ? $admin : false;
}