<?php 
// admin/views/customer/delete.php
ob_start();

include("../../includes/header.php");
require_once('../../../includes/db.php');           // Kết nối CSDL
require_once('../../controller/customerController.php'); // File xử lý khách hàng

// Kiểm tra sự tồn tại của biến GET id
if (!isset($_GET['id'])) {
    header("Location: index.php?msg=ID khách hàng không hợp lệ.&type=failure");
    exit;
}

$customer_id = $_GET['id'];
$customer = getCustomerById($conn, $customer_id);
if (!$customer) {
    header("Location: index.php?msg=Khách hàng không tồn tại.&type=failure");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (deleteCustomer($conn, $customer_id)) {
        header("Location: index.php?msg=Xóa khách hàng thành công!");
        exit;
    } else {
        header("Location: index.php?msg=Không thể xóa khách hàng!");
        exit;
    }
}
?>
<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <!-- Header: Tiêu đề và nút Quay lại -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Xóa Khách Hàng</h1>
        </div>
        <a href="index.php?msg=Hủy" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center"
            title="Quay lại">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path
                    d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32L109.2 224 214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <!-- Khung xác nhận xoá -->
    <div class="bg-white shadow rounded p-6">
        <p class="mb-4 text-lg">
            Bạn có chắc chắn muốn xóa khách hàng với các thông tin sau:
        </p>
        <ul class="mb-4 list-disc pl-5">
            <li><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></li>
            <li><strong>Họ và tên:</strong> <?= htmlspecialchars($customer['full_name']) ?></li>
            <li><strong>Điện thoại:</strong> <?= htmlspecialchars($customer['phone']) ?></li>
            <li><strong>Địa chỉ:</strong> <?= htmlspecialchars($customer['address']) ?></li>
        </ul>
        <form method="POST" action="">
            <div class="flex justify-end items-center space-x-4">
                <button type="submit" class="bg-red-700 hover:bg-red-800 text-white p-2 rounded flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1H9V4a1 1 0 011-1z" />
                    </svg>
                    <span>Xác nhận xóa</span>
                </button>
            </div>
        </form>
    </div>
</main>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>