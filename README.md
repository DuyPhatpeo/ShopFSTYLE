# 🧥 ShopFSTYLE – Website Bán Hàng Thời Trang

> “Xây dựng website bán hàng cho shop thời trang FSTYLE”  
> Đề tài chuyên đề tốt nghiệp – Đại học Nha Trang, Khoa CNTT  
> Sinh viên thực hiện: **Trần Duy Phát** – MSSV: 63135054  
> Giảng viên hướng dẫn: **ThS. Nguyễn Thị Hương Lý**

---

## 🎯 Mục tiêu đề tài

Dự án nhằm xây dựng một **website bán hàng trực tuyến cho Shop FStyle**, giúp số hóa quy trình bán hàng, quản lý sản phẩm, đơn hàng, khách hàng và nâng cao trải nghiệm người dùng. Website gồm 2 phân hệ:

- **Khách hàng:** Duyệt sản phẩm, giỏ hàng, đặt hàng, theo dõi đơn hàng, yêu thích…
- **Admin:** Quản lý sản phẩm (biến thể màu - size), đơn hàng, khách hàng, danh mục, khuyến mãi, báo cáo.

---

## 📌 Tính năng nổi bật

### 👤 Người dùng
- Đăng ký/Đăng nhập
- Xem sản phẩm theo danh mục, chi tiết
- Thêm sản phẩm vào giỏ hàng
- Yêu thích sản phẩm
- Theo dõi đơn hàng

### 🛠️ Quản trị viên
- Quản lý sản phẩm với màu, size, tồn kho, ảnh
- Quản lý danh mục cha - con, thương hiệu, banner
- Quản lý đơn hàng và cập nhật trạng thái
- Quản lý người dùng, phân quyền bằng role
- Xem dashboard và thống kê doanh thu

---

## 🧱 Công nghệ sử dụng

- **Frontend:** HTML, CSS, Tailwind CSS, JavaScript, Bootstrap (một số phần)
- **Backend:** PHP thuần (Không sử dụng framework)
- **Database:** MySQL (thiết kế chuẩn hóa, có ERD, physical schema, 20+ bảng)
- **Khác:** UUID, Xử lý ảnh, Phân trang, Giao diện động, Thông báo Tailwind, Responsive

---

## ⚙️ Cài đặt & chạy dự án

```bash
# Clone dự án
git clone https://github.com/DuyPhatpeo/ShopFSTYLE.git

# Trỏ đến thư mục gốc
cd ShopFSTYLE

# Import CSDL: Tạo database 'shopfstyle' và import file .sql (nếu có)

# Chạy trên localhost (XAMPP, Laragon,...)
http://localhost/ShopFSTYLE
