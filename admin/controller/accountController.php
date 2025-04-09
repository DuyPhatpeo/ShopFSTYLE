<?php
// File: admin/controller/accountController.php

require_once __DIR__ . '/../model/accountModel.php';

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
        } elseif (isUsernameExists($conn, $username)) {
            $errors['username'] = "Tên đăng nhập đã tồn tại.";
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
        } elseif (isEmailExists($conn, $email)) {
            $errors['email'] = "Email đã tồn tại.";
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

function processEditAdmin($conn, $admin_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $adminData = getAdminById($conn, $admin_id);
        if (!$adminData) {
            $errors['general'] = "Không tìm thấy tài khoản admin.";
            return $errors;
        }

        $email    = trim($_POST['email']);
        $fullName = trim($_POST['full_name']);
        $roleId   = isset($_POST['role_id']) ? trim($_POST['role_id']) : "";
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirm_password']);

        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } elseif (isEmailExists($conn, $email, $admin_id)) {
            $errors['email'] = "Email đã tồn tại.";
        }

        if (empty($fullName)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        } elseif (!preg_match("/^[\p{L}\s]+$/u", $fullName)) {
            $errors['full_name'] = "Họ và tên không được chứa số hoặc ký tự đặc biệt.";
        }

        if (empty($roleId)) {
            $errors['role_id'] = "Vui lòng chọn vai trò.";
        }

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