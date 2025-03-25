<?php
$pageTitle = "Trang chỉnh sửa khách hàng";

include("../../includes/session_check.php");

// admin/views/customer/edit.php

ob_start();
include("../../includes/header.php");
require_once('../../../includes/db.php');
require_once('../../controller/customerController.php');

// Lấy ID khách hàng từ URL
$customer_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$customer_id) {
    header("Location: index.php");
    exit;
}

// Lấy thông tin khách hàng cần chỉnh sửa
$customer = getCustomerById($conn, $customer_id);
if (!$customer) {
    echo "Khách hàng không tồn tại.";
    exit;
}

// Xử lý form (sẽ kiểm tra dữ liệu bên trong processEditCustomer)
$errors = processEditCustomer($conn, $customer_id);
?>
<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<main class="container mx-auto p-6">
    <!-- Tiêu đề và nút Quay lại -->
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-3xl font-bold">Chỉnh sửa Khách Hàng</h1>
            <p class="text-sm text-gray-500">
                Mã khách hàng: <?= htmlspecialchars($customer['customer_id']) ?>
            </p>
        </div>
        <a href="index.php" class="bg-blue-500 hover:bg-blue-600 text-white p-2 rounded flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-5 h-5 mr-1" fill="currentColor">
                <path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 
                       12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 
                       416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32
                       L109.2 224 214.6 118.6c12.5-12.5 
                       12.5-32.8 0-45.3s-32.8-12.5-45.3 0
                       l-160 160z" />
            </svg>
            <span class="hidden md:inline-block">Quay lại</span>
        </a>
    </div>

    <!-- Thông báo lỗi chung -->
    <?php if (!empty($errors['general'])): ?>
    <div class="bg-red-200 p-2 mb-4 text-red-800">
        <?= htmlspecialchars($errors['general']) ?>
    </div>
    <?php endif; ?>

    <!-- Form chỉnh sửa: Thêm novalidate để tắt HTML5 validation -->
    <form method="POST" action="" novalidate>
        <div class="flex flex-wrap -mx-2 mb-4">
            <!-- Cột 1: Email, Họ và tên, Phone, Address -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Email: đổi type="text" -->
                <div class="mb-4">
                    <label for="email" class="block mb-1 font-medium">Email:</label>
                    <input type="text" name="email" id="email" class="w-full p-2 border border-gray-300 rounded" value="<?= isset($_POST['email']) 
                                  ? htmlspecialchars($_POST['email']) 
                                  : htmlspecialchars($customer['email']) ?>">
                    <?php if (!empty($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['email']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Họ và tên -->
                <div class="mb-4">
                    <label for="full_name" class="block mb-1 font-medium">Họ và tên:</label>
                    <input type="text" name="full_name" id="full_name" class="w-full p-2 border border-gray-300 rounded"
                        value="<?= isset($_POST['full_name']) 
                                  ? htmlspecialchars($_POST['full_name']) 
                                  : htmlspecialchars($customer['full_name']) ?>">
                    <?php if (!empty($errors['full_name'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['full_name']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Điện thoại -->
                <div class="mb-4">
                    <label for="phone" class="block mb-1 font-medium">Số điện thoại:</label>
                    <input type="text" name="phone" id="phone" class="w-full p-2 border border-gray-300 rounded" value="<?= isset($_POST['phone']) 
                                  ? htmlspecialchars($_POST['phone']) 
                                  : htmlspecialchars($customer['phone']) ?>">
                    <?php if (!empty($errors['phone'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['phone']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Địa chỉ -->
                <div class="mb-4">
                    <label for="address" class="block mb-1 font-medium">Địa chỉ:</label>
                    <input type="text" name="address" id="address" class="w-full p-2 border border-gray-300 rounded"
                        value="<?= isset($_POST['address']) 
                                  ? htmlspecialchars($_POST['address']) 
                                  : htmlspecialchars($customer['address']) ?>">
                    <?php if (!empty($errors['address'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['address']) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột 2: Mật khẩu, Xác nhận mật khẩu, Trạng thái -->
            <div class="w-full md:w-1/2 px-2">
                <!-- Mật khẩu mới -->
                <div class="mb-4 relative">
                    <label for="password" class="block mb-1 font-medium">Mật khẩu mới:</label>
                    <input type="password" name="password" id="password"
                        class="w-full p-2 border border-gray-300 rounded pr-10" placeholder="Để trống nếu không đổi">
                    <button type="button" onclick="togglePassword('password','icon-password')"
                        class="absolute right-2 top-9 text-gray-600">
                        <svg id="icon-password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 
                                     12 5c4.477 0 8.268 2.943 
                                     9.542 7-1.274 4.057-5.065 7
                                     -9.542 7-4.477 0-8.268-2.943
                                     -9.542-7z" />
                        </svg>
                    </button>
                    <?php if (!empty($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['password']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Xác nhận mật khẩu mới -->
                <div class="mb-4 relative">
                    <label for="confirm_password" class="block mb-1 font-medium">Xác nhận mật khẩu:</label>
                    <input type="password" name="confirm_password" id="confirm_password"
                        class="w-full p-2 border border-gray-300 rounded pr-10" placeholder="Để trống nếu không đổi">
                    <button type="button" onclick="togglePassword('confirm_password','icon-confirm_password')"
                        class="absolute right-2 top-9 text-gray-600">
                        <svg id="icon-confirm_password" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 
                                     12 5c4.477 0 8.268 2.943 
                                     9.542 7-1.274 4.057-5.065 7
                                     -9.542 7-4.477 0-8.268-2.943
                                     -9.542-7z" />
                        </svg>
                    </button>
                    <?php if (!empty($errors['confirm_password'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['confirm_password']) ?>
                    </p>
                    <?php endif; ?>
                </div>

                <!-- Trạng thái -->
                <div class="mb-4">
                    <label for="status" class="block mb-1 font-medium">Trạng thái:</label>
                    <select name="status" id="status" class="w-full p-2 border border-gray-300 rounded">
                        <option value="1" <?= (isset($_POST['status']) && $_POST['status'] == '1')
                                ? 'selected'
                                : ((!isset($_POST['status']) && $customer['status'] == 1)
                                    ? 'selected'
                                    : '') ?>>
                            Hoạt động
                        </option>
                        <option value="0" <?= (isset($_POST['status']) && $_POST['status'] == '0')
                                ? 'selected'
                                : ((!isset($_POST['status']) && $customer['status'] == 0)
                                    ? 'selected'
                                    : '') ?>>
                            Khóa
                        </option>
                    </select>
                    <?php if (!empty($errors['status'])): ?>
                    <p class="text-red-500 text-sm mt-1">
                        <?= htmlspecialchars($errors['status']) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Nút Lưu -->
        <div class="flex justify-end items-center">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white p-2 rounded flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17 3H7c-1.1 0-2 .9-2 2v14h16V7l-4-4z
                           M12 19c-1.66 0-3-1.34-3-3
                           s1.34-3 3-3 3 1.34 3 3
                           -1.34 3-3 3zm3-7H9V5h6v7z" />
                </svg>
                Lưu thay đổi
            </button>
        </div>
    </form>
</main>

<!-- Script ẩn/hiện mật khẩu -->
<script>
function togglePassword(fieldId, iconId) {
    const inputField = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);
    if (inputField.type === "password") {
        inputField.type = "text";
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19
                     c-4.418 0-8.21-2.943-9.542-7
                     a10.05 10.05 0 012.05-3.45" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2 2l20 20" />`;
    } else {
        inputField.type = "password";
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 
                     12 5c4.477 0 8.268 2.943 
                     9.542 7-1.274 4.057-5.065 7
                     -9.542 7-4.477 0-8.268-2.943
                     -9.542-7z" />`;
    }
}
</script>

<?php
include('../../includes/footer.php');
ob_end_flush();
?>