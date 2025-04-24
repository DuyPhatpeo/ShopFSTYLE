<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
$pageTitle = "Đăng ký - FStyle";
include("../../includes/header.php");
?>

<div class="flex min-h-screen justify-center items-center bg-gradient-to-tr from-green-100 via-blue-50 to-white px-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8">
        <h2 class="text-3xl font-bold text-center text-green-600 mb-6">Đăng ký</h2>

        <?php if (!empty($errors['general'])): ?>
        <div class="bg-red-100 text-red-700 border border-red-400 px-4 py-3 rounded mb-4 text-sm">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
        <?php endif; ?>

        <form action="../../controller/authController.php?action=register" method="post" class="space-y-6">
            <input type="hidden" name="action" value="register">

            <!-- Họ tên -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Họ tên <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" placeholder="Nguyễn Văn A"
                    value="<?= htmlspecialchars($old['name'] ?? '') ?>"
                    class="w-full px-4 py-3 border rounded-xl shadow-sm text-sm transition focus:outline-none focus:ring-2 <?= isset($errors['name']) ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:ring-green-400' ?>">
                <?php if (isset($errors['name'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['name']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" placeholder="your@email.com"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    class="w-full px-4 py-3 border rounded-xl shadow-sm text-sm transition focus:outline-none focus:ring-2 <?= isset($errors['email']) ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:ring-green-400' ?>">
                <?php if (isset($errors['email'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Mật khẩu -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Mật khẩu <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" placeholder="••••••••"
                    class="w-full px-4 py-3 border rounded-xl shadow-sm text-sm transition focus:outline-none focus:ring-2 <?= isset($errors['password']) ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:ring-green-400' ?>">
                <?php if (isset($errors['password'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Nhập lại mật khẩu <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••"
                    class="w-full px-4 py-3 border rounded-xl shadow-sm text-sm transition focus:outline-none focus:ring-2 <?= isset($errors['password_confirmation']) ? 'border-red-500 focus:ring-red-400' : 'border-gray-300 focus:ring-green-400' ?>">
                <?php if (isset($errors['password_confirmation'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password_confirmation']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit"
                class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-xl transition font-semibold text-lg shadow-md">
                Đăng ký
            </button>
        </form>

        <p class="text-sm text-center mt-6 text-gray-600">
            Đã có tài khoản?
            <a href="login.php" class="text-blue-500 hover:underline">Đăng nhập</a>
        </p>
    </div>
</div>

<?php include("../../includes/footer.php"); ?>