<?php
require_once('../model/favouriteModel.php');
require_once('../includes/db.php');

header('Content-Type: application/json');

// Kiểm tra đăng nhập
session_start();
if (!isset($_SESSION['customer'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$customer_id = $_SESSION['customer']['customer_id'];
$favouriteModel = new FavouriteModel($conn);

// Xử lý các action
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $product_id = $_POST['product_id'] ?? 0;
        if ($product_id) {
            if ($favouriteModel->addFavourite($customer_id, $product_id)) {
                echo json_encode(['success' => true, 'message' => 'Đã thêm vào yêu thích']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
            }
        }
        break;

    case 'remove':
        $product_id = $_POST['product_id'] ?? 0;
        if ($product_id) {
            if ($favouriteModel->removeFavourite($customer_id, $product_id)) {
                echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi yêu thích']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
            }
        }
        break;

    case 'check':
        $product_id = $_POST['product_id'] ?? 0;
        if ($product_id) {
            $isFavourite = $favouriteModel->isFavourite($customer_id, $product_id);
            echo json_encode(['success' => true, 'isFavourite' => $isFavourite]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
        break;
}
?>