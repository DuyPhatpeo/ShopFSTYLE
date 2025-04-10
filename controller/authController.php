<?php
// File: controller/authController.php
session_start();
require_once(__DIR__ . '/../includes/db.php');        // Kết nối CSDL
require_once(__DIR__ . '/../model/customerModel.php'); // Các hàm thao tác DB cho khách hàng

$action = $_GET['action'] ?? '';

if ($action === 'logout') {
    session_unset();
    session_destroy();

    // Xoá cookie nếu có
    if (isset($_COOKIE['email'])) {
        setcookie('email', '', time() - 3600, "/");
    }
    if (isset($_COOKIE['password'])) {
        setcookie('password', '', time() - 3600, "/");
    }

    header("Location: ../views/auth/login.php"); // hoặc index.php nếu bạn muốn
    exit;
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Xử lý đăng ký
    if ($action === "register") {
        // Lấy thông tin từ form
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $errors = [];
        $_SESSION['old'] = $_POST;

        // Validate thông tin
        if (empty($full_name)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        } elseif (preg_match('/[^a-zA-ZÀ-Ỷà-ỹ0-9\s]/u', $full_name)) {
            $errors['full_name'] = "Họ và tên không được chứa ký tự đặc biệt.";
        }

        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } elseif (checkEmailExists($conn, $email)) {
            $errors['email'] = "Email đã tồn tại.";
        }

        if (empty($password)) {
            $errors['password'] = "Mật khẩu không được để trống.";
        } elseif (strlen($password) < 6) {
            $errors['password'] = "Mật khẩu phải có ít nhất 6 ký tự.";
        }

        if (empty($confirm)) {
            $errors['confirm_password'] = "Vui lòng xác nhận mật khẩu.";
        } elseif ($password !== $confirm) {
            $errors['confirm_password'] = "Mật khẩu nhập lại không khớp.";
        }

        if (empty($phone)) {
            $errors['phone'] = "Số điện thoại không được để trống.";
        } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
            $errors['phone'] = "Số điện thoại không hợp lệ (10 số).";
        }

        if (empty($address)) {
            $errors['address'] = "Địa chỉ không được để trống.";
        }

        if (!empty($errors)) {
            $errors['general'] = "Vui lòng kiểm tra lại thông tin.";
            $_SESSION['errors'] = $errors;
            header("Location: ../views/auth/register.php");
            exit;
        }

        // Tạo dữ liệu khách hàng
        $verify_code = generateVerificationCode();
        $customer = [
            'customer_id' => generateUUID(),
            'full_name'   => $full_name,
            'email'       => $email,
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'phone'       => $phone,
            'address'     => $address,
            'verify_code' => $verify_code,
            'is_verified' => 0,
            'status'      => 0
        ];

        if (addCustomer($conn, $customer)) {
            require_once(__DIR__ . '/../services/sendMailService.php');
            sendVerificationCode($email, $verify_code);
            $_SESSION['success'] = "Đăng ký thành công. Vui lòng kiểm tra email để xác nhận tài khoản.";
            unset($_SESSION['old']);
            header("Location: ../views/auth/verify.php?email=" . urlencode($email));
            exit;
        } else {
            $_SESSION['errors']['general'] = "Đăng ký thất bại. Vui lòng thử lại.";
            header("Location: ../views/auth/register.php");
            exit;
        }
    }
    // Xử lý xác nhận mã
    elseif ($action === "verify") {
        $email = trim($_POST['email'] ?? '');
        $code = trim($_POST['verify_code'] ?? '');

        if (verifyCustomerCode($conn, $email, $code)) {
            $_SESSION['success'] = "Xác nhận thành công! Bạn có thể đăng nhập.";
            header("Location: ../views/auth/login.php");
        } else {
            $_SESSION['errors']['general'] = "Mã xác nhận không đúng hoặc đã hết hạn.";
            header("Location: ../views/auth/verify.php?email=" . urlencode($email));
        }
        exit;
    }
    // Xử lý đăng nhập
    elseif ($action === "login") {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);

        $errors = [];

        // Validate email
        if (empty($email)) {
            $errors['email'] = "Vui lòng nhập email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        }
        
        // Validate password
        if (empty($password)) {
            $errors['password'] = "Vui lòng nhập mật khẩu.";
        }
        
        if (!empty($errors)) {
            $errors['general'] = "Vui lòng kiểm tra lại thông tin.";
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../views/auth/login.php");
            exit;
        }
        
        // Kiểm tra tài khoản
        $user = getCustomerByEmail($conn, $email);
        if (!$user || !password_verify($password, $user['password'])) {
            $errors['general'] = "Email hoặc mật khẩu không chính xác.";
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            header("Location: ../views/auth/login.php");
            exit;
        }
        
        // Đăng nhập thành công, lưu thông tin người dùng vào session
        $_SESSION['customer'] = $user;
        unset($_SESSION['old']);
        $_SESSION['success'] = "Đăng nhập thành công!";

        // Ghi nhớ đăng nhập: lưu email và mật khẩu vào cookie (lưu trong 30 ngày)
        if ($remember_me) {
            setcookie("email", $email, time() + (86400 * 30), "/");
            // Lưu ý: Không nên lưu mật khẩu dạng plain text trong cookie vì rủi ro bảo mật
            setcookie("password", $password, time() + (86400 * 30), "/");
        } else {
            if (isset($_COOKIE['email'])) {
                setcookie("email", "", time() - 3600, "/");
            }
            if (isset($_COOKIE['password'])) {
                setcookie("password", "", time() - 3600, "/");
            }
        }
        
        // Chuyển hướng sau khi đăng nhập thành công
        header("Location: ../views/home/index.php");
        exit;
    }

}
?>