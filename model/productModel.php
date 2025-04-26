<?php
class ProductModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy sản phẩm theo category_id (UUID)
    public function getProductsByCategoryUUID($category_id) {
        $query = "SELECT product_id, product_name, original_price, discount_price
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
                         p.created_at, p.status
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
            if ($row['size_id'] !== null) {
                $variants[] = $row;
            } else {
                $row['size_name'] = 'Không có kích thước';
                $variants[] = $row;
            }
        }

        return $variants;
    }

    public function getProduct($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getProducts($category_id = null, $search = '', $sort = 'newest', $page = 1, $per_page = 12) {
        $offset = ($page - 1) * $per_page;
        $where = [];
        $params = [];
        $types = '';

        if ($category_id) {
            $where[] = "category_id = ?";
            $params[] = $category_id;
            $types .= 'i';
        }

        if ($search) {
            $where[] = "name LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }

        $where_clause = $where ? "WHERE " . implode(" AND ", $where) : "";

        switch ($sort) {
            case 'price_asc':
                $order_by = "price ASC";
                break;
            case 'price_desc':
                $order_by = "price DESC";
                break;
            case 'name':
                $order_by = "name ASC";
                break;
            default:
                $order_by = "created_at DESC";
        }

        $query = "SELECT * FROM products $where_clause ORDER BY $order_by LIMIT ? OFFSET ?";
        $types .= 'ii';
        $params[] = $per_page;
        $params[] = $offset;

        $stmt = $this->conn->prepare($query);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalProducts($category_id = null, $search = '') {
        $where = [];
        $params = [];
        $types = '';

        if ($category_id) {
            $where[] = "category_id = ?";
            $params[] = $category_id;
            $types .= 'i';
        }

        if ($search) {
            $where[] = "name LIKE ?";
            $params[] = "%$search%";
            $types .= 's';
        }

        $where_clause = $where ? "WHERE " . implode(" AND ", $where) : "";
        $query = "SELECT COUNT(*) as total FROM products $where_clause";

        $stmt = $this->conn->prepare($query);
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['total'];
    }

    public function getProductVariantsBySizeAndColor($size_id, $color_id, $product_id) {
        $query = "SELECT * FROM product_variants WHERE size_id = ? AND color_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $size_id, $color_id, $product_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    /**
     * Lấy ảnh chính của sản phẩm
     */
    public function getMainProductImage($conn, $product_id) {
        $stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ? AND is_main = 1 LIMIT 1");
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $image = $result->fetch_assoc();
        $stmt->close();

        return $image ? $image['image_url'] : null;
    }
    /**
     * Lấy danh sách ảnh của sản phẩm dưới dạng mảng
     */
    public function getProductImagesArray($conn, $product_id) {
        $stmt = $conn->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY position ASC");
        $stmt->bind_param("s", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $images = [];
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
        
        $stmt->close();
        return $images;
    }
}
?>