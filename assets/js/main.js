//.......................................... hiệu ứng cuộn trang ...............................................
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
//.......................................... hiệu ứng menu ...............................................
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
// ............................... hộp thoại tìm kiếm ...............................................
// Hiển thị hộp thoại tìm kiếm
document.addEventListener("DOMContentLoaded", function () {
  const searchModal = document.getElementById("search-modal");
  const mobileSearchBtn = document.getElementById("mobile-search-btn");
  const desktopSearchBtn = document.getElementById("desktop-search-btn");
  const desktopSearchInput = document.getElementById("desktop-search-input");
  const closeSearchModalBtn = document.getElementById("close-search-modal");

  // Hiển thị modal
  function showSearchModal() {
    searchModal.classList.remove("opacity-0", "pointer-events-none");
    const modalContent = searchModal.querySelector("div");
    modalContent.classList.remove("-translate-y-full");
  }

  // Ẩn modal
  function hideSearchModal() {
    const modalContent = searchModal.querySelector("div");
    modalContent.classList.add("-translate-y-full");
    setTimeout(() => {
      searchModal.classList.add("opacity-0", "pointer-events-none");
    }, 300);
  }

  // Nút tìm kiếm mobile
  if (mobileSearchBtn) {
    mobileSearchBtn.addEventListener("click", showSearchModal);
  }
  // Nút tìm kiếm desktop (icon)
  if (desktopSearchBtn) {
    desktopSearchBtn.addEventListener("click", showSearchModal);
  }
  // Ô input desktop (bấm vào để mở modal)
  if (desktopSearchInput) {
    desktopSearchInput.addEventListener("focus", function (e) {
      e.preventDefault();
      showSearchModal();
    });
    desktopSearchInput.addEventListener("click", function (e) {
      e.preventDefault();
      showSearchModal();
    });
  }
  // Nút đóng modal
  if (closeSearchModalBtn) {
    closeSearchModalBtn.addEventListener("click", hideSearchModal);
  }
});
//.......................................... hộp thoại đăng nhập đăng ký ...............................................

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

const eyeOpenSVG = `
<!-- eye-open.svg -->
<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
</svg>
`;

const eyeClosedSVG = `
<!-- eye-closed.svg -->
<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 011.658-3.07m2.2-2.2A9.977 9.977 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.05 10.05 0 01-1.658 3.07m-2.2 2.2A9.977 9.977 0 0112 19c-1.657 0-3.205-.407-4.575-1.125" />
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
  <line x1="4" y1="4" x2="20" y2="20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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

// --- Toggle mật khẩu cho form đăng ký (Mật khẩu) ---
const toggleSignupPassword = document.getElementById("toggle-signup-password");
const signupPasswordInput = document.getElementById("signup-password-input");
const signupEyeIcon = document.getElementById("signup-eye-icon");

toggleSignupPassword.addEventListener("click", () => {
  if (signupPasswordInput.type === "password") {
    signupPasswordInput.type = "text";
    signupEyeIcon.innerHTML = eyeClosedSVG;
  } else {
    signupPasswordInput.type = "password";
    signupEyeIcon.innerHTML = eyeOpenSVG;
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
    signupConfirmEyeIcon.innerHTML = eyeClosedSVG;
  } else {
    signupConfirmPasswordInput.type = "password";
    signupConfirmEyeIcon.innerHTML = eyeOpenSVG;
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
