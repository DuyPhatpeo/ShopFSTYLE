/**
 * Hàm tạo thông báo mới
 * @param {string} message Nội dung thông báo
 * @param {string} type Loại thông báo ('success' hoặc 'failure')
 */
function createNotification(message, type) {
  const container = document.getElementById("notificationContainer");
  const notification = document.createElement("div");
  let bgClass, borderClass, textClass, title;

  if (type === "success") {
    bgClass = "bg-green-100";
    borderClass = "border-green-400";
    textClass = "text-green-700";
    title = "Thành công!";
  } else if (type === "failure") {
    bgClass = "bg-red-100";
    borderClass = "border-red-400";
    textClass = "text-red-700";
    title = "Thất bại!";
  } else {
    // Mặc định nếu type không hợp lệ
    bgClass = "bg-gray-100";
    borderClass = "border-gray-400";
    textClass = "text-gray-700";
    title = "Thông báo:";
  }

  notification.className = `notification ${bgClass} border ${borderClass} ${textClass} px-4 py-3 rounded shadow`;
  notification.innerHTML = `<strong class="font-bold">${title}</strong> <span>${message}</span>`;

  // Thêm thông báo mới vào đầu container
  container.prepend(notification);

  // Hiệu ứng xuất hiện
  setTimeout(() => {
    notification.classList.add("show");
  }, 100);

  // Sau 3 giây, ẩn thông báo và xoá khỏi DOM
  setTimeout(() => {
    notification.classList.remove("show");
    setTimeout(() => {
      notification.remove();
    }, 500);
  }, 3000);
}

// Lấy message và type từ URL do controller trả về
(function () {
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get("msg");
  // Kiểm tra nếu có tham số type, nếu không thì mặc định là success
  const type = urlParams.get("type") || "success";

  if (msg) {
    createNotification(msg, type);
  }
})();
