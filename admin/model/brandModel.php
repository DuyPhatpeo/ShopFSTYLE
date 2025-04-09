<?php
// File: admin/model/BrandModel.php
require_once __DIR__ . '/../controller/stringHelper.php';

class BrandModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function generateUCCID() {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return sprintf('%s-%s-%s-%s-%s',
            bin2hex(substr($data, 0, 4)),
            bin2hex(substr($data, 4, 2)),
            bin2hex(substr($data, 6, 2)),
            bin2hex(substr($data, 8, 2)),
            bin2hex(substr($data, 10, 6))
        );
    }

    public function isBrandNameExists($brandName, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM brand WHERE brand_name = ? AND brand_id != ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ss", $brandName, $excludeId);
        } else {
            $sql = "SELECT COUNT(*) as count FROM brand WHERE brand_name = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $brandName);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return ((int)$row['count'] > 0);
    }

    public function getBrandsWithPagination($page = 1, $limit = 10, $search = "") {
        $page   = max(1, (int)$page);
        $limit  = max(1, (int)$limit);
        $search = trim($search);

        $sqlCount = "SELECT COUNT(*) as total FROM brand WHERE brand_name LIKE ?";
        $stmtCount = $this->conn->prepare($sqlCount);
        $searchParam = "%" . $search . "%";
        $stmtCount->bind_param("s", $searchParam);
        $stmtCount->execute();
        $result = $stmtCount->get_result();
        $row = $result->fetch_assoc();
        $totalBrands = (int)($row['total'] ?? 0);
        $totalPages = max(1, ceil($totalBrands / $limit));

        $page = min($page, $totalPages);
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM brand
                WHERE brand_name LIKE ?
                ORDER BY brand_name ASC
                LIMIT ? OFFSET ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sii", $searchParam, $limit, $offset);
        $stmt->execute();
        $brands = $stmt->get_result();

        return [
            'brands'      => $brands,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'totalBrands' => $totalBrands
        ];
    }

    public function addBrand($brandName, $status, $imageUrl) {
        $brand_id = $this->generateUCCID();
        $sql = "INSERT INTO brand (brand_id, brand_name, status, image_url)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssis", $brand_id, $brandName, $status, $imageUrl);
        return $stmt->execute();
    }

    public function getBrandById($brand_id) {
        $sql = "SELECT * FROM brand WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $brand_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateBrand($brand_id, $brandName, $status, $imageUrl) {
        $sql = "UPDATE brand SET brand_name = ?, status = ?, image_url = ? WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("siss", $brandName, $status, $imageUrl, $brand_id);
        return $stmt->execute();
    }

    public function deleteBrand($brand_id) {
        $brand = $this->getBrandById($brand_id);
        if ($brand && !empty($brand['image_url'])) {
            $physicalPath = __DIR__ . '/../../' . $brand['image_url'];
            if (file_exists($physicalPath)) {
                unlink($physicalPath);
            }
        }

        $sql = "DELETE FROM brand WHERE brand_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $brand_id);
        return $stmt->execute();
    }

    public function getAllBrands() {
        $sql = "SELECT brand_id, brand_name FROM brand ORDER BY brand_name ASC";
        $result = $this->conn->query($sql);
        $brands = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $brands[] = $row;
            }
        }
        return $brands;
    }
}