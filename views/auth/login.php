<?php
// File: views/auth/login.php

session_start();
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
$pageTitle = "Đăng nhập - FStyle";
include("../../includes/header.php");

// Nếu có cookie "email" và "password", lấy giá trị gán vào dữ liệu cũ
if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    $old['email'] = $_COOKIE['email'];
    $old['password'] = $_COOKIE['password'];
}
?>

<div class="flex flex-col min-h-screen justify-center">
    <div class="max-w-full mx-auto bg-white p-6 rounded-3xl shadow-lg mt-12 mb-12 w-full sm:w-96">
        <h2 class="text-2xl sm:text-3xl font-bold text-center text-primary mb-6">Đăng nhập</h2>

        <?php if (!empty($errors['general'])): ?>
        <div class="bg-red-100 text-red-700 border border-red-400 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
        <?php endif; ?>

        <form action="../../controller/authController.php?action=login" method="post" class="space-y-5">
            <!-- Ẩn input action nếu cần -->
            <input type="hidden" name="action" value="login">

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" placeholder="your@email.com"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    class="w-full px-4 py-3 border rounded-lg text-sm focus:outline-none <?= isset($errors['email']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary' ?>">
                <?php if (isset($errors['email'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Mật khẩu -->
            <div class="relative">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    Mật khẩu <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" placeholder="••••••••"
                    value="<?= htmlspecialchars($old['password'] ?? '') ?>"
                    class="w-full px-4 py-3 border rounded-lg pr-10 text-sm focus:outline-none <?= isset($errors['password']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary' ?>">
                <!-- Nút toggle để hiển thị hoặc ẩn mật khẩu -->
                <button type="button" onclick="togglePassword('password','icon-password')"
                    class="absolute right-3 top-9 text-gray-600">
                    <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                    </svg>
                </button>
                <?php if (isset($errors['password'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Ghi nhớ đăng nhập -->
            <div class="flex items-center">
                <input type="checkbox" name="remember_me" id="remember_me" class="mr-2"
                    <?= isset($old['email']) ? 'checked' : '' ?>>
                <label for="remember_me" class="text-sm text-gray-700">Ghi nhớ đăng nhập</label>
            </div>

            <button type="submit"
                class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 transition font-semibold text-lg">
                Đăng nhập
            </button>
        </form>

        <p class="text-sm text-center mt-4">
            Chưa có tài khoản? <a href="register.php" class="text-blue-500 underline">Đăng ký</a>
        </p>
    </div>
</div>

<script>
function togglePassword(fieldId, iconId) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.add("text-blue-700");
    } else {
        input.type = "password";
        icon.classList.remove("text-blue-700");
    }
}
</script>

<?php include("../../includes/footer.php"); ?>