<?php
// File: model/categoryModel.php

class CategoryModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy tất cả danh mục cha (không có parent_id)
    public function getParentCategories() {
        $sql = "SELECT category_id, category_name, image_url FROM category WHERE status = 1 AND (parent_id IS NULL OR parent_id = '')";
        $result = $this->conn->query($sql);
        if (!$result) return false;

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    // Lấy danh mục con theo parent_id
    public function getChildCategories($parent_id) {
        // Đảm bảo parent_id được đưa vào câu truy vấn dưới dạng chuỗi
        $sql = "SELECT category_id, category_name, image_url FROM category WHERE status = 1 AND parent_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        // Bind param để truyền vào UUID (chuỗi)
        $stmt->bind_param("s", $parent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Kiểm tra lỗi truy vấn
        if (!$result) {
            return false;
        }
    
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    
        $stmt->close();
        return $categories;
    }

    // Lấy tất cả danh mục (cha và con)
    public function getAllCategories() {
        $sql = "SELECT category_id, category_name, parent_id, image_url FROM category WHERE status = 1";
        $result = $this->conn->query($sql);
        if (!$result) return false;

        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }

    // Lấy thông tin danh mục theo ID
    public function getCategoryById($categoryId) {
        $sql = "SELECT category_id, category_name, parent_id, image_url FROM category WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    // Cập nhật trạng thái danh mục (active/inactive)
    public function updateCategoryStatus($categoryId, $status) {
        $sql = "UPDATE category SET status = ? WHERE category_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ii", $status, $categoryId);
        return $stmt->execute();
    }
    // Lấy thông tin danh mục theo slug
    public function getCategoriesBySlug($slug) {
        $sql = "SELECT category_id, category_name, parent_id, image_url FROM category WHERE slug = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
    
        $stmt->bind_param("s", $slug);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
        return $categories;
    }
    
}
?>