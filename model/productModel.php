<?php

class ProductModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy sản phẩm theo category_id kiểu UUID (string)
    public function getProductsByCategoryUUID($category_id) {
        $query = "SELECT 
                      product_id,
                      product_name,
                      original_price,
                      discount_price,
                      main_image AS image_url
                  FROM product 
                  WHERE category_id = ? AND status = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $category_id); // "s" cho kiểu string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy chi tiết sản phẩm (với đầy đủ trường) theo product_id (UUID/string)
    public function getProductDetail($product_id) {
        $query = "SELECT
                    p.product_id,
                    p.product_name,
                    p.product_slug,
                    p.description,
                    p.original_price,
                    p.discount_price,
                    p.brand_id,
                    p.category_id,
                    p.created_at,
                    p.status,
                    p.main_image
                  FROM product p
                  WHERE p.product_id = ? AND p.status = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $product_id); // UUID là string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Lấy tổng số lượng tồn kho (tất cả biến thể) của sản phẩm dựa trên product_id
    public function getProductStock($product_id) {
        $query = "SELECT SUM(quantity) AS total_quantity 
                  FROM product_variants 
                  WHERE product_id = ? AND status = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['total_quantity'] ?? 0;
    }

    // [Tuỳ chọn] Lấy danh sách các biến thể (color_id, size_id, quantity) của sản phẩm
    public function getProductVariants($product_id) {
        $query = "SELECT variant_id, color_id, size_id, quantity, status
                  FROM product_variants
                  WHERE product_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>