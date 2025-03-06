<?php
// admin/views/brand/delete.php
ob_start();

// Include header và kết nối CSDL
include("../../includes/header.php");
require_once('../../../includes/db.php'); // Kết nối CSDL
require_once('../../controller/brandContnroller.php'); // File chứa xử lý

// Kiểm tra sự tồn tại của biến GET id
if (!isset($_GET['id'])) {
    header("Location: index.php?msg=invalid_id");
    exit;
}

$brand_id = $_GET['id'];
$brand = getBrandById($conn, $brand_id);
if (!$brand) {
    header("Location: index.php?msg=brand_not_found");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (deleteBrand($conn, $brand_id)) {
        // Nếu bảng có ràng buộc ON DELETE CASCADE, các bản ghi liên quan cũng sẽ bị xóa
        header("Location: index.php?msg=deleted");
        exit;
    } else {
        $error = "Xóa thương hiệu thất bại. Vui lòng thử lại.";
    }
}
?>

<main class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Xóa Thương Hiệu</h1>
    <?php if (isset($error)): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    <p class="mb-4">
        Bạn có chắc chắn muốn xóa thương hiệu:
        <strong><?= htmlspecialchars($brand['brand_name']) ?></strong>?
    </p>
    <form method="POST" action="">
        <div class="flex justify-between items-center">
            <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
                <!-- Icon SVG cho nút Quay lại -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại
            </a>
            <button type="submit" class="bg-red-700 hover:bg-red-800 text-white p-2 rounded flex items-center">
                <!-- Icon SVG cho nút Xác nhận xoá (Trash icon) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1H9V4a1 1 0 011-1z" />
                </svg>
                Xác nhận xoá
            </button>
        </div>
    </form>
</main>

<?php
include("../../includes/footer.php");
ob_end_flush();
?>