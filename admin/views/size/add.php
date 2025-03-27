<?php
$pageTitle = "Trang thêm Size";

include("../../includes/session_check.php");

// Bật output buffering để tránh lỗi "headers already sent"
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');           // Kết nối CSDL
require_once('../../controller/sizeController.php'); // File controller size

// Gọi hàm xử lý thêm size, nếu thêm thành công sẽ chuyển hướng
// Hàm processAddSize() trả về mảng lỗi (rỗng nếu không có lỗi)
$errors = processAddSize($conn);
?>
<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Thêm Size</h1>
        </div>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 
                    12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 
                    32-32s-14.3-32-32-32L109.2 224 214.6 118.6c12.5-12.5 
                    12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
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

    <!-- Form thêm size -->
    <form method="POST" action="">
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: Tên size -->
            <div class="w-full md:w-1/2 px-2">
                <div class="mb-4">
                    <label for="size_name" class="block mb-1 font-medium">
                        Tên size:
                        <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="size_name" id="size_name"
                        class="w-full p-2 border <?= !empty($errors['size_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['size_name']) ? htmlspecialchars($_POST['size_name']) : '' ?>">
                    <?php if (!empty($errors['size_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['size_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Nút thao tác -->
        <div class="flex justify-end items-center">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3
                             s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Thêm Size
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>