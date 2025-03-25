<!-- views/auth/auth.php -->
<!-- Container Toast Notifications (nếu cần) -->
<div id="notificationContainer" class="fixed top-10 right-4 flex flex-col space-y-2 z-50"></div>

<style>
/* Lớp ẩn dành riêng cho modal */
.modal-hidden {
    display: none !important;
}

/* Modal overlay */
#auth-modal {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 900;
    display: flex;
    justify-content: center;
    align-items: center;
    /* Desktop: căn giữa màn hình */
    animation: fade-in 0.3s ease-out;
}

@keyframes fade-in {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

/* Trên mobile (max-width: 767px): Modal từ dưới lên, sát lề dưới */
@media (max-width: 767px) {
    #auth-modal {
        align-items: flex-end;
        padding: 0;
    }

    #auth-modal-content {
        width: 100%;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        margin: 0;
        animation: slide-up 0.3s ease-out;
    }

    @keyframes slide-up {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
}

/* Style nội dung modal */
#auth-modal-content {
    background: #fff;
    width: 100%;
    max-width: 500px;
    border-radius: 8px;
    padding: 1.5rem;
    position: relative;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

/* Style chung cho form */
.form-container p {
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
}
</style>

<!-- Modal, ẩn mặc định bằng lớp "modal-hidden" -->
<div id="auth-modal" class="modal-hidden">
    <div id="auth-modal-content">
        <!-- Nút đóng modal -->
        <button id="close-auth-modal"
            class="absolute top-4 right-4 text-gray-600 hover:text-black text-2xl focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
                <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" />
            </svg>
        </button>
        <!-- Tiêu đề modal, thay đổi khi chuyển form -->
        <h1 id="modal-title" class="text-3xl font-bold text-blue-600 mb-4">FYSYLE <span
                class="text-blue-800">CLUB</span> - Đăng nhập</h1>

        <!-- Container form Đăng nhập -->
        <div id="login-container" class="form-container">
            <p>Đăng nhập tài khoản:</p>
            <!-- Đường dẫn ví dụ: ../../controller/authController.php?action=login -->
            <form id="login-form" action="../../controller/authController.php?action=login" method="POST"
                class="space-y-4">
                <input type="email" name="email" placeholder="Email/SĐT của bạn"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-gray-500" required />
                <div class="relative">
                    <input id="password-input" type="password" name="password" placeholder="Mật khẩu"
                        class="w-full px-4 py-2 border rounded pr-12 focus:outline-none focus:border-gray-500"
                        required />
                    <button type="button" id="toggle-password" class="absolute inset-y-0 right-3 flex items-center">
                        <span id="eye-icon">
                            <!-- Icon eye-open -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </button>
                </div>
                <button type="submit"
                    class="w-full bg-black text-white py-2 rounded transition-colors duration-200 hover:bg-gray-200 hover:text-black">ĐĂNG
                    NHẬP</button>
            </form>
            <div class="flex justify-between mt-4 text-sm">
                <span id="switch-to-signup" class="text-blue-600 cursor-pointer">Chưa có tài khoản? Đăng ký</span>
                <a href="#" class="text-blue-600">Quên mật khẩu</a>
            </div>
        </div>

        <!-- Container form Đăng ký (ẩn mặc định) -->
        <div id="signup-container" class="form-container hidden">
            <p>Đăng ký tài khoản mới:</p>
            <!-- Đường dẫn ví dụ: ../../controller/authController.php?action=register -->
            <form id="signup-form" action="../../controller/authController.php?action=register" method="POST"
                class="space-y-4">
                <input type="text" name="full_name" placeholder="Họ và tên"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-gray-500" required />
                <input type="email" name="email" placeholder="Email của bạn"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-gray-500" required />
                <input type="text" name="phone" placeholder="Số điện thoại"
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:border-gray-500" required />
                <div class="relative">
                    <input id="signup-password-input" type="password" name="password" placeholder="Mật khẩu"
                        class="w-full px-4 py-2 border rounded pr-12 focus:outline-none focus:border-gray-500"
                        required />
                    <button type="button" id="toggle-signup-password"
                        class="absolute inset-y-0 right-3 flex items-center">
                        <span id="signup-eye-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="relative">
                    <input id="signup-confirm-password-input" type="password" placeholder="Xác nhận mật khẩu"
                        class="w-full px-4 py-2 border rounded pr-12 focus:outline-none focus:border-gray-500"
                        required />
                    <button type="button" id="toggle-signup-confirm-password"
                        class="absolute inset-y-0 right-3 flex items-center">
                        <span id="signup-confirm-eye-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </span>
                    </button>
                </div>
                <button type="submit"
                    class="w-full bg-black text-white py-2 rounded transition-colors duration-200 hover:bg-gray-200 hover:text-black">ĐĂNG
                    KÝ</button>
            </form>
            <div class="flex justify-end mt-4 text-sm">
                <span id="switch-to-login" class="text-blue-600 cursor-pointer">Đã có tài khoản? Đăng nhập</span>
            </div>
        </div>
    </div>
</div>

<script>
// Mặc định hiển thị form đăng nhập khi modal mở
function showLoginForm() {
    document.getElementById("modal-title").textContent = "FYSYLE CLUB - Đăng nhập";
    document.getElementById("login-container").classList.remove("hidden");
    document.getElementById("signup-container").classList.add("hidden");
}
// Chuyển sang form đăng ký
function showSignupForm() {
    document.getElementById("modal-title").textContent = "FYSYLE CLUB - Đăng ký";
    document.getElementById("login-container").classList.add("hidden");
    document.getElementById("signup-container").classList.remove("hidden");
}

// Hiển thị modal (loại bỏ lớp ẩn)
function showAuthModal() {
    document.getElementById("auth-modal").classList.remove("modal-hidden");
    showLoginForm(); // Mặc định form đăng nhập
}
// Ẩn modal
function hideAuthModal() {
    document.getElementById("auth-modal").classList.add("modal-hidden");
}

// Toggle mật khẩu cho form đăng nhập
const togglePassword = document.getElementById("toggle-password");
const passwordInput = document.getElementById("password-input");
const eyeIcon = document.getElementById("eye-icon");

const eyeOpenSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
    </svg>
  `;
const eyeClosedSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
         viewBox="0 0 24 24" stroke="currentColor">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 011.658-3.07m2.2-2.2A9.977 9.977 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.658 3.07m-2.2 2.2A9.977 9.977 0 0112 19c-1.657 0-3.205-.407-4.575-1.125" />
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
         <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" stroke-width="2"
               stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
  `;

togglePassword.addEventListener("click", () => {
    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.innerHTML = eyeClosedSVG;
    } else {
        passwordInput.type = "password";
        eyeIcon.innerHTML = eyeOpenSVG;
    }
});

// Toggle mật khẩu cho form đăng ký
const toggleSignupPassword = document.getElementById("toggle-signup-password");
const signupPasswordInput = document.getElementById("signup-password-input");
const signupEyeIcon = document.getElementById("signup-eye-icon");

const toggleSignupConfirmPassword = document.getElementById("toggle-signup-confirm-password");
const signupConfirmPasswordInput = document.getElementById("signup-confirm-password-input");
const signupConfirmEyeIcon = document.getElementById("signup-confirm-eye-icon");

if (toggleSignupPassword) {
    toggleSignupPassword.addEventListener("click", () => {
        if (signupPasswordInput.type === "password") {
            signupPasswordInput.type = "text";
            signupEyeIcon.innerHTML = eyeClosedSVG;
        } else {
            signupPasswordInput.type = "password";
            signupEyeIcon.innerHTML = eyeOpenSVG;
        }
    });
}
if (toggleSignupConfirmPassword) {
    toggleSignupConfirmPassword.addEventListener("click", () => {
        if (signupConfirmPasswordInput.type === "password") {
            signupConfirmPasswordInput.type = "text";
            signupConfirmEyeIcon.innerHTML = eyeClosedSVG;
        } else {
            signupConfirmPasswordInput.type = "password";
            signupConfirmEyeIcon.innerHTML = eyeOpenSVG;
        }
    });
}

// Nút đóng modal: chỉ ẩn modal
const closeAuthModal = document.getElementById("close-auth-modal");
closeAuthModal.addEventListener("click", () => {
    hideAuthModal();
});

// Chuyển đổi giữa form đăng nhập và đăng ký
document.getElementById("switch-to-signup").addEventListener("click", () => {
    showSignupForm();
});
document.getElementById("switch-to-login").addEventListener("click", () => {
    showLoginForm();
});

// Hàm hiển thị toast thông báo (nếu cần)
function showNotification(message, type = 'info', duration = 3000) {
    const notification = document.createElement('div');
    let bgColor;
    switch (type) {
        case 'success':
            bgColor = 'bg-green-500';
            break;
        case 'error':
            bgColor = 'bg-red-500';
            break;
        default:
            bgColor = 'bg-blue-500';
            break;
    }
    notification.className =
        `${bgColor} text-white px-4 py-2 rounded shadow-lg opacity-0 transform -translate-y-2 transition-all duration-300`;
    notification.textContent = message;
    const container = document.getElementById('notificationContainer');
    container.appendChild(notification);
    requestAnimationFrame(() => {
        notification.classList.remove('opacity-0', '-translate-y-2');
    });
    setTimeout(() => {
        notification.classList.add('opacity-0', '-translate-y-2');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, duration);
}
</script>