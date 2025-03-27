<?php 
$pageTitle = "Trang thêm màu";

include("../../includes/session_check.php");
ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');           // Kết nối CSDL
require_once('../../controller/colorController.php'); // File controller color

// Gọi hàm xử lý thêm màu, nếu thành công sẽ chuyển hướng
$errors = processAddColor($conn);
?>
<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Thêm Màu</h1>
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

    <!-- Form thêm màu -->
    <form method="POST" action="">
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: Tên màu và Mã màu -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Tên màu -->
                <div class="mb-4">
                    <label for="color_name" class="block mb-1 font-medium">
                        Tên Màu:
                        <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="color_name" id="color_name"
                        class="w-full p-2 border <?= !empty($errors['color_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['color_name']) ? htmlspecialchars($_POST['color_name']) : '' ?>">
                    <?php if (!empty($errors['color_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['color_name']) ?></p>
                    <?php endif; ?>
                </div>
                <!-- Mã màu và ô preview -->
                <div class="mb-4">
                    <label for="color_code" class="block mb-1 font-medium">
                        Mã Màu:
                        <span class="text-red-600">*</span>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="text" name="color_code" id="color_code"
                            class="w-full p-2 border <?= !empty($errors['color_code']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                            placeholder="#FFFFFF"
                            value="<?= isset($_POST['color_code']) ? htmlspecialchars($_POST['color_code']) : '' ?>">
                        <!-- Ô preview: gradient phân chia chéo -->
                        <div id="color_preview" class="w-16 h-10 rounded border"></div>
                    </div>
                    <?php if (!empty($errors['color_code'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['color_code']) ?></p>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Cột phải: Trạng thái -->
            <div class="w-full md:w-1/2 px-2">
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">
                        Trạng Thái:
                        <span class="text-red-600">*</span>
                    </label>
                    <select name="status" id="status"
                        class="w-full p-2 border <?= !empty($errors['status']) ? 'border-red-500' : 'border-gray-300'; ?> rounded">
                        <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>
                            Hoạt động
                        </option>
                        <option value="2" <?= (isset($_POST['status']) && $_POST['status'] == 2) ? 'selected' : '' ?>>
                            Không hoạt động
                        </option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['status']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Nút submit -->
        <div class="flex justify-end items-center">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Thêm Màu
            </button>
        </div>
    </form>
</main>

<script>
// Cập nhật ô preview khi người dùng nhập mã màu
document.getElementById('color_code').addEventListener('input', function() {
    var code = this.value.trim();
    var preview = document.getElementById('color_preview');
    var codes = code.split(',').map(function(c) {
        return c.trim();
    });
    if (codes.length == 2 && /^#[0-9A-Fa-f]{6}$/.test(codes[0]) && /^#[0-9A-Fa-f]{6}$/.test(codes[1])) {
        // Sử dụng gradient phân chia chéo với góc 135 độ
        preview.style.background = "linear-gradient(135deg, " + codes[0] + " 50%, " + codes[1] + " 50%)";
    } else if (/^#[0-9A-Fa-f]{6}$/.test(codes[0])) {
        preview.style.background = codes[0];
    } else {
        preview.style.background = 'transparent';
    }
});

// Kích hoạt sự kiện khi trang load, nếu ô nhập đã có giá trị
document.addEventListener('DOMContentLoaded', function() {
    var event = new Event('input');
    document.getElementById('color_code').dispatchEvent(event);
});
</script>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>