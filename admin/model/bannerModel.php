<?php
// File: admin/model/bannerModel.php

require_once __DIR__ . '/../controller/stringHelper.php';

function generateUCCID() {
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

function getTotalBanners($conn, $search = "") {
    $search = "%" . $conn->real_escape_string($search) . "%";
    $sql = "SELECT COUNT(*) as total FROM banner WHERE banner_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total'] ?? 0;
}


function getBanners($conn, $limit, $offset, $search = "") {
    $search = "%" . $conn->real_escape_string($search) . "%";
    $sql = "SELECT * FROM banner WHERE banner_name LIKE ? ORDER BY banner_id DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $search, $limit, $offset);
    $stmt->execute();
    return $stmt->get_result();
}


function addBanner($conn, $bannerName, $imageUrl, $link, $status) {
    $banner_id = generateUCCID();
    $sql = "INSERT INTO banner (banner_id, banner_name, image_url, link, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $banner_id, $bannerName, $imageUrl, $link, $status);
    return $stmt->execute();
}

function getBannerById($conn, $banner_id) {
    $stmt = $conn->prepare("SELECT * FROM banner WHERE banner_id = ?");
    $stmt->bind_param("s", $banner_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

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
    return $stmt->get_result()->fetch_assoc()['count'] > 0;
}

function updateBanner($conn, $banner_id, $bannerName, $imageUrl, $link, $status) {
    $sql = "UPDATE banner SET banner_name = ?, image_url = ?, link = ?, status = ? WHERE banner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $bannerName, $imageUrl, $link, $status, $banner_id);
    return $stmt->execute();
}

function deleteBanner($conn, $banner_id) {
    $banner = getBannerById($conn, $banner_id);
    if ($banner && !empty($banner['image_url'])) {
        $path = realpath(__DIR__ . '/../uploads/banners/') . '/' . basename($banner['image_url']);
        if (file_exists($path)) unlink($path);
    }
    $stmt = $conn->prepare("DELETE FROM banner WHERE banner_id = ?");
    $stmt->bind_param("s", $banner_id);
    return $stmt->execute();
}