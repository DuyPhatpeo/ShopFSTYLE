<?php
// admin/controller/categoryController.php

// ---------------- HÀM HỖ TRỢ ----------------
// Hàm tạo ID dạng UUID v4
function generateUCCID() {
    $data = random_bytes(16);
    // Thiết lập phiên bản 4
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

// Kiểm tra xem tên danh mục đã tồn tại chưa (có thể loại trừ ID hiện tại khi chỉnh sửa)
function isCategoryNameExists($conn, $categoryName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM category WHERE category_name = ? AND category_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $categoryName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM category WHERE category_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $categoryName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

// ---------------- PHÂN TRANG & DANH SÁCH DANH MỤC ----------------
function getCategoriesWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);

    // Đếm tổng số danh mục
    $sqlCount = "SELECT COUNT(*) as total FROM category WHERE category_name LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $result = $stmtCount->get_result();
    $row = $result->fetch_assoc();
    $totalCategories = (int)($row['total'] ?? 0);
    $totalPages = max(1, ceil($totalCategories / $limit));

    // Đảm bảo trang hiện tại không vượt quá tổng số trang
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Lấy danh sách danh mục, kèm tên danh mục cha (nếu có)
    $sql = "SELECT c.*, p.category_name as parent_name
            FROM category c
            LEFT JOIN category p ON c.parent_id = p.category_id
            WHERE c.category_name LIKE ?
            ORDER BY c.category_name ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    $categories = $stmt->get_result();

    return [
        'categories'      => $categories,
        'totalPages'      => $totalPages,
        'currentPage'     => $page,
        'totalCategories' => $totalCategories
    ];
}

// ---------------- THÊM DANH MỤC ----------------
function addCategory($conn, $categoryName, $parentId, $status) {
    $category_id = generateUCCID();
    $sql = "INSERT INTO category (category_id, category_name, parent_id, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $category_id, $categoryName, $parentId, $status);
    return $stmt->execute();
}

function processAddCategory($conn) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryName = trim($_POST['category_name']);
        // Nếu không chọn danh mục cha, đặt NULL
        $parentId = (isset($_POST['parent_id']) && $_POST['parent_id'] !== '') ? trim($_POST['parent_id']) : null;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        
        if (empty($categoryName)) {
            return "Tên danh mục không được để trống.";
        }
        if (!preg_match("/^[\p{L}\p{N}\s]+$/u", $categoryName)) {
            return "Tên danh mục không được chứa ký tự đặt biệt.";
        }
        if (isCategoryNameExists($conn, $categoryName)) {
            return "Tên danh mục đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            return "Trạng thái không hợp lệ.";
        }
        
        if (addCategory($conn, $categoryName, $parentId, $status)) {
            header("Location: index.php?msg=added");
            exit;
        } else {
            return "Thêm danh mục thất bại.";
        }
    }
    return $error;
}

// ---------------- LẤY THÔNG TIN CHI TIẾT DANH MỤC ----------------
function getCategoryById($conn, $category_id) {
    $sql = "SELECT * FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ---------------- CHỈNH SỬA DANH MỤC ----------------
function processEditCategory($conn, $category_id) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryName = trim($_POST['category_name']);
        $parentId = (isset($_POST['parent_id']) && $_POST['parent_id'] !== '') ? trim($_POST['parent_id']) : null;
        $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;
        
        if (empty($categoryName)) {
            return "Tên danh mục không được để trống.";
        }
        if (!preg_match("/^[\p{L}\p{N}\s]+$/u", $categoryName)) {
            return "Tên danh mục không được chứa ký tự đặt biệt.";
        }
        if (isCategoryNameExists($conn, $categoryName, $category_id)) {
            return "Tên danh mục đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            return "Trạng thái không hợp lệ.";
        }
        
        $sql = "UPDATE category SET category_name = ?, parent_id = ?, status = ? WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $categoryName, $parentId, $status, $category_id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=updated");
            exit;
        } else {
            return "Cập nhật danh mục thất bại.";
        }
    }
    return $error;
}

// ---------------- XÓA DANH MỤC ----------------
function deleteCategory($conn, $category_id) {
    // Trước tiên, lấy tất cả danh mục con của danh mục hiện tại
    $sql = "SELECT category_id FROM category WHERE parent_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Duyệt qua từng danh mục con và gọi đệ quy xóa
    while ($child = $result->fetch_assoc()) {
        deleteCategory($conn, $child['category_id']);
    }
    
    // Sau đó, xóa danh mục hiện tại
    $sql = "DELETE FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_id);
    return $stmt->execute();
}