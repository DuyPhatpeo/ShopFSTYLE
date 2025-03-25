<?php
// controller/authController.php

require_once __DIR__ . '/../includes/config.php';

/**
 * Xử lý đăng ký tài khoản
 */
function register($data) {
    global $pdo;

    $full_name = trim($data['full_name']);
    $email     = trim($data['email']);
    $phone     = trim($data['phone']); // Lấy số điện thoại
    $password  = trim($data['password']);

    // Kiểm tra email đã tồn tại chưa
    $checkSql = "SELECT customer_id FROM customer WHERE email = :email LIMIT 1";
    $checkStmt = $pdo->prepare($checkSql);
    $checkStmt->execute([':email' => $email]);
    if ($checkStmt->fetch()) {
        echo "Email đã được sử dụng, vui lòng chọn email khác!";
        return;
    }

    // Mã hóa mật khẩu
    $hashPass = password_hash($password, PASSWORD_DEFAULT);

    // Tạo mã kích hoạt (6 số) và thời hạn (15 phút)
    $code = mt_rand(100000, 999999);
    $expired = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Lưu DB với status=0 (chưa kích hoạt), lưu thêm số điện thoại
    $sql = "INSERT INTO customer (email, password, full_name, phone, status, verify_code, verify_expire)
            VALUES (:email, :password, :full_name, :phone, 0, :verify_code, :verify_expire)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':email'         => $email,
        ':password'      => $hashPass,
        ':full_name'     => $full_name,
        ':phone'         => $phone,
        ':verify_code'   => $code,
        ':verify_expire' => $expired
    ]);

    // Gửi email (dùng mail() đơn giản; thay thế bằng PHPMailer nếu cần)
    $subject = "Kích hoạt tài khoản";
    $message = "Xin chào $full_name,\n"
             . "Cảm ơn bạn đã đăng ký. Mã kích hoạt tài khoản của bạn là: $code\n"
             . "Vui lòng nhập mã này trong vòng 15 phút để kích hoạt tài khoản.";
    mail($email, $subject, $message);

    // Chuyển hướng sang trang xác thực
    header("Location: /views/auth/verify.php?email=" . urlencode($email));
    exit;
}

/**
 * Xử lý đăng nhập tài khoản
 */
function login($data) {
    global $pdo;

    $email    = trim($data['email']);
    $password = trim($data['password']);

    $sql = "SELECT * FROM customer WHERE email = :email LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Tài khoản không tồn tại!";
        return;
    }

    if (!password_verify($password, $user['password'])) {
        echo "Mật khẩu không chính xác!";
        return;
    }

    if ($user['status'] == 0) {
        echo "Tài khoản chưa được kích hoạt. Vui lòng kiểm tra email!";
        return;
    }

    // Đăng nhập thành công
    session_start();
    $_SESSION['customer_id'] = $user['customer_id'];
    $_SESSION['full_name']   = $user['full_name'];

    header("Location: /index.php");
    exit;
}

/**
 * Xử lý xác thực tài khoản
 */
function verifyAccount($data) {
    global $pdo;

    $email = trim($data['email']);
    $code  = trim($data['verify_code']);

    $sql = "SELECT * FROM customer WHERE email = :email AND status = 0 LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Tài khoản không tồn tại hoặc đã được kích hoạt.";
        return;
    }

    if ($user['verify_code'] != $code) {
        echo "Mã xác thực không đúng!";
        return;
    }

    $now = date('Y-m-d H:i:s');
    if ($user['verify_expire'] < $now) {
        echo "Mã xác thực đã hết hạn!";
        return;
    }

    // Kích hoạt tài khoản
    $updateSql = "UPDATE customer SET status = 1, verify_code = NULL, verify_expire = NULL
                  WHERE customer_id = :id";
    $updateStmt = $pdo->prepare($updateSql);
    $updateStmt->execute([':id' => $user['customer_id']]);

    echo "Kích hoạt thành công! Bạn có thể <a href='/views/auth/login.php'>đăng nhập</a>.";
}

/**
 * Điều hướng theo action
 */
$action = $_GET['action'] ?? '';
switch ($action) {
    case 'register':
        register($_POST);
        break;
    case 'login':
        login($_POST);
        break;
    case 'verify':
        verifyAccount($_POST);
        break;
    default:
        echo "No valid action!";
        break;
}