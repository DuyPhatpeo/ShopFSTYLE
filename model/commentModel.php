<?php
// file: model/commentModel.php
class CommentModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addComment($product_id, $customer_id, $content, $rating) {
        // Kiểm tra nếu product_id không hợp lệ
        if (empty($product_id) || empty($customer_id) || empty($content) || empty($rating)) {
            return false;
        }
    
        $comment_id = uniqid('cmt_');
        $created_at = date('Y-m-d H:i:s');
        
        // Chuẩn bị và thực thi câu lệnh SQL
        $stmt = $this->conn->prepare("INSERT INTO comment (comment_id, product_id, customer_id, content, rating, created_at) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $comment_id, $product_id, $customer_id, $content, $rating, $created_at);
        
        return $stmt->execute() ? $comment_id : false;
    }
    

    // Lấy tất cả bình luận của 1 sản phẩm
    public function getCommentsByProduct($product_id) {
        // Sửa tên bảng từ 'commentsc' thành 'comment'
        $sql = "SELECT c.*, cu.email, cu.phone
                FROM comment c
                JOIN customer cu ON c.customer_id = cu.customer_id
                WHERE c.product_id = ?
                ORDER BY c.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy 1 bình luận theo ID
    public function getCommentById($comment_id) {
        $stmt = $this->conn->prepare("SELECT * FROM comment WHERE comment_id = ?");
        $stmt->bind_param("s", $comment_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Cập nhật nội dung bình luận
    public function updateComment($comment_id, $newContent, $newRating) {
        $stmt = $this->conn->prepare("UPDATE comment SET content = ?, rating = ? WHERE comment_id = ?");
        $stmt->bind_param("sis", $newContent, $newRating, $comment_id);
        return $stmt->execute();
    }

    // Xóa bình luận
    public function deleteComment($comment_id) {
        $stmt = $this->conn->prepare("DELETE FROM comment WHERE comment_id = ?");
        $stmt->bind_param("s", $comment_id);
        return $stmt->execute();
    }

    // Lấy điểm đánh giá trung bình của 1 sản phẩm
    public function getAverageRating($product_id) {
        $stmt = $this->conn->prepare("SELECT AVG(rating) AS avg_rating FROM comment WHERE product_id = ?");
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        // Kiểm tra nếu không có bình luận thì trả về 0
        return $result ? round((float)$result['avg_rating'], 1) : 0;
    }

    // Đếm số lượng bình luận của 1 sản phẩm
    public function countComments($product_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM comment WHERE product_id = ?");
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result ? (int)$result['total'] : 0;
    }
}
?>