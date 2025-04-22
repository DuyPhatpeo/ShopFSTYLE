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

// Hàm hiển thị toast thông báo (nếu cần)
function showNotification(message, type = "info", duration = 3000) {
  const notification = document.createElement("div");
  let bgColor;
  switch (type) {
    case "success":
      bgColor = "bg-green-500";
      break;
    case "error":
      bgColor = "bg-red-500";
      break;
    default:
      bgColor = "bg-blue-500";
      break;
  }
  notification.className = `${bgColor} text-white px-4 py-2 rounded shadow-lg opacity-0 transform -translate-y-2 transition-all duration-300`;
  notification.textContent = message;
  const container = document.getElementById("notificationContainer");
  container.appendChild(notification);
  requestAnimationFrame(() => {
    notification.classList.remove("opacity-0", "-translate-y-2");
  });
  setTimeout(() => {
    notification.classList.add("opacity-0", "-translate-y-2");
    setTimeout(() => {
      notification.remove();
    }, 300);
  }, duration);
}
