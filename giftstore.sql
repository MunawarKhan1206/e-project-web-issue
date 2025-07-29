-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Pritësi (host): 127.0.0.1
-- Koha e gjenerimit: Kor 29, 2025 në 03:33 MD
-- Versioni i serverit: 10.4.27-MariaDB
-- PHP Versioni: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databaza: `giftstore`
--

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zbraz të dhënat për tabelën `admins`
--

INSERT INTO `admins` (`id`, `user_id`, `email`, `created_at`) VALUES
(1, 4, 'shahzaibbaig112@gmail.com', '2025-07-28 10:28:30');

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zbraz të dhënat për tabelën `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `address`, `phone`, `created_at`) VALUES
(1, 4, NULL, NULL, '2025-07-17 07:27:48'),
(2, 5, NULL, NULL, '2025-07-21 09:47:43'),
(3, 6, NULL, NULL, '2025-07-26 06:51:23'),
(4, 11, NULL, NULL, '2025-07-28 09:40:01'),
(5, 12, NULL, NULL, '2025-07-29 02:26:18'),
(6, 14, NULL, NULL, '2025-07-29 12:13:23');

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zbraz të dhënat për tabelën `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `department`, `created_at`) VALUES
(1, 3, NULL, '2025-07-11 15:49:57');

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `order2`
--

CREATE TABLE `order2` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `apartment` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('pending','shipped','delivered','cancelled') DEFAULT 'pending',
  `total` decimal(10,2) NOT NULL,
  `totals` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `orders2`
--

CREATE TABLE `orders2` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `delivery` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `order_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zbraz të dhënat për tabelën `orders2`
--

INSERT INTO `orders2` (`id`, `customer_name`, `recipient_name`, `email`, `phone`, `address`, `payment_method`, `subtotal`, `discount`, `delivery`, `grand_total`, `status`, `created_at`, `order_number`) VALUES
(1, 'kashif baig', 'kashif baig', 'shahzaibbaig112@gmail.com', '03202052399', 'FLAT.NO 229/230, ADAM COLONY GARDEN WEST KARACHI, 123, KARACHI EAST, 2222, France', 'paypal', '0.00', '0.00', '5.00', '5.00', 'Delivered', '2025-07-23 15:39:27', NULL),
(6, 'kashif baig', '', 'shahzaibbaig112@gmail.com', '03202052399', 'FLAT.NO 229/230, ADAM COLONY GARDEN WEST KARACHI', 'paypal', '0.00', '0.00', '0.00', '0.00', 'Pending', '2025-07-24 15:08:28', NULL),
(7, 'kashif baig', '', 'shahzaibbaig112@gmail.com', '03202052399', 'FLAT.NO 229/230, ADAM COLONY GARDEN WEST KARACHI', 'vpp', '0.00', '0.00', '0.00', '0.00', 'Pending', '2025-07-24 20:15:50', 'VPP20250724773313'),
(8, 'kashif baig', '', 'shahzaibbaig112@gmail.com', '03202052399', 'FLAT.NO 229/230, ADAM COLONY GARDEN WEST KARACHI', 'vpp', '799.00', '0.00', '200.00', '999.00', 'Delivered', '2025-07-24 20:27:10', 'VPP20250724140706'),
(9, 'Shahzaib Baig', '', 'shahzaibbaig112@gmail.com', '03202052399', 'Al Naseer Square', 'vpp', '240.00', '0.00', '200.00', '440.00', 'Delivered', '2025-07-24 23:07:56', 'VPP20250724778568'),
(10, 'customer 123', '', 'customer@gmail.com', '1234567', '12345', 'vpp', '700.00', '0.00', '200.00', '900.00', 'Delivered', '2025-07-26 11:52:20', 'VPP20250726622525'),
(11, 'customer 123', '', 'customer12@gmail.com', '1234567', 'FLAT.NO 229/230, ADAM COLONY GARDEN WEST KARACHI', 'vpp', '470.00', '0.00', '200.00', '670.00', 'Delivered', '2025-07-29 07:27:09', 'VPP20250729132098'),
(12, 'kashif baig', '', 'shahzaibbaig112@gmail.com', '03202052399', 'FLAT.NO 229/230, ADAM COLONY GARDEN WEST KARACHI', 'check', '120.00', '0.00', '200.00', '320.00', 'Processed', '2025-07-29 07:58:35', 'PP20250729484064');

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zbraz të dhënat për tabelën `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(2, 6, 2, 3, '350.00'),
(3, 6, 7, 1, '250.00'),
(4, 6, 6, 2, '150.00'),
(5, 6, 5, 4, '199.00'),
(6, 6, 3, 2, '799.00'),
(7, 7, 2, 1, '350.00'),
(8, 8, 3, 1, '799.00'),
(9, 9, 1, 2, '120.00'),
(10, 10, 2, 2, '350.00'),
(11, 11, 1, 1, '120.00'),
(12, 11, 2, 1, '350.00'),
(13, 12, 1, 1, '120.00');

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zbraz të dhënat për tabelën `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(1, 'Hard-board', 'stationery', 'Comfortable for Work', '120.00', 1, 'product-board.png', '2025-07-14 15:57:00'),
(2, 'Book Set', 'stationery', 'A collection of notebooks for students and professionals.', '350.00', 22, 'product-books.png', '2025-07-14 16:13:38'),
(3, 'Simple Calculator', 'stationery', 'Durable and accurate calculator for math and science.', '799.00', 15, 'product-calculator.png', '2025-07-14 16:13:38'),
(4, 'Wooden Pen Holder', 'stationery', 'Organize your pens with this elegant holder.', '299.00', 30, 'product-penholder.png', '2025-07-14 16:13:38'),
(5, 'Color Pencils Set', 'stationery', '12-piece bright colored pencils for artwork.', '199.00', 40, 'product-colours.png', '2025-07-14 16:13:38'),
(6, 'Pack of Pens', 'stationery', 'Smooth-writing gel pens, pack of 5.', '150.00', 50, 'product-pens.png', '2025-07-14 16:13:38'),
(7, 'Stationery Pouch', 'stationery', 'Zipper pouch for keeping all essentials.', '250.00', 35, 'product-pouch.png', '2025-07-14 16:13:38'),
(8, 'Measuring Tape', 'stationery', '60-inch measuring tape for craft and tailoring.', '120.00', 2, 'product-measuringtape.png', '2025-07-14 16:13:38'),
(10, 'Teddy Bear', 'gift', 'Wrap your love in a hug with this adorable teddy bear! Crafted from ultra-soft, high-quality plush, it’s the perfect gift to express affection on birthdays, anniversaries, or just because. Whether for a child or a loved one, this teddy brings comfort, warmth, and endless smiles.', '550.00', 5, '1753760261_image__2_-removebg-preview.png', '2025-07-29 03:37:41'),
(11, 'Watch With Strap', 'Gift', '', '1200.00', 3, '1753791718_gift.png', '2025-07-29 12:21:58'),
(12, 'Offroad RC Car', 'Gift', '', '2500.00', 1, '1753791831_pic.png', '2025-07-29 12:23:51'),
(13, 'Nike Bag', 'BAGS', '', '6000.00', 20, '1753791900_o.png', '2025-07-29 12:25:00'),
(14, 'Original Nike Bottle', 'Bottles', '', '15000.00', 2, '1753792733_d.png', '2025-07-29 12:38:53');

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `returns`
--

CREATE TABLE `returns` (
  `id` int(11) NOT NULL,
  `order_number` varchar(250) NOT NULL,
  `reason` text NOT NULL,
  `return_date` datetime NOT NULL,
  `status` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zbraz të dhënat për tabelën `returns`
--

INSERT INTO `returns` (`id`, `order_number`, `reason`, `return_date`, `status`) VALUES
(1, 'VPP20250724778568', 'samaj nahi arha', '2025-07-26 09:35:04', 'rejected'),
(2, 'VPP20250724140706', 'chas ni andi payi', '2025-07-26 11:48:04', 'rejected'),
(3, 'VPP20250726622525', 'dil nahi kr rha', '2025-07-26 11:54:31', 'rejected'),
(4, 'VPP20250729132098', 'bs dil nahi kra', '2025-07-29 07:28:57', 'approved');

-- --------------------------------------------------------

--
-- Struktura e tabelës për tabelën `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employee','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zbraz të dhënat për tabelën `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, ' hi', 'hi@gmail.com', '$2y$10$0Zt4Lut8Bzsp1OEGUZdVIeK344PYmS8w1RUF7xWTMpGIdzN04X4A.', 'employee', '2025-07-11 15:45:47'),
(3, ' hi', 'hi112@gmail.com', '$2y$10$QG7dnmKx4lUoO1pwW1viPuQrY8ejAgD/SQfXlC/iSdI60mds5hb76', 'employee', '2025-07-11 15:49:57'),
(4, 'Shahzaib Baig', 'shahzaibbaig112@gmail.com', '', 'admin', '2025-07-17 07:27:48'),
(5, 'shahaib', 'shahzaibbaig12@gmail.com', '$2y$10$agjYglu0r5a6Mi4avy05luIvoQn48N1Zp.Tyvb3HLMlKKO13uFboe', 'customer', '2025-07-21 09:47:43'),
(6, 'customer', 'customer@gmail.com', '$2y$10$QmUy7YSHXtA6KtjBhr1c0OBRudl1Q8PK4//AozDbYsDl3SNEKUO.a', 'customer', '2025-07-26 06:51:23'),
(11, 'helo', '123@gmail.com', '$2y$10$9jZpDSd513N1NGbsdM2w6.n8lcinUiWoDUrVd0cWg3qUkL/UAgXma', 'customer', '2025-07-28 09:40:01'),
(12, 'customer12', 'customer12@gmail.com', '$2y$10$ORvEcoC62Lr02553J3RgAulXXfRRbxV5jYmjKm2trTxLTw/.RF.9a', 'customer', '2025-07-29 02:26:18'),
(13, ' hi', 'shahzaibbaig@gmail.com', '$2y$10$GdaAjpYXRrI3zdPDqxwIVOPjTPDYW3t0wYtZ.gP6pTwasU/8tM6ue', 'employee', '2025-07-29 02:55:32'),
(14, 'Shahzaib', 'shahzaibadmin@gmail.com', '$2y$10$PsdejYKxq7kYnkG4or/65.uzR9aiLDc27i1OD5./vHpPVawSnbqJ2', 'admin', '2025-07-29 12:13:23');

--
-- Indekset për tabelat e hedhura
--

--
-- Indekset për tabelë `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indekset për tabelë `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indekset për tabelë `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indekset për tabelë `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indekset për tabelë `order2`
--
ALTER TABLE `order2`
  ADD PRIMARY KEY (`id`);

--
-- Indekset për tabelë `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indekset për tabelë `orders2`
--
ALTER TABLE `orders2`
  ADD PRIMARY KEY (`id`);

--
-- Indekset për tabelë `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_ibfk_1` (`order_id`);

--
-- Indekset për tabelë `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indekset për tabelë `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`);

--
-- Indekset për tabelë `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT për tabelat e hedhura
--

--
-- AUTO_INCREMENT për tabelë `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT për tabelë `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT për tabelë `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT për tabelë `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT për tabelë `order2`
--
ALTER TABLE `order2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT për tabelë `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT për tabelë `orders2`
--
ALTER TABLE `orders2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT për tabelë `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT për tabelë `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT për tabelë `returns`
--
ALTER TABLE `returns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT për tabelë `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Detyrimet për tabelat e hedhura
--

--
-- Detyrimet për tabelën `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Detyrimet për tabelën `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Detyrimet për tabelën `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Detyrimet për tabelën `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Detyrimet për tabelën `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Detyrimet për tabelën `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders2` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
