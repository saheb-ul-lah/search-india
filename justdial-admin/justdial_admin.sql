
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `entity_type` varchar(50) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'update', 'categories', 1, 'Updated category: Restaurants', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-29 17:44:41'),
(2, 1, 'update', 'categories', 1, 'Updated category: Restaurants', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-03-29 17:44:57'),
(3, 1, 'create', 'categories', 11, 'Created category: Phucka', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 07:31:33'),
(4, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:09:44'),
(5, 1, 'update', 'categories', 11, 'Updated category: Phucka1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:14:40'),
(6, 1, 'update', 'categories', 11, 'Updated category: Phucka', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:19:44'),
(7, 1, 'update', 'categories', 11, 'Updated category: Phucka', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:20:11'),
(8, 1, 'update', 'categories', 11, 'Updated category: Phucka1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:20:25'),
(9, 1, 'update', 'categories', 11, 'Updated category: Phucka', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:21:05'),
(10, 1, 'update', 'categories', 11, 'Updated category: Phucka', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:21:37'),
(11, 1, 'update', 'categories', 11, 'Updated category: Phucka', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:22:09'),
(12, 1, 'update', 'categories', 11, 'Updated category: Phucka1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:22:18'),
(13, 1, 'update', 'categories', 11, 'Updated category: Phucka1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 14:22:40'),
(14, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 16:28:54'),
(15, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 16:41:05'),
(16, 2, 'register', 'users', 2, 'User registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 17:36:43'),
(17, 3, 'register', 'users', 3, 'User registered', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 17:37:15'),
(18, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 17:37:42'),
(19, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 17:37:50'),
(20, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 17:40:49'),
(21, 1, 'update', 'settings', NULL, 'Updated system settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-01 17:50:31'),
(22, 2, 'login', 'users', 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 03:15:05'),
(23, 2, 'logout', 'users', 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 03:16:50'),
(24, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 03:16:58'),
(25, 1, 'create', 'businesses', 1, 'Created business: Devplexity', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 05:08:54'),
(26, 1, 'update', 'businesses', 1, 'Updated business: Devplexity', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 05:09:39'),
(27, 1, 'create', 'services', 1, 'Created service: Website development', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 05:12:04'),
(28, 1, 'update', 'services', 1, 'Updated service: Website development', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 05:19:16'),
(29, 2, 'login', 'users', 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 07:02:48'),
(30, 2, 'logout', 'users', 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 07:03:54'),
(31, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 07:04:03'),
(32, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 07:05:13'),
(33, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 07:05:19'),
(34, 1, 'update_status', 'businesses', 1, 'Changed business status to: ', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 07:06:17'),
(35, 1, 'update_status', 'businesses', 1, 'Changed business status to: active', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 07:15:49'),
(36, 1, 'create', 'users', 4, 'Created user: Sahebullah Mansuri', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 12:32:24'),
(37, 1, 'update', 'users', 4, 'Updated user: Sahebullah Mansuri', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 12:46:41'),
(38, 1, 'update', 'users', 3, 'Updated user: admin3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 13:22:13'),
(39, 1, 'update', 'users', 3, 'Updated user: admin3', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 13:22:36'),
(40, 1, 'logout', 'users', 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-02 15:26:44'),
(41, 2, 'login', 'users', 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-03 05:16:56'),
(42, 2, 'logout', 'users', 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-03 05:24:52'),
(43, 1, 'login', 'users', 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36 Edg/134.0.0.0', '2025-04-03 05:24:59');



CREATE TABLE `businesses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT 'India',
  `postal_code` varchar(20) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `opening_hours` text DEFAULT NULL,
  `founded_year` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `status` enum('pending','active','inactive','rejected') DEFAULT 'pending',
  `is_verified` tinyint(1) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `businesses` (`id`, `name`, `slug`, `description`, `short_description`, `logo`, `cover_image`, `address`, `city`, `state`, `country`, `postal_code`, `latitude`, `longitude`, `phone`, `email`, `website`, `opening_hours`, `founded_year`, `owner_id`, `status`, `is_verified`, `is_featured`, `views`, `created_at`, `updated_at`) VALUES
(1, 'Devplexity', 'devplexity', 'This is an agency based in Dibrugarh', 'Software, websites, and digital solutions', '67ecc666aa2b0.jpeg', '67ecc6933bb76.jpg', 'Ledo', 'Tinsukia', 'Assam', 'India', '786182', NULL, NULL, '08638232587', 'iamsaheb786182@gmail.com', 'https://devplexity.com', NULL, 2024, 2, 'active', 1, 1, 0, '2025-04-02 05:08:54', '2025-04-02 07:15:49');



CREATE TABLE `business_categories` (
  `business_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `business_categories` (`business_id`, `category_id`) VALUES
(1, 5),
(1, 8),
(1, 10);



CREATE TABLE `business_hours` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') NOT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `is_closed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `business_images` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `featured` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `image`, `parent_id`, `status`, `featured`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Restaurants', 'restaurants', 'Find the best restaurants near you', 'utensils', NULL, NULL, 'active', 1, 1, '2025-03-29 17:08:44', '2025-03-29 17:44:57'),
(2, 'Hotels', 'hotels', 'Book hotels for your stay', 'hotel', NULL, NULL, 'active', 1, 2, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(3, 'Doctors', 'doctors', 'Find doctors and medical services', 'stethoscope', NULL, NULL, 'active', 1, 3, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(4, 'Real Estate', 'real-estate', 'Property listings and real estate services', 'building', NULL, NULL, 'active', 1, 4, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(5, 'Education', 'education', 'Schools, colleges and educational institutes', 'graduation-cap', NULL, NULL, 'active', 1, 5, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(6, 'Beauty & Spa', 'beauty-spa', 'Salons, spas and wellness centers', 'spa', NULL, NULL, 'active', 1, 6, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(7, 'Home Services', 'home-services', 'Plumbers, electricians and home repair services', 'tools', NULL, NULL, 'active', 1, 7, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(8, 'Travel', 'travel', 'Travel agencies and tour operators', 'plane', NULL, NULL, 'active', 1, 8, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(9, 'Shopping', 'shopping', 'Retail stores and shopping centers', 'shopping-bag', NULL, NULL, 'active', 1, 9, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(10, 'Automotive', 'automotive', 'Car dealers, repair shops and automotive services', 'car', NULL, NULL, 'active', 1, 10, '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(11, 'Phucka1', 'phucka1', 'Phucka dukan chat masala', 'hotel', '67eb9654d959f.jpeg', 1, 'active', 1, 2, '2025-04-01 07:31:32', '2025-04-01 14:22:40');



CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT 'India',
  `is_featured` tinyint(1) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `cities` (`id`, `name`, `state`, `country`, `is_featured`, `image`, `status`, `created_at`) VALUES
(1, 'Mumbai', 'Maharashtra', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(2, 'Delhi', 'Delhi', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(3, 'Bangalore', 'Karnataka', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(4, 'Hyderabad', 'Telangana', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(5, 'Chennai', 'Tamil Nadu', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(6, 'Kolkata', 'West Bengal', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(7, 'Pune', 'Maharashtra', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(8, 'Ahmedabad', 'Gujarat', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(9, 'Jaipur', 'Rajasthan', 'India', 1, NULL, 'active', '2025-03-29 17:08:44'),
(10, 'Lucknow', 'Uttar Pradesh', 'India', 0, NULL, 'active', '2025-03-29 17:08:44');



CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('new','contacted','converted','closed') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` decimal(2,1) NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `business_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `price_type` enum('fixed','starting_from','hourly','daily','custom') DEFAULT 'fixed',
  `duration` varchar(50) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `services` (`id`, `business_id`, `category_id`, `name`, `description`, `price`, `price_type`, `duration`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 'Website development', 'This is a website development service', 29999.00, 'starting_from', '8 hrs', '67ecc8d4e5289.jpeg', 'active', '2025-04-02 05:12:04', '2025-04-02 05:19:16');



CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_group` varchar(50) NOT NULL DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_group`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'JustDial Admin', 'general', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(2, 'site_tagline', 'Business Listing & Directory Platform', 'general', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(3, 'site_logo', 'logo.png', 'general', '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(4, 'site_favicon', 'favicon.ico', 'general', '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(5, 'admin_email', 'admin@example.com', 'general', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(6, 'items_per_page', '10', 'general', '2025-03-29 17:08:44', '2025-04-02 10:10:19'),
(7, 'enable_registration', '1', 'users', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(8, 'enable_email_verification', '1', 'users', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(9, 'enable_reviews', '1', 'users', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(10, 'review_moderation', '1', 'businesses', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(11, 'business_approval_required', '1', 'businesses', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(12, 'google_maps_api_key', '', 'integrations', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(13, 'smtp_host', '', 'email', '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(14, 'smtp_port', '587', 'email', '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(15, 'smtp_username', '', 'email', '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(16, 'smtp_password', '', 'email', '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(17, 'smtp_encryption', 'tls', 'email', '2025-03-29 17:08:44', '2025-03-29 17:08:44'),
(18, 'primary_color', '#3b82f6', 'appearance', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(19, 'secondary_color', '#10b981', 'appearance', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(20, 'accent_color', '#f59e0b', 'appearance', '2025-03-29 17:08:44', '2025-04-01 17:50:30'),
(35, 'email_from_address', 'saheb786182@gmail.com', 'email', '2025-04-01 17:50:30', '2025-04-01 17:50:30'),
(36, 'email_from_name', 'saheb786182@gmail.com', 'email', '2025-04-01 17:50:30', '2025-04-01 17:50:30'),
(37, 'email_smtp_host', 'saheb786182@gmail.com', 'email', '2025-04-01 17:50:30', '2025-04-01 17:50:30'),
(38, 'email_smtp_port', '587', 'email', '2025-04-01 17:50:30', '2025-04-01 17:50:30'),
(39, 'email_smtp_username', 'admin3@gmail.com', 'email', '2025-04-01 17:50:30', '2025-04-01 17:50:30'),
(40, 'email_smtp_password', '12345678', 'email', '2025-04-01 17:50:30', '2025-04-01 17:50:30'),
(41, 'email_smtp_encryption', 'tls', 'email', '2025-04-01 17:50:30', '2025-04-01 17:50:30'),
(43, 'logo', 'logos/67ec276700d1a.jpg', 'appearance', '2025-04-01 17:50:31', '2025-04-01 17:50:31');



CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'default.jpg',
  `role` enum('admin','manager','user') DEFAULT 'user',
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO `users` (`id`, `name`, `username`, `bio`, `email`, `password`, `phone`, `address`, `city`, `state`, `country`, `postal_code`, `profile_image`, `role`, `status`, `email_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin', 'this is my bio of admin', 'admin1@gmail.com', '$2y$10$s1VV3svD/yr0GcjqMZQ.xePhEQvMll061t4KTkfO2copJnQpWT/Ay', NULL, NULL, NULL, NULL, NULL, NULL, 'default.jpg', 'admin', 'active', 1, 'de962b0827bce7259fdf7bf27f460d4c9aed147e78db51efe634461353bfe450', NULL, NULL, '2025-04-03 05:24:59', '2025-03-29 17:08:44', '2025-04-03 05:24:59'),
(2, 'admin2', 'admin2', 'this is my bio of admin', 'admin2@gmail.com', '$2y$10$Jdy7rK78E22jTqkw6l4bXe5PazGQk0uDNQQOLWmgmp35Cuvy0kC/O', NULL, NULL, NULL, NULL, NULL, NULL, 'default.jpg', 'user', 'active', 1, 'de962b0827bce7259fdf7bf27f460d4c9aed147e78db51efe634461353bfe450', NULL, NULL, '2025-04-03 05:16:56', '2025-04-01 17:36:43', '2025-04-03 05:16:56'),
(3, 'admin3', 'admin3', 'this is my bio of admin3', 'admin3@gmail.com', '$2y$10$guyqzO/MtYLf9v5T11Jff.unUjXTuA1MxCLO8cdtoSZBmBd6LiUfe', '', '', '', '', '', '', 'default.jpg', 'user', 'active', 0, 'f69e56a4d9e345a0a0ef6f07d563a6efea4eda7507f224c73faea6eb9a003331', NULL, NULL, NULL, '2025-04-01 17:37:15', '2025-04-02 13:22:36'),
(4, 'Sahebullah Mansuri', 'saheb', 'I am a web developer from Assam', 'saheb786182@gmail.com', '$2y$10$twJR/02kn2ZXmTJC8vmvBukKPNL2odWxi1qmSmnhGqfntTJDw5sre', '08638232587', 'Ledo', 'Tinsukia', 'Assam', 'India', '786182', '67ed2e5804728.jpg', 'user', 'active', 0, NULL, NULL, NULL, NULL, '2025-04-02 12:32:24', '2025-04-02 12:46:41');


ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `owner_id` (`owner_id`);

ALTER TABLE `business_categories`
  ADD PRIMARY KEY (`business_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

ALTER TABLE `business_hours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_id` (`business_id`,`day_of_week`);

ALTER TABLE `business_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_id` (`business_id`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_id` (`business_id`);

ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_id` (`business_id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_id` (`business_id`),
  ADD KEY `fk_service_category` (`category_id`);

ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);


ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

ALTER TABLE `businesses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `business_hours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `business_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `businesses`
  ADD CONSTRAINT `businesses_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `business_categories`
  ADD CONSTRAINT `business_categories_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `business_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

ALTER TABLE `business_hours`
  ADD CONSTRAINT `business_hours_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;

ALTER TABLE `business_images`
  ADD CONSTRAINT `business_images_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;

ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;

ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `services`
  ADD CONSTRAINT `fk_service_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;

-- Allow NULL for business_id (Recommended)
ALTER TABLE `inquiries` MODIFY `business_id` int(11) NULL;

-- Update the status enum values
ALTER TABLE `inquiries` MODIFY `status` enum('new','in_progress','responded','closed','spam') DEFAULT 'new';

-- Add the admin_notes column
ALTER TABLE `inquiries` ADD COLUMN `admin_notes` TEXT NULL DEFAULT NULL AFTER `status`;


COMMIT;


