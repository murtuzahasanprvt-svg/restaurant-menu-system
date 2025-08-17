-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2025 at 11:27 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_menu_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'Super Admin logged in', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-14 22:29:01'),
(2, 1, 'user_create', 'Created user: manager@example.com', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-14 21:29:01'),
(3, 2, 'login', 'Branch Manager logged in', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-14 22:59:01'),
(4, 2, 'order_update', 'Updated order status to completed', '192.168.1.101', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-14 22:29:01'),
(5, 3, 'login', 'Staff Member logged in', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-14 22:44:01'),
(6, 1, 'qr_generate', 'Generated QR codes for Main Downtown Branch', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-14 20:29:01'),
(7, 4, 'order_create', 'Created new order ORD-2024-001', '192.168.1.103', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15', '2025-08-14 21:29:01'),
(8, 1, 'system_settings_update', 'Updated system settings', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', '2025-08-14 18:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `version` varchar(20) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `directory_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `is_installed` tinyint(1) DEFAULT 0,
  `is_system` tinyint(1) DEFAULT 0,
  `priority` int(11) DEFAULT 10,
  `config_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`id`, `name`, `description`, `status`, `version`, `author`, `directory_name`, `is_active`, `is_installed`, `is_system`, `priority`, `config_data`, `created_at`, `updated_at`) VALUES
(1, 'Sample Addon', 'A sample addon for demonstration', 'active', '1.0.0', 'System', '', 0, 0, 0, 10, NULL, '2025-08-14 23:29:01', '2025-08-14 23:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `opening_hours` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `address`, `phone`, `email`, `description`, `status`, `opening_hours`, `logo_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Main Downtown Branch', '123 Main Street, Downtown, NY 10001', '+1-555-0100', 'downtown@restaurant.com', 'Our flagship location in the heart of downtown with elegant dining atmosphere and exceptional service.', 'active', 'Mon-Thu: 11:00-22:00, Fri-Sat: 11:00-23:00, Sun: 12:00-21:00', NULL, 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(2, 'Mall Location', '456 Shopping Mall Ave, Mall Area, NY 10002', '+1-555-0200', 'mall@restaurant.com', 'Convenient location in the popular shopping mall, perfect for family dining and shopping breaks.', 'active', 'Mon-Sat: 10:00-22:00, Sun: 11:00-21:00', NULL, 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(3, 'Airport Branch', '789 Airport Terminal, International Airport, NY 10003', '+1-555-0300', 'airport@restaurant.com', 'Premium dining experience at the international airport, serving travelers 24/7 with quick service.', 'active', '24 Hours Daily', NULL, 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `menu_categories`
--

CREATE TABLE `menu_categories` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_categories`
--

INSERT INTO `menu_categories` (`id`, `branch_id`, `name`, `description`, `display_order`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Appetizers', 'Start your meal with our delicious appetizers', 1, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(2, 1, 'Main Courses', 'Hearty main courses for a satisfying meal', 2, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(3, 1, 'Desserts', 'Sweet endings to your perfect meal', 3, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(4, 1, 'Beverages', 'Refreshing drinks and beverages', 4, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(5, 2, 'Appetizers', 'Start your meal with our delicious appetizers', 1, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(6, 2, 'Main Courses', 'Hearty main courses for a satisfying meal', 2, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(7, 2, 'Desserts', 'Sweet endings to your perfect meal', 3, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(8, 2, 'Beverages', 'Refreshing drinks and beverages', 4, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(9, 3, 'Appetizers', 'Quick appetizers for travelers', 1, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(10, 3, 'Main Courses', 'Full meals for hungry travelers', 2, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(11, 3, 'Desserts', 'Sweet treats on the go', 3, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(12, 3, 'Beverages', 'Drinks and refreshments', 4, 'active', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `is_available` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `preparation_time` int(11) DEFAULT NULL,
  `calories` int(11) DEFAULT NULL,
  `allergens` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description`, `ingredients`, `price`, `image_url`, `status`, `is_available`, `is_featured`, `preparation_time`, `calories`, `allergens`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Spring Rolls', 'Crispy vegetable spring rolls with sweet chili sauce', 'Vegetables, wrappers, chili sauce', 8.99, 'images/appetizers/spring-rolls.jpg', 'unavailable', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-17 19:08:36'),
(2, 1, 'Garlic Bread', 'Fresh baked bread with garlic butter and herbs', 'Bread, garlic, butter, herbs', 6.99, 'images/appetizers/garlic-bread.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(3, 1, 'Caesar Salad', 'Classic Caesar salad with croutons and parmesan', 'Romaine lettuce, croutons, parmesan, caesar dressing', 9.99, 'images/appetizers/caesar-salad.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(4, 2, 'Grilled Salmon', 'Fresh Atlantic salmon with lemon butter sauce', 'Salmon, lemon, butter, herbs', 24.99, 'images/main/salmon.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(5, 2, 'Chicken Alfredo', 'Creamy fettuccine pasta with grilled chicken', 'Fettuccine, chicken, alfredo sauce, parmesan', 18.99, 'images/main/chicken-alfredo.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(6, 2, 'Beef Steak', 'Premium ribeye steak with mashed potatoes', 'Ribeye steak, potatoes, vegetables', 28.99, 'images/main/steak.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(7, 2, 'Vegetable Stir Fry', 'Fresh vegetables stir-fried with tofu', 'Mixed vegetables, tofu, soy sauce, ginger', 16.99, 'images/main/stir-fry.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(8, 3, 'Chocolate Cake', 'Rich chocolate cake with chocolate ganache', 'Chocolate, flour, eggs, cream', 7.99, 'images/desserts/chocolate-cake.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(9, 3, 'Ice Cream', 'Vanilla ice cream with chocolate sauce', 'Ice cream, chocolate sauce', 5.99, 'images/desserts/ice-cream.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(10, 3, 'Cheesecake', 'New York style cheesecake with berry compote', 'Cream cheese, graham cracker, berries', 8.99, 'images/desserts/cheesecake.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(11, 4, 'Soda', 'Fresh soda with ice and lemon', 'Soda, ice, lemon', 3.99, 'images/beverages/soda.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(12, 4, 'Fresh Juice', 'Freshly squeezed orange juice', 'Oranges', 4.99, 'images/beverages/juice.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(13, 4, 'Coffee', 'Fresh brewed coffee', 'Coffee beans, water', 2.99, 'images/beverages/coffee.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(14, 4, 'Iced Tea', 'Refreshing iced tea with lemon', 'Tea, ice, lemon', 3.49, 'images/beverages/iced-tea.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(15, 5, 'Spring Rolls', 'Crispy vegetable spring rolls with sweet chili sauce', 'Vegetables, wrappers, chili sauce', 8.99, 'images/appetizers/spring-rolls.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(16, 5, 'Garlic Bread', 'Fresh baked bread with garlic butter and herbs', 'Bread, garlic, butter, herbs', 6.99, 'images/appetizers/garlic-bread.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(17, 5, 'Mozzarella Sticks', 'Fried mozzarella sticks with marinara sauce', 'Mozzarella, breadcrumbs, marinara', 7.99, 'images/appetizers/mozzarella-sticks.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(18, 6, 'Grilled Salmon', 'Fresh Atlantic salmon with lemon butter sauce', 'Salmon, lemon, butter, herbs', 24.99, 'images/main/salmon.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(19, 6, 'Chicken Alfredo', 'Creamy fettuccine pasta with grilled chicken', 'Fettuccine, chicken, alfredo sauce, parmesan', 18.99, 'images/main/chicken-alfredo.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(20, 6, 'Pizza Margherita', 'Classic pizza with tomato sauce and mozzarella', 'Pizza dough, tomato sauce, mozzarella, basil', 16.99, 'images/main/pizza.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(21, 7, 'Chocolate Cake', 'Rich chocolate cake with chocolate ganache', 'Chocolate, flour, eggs, cream', 7.99, 'images/desserts/chocolate-cake.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(22, 7, 'Tiramisu', 'Classic Italian tiramisu with coffee flavor', 'Mascarpone, coffee, ladyfingers, cocoa', 8.99, 'images/desserts/tiramisu.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(23, 8, 'Soda', 'Fresh soda with ice and lemon', 'Soda, ice, lemon', 3.99, 'images/beverages/soda.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(24, 8, 'Fresh Juice', 'Freshly squeezed orange juice', 'Oranges', 4.99, 'images/beverages/juice.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(25, 9, 'Spring Rolls', 'Quick vegetable spring rolls', 'Vegetables, wrappers, chili sauce', 8.99, 'images/appetizers/spring-rolls.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(26, 9, 'Chicken Wings', 'Crispy chicken wings with BBQ sauce', 'Chicken wings, BBQ sauce', 9.99, 'images/appetizers/wings.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(27, 10, 'Grilled Salmon', 'Fresh Atlantic salmon with lemon butter sauce', 'Salmon, lemon, butter, herbs', 24.99, 'images/main/salmon.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(28, 10, 'Chicken Sandwich', 'Grilled chicken sandwich with fries', 'Chicken breast, bread, lettuce, tomato, fries', 14.99, 'images/main/sandwich.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(29, 11, 'Chocolate Cake', 'Rich chocolate cake with chocolate ganache', 'Chocolate, flour, eggs, cream', 7.99, 'images/desserts/chocolate-cake.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(30, 12, 'Soda', 'Fresh soda with ice and lemon', 'Soda, ice, lemon', 3.99, 'images/beverages/soda.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(31, 12, 'Coffee', 'Fresh brewed coffee', 'Coffee beans, water', 2.99, 'images/beverages/coffee.jpg', '', 1, 0, NULL, NULL, NULL, 0, '2025-08-14 23:29:01', '2025-08-14 23:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `table_id` int(11) DEFAULT NULL,
  `qr_code_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `order_type` enum('dine_in','takeaway','delivery') DEFAULT 'dine_in',
  `status` enum('pending','confirmed','preparing','ready','delivered','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `final_amount` decimal(10,2) NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `estimated_delivery_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `branch_id`, `table_id`, `qr_code_id`, `customer_name`, `customer_phone`, `customer_email`, `order_type`, `status`, `total_amount`, `discount_amount`, `tax_amount`, `final_amount`, `special_instructions`, `estimated_delivery_time`, `created_at`, `updated_at`) VALUES
(1, 4, 'ORD-2024-001', 1, 2, 2, NULL, NULL, NULL, 'dine_in', '', 45.97, 0.00, 0.00, 0.00, 'Extra spicy please', NULL, '2025-08-14 21:29:01', '2025-08-14 22:29:01'),
(2, 4, 'ORD-2024-002', 1, 4, 4, NULL, NULL, NULL, 'dine_in', '', 67.98, 0.00, 0.00, 0.00, 'No onions', NULL, '2025-08-14 20:29:01', '2025-08-14 21:29:01'),
(3, NULL, 'ORD-2024-003', 2, 12, 14, NULL, NULL, NULL, 'dine_in', 'pending', 32.98, 0.00, 0.00, 0.00, NULL, NULL, '2025-08-14 22:59:01', '2025-08-14 22:59:01'),
(4, NULL, 'ORD-2024-004', 3, 20, 20, NULL, NULL, NULL, 'dine_in', 'preparing', 24.99, 0.00, 0.00, 0.00, 'Quick service please', NULL, '2025-08-14 23:14:01', '2025-08-14 23:19:01'),
(5, 4, 'ORD-2024-005', 1, 6, 6, NULL, NULL, NULL, 'dine_in', 'delivered', 89.95, 0.00, 0.00, 0.00, 'Birthday celebration', NULL, '2025-08-14 19:29:01', '2025-08-14 20:29:01'),
(6, NULL, 'ORD202508161244198880', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 12:44:19', '2025-08-16 12:44:19'),
(7, NULL, 'ORD202508161245123618', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 12:45:12', '2025-08-16 12:45:12'),
(8, NULL, 'ORD202508161245428513', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'takeaway', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 12:45:42', '2025-08-16 12:45:42'),
(9, NULL, 'ORD202508161255502646', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 12:55:50', '2025-08-16 12:55:50'),
(10, NULL, 'ORD202508161256456552', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 12:56:45', '2025-08-16 12:56:45'),
(11, NULL, 'ORD202508161304149050', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 13:04:14', '2025-08-16 13:04:14'),
(12, NULL, 'ORD202508161304378847', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 13:04:37', '2025-08-16 13:04:37'),
(13, NULL, 'ORD202508161313521845', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 9.99, 0.00, 0.85, 10.84, '', NULL, '2025-08-16 13:13:52', '2025-08-16 13:13:52'),
(14, NULL, 'ORD202508161318312002', 1, 1, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'delivery', 'pending', 9.99, 0.00, 0.85, 10.84, 'cd', NULL, '2025-08-16 13:18:31', '2025-08-16 13:18:31'),
(15, NULL, 'ORD202508161357348695', 3, NULL, NULL, 'Jim Kwik', 's33', 'ss@gg.com', 'dine_in', 'pending', 21.46, 0.00, 1.82, 23.28, '', NULL, '2025-08-16 13:57:34', '2025-08-16 13:57:34'),
(16, NULL, 'ORD202508161358367195', 1, NULL, NULL, 'Jim Kwik', 's33', 'ss@gg.com', 'dine_in', 'pending', 40.93, 0.00, 3.48, 44.41, '', NULL, '2025-08-16 13:58:36', '2025-08-16 13:58:36'),
(17, NULL, 'ORD202508161359115868', 2, NULL, NULL, 'Jim Kwik', '444', 'ss@gg.com', 'dine_in', 'pending', 49.92, 0.00, 4.24, 54.16, '', NULL, '2025-08-16 13:59:11', '2025-08-16 13:59:11'),
(18, NULL, 'ORD202508161416185109', 3, NULL, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 28.97, 0.00, 2.46, 31.43, '', NULL, '2025-08-16 14:16:18', '2025-08-16 14:16:18'),
(19, NULL, 'ORD202508161418035691', 2, NULL, NULL, 'Jim Kwik', 's33', 'ss@gg.com', 'dine_in', 'pending', 31.98, 0.00, 2.72, 34.70, '', NULL, '2025-08-16 14:18:03', '2025-08-16 14:18:03'),
(20, NULL, 'ORD202508161429456766', 1, NULL, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 23.95, 0.00, 2.04, 25.99, '', NULL, '2025-08-16 14:29:45', '2025-08-16 14:29:45'),
(21, NULL, 'ORD202508161430233661', 1, NULL, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 11.98, 0.00, 1.02, 13.00, '', NULL, '2025-08-16 14:30:23', '2025-08-16 14:30:23'),
(22, NULL, 'ORD202508161431141412', 3, NULL, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 33.98, 0.00, 2.89, 36.87, '', NULL, '2025-08-16 14:31:14', '2025-08-16 14:31:14'),
(23, NULL, 'ORD202508161431565361', 3, NULL, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 14:31:56', '2025-08-16 14:31:56'),
(24, NULL, 'ORD202508161432372605', 3, NULL, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 12.98, 0.00, 1.10, 14.08, '', NULL, '2025-08-16 14:32:37', '2025-08-16 14:32:37'),
(27, NULL, 'ORD202508161455522376', 1, 2, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 18.98, 0.00, 1.61, 20.59, '', NULL, '2025-08-16 14:55:52', '2025-08-16 14:55:52'),
(28, NULL, 'ORD202508161457316589', 1, NULL, NULL, NULL, NULL, NULL, 'takeaway', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 14:57:31', '2025-08-16 14:57:31'),
(29, NULL, 'ORD202508161458118285', 1, NULL, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 14:58:11', '2025-08-16 14:58:11'),
(32, NULL, 'ORD202508161459411220', 1, 4, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 14:59:41', '2025-08-16 14:59:41'),
(33, NULL, 'ORD202508161519258847', 1, NULL, NULL, NULL, NULL, NULL, 'takeaway', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 15:19:25', '2025-08-16 15:19:25'),
(34, NULL, 'ORD202508161519463979', 1, 5, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 15:19:46', '2025-08-16 15:19:46'),
(40, NULL, 'ORD202508161528495147', 1, 3, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 5.98, 0.00, 0.51, 6.49, '', NULL, '2025-08-16 15:28:49', '2025-08-16 15:28:49'),
(42, NULL, 'ORD202508161539013488', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 2.99, 0.00, 0.25, 3.24, '', NULL, '2025-08-16 15:39:01', '2025-08-16 15:39:01'),
(43, NULL, 'ORD202508161539347311', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 6.98, 0.00, 0.59, 7.57, '', NULL, '2025-08-16 15:39:34', '2025-08-16 15:39:34'),
(44, NULL, 'ORD202508161540018810', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 2.99, 0.00, 0.25, 3.24, '', NULL, '2025-08-16 15:40:01', '2025-08-16 15:40:01'),
(45, NULL, 'ORD202508161540312609', 1, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 15:40:31', '2025-08-16 15:40:31'),
(46, NULL, 'ORD202508161549042720', 3, 19, NULL, 'Jim Kwik', '12', 'ss@gg.com', 'dine_in', 'pending', 2.99, 0.00, 0.25, 3.24, '', NULL, '2025-08-16 15:49:04', '2025-08-16 15:49:04'),
(47, NULL, 'ORD202508161559439883', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 2.99, 0.00, 0.25, 3.24, '', NULL, '2025-08-16 15:59:43', '2025-08-16 15:59:43'),
(48, NULL, 'ORD202508161600178621', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 2.99, 0.00, 0.25, 3.24, '', NULL, '2025-08-16 16:00:17', '2025-08-16 16:00:17'),
(49, NULL, 'ORD202508161606424135', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 2.99, 0.00, 0.25, 3.24, '', NULL, '2025-08-16 16:06:42', '2025-08-16 16:06:42'),
(50, NULL, 'ORD202508161607008831', 3, NULL, NULL, 'Jim Kwik', '12', NULL, 'takeaway', 'pending', 2.99, 0.00, 0.25, 3.24, '', NULL, '2025-08-16 16:07:00', '2025-08-16 16:07:00'),
(51, NULL, 'ORD202508161701117130', 1, NULL, NULL, NULL, NULL, NULL, 'takeaway', 'pending', 7.98, 0.00, 0.68, 8.66, '', NULL, '2025-08-16 17:01:11', '2025-08-16 17:01:11'),
(52, NULL, 'ORD202508161717215368', 1, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 17:17:21', '2025-08-16 17:17:21'),
(53, NULL, 'ORD202508161728292096', 1, NULL, NULL, 'Jim Kwik', '12', NULL, 'dine_in', 'pending', 8.99, 0.00, 0.76, 9.75, '', NULL, '2025-08-16 17:28:29', '2025-08-16 17:28:29'),
(54, NULL, 'ORD202508161921036133', 1, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 13.98, 0.00, 1.19, 15.17, '', NULL, '2025-08-16 19:21:03', '2025-08-16 19:21:03'),
(55, NULL, 'ORD202508161933064703', 1, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-16 19:33:06', '2025-08-16 19:33:06'),
(56, NULL, 'ORD202508162124239917', 2, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 22.96, 0.00, 1.95, 24.91, '', NULL, '2025-08-16 21:24:23', '2025-08-16 21:24:23'),
(57, NULL, 'ORD202508162156026596', 2, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 145.90, 0.00, 12.40, 158.30, '', NULL, '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(58, NULL, 'ORD202508170715585531', 3, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 103.89, 0.00, 8.83, 112.72, '', NULL, '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(59, NULL, 'ORD202508170754401375', 1, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 95.91, 0.00, 8.15, 104.06, '', NULL, '2025-08-17 07:54:40', '2025-08-17 07:54:40'),
(60, NULL, 'ORD202508170819312701', 1, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 55.96, 0.00, 4.76, 60.72, '', NULL, '2025-08-17 08:19:31', '2025-08-17 08:19:31'),
(61, NULL, 'ORD202508170819496672', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 18.98, 0.00, 1.61, 20.59, '', NULL, '2025-08-17 08:19:49', '2025-08-17 08:19:49'),
(62, NULL, 'ORD202508170831137109', 1, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 3.99, 0.00, 0.34, 4.33, '', NULL, '2025-08-17 08:31:13', '2025-08-17 08:31:13'),
(63, NULL, 'ORD202508170934204580', 3, 23, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 21.97, 0.00, 1.87, 23.84, '', NULL, '2025-08-17 09:34:20', '2025-08-17 09:34:20'),
(64, NULL, 'ORD202508171004171671', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 8.99, 0.00, 0.76, 9.75, '', NULL, '2025-08-17 10:04:17', '2025-08-17 10:04:17'),
(65, NULL, 'ORD202508171026483523', 3, 19, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 63.92, 0.00, 5.43, 69.35, '', NULL, '2025-08-17 10:26:48', '2025-08-17 10:26:48'),
(66, NULL, 'ORD202508171103498414', 1, NULL, NULL, NULL, NULL, NULL, 'dine_in', 'pending', 19.98, 0.00, 1.70, 21.68, '', NULL, '2025-08-17 11:03:49', '2025-08-17 11:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `status` enum('pending','preparing','ready','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `price`, `quantity`, `unit_price`, `total_price`, `special_instructions`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 8.99, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 21:29:01', '2025-08-14 22:29:01'),
(2, 1, 5, 24.99, 1, 0.00, 0.00, 'Well done', 'pending', '2025-08-14 21:29:01', '2025-08-14 22:29:01'),
(3, 1, 9, 7.99, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 21:29:01', '2025-08-14 22:29:01'),
(4, 1, 12, 4.00, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 21:29:01', '2025-08-14 22:29:01'),
(5, 2, 2, 6.99, 2, 0.00, 0.00, NULL, 'pending', '2025-08-14 20:29:01', '2025-08-14 21:29:01'),
(6, 2, 6, 18.99, 1, 0.00, 0.00, 'Extra cheese', 'pending', '2025-08-14 20:29:01', '2025-08-14 21:29:01'),
(7, 2, 10, 28.99, 1, 0.00, 0.00, 'Medium rare', 'pending', '2025-08-14 20:29:01', '2025-08-14 21:29:01'),
(8, 2, 11, 5.99, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 20:29:01', '2025-08-14 21:29:01'),
(9, 3, 13, 8.99, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 22:59:01', '2025-08-14 22:59:01'),
(10, 3, 17, 24.99, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 22:59:01', '2025-08-14 22:59:01'),
(11, 4, 19, 8.99, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 23:14:01', '2025-08-14 23:19:01'),
(12, 4, 22, 16.99, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 23:14:01', '2025-08-14 23:19:01'),
(13, 5, 1, 8.99, 2, 0.00, 0.00, NULL, 'pending', '2025-08-14 19:29:01', '2025-08-14 20:29:01'),
(14, 5, 2, 6.99, 2, 0.00, 0.00, NULL, 'pending', '2025-08-14 19:29:01', '2025-08-14 20:29:01'),
(15, 5, 5, 24.99, 2, 0.00, 0.00, NULL, 'pending', '2025-08-14 19:29:01', '2025-08-14 20:29:01'),
(16, 5, 6, 18.99, 1, 0.00, 0.00, NULL, 'pending', '2025-08-14 19:29:01', '2025-08-14 20:29:01'),
(17, 5, 9, 7.99, 2, 0.00, 0.00, NULL, 'pending', '2025-08-14 19:29:01', '2025-08-14 20:29:01'),
(18, 6, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 12:44:19', '2025-08-16 12:44:19'),
(19, 7, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 12:45:12', '2025-08-16 12:45:12'),
(20, 8, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 12:45:42', '2025-08-16 12:45:42'),
(21, 9, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 12:55:50', '2025-08-16 12:55:50'),
(22, 10, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 12:56:45', '2025-08-16 12:56:45'),
(23, 11, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 13:04:14', '2025-08-16 13:04:14'),
(24, 12, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 13:04:37', '2025-08-16 13:04:37'),
(25, 13, 3, 0.00, 1, 9.99, 9.99, NULL, 'pending', '2025-08-16 13:13:52', '2025-08-16 13:13:52'),
(26, 14, 3, 0.00, 1, 9.99, 9.99, NULL, 'pending', '2025-08-16 13:18:31', '2025-08-16 13:18:31'),
(27, 15, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 13:57:34', '2025-08-16 13:57:34'),
(28, 15, 14, 0.00, 1, 3.49, 3.49, NULL, 'pending', '2025-08-16 13:57:34', '2025-08-16 13:57:34'),
(29, 15, 12, 0.00, 1, 4.99, 4.99, NULL, 'pending', '2025-08-16 13:57:34', '2025-08-16 13:57:34'),
(30, 15, 25, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 13:57:34', '2025-08-16 13:57:34'),
(31, 16, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 13:58:36', '2025-08-16 13:58:36'),
(32, 16, 14, 0.00, 2, 3.49, 6.98, NULL, 'pending', '2025-08-16 13:58:36', '2025-08-16 13:58:36'),
(33, 16, 12, 0.00, 1, 4.99, 4.99, NULL, 'pending', '2025-08-16 13:58:36', '2025-08-16 13:58:36'),
(34, 16, 25, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 13:58:36', '2025-08-16 13:58:36'),
(35, 16, 1, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 13:58:36', '2025-08-16 13:58:36'),
(36, 16, 2, 0.00, 1, 6.99, 6.99, NULL, 'pending', '2025-08-16 13:58:36', '2025-08-16 13:58:36'),
(37, 17, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 13:59:11', '2025-08-16 13:59:11'),
(38, 17, 14, 0.00, 2, 3.49, 6.98, NULL, 'pending', '2025-08-16 13:59:11', '2025-08-16 13:59:11'),
(39, 17, 12, 0.00, 1, 4.99, 4.99, NULL, 'pending', '2025-08-16 13:59:11', '2025-08-16 13:59:11'),
(40, 17, 25, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 13:59:11', '2025-08-16 13:59:11'),
(41, 17, 1, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 13:59:11', '2025-08-16 13:59:11'),
(42, 17, 2, 0.00, 1, 6.99, 6.99, NULL, 'pending', '2025-08-16 13:59:11', '2025-08-16 13:59:11'),
(43, 17, 15, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 13:59:11', '2025-08-16 13:59:11'),
(44, 18, 3, 0.00, 2, 9.99, 19.98, NULL, 'pending', '2025-08-16 14:16:18', '2025-08-16 14:16:18'),
(45, 18, 25, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 14:16:18', '2025-08-16 14:16:18'),
(46, 19, 18, 0.00, 1, 24.99, 24.99, NULL, 'pending', '2025-08-16 14:18:03', '2025-08-16 14:18:03'),
(47, 19, 16, 0.00, 1, 6.99, 6.99, NULL, 'pending', '2025-08-16 14:18:03', '2025-08-16 14:18:03'),
(48, 20, 8, 0.00, 1, 7.99, 7.99, NULL, 'pending', '2025-08-16 14:29:45', '2025-08-16 14:29:45'),
(49, 20, 11, 0.00, 4, 3.99, 15.96, NULL, 'pending', '2025-08-16 14:29:45', '2025-08-16 14:29:45'),
(50, 21, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 14:30:23', '2025-08-16 14:30:23'),
(51, 21, 8, 0.00, 1, 7.99, 7.99, NULL, 'pending', '2025-08-16 14:30:23', '2025-08-16 14:30:23'),
(52, 22, 25, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 14:31:14', '2025-08-16 14:31:14'),
(53, 22, 27, 0.00, 1, 24.99, 24.99, NULL, 'pending', '2025-08-16 14:31:14', '2025-08-16 14:31:14'),
(54, 23, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 14:31:56', '2025-08-16 14:31:56'),
(55, 24, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 14:32:37', '2025-08-16 14:32:37'),
(56, 24, 25, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 14:32:37', '2025-08-16 14:32:37'),
(57, 27, 1, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 14:55:52', '2025-08-16 14:55:52'),
(58, 27, 3, 0.00, 1, 9.99, 9.99, NULL, 'pending', '2025-08-16 14:55:52', '2025-08-16 14:55:52'),
(59, 28, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 14:57:31', '2025-08-16 14:57:31'),
(60, 29, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 14:58:11', '2025-08-16 14:58:11'),
(61, 32, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 14:59:41', '2025-08-16 14:59:41'),
(62, 33, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 15:19:25', '2025-08-16 15:19:25'),
(63, 34, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 15:19:46', '2025-08-16 15:19:46'),
(64, 40, 31, 0.00, 2, 2.99, 5.98, NULL, 'pending', '2025-08-16 15:28:49', '2025-08-16 15:28:49'),
(65, 42, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-16 15:39:01', '2025-08-16 15:39:01'),
(66, 43, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 15:39:34', '2025-08-16 15:39:34'),
(67, 43, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-16 15:39:34', '2025-08-16 15:39:34'),
(68, 44, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-16 15:40:01', '2025-08-16 15:40:01'),
(69, 45, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 15:40:31', '2025-08-16 15:40:31'),
(70, 46, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-16 15:49:04', '2025-08-16 15:49:04'),
(71, 47, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-16 15:59:43', '2025-08-16 15:59:43'),
(72, 48, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-16 16:00:17', '2025-08-16 16:00:17'),
(73, 49, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-16 16:06:42', '2025-08-16 16:06:42'),
(74, 50, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-16 16:07:00', '2025-08-16 16:07:00'),
(75, 51, 11, 0.00, 2, 3.99, 7.98, NULL, 'pending', '2025-08-16 17:01:11', '2025-08-16 17:01:11'),
(76, 52, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 17:17:21', '2025-08-16 17:17:21'),
(77, 53, 1, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 17:28:29', '2025-08-16 17:28:29'),
(78, 54, 3, 0.00, 1, 9.99, 9.99, NULL, 'pending', '2025-08-16 19:21:03', '2025-08-16 19:21:03'),
(79, 54, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 19:21:03', '2025-08-16 19:21:03'),
(80, 55, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 19:33:06', '2025-08-16 19:33:06'),
(81, 56, 10, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 21:24:23', '2025-08-16 21:24:23'),
(82, 56, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-16 21:24:23', '2025-08-16 21:24:23'),
(83, 56, 12, 0.00, 2, 4.99, 9.98, NULL, 'pending', '2025-08-16 21:24:23', '2025-08-16 21:24:23'),
(84, 57, 22, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(85, 57, 21, 0.00, 1, 7.99, 7.99, NULL, 'pending', '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(86, 57, 16, 0.00, 1, 6.99, 6.99, NULL, 'pending', '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(87, 57, 17, 0.00, 1, 7.99, 7.99, NULL, 'pending', '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(88, 57, 15, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(89, 57, 18, 0.00, 2, 24.99, 49.98, NULL, 'pending', '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(90, 57, 19, 0.00, 2, 18.99, 37.98, NULL, 'pending', '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(91, 57, 20, 0.00, 1, 16.99, 16.99, NULL, 'pending', '2025-08-16 21:56:02', '2025-08-16 21:56:02'),
(92, 58, 3, 0.00, 1, 9.99, 9.99, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(93, 58, 2, 0.00, 1, 6.99, 6.99, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(94, 58, 1, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(95, 58, 27, 0.00, 1, 24.99, 24.99, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(96, 58, 26, 0.00, 2, 9.99, 19.98, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(97, 58, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(98, 58, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(99, 58, 25, 0.00, 2, 8.99, 17.98, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(100, 58, 29, 0.00, 1, 7.99, 7.99, NULL, 'pending', '2025-08-17 07:15:58', '2025-08-17 07:15:58'),
(101, 59, 29, 0.00, 1, 7.99, 7.99, NULL, 'pending', '2025-08-17 07:54:40', '2025-08-17 07:54:40'),
(102, 59, 26, 0.00, 1, 9.99, 9.99, NULL, 'pending', '2025-08-17 07:54:40', '2025-08-17 07:54:40'),
(103, 59, 28, 0.00, 2, 14.99, 29.98, NULL, 'pending', '2025-08-17 07:54:40', '2025-08-17 07:54:40'),
(104, 59, 25, 0.00, 2, 8.99, 17.98, NULL, 'pending', '2025-08-17 07:54:40', '2025-08-17 07:54:40'),
(105, 59, 3, 0.00, 3, 9.99, 29.97, NULL, 'pending', '2025-08-17 07:54:40', '2025-08-17 07:54:40'),
(106, 60, 6, 0.00, 1, 28.99, 28.99, NULL, 'pending', '2025-08-17 08:19:31', '2025-08-17 08:19:31'),
(107, 60, 3, 0.00, 2, 9.99, 19.98, NULL, 'pending', '2025-08-17 08:19:31', '2025-08-17 08:19:31'),
(108, 60, 2, 0.00, 1, 6.99, 6.99, NULL, 'pending', '2025-08-17 08:19:31', '2025-08-17 08:19:31'),
(109, 61, 28, 0.00, 1, 14.99, 14.99, NULL, 'pending', '2025-08-17 08:19:49', '2025-08-17 08:19:49'),
(110, 61, 30, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-17 08:19:49', '2025-08-17 08:19:49'),
(111, 62, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-17 08:31:13', '2025-08-17 08:31:13'),
(112, 63, 31, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-17 09:34:20', '2025-08-17 09:34:20'),
(113, 63, 25, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-17 09:34:20', '2025-08-17 09:34:20'),
(114, 63, 26, 0.00, 1, 9.99, 9.99, NULL, 'pending', '2025-08-17 09:34:20', '2025-08-17 09:34:20'),
(115, 64, 25, 0.00, 1, 8.99, 8.99, NULL, 'pending', '2025-08-17 10:04:17', '2025-08-17 10:04:17'),
(116, 65, 3, 0.00, 3, 9.99, 29.97, NULL, 'pending', '2025-08-17 10:26:48', '2025-08-17 10:26:48'),
(117, 65, 1, 0.00, 3, 8.99, 26.97, NULL, 'pending', '2025-08-17 10:26:48', '2025-08-17 10:26:48'),
(118, 65, 11, 0.00, 1, 3.99, 3.99, NULL, 'pending', '2025-08-17 10:26:48', '2025-08-17 10:26:48'),
(119, 65, 13, 0.00, 1, 2.99, 2.99, NULL, 'pending', '2025-08-17 10:26:48', '2025-08-17 10:26:48'),
(120, 66, 3, 0.00, 2, 9.99, 19.98, NULL, 'pending', '2025-08-17 11:03:49', '2025-08-17 11:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','preparing','ready','delivered','cancelled') NOT NULL,
  `changed_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `status`, `changed_by`, `notes`, `created_at`) VALUES
(1, 6, 'pending', 1, 'Order created successfully', '2025-08-16 12:44:19'),
(2, 7, 'pending', 1, 'Order created successfully', '2025-08-16 12:45:12'),
(3, 8, 'pending', 1, 'Order created successfully', '2025-08-16 12:45:42'),
(4, 9, 'pending', 1, 'Order created successfully', '2025-08-16 12:55:50'),
(5, 10, 'pending', 1, 'Order created successfully', '2025-08-16 12:56:45'),
(6, 11, 'pending', 1, 'Order created successfully', '2025-08-16 13:04:14'),
(7, 12, 'pending', 1, 'Order created successfully', '2025-08-16 13:04:37'),
(8, 13, 'pending', 1, 'Order created successfully', '2025-08-16 13:13:52'),
(9, 14, 'pending', 1, 'Order created successfully', '2025-08-16 13:18:31'),
(10, 15, 'pending', 1, 'Order created successfully', '2025-08-16 13:57:34'),
(11, 16, 'pending', 1, 'Order created successfully', '2025-08-16 13:58:36'),
(12, 17, 'pending', 1, 'Order created successfully', '2025-08-16 13:59:11'),
(13, 18, 'pending', 1, 'Order created successfully', '2025-08-16 14:16:18'),
(14, 19, 'pending', 1, 'Order created successfully', '2025-08-16 14:18:03'),
(15, 20, 'pending', 1, 'Order created successfully', '2025-08-16 14:29:45'),
(16, 21, 'pending', 1, 'Order created successfully', '2025-08-16 14:30:23'),
(17, 22, 'pending', 1, 'Order created successfully', '2025-08-16 14:31:14'),
(18, 23, 'pending', 1, 'Order created successfully', '2025-08-16 14:31:56'),
(19, 24, 'pending', 1, 'Order created successfully', '2025-08-16 14:32:37'),
(20, 27, 'pending', 1, 'Order created successfully', '2025-08-16 14:55:52'),
(21, 28, 'pending', 1, 'Order created successfully', '2025-08-16 14:57:31'),
(22, 29, 'pending', 1, 'Order created successfully', '2025-08-16 14:58:11'),
(23, 32, 'pending', 1, 'Order created successfully', '2025-08-16 14:59:41'),
(24, 33, 'pending', 1, 'Order created successfully', '2025-08-16 15:19:25'),
(25, 34, 'pending', 1, 'Order created successfully', '2025-08-16 15:19:46');

-- --------------------------------------------------------

--
-- Table structure for table `qr_codes`
--

CREATE TABLE `qr_codes` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `qr_code` varchar(255) NOT NULL,
  `qr_image_url` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qr_codes`
--

INSERT INTO `qr_codes` (`id`, `branch_id`, `table_id`, `qr_code`, `qr_image_url`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'RESTO-B1-T001', 'qrcodes/RESTO-B1-T001.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(2, 1, 2, 'RESTO-B1-T002', 'qrcodes/RESTO-B1-T002.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(3, 1, 3, 'RESTO-B1-T003', 'qrcodes/RESTO-B1-T003.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(4, 1, 4, 'RESTO-B1-T004', 'qrcodes/RESTO-B1-T004.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(5, 1, 5, 'RESTO-B1-T005', 'qrcodes/RESTO-B1-T005.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(6, 1, 6, 'RESTO-B1-T006', 'qrcodes/RESTO-B1-T006.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(7, 1, 7, 'RESTO-B1-T007', 'qrcodes/RESTO-B1-T007.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(8, 1, 8, 'RESTO-B1-T008', 'qrcodes/RESTO-B1-T008.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(9, 1, 9, 'RESTO-B1-T009', 'qrcodes/RESTO-B1-T009.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(10, 1, 10, 'RESTO-B1-T010', 'qrcodes/RESTO-B1-T010.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(11, 2, 11, 'RESTO-B2-M001', 'qrcodes/RESTO-B2-M001.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(12, 2, 12, 'RESTO-B2-M002', 'qrcodes/RESTO-B2-M002.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(13, 2, 13, 'RESTO-B2-M003', 'qrcodes/RESTO-B2-M003.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(14, 2, 14, 'RESTO-B2-M004', 'qrcodes/RESTO-B2-M004.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(15, 2, 15, 'RESTO-B2-M005', 'qrcodes/RESTO-B2-M005.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(16, 2, 16, 'RESTO-B2-M006', 'qrcodes/RESTO-B2-M006.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(17, 2, 17, 'RESTO-B2-M007', 'qrcodes/RESTO-B2-M007.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(18, 2, 18, 'RESTO-B2-M008', 'qrcodes/RESTO-B2-M008.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(19, 3, 19, 'RESTO-B3-A001', 'qrcodes/RESTO-B3-A001.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(20, 3, 20, 'RESTO-B3-A002', 'qrcodes/RESTO-B3-A002.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(21, 3, 21, 'RESTO-B3-A003', 'qrcodes/RESTO-B3-A003.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(22, 3, 22, 'RESTO-B3-A004', 'qrcodes/RESTO-B3-A004.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(23, 3, 23, 'RESTO-B3-A005', 'qrcodes/RESTO-B3-A005.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(24, 3, 24, 'RESTO-B3-A006', 'qrcodes/RESTO-B3-A006.png', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Restaurant Menu System', 'string', 'System name', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(2, 'site_description', 'QR Code Restaurant Menu System', 'string', 'System description', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(3, 'admin_email', 'admin@restaurant.com', 'string', 'Admin email address', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(4, 'currency', 'USD', 'string', 'Default currency', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(5, 'tax_rate', '8.5', 'number', 'Default tax rate percentage', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(6, 'order_prefix', 'ORD', 'string', 'Order number prefix', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(7, 'qr_code_size', '300', 'number', 'QR code image size', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(8, 'session_timeout', '3600', 'number', 'Session timeout in seconds', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(9, 'max_login_attempts', '5', 'number', 'Maximum login attempts before lockout', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(10, 'lockout_duration', '900', 'number', 'Account lockout duration in seconds', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(11, 'enable_notifications', '1', 'boolean', 'Enable system notifications', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43'),
(12, 'default_theme', 'default', 'string', 'Default theme directory name', 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'Restaurant Menu System', 'string', 'Application name', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43'),
(2, 'app_version', '1.0.0', 'string', 'Application version', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43'),
(3, 'currency', 'USD', 'string', 'Default currency', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43'),
(4, 'tax_rate', '8.5', 'string', 'Tax rate percentage', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43'),
(5, 'service_charge', '10', 'string', 'Service charge percentage', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43'),
(6, 'max_login_attempts', '5', 'string', 'Maximum login attempts before lockout', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43'),
(7, 'lockout_duration', '15', 'string', 'Account lockout duration in minutes', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43'),
(8, 'qr_code_expiry', '24', 'string', 'QR code expiry in hours', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43'),
(9, 'order_timeout', '30', 'string', 'Order timeout in minutes', 0, '2025-08-14 23:30:43', '2025-08-14 23:30:43');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `table_number` varchar(20) NOT NULL,
  `capacity` int(11) DEFAULT 4,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('available','occupied','reserved') DEFAULT 'available',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `branch_id`, `table_number`, `capacity`, `location`, `status`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'T001', 2, 'Near Window', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(2, 1, 'T002', 4, 'Center', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(3, 1, 'T003', 4, 'Near Window', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(4, 1, 'T004', 6, 'Center', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(5, 1, 'T005', 6, 'Private Area', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(6, 1, 'T006', 8, 'Private Area', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(7, 1, 'T007', 2, 'Bar Area', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(8, 1, 'T008', 4, 'Bar Area', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(9, 1, 'T009', 4, 'Outdoor', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(10, 1, 'T010', 6, 'Outdoor', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(11, 2, 'M001', 2, 'Food Court', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(12, 2, 'M002', 4, 'Food Court', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(13, 2, 'M003', 4, 'Food Court', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(14, 2, 'M004', 6, 'Food Court', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(15, 2, 'M005', 6, 'Quiet Area', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(16, 2, 'M006', 8, 'Quiet Area', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(17, 2, 'M007', 2, 'Food Court', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(18, 2, 'M008', 4, 'Food Court', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(19, 3, 'A001', 2, 'Terminal A', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(20, 3, 'A002', 4, 'Terminal A', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(21, 3, 'A003', 4, 'Terminal A', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(22, 3, 'A004', 6, 'Terminal B', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(23, 3, 'A005', 6, 'Terminal B', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(24, 3, 'A006', 8, 'Terminal B', 'available', 1, '2025-08-14 23:29:01', '2025-08-14 23:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `themes`
--

CREATE TABLE `themes` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `version` varchar(20) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `directory_name` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `themes`
--

INSERT INTO `themes` (`id`, `name`, `description`, `version`, `author`, `directory_name`, `is_active`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Default Theme', 'Default system theme', '1.0.0', 'System', 'default', 1, 1, '2025-08-14 23:14:43', '2025-08-14 23:14:43');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('super_admin','branch_manager','chef','waiter','staff') NOT NULL,
  `status` enum('active','inactive','pending') DEFAULT 'active',
  `branch_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `address`, `role`, `status`, `branch_id`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', '+1-555-0001', '123 Admin Street, Downtown', 'super_admin', 'active', NULL, 1, NULL, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(2, 'manager', 'manager@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Branch Manager', '+1-555-0002', '456 Manager Ave, Downtown', 'branch_manager', 'active', NULL, 1, NULL, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(3, 'staff', 'staff@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Member', '+1-555-0003', '789 Staff Blvd, Downtown', 'staff', 'active', NULL, 1, NULL, '2025-08-14 23:29:01', '2025-08-14 23:29:01'),
(4, 'john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '+1-555-0101', '321 Customer St', '', 'active', NULL, 1, NULL, '2025-08-14 23:29:01', '2025-08-14 23:29:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_logs_user` (`user_id`),
  ADD KEY `idx_activity_logs_created_at` (`created_at`);

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `directory_name` (`directory_name`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_menu_categories_branch` (`branch_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_menu_items_category` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `idx_orders_branch_status` (`branch_id`,`status`),
  ADD KEY `idx_orders_created_at` (`created_at`),
  ADD KEY `fk_orders_user_id` (`user_id`),
  ADD KEY `fk_orders_qr_code_id` (`qr_code_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_item_id` (`menu_item_id`),
  ADD KEY `idx_order_items_order_id` (`order_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qr_code` (`qr_code`),
  ADD UNIQUE KEY `unique_qr_branch_table` (`branch_id`,`table_id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `idx_qr_codes_branch` (`branch_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_table_branch` (`branch_id`,`table_number`),
  ADD KEY `idx_tables_branch` (`branch_id`);

--
-- Indexes for table `themes`
--
ALTER TABLE `themes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `directory_name` (`directory_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_branch_role` (`branch_id`,`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `menu_categories`
--
ALTER TABLE `menu_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `qr_codes`
--
ALTER TABLE `qr_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `themes`
--
ALTER TABLE `themes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `menu_categories`
--
ALTER TABLE `menu_categories`
  ADD CONSTRAINT `menu_categories_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `menu_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_qr_code_id` FOREIGN KEY (`qr_code_id`) REFERENCES `qr_codes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `qr_codes`
--
ALTER TABLE `qr_codes`
  ADD CONSTRAINT `qr_codes_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `qr_codes_ibfk_2` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sessions`
--
ALTER TABLE `sessions`
  ADD CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tables`
--
ALTER TABLE `tables`
  ADD CONSTRAINT `tables_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
