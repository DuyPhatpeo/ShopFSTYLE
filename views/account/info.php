<?php
// Khởi tạo session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['customer'])) {
    header('Location: ' . USER_URL . '/views/auth/login.php');
    exit();
}

// Lấy thông tin người dùng từ session
$customer = $_SESSION['customer'];

// Xử lý khi form được gửi đi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy thông tin từ form
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Cập nhật thông tin trong cơ sở dữ liệu (ví dụ sử dụng MySQLi)
    include_once __DIR__ . "/../../includes/db.php";  // Kết nối cơ sở dữ liệu

    $customer_id = $_SESSION['customer']['customer_id'];

    $query = "UPDATE customers SET full_name = ?, phone = ?, address = ? WHERE customer_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('sssi', $full_name, $phone, $address, $customer_id);

    if ($stmt->execute()) {
        // Cập nhật lại session sau khi thay đổi thông tin
        $_SESSION['customer']['full_name'] = $full_name;
        $_SESSION['customer']['phone'] = $phone;
        $_SESSION['customer']['address'] = $address;
        $message = "Cập nhật thông tin thành công!";
    } else {
        $message = "Đã xảy ra lỗi trong quá trình cập nhật thông tin.";
    }
}

include_once __DIR__ . "/../../includes/header.php";  // Include header
?>

<main class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Thông tin cá nhân</h1>
    <div id="msg" class="text-red-600 mb-4"><?= isset($message) ? $message : '' ?></div>

    <!-- Hiển thị thông tin cá nhân -->
    <form method="POST" class="bg-white p-6 shadow rounded mb-8">
        <label>Họ và tên</label>
        <input id="fn" name="full_name" class="w-full p-2 border rounded mb-4"
            value="<?= htmlspecialchars($customer['full_name']) ?>" />

        <label>Email</label>
        <input id="em" name="email" disabled class="w-full p-2 border bg-gray-100 rounded mb-4"
            value="<?= htmlspecialchars($customer['email']) ?>" />

        <label>Số điện thoại</label>
        <input id="ph" name="phone" class="w-full p-2 border rounded mb-4"
            value="<?= htmlspecialchars($customer['phone']) ?>" />

        <label>Địa chỉ</label>
        <textarea id="ad" name="address"
            class="w-full p-2 border rounded mb-4"><?= htmlspecialchars($customer['address']) ?></textarea>

        <button type="submit" class="px-4 py-2 bg-blue-500 text-white">Lưu</button>
    </form>

    <h2 class="text-xl font-bold mb-4">Đổi mật khẩu</h2>
    <form id="frmPass" class="bg-white p-6 shadow rounded" method="POST">
        <label>Mật khẩu cũ</label>
        <input id="old" type="password" name="old_password" class="w-full p-2 border rounded mb-4" />

        <label>Mật khẩu mới</label>
        <input id="newp" type="password" name="new_password" class="w-full p-2 border rounded mb-4" />

        <button type="submit" class="px-4 py-2 bg-green-500 text-white">Đổi</button>
    </form>
</main>

<?php include __DIR__ . "/../../includes/footer.php";  // Include footer ?>