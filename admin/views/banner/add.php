<?php
// Bật output buffering để tránh lỗi "headers already sent"
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');           // Kết nối CSDL
require_once('../../controller/bannerController.php'); // File controller banner (chứa hàm processAddBanner)

// Gọi hàm xử lý thêm banner, nếu thêm thành công hàm sẽ chuyển hướng về index.php (hoặc trang khác tùy chỉnh)
$error = processAddBanner($conn);
?>

<main class="container mx-auto p-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Thêm Banner</h1>
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

    <?php if ($error): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <!-- Form thêm banner có hỗ trợ upload file -->
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột trái: các trường thông tin -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Link banner -->
                <div class="mb-4">
                    <label for="link" class="block mb-1 font-medium">Link Banner:</label>
                    <input type="text" name="link" id="link" class="w-full p-2 border border-gray-300 rounded"
                        value="<?= isset($_POST['link']) ? htmlspecialchars($_POST['link']) : '' ?>"
                        placeholder="https://example.com/...">
                </div>
                <!-- Trạng thái -->
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">Trạng thái:</label>
                    <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded">
                        <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == 1) ? 'selected' : '' ?>>
                            On
                        </option>
                        <option value="2" <?= (isset($_POST['status']) && $_POST['status'] == 2) ? 'selected' : '' ?>>
                            Off
                        </option>
                    </select>
                </div>
            </div>

            <!-- Cột phải: phần upload ảnh -->
            <div class="w-full md:w-1/2 px-2">
                <label for="image" class="block mb-1 font-medium">Hình ảnh:</label>
                <!-- Vùng upload có kích thước cố định, canh giữa -->
                <div id="uploadArea"
                    class="group relative border-2 border-dashed border-gray-300 rounded-lg text-center cursor-pointer
                           hover:border-blue-400 flex items-center justify-center w-[400px] h-[300px] mx-auto overflow-hidden"
                    style="position: relative;">
                    <!-- Placeholder ban đầu -->
                    <div id="uploadPlaceholder"
                        class="absolute flex flex-col items-center justify-center pointer-events-none">
                        <svg class="h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H20v8H8v20h32V16H28V8z" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Chọn ảnh hoặc kéo thả</p>
                    </div>
                    <!-- Phần preview ảnh, ban đầu ẩn -->
                    <img id="imagePreview" src="#" alt="Xem trước ảnh" class="hidden object-contain w-full h-full" />
                    <!-- Input file luôn có mặt -->
                    <input type="file" name="image" id="image" accept="image/*"
                        class="absolute inset-0 opacity-0 cursor-pointer">
                </div>
            </div>
        </div>

        <!-- Nút thao tác -->
        <div class="flex justify-end items-center">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19
                             c-1.66 0-3-1.34-3-3s1.34-3 3-3
                             3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Thêm Banner
            </button>
        </div>
    </form>
</main>

<!-- Script JS để hiển thị preview ảnh và hỗ trợ kéo thả file -->
<script>
// Khi chọn file thông qua input, cập nhật preview và ẩn placeholder
document.getElementById('image').addEventListener('change', function() {
    var file = this.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(event) {
            var preview = document.getElementById('imagePreview');
            preview.src = event.target.result;
            preview.classList.remove('hidden');
            // Ẩn placeholder
            document.getElementById('uploadPlaceholder').style.display = 'none';
        }
        reader.readAsDataURL(file);
    } else {
        // Nếu không có file nào được chọn, hiện lại placeholder và ẩn preview
        document.getElementById('uploadPlaceholder').style.display = 'flex';
        document.getElementById('imagePreview').classList.add('hidden');
    }
});

// Hỗ trợ kéo thả file vào khu vực upload
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
    // Kích hoạt sự kiện change để hiển thị preview
    var event = new Event('change');
    document.getElementById('image').dispatchEvent(event);
});
</script>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>