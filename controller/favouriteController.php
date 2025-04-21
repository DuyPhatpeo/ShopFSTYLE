<?php
require_once '../model/FavouriteModel.php';

session_start();

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập!']);
    exit;
}

$customer_id = $_SESSION['customer_id'];
$product_id  = $_POST['product_id'] ?? '';

$favModel = new FavouriteModel($conn);

if ($favModel->isFavourite($customer_id, $product_id)) {
    $favModel->removeFavourite($customer_id, $product_id);
    echo json_encode(['status' => 'removed']);
} else {
    $favModel->addFavourite($customer_id, $product_id);
    echo json_encode(['status' => 'added']);
}
?>