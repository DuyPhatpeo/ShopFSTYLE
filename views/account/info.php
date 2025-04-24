<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['customer'])) {
    header('Location: ' . USER_URL . '/views/auth/login.php');
    exit();
}
$customer = $_SESSION['customer'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    include_once __DIR__ . "/../../includes/db.php";
    $customer_id = $customer['customer_id'];
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

<main class="container mx-auto max-w-5xl py-10">
    <div class="flex space-x-6">
        <!-- Sidebar -->
        <aside class="w-1/4 bg-white border border-gray-200 rounded-lg p-6 shadow-md">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Tài khoản của bạn</h2>
            <nav class="space-y-4">
                <a href="<?= USER_URL ?>/views/account/favourite.php"
                    class="block w-full text-center py-2 bg-indigo-500 text-white font-medium rounded-lg hover:bg-indigo-600 transition">❤️
                    Yêu thích</a>
                <a href="<?= USER_URL ?>/views/account/orders.php"
                    class="block w-full text-center py-2 bg-indigo-500 text-white font-medium rounded-lg hover:bg-indigo-600 transition">📜
                    Đơn hàng</a>
            </nav>
        </aside>

        <!-- Content -->
        <section class="flex-1 space-y-10">
            <!-- Thông tin cá nhân -->
            <div class="bg-white shadow-md rounded-xl border border-gray-200 p-6">
                <h1 class="text-2xl font-bold text-gray-800 mb-6">👤 Thông tin cá nhân</h1>
                <?php if (isset($message)): ?>
                <div id="msg"
                    class="mb-6 px-4 py-3 rounded bg-green-100 text-green-800 border border-green-300 shadow-sm">
                    <?= $message ?>
                </div>
                <?php endif; ?>
                <form method="POST" class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-1">Họ và tên</label>
                        <input type="text" name="full_name" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400"
                            value="<?= htmlspecialchars($customer['full_name']) ?>" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" disabled
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
                            class="bg-blue-500 text-white px-5 py-2 rounded-lg hover:bg-blue-600 transition font-semibold">💾
                            Lưu thay đổi</button>
                    </div>
                </form>
            </div>

            <!-- Đổi mật khẩu -->
            <div class="bg-white shadow-md rounded-xl border border-gray-200 p-6">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">🔒 Đổi mật khẩu</h2>
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
                            class="bg-green-500 text-white px-5 py-2 rounded-lg hover:bg-green-600 transition font-semibold">🔄
                            Đổi mật khẩu</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</main>

<?php include __DIR__ . "/../../includes/footer.php"; ?>