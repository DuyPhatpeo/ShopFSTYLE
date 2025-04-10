<?php
// controllers/customerController.php
require_once __DIR__ . '/../model/customerModel.php';

function processEditCustomer($conn, $customer_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customerData = getCustomerById($conn, $customer_id);
        if (!$customerData) {
            $errors['general'] = "Không tìm thấy khách hàng.";
            return $errors;
        }
        
        $email           = $_POST['email'] ?? '';
        $fullName        = $_POST['full_name'] ?? '';
        $phone           = $_POST['phone'] ?? '';
        $address         = $_POST['address'] ?? '';
        $status          = (int) ($_POST['status'] ?? 1);
        $newPassword     = trim($_POST['new_password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } elseif (isEmailExists($conn, $email, $customer_id)) {
            $errors['email'] = "Email đã tồn tại.";
        }

        if (empty($fullName)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        }

        if ($newPassword !== '' || $confirmPassword !== '') {
            if (strlen($newPassword) < 6) {
                $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
            }
            if ($newPassword !== $confirmPassword) {
                $errors['confirm_password'] = "Mật khẩu và xác nhận không khớp.";
            }
        }

        if (empty($errors)) {
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

function processAddCustomer($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $address  = trim($_POST['address'] ?? '');
        $status   = (int) ($_POST['status'] ?? 1);

        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } elseif (isEmailExists($conn, $email)) {
            $errors['email'] = "Email đã tồn tại.";
        }

        if (empty($password)) {
            $errors['password'] = "Mật khẩu không được để trống.";
        } elseif (strlen($password) < 6) {
            $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
        }

        if (empty($fullName)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        }

        if (empty($errors)) {
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

function processDeleteCustomer($conn, $customer_id) {
    if (deleteCustomer($conn, $customer_id)) {
        header("Location: index.php?msg=Xóa khách hàng thành công!");
    } else {
        header("Location: index.php?msg=Không thể xóa khách hàng!");
    }
    exit;
}