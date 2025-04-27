<?php
// Giả sử bạn đã có $conn (kết nối DB) và require Controller rồi
if (!isset($commentController)) {
    require_once __DIR__ . '/../controller/commentController.php';
    $commentController = new CommentController($conn);
}

// Lấy ID sản phẩm từ URL (?product_id=...)
$product_id = $_GET['product_id'] ?? '';

// Xử lý thêm bình luận
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $response = $commentController->addComment();
    echo "<script>alert('{$response['message']}');</script>";
}

// Lấy điểm đánh giá trung bình và số lượng bình luận
$averageRating = $commentController->getAverageRating($product_id);
$totalComments = $commentController->countComments($product_id);

// Lấy danh sách bình luận
$comments = $commentController->getComments($product_id);
?>

<!-- Container bao bọc toàn bộ phần nội dung bình luận -->
<div class="max-w-7xl mx-auto px-6 py-12">
    <!-- Tiêu đề phần bình luận -->
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Bình luận sản phẩm</h2>

    <!-- Hiển thị điểm trung bình và số lượng bình luận -->
    <p class="text-center text-xl text-gray-600 mb-8">⭐ Trung bình: <strong><?= $averageRating ?>/5</strong> từ
        <strong><?= $totalComments ?></strong> lượt đánh giá
    </p>

    <!-- Form viết đánh giá mới -->
    <div class="bg-white rounded-xl shadow-lg p-8 mb-12">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Viết đánh giá của bạn</h3>

        <form method="POST" class="space-y-6">
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($product_id) ?>">
            <input type="hidden" name="customer_id" value="customer_test_id">
            <!-- TODO: thay bằng ID khách đăng nhập -->

            <!-- Nội dung đánh giá -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Nội dung đánh giá</label>
                <textarea name="content"
                    class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="4" placeholder="Viết cảm nhận của bạn..." required></textarea>
            </div>

            <!-- Chọn số sao -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Đánh giá (số sao)</label>
                <div class="flex space-x-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <label class="flex items-center space-x-1">
                        <input type="radio" name="rating" value="<?= $i ?>" required>
                        <span class="text-yellow-400"><?= str_repeat('⭐', $i) ?></span>
                    </label>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Nút gửi -->
            <div class="text-right">
                <button type="submit" name="add_comment"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg">
                    Gửi đánh giá
                </button>
            </div>
        </form>
    </div>

    <!-- Danh sách bình luận -->
    <div class="space-y-6">
        <?php if (!empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
        <div class="border p-6 rounded-2xl shadow-sm bg-white">
            <div class="flex justify-between items-center mb-3">
                <strong class="text-lg text-gray-800"><?= htmlspecialchars($comment['email']) ?></strong>
                <span class="text-yellow-400 text-xl"><?= str_repeat('⭐', (int)$comment['rating']) ?></span>
            </div>
            <p class="mb-3 text-gray-700"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
            <small class="text-gray-400">Đã đăng vào <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></small>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <p class="text-center text-gray-500">Chưa có bình luận nào.</p>
        <?php endif; ?>
    </div>

</div>