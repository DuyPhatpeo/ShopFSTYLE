<?php
// File: admin/controller/bannerController.php

require_once __DIR__ . '/stringHelper.php';

/**
 * Tạo ID dạng UUID v4 cho banner.
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
 * Lấy danh sách banner theo phân trang.
 *
 * Nếu có từ khóa tìm kiếm, sẽ dùng điều kiện WHERE link LIKE ?.
 * Nếu không có từ khóa, sẽ lấy tất cả các banner.
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
 * Kiểm tra xem banner_name đã tồn tại chưa (loại trừ banner hiện tại nếu cần).
 *
 * @param mysqli $conn Kết nối CSDL.
 * @param string $banner_name Tên banner cần kiểm tra.
 * @param string|null $excludeId ID banner cần loại trừ.
 * @return bool True nếu tồn tại.
 */
function isBannerNameExists($conn, $banner_name, $excludeId = null) {
    if ($excludeId) {
        $sql = "SELECT COUNT(*) as count FROM banner WHERE banner_name = ? AND banner_id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $banner_name, $excludeId);
    } else {
        $sql = "SELECT COUNT(*) as count FROM banner WHERE banner_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $banner_name);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return ((int)$row['count'] > 0);
}

/**
 * Xử lý thêm banner qua form.
 *
 * Nếu có lỗi (ví dụ tên hoặc ảnh để trống, hoặc tên chứa ký tự đặc biệt), 
 * trả về mảng lỗi với các thông báo tương ứng.
 * Nếu thành công, chuyển hướng sang trang index.
 *
 * @param mysqli $conn Kết nối DB.
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processAddBanner($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Lấy tên banner, bắt buộc không được để trống và không chứa ký tự đặc biệt như @, #
        $bannerName = trim($_POST['banner_name'] ?? '');
        if (empty($bannerName)) {
            $errors['banner_name'] = "Tên banner không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $bannerName)) {
            $errors['banner_name'] = "Tên banner không được chứa ký tự đặc biệt như @, #, v.v.";
        } else {
            // Kiểm tra tính duy nhất của banner_name
            if (isBannerNameExists($conn, $bannerName)) {
                $errors['banner_name'] = "Tên banner đã tồn tại.";
            }
        }
        
        // Kiểm tra xem đã chọn ảnh chưa (bắt buộc không được để trống)
        if (empty($_FILES['image']['name'])) {
            $errors['image'] = "Ảnh banner không được để trống.";
        }
        
        // Lấy link (cho phép rỗng) và trạng thái
        $link = trim($_POST['link'] ?? '');
        if ($link === '') {
            $link = null;
        }
        $status = (int)($_POST['status'] ?? 1);
        $imageUrl = null;

        // Chỉ tiến hành upload ảnh nếu không có lỗi
        if (empty($errors)) {
            $targetDir = __DIR__ . '/../uploads/banners/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            // Sử dụng time() + uniqid() để tạo tên file duy nhất
            $filename  = 'banner_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath  = $targetDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = 'admin/uploads/banners/' . $filename;
            } else {
                $errors['image'] = "Upload ảnh không thành công.";
            }
        }

        if (empty($errors)) {
            if (addBanner($conn, $bannerName, $imageUrl, $link, $status)) {
                header("Location: index.php?msg=Thêm banner thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Thêm banner thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Xử lý cập nhật banner qua form.
 *
 * Nếu có lỗi, trả về mảng lỗi.
 * Nếu thành công, chuyển hướng sang trang index.
 *
 * @param mysqli $conn Kết nối DB.
 * @param string $banner_id
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processEditBanner($conn, $banner_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bannerName = trim($_POST['banner_name'] ?? '');
        if (empty($bannerName)) {
            $errors['banner_name'] = "Tên banner không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $bannerName)) {
            $errors['banner_name'] = "Tên banner không được chứa ký tự đặc biệt như @, #, v.v.";
        } else {
            // Kiểm tra tính duy nhất của banner_name, loại trừ banner hiện tại
            if (isBannerNameExists($conn, $bannerName, $banner_id)) {
                $errors['banner_name'] = "Tên banner đã tồn tại.";
            }
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
            // Sử dụng time() + uniqid() để tạo tên file duy nhất
            $filename  = 'banner_' . time() . '_' . uniqid() . '.' . $extension;
            $filePath  = $targetDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = 'admin/uploads/banners/' . $filename;
            } else {
                $errors['image'] = "Upload ảnh mới không thành công.";
            }
        } else {
            // Nếu không có ảnh mới và banner hiện tại không có ảnh, báo lỗi
            if (empty($imageUrl)) {
                $errors['image'] = "Ảnh banner không được để trống.";
            }
        }

        if (empty($errors)) {
            $sql = "UPDATE banner
                    SET banner_name = ?, image_url = ?, link = ?, status = ?
                    WHERE banner_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssis", $bannerName, $imageUrl, $link, $status, $banner_id);
            if ($stmt->execute()) {
                header("Location: index.php?msg=Cập nhật banner thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật banner thất bại.";
            }
        }
    }
    return $errors;
}

/**
 * Xử lý xóa banner qua form.
 *
 * @param mysqli $conn Kết nối DB.
 * @param string $banner_id
 * @return array Mảng lỗi (rỗng nếu thành công).
 */
function processDeleteBanner($conn, $banner_id) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (deleteBanner($conn, $banner_id)) {
            header("Location: index.php?msg=Xóa banner thành công!&type=success");
            exit;
        } else {
            $errors['general'] = "Xóa banner thất bại.";
        }
    }
    return $errors;
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