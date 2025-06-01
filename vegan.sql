-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 01, 2025 at 03:08 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vegan`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image`, `created_at`) VALUES
(1, 'Cơm Chay', 'images/comchay.jpg', '2025-05-29 16:18:29'),
(2, 'Bún Chay', 'images/bunchay.jpg', '2025-05-29 16:18:29'),
(3, 'Lẩu Chay', 'images/lauchay.jpg', '2025-05-29 16:18:29'),
(4, 'Món Mặn Chay', 'images/monmanchay.jpg', '2025-05-29 16:18:29'),
(11, 'rau', 'https://cafefcdn.com/203337114487263232/2025/5/22/hinh-13-1477885609-17478918795291411602985-1747904942403-1747904944112789608768.jpg', '2025-06-01 03:02:32');

-- --------------------------------------------------------

--
-- Table structure for table `option_groups`
--

CREATE TABLE `option_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('single','multiple') NOT NULL DEFAULT 'single',
  `is_required` tinyint(1) DEFAULT 0,
  `applies_to` enum('food','drink','both') DEFAULT 'both'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `option_groups`
--

INSERT INTO `option_groups` (`id`, `name`, `type`, `is_required`, `applies_to`) VALUES
(1, 'Kích thước', 'single', 1, 'both'),
(2, 'Độ cay', 'single', 0, 'food'),
(3, 'Loại nước sốt', 'single', 0, 'both'),
(4, 'Topping', 'multiple', 0, 'both'),
(5, 'Tùy chọn bổ sung', 'multiple', 0, 'both'),
(8, 'test nhom', 'single', 0, 'both');

-- --------------------------------------------------------

--
-- Table structure for table `option_items`
--

CREATE TABLE `option_items` (
  `id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `label` varchar(100) NOT NULL,
  `price` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `option_items`
--

INSERT INTO `option_items` (`id`, `group_id`, `label`, `price`) VALUES
(1, 1, 'Vừa (300ml)', 0),
(2, 1, 'Lớn (500ml)', 15000),
(3, 2, 'Không cay', 0),
(4, 2, 'Ít cay', 0),
(5, 2, 'Cay vừa', 0),
(6, 2, 'Cay nhiều', 0),
(7, 3, 'Không sốt', 0),
(8, 3, 'Sốt cay', 0),
(9, 3, 'Sốt chua ngọt', 0),
(10, 3, 'Sốt nấm', 0),
(11, 4, 'Đậu hũ non', 8000),
(12, 4, 'Nấm đông cô', 10000),
(13, 4, 'Rau củ mix', 7000),
(14, 4, 'Chả chay', 9000),
(15, 5, 'Không hành', 0),
(16, 5, 'Ít đường', 0),
(17, 5, 'Thêm chanh', 0),
(18, 5, 'Thêm ớt', 0),
(21, 8, 'đậu hủ', 10000),
(22, 8, 'hehe', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `delivery_address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `payment_method` enum('cod','bank','momo','vnpay') DEFAULT 'cod',
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `confirmed_at` datetime DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `customer_name`, `customer_phone`, `customer_email`, `delivery_address`, `notes`, `payment_method`, `subtotal`, `shipping_fee`, `total_amount`, `status`, `created_at`, `updated_at`, `confirmed_at`, `shipped_at`, `delivered_at`, `completed_at`, `cancelled_at`) VALUES
(1, 1, 'ORD20250530120251444', '31313', '1313131', 'admin@gmail.com', '31313', '131', 'cod', 83000.00, 30000.00, 113000.00, 'pending', '2025-05-30 10:02:51', '2025-06-01 11:42:19', NULL, NULL, NULL, NULL, NULL),
(2, 1, 'ORD20250530120714395', '31 3131', '1313131', 'admin@gmail.com', '3131', '3131', 'cod', 126000.00, 30000.00, 156000.00, 'pending', '2025-05-30 10:07:14', '2025-06-01 11:42:19', NULL, NULL, NULL, NULL, NULL),
(3, 1, 'ORD20250530120848117', '31313', '1313131', 'admin@gmail.com', '3131', '', 'cod', 30000.00, 30000.00, 60000.00, 'pending', '2025-05-30 10:08:48', '2025-06-01 11:42:19', NULL, NULL, NULL, NULL, NULL),
(4, 1, 'ORD20250530120908461', '31313', '1313131', 'admin@gmail.com', '3131', '3131', 'cod', 28000.00, 30000.00, 58000.00, 'pending', '2025-05-30 10:09:08', '2025-06-01 11:42:19', NULL, NULL, NULL, NULL, NULL),
(5, 1, 'ORD20250531044316485', 'admin', '1313131', 'admin@gmail.com', '14141', '4141', 'cod', 50000.00, 30000.00, 80000.00, 'pending', '2025-05-31 02:43:16', '2025-06-01 11:42:19', NULL, NULL, NULL, NULL, NULL),
(6, 1, 'ORD20250531044442518', '3131', '1313131', 'admin@gmail.com', '313131', '', 'cod', 50000.00, 30000.00, 80000.00, 'pending', '2025-05-31 02:44:42', '2025-06-01 11:42:19', NULL, NULL, NULL, NULL, NULL),
(7, 1, 'ORD20250531044620961', '3131', '1313131', 'admin@gmail.com', '3131', '3131', 'cod', 38000.00, 30000.00, 68000.00, 'confirmed', '2025-05-31 02:46:20', '2025-06-01 06:43:51', '2025-06-01 06:43:51', NULL, NULL, NULL, NULL),
(8, 1, 'ORD20250531044910754', '3131', '1313131', 'admin@gmail.com', '31313', '3131', 'cod', 65000.00, 30000.00, 95000.00, 'completed', '2025-05-31 02:49:10', '2025-06-01 11:42:19', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `item_total` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `options`, `item_total`, `created_at`) VALUES
(1, 1, 21, 'Phở Chay', 50000.00, 1, '{\"selectedOptions\":{\"1\":{\"id\":\"2\",\"label\":\"L\\u1edbn (500ml) (+15.000\\u0111)\",\"price\":15000,\"type\":\"single\"},\"2\":{\"id\":\"4\",\"label\":\"\\u00cdt cay\",\"price\":0,\"type\":\"single\"},\"3\":{\"id\":\"9\",\"label\":\"S\\u1ed1t chua ng\\u1ecdt\",\"price\":0,\"type\":\"single\"},\"4\":{\"items\":[{\"id\":\"11\",\"label\":\"\\u0110\\u1eadu h\\u0169 non\",\"price\":8000},{\"id\":\"12\",\"label\":\"N\\u1ea5m \\u0111\\u00f4ng c\\u00f4\",\"price\":10000}],\"type\":\"multiple\"},\"5\":{\"items\":[{\"id\":\"15\",\"label\":\"Kh\\u00f4ng h\\u00e0nh\",\"price\":0}],\"type\":\"multiple\"}},\"note\":\"3131\"}', 83000.00, '2025-05-30 10:02:51'),
(2, 2, 21, 'Phở Chay', 50000.00, 1, '{\"selectedOptions\":{\"1\":{\"id\":\"1\",\"label\":\"V\\u1eeba (300ml)\",\"price\":0,\"type\":\"single\"},\"2\":{\"id\":\"3\",\"label\":\"Kh\\u00f4ng cay\",\"price\":0,\"type\":\"single\"},\"3\":{\"id\":\"7\",\"label\":\"Kh\\u00f4ng s\\u1ed1t\",\"price\":0,\"type\":\"single\"},\"4\":{\"items\":[{\"id\":\"11\",\"label\":\"\\u0110\\u1eadu h\\u0169 non\",\"price\":8000},{\"id\":\"12\",\"label\":\"N\\u1ea5m \\u0111\\u00f4ng c\\u00f4\",\"price\":10000}],\"type\":\"multiple\"},\"5\":{\"items\":[{\"id\":\"15\",\"label\":\"Kh\\u00f4ng h\\u00e0nh\",\"price\":0}],\"type\":\"multiple\"}},\"note\":\"\"}', 68000.00, '2025-05-30 10:07:14'),
(3, 2, 39, 'Trà Đào Cam Sả', 30000.00, 1, '{\"selectedOptions\":[],\"note\":\"\"}', 30000.00, '2025-05-30 10:07:14'),
(4, 2, 40, 'Nước Rau Má Đậu Xanh', 28000.00, 1, '{\"selectedOptions\":[],\"note\":\"\"}', 28000.00, '2025-05-30 10:07:14'),
(5, 3, 39, 'Trà Đào Cam Sả', 30000.00, 1, '{\"selectedOptions\":[],\"note\":\"\"}', 30000.00, '2025-05-30 10:08:48'),
(6, 4, 40, 'Nước Rau Má Đậu Xanh', 28000.00, 1, '{\"selectedOptions\":[],\"note\":\"\"}', 28000.00, '2025-05-30 10:09:08'),
(7, 5, 36, 'Bí Đỏ Hầm', 42000.00, 1, '{\"selectedOptions\":{\"1\":{\"id\":\"1\",\"label\":\"Nh\\u1ecf\",\"price\":0,\"type\":\"single\"},\"2\":{\"id\":\"4\",\"label\":\"Truy\\u1ec1n th\\u1ed1ng\",\"price\":0,\"type\":\"single\"},\"3\":{\"items\":[{\"id\":\"7\",\"label\":\"Rau c\\u1ee7 th\\u00eam\",\"price\":8000}],\"type\":\"multiple\"}},\"note\":\"\"}', 50000.00, '2025-05-31 02:43:16'),
(8, 6, 36, 'Bí Đỏ Hầm', 42000.00, 1, '{\"selectedOptions\":{\"1\":{\"id\":\"1\",\"label\":\"Nh\\u1ecf\",\"price\":0,\"type\":\"single\"},\"2\":{\"id\":\"4\",\"label\":\"Truy\\u1ec1n th\\u1ed1ng\",\"price\":0,\"type\":\"single\"},\"3\":{\"items\":[{\"id\":\"7\",\"label\":\"Rau c\\u1ee7 th\\u00eam\",\"price\":8000}],\"type\":\"multiple\"}},\"note\":\"\"}', 50000.00, '2025-05-31 02:44:42'),
(9, 7, 39, 'Trà Đào Cam Sả', 30000.00, 1, '{\"selectedOptions\":{\"1\":{\"id\":\"1\",\"label\":\"Nh\\u1ecf\",\"price\":0,\"type\":\"single\"},\"2\":{\"id\":\"4\",\"label\":\"Truy\\u1ec1n th\\u1ed1ng\",\"price\":0,\"type\":\"single\"},\"3\":{\"items\":[{\"id\":\"7\",\"label\":\"Rau c\\u1ee7 th\\u00eam\",\"price\":8000}],\"type\":\"multiple\"}},\"note\":\"\"}', 38000.00, '2025-05-31 02:46:20'),
(10, 8, 36, 'Bí Đỏ Hầm', 42000.00, 1, '{\"selectedOptions\":{\"1\":{\"id\":\"2\",\"label\":\"V\\u1eeba\",\"price\":15000,\"type\":\"single\"},\"2\":{\"id\":\"4\",\"label\":\"Truy\\u1ec1n th\\u1ed1ng\",\"price\":0,\"type\":\"single\"},\"3\":{\"items\":[{\"id\":\"7\",\"label\":\"Rau c\\u1ee7 th\\u00eam\",\"price\":8000}],\"type\":\"multiple\"}},\"note\":\"13131\"}', 65000.00, '2025-05-31 02:49:10');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `flavor_profile` varchar(100) DEFAULT NULL,
  `size_options` varchar(255) DEFAULT NULL,
  `addon_options` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `ingredients`, `flavor_profile`, `size_options`, `addon_options`, `image`, `price`, `is_featured`, `created_at`, `updated_at`) VALUES
(21, 1, 'Phở Chay', 'pho-chay', 'Phở chay thanh đạm với nước dùng nấm hương.', 'nấm hương, bánh phở, rau thơm', 'thanh đạm', 'Lớn|500ml', NULL, 'images/pho.jpg', 50000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(22, 1, 'Cơm Tấm Chay', 'com-tam-chay', 'Cơm tấm chay ăn kèm sườn đậu và đồ chua.', 'gạo tấm, đậu hũ, rau củ', 'đậm đà', 'Vừa|Lớn', NULL, 'images/comtam.jpg', 45000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(23, 2, 'Bún Huế Chay', 'bun-hue-chay', 'Món bún chay cay nồng đặc trưng xứ Huế.', 'bún, đậu, nấm, sa tế', 'cay', 'Vừa|Cay nhiều', NULL, 'images/bunhue.jpg', 55000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(24, 2, 'Bún Thái Chay', 'bun-thai-chay', 'Bún thái chay chua cay vừa miệng.', 'nấm, đậu, rau', 'chua cay', 'Lớn|Mặc định', NULL, 'images/bunthai.jpg', 52000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(25, 3, 'Lẩu Nấm Chay', 'lau-nam-chay', 'Lẩu chay gồm nhiều loại nấm và rau.', 'nấm đùi gà, rau cải, đậu hũ', 'ngọt thanh', 'Lớn', NULL, 'images/launam.jpg', 150000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(26, 3, 'Lẩu Kim Chi Chay', 'lau-kimchi-chay', 'Lẩu chay vị kim chi nhẹ nhàng.', 'kim chi chay, nấm, đậu', 'cay nhẹ', '2 người|4 người', NULL, 'images/laukimchi.jpg', 160000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(27, 4, 'Đậu Hũ Sốt Cà', 'dau-hu-sot-ca', 'Đậu hũ mềm chiên giòn kèm sốt cà.', 'đậu hũ, sốt cà, rau', 'chua ngọt', 'Mặc định', NULL, 'images/dauhu.jpg', 40000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(28, 4, 'Nấm Kho Tiêu', 'nam-kho-tieu', 'Nấm rơm kho tiêu đậm đà, ăn cùng cơm.', 'nấm rơm, tiêu, xì dầu', 'mặn cay', 'Mặc định', NULL, 'images/namkho.jpg', 45000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(29, 4, 'Chả Giò Chay', 'cha-gio-chay', 'Chả giò chiên giòn nhân rau củ.', 'cà rốt, miến, đậu hũ', 'giòn béo', '4 cuốn|6 cuốn', NULL, 'images/chagio.jpg', 38000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(30, 4, 'Gỏi Cuốn Chay', 'goi-cuon-chay', 'Gỏi cuốn thanh mát với nước chấm đặc biệt.', 'bún, rau sống, đậu', 'thanh nhẹ', '2 cuốn|4 cuốn', NULL, 'images/goicuon.jpg', 35000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(31, 1, 'Sữa Hạt Sen', 'sua-hat-sen', 'Sữa hạt sen tươi mát, không đường.', 'hạt sen, nước lọc', 'ngọt nhẹ', 'Mặc định', NULL, 'images/suahat.jpg', 25000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(32, 1, 'Nước Chanh Sả', 'nuoc-chanh-sa', 'Nước chanh sả mát lạnh, tốt cho tiêu hóa.', 'chanh, sả, đường phèn', 'mát lạnh', 'Ít đá|Nhiều đá', NULL, 'images/nuocchanhsa.jpg', 28000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(33, 1, 'Cơm Chiên Chay', 'com-chien-chay', 'Cơm chiên thập cẩm rau củ.', 'cơm, cà rốt, đậu, bắp', 'béo ngậy', 'Mặc định', NULL, 'images/comchien.jpg', 48000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(34, 2, 'Bánh Canh Chay', 'banh-canh-chay', 'Bánh canh mềm, nước dùng ngọt thanh.', 'bánh canh, nấm, rau', 'thanh đạm', 'Lớn', NULL, 'images/banhcanh.jpg', 47000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(35, 3, 'Lẩu Thái Chay', 'lau-thai-chay', 'Lẩu Thái chay chua cay đậm vị.', 'nấm, sả, đậu', 'cay chua', '2 người|4 người', NULL, 'images/thaichay.jpg', 170000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(36, 3, 'Bí Đỏ Hầm', 'bi-do-ham', 'Bí đỏ hầm đậu phộng mềm thơm.', 'bí đỏ, đậu phộng, nước dừa', 'ngọt béo', 'Mặc định', NULL, 'images/bidoham.jpg', 42000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(37, 3, 'Đậu Hũ Chiên Sả', 'dau-hu-sả', 'Đậu hũ chiên giòn tẩm sả ớt.', 'đậu hũ, sả, ớt', 'mặn cay', 'Mặc định', NULL, 'images/dauhusa.jpg', 45000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(38, 2, 'Bún Chả Chay', 'bun-cha-chay', 'Bún chả chay ăn kèm đồ chua và nước mắm chay.', 'bún, đậu, rau', 'đậm đà', 'Mặc định', NULL, 'images/buncha.jpg', 50000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(39, 3, 'Trà Đào Cam Sả', 'tra-dao-cam-sa', 'Thức uống trái cây chay mát lạnh.', 'trà, đào, cam, sả', 'thanh mát', 'Ít đá|Nhiều đá', NULL, 'images/tradao.jpg', 30000.00, 0, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(40, 3, 'Nước Rau Má Đậu Xanh', 'nuoc-rauma', 'Nước rau má đậu xanh giúp mát gan.', 'rau má, đậu xanh, đường', 'mát lạnh', 'Mặc định', NULL, 'images/rauma.jpg', 28000.00, 1, '2025-05-29 16:40:18', '2025-05-29 16:40:18'),
(41, 2, '313131', '313131', '1231313', '31313131', NULL, NULL, NULL, 'https://cdn.tgdd.vn/Files/2022/03/21/1421421/tong-hop-16-cach-lam-mon-chay-thanh-dam-dinh-duong-tai-nha-202203211050443101.jpg', 3131.00, 0, '2025-05-30 12:10:49', '2025-05-30 12:10:49'),
(42, 11, 'rau muống', 'rau-mung', 'mau muống', 'rau muống', NULL, NULL, NULL, 'https://cafefcdn.com/203337114487263232/2025/5/22/hinh-13-1477885609-17478918795291411602985-1747904942403-1747904944112789608768.jpg', 20000.00, 1, '2025-06-01 03:05:05', '2025-06-01 03:05:05');

-- --------------------------------------------------------

--
-- Table structure for table `product_options`
--

CREATE TABLE `product_options` (
  `product_id` int(11) NOT NULL,
  `option_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_options`
--

INSERT INTO `product_options` (`product_id`, `option_group_id`) VALUES
(21, 1),
(21, 2),
(21, 3),
(21, 4),
(21, 5),
(41, 1),
(41, 3),
(41, 4),
(42, 1),
(42, 8);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `password`, `created_at`, `phone`, `status`) VALUES
(1, '3131', 'admin@gmail.com', 'admin', '$2y$10$LXYQhOarJNH44GS8v2Jl.Om8pf7clpTLTHpYTnLZj6Z/OAprLf4cy', '2025-05-30 02:12:22', '1313131', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `option_groups`
--
ALTER TABLE `option_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `option_items`
--
ALTER TABLE `option_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_options`
--
ALTER TABLE `product_options`
  ADD PRIMARY KEY (`product_id`,`option_group_id`),
  ADD KEY `option_group_id` (`option_group_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `option_groups`
--
ALTER TABLE `option_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `option_items`
--
ALTER TABLE `option_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `option_items`
--
ALTER TABLE `option_items`
  ADD CONSTRAINT `option_items_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `option_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_options`
--
ALTER TABLE `product_options`
  ADD CONSTRAINT `product_options_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_options_ibfk_2` FOREIGN KEY (`option_group_id`) REFERENCES `option_groups` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
