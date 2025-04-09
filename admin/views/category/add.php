<?php
$pageTitle = "Trang thêm danh mục";

include("../../includes/session_check.php");

// File: admin/views/category/add.php

ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');               // Kết nối CSDL
require_once('../../controller/categoryController.php');   // Controller danh mục

// Xử lý form thêm danh mục; hàm này trả về mảng lỗi ($errors)
$errors = processAddCategory($conn);

// Hàm lấy danh sách tất cả danh mục cha
function getAllCategories($conn) {
    $sql = "SELECT category_id, category_name FROM category ORDER BY category_name ASC";
    $result = $conn->query($sql);
    $categories = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

$allCategories = getAllCategories($conn);
?>
<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Thêm Danh Mục</h1>
        </div>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <?php if (!empty($errors['general'])): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="flex flex-wrap -mx-2 mb-4">
            <div class="w-full md:w-1/2 px-2">
                <!-- Tên danh mục -->
                <div class="mb-4">
                    <label for="category_name" class="block mb-1 font-medium">
                        Tên danh mục: <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="category_name" id="category_name"
                        class="w-full p-2 border <?= !empty($errors['category_name']) ? 'border-red-500' : 'border-gray-300' ?> rounded"
                        value="<?= isset($_POST['category_name']) ? htmlspecialchars($_POST['category_name']) : '' ?>">
                    <?php if (!empty($errors['category_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['category_name']) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Danh mục cha -->
                <div class="mb-4">
                    <label for="parent_id" class="block mb-1 font-medium">Danh mục cha:</label>
                    <select name="parent_id" id="parent_id" class="w-full p-2 border border-gray-300 rounded">
                        <option value="">-- Không chọn --</option>
                        <?php foreach ($allCategories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category_id']) ?>"
                            <?= (isset($_POST['parent_id']) && $_POST['parent_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Trạng thái -->
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">Trạng thái:</label>
                    <select name="status" id="status"
                        class="w-full p-2 border <?= !empty($errors['status']) ? 'border-red-500' : 'border-gray-300' ?> rounded">
                        <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>On
                        </option>
                        <option value="2" <?= (isset($_POST['status']) && $_POST['status'] == 2) ? 'selected' : '' ?>>
                            Off</option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['status']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ảnh -->
            <div class="w-full md:w-1/2 px-2">
                <label for="image" class="block mb-1 font-medium">Hình ảnh:</label>
                <div id="uploadArea"
                    class="group relative border-2 border-dashed border-gray-300 rounded-lg text-center cursor-pointer hover:border-blue-400 flex items-center justify-center w-full max-w-md h-[300px] mx-auto overflow-hidden"
                    style="position: relative;">
                    <div id="uploadPlaceholder"
                        class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <svg class="h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H20v8H8v20h32V16H28V8z" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Chọn ảnh hoặc kéo thả</p>
                    </div>
                    <img id="imagePreview" src="#" alt="Xem trước ảnh" class="hidden object-contain w-full h-full">
                    <input type="file" name="image" id="image" accept="image/*"
                        class="absolute inset-0 opacity-0 cursor-pointer">
                </div>
                <?php if (!empty($errors['image'])): ?>
                <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['image']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Nút thêm -->
        <div class="flex justify-end">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm1 15h-2v-4H7v-2h4V7h2v4h4v2h-4v4z" />
                </svg>
                Thêm danh mục
            </button>
        </div>
    </form>
</main>

<script>
document.getElementById('image').addEventListener('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var preview = document.getElementById('imagePreview');
            preview.src = event.target.result;
            preview.classList.remove('hidden');
            document.getElementById('uploadPlaceholder').style.display = 'none';
        }
        reader.readAsDataURL(file);
    } else {
        document.getElementById('uploadPlaceholder').style.display = 'flex';
        document.getElementById('imagePreview').classList.add('hidden');
    }
});
var uploadArea = document.getElementById('uploadArea');
uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('bg-gray-100');
});
uploadArea.addEventListener('dragleave', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('bg-gray-100');
});
uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('bg-gray-100');
    var dt = e.dataTransfer;
    var files = dt.files;
    document.getElementById('image').files = files;
    var event = new Event('change');
    document.getElementById('image').dispatchEvent(event);
});
</script>

<?php
include('../../includes/footer.php');
ob_end_flush();