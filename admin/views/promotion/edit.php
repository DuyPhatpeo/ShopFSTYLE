<?php 
$pageTitle = "Trang chỉnh sửa khuyến mãi";

include("../../includes/session_check.php");
ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/promotionController.php');

$promotion_id = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($promotion_id)) {
    die("ID khuyến mãi không hợp lệ.");
}

// Lấy thông tin khuyến mãi hiện tại
$currentPromotion = getPromotionById($conn, $promotion_id);
if (!$currentPromotion) {
    die("Khuyến mãi không tồn tại.");
}

// Gọi hàm xử lý cập nhật, trả về mảng lỗi nếu có
$errors = processEditPromotion($conn, $promotion_id);

// Nếu chưa có dữ liệu POST, chuyển đổi định dạng ngày của CSDL cho input type="date"
$start_date_value = isset($_POST['start_date']) 
    ? htmlspecialchars($_POST['start_date']) 
    : date("Y-m-d", strtotime($currentPromotion['start_date']));
    
$end_date_value = isset($_POST['end_date']) 
    ? htmlspecialchars($_POST['end_date']) 
    : date("Y-m-d", strtotime($currentPromotion['end_date']));
?>

<div id="notificationContainer" class="fixed top-8 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-3xl font-bold">Chỉnh sửa Khuyến Mãi</h1>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 
                         416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 224 214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
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

    <!-- Form chỉnh sửa khuyến mãi -->
    <form method="POST" action="">
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: Tên, Mã và % Giảm -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Tên khuyến mãi -->
                <div class="mb-4">
                    <label for="promotion_name" class="block mb-1 font-medium">
                        Tên Khuyến Mãi: <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="promotion_name" id="promotion_name"
                        class="w-full p-2 border <?= !empty($errors['promotion_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                        value="<?= isset($_POST['promotion_name']) ? htmlspecialchars($_POST['promotion_name']) : htmlspecialchars($currentPromotion['promotion_name']) ?>">
                    <?php if (!empty($errors['promotion_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['promotion_name']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Mã khuyến mãi -->
                <div class="mb-4">
                    <label for="promotion_code" class="block mb-1 font-medium">
                        Mã Khuyến Mãi: <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="promotion_code" id="promotion_code"
                        class="w-full p-2 border <?= !empty($errors['promotion_code']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                        value="<?= isset($_POST['promotion_code']) ? htmlspecialchars($_POST['promotion_code']) : htmlspecialchars($currentPromotion['promotion_code']) ?>">
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
                        class="w-full p-2 border <?= !empty($errors['discount_value']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                        value="<?= isset($_POST['discount_value']) ? htmlspecialchars($_POST['discount_value']) : htmlspecialchars($currentPromotion['discount_value']) ?>">
                    <?php if (!empty($errors['discount_value'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['discount_value']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải: Ngày Bắt Đầu, Ngày Kết Thúc và Mô Tả -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Ngày bắt đầu -->
                <div class="mb-4">
                    <label for="start_date" class="block mb-1 font-medium">
                        Ngày Bắt Đầu:
                    </label>
                    <input type="date" name="start_date" id="start_date"
                        class="w-full p-2 border border-gray-300 rounded" value="<?= $start_date_value ?>">
                    <?php if (!empty($errors['start_date'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['start_date']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Ngày kết thúc -->
                <div class="mb-4">
                    <label for="end_date" class="block mb-1 font-medium">
                        Ngày Kết Thúc:
                    </label>
                    <input type="date" name="end_date" id="end_date" class="w-full p-2 border border-gray-300 rounded"
                        value="<?= $end_date_value ?>">
                    <?php if (!empty($errors['end_date'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['end_date']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Mô tả (tuỳ chọn) -->
                <div class="mb-4">
                    <label for="description" class="block mb-1 font-medium">
                        Mô Tả (tuỳ chọn):
                    </label>
                    <textarea name="description" id="description" rows="5"
                        class="w-full p-2 border border-gray-300 rounded"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($currentPromotion['description'] ?? '') ?></textarea>
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
                Cập Nhật Khuyến Mãi
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>