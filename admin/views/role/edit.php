<?php
// admin/views/role/edit.php

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

// Gọi hàm xử lý cập nhật
$error = processEditRole($conn, $role_id);
?>
<div id="notificationContainer" class="fixed top-8 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Sửa Vai Trò</h1>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160
                         160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8
                         0-45.3L109.2 288 416 288c17.7 0 32-14.3
                         32-32s-14.3-32-32-32L109.2 224 214.6
                         118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3
                         0l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <!-- Hiển thị lỗi nếu có -->
    <?php if ($error): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Form chỉnh sửa vai trò, chia thành 2 cột giống giao diện "add" -->
    <form method="POST" action="">
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: Tên vai trò + Trạng thái -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Tên vai trò -->
                <div class="mb-4">
                    <label for="role_name" class="block mb-1 font-medium">Tên Vai Trò:</label>
                    <!-- Bỏ 'required' để không xuất hiện thông báo mặc định của trình duyệt -->
                    <input type="text" name="role_name" id="role_name" class="w-full p-2 border border-gray-300 rounded"
                        value="<?= htmlspecialchars($currentRole['role_name']) ?>">
                </div>

                <!-- Trạng Thái -->
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">Trạng Thái:</label>
                    <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded">
                        <option value="1" <?= $currentRole['status'] == 1 ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="2" <?= $currentRole['status'] == 2 ? 'selected' : '' ?>>Không hoạt động</option>
                    </select>
                </div>
            </div>

            <!-- Cột phải: Mô tả (tuỳ chọn) -->
            <div class="w-full md:w-1/2 px-2">
                <div class="mb-4">
                    <label for="description" class="block mb-1 font-medium">Mô tả (tuỳ chọn):</label>
                    <!-- Ở đây, nếu bạn muốn lưu mô tả vào DB, cần sửa processEditRole và DB cho phù hợp -->
                    <textarea name="description" id="description" rows="5"
                        class="w-full p-2 border border-gray-300 rounded"><?php
                        // Nếu bạn đang lưu mô tả, hãy lấy $currentRole['description'] thay vì chuỗi rỗng
                        echo isset($currentRole['description'])
                            ? htmlspecialchars($currentRole['description'])
                            : '';
                    ?></textarea>
                </div>
            </div>
        </div>

        <!-- Nút cập nhật -->
        <div class="flex justify-end">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 4v16m8-8H4" />
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