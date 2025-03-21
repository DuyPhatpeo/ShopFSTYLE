<?php
// File: admin/views/role/edit.php

ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/roleController.php');

$role_id = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($role_id)) {
    die("ID vai trò không hợp lệ.");
}

// Lấy thông tin hiện tại
$currentRole = getRoleById($conn, $role_id);
if (!$currentRole) {
    die("Vai trò không tồn tại.");
}

// Gọi hàm xử lý cập nhật, trả về mảng lỗi
$errors = processEditRole($conn, $role_id);
?>
<div id="notificationContainer" class="fixed top-8 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Sửa Vai Trò</h1>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 224 214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
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

    <!-- Form chỉnh sửa vai trò -->
    <form method="POST" action="">
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: Tên vai trò và Trạng thái -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Tên vai trò -->
                <div class="mb-4">
                    <label for="role_name" class="block mb-1 font-medium">
                        Tên Vai Trò:
                        <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="role_name" id="role_name"
                        class="w-full p-2 border <?= !empty($errors['role_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                        value="<?= isset($_POST['role_name']) ? htmlspecialchars($_POST['role_name']) : htmlspecialchars($currentRole['role_name']) ?>">
                    <?php if (!empty($errors['role_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['role_name']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Trạng thái -->
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">
                        Trạng Thái:
                        <span class="text-red-600">*</span>
                    </label>
                    <select name="status" id="status"
                        class="w-full p-2 border <?= !empty($errors['status']) ? 'border-red-500' : 'border-gray-300' ?> rounded">
                        <option value="1"
                            <?= ((isset($_POST['status']) && $_POST['status'] == 1) || (!isset($_POST['status']) && $currentRole['status'] == 1)) ? 'selected' : '' ?>>
                            Hoạt động
                        </option>
                        <option value="2"
                            <?= ((isset($_POST['status']) && $_POST['status'] == 2) || (!isset($_POST['status']) && $currentRole['status'] == 2)) ? 'selected' : '' ?>>
                            Không hoạt động
                        </option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['status']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải: Mô tả (tuỳ chọn) -->
            <div class="w-full md:w-1/2 px-2">
                <div class="mb-4">
                    <label for="description" class="block mb-1 font-medium">Mô tả (tuỳ chọn):</label>
                    <textarea name="description" id="description" rows="5"
                        class="w-full p-2 border border-gray-300 rounded"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($currentRole['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Nút cập nhật -->
        <div class="flex justify-end">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3
                             s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Cập Nhật Vai Trò
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>