<?php
// Bật output buffering ngay từ đầu để tránh lỗi "headers already sent"
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php'); // Kết nối CSDL
require_once('../../controller/categoryController.php'); // File controller danh mục

// Lấy ID danh mục từ URL
$category_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$category_id) {
    header("Location: index.php");
    exit;
}

// Lấy thông tin danh mục cần chỉnh sửa
$category = getCategoryById($conn, $category_id);
if (!$category) {
    echo "Danh mục không tồn tại.";
    exit;
}

// Xử lý form chỉnh sửa danh mục
$error = processEditCategory($conn, $category_id);

// Hàm lấy danh sách danh mục để hiển thị trong dropdown (loại trừ chính danh mục hiện tại)
function getAllCategories($conn, $excludeId) {
    $sql = "SELECT category_id, category_name FROM category WHERE category_id != ? ORDER BY category_name ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $excludeId);
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = [];
    if ($result && $result->num_rows > 0) {
         while ($row = $result->fetch_assoc()){
             $categories[] = $row;
         }
    }
    return $categories;
}

$allCategories = getAllCategories($conn, $category_id);
?>

<main class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Chỉnh sửa Danh Mục</h1>
    <?php if ($error): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    <form method="POST" action="">
        <!-- Hàng chứa trường Tên danh mục và Danh mục cha -->
        <div class="flex flex-wrap -mx-2 mb-4">
            <div class="w-full md:w-1/2 px-2">
                <label for="category_name" class="block mb-1 font-medium">Tên danh mục:</label>
                <input type="text" name="category_name" id="category_name"
                    class="w-full p-2 border border-gray-300 rounded"
                    value="<?= isset($_POST['category_name']) ? htmlspecialchars($_POST['category_name']) : htmlspecialchars($category['category_name']) ?>">
            </div>
            <div class="w-full md:w-1/2 px-2">
                <label for="parent_id" class="block mb-1 font-medium">Danh mục cha:</label>
                <select name="parent_id" id="parent_id" class="w-full p-2 border border-gray-300 rounded">
                    <option value="">-- Không chọn --</option>
                    <?php foreach ($allCategories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat['category_id']) ?>"
                        <?= ((isset($_POST['parent_id']) && $_POST['parent_id'] == $cat['category_id']) || (!isset($_POST['parent_id']) && $category['parent_id'] == $cat['category_id'])) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <!-- Hàng chứa trường Trạng thái -->
        <div class="flex flex-wrap -mx-2 mb-4">
            <div class="w-full md:w-1/2 px-2">
                <label for="status" class="block mb-1 font-medium">Trạng thái:</label>
                <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded">
                    <option value="1"
                        <?= ((isset($_POST['status']) && $_POST['status'] == 1) || (!isset($_POST['status']) && $category['status'] == 1)) ? 'selected' : '' ?>>
                        On</option>
                    <option value="2"
                        <?= ((isset($_POST['status']) && $_POST['status'] == 2) || (!isset($_POST['status']) && $category['status'] == 2)) ? 'selected' : '' ?>>
                        Off</option>
                </select>
            </div>
        </div>
        <!-- Nút "Quay lại" và "Cập nhật danh mục" -->
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
                <!-- Icon SVG cho nút Lưu (Floppy disk icon) -->
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4zM12 19c-1.66 0-3-1.34-3-3
                    s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Cập nhật danh mục
            </button>
        </div>
    </form>
</main>

<?php
include('../../includes/footer.php'); 

// Kết thúc output buffering và đẩy nội dung ra trình duyệt
ob_end_flush();
?>