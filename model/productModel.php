<?php
class ProductModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy sản phẩm theo category_id (UUID)
    public function getProductsByCategoryUUID($category_id) {
        $query = "SELECT product_id, product_name, original_price, discount_price, main_image AS image_url
                  FROM product
                  WHERE category_id = ? AND status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $category_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy chi tiết sản phẩm theo product_id (UUID)
    public function getProductDetail($product_id) {
        $query = "SELECT p.product_id, p.product_name, p.product_slug, p.description,
                         p.original_price, p.discount_price, p.brand_id, p.category_id,
                         p.created_at, p.status, p.main_image
                  FROM product p
                  WHERE p.product_id = ? AND p.status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Tổng tồn kho tất cả biến thể
    public function getProductStock($product_id) {
        $query = "SELECT SUM(quantity) AS total_quantity
                  FROM product_variants
                  WHERE product_id = ? AND status = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['total_quantity'] ?? 0;
    }

    // Lấy biến thể: color + size + quantity
    public function getProductVariants($product_id) {
        $query = "SELECT pv.variant_id, pv.color_id, c.color_name, c.color_code,
                        pv.size_id, s.size_name, pv.quantity, pv.status
                FROM product_variants pv
                LEFT JOIN color c ON pv.color_id = c.color_id AND c.status = 1
                LEFT JOIN sizes s ON pv.size_id = s.size_id
                WHERE pv.product_id = ? AND pv.status = 1
                ORDER BY c.color_name, s.size_name";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $variants = [];
        while ($row = $result->fetch_assoc()) {
            // Nếu có size, thêm vào mảng biến thể
            if ($row['size_id'] !== null) {
                $variants[] = $row;
            } else {
                // Nếu không có size, chỉ thêm màu
                $row['size_name'] = 'Không có kích thước';
                $variants[] = $row;
            }
        }

        return $variants;
    }

}
?>