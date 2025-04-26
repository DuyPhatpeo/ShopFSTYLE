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
