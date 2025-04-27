<?php
require_once __DIR__ . '/../model/commentModel.php';

class CommentController {
    private $commentModel;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->commentModel = new CommentModel($conn);
    }

    // Thêm bình luận
    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'];
            $customer_id = $_POST['customer_id']; // Chú ý thay thế bằng ID khách hàng thực tế khi đăng nhập
            $content = $_POST['content'];
            $rating = $_POST['rating'];

            // Thêm bình luận mới
            $comment_id = $this->commentModel->addComment($product_id, $customer_id, $content, $rating);
            
            if ($comment_id) {
                return ['status' => 'success', 'message' => 'Bình luận đã được thêm thành công.'];
            } else {
                return ['status' => 'error', 'message' => 'Có lỗi khi thêm bình luận.'];
            }
        }
    }

    // Lấy tất cả bình luận của sản phẩm
    public function getComments($product_id) {
        return $this->commentModel->getCommentsByProduct($product_id);
    }

    // Lấy điểm đánh giá trung bình của sản phẩm
    public function getAverageRating($product_id) {
        return $this->commentModel->getAverageRating($product_id);
    }

    // Lấy số lượng bình luận của sản phẩm
    public function countComments($product_id) {
        return $this->commentModel->countComments($product_id);
    }
}
?>