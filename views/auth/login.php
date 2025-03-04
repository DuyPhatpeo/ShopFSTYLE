<!-- Modal Đăng Nhập / Đăng Ký -->
<div id="auth-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
    <div id="auth-modal-content" class="bg-white w-full max-w-md rounded-lg p-6 relative">
        <!-- Nút đóng -->
        <button id="close-auth-modal"
            class="absolute top-4 right-4 text-gray-600 hover:text-black text-2xl focus:outline-none">
            <img src="assets/icons/close.svg" alt="close" class="w-6 h-6" />
        </button>
        <!-- Tiêu đề Modal (cập nhật theo form) -->
        <h1 id="modal-title" class="text-3xl font-bold text-blue-600">
            FYSYLE <span class="text-blue-800">CLUB</span> - Đăng nhập
        </h1>
        <!-- Form Đăng Nhập -->
        <div id="login-container" class="mt-4">
            <p class="text-left mb-2">Đăng nhập tài khoản:</p>
            <form id="login-form" class="space-y-4">
                <input type="email" placeholder="Email/SĐT của bạn"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-gray-500" required />
                <div class="relative">
                    <input id="password-input" type="password" placeholder="Mật khẩu"
                        class="w-full px-4 py-2 border rounded pr-12 focus:outline-none focus:border-gray-500"
                        required />
                    <button type="button" id="toggle-password" class="absolute inset-y-0 right-3 flex items-center">
                        <img id="eye-icon" src="assets/icons/eye-open.svg" alt="Show password" class="h-6 w-6" />
                    </button>
                </div>
                <button type="submit"
                    class="w-full bg-black text-white py-2 rounded transition-colors duration-200 hover:bg-gray-200 hover:text-black">
                    ĐĂNG NHẬP
                </button>
            </form>

            <!-- Dòng phân cách "Đăng nhập khác" -->
            <div class="flex items-center my-4">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="mx-2 text-sm text-gray-500">Đăng nhập khác</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>

            <!-- Nút Đăng nhập bằng Google -->
            <div class="mt-4">
                <button type="button"
                    class="w-full border border-gray-300 rounded flex items-center justify-center py-2 space-x-2 hover:bg-gray-100 transition-colors duration-200">
                    <img src="assets/images/google.png" alt="google" class="w-8 h-8" />
                    <span class="font-medium text-gray-700">Đăng nhập bằng Google</span>
                </button>
            </div>

            <!-- Liên kết phụ: Đăng ký và Quên mật khẩu -->
            <div class="flex justify-between mt-4 text-sm">
                <span class="text-blue-600 cursor-pointer" id="show-signup">
                    Chưa có tài khoản? Đăng ký
                </span>
                <a href="#" class="text-blue-600">Quên mật khẩu</a>
            </div>
        </div>

        <!-- Form Đăng Ký (ẩn mặc định) -->
        <div id="signup-container" class="hidden mt-4">
            <p class="text-left mb-2">Đăng ký tài khoản mới:</p>
            <form id="signup-form" class="space-y-4">
                <input type="text" placeholder="Họ và tên"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-gray-500" required />
                <input type="email" placeholder="Email của bạn"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-gray-500" required />
                <!-- Trường mật khẩu với toggle -->
                <div class="relative">
                    <input id="signup-password-input" type="password" placeholder="Mật khẩu"
                        class="w-full px-4 py-2 border rounded pr-12 focus:outline-none focus:border-gray-500"
                        required />
                    <button type="button" id="toggle-signup-password"
                        class="absolute inset-y-0 right-3 flex items-center">
                        <img id="signup-eye-icon" src="assets/icons/eye-open.svg" alt="Show password" class="h-6 w-6" />
                    </button>
                </div>
                <!-- Trường xác nhận mật khẩu với toggle -->
                <div class="relative">
                    <input id="signup-confirm-password-input" type="password" placeholder="Xác nhận mật khẩu"
                        class="w-full px-4 py-2 border rounded pr-12 focus:outline-none focus:border-gray-500"
                        required />
                    <button type="button" id="toggle-signup-confirm-password"
                        class="absolute inset-y-0 right-3 flex items-center">
                        <img id="signup-confirm-eye-icon" src="assets/icons/eye-open.svg" alt="Show password"
                            class="h-6 w-6" />
                    </button>
                </div>
                <button type="submit"
                    class="w-full bg-black text-white py-2 rounded transition-colors duration-200 hover:bg-gray-200 hover:text-black">
                    ĐĂNG KÝ
                </button>
            </form>
            <!-- Liên kết chuyển về form Đăng nhập -->
            <div class="flex justify-end mt-4 text-sm">
                <span class="text-blue-600 cursor-pointer" id="show-login">
                    Đã có tài khoản? Đăng nhập
                </span>
            </div>
        </div>
    </div>
</div>