<?php
if (session_status() == PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['customer'])) {
    header('Location: ' . USER_URL . '/views/auth/login.php');
    exit();
}
$customer = $_SESSION['customer'];
include_once __DIR__ . "/../../includes/header.php";
?>

<div class="flex flex-col min-h-screen">

    <main class="flex-grow bg-gray-50">
        <div class="container mx-auto max-w-4xl px-4 py-12">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Sidebar -->
                <aside class="md:w-1/4 bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">👋 Chào bạn!</h2>
                    <p class="text-indigo-600 font-bold text-lg mb-8"><?= htmlspecialchars($customer['full_name']) ?>
                    </p>
                    <nav class="space-y-3">
                        <a href="<?= USER_URL ?>/views/account/favourite.php"
                            class="flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 transition">
                            ❤️ Yêu thích
                        </a>
                        <a href="<?= USER_URL ?>/views/account/orders.php"
                            class="flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 transition">
                            📜 Đơn hàng
                        </a>
                    </nav>
                </aside>

                <!-- Profile Card -->
                <section class="flex-1">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <!-- Avatar & Name -->
                        <div class="flex flex-col items-center mb-8">
                            <div
                                class="w-28 h-28 bg-gray-100 rounded-full flex items-center justify-center text-5xl text-gray-400 mb-4">
                                <?= strtoupper(substr(htmlspecialchars($customer['full_name']), 0, 1)) ?>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($customer['full_name']) ?>
                            </h1>
                            <p class="text-sm text-gray-500 mt-1"><?= htmlspecialchars($customer['email']) ?></p>
                        </div>

                        <!-- Details -->
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="text-2xl">📞</div>
                                <div>
                                    <p class="text-sm text-gray-500">Số điện thoại</p>
                                    <p class="text-lg font-medium text-gray-900">
                                        <?= htmlspecialchars($customer['phone']) ?></p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="text-2xl">🏠</div>
                                <div>
                                    <p class="text-sm text-gray-500">Địa chỉ</p>
                                    <p class="text-lg font-medium text-gray-900">
                                        <?= nl2br(htmlspecialchars($customer['address'])) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>