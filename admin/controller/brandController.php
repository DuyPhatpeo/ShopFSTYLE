<?php
// admin/controller/brandController.php

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
 * Kiểm tra xem tên thương hiệu đã tồn tại chưa (có thể loại trừ ID hiện tại khi chỉnh sửa).
 *
 * @param mysqli      $conn       Kết nối CSDL.
 * @param string      $brandName  Tên thương hiệu.
 * @param string|null $excludeId  ID cần loại trừ.
 * @return bool True nếu tồn tại.
 */
function isBrandNameExists($conn, $brandName, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM brand WHERE brand_name = ? AND brand_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $brandName, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM brand WHERE brand_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $brandName);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/**
 * Lấy danh sách thương hiệu theo phân trang, hỗ trợ tìm kiếm theo tên.
 *
 * @param mysqli $conn   Kết nối CSDL.
 * @param int    $page   Trang hiện tại.
 * @param int    $limit  Số thương hiệu mỗi trang.
 * @param string $search Từ khóa tìm kiếm.
 * @return array Dữ liệu gồm danh sách thương hiệu, tổng số trang, trang hiện tại, tổng thương hiệu.
 */
function getBrandsWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
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

    // Đảm bảo trang hiện tại không vượt quá tổng số trang
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    // Lấy danh sách thương hiệu
    $sql = "SELECT * FROM brand
            WHERE brand_name LIKE ?
            ORDER BY brand_name ASC
            LIMIT ? OFFSET ?";
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

/**
 * Thêm thương hiệu mới (có thể kèm ảnh).
 *
 * @param mysqli      $conn       Kết nối CSDL.
 * @param string      $brandName  Tên thương hiệu.
 * @param int         $status     Trạng thái (1 hoặc 2).
 * @param string|null $imageUrl   Đường dẫn ảnh.
 * @return bool True nếu thêm thành công.
 */
function addBrandWithImage($conn, $brandName, $status, $imageUrl) {
    $brand_id = generateUCCID();
    $sql = "INSERT INTO brand (brand_id, brand_name, status, image_url)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis", $brand_id, $brandName, $status, $imageUrl);
    return $stmt->execute();
}

/**
 * Xử lý thêm thương hiệu mới thông qua form.
 *
 * @param mysqli $conn Kết nối CSDL.
 * @return string Thông báo lỗi nếu có.
 */
function processAddBrand($conn) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brandName = trim($_POST['brand_name']);
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        // Kiểm tra dữ liệu đầu vào
        if (empty($brandName)) {
            return "Tên thương hiệu không được để trống.";
        }
        // Chỉ cho phép ký tự chữ, số và khoảng trắng (có hỗ trợ unicode)
        if (!preg_match("/^[\\p{L}\\p{N}\\s]+$/u", $brandName)) {
            return "Tên thương hiệu không được chứa ký tự đặc biệt.";
        }
        if (isBrandNameExists($conn, $brandName)) {
            return "Tên thương hiệu đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            return "Trạng thái không hợp lệ.";
        }

        // Xử lý upload ảnh nếu có
        $imageUrl = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = __DIR__ . '/../uploads/brands/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            // Lấy phần mở rộng của file
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $safeName  = safeString($brandName);
            $filename  = 'brand_' . $safeName . '.' . $extension;
            $filePath  = $targetDir . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $filePath);
            $imageUrl  = 'admin/uploads/brands/' . $filename;
        }

        if (addBrandWithImage($conn, $brandName, $status, $imageUrl)) {
            header("Location: index.php?msg=added");
            exit;
        } else {
            return "Thêm thương hiệu thất bại.";
        }
    }
    return $error;
}

/**
 * Lấy thông tin chi tiết của thương hiệu theo ID.
 *
 * @param mysqli $conn     Kết nối CSDL.
 * @param string $brand_id ID thương hiệu.
 * @return array|null Mảng thông tin thương hiệu hoặc null nếu không tìm thấy.
 */
function getBrandById($conn, $brand_id) {
    $sql = "SELECT * FROM brand WHERE brand_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $brand_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Xử lý chỉnh sửa thương hiệu.
 *
 * Khi có upload ảnh mới, hệ thống sẽ xóa ảnh cũ (nếu tồn tại) rồi lưu ảnh mới.
 *
 * @param mysqli $conn     Kết nối CSDL.
 * @param string $brand_id ID thương hiệu cần chỉnh sửa.
 * @return string Thông báo lỗi nếu có.
 */
function processEditBrand($conn, $brand_id) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $brandName = trim($_POST['brand_name']);
        $status    = isset($_POST['status']) ? (int)$_POST['status'] : 1;

        // Kiểm tra dữ liệu
        if (empty($brandName)) {
            return "Tên thương hiệu không được để trống.";
        }
        if (!preg_match("/^[\\p{L}\\p{N}\\s]+$/u", $brandName)) {
            return "Tên thương hiệu không được chứa ký tự đặc biệt.";
        }
        if (isBrandNameExists($conn, $brandName, $brand_id)) {
            return "Tên thương hiệu đã tồn tại.";
        }
        if ($status !== 1 && $status !== 2) {
            return "Trạng thái không hợp lệ.";
        }

        // Xử lý ảnh: nếu có upload ảnh mới thì xoá ảnh cũ nếu tồn tại
        $imageUrl = null;
        if (!empty($_FILES['image']['name'])) {
            $currentBrand = getBrandById($conn, $brand_id);
            if ($currentBrand && !empty($currentBrand['image_url'])) {
                $oldImagePath = __DIR__ . '/../../' . $currentBrand['image_url'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $targetDir = __DIR__ . '/../uploads/brands/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $safeName  = safeString($brandName);
            $filename  = 'brand_' . $safeName . '.' . $extension;
            $filePath  = $targetDir . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $filePath);
            $imageUrl  = 'admin/uploads/brands/' . $filename;
        } else {
            // Nếu không upload file mới, giữ nguyên file cũ
            $currentBrand = getBrandById($conn, $brand_id);
            $imageUrl = $currentBrand['image_url'] ?? null;
        }

        $sql = "UPDATE brand
                SET brand_name = ?, status = ?, image_url = ?
                WHERE brand_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siss", $brandName, $status, $imageUrl, $brand_id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=updated");
            exit;
        } else {
            return "Cập nhật thương hiệu thất bại.";
        }
    }
    return $error;
}

/**
 * Xóa thương hiệu (kèm file ảnh nếu có).
 *
 * @param mysqli $conn     Kết nối CSDL.
 * @param string $brand_id ID thương hiệu cần xóa.
 * @return bool True nếu xóa thành công.
 */
function deleteBrand($conn, $brand_id) {
    // Xóa file ảnh vật lý nếu có
    $currentBrand = getBrandById($conn, $brand_id);
    if ($currentBrand && !empty($currentBrand['image_url'])) {
        $physicalPath = __DIR__ . '/../../' . $currentBrand['image_url'];
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
    }

    // Xóa brand khỏi DB
    $sql = "DELETE FROM brand WHERE brand_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $brand_id);
    return $stmt->execute();
}

// Nếu bạn cần hàm lấy toàn bộ thương hiệu (không phân trang), bạn có thể thêm:
if (!function_exists('getAllBrands')) {
    /**
     * Lấy danh sách thương hiệu (VD: để hiển thị trong dropdown).
     *
     * @param mysqli $conn Kết nối CSDL.
     * @return array Danh sách thương hiệu.
     */
    function getAllBrands($conn) {
        $sql = "SELECT brand_id, brand_name FROM brand ORDER BY brand_name ASC";
        $result = $conn->query($sql);
        $brands = [];
        if ($result && $result->num_rows > 0) {
             while ($row = $result->fetch_assoc()){
                 $brands[] = $row;
             }
        }
        return $brands;
    }
}