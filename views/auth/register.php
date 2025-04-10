<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
$pageTitle = "Đăng ký - FStyle";
include("../../includes/header.php");
?>
<div class="flex flex-col min-h-screen justify-center">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-3xl shadow-lg mt-12 mb-12 w-full sm:w-96">

        <h2 class="text-2xl sm:text-3xl font-bold text-center text-primary mb-6">Tạo tài khoản FStyle</h2>

        <?php if (!empty($errors['general'])): ?>
        <div class="bg-red-100 text-red-700 border border-red-400 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errors['general']) ?>
        </div>
        <?php endif; ?>

        <form action="../../controller/authController.php?action=register" method="post" class="space-y-5">
            <input type="hidden" name="action" value="register">

            <!-- Họ và tên -->
            <div>
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Họ và tên <span class="text-red-500">*</span>
                </label>
                <input type="text" name="full_name" id="full_name" placeholder="Nguyễn Văn A"
                    value="<?= htmlspecialchars($old['full_name'] ?? '') ?>"
                    class="w-full px-4 py-3 border rounded-lg text-sm focus:outline-none <?= isset($errors['full_name']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary' ?>">
                <?php if (isset($errors['full_name'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['full_name']) ?></p>
                <?php endif; ?>
            </div>

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
                    class="w-full px-4 py-3 border rounded-lg pr-10 text-sm focus:outline-none <?= isset($errors['password']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary' ?>">
                <button type="button" onclick="togglePassword('password','icon-password')"
                    class="absolute right-3 top-9 text-gray-600">
                    <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                    </svg>
                </button>
                <?php if (isset($errors['password'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Nhập lại mật khẩu -->
            <div class="relative">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                    Nhập lại mật khẩu <span class="text-red-500">*</span>
                </label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="••••••••"
                    class="w-full px-4 py-3 border rounded-lg pr-10 text-sm focus:outline-none <?= isset($errors['confirm_password']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary' ?>">
                <button type="button" onclick="togglePassword('confirm_password','icon-confirm_password')"
                    class="absolute right-3 top-9 text-gray-600">
                    <svg id="icon-confirm_password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z" />
                    </svg>
                </button>
                <?php if (isset($errors['confirm_password'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['confirm_password']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Số điện thoại -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Số điện thoại <span class="text-red-500">*</span>
                </label>
                <input type="text" name="phone" id="phone" placeholder="0123456789"
                    value="<?= htmlspecialchars($old['phone'] ?? '') ?>"
                    class="w-full px-4 py-3 border rounded-lg text-sm focus:outline-none <?= isset($errors['phone']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary' ?>">
                <?php if (isset($errors['phone'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['phone']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Địa chỉ -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                    Địa chỉ <span class="text-red-500">*</span>
                </label>
                <input type="text" name="address" id="address" placeholder="123 Đường ABC, Quận XYZ"
                    value="<?= htmlspecialchars($old['address'] ?? '') ?>"
                    class="w-full px-4 py-3 border rounded-lg text-sm focus:outline-none <?= isset($errors['address']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary' ?>">
                <?php if (isset($errors['address'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['address']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit"
                class="w-full bg-blue-400 text-white py-3 rounded-lg hover:bg-blue-500 transition font-semibold text-lg">
                Đăng ký
            </button>
        </form>

        <p class="text-sm text-center mt-4">
            Đã có tài khoản? <a href="login.php" class="text-blue-500 underline">Đăng nhập</a>
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