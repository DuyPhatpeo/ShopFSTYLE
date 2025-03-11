<?php
require_once __DIR__ . '/stringHelper.php';

/**
 * Tạo ID dạng UUID v4 (có thể dùng chung với category).
 *
 * @return string
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
 * Lấy danh sách banner theo phân trang (tùy chọn có thể tìm kiếm theo link hoặc cột khác).
 *
 * @param mysqli $conn Kết nối CSDL
 * @param int $page Trang hiện tại
 * @param int $limit Số banner trên mỗi trang
 * @param string $search Từ khóa tìm kiếm (ví dụ tìm trong cột link)
 * @return array
 */
function getBannersWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);

    // Đếm tổng số banner
    $sqlCount = "SELECT COUNT(*) as total FROM banner WHERE link LIKE ?";
    $stmtCount = $conn->prepare($sqlCount);
    $searchParam = "%" . $search . "%";
    $stmtCount->bind_param("s", $searchParam);
    $stmtCount->execute();
    $resultCount = $stmtCount->get_result();
    $rowCount    = $resultCount->fetch_assoc();
    $totalBanners = (int)($rowCount['total'] ?? 0);

    $totalPages = max(1, ceil($totalBanners / $limit));
    $page       = min($page, $totalPages);
    $offset     = ($page - 1) * $limit;

    // Lấy danh sách banner
    $sql = "SELECT * 
            FROM banner
            WHERE link LIKE ?
            ORDER BY banner_id DESC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $searchParam, $limit, $offset);
    $stmt->execute();
    $banners = $stmt->get_result();

    return [
        'banners'      => $banners,
        'totalPages'   => $totalPages,
        'currentPage'  => $page,
        'totalBanners' => $totalBanners
    ];
}

/**
 * Thêm banner mới.
 *
 * @param mysqli $conn
 * @param string|null $imageUrl  Đường dẫn ảnh (sau khi upload)
 * @param string|null $link      Link đích (cho phép để trống)
 * @param int         $status    Trạng thái (1 = hiển thị, 2 = ẩn)
 * @return bool
 */
function addBanner($conn, $imageUrl, $link, $status) {
    $banner_id = generateUCCID();
    $sql = "INSERT INTO banner (banner_id, image_url, link, status)
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $banner_id, $imageUrl, $link, $status);
    return $stmt->execute();
}

/**
 * Lấy banner theo ID.
 *
 * @param mysqli $conn
 * @param string $banner_id
 * @return array|null
 */
function getBannerById($conn, $banner_id) {
    $sql = "SELECT * FROM banner WHERE banner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $banner_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Xử lý thêm banner qua form.
 *
 * @param mysqli $conn
 * @return string Thông báo lỗi (nếu có)
 */
function processAddBanner($conn) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $link = trim($_POST['link'] ?? '');
        if ($link === '') {
            $link = null; // Cho phép link rỗng, lưu dưới dạng NULL
        }
        $status = (int)($_POST['status'] ?? 1);

        // Upload ảnh nếu có
        $imageUrl = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = __DIR__ . '/../uploads/banners/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename  = 'banner_' . time() . '.' . $extension;
            $filePath  = $targetDir . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $filePath);
            $imageUrl = 'admin/uploads/banners/' . $filename;
        }

        // Thực hiện thêm banner
        if (addBanner($conn, $imageUrl, $link, $status)) {
            header("Location: index.php?msg=added_banner");
            exit;
        } else {
            $error = "Thêm banner thất bại.";
        }
    }
    return $error;
}

/**
 * Xử lý cập nhật banner.
 *
 * @param mysqli $conn
 * @param string $banner_id
 * @return string Thông báo lỗi (nếu có)
 */
function processEditBanner($conn, $banner_id) {
    $error = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $link = trim($_POST['link'] ?? '');
        if ($link === '') {
            $link = null; // Cho phép link rỗng, lưu dưới dạng NULL
        }
        $status = (int)($_POST['status'] ?? 1);

        // Lấy banner hiện tại để xóa ảnh cũ nếu upload mới
        $currentBanner = getBannerById($conn, $banner_id);
        $imageUrl = $currentBanner['image_url'] ?? null;

        // Nếu có upload ảnh mới
        if (!empty($_FILES['image']['name'])) {
            // Xóa ảnh cũ nếu tồn tại
            if ($currentBanner && !empty($currentBanner['image_url'])) {
                $oldImagePath = __DIR__ . '/../../' . $currentBanner['image_url'];
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            $targetDir = __DIR__ . '/../uploads/banners/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename  = 'banner_' . time() . '.' . $extension;
            $filePath  = $targetDir . $filename;
            move_uploaded_file($_FILES['image']['tmp_name'], $filePath);
            $imageUrl = 'admin/uploads/banners/' . $filename;
        }

        // Thực hiện cập nhật
        $sql = "UPDATE banner
                SET image_url = ?, link = ?, status = ?
                WHERE banner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssis", $imageUrl, $link, $status, $banner_id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=updated_banner");
            exit;
        } else {
            $error = "Cập nhật banner thất bại.";
        }
    }
    return $error;
}

/**
 * Xóa banner (xóa luôn file ảnh nếu có).
 *
 * @param mysqli $conn
 * @param string $banner_id
 * @return bool
 */
function deleteBanner($conn, $banner_id) {
    // Lấy thông tin banner để xóa file ảnh
    $currentBanner = getBannerById($conn, $banner_id);
    if ($currentBanner && !empty($currentBanner['image_url'])) {
        $physicalPath = __DIR__ . '/../../' . $currentBanner['image_url'];
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
    }

    // Xóa banner khỏi DB
    $sql = "DELETE FROM banner WHERE banner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $banner_id);
    return $stmt->execute();
}