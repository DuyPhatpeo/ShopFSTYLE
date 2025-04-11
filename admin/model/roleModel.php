<?php
// File: admin/model/roleModel.php

require_once __DIR__ . '/../controller/stringHelper.php';



function getRolesWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $search = trim($search);

    $sqlCount = "SELECT COUNT(*) as total FROM role WHERE role_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $row = $result->fetch_assoc();
    $totalRoles = (int)($row['total'] ?? 0);
    $totalPages = max(1, ceil($totalRoles / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    $sql = "SELECT * FROM role WHERE role_name LIKE ? ORDER BY role_name ASC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    $roles = $stmt->get_result();

    return [
        'roles' => $roles,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'totalRoles' => $totalRoles
    ];
}

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

function addRole($conn, $roleName, $status) {
    $role_id = generateUCCID();
    $sql = "INSERT INTO role (role_id, role_name, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $role_id, $roleName, $status);
    return $stmt->execute();
}

function getRoleById($conn, $role_id) {
    $sql = "SELECT * FROM role WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $role_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateRole($conn, $role_id, $roleName, $status) {
    $sql = "UPDATE role SET role_name = ?, status = ? WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $roleName, $status, $role_id);
    return $stmt->execute();
}

function deleteRole($conn, $role_id) {
    $sql = "DELETE FROM role WHERE role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $role_id);
    return $stmt->execute();
}

function getRoleDetail($conn, $role_id) {
    return getRoleById($conn, $role_id);
}