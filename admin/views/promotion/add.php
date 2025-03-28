<?php
$pageTitle = "Trang thêm khuyến mãi";

include("../../includes/session_check.php");
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');             // Kết nối CSDL
require_once('../../controller/promotionController.php'); // File controller khuyến mãi

// Xử lý thêm khuyến mãi: nếu thành công sẽ chuyển hướng; nếu lỗi trả về mảng lỗi
$errors = processAddPromotion($conn);
?>

<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Thêm Khuyến Mãi</h1>
        </div>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160
                         c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 
                         416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32
                         L109.2 224 214.6 118.6c12.5-12.5 
                         12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <?php if (!empty($errors['general'])): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <!-- Form thêm khuyến mãi -->
    <form method="POST" action="">
        <!-- Chia form thành 2 cột -->
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: Tên, Mã và % giảm -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Tên khuyến mãi -->
                <div class="mb-4">
                    <label for="promotion_name" class="block mb-1 font-medium">
                        Tên khuyến mãi: <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="promotion_name" id="promotion_name"
                        class="w-full p-2 border <?= !empty($errors['promotion_name']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['promotion_name']) ? htmlspecialchars($_POST['promotion_name']) : '' ?>">
                    <?php if (!empty($errors['promotion_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['promotion_name']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Mã khuyến mãi -->
                <div class="mb-4">
                    <label for="promotion_code" class="block mb-1 font-medium">
                        Mã khuyến mãi: <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="promotion_code" id="promotion_code"
                        class="w-full p-2 border <?= !empty($errors['promotion_code']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['promotion_code']) ? htmlspecialchars($_POST['promotion_code']) : '' ?>">
                    <?php if (!empty($errors['promotion_code'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['promotion_code']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- % Giảm -->
                <div class="mb-4">
                    <label for="discount_value" class="block mb-1 font-medium">
                        Giảm (%) <span class="text-red-600">*</span>:
                    </label>
                    <input type="number" step="0.01" name="discount_value" id="discount_value"
                        class="w-full p-2 border <?= !empty($errors['discount_value']) ? 'border-red-500' : 'border-gray-300'; ?> rounded"
                        value="<?= isset($_POST['discount_value']) ? htmlspecialchars($_POST['discount_value']) : '' ?>">
                    <?php if (!empty($errors['discount_value'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['discount_value']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải: Ngày bắt đầu, Ngày kết thúc, và Mô tả -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Ngày bắt đầu -->
                <div class="mb-4">
                    <label for="start_date" class="block mb-1 font-medium">
                        Ngày bắt đầu:
                    </label>
                    <input type="date" name="start_date" id="start_date"
                        class="w-full p-2 border border-gray-300 rounded"
                        value="<?= isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : '' ?>">
                </div>

                <!-- Ngày kết thúc -->
                <div class="mb-4">
                    <label for="end_date" class="block mb-1 font-medium">
                        Ngày kết thúc:
                    </label>
                    <input type="date" name="end_date" id="end_date" class="w-full p-2 border border-gray-300 rounded"
                        value="<?= isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : '' ?>">
                    <?php if (!empty($errors['date_range'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['date_range']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Mô tả (tuỳ chọn) -->
                <div class="mb-4">
                    <label for="description" class="block mb-1 font-medium">
                        Mô tả (tuỳ chọn):
                    </label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full p-2 border border-gray-300 rounded"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
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
                Thêm khuyến mãi
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>