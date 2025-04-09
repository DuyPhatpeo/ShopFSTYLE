<?php
// File: admin/controller/roleController.php

require_once __DIR__ . '/../model/roleModel.php';

/**
 * Xử lý thêm role từ form.
 */
function processAddRole($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $roleName = trim($_POST['role_name'] ?? '');
        $status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;

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
 * Xử lý chỉnh sửa role từ form.
 */
function processEditRole($conn, $role_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $roleName = trim($_POST['role_name'] ?? '');
        $status   = isset($_POST['status']) ? (int)$_POST['status'] : 1;

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
            if (updateRole($conn, $role_id, $roleName, $status)) {
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
 * Xử lý xóa role từ form.
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