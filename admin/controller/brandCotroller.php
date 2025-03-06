<?php

// Hàm lấy danh sách thương hiệu (đã có)
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

// Hàm tạo UCCID tự sinh cho brand_id
function generateUCCID() {
    // Ví dụ: UCC + chuỗi uniqid() chuyển thành chữ in hoa
    return 'UCC' . strtoupper(uniqid());
}

// Hàm kiểm tra xem tên thương hiệu đã tồn tại hay chưa
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

// Hàm thêm thương hiệu mới (chỉ thực hiện câu lệnh INSERT)
// Sử dụng UCCID tự sinh cho brand_id
function addBrand($conn, $brandName, $status = 1) {
    $brand_id = generateUCCID(); // Sinh id theo kiểu UCCID
    $sql = "INSERT INTO brand (brand_id, brand_name, status) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("ssi", $brand_id, $brandName, $status);
    return $stmt->execute();
}

// Hàm xử lý thêm thương hiệu, toàn bộ xử lý được thực hiện bên controller
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
        
        /* 
           Ràng buộc: Tên thương hiệu không chứa ký tự đặt biệt
        */
        if (!preg_match("/^[\p{L}\p{N}\s]+$/u", $brandName)) {
            $error = "Tên thương hiệu không được chứa ký tự đặt biệt.";
            return $error;
        }
        
        // Ràng buộc: Kiểm tra tên thương hiệu đã tồn tại chưa
        if (isBrandNameExists($conn, $brandName)) {
            $error = "Tên thương hiệu đã tồn tại.";
            return $error;
        }
        
        // Ràng buộc: Trạng thái phải là 1 (On) hoặc 2 (Off)
        if ($status !== 1 && $status !== 2) {
            $error = "Trạng thái không hợp lệ.";
            return $error;
        }
        
        // Nếu vượt qua các kiểm tra, thêm thương hiệu vào CSDL
        $result = addBrand($conn, $brandName, $status);
        if ($result) {
            // Nếu thêm thành công, chuyển hướng về trang danh sách kèm thông báo
            header("Location: index.php?msg=added");
            exit;
        } else {
            $error = "Thêm thương hiệu thất bại. Vui lòng thử lại.";
        }
    }
    return $error;
}



?>