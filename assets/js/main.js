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

// .......................................... hiệu ứng product details ...............................................
// Các biến toàn cục dùng cho modal ảnh
let currentImageIndex = 0;
const images = Array.from(document.querySelectorAll(".flex-col img"));

// Hàm cập nhật transform cho modal (scale và offset)
function updateModalTransform(scale, offsetX, offsetY) {
  document.getElementById(
    "modalImage"
  ).style.transform = `scale(${scale}) translate(${offsetX}px, ${offsetY}px)`;
}

// Thay đổi ảnh chính và cập nhật trạng thái selected cho ảnh nhỏ
function changeImage(element) {
  document.getElementById("mainImage").src = element.src;
  images.forEach((img) => img.classList.add("opacity-50"));
  element.classList.remove("opacity-50");
  currentImageIndex = images.indexOf(element);
}
document.querySelector(".flex-col img").classList.remove("opacity-50");

// Mở modal với ảnh được chọn và reset trạng thái zoom
function openModal(src) {
  const modalImg = document.getElementById("modalImage");
  modalImg.src = src;
  currentScale = 1;
  currentOffsetX = 0;
  currentOffsetY = 0;
  updateModalTransform(currentScale, currentOffsetX, currentOffsetY);
  modalImg.style.cursor = "zoom-in";
  document.getElementById("modalHint").textContent =
    "Chạm vào ảnh để phóng to & giữ chuột trái để di chuyển";
  document.getElementById("imageModal").classList.remove("hidden");
}

// Đóng modal khi click ngoài ảnh hoặc nhấn nút đóng
function closeModal(event) {
  if (!event || event.target === document.getElementById("imageModal")) {
    document.getElementById("imageModal").classList.add("hidden");
  }
}

// Các biến điều khiển zoom và kéo
let currentScale = 1;
let currentOffsetX = 0;
let currentOffsetY = 0;
let isDragging = false;
let pointerDown = false;
let startX = 0,
  startY = 0;
const dragThreshold = 5; // pixel

const modalImage = document.getElementById("modalImage");

// Khi nhấn xuống: chỉ xử lý sự kiện nếu nhấn chuột trái (button === 0)
modalImage.addEventListener("pointerdown", function (e) {
  if (e.button !== 0) return; // Chỉ xử lý chuột trái
  pointerDown = true;
  isDragging = false;
  startX = e.clientX;
  startY = e.clientY;
  e.preventDefault();
});

// Khi di chuyển, nếu đã zoom (scale > 1) mới cho kéo
modalImage.addEventListener("pointermove", function (e) {
  if (!pointerDown) return;
  if (currentScale === 1) return; // Nếu chưa zoom thì không cho kéo
  let dx = e.clientX - startX;
  let dy = e.clientY - startY;
  if (
    !isDragging &&
    (Math.abs(dx) > dragThreshold || Math.abs(dy) > dragThreshold)
  ) {
    isDragging = true;
  }
  if (isDragging) {
    let newOffsetX = currentOffsetX + dx;
    let newOffsetY = currentOffsetY + dy;
    updateModalTransform(currentScale, newOffsetX, newOffsetY);
  }
  e.preventDefault();
});

// Khi nhả chuột: nếu chưa zoom thì zoom in, nếu đã zoom mà kéo thì cập nhật offset, còn không thì toggle zoom (zoom out)
modalImage.addEventListener("pointerup", function (e) {
  if (!pointerDown) return;
  if (currentScale === 1) {
    toggleZoom();
  } else {
    if (isDragging) {
      let dx = e.clientX - startX;
      let dy = e.clientY - startY;
      currentOffsetX += dx;
      currentOffsetY += dy;
      updateModalTransform(currentScale, currentOffsetX, currentOffsetY);
    } else {
      toggleZoom();
    }
  }
  pointerDown = false;
  isDragging = false;
});

// Nếu pointer bị hủy
modalImage.addEventListener("pointercancel", function (e) {
  pointerDown = false;
});

// Hàm toggle zoom: nếu chưa zoom (scale=1) thì phóng to, ngược lại thu nhỏ và reset vị trí
function toggleZoom() {
  if (currentScale === 1) {
    currentScale = 1.5;
    modalImage.style.cursor = "grab";
    document.getElementById("modalHint").textContent =
      "Giữ chuột trái để di chuyển, chạm vào ảnh để thu nhỏ";
  } else {
    currentScale = 1;
    currentOffsetX = 0;
    currentOffsetY = 0;
    modalImage.style.cursor = "zoom-in";
    document.getElementById("modalHint").textContent =
      "Chạm vào ảnh để phóng to & giữ chuột trái để di chuyển";
  }
  updateModalTransform(currentScale, currentOffsetX, currentOffsetY);
}

// Xử lý phím: chỉ khi modal mở, xử lý Escape, ArrowLeft, ArrowRight và bỏ qua phím "d"
document.addEventListener("keydown", function (event) {
  // Chỉ xử lý khi modal đang mở
  const modal = document.getElementById("imageModal");
  if (modal.classList.contains("hidden")) return;

  // Nếu nhấn phím "d" (hoặc "D"), ngăn không cho xử lý thêm
  if (event.key.toLowerCase() === "d" || event.key.toLowerCase() === "a") {
    event.preventDefault();
    return;
  }

  if (event.key === "Escape") {
    closeModal();
  } else if (event.key === "ArrowLeft") {
    showPrevImage();
  } else if (event.key === "ArrowRight") {
    showNextImage();
  }
});

document.getElementById("prevImage").addEventListener("click", showPrevImage);
document.getElementById("nextImage").addEventListener("click", showNextImage);

function showPrevImage() {
  currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
  openModal(images[currentImageIndex].src);
}

function showNextImage() {
  currentImageIndex = (currentImageIndex + 1) % images.length;
  openModal(images[currentImageIndex].src);
}

// Xử lý tăng giảm số lượng sản phẩm
const decreaseQty = document.getElementById("decreaseQty");
const increaseQty = document.getElementById("increaseQty");
const productQty = document.getElementById("productQty");

decreaseQty.addEventListener("click", () => {
  let qty = parseInt(productQty.textContent);
  if (qty > 1) {
    productQty.textContent = qty - 1;
  }
});

increaseQty.addEventListener("click", () => {
  let qty = parseInt(productQty.textContent);
  productQty.textContent = qty + 1;
});

// Xử lý chọn màu
function selectColor(element) {
  if (element.classList.contains("out-of-stock")) return; // Không cho chọn màu hết hàng

  const swatches = document.querySelectorAll(".color-swatch");
  swatches.forEach((el) => {
    el.classList.remove("border-4", "border-blue-500", "selected");
    el.classList.add("border", "border-gray-400");
  });

  element.classList.remove("border-gray-400");
  element.classList.add("border-4", "border-blue-500", "selected");
}

// Tự động chọn màu và ảnh đầu tiên khi trang được tải
window.onload = function () {
  const firstSwatch = document.querySelector(
    ".color-swatch:not(.out-of-stock)"
  ); // Chỉ chọn màu còn hàng
  if (firstSwatch) {
    selectColor(firstSwatch);
  }

  const firstImage = document.querySelector(".flex-col img");
  if (firstImage) {
    firstImage.classList.remove("opacity-50");
  }
};

// Xử lý chọn size
document.querySelectorAll(".size-button").forEach(function (button) {
  button.addEventListener("click", function () {
    if (this.hasAttribute("disabled")) return;
    document.querySelectorAll(".size-button").forEach((btn) => {
      btn.classList.remove("bg-black", "text-white");
      btn.classList.add("hover:bg-gray-200");
    });
    this.classList.add("bg-black", "text-white");
    this.classList.remove("hover:bg-gray-200");
  });
});
document.getElementById("openSizeGuide").addEventListener("click", function () {
  document.getElementById("sizeGuideModal").classList.remove("hidden");
});

document
  .getElementById("closeSizeGuide")
  .addEventListener("click", function () {
    document.getElementById("sizeGuideModal").classList.add("hidden");
  });

document
  .getElementById("sizeGuideModal")
  .addEventListener("click", function (e) {
    if (e.target === this) {
      this.classList.add("hidden");
    }
  });
