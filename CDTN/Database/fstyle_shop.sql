-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 24, 2025 lúc 06:28 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `fstyle_shop`
--
CREATE DATABASE IF NOT EXISTS `fstyle_shop` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `fstyle_shop`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `admin_id` varchar(50) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`, `role_id`, `email`, `full_name`) VALUES
('a1a1a1a1-f40a-4865-8ace-111111111111', 'minhtam', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'minhtam01@gmail.com', 'Lê Minh Tâm'),
('a3a3a3a3-f40a-4865-8ace-777788889999', 'kimngan', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'ngankim13@gmail.com', 'Đỗ Kim Ngân'),
('a7a7a7a7-f40a-4865-8ace-777777777777', 'linhanh', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'anhlinh07@gmail.com', 'Linh Thị Anh'),
('a9a9a9a9-f40a-4865-8ace-314231423142', 'tienthanh', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'thanhtien19@gmail.com', 'Tiến Thành'),
('ab974bc6-e043-4ba2-81e7-d9b36120d967', 'admin', '$2y$10$zzkOTx/hAmxO3yJJexZ8LegZYsi11Cs1vS1dLKe8OetHDnro1r9lC', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'admin@gmail.com', 'Admin Fstyle'),
('b0b0b0b0-f40a-4865-8ace-161616161616', 'myduyen', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'duyenmy20@gmail.com', 'Mỹ Duyên'),
('b2b2b2b2-f40a-4865-8ace-222222222222', 'huynhanh', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'anhhuynh02@gmail.com', 'Huỳnh Thị Ánh'),
('b4b4b4b4-f40a-4865-8ace-aaaabbbbcccc', 'trunghieu', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'hieutrung14@gmail.com', 'Phan Trung Hiếu'),
('b8b8b8b8-f40a-4865-8ace-888888888888', 'hungphat', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'phathung08@gmail.com', 'Nguyễn Hùng Phát'),
('c3c3c3c3-f40a-4865-8ace-333333333333', 'tuanhuy', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'huytuan03@gmail.com', 'Nguyễn Tuấn Huy'),
('c5c5c5c5-f40a-4865-8ace-ddddeeeeffff', 'thanhha', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'hathanh15@gmail.com', 'Thanh Hà'),
('c9c9c9c9-f40a-4865-8ace-999999999999', 'thaongoc', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'ngocthao09@gmail.com', 'Thảo Ngọc'),
('d0d0d0d0-f40a-4865-8ace-000000000000', 'manhcuong', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'cuongmanh10@gmail.com', 'Trần Mạnh Cường'),
('d4d4d4d4-f40a-4865-8ace-444444444444', 'thutrang', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'trangthu04@gmail.com', 'Trần Thu Trang'),
('d6d6d6d6-f40a-4865-8ace-123412341234', 'thienbao', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'baothien16@gmail.com', 'Trịnh Thiên Bảo'),
('e5e5e5e5-f40a-4865-8ace-555555555555', 'quocviet', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'vietquoc05@gmail.com', 'Phạm Quốc Việt'),
('e7e7e7e7-f40a-4865-8ace-567856785678', 'kimphung', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'phungkim17@gmail.com', 'Kim Phụng'),
('f2f2f2f2-f40a-4865-8ace-444455556666', 'nhatlien', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'liennhat12@gmail.com', 'Lý Nhật Liên'),
('f6f6f6f6-f40a-4865-8ace-666666666666', 'kienhoang', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'hoangkien06@gmail.com', 'Hoàng Văn Kiên'),
('f8f8f8f8-f40a-4865-8ace-910191019101', 'dinhhieu', '$2y$10$XZX5IoXDGD9YfDQOH1qSheJRuNBvGXAL176u1dao4imf/5dyvRGIq', '7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'hieudinh18@gmail.com', 'Đinh Hữu Hiếu');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `banner`
--

CREATE TABLE `banner` (
  `banner_id` varchar(50) NOT NULL,
  `banner_name` varchar(255) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `banner`
--

INSERT INTO `banner` (`banner_id`, `banner_name`, `image_url`, `link`, `status`) VALUES
('64506c04-5523-4eea-8962-36cab1dbe55f', 'Ảnh mới', 'admin/uploads/banners/banner_1746582691_681abca36b8ee.png', NULL, 1),
('9ab3d668-b082-44d3-b25c-7fe85f8275b7', 'Ảnh 2', 'admin/uploads/banners/banner_1746582131_681aba73bee2d.png', NULL, 1),
('f2550c50-6b2c-4520-a481-7f97883bf65e', 'Ảnh tiếp', 'admin/uploads/banners/banner_1746582520_681abbf8cc8ad.jpg', NULL, 1),
('fbac0034-2378-48ec-b87a-bd3665035fee', 'Ảnh đầu', 'admin/uploads/banners/banner_1746582679_681abc9754453.png', NULL, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `brand`
--

CREATE TABLE `brand` (
  `brand_id` varchar(50) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL,
  `brand_slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `brand`
--

INSERT INTO `brand` (`brand_id`, `brand_name`, `status`, `image_url`, `brand_slug`) VALUES
('1f4fb6a6-d776-441e-bc73-0f6772ba2e0b', 'Adidas', 1, 'admin/uploads/brands/brand-adidas.png', 'adidas'),
('21a7d2a1-fd19-4935-a719-ee0778554177', 'DirtyCoins', 1, 'admin/uploads/brands/brand-dirtycoinspng', 'dirtycoins'),
('5453326f-916e-489c-9086-8066094c89bc', 'Nike', 1, 'admin/uploads/brands/brand-nike.png', 'nike'),
('e61798f4-7e60-44fe-a562-7775eae6e6e0', 'CoolMate', 1, 'admin/uploads/brands/brand-coolmate.png', 'coolmate');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `cart_id` varchar(50) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `created_at`, `updated_at`) VALUES
('cart_681729e70d543', '7dcbf099-169c-4327-9c1c-63fc9c155841', '2025-05-04 15:48:39', '2025-05-04 15:48:39');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_item_id` char(36) NOT NULL,
  `cart_id` char(36) NOT NULL,
  `variant_id` char(36) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `is_selected` tinyint(1) DEFAULT 1,
  `added_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart_items`
--

INSERT INTO `cart_items` (`cart_item_id`, `cart_id`, `variant_id`, `quantity`, `is_selected`, `added_at`) VALUES
('item_681a369010c58', 'cart_681729e70d543', '76efdf3e-e137-450e-b870-14f59276eba9', 1, 1, '2025-05-06 23:19:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `category`
--

CREATE TABLE `category` (
  `category_id` varchar(50) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_slug` varchar(255) NOT NULL,
  `parent_id` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_slug`, `parent_id`, `status`, `image_url`) VALUES
('0f783d1e-d1c7-41c5-ac51-5f312926ad61', 'Áo khoác', 'ao_khoac', 'c8a1d313-89c5-4d0f-b841-3beb1076e79c', 1, 'admin/uploads/categories/category-ao_khoac.webp'),
('1a153355-0819-42bb-a839-e741000718cb', 'Phụ kiện', 'phu_kien', '', 1, 'admin/uploads/categories/category-phu_kien.webp'),
('704088f0-3bbb-4d1c-a2fe-d0c25b919b89', 'Áo sơ mi', 'ao_so_mi', 'c8a1d313-89c5-4d0f-b841-3beb1076e79c', 1, 'admin/uploads/categories/category-ao_so_mi.webp'),
('b46e14b6-d781-46eb-b67e-5afe9d4b91ed', 'Quần', 'quan', '', 1, 'admin/uploads/categories/category-quan.webp'),
('c8a1d313-89c5-4d0f-b841-3beb1076e79c', 'Áo', 'ao', '', 1, 'admin/uploads/categories/category-ao.webp'),
('e34aa81b-5122-4b08-b5c9-c7b815472b50', 'Quần dài', 'quan_dai', 'b46e14b6-d781-46eb-b67e-5afe9d4b91ed', 1, 'admin/uploads/categories/category-quan_dai.webp'),
('e9348463-1ac7-41a0-84bf-b37f444376cd', 'Áo thun', 'ao_thun', 'c8a1d313-89c5-4d0f-b841-3beb1076e79c', 1, 'admin/uploads/categories/category-ao_thun.webp'),
('f68e32f1-bf76-4bdc-936c-e670a3805f0b', 'Quần short', 'quan_short', 'b46e14b6-d781-46eb-b67e-5afe9d4b91ed', 1, 'admin/uploads/categories/category-quan_short.webp');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `color`
--

CREATE TABLE `color` (
  `color_id` varchar(50) NOT NULL,
  `color_name` varchar(50) NOT NULL,
  `color_code` varchar(20) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `color`
--

INSERT INTO `color` (`color_id`, `color_name`, `color_code`, `status`) VALUES
('12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', 'Đỏ', '#FF0000', 1),
('4e22ee56-264c-46c5-bf4c-4c836ce32625', 'Be', '#fffdd0', 1),
('a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', 'Xanh dương', '#0000FF', 1),
('a2b3c4d5-5678-4171-bbcc-44ee55ff6677', 'Nâu', '#A52A2A', 1),
('b5d6f7a8-5678-4c2c-ccdd-44ff55aa6677', 'Xanh lá', '#00FF00', 1),
('b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'Đen', '#000000', 1),
('c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'Trắng', '#FFFFFF', 1),
('c9e8f0b2-7890-4d3d-ddee-66aa77bb8899', 'Vàng', '#FFFF00', 1),
('d3f1a4b6-2345-4e4e-eeff-88bb99cc0011', 'Cam', '#FFA500', 1),
('d4e5f6a7-8901-4404-eeff-00bb11cc2233', 'Xám', '#808080', 1),
('e7a9b8c0-3456-4f5f-ffaa-00cc11dd2233', 'Tím', '#800080', 1),
('f1c2d3e4-4567-4060-aabb-22dd33ee4455', 'Hồng', '#FFC0CB', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `customer`
--

CREATE TABLE `customer` (
  `customer_id` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `is_verified` tinyint(1) DEFAULT 0,
  `verify_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `customer`
--

INSERT INTO `customer` (`customer_id`, `email`, `password`, `full_name`, `phone`, `address`, `status`, `is_verified`, `verify_code`) VALUES
('7dcbf099-169c-4327-9c1c-63fc9c155841', 'phattranduy00@gmail.com', '$2y$10$NDVC/vLTUQD6zY6nQKkkHuhGp6stBFg4lnCSlTUjkS9iVzt0dprcK', 'Trần Duy Phát', '0866482834', '18 Trần Mai Ninh', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favourite`
--

CREATE TABLE `favourite` (
  `favourite_id` varchar(50) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `favourite`
--

INSERT INTO `favourite` (`favourite_id`, `customer_id`, `product_id`) VALUES
('681a2c36a60ef', '7dcbf099-169c-4327-9c1c-63fc9c155841', '441d396f-f043-47d8-a73a-d0f2617ef2c1'),
('681a34393b608', '7dcbf099-169c-4327-9c1c-63fc9c155841', 'c94a07a1-e450-4189-8322-97ea98d2f1eb');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order`
--

CREATE TABLE `order` (
  `order_id` varchar(50) NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `promotion` varchar(50) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order`
--

INSERT INTO `order` (`order_id`, `customer_id`, `shipping_address`, `discount_amount`, `total_amount`, `promotion`, `status`, `payment_method`, `note`, `created_at`) VALUES
('ORD_681a2d06d5627', '7dcbf099-169c-4327-9c1c-63fc9c155841', '18 Trần Mai Ninh', 0.00, 1736000.00, NULL, 'completed', 'cod', '', '2025-05-06 22:38:46'),
('ORD_681a343d84474', '7dcbf099-169c-4327-9c1c-63fc9c155841', '18 Trần Mai Ninh', 0.00, 850000.00, NULL, 'completed', 'cod', '', '2025-05-06 23:09:33'),
('ORD_6825b061668ec', '7dcbf099-169c-4327-9c1c-63fc9c155841', '18 Trần Mai Ninh', 0.00, 1105000.00, NULL, 'completed', 'cod', '', '2025-05-15 16:14:09'),
('ORD_682ff925633c5', '7dcbf099-169c-4327-9c1c-63fc9c155841', '18 Trần Mai Ninh', 0.00, 1917000.00, NULL, 'completed', 'cod', '', '2025-05-23 11:27:17'),
('ORD_683149e97491a', '7dcbf099-169c-4327-9c1c-63fc9c155841', '18 Trần Mai Ninh', 0.00, 598000.00, NULL, 'completed', 'cod', '', '2025-05-24 11:24:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_detail`
--

CREATE TABLE `order_detail` (
  `order_detail_id` char(36) NOT NULL,
  `order_id` char(36) NOT NULL,
  `variant_id` char(36) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_detail`
--

INSERT INTO `order_detail` (`order_detail_id`, `order_id`, `variant_id`, `quantity`, `unit_price`) VALUES
('OD_681a2d06d5d0a', 'ORD_681a2d06d5627', '1cb51478-ad3e-44d0-b6d7-35c5888bd2d7', 2, 299000.00),
('OD_681a2d06d67a0', 'ORD_681a2d06d5627', '7719ff4a-d244-40ca-9b09-30a4ecda6e09', 1, 639000.00),
('OD_681a2d06d8462', 'ORD_681a2d06d5627', '5f1397bf-cca6-4955-a040-5d65149efdb0', 1, 499000.00),
('OD_681a343d84801', 'ORD_681a343d84474', '03b288d2-6889-46c2-afcd-e971a182803e', 1, 850000.00),
('OD_6825b06167286', 'ORD_6825b061668ec', 'c8aa0245-536e-4375-97a9-3b9691953c05', 1, 299000.00),
('OD_6825b06167e84', 'ORD_6825b061668ec', '05f8da55-3dba-4ac6-a6aa-d9e8050f2ad4', 1, 299000.00),
('OD_6825b0616bcb2', 'ORD_6825b061668ec', '5bdb1e15-36fb-4d59-89e0-ff7692f6130c', 1, 169000.00),
('OD_6825b0616f4e2', 'ORD_6825b061668ec', 'c280b703-7e6c-43ce-bbb7-65d562432416', 1, 169000.00),
('OD_6825b0616f906', 'ORD_6825b061668ec', '7dbe1126-6175-4f6d-913e-80e455050736', 1, 169000.00),
('OD_682ff92563e77', 'ORD_682ff925633c5', 'f93dc8a4-227f-4254-863c-7a15425e34dc', 1, 639000.00),
('OD_682ff9256477f', 'ORD_682ff925633c5', 'c3151f5d-c94e-416e-a243-5430921e3cbd', 1, 639000.00),
('OD_682ff925655ae', 'ORD_682ff925633c5', 'bced997e-bf98-4cf5-ac01-9324fec7542c', 1, 639000.00),
('OD_683149e975627', 'ORD_683149e97491a', 'c8aa0245-536e-4375-97a9-3b9691953c05', 2, 299000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product`
--

CREATE TABLE `product` (
  `product_id` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `brand_id` varchar(50) DEFAULT NULL,
  `category_id` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `status` tinyint(1) DEFAULT 1 COMMENT '0: Coming Soon, 1: On Sale, 2: Out of Stock'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_slug`, `description`, `original_price`, `discount_price`, `brand_id`, `category_id`, `created_at`, `status`) VALUES
('293e9b93-bb4f-42c7-8195-f5788afcd31b', 'Áo thun Relaxed Fit in Bé trai cầm cờ', 'ao_thun_relaxed_fit_in_be_trai_cam_co', 'Chất liệu\r\n100% Cotton\r\nĐịnh lượng vải: 220gsm\r\nKiểu dáng\r\nRelaxed fit\r\nMẫu nam: 1m73 - 75 kí - mặc size L\r\nPhù hợp\r\nMặc hàng ngày\r\n* Proudly Made In Vietnam', 249000.00, 0.00, 'e61798f4-7e60-44fe-a562-7775eae6e6e0', 'e9348463-1ac7-41a0-84bf-b37f444376cd', '2025-05-07 19:42:01', 1),
('2a095ce8-4e13-4629-9afd-3008d174ca0d', 'Quần DirtyCoins Denim Letter Monogram - Black', 'quan_dirtycoins_denim_letter_monogram_-_black', 'Chi tiết sản phẩm:\r\n\r\n• Chất liệu: Cotton Denim.\r\n\r\n• Relaxed Fit.\r\n\r\n• Bề mặt vải được xử lý wash nhẹ.\r\n\r\n• Pattern monogram được dệt trên bề mặt vải.\r\n\r\n• Nhãn da thương hiệu may ở sau lưng quần.\r\n\r\n\r\n\r\nThông số chiều cao cân nặng mẫu Nam 1m75 - 65kg mặc size 30\r\n\r\n\r\n\r\nSize Chart:\r\n\r\n\r\nCHÍNH SÁCH BẢO HÀNH VÀ ĐỔI TRẢ\r\n\r\n● Chính sách Bảo hành đối với từng loại sản phẩm\r\n\r\n- Đối với các sản phẩm áo thun (T-shirt, Polo...), các sản phẩm có hình in : 6 tháng\r\n\r\n- Đối với các sản phẩm áo khoác (Varsity Jacket, Coach Jacket...), các sản phẩm phụ kiện (Backpack, Crossbody Bag, Slides, Sneakers...) : 3 tháng\r\n\r\n- Chính sách bảo hành này áp dụng với các sản phẩm có lỗi từ nhà sản xuất như: sản phẩm bị ra màu, hình in bong tróc, hình in bị loang màu, thiếu hình in, lỗi dây kéo, lỗi dây rút.\r\n\r\n● Chính sách Đổi trả sản phẩm đã mua\r\n\r\n1. Unbox sản phẩm và quay video chi tiết.\r\n\r\n2. Nếu sản phẩm có vấn đề thì liên hệ ngay với chúng mình để được giải quyết đổi trả nha. (Trong vòng 15 ngày)\r\n\r\nLưu ý: Các quy định đối với sản phẩm được đổi:\r\n\r\n- Không có dấu hiệu bị bẩn, có mùi lạ hoặc có dấu hiệu đã qua sử dụng.\r\n\r\n- Có kèm hóa đơn hoặc xác nhận mua hàng.\r\n\r\n- Có đầy đủ tag giấy và nhãn vải đính kèm sản phẩm.\r\n\r\n- Do lí do vệ sinh, các sản phẩm vớ (tất) và đồ lót sẽ không hỗ trợ đổi sản phẩm.', 690000.00, 639000.00, '21a7d2a1-fd19-4935-a719-ee0778554177', 'e34aa81b-5122-4b08-b5c9-c7b815472b50', '2025-05-06 22:29:17', 1),
('441d396f-f043-47d8-a73a-d0f2617ef2c1', 'Áo DirtyCoins Letters Monogram Denim Jersey Shirt - Black', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black', 'Chi tiết sản phẩm:\r\n\r\n• Màu sắc: Đen\r\n\r\n• Size: M - L - XL\r\n\r\n• Chất liệu: Cotton Denim.\r\n\r\n• Relaxed Fit.\r\n\r\n• Bề mặt vải được xử lý wash nhẹ.\r\n\r\n• Pattern monogram được dệt full trên bề mặt vải.\r\n\r\n• Nhãn dệt may ở góc dưới thân áo.\r\n\r\n• Nút áo đúc nổi logo thương hiệu.\r\n\r\n• Patch xù logo Y được thêu trên ngực áo\r\nCHÍNH SÁCH BẢO HÀNH VÀ ĐỔI TRẢ\r\n\r\n● Chính sách Bảo hành đối với từng loại sản phẩm\r\n\r\n- Đối với các sản phẩm áo thun (T-shirt, Polo...), các sản phẩm có hình in : 6 tháng\r\n\r\n- Đối với các sản phẩm áo khoác (Varsity Jacket, Coach Jacket...), các sản phẩm phụ kiện (Backpack, Crossbody Bag, Slides, Sneakers...) : 3 tháng\r\n\r\n- Chính sách bảo hành này áp dụng với các sản phẩm có lỗi từ nhà sản xuất như: sản phẩm bị ra màu, hình in bong tróc, hình in bị loang màu, thiếu hình in, lỗi dây kéo, lỗi dây rút.\r\n\r\n● Chính sách Đổi trả sản phẩm đã mua\r\n\r\n1. Unbox sản phẩm và quay video chi tiết.\r\n\r\n2. Nếu sản phẩm có vấn đề thì liên hệ ngay với chúng mình để được giải quyết đổi trả nha. (Trong vòng 15 ngày)\r\n\r\nLưu ý: Các quy định đối với sản phẩm được đổi:\r\n\r\n- Không có dấu hiệu bị bẩn, có mùi lạ hoặc có dấu hiệu đã qua sử dụng.\r\n\r\n- Có kèm hóa đơn hoặc xác nhận mua hàng.\r\n\r\n- Có đầy đủ tag giấy và nhãn vải đính kèm sản phẩm.\r\n\r\n- Do lí do vệ sinh, các sản phẩm vớ (tất) và đồ lót sẽ không hỗ trợ đổi sản phẩm.', 600000.00, 499000.00, '21a7d2a1-fd19-4935-a719-ee0778554177', '704088f0-3bbb-4d1c-a2fe-d0c25b919b89', '2025-05-06 20:53:49', 1),
('45aad002-1f27-4da3-820d-d59750291c9a', 'Áo Thun DirtyCoins Logo Football Jersey', 'a_o_thun_dirtycoins_logo_football_jersey', 'Chi tiết sản phẩm:\r\n\r\n• Chất liệu: Polyester.\r\n\r\n• Màu sắc: Đen - Đỏ - Trắng\r\n\r\n• Size: M - L - XL\r\n\r\n• Relaxed Fit.\r\n\r\n• Hình in mặt trước và mặt sau áo áp dụng công nghệ in kéo lụa.\r\n\r\n• Nhãn dệt riêng ở góc dưới thân áo.\r\nCHÍNH SÁCH BẢO HÀNH VÀ ĐỔI TRẢ\r\n\r\n● Chính sách Bảo hành đối với từng loại sản phẩm\r\n\r\n- Đối với các sản phẩm áo thun (T-shirt, Polo...), các sản phẩm có hình in : 6 tháng\r\n\r\n- Đối với các sản phẩm áo khoác (Varsity Jacket, Coach Jacket...), các sản phẩm phụ kiện (Backpack, Crossbody Bag, Slides, Sneakers...) : 3 tháng\r\n\r\n- Chính sách bảo hành này áp dụng với các sản phẩm có lỗi từ nhà sản xuất như: sản phẩm bị ra màu, hình in bong tróc, hình in bị loang màu, thiếu hình in, lỗi dây kéo, lỗi dây rút.\r\n\r\n● Chính sách Đổi trả sản phẩm đã mua\r\n\r\n1. Unbox sản phẩm và quay video chi tiết.\r\n\r\n2. Nếu sản phẩm có vấn đề thì liên hệ ngay với chúng mình để được giải quyết đổi trả nha. (Trong vòng 15 ngày)\r\n\r\nLưu ý: Các quy định đối với sản phẩm được đổi:\r\n\r\n- Không có dấu hiệu bị bẩn, có mùi lạ hoặc có dấu hiệu đã qua sử dụng.\r\n\r\n- Có kèm hóa đơn hoặc xác nhận mua hàng.\r\n\r\n- Có đầy đủ tag giấy và nhãn vải đính kèm sản phẩm.\r\n\r\n- Do lí do vệ sinh, các sản phẩm vớ (tất) và đồ lót sẽ không hỗ trợ đổi sản phẩm.', 450000.00, 399000.00, '21a7d2a1-fd19-4935-a719-ee0778554177', 'e9348463-1ac7-41a0-84bf-b37f444376cd', '2025-05-06 21:51:52', 1),
('4bba83af-9545-4c63-9eb6-64a435314981', 'Dép DirtyCoins Embossed Slides', 'de_p_dirtycoins_embossed_slides', 'Chi tiết sản phẩm:\r\n\r\n• Chất liệu: Nhựa EVA\r\n\r\n• Form dép quai ngang (slides)\r\n\r\n• Dép đúc nguyên khối.\r\n\r\n• Hoạ tiết DirtyCoins dập chìm trên quai dép\r\n\r\n• Size từ 1 tới 6 (tương đương size 35 tới 46)\r\n\r\n• Kích thước hộp: 35 x 20 x 15 (cm)', 550000.00, 420000.00, '21a7d2a1-fd19-4935-a719-ee0778554177', '1a153355-0819-42bb-a839-e741000718cb', '2025-05-06 22:24:11', 1),
('6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'Áo thun Relaxed Fit Stitch Give Me Space', 'ao_thun_relaxed_fit_stitch_give_me_space', 'Chất liệu\r\n100% Cotton\r\nĐịnh lượng vải 245gsm\r\nKiểu dáng\r\nRelaxed fit\r\nMẫu nữ: 1m68 - 50 kí - mặc size S\r\nMẫu nam: 1m73 - 75 kí - mặc size XL\r\nPhù hợp\r\nĐi chơi - Mặc hàng ngày', 200000.00, 0.00, 'e61798f4-7e60-44fe-a562-7775eae6e6e0', 'e9348463-1ac7-41a0-84bf-b37f444376cd', '2025-05-07 19:16:06', 1),
('bd326904-f0d3-4488-8580-ac8722239bbe', 'Quần DirtyCoins Logo Mesh Shorts', 'qua_n_dirtycoins_logo_mesh_shorts', 'Chi tiết sản phẩm:\r\n\r\n• Màu: Đỏ - Trắng - Đen\r\n\r\n• Size: M L XL\r\n\r\n•Chất liệu: vải mesh poly.\r\n\r\n• Hình mặt trước quần áp dụng kĩ thuật in kéo lụa.\r\n\r\n• Lưng thun, có dây rút.\r\n\r\n• Có túi hai bên.\r\n\r\nSize Chart:\r\n\r\n\r\n\r\n\r\n● Chính sách Bảo hành đối với từng loại sản phẩm\r\n\r\n- Đối với các sản phẩm áo thun (T-shirt, Polo...), các sản phẩm có hình in : 6 tháng\r\n\r\n- Đối với các sản phẩm áo khoác (Varsity Jacket, Coach Jacket...), các sản phẩm phụ kiện (Backpack, Crossbody Bag, Slides, Sneakers...) : 3 tháng\r\n\r\n- Chính sách bảo hành này áp dụng với các sản phẩm có lỗi từ nhà sản xuất như: sản phẩm bị ra màu, hình in bong tróc, hình in bị loang màu, thiếu hình in, lỗi dây kéo, lỗi dây rút.\r\n\r\n● Chính sách Đổi trả sản phẩm đã mua\r\n\r\n1. Unbox sản phẩm và quay video chi tiết.\r\n\r\n2. Nếu sản phẩm có vấn đề thì liên hệ ngay với chúng mình để được giải quyết đổi trả nha. (Trong vòng 15 ngày)\r\n\r\nLưu ý: Các quy định đối với sản phẩm được đổi:\r\n\r\n- Không có dấu hiệu bị bẩn, có mùi lạ hoặc có dấu hiệu đã qua sử dụng.\r\n\r\n- Có kèm hóa đơn hoặc xác nhận mua hàng.\r\n\r\n- Có đầy đủ tag giấy và nhãn vải đính kèm sản phẩm.\r\n\r\n- Do lí do vệ sinh, các sản phẩm vớ (tất) và đồ lót sẽ không hỗ trợ đổi sản phẩm.', 390000.00, 350000.00, '21a7d2a1-fd19-4935-a719-ee0778554177', 'f68e32f1-bf76-4bdc-936c-e670a3805f0b', '2025-05-06 22:56:13', 1),
('c825969f-1853-4d56-942c-cc0620123aa0', 'Quần dài kaki ECC Pants', 'quan_dai_kaki_ecc_pants', 'Chất liệu\r\n58% cotton 29% Nylon 13% Spandex\r\nKiểu dáng\r\nRegular fit\r\nPhù hợp\r\nMặc hàng ngày\r\nTính năng\r\nThiết kế có 5 túi đựng tiện lợi\r\n* Proudly Made In Vietnam', 599000.00, 529000.00, 'e61798f4-7e60-44fe-a562-7775eae6e6e0', 'e34aa81b-5122-4b08-b5c9-c7b815472b50', '2025-05-07 20:11:17', 1),
('c94a07a1-e450-4189-8322-97ea98d2f1eb', 'Áo Khoác DirtyCoins Frayed Logos Denim Jacket', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket', 'Chi tiết sản phẩm:\r\n\r\n• Chất liệu: Cotton Denim.\r\n\r\n• Relaxed Fit.\r\n\r\n• Bề mặt vải dệt jacquard logo pattern.\r\n\r\n• Vải có hiệu ứng wash bề mặt.\r\n\r\n• Nút kim loại dập logo thương hiệu.\r\n\r\n• Nhãn da thương hiệu may ở lai áo thân sau.\r\n\r\nSize Chart:\r\n\r\n\r\n\r\n\r\n\r\n\r\nCHÍNH SÁCH BẢO HÀNH VÀ ĐỔI TRẢ\r\n\r\n● Chính sách Bảo hành đối với từng loại sản phẩm\r\n\r\n- Đối với các sản phẩm áo thun (T-shirt, Polo...), các sản phẩm có hình in : 6 tháng\r\n\r\n- Đối với các sản phẩm áo khoác (Varsity Jacket, Coach Jacket...), các sản phẩm phụ kiện (Backpack, Crossbody Bag, Slides, Sneakers...) : 3 tháng\r\n\r\n- Chính sách bảo hành này áp dụng với các sản phẩm có lỗi từ nhà sản xuất như: sản phẩm bị ra màu, hình in bong tróc, hình in bị loang màu, thiếu hình in, lỗi dây kéo, lỗi dây rút.\r\n\r\n● Chính sách Đổi trả sản phẩm đã mua\r\n\r\n1. Unbox sản phẩm và quay video chi tiết.\r\n\r\n2. Nếu sản phẩm có vấn đề thì liên hệ ngay với chúng mình để được giải quyết đổi trả nha. (Trong vòng 15 ngày)\r\n\r\nLưu ý: Các quy định đối với sản phẩm được đổi:\r\n\r\n- Không có dấu hiệu bị bẩn, có mùi lạ hoặc có dấu hiệu đã qua sử dụng.\r\n\r\n- Có kèm hóa đơn hoặc xác nhận mua hàng.\r\n\r\n- Có đầy đủ tag giấy và nhãn vải đính kèm sản phẩm.\r\n\r\n- Do lí do vệ sinh, các sản phẩm vớ (tất) và đồ lót sẽ không hỗ trợ đổi sản phẩm.', 890000.00, 850000.00, '21a7d2a1-fd19-4935-a719-ee0778554177', '0f783d1e-d1c7-41c5-ac51-5f312926ad61', '2025-05-06 23:07:53', 1),
('dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'Quần Shorts thể thao 7 inch đa năng', 'quan_shorts_the_thao_7_inch_da_nang', 'Bề mặt vải mềm mịn và nhẹ, kết hợp\r\nthành phần Spandex giúp quần tăng\r\nsự co giãn để bạn thoải mái tập luyện.\r\n\r\nThiết kế phối lưới\r\ngiúp quần tăng khả năng lưu thông\r\nkhông khí từ cơ thể ra ngoài\r\n\r\nCông nghệ ExDry thấm\r\nhút mồ hôi và làm khô hiệu\r\nquả, giúp bạn khô thoáng\r\nnhanh chóng\r\n\r\nVải mesh thoáng khí\r\n90% Polyamide\r\n10% Spandex', 198000.00, 169000.00, 'e61798f4-7e60-44fe-a562-7775eae6e6e0', 'f68e32f1-bf76-4bdc-936c-e670a3805f0b', '2025-05-07 20:06:09', 1),
('e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'Áo Thun DirtyCoins Wavy Logo T-Shirt', 'a_o_thun_dirtycoins_wavy_logo_t-shirt', 'Chi tiết sản phẩm:\r\n\r\n• Chất liệu: cotton.\r\n\r\n• Relaxed Fit.\r\n\r\n• Bo cổ 2 chiều.\r\n\r\n• Hình in mặt trước và sau áo áp dụng công nghệ in kéo lụa thủ công.\r\n\r\n\r\n\r\nSize Chart:\r\n\r\n\r\n\r\n\r\n\r\n\r\nCHÍNH SÁCH BẢO HÀNH VÀ ĐỔI TRẢ\r\n\r\n● Chính sách Bảo hành đối với từng loại sản phẩm\r\n\r\n- Đối với các sản phẩm áo thun (T-shirt, Polo...), các sản phẩm có hình in : 6 tháng\r\n\r\n- Đối với các sản phẩm áo khoác (Varsity Jacket, Coach Jacket...), các sản phẩm phụ kiện (Backpack, Crossbody Bag, Slides, Sneakers...) : 3 tháng\r\n\r\n- Chính sách bảo hành này áp dụng với các sản phẩm có lỗi từ nhà sản xuất như: sản phẩm bị ra màu, hình in bong tróc, hình in bị loang màu, thiếu hình in, lỗi dây kéo, lỗi dây rút.\r\n\r\n● Chính sách Đổi trả sản phẩm đã mua\r\n\r\n1. Unbox sản phẩm và quay video chi tiết.\r\n\r\n2. Nếu sản phẩm có vấn đề thì liên hệ ngay với chúng mình để được giải quyết đổi trả nha. (Trong vòng 15 ngày)\r\n\r\nLưu ý: Các quy định đối với sản phẩm được đổi:\r\n\r\n- Không có dấu hiệu bị bẩn, có mùi lạ hoặc có dấu hiệu đã qua sử dụng.\r\n\r\n- Có kèm hóa đơn hoặc xác nhận mua hàng.\r\n\r\n- Có đầy đủ tag giấy và nhãn vải đính kèm sản phẩm.\r\n\r\n- Do lí do vệ sinh, các sản phẩm vớ (tất) và đồ lót sẽ không hỗ trợ đổi sản phẩm.', 390000.00, 299000.00, '21a7d2a1-fd19-4935-a719-ee0778554177', 'e9348463-1ac7-41a0-84bf-b37f444376cd', '2025-05-06 22:33:11', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `image_id` varchar(36) NOT NULL,
  `product_id` varchar(36) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `is_main` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`, `position`, `status`, `is_main`) VALUES
('058b606b-ed5d-4206-964b-4ceab2907692', '4bba83af-9545-4c63-9eb6-64a435314981', 'de_p_dirtycoins_embossed_slides-1.webp', 1, 1, 1),
('07eb78e0-6255-4839-891b-b32cf9ff40ee', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a_o_thun_dirtycoins_wavy_logo_t-shirt-8.webp', 8, 1, 0),
('0a957ac1-c46d-473d-9fc9-50b573325904', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black-8.webp', 8, 1, 0),
('0c606f65-e8e5-49b6-a151-4cf83841add1', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'quan_shorts_the_thao_7_inch_da_nang-2.webp', 2, 1, 0),
('12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black-1.webp', 1, 1, 1),
('13110ed7-3417-44eb-94da-a94df20bd277', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'ao_thun_relaxed_fit_stitch_give_me_space-4.webp', 4, 1, 0),
('2330d6af-a15c-43f2-9e6b-f4c51bb5a91f', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'quan_shorts_the_thao_7_inch_da_nang-4.webp', 4, 1, 0),
('24eaafe2-db56-497b-a8d4-8f1c6f3a924d', '4bba83af-9545-4c63-9eb6-64a435314981', 'de_p_dirtycoins_embossed_slides-7.webp', 7, 1, 0),
('2b1bfa04-f00c-4428-a55b-8c7774fa8532', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'quan_dirtycoins_denim_letter_monogram_-_black-4.webp', 4, 1, 0),
('3081983f-3db8-433e-bd8a-e319ecf865be', '4bba83af-9545-4c63-9eb6-64a435314981', 'de_p_dirtycoins_embossed_slides-6.webp', 6, 1, 0),
('34015982-0ac1-4dc4-8fa1-88fbe5a2abe9', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'quan_dirtycoins_denim_letter_monogram_-_black-2.webp', 2, 1, 0),
('35f0cc9e-7e17-4ef9-98e0-4aa800c0f1fd', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'quan_dirtycoins_denim_letter_monogram_-_black-6.webp', 6, 1, 0),
('37eb4db0-4aab-4bb9-887d-03d4209cd2fb', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a_o_thun_dirtycoins_wavy_logo_t-shirt-6.webp', 6, 1, 0),
('3a64692e-2cea-4db4-b7ab-12251d409a9e', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket-8.webp', 8, 1, 0),
('3c7d5c7a-f430-47bf-ad86-42089ce78197', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-4.jpg', 4, 1, 0),
('3d8c6002-b794-4477-b3bc-6f1ebf55b746', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket-1.webp', 1, 1, 0),
('3e155b52-5020-4d31-a9bb-9511b89bfdde', '4bba83af-9545-4c63-9eb6-64a435314981', 'de_p_dirtycoins_embossed_slides-3.webp', 3, 1, 0),
('402f81cb-9378-4c60-a010-a6574f4e4af7', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'quan_shorts_the_thao_7_inch_da_nang-5.webp', 5, 1, 0),
('436c1ba2-c74f-4215-8dc6-75f016789b0a', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-5.jpg', 5, 1, 0),
('43eb075c-2683-4a0a-b972-99dd4ba23551', '4bba83af-9545-4c63-9eb6-64a435314981', 'de_p_dirtycoins_embossed_slides-5.webp', 5, 1, 0),
('53cb71a2-8fb6-4af3-9ac1-817704a7bfcb', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'ao_thun_relaxed_fit_stitch_give_me_space-5.webp', 5, 1, 0),
('55bfd433-bc0f-4939-8f2d-96347b1e9acb', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'quan_shorts_the_thao_7_inch_da_nang-6.webp', 6, 1, 0),
('5611a2d6-14fd-473c-b21b-37cb125d25fb', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-3.webp', 3, 1, 0),
('58a51cdd-43b7-462a-bb0a-32e264efc712', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-4.webp', 4, 1, 0),
('58e1d5e9-ff1f-4666-85b7-aae5382d57b0', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'ao_thun_relaxed_fit_in_be_trai_cam_co-3.webp', 3, 1, 0),
('5a9e55de-064d-4e1a-8a50-77781f37d9b5', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'quan_dirtycoins_denim_letter_monogram_-_black-1.webp', 1, 1, 1),
('5b6be2a9-a1ca-43eb-a537-82d73bb819e2', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'quan_shorts_the_thao_7_inch_da_nang-7.webp', 7, 1, 0),
('5b6fbed0-6f43-4515-9bd1-ff38f820a924', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'qua_n_dirtycoins_logo_mesh_shorts-1.webp', 1, 1, 1),
('5c644ca9-926e-4627-aef4-16bc69350960', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'qua_n_dirtycoins_logo_mesh_shorts-8.webp', 8, 1, 0),
('5e58878d-a93a-4fc6-87d8-e535c539dc08', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'quan_shorts_the_thao_7_inch_da_nang-8.webp', 8, 1, 0),
('60e12120-1103-40d5-9331-122763f0d115', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket-4.webp', 4, 1, 0),
('621123f3-4455-4e9e-8068-30db53000cde', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'ao_thun_relaxed_fit_in_be_trai_cam_co-6.webp', 6, 1, 0),
('6605744a-9f56-4c41-a0a7-8e48ccb76df1', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-7.webp', 7, 1, 0),
('6691540b-31a0-4788-a4f2-b7b5f1268c2c', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'qua_n_dirtycoins_logo_mesh_shorts-7.webp', 7, 1, 0),
('675605c5-db93-498e-96e1-def2ca9fde78', '4bba83af-9545-4c63-9eb6-64a435314981', 'de_p_dirtycoins_embossed_slides-4.webp', 4, 1, 0),
('6df1e448-ef0d-43e9-a447-a33d7cb16697', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'ao_thun_relaxed_fit_stitch_give_me_space-1.webp', 1, 1, 1),
('6e86f0e7-7439-4a3a-9496-cab736752c7d', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket-6.webp', 6, 1, 0),
('6ff29a84-c5ff-4437-adb5-a2c1314b9db7', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-8.jpg', 8, 1, 0),
('7cbbef91-2132-4a13-81d6-80a5776d56f5', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'quan_shorts_the_thao_7_inch_da_nang-3.webp', 3, 1, 0),
('7e6c405a-c937-41b2-9ad1-6485c1924c60', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-9.webp', 9, 1, 0),
('7f27ff39-36ae-46d1-8a68-193767393a4d', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black-6.webp', 6, 1, 0),
('7f62e76a-e61c-4e82-b985-4fe8c7553527', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-2.webp', 2, 1, 0),
('83b57351-f9ac-4b54-891e-1aedc12d0c49', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a_o_thun_dirtycoins_wavy_logo_t-shirt-2.webp', 2, 1, 0),
('83f3bbbd-3033-40dd-acea-c72956f4d28a', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a_o_thun_dirtycoins_wavy_logo_t-shirt-1.webp', 1, 1, 1),
('89c4bb58-ad49-494b-8314-0bf73139bbc0', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-5.webp', 5, 1, 0),
('8a01fa4d-39ee-441a-b8f6-cc478aef55ee', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black-4.webp', 4, 1, 0),
('8cebb240-fcb4-48f9-8c67-79c99441e384', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-3.jpg', 3, 1, 0),
('8cf28b96-3bb6-4a7a-8547-531941e4ef65', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'qua_n_dirtycoins_logo_mesh_shorts-4.webp', 4, 1, 0),
('8e1a74e8-d8c0-474e-acdf-0c7b1944d79c', '4bba83af-9545-4c63-9eb6-64a435314981', 'de_p_dirtycoins_embossed_slides-8.webp', 8, 1, 0),
('9017967a-b56e-4708-af04-7557c2726b9a', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'quan_dirtycoins_denim_letter_monogram_-_black-3.webp', 3, 1, 0),
('909c1a22-9594-4392-99db-38f7674060b5', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-1.webp', 1, 1, 1),
('950a3eb7-d757-49e5-9ad1-9e9f4187d136', '4bba83af-9545-4c63-9eb6-64a435314981', 'de_p_dirtycoins_embossed_slides-2.webp', 2, 1, 0),
('9bec2570-1847-48c7-9cff-5d08f7caf295', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black-7.webp', 7, 1, 0),
('a4286cb8-5b1b-4f94-bf00-dcf790458738', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-6.jpg', 6, 1, 0),
('aafe2f77-96f4-49e4-8cca-e63aba022f48', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'qua_n_dirtycoins_logo_mesh_shorts-3.webp', 3, 1, 0),
('adf5c237-f02c-4e75-9950-f08aea8d471b', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'ao_thun_relaxed_fit_stitch_give_me_space-3.webp', 3, 1, 0),
('af7a6188-4a7f-4459-812b-2771fbe19871', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-9.jpg', 9, 1, 0),
('b3d7406d-6b27-4ed6-8e01-ff44af77200c', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'ao_thun_relaxed_fit_stitch_give_me_space-2.webp', 2, 1, 0),
('b4034b70-cd44-4360-af4f-f9c4596a6fe4', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a_o_thun_dirtycoins_wavy_logo_t-shirt-5.webp', 5, 1, 0),
('b5fba9f0-fde2-4a00-b2a7-01f01949f87f', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'ao_thun_relaxed_fit_in_be_trai_cam_co-5.webp', 5, 1, 0),
('bb3a1757-e61d-4730-ac72-317361e2cbf3', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'quan_shorts_the_thao_7_inch_da_nang-1.webp', 1, 1, 1),
('c0282f37-af38-45c8-adff-521bd0034ffa', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'quan_dirtycoins_denim_letter_monogram_-_black-5.webp', 5, 1, 0),
('c38f2e7a-80fd-4f14-af2b-7b97bebcd2e6', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-7.jpg', 7, 1, 0),
('c8357ce1-98ae-440e-8e9b-cc1eef7ee8f8', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'quan_dirtycoins_denim_letter_monogram_-_black-7.webp', 7, 1, 0),
('ca944918-269f-489f-b926-5749468e71a0', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-6.webp', 6, 1, 0),
('d306c3fe-5658-456e-a649-aea8923d87a1', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-8.webp', 8, 1, 0),
('d35c0ab0-e91f-4196-8b48-0eb3d6fa7878', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black-2.webp', 2, 1, 0),
('d6c93ee2-3c5e-4073-b463-fe5dad75512b', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket-2.webp', 2, 1, 0),
('e1333949-eb86-4d75-90e7-6bdc3b44d4f7', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black-5.webp', 5, 1, 0),
('e1a18b5c-a69c-417a-b75a-35a5405e0102', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'a_o_dirtycoins_letters_monogram_denim_jersey_shirt_-_black-3.webp', 3, 1, 0),
('eb50ad54-74dd-4384-99d0-181bc8465279', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'qua_n_dirtycoins_logo_mesh_shorts-2.webp', 2, 1, 0),
('ebcc852d-49ac-4352-9c7e-efcaf131c411', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket-5.webp', 5, 1, 0),
('ebeb1b5d-99f1-435c-85fc-a1ae13c08cb7', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'qua_n_dirtycoins_logo_mesh_shorts-6.webp', 6, 1, 0),
('edb93d07-ca3c-45c2-8ea1-51be25092a51', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a_o_thun_dirtycoins_wavy_logo_t-shirt-4.webp', 4, 1, 0),
('f054af06-bb7f-48ab-97de-918dbfa64e0f', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a_o_thun_dirtycoins_wavy_logo_t-shirt-7.webp', 7, 1, 0),
('f08fb23a-6722-4e35-aee5-00210e477e8c', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a_o_thun_dirtycoins_wavy_logo_t-shirt-3.webp', 3, 1, 0),
('f10e00d9-6cd6-40e4-ac1f-f75c72a48531', 'c825969f-1853-4d56-942c-cc0620123aa0', 'quan_dai_kaki_ecc_pants-2.jpg', 2, 1, 0),
('f117febc-1cbe-4f16-999d-fef2a68a3778', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'ao_thun_relaxed_fit_in_be_trai_cam_co-2.webp', 2, 1, 0),
('f6178e2c-9555-4adf-ab3d-a3887bfd911a', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket-7.webp', 7, 1, 0),
('fa26d7fa-d425-4de7-8b1f-54caed4d6dda', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'qua_n_dirtycoins_logo_mesh_shorts-5.webp', 5, 1, 0),
('fb3e1d61-c6cf-4cca-9cf1-ceba720cd41c', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'ao_thun_relaxed_fit_in_be_trai_cam_co-1.webp', 1, 1, 1),
('fbc7f24b-5a6b-49fe-8604-6f60dbf41fa8', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'ao_thun_relaxed_fit_in_be_trai_cam_co-4.webp', 4, 1, 0),
('fc3ec32e-d46c-4aaf-85a3-53231b5078fc', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'ao_thun_relaxed_fit_in_be_trai_cam_co-7.webp', 7, 1, 0),
('fe115c31-7b75-478b-a585-65781dcf0010', '45aad002-1f27-4da3-820d-d59750291c9a', 'a_o_thun_dirtycoins_logo_football_jersey-1.webp', 1, 1, 1),
('fe16be28-78e7-490f-bfdd-03c956254c8c', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'ao_khoac_dirtycoins_frayed_logos_denim_jacket-3.webp', 3, 1, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_variants`
--

CREATE TABLE `product_variants` (
  `variant_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `color_id` varchar(50) NOT NULL,
  `size_id` varchar(50) DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_variants`
--

INSERT INTO `product_variants` (`variant_id`, `product_id`, `color_id`, `size_id`, `quantity`, `status`) VALUES
('03aaf96e-d77b-46a2-93c2-7871516bbd05', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('03b288d2-6889-46c2-afcd-e971a182803e', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', 'd3343c39-7324-4a29-bd07-6130e733c10c', 99, 1),
('05141a25-1fce-4fbf-85cf-aca2b8ba8bc1', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('05f8da55-3dba-4ac6-a6aa-d9e8050f2ad4', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 99, 1),
('07dc5612-0138-45e6-ba0e-d27f29f1dbea', '4bba83af-9545-4c63-9eb6-64a435314981', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', '82c2cef4-7c45-4a5b-9014-ca9a00c733f5', 50, 1),
('0b71c32a-17e6-48fc-9636-8310f7aa1bc7', 'c825969f-1853-4d56-942c-cc0620123aa0', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('0fe4640b-4146-4db9-a1f9-659ce0cb2f81', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('105e8773-44b8-4a49-b39f-aeca79763cfd', '4bba83af-9545-4c63-9eb6-64a435314981', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', 'c0d81e31-3fca-46ff-a96f-0ea47fb0030d', 50, 1),
('13937d72-61ab-4a3d-a4ea-6d4ce4a0e3e4', '45aad002-1f27-4da3-820d-d59750291c9a', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('155c1392-ee55-407c-90d5-0e49ad5da501', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('171c8281-6a7f-44fe-bb52-fa91bce45213', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'b5d6f7a8-5678-4c2c-ccdd-44ff55aa6677', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('18ab48cc-e71d-4417-bac9-a4cb46ff4074', '45aad002-1f27-4da3-820d-d59750291c9a', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('1cb51478-ad3e-44d0-b6d7-35c5888bd2d7', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 98, 1),
('1fd54cae-40db-4a5c-b476-6787203c6b95', '45aad002-1f27-4da3-820d-d59750291c9a', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('22aa04df-2c91-4f14-b6b1-73c528af959c', 'c825969f-1853-4d56-942c-cc0620123aa0', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '8ef5dfaa-cde0-4b42-a142-31b4a1641b72', 100, 1),
('2499a870-ca89-4d76-bc37-7b1e68e0b11c', '4bba83af-9545-4c63-9eb6-64a435314981', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', 'c5595a2a-cc7e-48b2-a442-4cacc1ae5f47', 50, 1),
('25e05b89-0155-4bc1-8d49-e0d94d29a155', '4bba83af-9545-4c63-9eb6-64a435314981', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'ce247978-2fe9-4fd5-a364-f2cd3575d1bb', 50, 1),
('274c0ea5-7072-473e-9ae6-bb3691b440f8', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('29fee93f-1998-45d2-bb23-eb6b4df525e0', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('2a059bd8-b683-41a7-8b2a-4aa9c6be6185', '45aad002-1f27-4da3-820d-d59750291c9a', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('2bc2b1cd-5111-49c1-a063-d7c2ff5de45a', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('2c098e40-65e0-4591-877b-e31769e72843', '45aad002-1f27-4da3-820d-d59750291c9a', 'b5d6f7a8-5678-4c2c-ccdd-44ff55aa6677', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('2c393ff4-5939-4baa-93e6-f9659fc086f4', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('2d2df383-5a0d-469a-b7d0-82885ddbd483', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd0a047f6-ff7c-4c6a-93e3-4919582e7743', 100, 1),
('2e88a277-3e97-4d90-b2a6-9c134356a7cd', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('34f33f56-e4cc-4432-add3-2f3dc162a678', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('36084650-218d-4ec3-8dcf-289c5ed57af1', '45aad002-1f27-4da3-820d-d59750291c9a', 'c9e8f0b2-7890-4d3d-ddee-66aa77bb8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('382ec646-42a0-43f8-a80c-d1aa397ab8ce', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('3c4125f2-dca8-4771-b339-0f0ffea997ce', '45aad002-1f27-4da3-820d-d59750291c9a', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('3cabd67c-5900-48dc-804b-8fb0a0629046', '4bba83af-9545-4c63-9eb6-64a435314981', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'c0d81e31-3fca-46ff-a96f-0ea47fb0030d', 50, 1),
('3de165b9-d5df-428e-8562-cd7b7705e377', '4bba83af-9545-4c63-9eb6-64a435314981', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', '72dcf819-79a1-4e11-a321-64efaccbb26a', 50, 1),
('40cbe212-1911-4c4d-b52d-51668d2aa28c', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('40e88157-26ee-4d9e-9e0e-c5b9ace78683', '293e9b93-bb4f-42c7-8195-f5788afcd31b', '4e22ee56-264c-46c5-bf4c-4c836ce32625', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('43b9a05f-468e-4ac6-a945-d28998506b34', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '8ef5dfaa-cde0-4b42-a142-31b4a1641b72', 100, 1),
('48605963-dae2-4688-a629-2ac8b8fce950', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('4a1efb60-32f0-4c9e-ad3a-beb0b15385d8', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('510e7fdf-5408-45cb-a43a-78eb5ced373f', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'b5d6f7a8-5678-4c2c-ccdd-44ff55aa6677', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('547b0a34-c3eb-41e6-8c72-3958453c747d', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('563ba479-a902-487a-9013-48f23b3e0d5f', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('568623f1-ad95-4842-893d-98865f328707', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('58092c6b-7334-4a2a-9e07-50d3a918f28b', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('58b4594a-134c-4e9e-bc07-cda60a9a581d', '293e9b93-bb4f-42c7-8195-f5788afcd31b', '4e22ee56-264c-46c5-bf4c-4c836ce32625', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('59c97bee-549e-4dd8-93fc-f0a24ecf5dab', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('5ac49ad1-33f1-4a81-860e-25e9b0b06bb9', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('5bdb1e15-36fb-4d59-89e0-ff7692f6130c', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 99, 1),
('5f1397bf-cca6-4955-a040-5d65149efdb0', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 99, 1),
('6386cb26-9328-49b5-bb75-62d70082a35d', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', '4e22ee56-264c-46c5-bf4c-4c836ce32625', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('65cbaa63-fb31-4532-9cee-baf5ca2830ae', '45aad002-1f27-4da3-820d-d59750291c9a', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('6662e3a7-2cd4-4242-9398-67170b76524c', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('6825b7b6-99fb-4f5a-817a-78b58f129fbc', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('6923fdea-e98c-4474-8fd4-7a6efa7ff242', '45aad002-1f27-4da3-820d-d59750291c9a', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('6ff6d368-6987-4f67-a7fa-83c9729ca9f2', '4bba83af-9545-4c63-9eb6-64a435314981', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '72dcf819-79a1-4e11-a321-64efaccbb26a', 50, 1),
('71504115-fbdf-48e9-9961-e5c98ce01505', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', '4e22ee56-264c-46c5-bf4c-4c836ce32625', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('73789f94-2827-41d8-97ac-b3e524163ac8', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('74fdb92b-9e50-4e57-a518-4af4a3cac51b', 'c825969f-1853-4d56-942c-cc0620123aa0', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('75eeb2ed-2e3c-4c7e-b15f-0f8466e3b20b', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('76854e52-0e85-456c-847b-3b763d7ae152', '45aad002-1f27-4da3-820d-d59750291c9a', 'c9e8f0b2-7890-4d3d-ddee-66aa77bb8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('76efdf3e-e137-450e-b870-14f59276eba9', '4bba83af-9545-4c63-9eb6-64a435314981', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '82c2cef4-7c45-4a5b-9014-ca9a00c733f5', 0, 1),
('7719ff4a-d244-40ca-9b09-30a4ecda6e09', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '0ab40d8f-4fdd-4a1a-a2f5-98ed6f1e7b31', 99, 1),
('779bdeae-73e7-4eb7-918d-2d77a6db5dae', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '8ef5dfaa-cde0-4b42-a142-31b4a1641b72', 100, 1),
('7950e2c6-a65b-456c-b3f2-c98768d61f7b', 'bd326904-f0d3-4488-8580-ac8722239bbe', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('7dbe1126-6175-4f6d-913e-80e455050736', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 99, 1),
('7e93e15e-2719-4141-b7e0-9a389cd61267', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('7f45a4e7-694b-4b98-ac4e-8b823ddb1f5d', '4bba83af-9545-4c63-9eb6-64a435314981', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'c5595a2a-cc7e-48b2-a442-4cacc1ae5f47', 50, 1),
('8276bc61-77e5-4031-b641-71f8682f474e', '45aad002-1f27-4da3-820d-d59750291c9a', 'b5d6f7a8-5678-4c2c-ccdd-44ff55aa6677', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('83746161-a6fe-487b-aad2-ebe89562cf82', 'bd326904-f0d3-4488-8580-ac8722239bbe', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('8506a328-ede9-4c78-8c37-fe5aa022e1d5', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', '8ef5dfaa-cde0-4b42-a142-31b4a1641b72', 100, 1),
('86237da6-b9fc-403e-8962-2410d33a5713', '45aad002-1f27-4da3-820d-d59750291c9a', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('89f23d1e-be2a-4064-b883-95aff11cf43a', '4bba83af-9545-4c63-9eb6-64a435314981', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', 'ce247978-2fe9-4fd5-a364-f2cd3575d1bb', 50, 1),
('8a216078-af96-44dc-bbbd-0d53778581a3', '4bba83af-9545-4c63-9eb6-64a435314981', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'c0d81e31-3fca-46ff-a96f-0ea47fb0030d', 50, 1),
('8bbf81ad-6230-46c4-872e-cd50a9e89ebd', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('92c6f47f-9334-4119-adee-a7b9596b9ad3', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('93303b80-0980-44fe-8902-deffb9333e99', 'c825969f-1853-4d56-942c-cc0620123aa0', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', '8ef5dfaa-cde0-4b42-a142-31b4a1641b72', 100, 1),
('940db793-2dfc-4e40-bd25-fd85ebe50c0a', '45aad002-1f27-4da3-820d-d59750291c9a', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('9512c6b1-9ade-4778-83f5-6f362d369c75', '293e9b93-bb4f-42c7-8195-f5788afcd31b', '4e22ee56-264c-46c5-bf4c-4c836ce32625', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('98d50cf4-e538-4cf2-88df-519e0f74cdc5', '4bba83af-9545-4c63-9eb6-64a435314981', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', '82c2cef4-7c45-4a5b-9014-ca9a00c733f5', 50, 1),
('9967d981-ffc4-497a-98aa-16e44516aa7e', 'c825969f-1853-4d56-942c-cc0620123aa0', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('9dc7de40-41fd-4e77-bb4d-d1188c99dee0', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('a098ecd9-2f70-4f09-905e-b1ea32712f48', 'bd326904-f0d3-4488-8580-ac8722239bbe', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('a17d5389-f846-4429-bf63-b1a9977344dd', '4bba83af-9545-4c63-9eb6-64a435314981', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', 'c5595a2a-cc7e-48b2-a442-4cacc1ae5f47', 50, 1),
('a3642edd-73db-4c53-8f6a-61abcea7521d', '4bba83af-9545-4c63-9eb6-64a435314981', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '58a82d91-5324-4d60-beac-12a71d241706', 50, 1),
('a5a1c45f-3b60-4e2a-8534-034554ed379e', 'c825969f-1853-4d56-942c-cc0620123aa0', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('a72d27e6-b727-4704-9891-4677d665ca19', '4bba83af-9545-4c63-9eb6-64a435314981', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', 'c0d81e31-3fca-46ff-a96f-0ea47fb0030d', 50, 1),
('ab281c4d-826c-427c-9875-df5ff2d7e383', 'bd326904-f0d3-4488-8580-ac8722239bbe', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('b36dc190-0231-4a13-91aa-d96a6e68ed0c', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('b47c9b86-19dd-444e-90fa-4a1b7746f655', '4bba83af-9545-4c63-9eb6-64a435314981', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', '58a82d91-5324-4d60-beac-12a71d241706', 50, 1),
('b51225f6-5a30-49e6-b2f8-82bbd8c2be76', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('b68d187e-bd97-42dc-9235-b010938b7464', '441d396f-f043-47d8-a73a-d0f2617ef2c1', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('bad40386-0692-465e-9d65-65e227dce942', '4bba83af-9545-4c63-9eb6-64a435314981', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '82c2cef4-7c45-4a5b-9014-ca9a00c733f5', 50, 1),
('bba059a3-a2a3-47ff-905b-515fb650e733', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('bced997e-bf98-4cf5-ac01-9324fec7542c', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'c61d6411-cdf9-4c53-bf14-74ca10d8e2f7', 99, 1),
('bd5d7098-7b05-4738-98e7-8b82bf4a1f6b', '293e9b93-bb4f-42c7-8195-f5788afcd31b', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', '41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 100, 1),
('bdf5fc70-3ba8-49d9-a4d6-ecd1852ec1fe', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', '4e22ee56-264c-46c5-bf4c-4c836ce32625', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('beb317af-bb65-40e7-a3be-d7a448837091', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('c280b703-7e6c-43ce-bbb7-65d562432416', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 99, 1),
('c3151f5d-c94e-416e-a243-5430921e3cbd', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'b19ea8db-a98d-4ad5-a131-911d5057e91d', 99, 1),
('c3fcfbe7-7603-44cc-8f1b-c622b9f6ac1f', '45aad002-1f27-4da3-820d-d59750291c9a', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('c6a75ef2-b9fa-495f-9af2-08d4d1e394f4', '4bba83af-9545-4c63-9eb6-64a435314981', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', 'ce247978-2fe9-4fd5-a364-f2cd3575d1bb', 50, 1),
('c77874f9-eb94-4466-81b7-1adf1d88e5e9', 'c825969f-1853-4d56-942c-cc0620123aa0', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('c80d55b0-398a-4f89-b44f-ab8e82d94ab2', '4bba83af-9545-4c63-9eb6-64a435314981', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', '58a82d91-5324-4d60-beac-12a71d241706', 50, 1),
('c8aa0245-536e-4375-97a9-3b9691953c05', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 97, 1),
('c8ab38ee-f6ee-4660-abf5-499e89ffe18e', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('c93d6dd7-f85f-4e60-994f-0961962b35cb', '293e9b93-bb4f-42c7-8195-f5788afcd31b', '4e22ee56-264c-46c5-bf4c-4c836ce32625', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('cdbb9126-7037-4694-8602-ea9fb86de574', '45aad002-1f27-4da3-820d-d59750291c9a', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('d3cfb797-3991-45d1-afab-05407a42c5e7', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'b5d6f7a8-5678-4c2c-ccdd-44ff55aa6677', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('d5f3fd1b-a0e8-4b93-b643-a0dfbc307548', 'e1b685dc-9b22-49b6-8d1c-c761c6cc3301', 'b5d6f7a8-5678-4c2c-ccdd-44ff55aa6677', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('da398db1-5c2c-41d0-be64-c82b25f8b003', 'c825969f-1853-4d56-942c-cc0620123aa0', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('dae60e8a-6114-40f8-a54d-39984835b01a', '45aad002-1f27-4da3-820d-d59750291c9a', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('db3bb415-b64d-4485-a99f-a238f5d6f9ef', '45aad002-1f27-4da3-820d-d59750291c9a', 'c9e8f0b2-7890-4d3d-ddee-66aa77bb8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('df2630e5-8f65-4429-aabc-093c5d11c974', 'dfc3f4dd-8ae2-4a08-82d6-e977473596dd', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('dfc78090-faae-49ee-871c-4b8dd6e7cab5', 'c825969f-1853-4d56-942c-cc0620123aa0', 'd4e5f6a7-8901-4404-eeff-00bb11cc2233', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('e2198502-3b86-4c81-88f3-aaff1d15e5ae', '45aad002-1f27-4da3-820d-d59750291c9a', 'b5d6f7a8-5678-4c2c-ccdd-44ff55aa6677', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('e4139fa7-7583-40e9-ae49-368147ec90b9', '4bba83af-9545-4c63-9eb6-64a435314981', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'c5595a2a-cc7e-48b2-a442-4cacc1ae5f47', 50, 1),
('e8620780-b56f-4052-9ac9-e713aab14802', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'a1c3e2f4-1234-4a1a-bbcd-22ee33ff4455', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('edc00f55-f9dd-45f1-9e12-8990b87771f9', 'c94a07a1-e450-4189-8322-97ea98d2f1eb', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 100, 1),
('ee2aefec-3f61-4bb8-b641-b39111bbb772', '6bf2f33f-e7e9-4727-b9e8-d8620097c61d', '4e22ee56-264c-46c5-bf4c-4c836ce32625', 'd3343c39-7324-4a29-bd07-6130e733c10c', 100, 1),
('f457e5cf-da09-418d-80c1-d0a97f6126c5', '4bba83af-9545-4c63-9eb6-64a435314981', '12bc2b68-31e5-4510-9bd7-f5f253eb5c2e', '72dcf819-79a1-4e11-a321-64efaccbb26a', 50, 1),
('f93dc8a4-227f-4254-863c-7a15425e34dc', '2a095ce8-4e13-4629-9afd-3008d174ca0d', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '2834e0e6-1bd9-489f-a502-1f6c92f95770', 99, 1),
('fb021bc9-e72a-49d2-b256-ecc11a072b1f', '4bba83af-9545-4c63-9eb6-64a435314981', 'c0d1e2f3-7890-4393-ddee-88aa99bb0011', 'ce247978-2fe9-4fd5-a364-f2cd3575d1bb', 50, 1),
('fb115f21-4692-4bdf-a6f4-a28cfd7966ab', '4bba83af-9545-4c63-9eb6-64a435314981', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '58a82d91-5324-4d60-beac-12a71d241706', 50, 1),
('fecddeee-f038-43b0-93ad-afd15e67196a', 'c825969f-1853-4d56-942c-cc0620123aa0', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', 'e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 100, 1),
('fffc3701-3182-4b7a-9479-585644dc6fa3', '4bba83af-9545-4c63-9eb6-64a435314981', 'b6c7d8e9-6789-4282-ccdd-66ff77aa8899', '72dcf819-79a1-4e11-a321-64efaccbb26a', 50, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `promotion`
--

CREATE TABLE `promotion` (
  `promotion_id` varchar(50) NOT NULL,
  `promotion_name` varchar(255) NOT NULL,
  `promotion_code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `promotion`
--

INSERT INTO `promotion` (`promotion_id`, `promotion_name`, `promotion_code`, `description`, `discount_value`, `start_date`, `end_date`) VALUES
('a1b2c3d4-e5f6-4a7b-8c9d-1234567890ab', 'Summer Sale', 'SUMMER2025', 'Giảm 20% cho toàn bộ sản phẩm mùa hè', 20.00, '2025-06-01 00:00:00', '2025-06-30 00:00:00'),
('b2c3d4e5-f6a7-4b8c-9d0e-2345678901bc', 'Black Friday', 'BLACKFRI2025', 'Chương trình giảm 30% vào Black Friday', 30.00, '2025-11-25 00:00:00', '2025-11-25 00:00:00'),
('c3d4e5f6-a7b8-4c9d-0e1f-3456789012cd', 'Christmas Special', 'XMAS2025', 'Ưu đãi giảm 25% cho dịp Giáng Sinh', 25.00, '2025-12-20 00:00:00', '2025-12-31 00:00:00'),
('d4e5f6a7-b8c9-4d0e-1f2a-4567890123de', 'New Year Offer', 'NEWYEAR2026', 'Giảm 15% chào năm mới cho khách hàng thân thiết', 15.00, '2026-01-01 00:00:00', '2026-01-10 00:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role`
--

CREATE TABLE `role` (
  `role_id` varchar(50) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `role`
--

INSERT INTO `role` (`role_id`, `role_name`, `status`) VALUES
('56235a7e-b075-42f9-96fd-62a1ee126e56', 'ADMIN VIP PRO', 1),
('7877fe01-c0f9-4cf9-a23a-8cc87446957c', 'Chủ', 1),
('7b8b2b98-a764-47cc-9ffe-e88d186fea04', 'ADMIN', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sizes`
--

CREATE TABLE `sizes` (
  `size_id` varchar(50) NOT NULL,
  `size_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sizes`
--

INSERT INTO `sizes` (`size_id`, `size_name`) VALUES
('0ab40d8f-4fdd-4a1a-a2f5-98ed6f1e7b31', '37'),
('2834e0e6-1bd9-489f-a502-1f6c92f95770', '38'),
('41f5fc0e-14f3-4b5c-9b36-7c4db2b08c77', 'S'),
('58a82d91-5324-4d60-beac-12a71d241706', '1'),
('72dcf819-79a1-4e11-a321-64efaccbb26a', '6'),
('82c2cef4-7c45-4a5b-9014-ca9a00c733f5', '2'),
('8ef5dfaa-cde0-4b42-a142-31b4a1641b72', 'XXL'),
('96da33ec-1ef3-4d53-a65a-b5fd29268ce0', 'L'),
('b19ea8db-a98d-4ad5-a131-911d5057e91d', '39'),
('c0d81e31-3fca-46ff-a96f-0ea47fb0030d', '4'),
('c5595a2a-cc7e-48b2-a442-4cacc1ae5f47', '5'),
('c61d6411-cdf9-4c53-bf14-74ca10d8e2f7', '40'),
('ce247978-2fe9-4fd5-a364-f2cd3575d1bb', '3'),
('d0a047f6-ff7c-4c6a-93e3-4919582e7743', '36'),
('d3343c39-7324-4a29-bd07-6130e733c10c', 'XL'),
('e4390c74-18b9-4b5b-b511-6e4fb9e7a92f', 'M');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Chỉ mục cho bảng `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`banner_id`);

--
-- Chỉ mục cho bảng `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`),
  ADD UNIQUE KEY `brand_slug` (`brand_slug`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Chỉ mục cho bảng `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Chỉ mục cho bảng `color`
--
ALTER TABLE `color`
  ADD PRIMARY KEY (`color_id`);

--
-- Chỉ mục cho bảng `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `favourite`
--
ALTER TABLE `favourite`
  ADD PRIMARY KEY (`favourite_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_order_customer` (`customer_id`),
  ADD KEY `fk_order_promotion` (`promotion`);

--
-- Chỉ mục cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `variant_id` (`variant_id`);

--
-- Chỉ mục cho bảng `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `fk_variants_product` (`product_id`),
  ADD KEY `fk_variants_color` (`color_id`),
  ADD KEY `fk_variants_size` (`size_id`);

--
-- Chỉ mục cho bảng `promotion`
--
ALTER TABLE `promotion`
  ADD PRIMARY KEY (`promotion_id`),
  ADD UNIQUE KEY `promotion_code` (`promotion_code`);

--
-- Chỉ mục cho bảng `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Chỉ mục cho bảng `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `favourite`
--
ALTER TABLE `favourite`
  ADD CONSTRAINT `favourite_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favourite_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_detail_ibfk_2` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`variant_id`);

--
-- Các ràng buộc cho bảng `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Các ràng buộc cho bảng `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `fk_variants_color` FOREIGN KEY (`color_id`) REFERENCES `color` (`color_id`),
  ADD CONSTRAINT `fk_variants_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `fk_variants_size` FOREIGN KEY (`size_id`) REFERENCES `sizes` (`size_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
