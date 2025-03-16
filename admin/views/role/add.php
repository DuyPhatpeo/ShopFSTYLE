<?php
// Bật output buffering để tránh lỗi "headers already sent"
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php'); // Kết nối CSDL
require_once('../../controller/roleController.php'); // File controller role

// Gọi hàm xử lý thêm vai trò, nếu thêm thành công sẽ chuyển hướng về index.php
$error = processAddRole($conn);
?>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Thêm Vai Trò</h1>
        </div>
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

    <?php if ($error): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Form thêm vai trò -->
    <form method="POST" action="">
        <!-- Chia form thành 2 cột, giống giao diện danh mục -->
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: các trường thông tin -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Tên vai trò -->
                <div class="mb-4">
                    <label for="role_name" class="block mb-1 font-medium">Tên vai trò:</label>
                    <input type="text" name="role_name" id="role_name" class="w-full p-2 border border-gray-300 rounded"
                        value="<?= isset($_POST['role_name']) ? htmlspecialchars($_POST['role_name']) : '' ?>" required>
                </div>

                <!-- Trạng thái -->
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">Trạng thái:</label>
                    <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded">
                        <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>
                            Hoạt động
                        </option>
                        <option value="2" <?= (isset($_POST['status']) && $_POST['status'] == 2) ? 'selected' : '' ?>>
                            Không hoạt động
                        </option>
                    </select>
                </div>
            </div>

            <!-- Cột phải: (Role không cần upload ảnh) -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Nếu bạn muốn thêm mô tả hoặc phân quyền, bạn có thể thêm ở đây. -->
                <div class="mb-4">
                    <label for="description" class="block mb-1 font-medium">Mô tả (tuỳ chọn):</label>
                    <textarea name="description" id="description" rows="5"
                        class="w-full p-2 border border-gray-300 rounded"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                </div>
            </div>
        </div>

        <!-- Nút thao tác -->
        <div class="flex justify-end items-center">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12
                    19c-1.66 0-3-1.34-3-3s1.34-3 3-3
                    3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Thêm vai trò
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>