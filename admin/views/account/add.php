<?php
$pageTitle = "Trang thêm tài khoản";
include("../../includes/session_check.php");
// Bật output buffering để tránh lỗi "headers already sent"
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');         // Kết nối CSDL
require_once('../../controller/accountController.php'); // File controller tài khoản

// Gọi hàm xử lý thêm tài khoản, nếu thêm thành công hàm sẽ chuyển hướng về index.php
$errors = processAddAdmin($conn);

// Lấy danh sách các vai trò từ bảng role để hiển thị trong dropdown
$roles = getAllRoles($conn);
?>
<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Thêm Tài Khoản</h1>
        </div>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <!-- Hiển thị lỗi chung nếu có -->
    <?php if (!empty($errors['general'])): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <!-- Form thêm tài khoản -->
    <form method="POST" action="">
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: Tên đăng nhập, Họ và tên, Email -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Tên đăng nhập -->
                <div class="mb-4">
                    <label for="username" class="block mb-1 font-medium">
                        Tên đăng nhập <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="username" id="username"
                        class="w-full p-2 border <?= !empty($errors['username']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                    <?php if (!empty($errors['username'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['username']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Họ và tên -->
                <div class="mb-4">
                    <label for="full_name" class="block mb-1 font-medium">
                        Họ và tên <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="full_name" id="full_name"
                        class="w-full p-2 border <?= !empty($errors['full_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>">
                    <?php if (!empty($errors['full_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['full_name']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block mb-1 font-medium">
                        Email <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="email" id="email"
                        class="w-full p-2 border <?= !empty($errors['email']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    <?php if (!empty($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải: Mật khẩu, Nhập lại mật khẩu, Vai trò -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Mật khẩu -->
                <div class="mb-4 relative">
                    <label for="password" class="block mb-1 font-medium">
                        Mật khẩu <span class="text-red-600">*</span>
                    </label>
                    <input type="password" name="password" id="password"
                        class="w-full p-2 border <?= !empty($errors['password']) ? 'border-red-500' : 'border-gray-300'; ?> rounded pr-10">
                    <button type="button" onclick="togglePassword('password', 'icon-password')"
                        class="absolute right-2 top-9 text-gray-600">
                        <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <?php if (!empty($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Nhập lại mật khẩu -->
                <div class="mb-4 relative">
                    <label for="confirm_password" class="block mb-1 font-medium">
                        Nhập lại mật khẩu <span class="text-red-600">*</span>
                    </label>
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="w-full p-2 border <?= !empty($errors['confirm_password']) ? 'border-red-500' : 'border-gray-300'; ?> rounded pr-10">
                    <button type="button" onclick="togglePassword('confirm_password', 'icon-confirm_password')"
                        class="absolute right-2 top-9 text-gray-600">
                        <svg id="icon-confirm_password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <?php if (!empty($errors['confirm_password'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['confirm_password']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Vai trò -->
                <div class="mb-4">
                    <label for="role_id" class="block mb-1 font-medium">
                        Vai trò <span class="text-red-600">*</span>
                    </label>
                    <select name="role_id" id="role_id"
                        class="w-full p-2 border <?= !empty($errors['role_id']) ? 'border-red-500' : 'border-gray-300'; ?> rounded">
                        <option value="">-- Chọn vai trò --</option>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?= htmlspecialchars($role['role_id']) ?>"
                            <?= (isset($_POST['role_id']) && $_POST['role_id'] == $role['role_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['role_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors['role_id'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['role_id']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Nút thao tác -->
        <div class="flex justify-end items-center">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3 s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Thêm tài khoản
            </button>
        </div>
    </form>
</main>

<script>
// Hàm chuyển đổi ẩn/hiện mật khẩu và thay đổi icon tương ứng
function togglePassword(fieldId, iconId) {
    var inputField = document.getElementById(fieldId);
    var icon = document.getElementById(iconId);
    if (inputField.type === "password") {
        inputField.type = "text";
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.418 0-8.21-2.943-9.542-7a10.05 10.05 0 012.05-3.45" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2 2l20 20" />`;
    } else {
        inputField.type = "password";
        icon.innerHTML =
            `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
    }
}
</script>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>