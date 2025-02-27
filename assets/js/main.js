// Lấy nút "Back to Top" và thêm sự kiện cuộn trang
const backToTopButton = document.getElementById("back-to-top");
window.addEventListener("scroll", () => {
  // Hiển thị nút "Back to Top" khi cuộn xuống quá 300px
  if (window.scrollY > 300) {
    backToTopButton.classList.remove("hidden");
  } else {
    backToTopButton.classList.add("hidden");
  }
});
// Cuộn lên đầu trang khi click vào nút "Back to Top"
backToTopButton.addEventListener("click", () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
});

// Lấy các phần tử liên quan đến menu di động
const menuToggle = document.getElementById("menu-toggle");
const mobileMenu = document.getElementById("mobile-menu");
const closeMenu = document.getElementById("close-menu");

// Mở mobile menu khi click vào nút menu-toggle
menuToggle.addEventListener("click", () => {
  mobileMenu.classList.toggle("-translate-x-full");
});

// Đóng mobile menu khi click vào nút close-menu
closeMenu.addEventListener("click", () => {
  mobileMenu.classList.add("-translate-x-full");
});

// JavaScript để mở và đóng modal đăng nhập
const profileButtons = document.querySelectorAll(".profile-btn");
const authModal = document.getElementById("auth-modal");
const closeAuthModal = document.getElementById("close-auth-modal");

// Mở modal khi nhấn vào icon profile
profileButtons.forEach((btn) => {
  btn.addEventListener("click", () => {
    authModal.classList.remove("hidden");
  });
});

// Đóng modal khi nhấn vào nút đóng
closeAuthModal.addEventListener("click", () => {
  authModal.classList.add("hidden");
});

// Đóng modal khi nhấn ra ngoài modal
window.addEventListener("click", (event) => {
  if (event.target === authModal) {
    authModal.classList.add("hidden");
  }
});
// --- Toggle mật khẩu cho form đăng nhập ---
const togglePassword = document.getElementById("toggle-password");
const passwordInput = document.getElementById("password-input");
const eyeIcon = document.getElementById("eye-icon");

togglePassword.addEventListener("click", () => {
  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    eyeIcon.src = "assets/icons/eye-closed.svg";
    eyeIcon.alt = "Hide password";
  } else {
    passwordInput.type = "password";
    eyeIcon.src = "assets/icons/eye-open.svg";
    eyeIcon.alt = "Show password";
  }
});

// --- Toggle mật khẩu cho form đăng ký (Mật khẩu) ---
const toggleSignupPassword = document.getElementById("toggle-signup-password");
const signupPasswordInput = document.getElementById("signup-password-input");
const signupEyeIcon = document.getElementById("signup-eye-icon");

toggleSignupPassword.addEventListener("click", () => {
  if (signupPasswordInput.type === "password") {
    signupPasswordInput.type = "text";
    signupEyeIcon.src = "assets/icons/eye-closed.svg";
    signupEyeIcon.alt = "Hide password";
  } else {
    signupPasswordInput.type = "password";
    signupEyeIcon.src = "assets/icons/eye-open.svg";
    signupEyeIcon.alt = "Show password";
  }
});

// --- Toggle mật khẩu cho form đăng ký (Xác nhận mật khẩu) ---
const toggleSignupConfirmPassword = document.getElementById(
  "toggle-signup-confirm-password"
);
const signupConfirmPasswordInput = document.getElementById(
  "signup-confirm-password-input"
);
const signupConfirmEyeIcon = document.getElementById("signup-confirm-eye-icon");

toggleSignupConfirmPassword.addEventListener("click", () => {
  if (signupConfirmPasswordInput.type === "password") {
    signupConfirmPasswordInput.type = "text";
    signupConfirmEyeIcon.src = "assets/icons/eye-closed.svg";
    signupConfirmEyeIcon.alt = "Hide password";
  } else {
    signupConfirmPasswordInput.type = "password";
    signupConfirmEyeIcon.src = "assets/icons/eye-open.svg";
    signupConfirmEyeIcon.alt = "Show password";
  }
});

// --- Chuyển đổi giữa form Đăng nhập và Đăng ký ---
const loginContainer = document.getElementById("login-container");
const signupContainer = document.getElementById("signup-container");
const showSignup = document.getElementById("show-signup");
const showLogin = document.getElementById("show-login");
const modalTitle = document.getElementById("modal-title");

showSignup.addEventListener("click", () => {
  loginContainer.classList.add("hidden");
  signupContainer.classList.remove("hidden");
  modalTitle.innerHTML = `FYSYLE <span class="text-blue-800">CLUB</span> - Đăng ký`;
});

showLogin.addEventListener("click", () => {
  signupContainer.classList.add("hidden");
  loginContainer.classList.remove("hidden");
  modalTitle.innerHTML = `FYSYLE <span class="text-blue-800">CLUB</span> - Đăng nhập`;
});

// --- Xử lý hiện hộp thoại tìm kiếm khi ở mobile - search ---
document.addEventListener("DOMContentLoaded", function () {
  // Lấy các phần tử liên quan đến tìm kiếm di động
  const mobileSearchBtn = document.getElementById("mobile-search-btn");
  const mobileSearch = document.getElementById("mobile-search");
  const closeSearch = document.getElementById("close-search");

  if (mobileSearchBtn && mobileSearch && closeSearch) {
    // Hiển thị hộp thoại tìm kiếm khi click vào icon tìm kiếm
    mobileSearchBtn.addEventListener("click", () => {
      mobileSearch.classList.remove("-translate-y-full");
      mobileSearch.classList.add("translate-y-0");
    });

    // Đóng hộp thoại tìm kiếm khi click vào icon đóng
    closeSearch.addEventListener("click", () => {
      mobileSearch.classList.remove("translate-y-0");
      mobileSearch.classList.add("-translate-y-full");
    });
  } else {
    console.error(
      "Không tìm thấy phần tử mobile-search hoặc mobile-search-btn."
    );
  }
});

document.addEventListener("DOMContentLoaded", function () {
  console.log("JS đã tải thành công!");
});
