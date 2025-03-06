<?php
// admin/controller/brandCotroller.php

// ---------------- PHÂN TRANG VÀ DANH SÁCH THƯƠNG HIỆU ----------------
function getBrandsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $search = trim($search);

    // Đếm tổng số thương hiệu
    $sqlCount = "SELECT COUNT(*) as total FROM brand WHERE brand_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $row = $result->fetch_assoc();
    $totalBrands = (int)($row['total'] ?? 0);
    $totalPages = max(1, ceil($totalBrands / $limit));

    // Đảm bảo currentPage không vượt quá tổng số trang
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Lấy danh sách thương hiệu
    $sql = "SELECT * FROM brand WHERE brand_name LIKE ? ORDER BY brand_name ASC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
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

// ---------------- HÀM TẠO ID VỚI UUID V4 ----------------
function generateUCCID() {
    $data = random_bytes(16);
    // Thiết lập phiên bản 4 cho UUID
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    // Thiết lập biến thể
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return sprintf('%s-%s-%s-%s-%s',
        bin2hex(substr($data, 0, 4)),
        bin2hex(substr($data, 4, 2)),
        bin2hex(substr($data, 6, 2)),
        bin2hex(substr($data, 8, 2)),
        bin2hex(substr($data, 10, 6))
    );
}

// ---------------- KIỂM TRA TÊN THƯƠNG HIỆU ----------------
function isBrandNameExists($conn, $brandName) {
    $sql = "SELECT COUNT(*) as count FROM brand WHERE brand_name = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("s", $brandName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

// ---------------- THÊM THƯƠNG HIỆU ----------------
function addBrand($conn, $brandName, $status = 1) {
    $brand_id = generateUCCID();
    $sql = "INSERT INTO brand (brand_id, brand_name, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ssi", $brand_id, $brandName, $status);
    return $stmt->execute();
}

function processAddBrand($conn) {
    $error = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brandName = trim($_POST['brand_name']);
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        
        // Ràng buộc: Tên thương hiệu không được để trống
        if (empty($brandName)) {
            $error = "Tên thương hiệu không được để trống.";
            return $error;
        }
        
        // Ràng buộc: Tên thương hiệu không chứa ký tự đặt biệt
        if (!preg_match("/^[\p{L}\p{N}\s]+$/u", $brandName)) {
            $error = "Tên thương hiệu không được chứa ký tự đặt biệt.";
            return $error;
        }
        
        // Ràng buộc: Kiểm tra tên đã tồn tại
        if (isBrandNameExists($conn, $brandName)) {
            $error = "Tên thương hiệu đã tồn tại.";
            return $error;
        }
        
        // Ràng buộc: Trạng thái phải là 1 hoặc 2
        if ($status !== 1 && $status !== 2) {
            $error = "Trạng thái không hợp lệ.";
            return $error;
        }
        
        if (addBrand($conn, $brandName, $status)) {
            header("Location: index.php?msg=added");
            exit;
        } else {
            $error = "Thêm thương hiệu thất bại. Vui lòng thử lại.";
        }
    }
    
    return $error;
}

// ---------------- CHỈNH SỬA THƯƠNG HIỆU ----------------
function getBrandById($conn, $brand_id) {
    $sql = "SELECT * FROM brand WHERE brand_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $brand_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function processEditBrand($conn, $brand_id) {
    $error = null;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brandName = trim($_POST['brand_name']);
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        
        if (empty($brandName)) {
            $error = "Tên thương hiệu không được để trống.";
            return $error;
        }
        
        if (!preg_match("/^[\p{L}\p{N}\s]+$/u", $brandName)) {
            $error = "Tên thương hiệu không được chứa ký tự đặt biệt.";
            return $error;
        }
        
        // Kiểm tra tên đã tồn tại, loại trừ bản ghi hiện tại
        $sql = "SELECT COUNT(*) as count FROM brand WHERE brand_name = ? AND brand_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $brandName, $brand_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if ((int)$row['count'] > 0) {
            $error = "Tên thương hiệu đã tồn tại.";
            return $error;
        }
        
        if ($status !== 1 && $status !== 2) {
            $error = "Trạng thái không hợp lệ.";
            return $error;
        }
        
        $sql = "UPDATE brand SET brand_name = ?, status = ? WHERE brand_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $brandName, $status, $brand_id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=updated");
            exit;
        } else {
            $error = "Cập nhật thương hiệu thất bại. Vui lòng thử lại.";
        }
    }
    
    return $error;
}

// ---------------- XÓA THƯƠNG HIỆU ----------------
function deleteBrand($conn, $brand_id) {
    $sql = "DELETE FROM brand WHERE brand_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $brand_id);
    return $stmt->execute();
}
?>