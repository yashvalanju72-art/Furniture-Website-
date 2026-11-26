-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2026 at 07:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `furniture_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', 'admin123', '2026-02-21 08:27:29');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(7, 2, 8, 3, '2026-02-23 06:35:10'),
(9, 2, 7, 3, '2026-02-23 06:48:06'),
(15, 2, 19, 1, '2026-02-23 07:13:01'),
(16, 1, 20, 1, '2026-02-23 07:30:43'),
(17, 1, 24, 1, '2026-02-23 07:30:48'),
(19, 3, 9, 1, '2026-02-23 07:36:07');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Amrapali sandip gamare', 'amrapali17@gmail.com', 'interior', 'tnx for this good quality', '2026-02-23 06:47:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Processing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total`, `status`, `created_at`, `name`, `email`, `address`, `city`, `phone`, `payment_method`, `order_date`) VALUES
(1, 1, 299.00, 'Processing', '2026-02-21 08:46:57', 'Amrapali sandip gamare', 'amrapali17@gmail.com', 'chiplun', 'chiplun,kherdi', '91657456223', 'COD', '2026-02-23 11:53:24'),
(2, 3, 299.00, 'Processing', '2026-02-23 07:35:38', 'nilam', 'nilam@gmail.com', 'chiplun', 'chiplun,kherdi', '9854671235', 'Card', '2026-02-23 13:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 8, 1, 299.00),
(2, 2, 8, 1, 299.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `stock`, `created_at`) VALUES
(6, 'Luxury Sofa', 'Comfortable 3-seater sofa for your living room.', 599.00, 'img/products/product1.jpg', 0, '2026-02-21 08:37:59'),
(7, 'Modern Dining Table', 'Elegant dining table for 6 people.', 699.00, 'img/products/product2.jpg', 0, '2026-02-21 08:37:59'),
(8, 'Cozy Armchair', 'Perfect armchair for reading or relaxing.', 299.00, 'img/products/product3.jpg', 0, '2026-02-21 08:37:59'),
(9, 'Wooden Coffee Table', 'Classic wooden coffee table for living room.', 199.00, 'img/products/product4.jpg', 0, '2026-02-21 08:37:59'),
(10, 'King Size Bed', 'Spacious bed for ultimate comfort.', 799.00, 'img/products/product5.jpg', 0, '2026-02-21 08:37:59'),
(11, 'Wardrobe Cabinet', 'Stylish wardrobe for bedroom storage.', 499.00, 'img/products/product6.jpg', 0, '2026-02-21 08:37:59'),
(12, 'Nightstand', 'Compact nightstand for bedside essentials.', 129.00, 'img/products/product7.jpg', 0, '2026-02-21 08:37:59'),
(13, 'Bookshelf', 'Wooden bookshelf for home library.', 249.00, 'img/products/product8.jpg', 0, '2026-02-21 08:37:59'),
(14, 'Recliner Sofa', 'Comfortable recliner sofa with plush cushions.', 599.00, 'img/products/product9.jpg', 0, '2026-02-21 08:37:59'),
(15, 'Dining Chair Set', 'Set of 4 modern dining chairs.', 299.00, 'img/products/product10.jpg', 0, '2026-02-21 08:37:59'),
(16, 'Round Dining Table', 'Round table perfect for family meals.', 349.00, 'img/products/product11.jpg', 0, '2026-02-21 08:37:59'),
(17, 'Office Desk', 'Modern office desk with storage.', 399.00, 'img/products/product12.jpg', 0, '2026-02-21 08:37:59'),
(18, 'Office Chair', 'Ergonomic chair for long working hours.', 199.00, 'img/products/product13.jpg', 0, '2026-02-21 08:37:59'),
(19, 'TV Stand', 'Stylish TV stand with shelves.', 249.00, 'img/products/product14.jpg', 0, '2026-02-21 08:37:59'),
(20, 'Corner Sofa', 'Spacious corner sofa for family.', 699.00, 'img/products/product15.jpg', 0, '2026-02-21 08:37:59'),
(21, 'Glass Coffee Table', 'Modern glass coffee table.', 179.00, 'img/products/product16.jpg', 0, '2026-02-21 08:37:59'),
(22, 'Single Bed', 'Cozy single bed for kids or guest room.', 299.00, 'img/products/product17.jpg', 0, '2026-02-21 08:37:59'),
(23, 'Double Bed', 'Comfortable double bed with headboard.', 599.00, 'img/products/product18.jpg', 0, '2026-02-21 08:37:59'),
(24, 'Sofa Set', '3+2 seating sofa set for living room.', 899.00, 'img/products/product19.jpg', 0, '2026-02-21 08:37:59'),
(25, 'Dining Bench', 'Modern dining bench for 2 people.', 149.00, 'img/products/product20.jpg', 0, '2026-02-21 08:37:59'),
(26, 'Bookshelf Corner', 'Corner bookshelf for extra storage.', 199.00, 'img/products/product21.jpg', 0, '2026-02-21 08:37:59'),
(27, 'TV Cabinet', 'Wooden TV cabinet with drawers.', 299.00, 'img/products/product22.jpg', 0, '2026-02-21 08:37:59'),
(28, 'Leather Armchair', 'Luxury leather armchair.', 399.00, 'img/products/product23.jpg', 0, '2026-02-21 08:37:59'),
(29, 'Futon Sofa', 'Convertible futon sofa bed.', 349.00, 'img/products/product24.jpg', 0, '2026-02-21 08:37:59'),
(30, 'Wardrobe Small', 'Compact wardrobe for small rooms.', 299.00, 'img/products/product25.jpg', 0, '2026-02-21 08:37:59'),
(31, 'Chest of Drawers', 'Wooden chest of drawers.', 259.00, 'img/products/product26.jpg', 0, '2026-02-21 08:37:59'),
(32, 'Bedside Table', 'Stylish bedside table.', 129.00, 'img/products/product27.jpg', 0, '2026-02-21 08:37:59'),
(33, 'TV Unit Modern', 'Modern TV unit with storage.', 399.00, 'img/products/product28.jpg', 0, '2026-02-21 08:37:59'),
(34, 'Office Storage', 'Office storage cabinet.', 199.00, 'img/products/product29.jpg', 0, '2026-02-21 08:37:59'),
(35, 'Lounge Chair', 'Comfortable lounge chair.', 299.00, 'img/products/product30.jpg', 0, '2026-02-21 08:37:59'),
(36, 'Round Coffee Table', 'Round coffee table with shelf.', 179.00, 'img/products/product31.jpg', 0, '2026-02-21 08:37:59'),
(37, 'Glass Dining Table', 'Elegant glass dining table.', 549.00, 'img/products/product32.jpg', 0, '2026-02-21 08:37:59'),
(38, 'Study Desk', 'Compact study desk.', 199.00, 'img/products/product33.jpg', 0, '2026-02-21 08:37:59'),
(39, 'Study Chair', 'Comfortable study chair.', 129.00, 'img/products/product34.jpg', 0, '2026-02-21 08:37:59'),
(40, 'Fabric Sofa', 'Soft fabric sofa for living room.', 499.00, 'img/products/product35.jpg', 0, '2026-02-21 08:37:59'),
(41, 'Dining Set', 'Dining table with 4 chairs.', 699.00, 'img/products/product36.jpg', 0, '2026-02-21 08:37:59'),
(42, 'Accent Chair', 'Stylish accent chair.', 249.00, 'img/products/product37.jpg', 0, '2026-02-21 08:37:59'),
(43, 'TV Stand Modern', 'Modern TV stand.', 299.00, 'img/products/product38.jpg', 0, '2026-02-21 08:37:59'),
(44, 'Sofa Bed', 'Convertible sofa bed.', 399.00, 'img/products/product39.jpg', 0, '2026-02-21 08:37:59'),
(45, 'Dressing Table', 'Elegant dressing table with mirror.', 499.00, 'img/products/product40.jpg', 0, '2026-02-21 08:37:59'),
(46, 'TV Wall Unit', 'TV wall unit with shelves.', 599.00, 'img/products/product41.jpg', 0, '2026-02-21 08:37:59'),
(47, 'Bookshelf Modern', 'Modern bookshelf.', 299.00, 'img/products/product42.jpg', 0, '2026-02-21 08:37:59'),
(48, 'Bed Frame', 'Solid wood bed frame.', 399.00, 'img/products/product43.jpg', 0, '2026-02-21 08:37:59'),
(49, 'Nightstand Set', 'Set of 2 nightstands.', 199.00, 'img/products/product44.jpg', 0, '2026-02-21 08:37:59'),
(50, 'Accent Table', 'Stylish accent table.', 149.00, 'img/products/product45.jpg', 0, '2026-02-21 08:37:59'),
(51, 'Leather Sofa', 'Premium leather sofa.', 899.00, 'img/products/product46.jpg', 0, '2026-02-21 08:37:59'),
(52, 'Bean Bag Chair', 'Comfortable bean bag.', 99.00, 'img/products/product47.jpg', 0, '2026-02-21 08:37:59'),
(53, 'Wooden Stool', 'Simple wooden stool.', 79.00, 'img/products/product48.jpg', 0, '2026-02-21 08:37:59'),
(54, 'Wall Shelf', 'Decorative wall shelf.', 59.00, 'img/products/product49.jpg', 0, '2026-02-21 08:37:59'),
(55, 'Console Table', 'Entryway console table.', 189.00, 'img/products/product50.jpg', 0, '2026-02-21 08:37:59');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Amrapali sandip gamare', 'amrapali17@gmail.com', '$2y$10$rTN9nkWjtzLCI0HAf4X7feXb1gC71E1N/N0vooUbAXyAlh5R45TzW', '2026-02-21 08:34:18'),
(2, 'Amrapali sandip gamare', 'amrapali27@gmail.com', '$2y$10$LjO6Vxba0QNWI7S5E.0Fr.wifdz7n45JcY3xpnriHuZZA3Y9kqfiy', '2026-02-23 06:16:36'),
(3, 'Nilam', 'nilam@gmail.com', '$2y$10$b4Rqde6Yru1QOvocmnz4cu1c0qtV60X8v4I7rCrWPeIMayuD.ZB9O', '2026-02-23 07:32:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
