<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['customer'])) {
    header('Location: ' . USER_URL . '/views/auth/login.php');
    exit();
}
$customer = $_SESSION['customer'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    include_once __DIR__ . "/../../includes/db.php";
    $customer_id = $_SESSION['customer']['customer_id'];
    $query = "UPDATE customers SET full_name = ?, phone = ?, address = ? WHERE customer_id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('sssi', $full_name, $phone, $address, $customer_id);
    if ($stmt->execute()) {
        $_SESSION['customer']['full_name'] = $full_name;
        $_SESSION['customer']['phone'] = $phone;
        $_SESSION['customer']['address'] = $address;
        $message = "✅ Cập nhật thông tin thành công!";
    } else {
        $message = "❌ Đã xảy ra lỗi khi cập nhật thông tin.";
    }
}
include_once __DIR__ . "/../../includes/header.php";
?>

<main class="container max-w-3xl mx-auto py-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">👤 Thông tin cá nhân</h1>

    <div class="mb-6">
        <a href="<?= USER_URL ?>/views/account/favourite.php"
            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 transition">
            ❤️ Xem sản phẩm yêu thích
        </a>
    </div>

    <?php if (isset($message)): ?>
    <div id="msg" class="mb-6 px-4 py-3 rounded bg-green-100 text-green-800 border border-green-300 shadow-sm">
        <?= $message ?>
    </div>
    <?php endif; ?>

    <!-- Form thông tin cá nhân -->
    <div class="bg-white shadow-md rounded-xl p-6 mb-10 border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Cập nhật thông tin</h2>
        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1">Họ và tên</label>
                <input type="text" name="full_name" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    value="<?= htmlspecialchars($customer['full_name']) ?>" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Email</label>
                <input type="email" name="email" disabled
                    class="w-full px-4 py-2 border bg-gray-100 rounded-lg text-gray-500 cursor-not-allowed"
                    value="<?= htmlspecialchars($customer['email']) ?>" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Số điện thoại</label>
                <input type="text" name="phone" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                    value="<?= htmlspecialchars($customer['phone']) ?>" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Địa chỉ</label>
                <textarea name="address" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"><?= htmlspecialchars($customer['address']) ?></textarea>
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition">💾 Lưu thay đổi
                </button>
            </div>
        </form>
    </div>

    <!-- Form đổi mật khẩu -->
    <div class="bg-white shadow-md rounded-xl p-6 border border-gray-200">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">🔒 Đổi mật khẩu</h2>
        <form method="POST" action="#" class="space-y-5">
            <div>
                <label class="block text-sm font-medium mb-1">Mật khẩu cũ</label>
                <input type="password" name="old_password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400" />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Mật khẩu mới</label>
                <input type="password" name="new_password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-400" />
            </div>

            <div class="text-right">
                <button type="submit"
                    class="bg-green-500 text-white px-5 py-2 rounded-lg hover:bg-green-600 transition">🔄 Đổi mật khẩu
                </button>
            </div>
        </form>
    </div>
</main>

<?php include __DIR__ . "/../../includes/footer.php"; ?>