<?php
// admin/views/banner/delete.php
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');             // Kết nối CSDL
require_once('../../controller/bannerController.php');  // File xử lý banner

// Kiểm tra sự tồn tại của biến GET id
if (!isset($_GET['id'])) {
    header("Location: index.php?msg=invalid_id");
    exit;
}

$banner_id = $_GET['id'];
$banner = getBannerById($conn, $banner_id);
if (!$banner) {
    header("Location: index.php?msg=banner_not_found");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (deleteBanner($conn, $banner_id)) {
        // Sau khi xóa, chuyển hướng về trang danh sách banner
        header("Location: index.php?msg=deleted_banner");
        exit;
    } else {
        $error = "Xóa banner thất bại. Vui lòng thử lại.";
    }
}
?>

<main class="container mx-auto p-6">
    <!-- Header: Tiêu đề và nút Quay lại -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Xóa Banner</h1>
            <p class="text-sm text-gray-500">ID banner: <?= htmlspecialchars($banner['banner_id']) ?></p>
        </div>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160
                         c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288
                         416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0
                         L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3
                         s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <?php if (isset($error)): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Khung xác nhận xoá với thông tin banner -->
    <div class="bg-white shadow rounded p-6">
        <p class="mb-4 text-lg">
            Bạn có chắc chắn muốn xóa banner:
            <strong><?= (!empty($banner['link']) ? htmlspecialchars($banner['link']) : "Banner không có link") ?></strong>?
        </p>
        <?php if (!empty($banner['image_url'])): ?>
        <div class="mb-4">
            <img src="../../../<?= htmlspecialchars($banner['image_url']) ?>" alt="Banner"
                class="object-contain max-w-xs border rounded">
        </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="flex justify-end items-center space-x-4">
                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white p-2 rounded flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1H9V4a1 1 0 011-1z" />
                    </svg>
                    <span>Xác nhận xoá</span>
                </button>
            </div>
        </form>
    </div>
</main>

<?php
include("../../includes/footer.php");
ob_end_flush();
?>