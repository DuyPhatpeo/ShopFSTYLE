<?php 
    include ('../../includes/header.php'); 
    include ('../../includes/search.php');
    include ('../user/login-register.php');  
?>

<!-- Phần nội dung chính -->
<div class="container mx-auto py-4 pb-32">
    <!-- Khối chung nền trắng cho 2 cột -->
    <div class="bg-white p-8 rounded-lg shadow-lg flex flex-col md:flex-row md:space-x-6">

        <!-- Cột trái: Thông tin đặt hàng -->
        <div class="md:w-2/3 mb-6 md:mb-0">
            <h2 class="text-3xl font-bold mb-6 text-gray-800">Thông tin đặt hàng</h2>

            <!-- Form đặt hàng -->
            <form id="order-form" action="order-process.php" method="POST" class="space-y-4">

                <!-- Họ và tên + Số điện thoại -->
                <div class="flex flex-col sm:flex-row sm:space-x-4">
                    <div class="flex-1">
                        <label class="block text-gray-700 mb-1">Họ và tên</label>
                        <input type="text" name="fullname" required class="w-full p-2 border rounded-lg">
                    </div>
                    <div class="sm:w-1/3">
                        <label class="block text-gray-700 mb-1">Số điện thoại</label>
                        <input type="text" name="phone" required class="w-full p-2 border rounded-lg">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required class="w-full p-2 border rounded-lg">
                </div>

                <!-- Địa chỉ -->
                <div>
                    <label class="block text-gray-700 mb-1">Địa chỉ</label>
                    <input type="text" name="address" required class="w-full p-2 border rounded-lg">
                </div>

                <!-- Quận/Huyện + Phường/Xã -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 mb-1">Quận/Huyện</label>
                        <select name="district" class="w-full p-2 border rounded-lg">
                            <option value="">Chọn Quận/Huyện</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 mb-1">Phường/Xã</label>
                        <select name="ward" class="w-full p-2 border rounded-lg">
                            <option value="">Chọn Phường/Xã</option>
                        </select>
                    </div>
                </div>

                <!-- Ghi chú -->
                <div>
                    <label class="block text-gray-700 mb-1">Ghi chú</label>
                    <textarea name="note" class="w-full p-2 border rounded-lg"></textarea>
                </div>

                <!-- Hình thức thanh toán -->
                <h3 class="text-xl font-semibold mb-4">Hình thức thanh toán</h3>
                <div class="space-y-4">
                    <!-- Phương thức 1: COD -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment" value="cod" checked class="hidden peer">
                        <div class="border border-gray-300 rounded-lg p-4 hover:shadow-sm transition 
                            peer-checked:border-blue-300 peer-checked:bg-blue-100">
                            <div class="flex items-center space-x-3">
                                <img src="<?= USER_URL ?>/assets/images/cod.png" alt="COD icon" class="w-10 h-10">
                                <span class="font-medium text-gray-700">Thanh toán khi nhận hàng</span>
                            </div>
                        </div>
                    </label>
                    <!-- Phương thức 2: MoMo -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment" value="momo" class="hidden peer">
                        <div class="border border-gray-300 rounded-lg p-4 hover:shadow-sm transition 
                            peer-checked:border-blue-300 peer-checked:bg-blue-100">
                            <div class="flex items-center space-x-3">
                                <img src="<?= USER_URL ?>/assets/images/momo.png" alt="MoMo icon" class="w-10 h-10">
                                <span class="font-medium text-gray-700">Ví MoMo</span>
                            </div>
                        </div>
                    </label>
                    <!-- Phương thức 3: ZaloPay -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="payment" value="zalopay" class="hidden peer">
                        <div class="border border-gray-300 rounded-lg p-4 hover:shadow-sm transition 
                            peer-checked:border-blue-300 peer-checked:bg-blue-100">
                            <div class="flex items-center space-x-3">
                                <img src="<?= USER_URL ?>/assets/images/zalopay.png" alt="ZaloPay icon"
                                    class="w-10 h-10">
                                <span class="font-medium text-gray-700">ZaloPay</span>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- End Hình thức thanh toán -->
            </form>
        </div>
        <!-- End cột trái -->

        <!-- Cột phải: Giỏ hàng -->
        <div class="md:w-1/3">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Giỏ hàng</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 border rounded-lg">
                    <img src="path/to/image.jpg" alt="Sản phẩm" class="w-16 h-16 object-cover rounded-lg">
                    <div class="flex-1 ml-4">
                        <p class="font-semibold">Tên sản phẩm</p>
                        <p class="text-gray-600">Màu: Màu sản phẩm</p>
                        <p class="text-gray-600">Số lượng: 1</p>
                    </div>
                    <div class="font-bold">100.000₫</div>
                </div>
            </div>
            <div class="mt-6 p-4 border rounded-lg space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-700">Tạm tính</span>
                    <span class="font-semibold">200.000₫</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Phí vận chuyển</span>
                    <span class="font-semibold">30.000₫</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700">Giảm giá</span>
                    <span class="font-semibold">-20.000₫</span>
                </div>
                <hr class="my-2">
                <div class="flex justify-between text-lg font-bold">
                    <span>Tổng cộng</span>
                    <span>210.000₫</span>
                </div>
            </div>
        </div>
        <!-- End cột phải -->
    </div>
</div>
<!-- Thanh cố định sát đáy, responsive: 1 cột trên mobile, 2 cột trên md trở lên -->
<div class="fixed left-0 right-0 bottom-0 z-50">
    <div class="grid grid-cols-1 md:grid-cols-2">
        <!-- Cột trái: nền xanh nhạt, hiển thị icon và text phương thức thanh toán -->
        <div class="bg-blue-100 p-2 md:p-4 flex items-center justify-center space-x-2">
            <img id="payment-method-icon" src="<?= USER_URL ?>/assets/images/cod.png" alt="Payment Method Icon"
                class="w-10 h-10 md:w-16 md:h-16">
            <span id="payment-method-chosen" class="font-semibold text-gray-800 text-center text-sm md:text-base">
                Thanh toán khi nhận hàng
            </span>
        </div>

        <!-- Cột phải: nền trắng, hiển thị tổng tiền và nút đặt hàng -->
        <div
            class="bg-white p-2 md:p-4 flex flex-col md:flex-row md:items-center md:justify-center space-y-2 md:space-y-0 md:space-x-4">
            <div id="payment-total" class="text-xl md:text-2xl font-bold text-center">
                Thành tiền: <span class="text-blue-500">210.000₫</span>
            </div>
            <button id="submit-btn" type="submit" form="order-form"
                class="w-full md:w-40 uppercase text-white bg-blue-600 border border-white text-base md:text-lg px-4 md:px-8 py-2 md:py-4 rounded-md flex justify-center items-center whitespace-nowrap hover:bg-blue-700 transition duration-300 ease-in-out">
                ĐẶT HÀNG
            </button>
        </div>
    </div>
</div>


<!-- JS cập nhật phương thức thanh toán, icon và text nút -->
<script>
const paymentRadios = document.querySelectorAll('input[name="payment"]');
const paymentMethodChosen = document.getElementById('payment-method-chosen');
const paymentMethodIcon = document.getElementById('payment-method-icon');
const submitBtn = document.getElementById('submit-btn');

function updatePaymentMethod() {
    const selected = document.querySelector('input[name="payment"]:checked');
    if (!selected) {
        paymentMethodChosen.textContent = 'CHƯA CHỌN';
        paymentMethodIcon.src = '<?= USER_URL ?>/assets/images/cod.png';
        submitBtn.textContent = 'ĐẶT HÀNG';
        return;
    }
    switch (selected.value) {
        case 'cod':
            paymentMethodChosen.textContent = 'THANH TOÁN KHI NHẬN HÀNG';
            paymentMethodIcon.src = '<?= USER_URL ?>/assets/images/cod.png';
            submitBtn.textContent = 'ĐẶT HÀNG';
            break;
        case 'momo':
            paymentMethodChosen.textContent = 'VÍ MOMO';
            paymentMethodIcon.src = '<?= USER_URL ?>/assets/images/momo.png';
            submitBtn.textContent = 'THANH TOÁN';
            break;
        case 'zalopay':
            paymentMethodChosen.textContent = 'ZALOPAY';
            paymentMethodIcon.src = '<?= USER_URL ?>/assets/images/zalopay.png';
            submitBtn.textContent = 'THANH TOÁN';
            break;
        default:
            paymentMethodChosen.textContent = 'CHƯA CHỌN';
            paymentMethodIcon.src = '<?= USER_URL ?>/assets/images/cod.png';
            submitBtn.textContent = 'ĐẶT HÀNG';
            break;
    }
}

paymentRadios.forEach(radio => {
    radio.addEventListener('change', updatePaymentMethod);
});
updatePaymentMethod();
</script>

<div class="hidden">
    <?php include ('../../includes/footer.php'); ?>
</div>