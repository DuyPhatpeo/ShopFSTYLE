<?php
// Bật output buffering ngay từ đầu để tránh lỗi "headers already sent"
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php'); // Kết nối CSDL
require_once('../../controller/brandCotroller.php'); // File chứa xử lý

// Gọi hàm xử lý thêm thương hiệu (toàn bộ xử lý đã được thực hiện trong controller)
// Nếu thêm thành công, hàm processAddBrand sẽ tự chuyển hướng về index.php
$error = processAddBrand($conn);
?>

<main class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Thêm Thương Hiệu</h1>
    <?php if ($error): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    <form method="POST" action="">
        <!-- Hàng chứa 2 trường: Tên thương hiệu bên trái, Trạng thái bên phải -->
        <div class="flex flex-wrap -mx-2 mb-4">
            <div class="w-full md:w-1/2 px-2">
                <label for="brand_name" class="block mb-1 font-medium">Tên thương hiệu:</label>
                <input type="text" name="brand_name" id="brand_name" class="w-full p-2 border border-gray-300 rounded"
                    value="<?= isset($_POST['brand_name']) ? htmlspecialchars($_POST['brand_name']) : '' ?>">
            </div>
            <div class="w-full md:w-1/2 px-2">
                <label for="status" class="block mb-1 font-medium">Trạng thái:</label>
                <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded">
                    <option value="1">On</option>
                    <option value="2">Off</option>
                </select>
            </div>
        </div>
        <!-- Hai nút "Quay lại" và "Thêm thương hiệu" chia làm 2 bên, canh giữa -->
        <div class="flex justify-between items-center">
            <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
                <!-- Icon SVG cho nút Quay lại -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Quay lại
            </a>
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <!-- Icon SVG mới cho nút Lưu (Floppy disk icon) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3
                    s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Thêm thương hiệu
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php'); 

// Kết thúc output buffering và đẩy nội dung ra trình duyệt
ob_end_flush();
?>