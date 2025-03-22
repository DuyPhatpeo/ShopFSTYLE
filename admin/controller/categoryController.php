<?php
// File: admin/controller/categoryController.php

require_once __DIR__ . '/stringHelper.php';

/**
 * Tạo ID dạng UUID v4.
 *
 * @return string UUID v4.
 */
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

/**
 * Kiểm tra xem tên danh mục đã tồn tại chưa (có thể loại trừ ID hiện tại khi chỉnh sửa).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $categoryName Tên danh mục.
 * @param string|null $excludeId ID cần loại trừ.
 * @return bool True nếu tồn tại.
 */
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

/**
 * Lấy danh mục theo phân trang, hỗ trợ tìm kiếm theo tên.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param int $page Trang hiện tại.
 * @param int $limit Số danh mục mỗi trang.
 * @param string $search Từ khóa tìm kiếm.
 * @return array Dữ liệu gồm danh sách danh mục, tổng số trang, trang hiện tại, tổng danh mục.
 */
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

/**
 * Thêm danh mục mới (có thể kèm ảnh).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $categoryName Tên danh mục.
 * @param string|null $parentId ID danh mục cha.
 * @param int $status Trạng thái danh mục (1 hoặc 2).
 * @param string|null $imageUrl Đường dẫn ảnh.
 * @return bool True nếu thêm thành công.
 */
function addCategoryWithImage($conn, $categoryName, $parentId, $status, $imageUrl) {
    $category_id = generateUCCID();
    $sql = "INSERT INTO category (category_id, category_name, parent_id, status, image_url)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $category_id, $categoryName, $parentId, $status, $imageUrl);
    return $stmt->execute();
}

/**
 * Xử lý thêm danh mục mới thông qua form.
 * Nếu có lỗi: trả về thông báo lỗi (không redirect).
 * Nếu thành công: redirect về trang danh sách danh mục.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return string|null Lỗi (nếu có)
 */
function processAddCategory($conn) {
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryName = trim($_POST['category_name']);
        $parentId     = (isset($_POST['parent_id']) && $_POST['parent_id'] !== '') ? trim($_POST['parent_id']) : null;
        $status       = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        // Kiểm tra dữ liệu đầu vào
        if (empty($categoryName)) {
            $error = "Tên danh mục không được để trống.";
        } elseif (!preg_match("/^[\\p{L}\\p{N}\\s]+$/u", $categoryName)) {
            $error = "Tên danh mục không được chứa ký tự đặc biệt.";
        } elseif (isCategoryNameExists($conn, $categoryName)) {
            $error = "Tên danh mục đã tồn tại.";
        } elseif ($status !== 1 && $status !== 2) {
            $error = "Trạng thái không hợp lệ.";
        } else {
            // Xử lý upload ảnh nếu có
            $imageUrl = null;
            if (!empty($_FILES['image']['name'])) {
                $targetDir = __DIR__ . '/../uploads/categories/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                // Sử dụng uniqid để đảm bảo tên file không bị trùng
                $filename  = 'category_' . uniqid() . '.' . $extension;
                $targetFile = $targetDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imageUrl = 'admin/uploads/categories/' . $filename;
                } else {
                    $error = "Tải ảnh lên thất bại.";
                }
            }

            if (!$error && addCategoryWithImage($conn, $categoryName, $parentId, $status, $imageUrl)) {
                header("Location: index.php?msg=Thêm danh mục thành công!&type=success");
                exit;
            } elseif (!$error) {
                $error = "Thêm danh mục thất bại.";
            }
        }
    }

    return $error;
}

/**
 * Lấy thông tin chi tiết của danh mục theo ID.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $category_id ID danh mục.
 * @return array|null Mảng thông tin danh mục.
 */
function getCategoryById($conn, $category_id) {
    $sql = "SELECT * FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Xử lý chỉnh sửa danh mục.
 * Nếu có lỗi: trả về thông báo lỗi (không redirect).
 * Nếu thành công: redirect về trang danh sách danh mục.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $category_id ID danh mục cần chỉnh sửa.
 * @return string|null Lỗi (nếu có)
 */
function processEditCategory($conn, $category_id) {
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $categoryName = trim($_POST['category_name']);
        $parentId     = (isset($_POST['parent_id']) && $_POST['parent_id'] !== '') ? trim($_POST['parent_id']) : null;
        $status       = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        // Kiểm tra dữ liệu
        if (empty($categoryName)) {
            $error = "Tên danh mục không được để trống.";
        } elseif (!preg_match("/^[\\p{L}\\p{N}\\s]+$/u", $categoryName)) {
            $error = "Tên danh mục không được chứa ký tự đặc biệt.";
        } elseif (isCategoryNameExists($conn, $categoryName, $category_id)) {
            $error = "Tên danh mục đã tồn tại.";
        } elseif ($status !== 1 && $status !== 2) {
            $error = "Trạng thái không hợp lệ.";
        } else {
            // Xử lý upload ảnh: nếu có ảnh mới, xoá ảnh cũ
            $imageUrl = null;
            if (!empty($_FILES['image']['name'])) {
                $currentCategory = getCategoryById($conn, $category_id);
                if ($currentCategory && !empty($currentCategory['image_url'])) {
                    $oldImagePath = __DIR__ . '/../../' . $currentCategory['image_url'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $targetDir = __DIR__ . '/../uploads/categories/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename  = 'category_' . uniqid() . '.' . $extension;
                $targetFile = $targetDir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    $imageUrl = 'admin/uploads/categories/' . $filename;
                } else {
                    $error = "Tải ảnh lên thất bại.";
                }
            } else {
                // Giữ lại ảnh cũ nếu không có ảnh mới
                $currentCategory = getCategoryById($conn, $category_id);
                $imageUrl = $currentCategory['image_url'] ?? null;
            }

            if (!$error) {
                $sql = "UPDATE category
                        SET category_name = ?, parent_id = ?, status = ?, image_url = ?
                        WHERE category_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssiss", $categoryName, $parentId, $status, $imageUrl, $category_id);
                if ($stmt->execute()) {
                    header("Location: index.php?msg=Cập nhật danh mục thành công!&type=success");
                    exit;
                } else {
                    $error = "Cập nhật danh mục thất bại.";
                }
            }
        }
    }

    return $error;
}

/**
 * Xóa danh mục (bao gồm cả danh mục con và file ảnh nếu có).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $category_id ID danh mục cần xóa.
 * @return bool True nếu xóa thành công.
 */
function deleteCategory($conn, $category_id) {
    // Lấy danh sách danh mục con
    $sql = "SELECT category_id FROM category WHERE parent_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Đệ quy xóa danh mục con
    while ($child = $result->fetch_assoc()) {
        deleteCategory($conn, $child['category_id']);
    }

    // Xoá file ảnh vật lý nếu có
    $currentCategory = getCategoryById($conn, $category_id);
    if ($currentCategory && !empty($currentCategory['image_url'])) {
        $physicalPath = __DIR__ . '/../../' . $currentCategory['image_url'];
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
    }

    // Xóa danh mục khỏi DB
    $sql = "DELETE FROM category WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_id);
    return $stmt->execute();
}

/**
 * Xử lý xóa danh mục thông qua form.
 * Nếu có lỗi: trả về thông báo lỗi (không redirect).
 * Nếu thành công: redirect về trang danh sách danh mục.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $category_id ID danh mục cần xóa.
 * @return string|null Lỗi (nếu có)
 */
function processDeleteCategory($conn, $category_id) {
    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteCategory($conn, $category_id)) {
            header("Location: index.php?msg=Xóa danh mục thành công!&type=success");
            exit;
        } else {
            $error = "Xóa danh mục thất bại.";
        }
    }

    return $error;
}

/**
 * Nếu hàm getAllCategories chưa được định nghĩa, ta định nghĩa nó ở đây.
 * (Lưu ý: Bạn nên loại bỏ định nghĩa hàm này trong các file view như add.php để tránh trùng lặp.)
 *
 * Lấy danh sách danh mục để hiển thị trong dropdown (loại trừ danh mục hiện tại).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $excludeId ID danh mục cần loại trừ.
 * @return array Danh sách danh mục.
 */
if (!function_exists('getAllCategories')) {
    function getAllCategories($conn, $excludeId) {
        $sql = "SELECT category_id, category_name FROM category WHERE category_id != ? ORDER BY category_name ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $excludeId);
        $stmt->execute();
        $result = $stmt->get_result();
        $categories = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()){
                $categories[] = $row;
            }
        }
        return $categories;
    }
}
?>