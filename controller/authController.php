<?php
// Nếu session chưa khởi tạo, khởi tạo ngay
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Sử dụng __DIR__ để tính đường dẫn tuyệt đối
require_once(__DIR__ . '/../includes/db.php');         // Kết nối CSDL qua PDO
require_once(__DIR__ . '/../model/customerModel.php');   // Các hàm tương tác bảng customer

$action = $_GET['action'] ?? '';
$errors = [];
$_SESSION['old'] = $_POST;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($action === "register") {
        // Lấy thông tin từ form
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        // Validate từng trường
        if (empty($full_name)) {
            $errors['full_name'] = "Họ và tên không được để trống.";
        } elseif (preg_match('/[^a-zA-ZÀ-Ỷà-ỹ0-9\s]/u', $full_name)) {
            $errors['full_name'] = "Họ và tên không được chứa ký tự đặc biệt.";
        }

        // Kiểm tra email với biểu thức chính quy
        if (empty($email)) {
            $errors['email'] = "Email không được để trống.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        } elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $email)) {
            $errors['email'] = "Email không hợp lệ (đảm bảo có ký tự @ và dấu chấm).";
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
        $customer = [
            'customer_id' => generateUUID(),
            'full_name'   => $full_name,
            'email'       => $email,
            'password'    => password_hash($password, PASSWORD_DEFAULT),
            'phone'       => $phone,
            'address'     => $address,
            'status'      => 1
        ];

        if (addCustomer($conn, $customer)) {
            $_SESSION['success'] = "Đăng ký thành công. Vui lòng đăng nhập.";
            unset($_SESSION['old']);
            header("Location: ../views/auth/login.php");
            exit;
        } else {
            $errors['general'] = "Đăng ký thất bại. Vui lòng thử lại.";
            $_SESSION['errors'] = $errors;
            header("Location: ../views/auth/register.php");
            exit;
        }
    }
    elseif ($action === "login") {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']);  // Kiểm tra nếu người dùng chọn "Ghi nhớ tôi"

        // Kiểm tra email với biểu thức chính quy
        if (empty($email)) {
            $errors['email'] = "Vui lòng nhập email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Email không hợp lệ.";
        }

        if (empty($password)) {
            $errors['password'] = "Vui lòng nhập mật khẩu.";
        }

        if (!empty($errors)) {
            $errors['general'] = "Vui lòng kiểm tra lại thông tin.";
            $_SESSION['errors'] = $errors;
            header("Location: ../views/auth/login.php");
            exit;
        }

        // Kiểm tra thông tin đăng nhập
        $user = getCustomerByEmail($conn, $email);
        if (!$user || !password_verify($password, $user['password'])) {
            $errors['general'] = "Email hoặc mật khẩu không đúng.";
            $_SESSION['errors'] = $errors;
            header("Location: ../views/auth/login.php");
            exit;
        }

        // Đăng nhập thành công, lưu thông tin vào session
        $_SESSION['customer'] = $user;
        unset($_SESSION['old']);
        $_SESSION['success'] = "Đăng nhập thành công!";

        // Nếu người dùng chọn "Ghi nhớ tôi", lưu vào cookie
        if ($remember_me) {
            setcookie('email', $email, time() + (86400 * 30), "/"); // 30 ngày
            setcookie('password', $password, time() + (86400 * 30), "/"); // 30 ngày
        } else {
            // Nếu không chọn "Ghi nhớ tôi", xóa cookie (nếu có)
            setcookie('email', '', time() - 3600, "/");
            setcookie('password', '', time() - 3600, "/");
        }

        header("Location: ../views/home/index.php");
        exit;
    }
}
?>