<?php
// File: admin/controller/bannerController.php

require_once __DIR__ . '/../model/bannerModel.php';

function getBannersWithPagination($conn, $page = 1, $limit = 10, $search = "") {
    $page = max(1, (int)$page);
    $limit = max(1, (int)$limit);
    $search = trim($search);

    $total = getTotalBanners($conn, $search);
    $totalPages = max(1, ceil($total / $limit));
    $page = min($page, $totalPages);
    $offset = ($page - 1) * $limit;

    return [
        'banners'      => getBanners($conn, $limit, $offset, $search),
        'totalPages'   => $totalPages,
        'currentPage'  => $page,
        'totalBanners' => $total
    ];
}

function processAddBanner($conn) {
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bannerName = trim($_POST['banner_name'] ?? '');
        if (empty($bannerName)) {
            $errors['banner_name'] = "Tên banner không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $bannerName)) {
            $errors['banner_name'] = "Tên banner không hợp lệ.";
        } elseif (isBannerNameExists($conn, $bannerName)) {
            $errors['banner_name'] = "Tên banner đã tồn tại.";
        }

        if (empty($_FILES['image']['name'])) {
            $errors['image'] = "Ảnh banner không được để trống.";
        }

        $link   = trim($_POST['link'] ?? '') ?: null;
        $status = (int)($_POST['status'] ?? 1);
        $imageUrl = null;

        if (empty($errors)) {
            $targetDir = __DIR__ . '/../uploads/banners/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            $filename = 'banner_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filePath = $targetDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                $imageUrl = 'admin/uploads/banners/' . $filename;
            } else {
                $errors['image'] = "Không thể upload ảnh.";
            }
        }

        if (empty($errors) && addBanner($conn, $bannerName, $imageUrl, $link, $status)) {
            header("Location: index.php?msg=Thêm banner thành công!&type=success");
            exit;
        } elseif (empty($errors)) {
            $errors['general'] = "Thêm banner thất bại.";
        }
    }
    return $errors;
}

function processEditBanner($conn, $banner_id) {
    $errors = [];
    $banner = getBannerById($conn, $banner_id);

    if (!$banner) {
        header("Location: index.php?msg=Không tìm thấy banner.&type=danger");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bannerName = trim($_POST['banner_name'] ?? '');
        $link       = trim($_POST['link'] ?? '') ?: null;
        $status     = (int)($_POST['status'] ?? 1);
        $imageUrl   = $banner['image_url']; // Giữ lại ảnh cũ mặc định

        // Kiểm tra tên
        if (empty($bannerName)) {
            $errors['banner_name'] = "Tên banner không được để trống.";
        } elseif (!preg_match("/^[\p{L}\p{N}\s]+$/u", $bannerName)) {
            $errors['banner_name'] = "Tên banner không hợp lệ.";
        } elseif (isBannerNameExists($conn, $bannerName, $banner_id)) {
            $errors['banner_name'] = "Tên banner đã tồn tại.";
        }

        // Nếu có chọn ảnh mới
        if (!empty($_FILES['image']['name'])) {
            $targetDir = __DIR__ . '/../uploads/banners/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            $filename = 'banner_' . time() . '_' . uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filePath = $targetDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                // Xoá ảnh cũ nếu tồn tại
                if (!empty($banner['image_url'])) {
                    $oldPath = realpath(__DIR__ . '/../' . $banner['image_url']);
                    if ($oldPath && file_exists($oldPath)) unlink($oldPath);
                }
                $imageUrl = 'admin/uploads/banners/' . $filename;
            } else {
                $errors['image'] = "Không thể upload ảnh mới.";
            }
        }

        if (empty($errors)) {
            if (updateBanner($conn, $banner_id, $bannerName, $imageUrl, $link, $status)) {
                header("Location: index.php?msg=Cập nhật banner thành công!&type=success");
                exit;
            } else {
                $errors['general'] = "Cập nhật thất bại.";
            }
        }
    }

    return ['errors' => $errors, 'banner' => $banner];
}


function processDeleteBanner($conn, $banner_id) {
    $banner = getBannerById($conn, $banner_id);
    if (!$banner) {
        header("Location: index.php?msg=Không tìm thấy banner.&type=danger");
        exit;
    }

    if (deleteBanner($conn, $banner_id)) {
        header("Location: index.php?msg=Xoá banner thành công!&type=success");
    } else {
        header("Location: index.php?msg=Xoá banner thất bại.&type=danger");
    }
    exit;
}