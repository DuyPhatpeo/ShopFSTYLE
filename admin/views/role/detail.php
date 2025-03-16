<?php
// admin/views/role/detail.php

ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/roleController.php');

$role_id = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($role_id)) {
    die("ID vai trò không hợp lệ.");
}

// Lấy chi tiết role
$role = getRoleDetail($conn, $role_id);
if (!$role) {
    die("Vai trò không tồn tại.");
}
?>

<main class="container mx-auto p-6">
    <!-- Header với tiêu đề và các nút hành động (căn lề phải) -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Chi Tiết Vai Trò</h1>
        <div class="inline-flex gap-2">
            <!-- Nút Sửa (màu vàng) -->
            <a href="edit.php?id=<?= urlencode($role['role_id']) ?>"
                class="bg-yellow-500 hover:bg-yellow-600 text-white flex items-center justify-center rounded px-2 py-2 w-10 sm:w-auto"
                title="Chỉnh sửa">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                </svg>
                <span class="hidden md:inline-block ml-1">Sửa</span>
            </a>
            <!-- Nút Xóa -->
            <a href="delete.php?id=<?= urlencode($role['role_id']) ?>"
                onclick="return confirm('Bạn có chắc muốn xóa vai trò này?');"
                class="bg-red-500 hover:bg-red-600 text-white flex items-center justify-center rounded px-2 py-2 w-10 sm:w-auto"
                title="Xóa">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 6h18"></path>
                    <path d="M8 6V4a2 2 0 012-2h4a2 2 0 012 2v2"></path>
                    <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"></path>
                    <path d="M10 11v6"></path>
                    <path d="M14 11v6"></path>
                </svg>
                <span class="hidden md:inline-block ml-1">Xóa</span>
            </a>
            <!-- Nút Quay lại (màu xanh dương như yêu cầu) -->
            <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center"
                title="Quay lại">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                    <path
                        d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 224 214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
                </svg>
                <span class="hidden md:inline-block">Quay lại</span>
            </a>
        </div>
    </div>

    <!-- Nội dung chi tiết -->
    <div class="bg-white shadow-md rounded p-6">
        <p class="mb-3">
            <strong>ID:</strong>
            <?= htmlspecialchars($role['role_id']) ?>
        </p>
        <p class="mb-3">
            <strong>Tên Vai Trò:</strong>
            <?= htmlspecialchars($role['role_name']) ?>
        </p>
        <p class="mb-3">
            <strong>Trạng Thái:</strong>
            <?php if ($role['status'] == 1): ?>
            <span class="px-2 py-1 bg-green-200 text-green-800 rounded">Hoạt động</span>
            <?php else: ?>
            <span class="px-2 py-1 bg-red-200 text-red-800 rounded">Không hoạt động</span>
            <?php endif; ?>
        </p>
    </div>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>