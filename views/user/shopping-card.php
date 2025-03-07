<?php 
    include ('../../includes/header.php'); 
    include ('../../includes/search.php');
    include ('../user/login-register.php');  
?>

<div class="container mx-auto py-10 pb-24">
    <div class="flex flex-col md:flex-row md:space-x-6">
        <!-- Cột trái: Thông tin đặt hàng và hình thức thanh toán -->
        <div class="md:w-1/2 bg-white p-8 rounded-lg shadow-lg mb-6 md:mb-0">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Thông tin đặt hàng</h2>

            <!-- Form đặt hàng với id "order-form" để nút Đặt hàng cố định bên dưới có thể submit -->
            <form id="order-form" action="order-process.php" method="POST" class="space-y-4">

                <!-- Họ và tên & Số điện thoại cùng 1 hàng -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700">Họ và tên</label>
                        <input type="text" name="fullname" required class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700">Số điện thoại</label>
                        <input type="text" name="phone" required class="w-full p-2 border rounded">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email" required class="w-full p-2 border rounded">
                </div>

                <!-- Địa chỉ -->
                <div>
                    <label class="block text-gray-700">Địa chỉ</label>
                    <input type="text" name="address" required class="w-full p-2 border rounded">
                </div>

                <!-- Quận/Huyện & Phường/Xã -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700">Quận/Huyện</label>
                        <select name="district" class="w-full p-2 border rounded">
                            <option value="">Chọn Quận/Huyện</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700">Phường/Xã</label>
                        <select name="ward" class="w-full p-2 border rounded">
                            <option value="">Chọn Phường/Xã</option>
                        </select>
                    </div>
                </div>

                <!-- Ghi chú -->
                <div>
                    <label class="block text-gray-700">Ghi chú</label>
                    <textarea name="note" class="w-full p-2 border rounded"></textarea>
                </div>

                <!-- Hình thức thanh toán (dạng card với icon) -->
                <h3 class="text-xl font-semibold mb-4">Hình thức thanh toán</h3>
                <div class="space-y-4">
                    <!-- Thanh toán khi nhận hàng -->
                    <label class="block border rounded-lg p-4 hover:shadow-sm cursor-pointer transition">
                        <div class="flex items-center space-x-3">
                            <!-- Radio button -->
                            <input type="radio" name="payment" value="cod" checked
                                class="form-radio h-5 w-5 text-blue-600">
                            <!-- Icon + Tên phương thức -->
                            <div class="flex items-center space-x-2">
                                <!-- Thay src=... bằng đường dẫn icon COD thực tế -->
                                <img src="images/cod-icon.png" alt="COD icon" class="w-6 h-6">
                                <span class="font-medium text-gray-700">Thanh toán khi nhận hàng</span>
                            </div>
                        </div>
                    </label>

                    <!-- Ví MoMo -->
                    <label class="block border rounded-lg p-4 hover:shadow-sm cursor-pointer transition">
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="payment" value="momo" class="form-radio h-5 w-5 text-blue-600">
                            <div class="flex items-center space-x-2">
                                <!-- Thay src=... bằng đường dẫn icon MoMo thực tế -->
                                <img src="images/momo-icon.png" alt="MoMo icon" class="w-6 h-6">
                                <span class="font-medium text-gray-700">Ví MoMo</span>
                            </div>
                        </div>
                    </label>

                    <!-- Thanh toán qua ZaloPay -->
                    <label class="block border rounded-lg p-4 hover:shadow-sm cursor-pointer transition">
                        <div class="flex items-start space-x-3">
                            <input type="radio" name="payment" value="zalopay"
                                class="form-radio h-5 w-5 text-blue-600 mt-1">
                            <div>
                                <!-- Dòng chính -->
                                <div class="flex items-center space-x-2">
                                    <!-- Thay src=... bằng đường dẫn icon ZaloPay thực tế -->
                                    <img src="images/zalopay-icon.png" alt="ZaloPay icon" class="w-6 h-6">
                                    <span class="font-medium text-gray-700">Thanh toán qua ZaloPay</span>
                                </div>
                                <!-- Dòng hiển thị logo napas, visa, ... -->
                                <div class="mt-2 flex items-center space-x-2">
                                    <!-- Thay src=... bằng đường dẫn icon thực tế -->
                                    <img src="images/napas-icon.png" alt="Napas icon" class="w-8 h-5 object-contain">
                                    <img src="images/visa-icon.png" alt="Visa icon" class="w-8 h-5 object-contain">
                                    <img src="images/mastercard-icon.png" alt="Mastercard icon"
                                        class="w-8 h-5 object-contain">
                                    <img src="images/applepay-icon.png" alt="Apple Pay icon"
                                        class="w-8 h-5 object-contain">
                                </div>
                            </div>
                        </div>
                    </label>

                    <!-- Ví điện tử VNPAY -->
                    <label class="block border rounded-lg p-4 hover:shadow-sm cursor-pointer transition">
                        <div class="flex items-center space-x-3">
                            <input type="radio" name="payment" value="vnpay" class="form-radio h-5 w-5 text-blue-600">
                            <div class="flex items-center space-x-2">
                                <!-- Thay src=... bằng đường dẫn icon VNPAY thực tế -->
                                <img src="images/vnpay-icon.png" alt="VNPAY icon" class="w-6 h-6">
                                <span class="font-medium text-gray-700">Ví điện tử VNPAY</span>
                            </div>
                        </div>
                    </label>
                </div>
                <!-- Kết thúc phần phương thức thanh toán -->
            </form>
        </div>

        <!-- Cột phải: Giỏ hàng hiển thị danh sách sản phẩm -->
        <div class="md:w-1/2 bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Giỏ hàng</h2>
            <div class="space-y-4">
                <!-- Ví dụ về 1 sản phẩm trong giỏ hàng -->
                <div class="flex items-center justify-between p-4 border rounded">
                    <img src="path/to/image.jpg" alt="Sản phẩm" class="w-16 h-16 object-cover rounded">
                    <div class="flex-1 ml-4">
                        <p class="font-semibold">Tên sản phẩm</p>
                        <p class="text-gray-600">Màu: Màu sản phẩm</p>
                        <p class="text-gray-600">Số lượng: 1</p>
                    </div>
                    <div class="font-bold">100.000₫</div>
                </div>
                <!-- Có thể lặp lại khối sản phẩm trên với dữ liệu thực -->
            </div>
        </div>
    </div>
</div>

<!-- Thanh cố định ở dưới hiển thị thành tiền và nút đặt hàng -->
<div class="fixed bottom-0 left-0 right-0 bg-white shadow-inner border-t border-gray-200 py-6">
    <div class="container mx-auto flex flex-col md:flex-row items-center justify-between px-4">
        <div class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">
            Thành tiền: <span class="text-red-600">200.000₫</span>
        </div>
        <!-- Nút submit form "order-form" -->
        <button type="submit" form="order-form"
            class="w-full md:w-auto bg-blue-600 text-white text-lg px-8 py-4 rounded-lg hover:bg-blue-700 transition duration-300 ease-in-out">
            Đặt hàng
        </button>
    </div>
</div>