<?php
require_once __DIR__ . '/stringHelper.php';

/**
 * Tạo ID dạng UUID v4.
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
 * Lấy danh sách banner theo phân trang.
 *
 * Nếu có từ khóa tìm kiếm, sẽ dùng điều kiện WHERE link LIKE ?.
 * Nếu không có từ khóa, sẽ lấy tất cả các banner (bao gồm cả những banner có link NULL).
 *
 * @param mysqli $conn Kết nối CSDL
 * @param int $page Trang hiện tại
 * @param int $limit Số banner trên mỗi trang
 * @param string $search Từ khóa tìm kiếm
 * @return array
 */
function getBannersWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page   = max(1, (int)$page);
    $limit  = max(1, (int)$limit);
    $search = trim($search);

    if ($search !== "") {
        $sqlCount = "SELECT COUNT(*) as total FROM banner WHERE link LIKE ?";
        $searchParam = "%" . $search . "%";
        $stmtCount = $conn->prepare($sqlCount);
        $stmtCount->bind_param("s", $searchParam);
        $stmtCount->execute();
        $resultCount = $stmtCount->get_result();
        $rowCount = $resultCount->fetch_assoc();
        $totalBanners = (int)($rowCount['total'] ?? 0);
    } else {
        $sqlCount = "SELECT COUNT(*) as total FROM banner";
        $resultCount = $conn->query($sqlCount);
        $rowCount = $resultCount->fetch_assoc();
        $totalBanners = (int)($rowCount['total'] ?? 0);
    }

    $totalPages = max(1, ceil($totalBanners / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    if ($search !== "") {
        $sql = "SELECT * FROM banner
                WHERE link LIKE ?
                ORDER BY banner_id DESC
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $searchParam, $limit, $offset);
    } else {
        $sql = "SELECT * FROM banner
                ORDER BY banner_id DESC
                LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
    }
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
 * Thêm banner mới vào DB.
 *
 * @param mysqli $conn Kết nối DB.
 * @param string $bannerName Tên banner.
 * @param string|null $imageUrl Đường dẫn ảnh.
 * @param string|null $link Link đích (cho phép rỗng).
 * @param int $status Trạng thái (1=Hiển thị, 2=Ẩn).
 * @return bool True nếu thêm thành công.
 */
function addBanner($conn, $bannerName, $imageUrl, $link, $status) {
    $banner_id = generateUCCID();
    $sql = "INSERT INTO banner (banner_id, banner_name, image_url, link, status)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $banner_id, $bannerName, $imageUrl, $link, $status);
    return $stmt->execute();
}

/**
 * Lấy thông tin banner theo ID.
 *
 * @param mysqli $conn Kết nối DB.
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
 * Nếu có lỗi (ví dụ tên hoặc ảnh để trống, hoặc tên chứa ký tự đặc biệt), trả về thông báo lỗi dưới dạng chuỗi.
 * Nếu thành công, chuyển hướng sang trang index.
 *
 * @param mysqli $conn Kết nối DB.
 * @return string|null Thông báo lỗi nếu có, hoặc null nếu không có lỗi.
 */
function processAddBanner($conn) {
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy tên banner, bắt buộc không được để trống và không chứa ký tự đặc biệt như @, #
        $bannerName = trim($_POST['banner_name'] ?? '');
        if (empty($bannerName)) {
            $error = "Tên banner không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $bannerName)) {
            $error = "Tên banner không được chứa ký tự đặc biệt như @, #, v.v.";
        }
        
        // Kiểm tra xem đã chọn ảnh chưa (không cho phép để trống)
        if (empty($_FILES['image']['name'])) {
            $error = "Ảnh banner không được để trống.";
        }
        
        // Lấy link (cho phép rỗng) và trạng thái
        $link = trim($_POST['link'] ?? '');
        if ($link === '') {
            $link = null;
        }
        $status = (int)($_POST['status'] ?? 1);

        // Chỉ tiến hành upload ảnh nếu không có lỗi
        if (!$error) {
            $targetDir = __DIR__ . '/../uploads/banners/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename  = 'banner_' . time() . '.' . $extension;
            $filePath  = $targetDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = 'admin/uploads/banners/' . $filename;
            } else {
                $error = "Upload ảnh không thành công.";
            }
        }

        if (!$error) {
            if (addBanner($conn, $bannerName, $imageUrl, $link, $status)) {
                header("Location: index.php?msg=Thêm banner thành công!&type=success");
                exit;
            } else {
                $error = "Thêm banner thất bại.";
            }
        }
    }
    return $error;
}

/**
 * Xử lý cập nhật banner qua form.
 *
 * Nếu có lỗi, trả về thông báo lỗi.
 * Nếu thành công, chuyển hướng sang trang index.
 *
 * @param mysqli $conn Kết nối DB.
 * @param string $banner_id
 * @return string|null Thông báo lỗi nếu có, hoặc null nếu không có lỗi.
 */
function processEditBanner($conn, $banner_id) {
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bannerName = trim($_POST['banner_name'] ?? '');
        if (empty($bannerName)) {
            $error = "Tên banner không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $bannerName)) {
            $error = "Tên banner không được chứa ký tự đặc biệt như @, #, v.v.";
        }

        $link = trim($_POST['link'] ?? '');
        if ($link === '') {
            $link = null;
        }
        $status = (int)($_POST['status'] ?? 1);

        $currentBanner = getBannerById($conn, $banner_id);
        $imageUrl = $currentBanner['image_url'] ?? null;

        // Nếu có upload ảnh mới, xoá ảnh cũ và thay thế bằng ảnh mới
        if (!empty($_FILES['image']['name'])) {
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
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = 'admin/uploads/banners/' . $filename;
            } else {
                $error = "Upload ảnh mới không thành công.";
            }
        } else {
            // Nếu không có ảnh mới và banner hiện tại không có ảnh, báo lỗi
            if (empty($imageUrl)) {
                $error = "Ảnh banner không được để trống.";
            }
        }

        if (!$error) {
            $sql = "UPDATE banner
                    SET banner_name = ?, image_url = ?, link = ?, status = ?
                    WHERE banner_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssis", $bannerName, $imageUrl, $link, $status, $banner_id);
            if ($stmt->execute()) {
                header("Location: index.php?msg=Cập nhật banner thành công!&type=success");
                exit;
            } else {
                $error = "Cập nhật banner thất bại.";
            }
        }
    }
    return $error;
}

/**
 * Xử lý xóa banner qua form.
 *
 * @param mysqli $conn Kết nối DB.
 * @param string $banner_id
 * @return string|null Thông báo lỗi nếu có, hoặc null nếu thành công.
 */
function processDeleteBanner($conn, $banner_id) {
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteBanner($conn, $banner_id)) {
            header("Location: index.php?msg=Xóa banner thành công!&type=success");
            exit;
        } else {
            $error = "Xóa banner thất bại.";
        }
    }
    return $error;
}

/**
 * Xử lý xóa banner (bao gồm cả file ảnh nếu có) từ thư mục uploads/banners.
 *
 * @param mysqli $conn Kết nối DB.
 * @param string $banner_id
 * @return bool True nếu xóa thành công.
 */
function deleteBanner($conn, $banner_id) {
    $currentBanner = getBannerById($conn, $banner_id);
    if ($currentBanner && !empty($currentBanner['image_url'])) {
        $uploadDir = realpath(__DIR__ . '/../uploads/banners/');
        $physicalPath = $uploadDir . '/' . basename($currentBanner['image_url']);
        if (file_exists($physicalPath)) {
            unlink($physicalPath);
        }
    }
    $sql = "DELETE FROM banner WHERE banner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $banner_id);
    return $stmt->execute();
}
?>