-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 13, 2023 at 01:30 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `onlinefo_onlinefo`
--

-- --------------------------------------------------------

--
-- Table structure for table `add_ons_category`
--

CREATE TABLE `add_ons_category` (
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `mandatory` tinyint(4) DEFAULT 0,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `add_ons_category`
--

INSERT INTO `add_ons_category` (`entity_id`, `name`, `mandatory`, `content_id`, `language_slug`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`, `is_masterdata`) VALUES
(40, 'Extra cheese', 0, 1227, 'en', 1, 1, '2021-06-17 11:47:50', 1, '2022-07-21 11:56:03', '0'),
(42, 'Size', 0, 1229, 'en', 1, 1, '2021-06-17 11:48:48', 1, '2021-07-20 12:41:10', '0'),
(43, 'Drinks', 0, 1230, 'en', 1, 1, '2021-06-17 11:53:36', 1068, '2022-07-21 11:28:48', '0'),
(50, 'boissons', 0, 1230, 'fr', 1, 1, '2021-06-17 12:01:12', NULL, NULL, '0'),
(51, 'مشروبات', 0, 1230, 'ar', 1, 1, '2021-06-17 12:01:33', NULL, NULL, '0'),
(52, 'Taille', 0, 1229, 'fr', 1, 1, '2021-06-17 12:02:00', NULL, NULL, '0'),
(53, 'بحجم', 0, 1229, 'ar', 1, 1, '2021-06-17 12:02:18', NULL, NULL, '0'),
(56, 'Fromage supplémentaire', 0, 1227, 'fr', 1, 1, '2021-06-17 12:03:48', NULL, NULL, '0'),
(57, 'الجبن إضافية', 0, 1227, 'ar', 1, 1, '2021-06-17 12:04:08', NULL, NULL, '0'),
(76, 'Beverages', 0, 1639, 'en', 1, 1, '2021-07-15 09:25:17', 1, '2021-07-15 14:58:27', '0'),
(78, 'المشروبات', 0, 1639, 'ar', 1, 1, '2021-08-06 06:36:20', 1, '2022-08-09 05:54:09', '0'),
(100, 'Breuvages', 0, 1639, 'fr', 1, 1, '2022-08-09 05:54:20', NULL, NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `add_ons_master`
--

CREATE TABLE `add_ons_master` (
  `add_ons_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL COMMENT 'addons_category_id',
  `add_ons_name` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `add_ons_price` decimal(20,2) DEFAULT NULL,
  `is_multiple` tinyint(4) NOT NULL DEFAULT 0,
  `display_limit` int(11) DEFAULT NULL,
  `mandatory` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `add_ons_master`
--

INSERT INTO `add_ons_master` (`add_ons_id`, `menu_id`, `category_id`, `add_ons_name`, `add_ons_price`, `is_multiple`, `display_limit`, `mandatory`) VALUES
(46, 692, 50, 'Pepsi', '5.00', 0, NULL, 0),
(47, 692, 50, 'mirinda', '8.00', 0, NULL, 0),
(48, 692, 50, 'sprite', '12.00', 0, NULL, 0),
(49, 692, 52, 'petite', '4.00', 0, NULL, 0),
(50, 692, 52, 'Moyen', '6.00', 0, NULL, 0),
(51, 692, 52, 'grande', '8.00', 0, NULL, 0),
(58, 693, 78, 'شبح', '5.00', 0, NULL, 0),
(59, 693, 78, 'فحم الكوك', '8.00', 0, NULL, 0),
(60, 693, 78, 'بيبسي', '12.00', 0, NULL, 0),
(61, 693, 51, 'صغير', '4.00', 0, NULL, 0),
(62, 693, 51, 'متوسط', '6.00', 0, NULL, 0),
(63, 693, 51, 'كبير', '8.00', 0, NULL, 0),
(64, 651, 43, 'pepsi', '5.00', 0, NULL, 0),
(65, 651, 43, 'mirinda', '8.00', 0, NULL, 0),
(66, 651, 43, 'sprite', '12.00', 0, NULL, 0),
(67, 651, 42, 'small', '4.00', 0, NULL, 0),
(68, 651, 42, 'medum', '6.00', 0, NULL, 0),
(69, 651, 42, 'large', '8.00', 0, NULL, 0),
(87, 688, 56, 'grande', '4.00', 0, NULL, 0),
(88, 688, 56, 'Moyen', '4.00', 0, NULL, 0),
(89, 688, 56, 'petite', '4.00', 0, NULL, 0),
(90, 688, 52, 'petite', '5.00', 1, 2, 1),
(91, 688, 52, 'Moyen', '8.00', 1, 2, 1),
(92, 688, 52, 'Grande', '12.00', 1, 2, 1),
(93, 689, 78, 'شبح', '4.00', 0, NULL, 0),
(94, 689, 78, 'فحم الكوك', '4.00', 0, NULL, 0),
(95, 689, 78, 'بيبسي', '4.00', 0, NULL, 0),
(96, 689, 51, 'صغير', '5.00', 1, 2, 1),
(97, 689, 51, 'دواء', '8.00', 1, 2, 1),
(98, 689, 51, 'كبير', '12.00', 1, 2, 1),
(99, 668, 43, 'sprite', '4.00', 0, NULL, 0),
(100, 668, 43, 'Coke', '4.00', 0, NULL, 0),
(101, 668, 43, 'Pepsi', '4.00', 0, NULL, 0),
(102, 668, 42, 'small', '5.00', 1, 2, 1),
(103, 668, 42, 'medium', '8.00', 1, 2, 1),
(104, 668, 42, 'large', '12.00', 1, 2, 1),
(105, 710, 56, 'Fromage supplémentaire', '10.00', 0, NULL, 0),
(106, 710, 56, 'Fromage Double Extra', '20.00', 0, NULL, 0),
(107, 711, 53, 'الجبن إضافية', '10.00', 0, NULL, 0),
(108, 711, 53, 'دبل اكسترا جبنة', '20.00', 0, NULL, 0),
(109, 439, 40, 'Extra cheese', '10.00', 0, NULL, 0),
(110, 439, 40, 'Double Extra cheese', '20.00', 0, NULL, 0),
(111, 709, 51, 'صغير', '15.00', 0, NULL, 0),
(112, 709, 51, 'متوسط', '19.00', 0, NULL, 0),
(113, 709, 51, 'كبير', '24.00', 0, NULL, 0),
(114, 708, 52, 'petite', '15.00', 0, NULL, 0),
(115, 708, 52, 'Moyen', '19.00', 0, NULL, 0),
(116, 708, 52, 'grande', '24.00', 0, NULL, 0),
(117, 441, 42, 'small', '15.00', 0, NULL, 0),
(118, 441, 42, 'medum', '19.00', 0, NULL, 0),
(119, 441, 42, 'large', '24.00', 0, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `admin_alerts`
--

CREATE TABLE `admin_alerts` (
  `alert_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `button_label` varchar(100) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_alerts`
--

INSERT INTO `admin_alerts` (`alert_id`, `message`, `from_date`, `to_date`, `button_label`, `created_by`, `created_at`) VALUES
(1, 'This is the alert for admin use only.', '2021-07-28', '2021-07-30', 'Got it', 1, '2021-07-29 10:42:40');

-- --------------------------------------------------------

--
-- Table structure for table `agent_order_notification`
--

CREATE TABLE `agent_order_notification` (
  `agent_notification_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'for cancelled or failed refund notifications',
  `notification_slug` enum('order_accepted','order_preparing','order_ongoing','order_delivered','order_canceled','order_rejected','order_ready','order_served','order_completed','order_updated','admin_order_created','order_rejected_refunded','order_canceled_refunded','order_initiated','tip_refund_initiated','order_refund_canceled','order_refund_failed','order_refund_pending','tip_refund_canceled','tip_refund_failed','tip_refund_pending') NOT NULL,
  `view_status` tinyint(4) NOT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark_restaurant`
--

CREATE TABLE `bookmark_restaurant` (
  `entity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookmark_restaurant`
--

INSERT INTO `bookmark_restaurant` (`entity_id`, `user_id`, `restaurant_id`) VALUES
(1, 23, 17);

-- --------------------------------------------------------

--
-- Table structure for table `cancel_reject_reasons`
--

CREATE TABLE `cancel_reject_reasons` (
  `entity_id` int(11) NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `reason_type` enum('cancel','reject') NOT NULL,
  `user_type` enum('Admin','Driver','Customer') NOT NULL DEFAULT 'Admin',
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cancel_reject_reasons`
--

INSERT INTO `cancel_reject_reasons` (`entity_id`, `reason`, `reason_type`, `user_type`, `content_id`, `language_slug`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(45, 'Items are not available', 'cancel', 'Admin', 2069, 'en', 1, 1, '2022-05-18 11:58:25', NULL, '2022-05-18 11:58:25'),
(46, 'Due to Cyclone we are not accepting all order', 'cancel', 'Admin', 2070, 'en', 1, 1, '2022-05-18 11:59:24', NULL, '2022-05-18 11:59:24'),
(47, 'Unable to track the User Address', 'cancel', 'Admin', 2071, 'en', 1, 1, '2022-05-18 12:00:00', 1, '2022-05-18 12:04:01'),
(48, 'Impossible de suivre l\'utilisateur', 'cancel', 'Admin', 2071, 'fr', 1, 1, '2022-05-18 12:05:04', NULL, '2022-05-18 12:05:04'),
(49, 'en raison du cyclone, nous n\'acceptons pas', 'cancel', 'Admin', 2070, 'fr', 1, 1, '2022-05-18 12:05:37', NULL, '2022-05-18 12:05:37'),
(50, 'Les articles ne sont pas disponibles', 'cancel', 'Admin', 2069, 'fr', 1, 1, '2022-05-18 12:06:05', NULL, '2022-05-18 12:06:05'),
(51, 'العناصر غير متوفرة', 'cancel', 'Admin', 2069, 'ar', 1, 1, '2022-05-18 12:06:35', NULL, '2022-05-18 12:06:35'),
(52, 'نحن لا نقبل بسبب الإعصار', 'cancel', 'Admin', 2070, 'ar', 1, 1, '2022-05-18 12:07:26', NULL, '2022-05-18 12:07:26'),
(55, 'Items went out of stock', 'reject', 'Admin', 2082, 'en', 1, 1, '2022-06-08 08:53:06', NULL, '2022-06-08 08:53:06'),
(60, 'by mistake i placed this items', 'cancel', 'Customer', 2136, 'en', 1, 1, '2022-06-20 13:19:30', 1, '2022-06-20 13:20:24'),
(61, 'I changed my opinion', 'cancel', 'Customer', 2137, 'en', 1, 1, '2022-06-20 13:20:09', NULL, '2022-06-20 13:20:09'),
(62, 'I Want to replace Other Restaurant', 'cancel', 'Customer', 2138, 'en', 1, 1, '2022-06-20 13:20:54', NULL, '2022-06-20 13:20:54'),
(63, 'I want to  add More items', 'cancel', 'Customer', 2139, 'en', 1, 1, '2022-06-20 13:21:23', NULL, '2022-06-20 13:21:23'),
(64, 'unable To track the Location', 'cancel', 'Driver', 2144, 'en', 1, 1, '2022-06-22 05:56:04', NULL, '2022-06-22 05:56:04');

-- --------------------------------------------------------

--
-- Table structure for table `cart_detail`
--

CREATE TABLE `cart_detail` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `items` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`entity_id`, `name`, `sequence`, `image`, `content_id`, `language_slug`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`, `is_masterdata`) VALUES
(24, 'Dessert', 0, 'category/Dessert-en1660025520.png', 573, 'en', 1, 1, '2021-04-20 17:52:45', 1, '2022-08-09 06:12:00', '0'),
(90, 'Appetizers', 0, 'category/Appetizers-en1660025341.jpg', 1220, 'en', 1, 1, '2021-06-17 09:21:01', 1, '2022-08-09 06:09:01', '0'),
(93, 'Mains', 0, 'category/Mains-en1660025245.jpg', 1223, 'en', 1, 1, '2021-06-17 09:25:53', 1, '2022-08-09 06:07:25', '0'),
(100, 'secteur', 0, 'category/secteur-fr1660025238.jpg', 1223, 'fr', 1, 1, '2021-06-17 09:43:48', 1, '2022-08-09 06:07:18', '0'),
(102, 'أنابيب', 0, 'category/أنابيب-ar1660025232.jpg', 1223, 'ar', 1, 1, '2021-06-17 09:44:21', 1, '2022-08-09 06:07:12', '0'),
(107, 'Apéritifs', 0, 'category/Apéritifs-fr1660025355.jpg', 1220, 'fr', 1, 1, '2021-06-17 09:50:06', 1, '2022-08-09 06:09:15', '0'),
(108, 'المقبلات', 0, 'category/المقبلات-ar1660025364.jpg', 1220, 'ar', 1, 1, '2021-06-17 09:51:20', 1, '2022-08-09 06:09:24', '0'),
(113, 'Dessert', 0, 'category/Dessert-fr1660025528.png', 573, 'fr', 1, 1, '2021-06-17 09:58:21', 1, '2022-08-09 06:12:08', '0'),
(114, 'الحلوى', 0, 'category/الحلوى-ar1660025536.png', 573, 'ar', 1, 1, '2021-06-17 09:59:54', 1, '2022-08-09 06:12:16', '0'),
(125, 'Burger', 0, 'category/Burger-en1660025119.png', 1652, 'en', 1, 1, '2021-07-26 12:48:10', 1, '2022-08-09 06:05:19', '0'),
(135, 'Pizza', 0, 'category/Pizza-en1660025062.png', 1848, 'en', 1, 1, '2021-12-06 08:28:10', 1, '2022-08-09 06:04:22', '0'),
(154, 'برجر', 0, 'category/برجر-ar1660025115.png', 1652, 'ar', 1, 1, '2022-02-11 13:02:08', 1, '2022-08-09 06:05:15', '0'),
(156, 'بيتزا', 0, 'category/بيتزا-ar1660025057.png', 1848, 'ar', 1, 1, '2022-02-11 15:08:10', 1, '2022-08-09 06:04:17', '0'),
(161, 'Burger', 0, 'category/Burger-fr1660025116.png', 1652, 'fr', 1, 1, '2022-05-18 13:09:04', 1, '2022-08-09 06:05:16', '0'),
(164, 'Pizza', 0, 'category/Pizza-fr1660025058.png', 1848, 'fr', 1, 1, '2022-08-09 06:00:11', 1, '2022-08-09 06:04:18', '0');

-- --------------------------------------------------------

--
-- Table structure for table `cms`
--

CREATE TABLE `cms` (
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `CMSSlug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `cms_icon` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_date` int(11) DEFAULT NULL,
  `meta_title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cms`
--

INSERT INTO `cms` (`entity_id`, `name`, `CMSSlug`, `description`, `image`, `cms_icon`, `content_id`, `language_slug`, `status`, `created_by`, `updated_by`, `updated_date`, `created_date`, `meta_title`, `meta_description`) VALUES
(4, 'Privacy Policy', 'privacy-policy', '<h1>Privacy Policy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">This Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your information when You use the Service and tells You about Your privacy rights and how the law protects You.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We use Your Personal data to provide and improve the Service. By using the Service, You agree to the collection and use of information in accordance with this Privacy Policy.</p>\r\n\r\n<h1>Interpretation and Definitions</h1>\r\n\r\n<h2 style=\"display:block;width:100%;\">Interpretation</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>\r\n\r\n<h2>Definitions</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">For the purposes of this Privacy Policy:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Account</strong> means a unique account created for You to access our Service or parts of our Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Affiliate</strong> means an entity that controls, is controlled by or is under common control with a party, where &quot;control&quot; means ownership of 50% or more of the shares, equity interest or other securities entitled to vote for election of directors or other managing authority.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Application</strong> means the software program provided by the Company downloaded by You on any electronic device, named Haus des Döners Restaurant.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Cookies</strong> are small files that are placed on Your computer, mobile device or any other device by a website, containing the details of Your browsing history on that website among its many uses.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Country</strong> refers to: Gujarat, India</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Device</strong> means any device that can access the Service such as a computer, a cellphone or a digital tablet.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Personal Data</strong> is any information that relates to an identified or identifiable individual.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Service</strong> refers to the Application or the Website or both.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Service Provider</strong> means any natural or legal person who processes the data on behalf of the Company. It refers to third-party companies or individuals employed by the Company to facilitate the Service, to provide the Service on behalf of the Company, to perform services related to the Service or to assist the Company in analyzing how the Service is used.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Third-party Social Media Service</strong> refers to any website or any social network website through which a User can log in or create an account to use the Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Usage Data</strong> refers to data collected automatically, either generated by the use of the Service or from the Service infrastructure itself (for example, the duration of a page visit).</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Website</strong> refers to Haus des Döners Restaurant, accessible from <a href=\"https://www.hausdesdoeners.com\" rel=\"external nofollow noopener\" target=\"_blank\">https://www.hausdesdoeners.com</a></p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>You</strong> means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</p>\r\n	</li>\r\n</ul>\r\n\r\n<h1>Collecting and Using Your Personal Data</h1>\r\n\r\n<h2 style=\"width: 100%;\">Types of Data Collected</h2>\r\n\r\n<h3>Personal Data</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">While using Our Service, We may ask You to provide Us with certain personally identifiable information that can be used to contact or identify You. Personally identifiable information may include, but is not limited to:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Email address</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">First name and last name</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Phone number</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Address, State, Province, ZIP/Postal code, City</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Usage Data</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3 style=\"width: 100%;\">Usage Data</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">Usage Data is collected automatically when using the Service.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">Usage Data may include information such as Your Device&#39;s Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that You visit, the time and date of Your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">When You access the Service by or through a mobile device, We may collect certain information automatically, including, but not limited to, the type of mobile device You use, Your mobile device unique ID, the IP address of Your mobile device, Your mobile operating system, the type of mobile Internet browser You use, unique device identifiers and other diagnostic data.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We may also collect information that Your browser sends whenever You visit our Service or when You access the Service by or through a mobile device.</p>\r\n\r\n<h3>Information from Third-Party Social Media Services</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">The Company allows You to create an account and log in to use the Service through the following Third-party Social Media Services:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\">Google</li>\r\n	<li style=\"margin: 0;\">Facebook</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">If You decide to register through or otherwise grant us access to a Third-Party Social Media Service, We may collect Personal data that is already associated with Your Third-Party Social Media Service&#39;s account, such as Your name, Your email address, Your activities or Your contact list associated with that account.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">You may also have the option of sharing additional information with the Company through Your Third-Party Social Media Service&#39;s account. If You choose to provide such information and Personal Data, during registration or otherwise, You are giving the Company permission to use, share, and store it in a manner consistent with this Privacy Policy.</p>\r\n\r\n<h3>Information Collected while Using the Application</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">While using Our Application, in order to provide features of Our Application, We may collect, with Your prior permission:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Information regarding your location</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Pictures and other information from your Device&#39;s camera and photo library</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We use this information to provide features of Our Service, to improve and customize Our Service. The information may be uploaded to the Company&#39;s servers and/or a Service Provider&#39;s server or it may be simply stored on Your device.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">You can enable or disable access to this information at any time, through Your Device settings.</p>\r\n\r\n<h3>Tracking Technologies and Cookies</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We use Cookies and similar tracking technologies to track the activity on Our Service and store certain information. Tracking technologies used are beacons, tags, and scripts to collect and track information and to improve and analyze Our Service. The technologies We use may include:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\"><strong>Cookies or Browser Cookies.</strong> A cookie is a small file placed on Your Device. You can instruct Your browser to refuse all Cookies or to indicate when a Cookie is being sent. However, if You do not accept Cookies, You may not be able to use some parts of our Service. Unless you have adjusted Your browser setting so that it will refuse Cookies, our Service may use Cookies.</li>\r\n	<li style=\"margin: 0;\"><strong>Flash Cookies.</strong> Certain features of our Service may use local stored objects (or Flash Cookies) to collect and store information about Your preferences or Your activity on our Service. Flash Cookies are not managed by the same browser settings as those used for Browser Cookies. For more information on how You can delete Flash Cookies, please read &quot;Where can I change the settings for disabling, or deleting local shared objects?&quot; available at <a href=\"https://helpx.adobe.com/flash-player/kb/disable-local-shared-objects-flash.html#main_Where_can_I_change_the_settings_for_disabling__or_deleting_local_shared_objects_\" rel=\"external nofollow noopener\" target=\"_blank\">https://helpx.adobe.com/flash-player/kb/disable-local-shared-objects-flash.html#main_Where_can_I_change_the_settings_for_disabling__or_deleting_local_shared_objects_</a></li>\r\n	<li style=\"margin: 0;\"><strong>Web Beacons.</strong> Certain sections of our Service and our emails may contain small electronic files known as web beacons (also referred to as clear gifs, pixel tags, and single-pixel gifs) that permit the Company, for example, to count users who have visited those pages or opened an email and for other related website statistics (for example, recording the popularity of a certain section and verifying system and server integrity).</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">Cookies can be &quot;Persistent&quot; or &quot;Session&quot; Cookies. Persistent Cookies remain on Your personal computer or mobile device when You go offline, while Session Cookies are deleted as soon as You close Your web browser.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We use both Session and Persistent Cookies for the purposes set out below:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Necessary / Essential Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Type: Session Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Purpose: These Cookies are essential to provide You with services available through the Website and to enable You to use some of its features. They help to authenticate users and prevent fraudulent use of user accounts. Without these Cookies, the services that You have asked for cannot be provided, and We only use these Cookies to provide You with those services.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Cookies Policy / Notice Acceptance Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Type: Persistent Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Purpose: These Cookies identify if users have accepted the use of cookies on the Website.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>Functionality Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Type: Persistent Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\">Purpose: These Cookies allow us to remember choices You make when You use the Website, such as remembering your login details or language preference. The purpose of these Cookies is to provide You with a more personal experience and to avoid You having to re-enter your preferences every time You use the Website.</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">For more information about the cookies we use and your choices regarding cookies, please visit our Cookies Policy or the Cookies section of our Privacy Policy.</p>\r\n\r\n<h2>Use of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">The Company may use Personal Data for the following purposes:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>To provide and maintain our Service</strong>, including to monitor the usage of our Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>To manage Your Account:</strong> to manage Your registration as a user of the Service. The Personal Data You provide can give You access to different functionalities of the Service that are available to You as a registered user.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>For the performance of a contract:</strong> the development, compliance and undertaking of the purchase contract for the products, items or services You have purchased or of any other contract with Us through the Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>To contact You:</strong> To contact You by email, telephone calls, SMS, or other equivalent forms of electronic communication, such as a mobile application&#39;s push notifications regarding updates or informative communications related to the functionalities, products or contracted services, including the security updates, when necessary or reasonable for their implementation.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>To provide You</strong> with news, special offers and general information about other goods, services and events which we offer that are similar to those that you have already purchased or enquired about unless You have opted not to receive such information.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>To manage Your requests:</strong> To attend and manage Your requests to Us.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>For business transfers:</strong> We may use Your information to evaluate or conduct a merger, divestiture, restructuring, reorganization, dissolution, or other sale or transfer of some or all of Our assets, whether as a going concern or as part of bankruptcy, liquidation, or similar proceeding, in which Personal Data held by Us about our Service users is among the assets transferred.</p>\r\n	</li>\r\n	<li style=\"margin: 0;\">\r\n	<p style=\"line-height: 2; padding: 0; margin: 0;\"><strong>For other purposes</strong>: We may use Your information for other purposes, such as data analysis, identifying usage trends, determining the effectiveness of our promotional campaigns and to evaluate and improve our Service, products, services, marketing and your experience.</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We may share Your personal information in the following situations:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\"><strong>With Service Providers:</strong> We may share Your personal information with Service Providers to monitor and analyze the use of our Service, to contact You.</li>\r\n	<li style=\"margin: 0;\"><strong>For business transfers:</strong> We may share or transfer Your personal information in connection with, or during negotiations of, any merger, sale of Company assets, financing, or acquisition of all or a portion of Our business to another company.</li>\r\n	<li style=\"margin: 0;\"><strong>With Affiliates:</strong> We may share Your information with Our affiliates, in which case we will require those affiliates to honor this Privacy Policy. Affiliates include Our parent company and any other subsidiaries, joint venture partners or other companies that We control or that are under common control with Us.</li>\r\n	<li style=\"margin: 0;\"><strong>With business partners:</strong> We may share Your information with Our business partners to offer You certain products, services or promotions.</li>\r\n	<li style=\"margin: 0;\"><strong>With other users:</strong> when You share personal information or otherwise interact in the public areas with other users, such information may be viewed by all users and may be publicly distributed outside. If You interact with other users or register through a Third-Party Social Media Service, Your contacts on the Third-Party Social Media Service may see Your name, profile, pictures and description of Your activity. Similarly, other users will be able to view descriptions of Your activity, communicate with You and view Your profile.</li>\r\n	<li style=\"margin: 0;\"><strong>With Your consent</strong>: We may disclose Your personal information for any other purpose with Your consent.</li>\r\n</ul>\r\n\r\n<h2>Retention of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">The Company will retain Your Personal Data only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use Your Personal Data to the extent necessary to comply with our legal obligations (for example, if we are required to retain your data to comply with applicable laws), resolve disputes, and enforce our legal agreements and policies.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">The Company will also retain Usage Data for internal analysis purposes. Usage Data is generally retained for a shorter period of time, except when this data is used to strengthen the security or to improve the functionality of Our Service, or We are legally obligated to retain this data for longer time periods.</p>\r\n\r\n<h2>Transfer of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">Your information, including Personal Data, is processed at the Company&#39;s operating offices and in any other places where the parties involved in the processing are located. It means that this information may be transferred to &mdash; and maintained on &mdash; computers located outside of Your state, province, country or other governmental jurisdiction where the data protection laws may differ than those from Your jurisdiction.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">Your consent to this Privacy Policy followed by Your submission of such information represents Your agreement to that transfer.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">The Company will take all steps reasonably necessary to ensure that Your data is treated securely and in accordance with this Privacy Policy and no transfer of Your Personal Data will take place to an organization or a country unless there are adequate controls in place including the security of Your data and other personal information.</p>\r\n\r\n<h2>Disclosure of Your Personal Data</h2>\r\n\r\n<h3>Business Transactions</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">If the Company is involved in a merger, acquisition or asset sale, Your Personal Data may be transferred. We will provide notice before Your Personal Data is transferred and becomes subject to a different Privacy Policy.</p>\r\n\r\n<h3>Law enforcement</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">Under certain circumstances, the Company may be required to disclose Your Personal Data if required to do so by law or in response to valid requests by public authorities (e.g. a court or a government agency).</p>\r\n\r\n<h3>Other legal requirements</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">The Company may disclose Your Personal Data in the good faith belief that such action is necessary to:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\">Comply with a legal obligation</li>\r\n	<li style=\"margin: 0;\">Protect and defend the rights or property of the Company</li>\r\n	<li style=\"margin: 0;\">Prevent or investigate possible wrongdoing in connection with the Service</li>\r\n	<li style=\"margin: 0;\">Protect the personal safety of Users of the Service or the public</li>\r\n	<li style=\"margin: 0;\">Protect against legal liability</li>\r\n</ul>\r\n\r\n<h2 style=\"display:block;width:100%;\">Security of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">The security of Your Personal Data is important to Us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While We strive to use commercially acceptable means to protect Your Personal Data, We cannot guarantee its absolute security.</p>\r\n\r\n<h1>Children&#39;s Privacy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">Our Service does not address anyone under the age of 13. We do not knowingly collect personally identifiable information from anyone under the age of 13. If You are a parent or guardian and You are aware that Your child has provided Us with Personal Data, please contact Us. If We become aware that We have collected Personal Data from anyone under the age of 13 without verification of parental consent, We take steps to remove that information from Our servers.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">If We need to rely on consent as a legal basis for processing Your information and Your country requires consent from a parent, We may require Your parent&#39;s consent before We collect and use that information.</p>\r\n\r\n<h1>Links to Other Websites</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">Our Service may contain links to other websites that are not operated by Us. If You click on a third party link, You will be directed to that third party&#39;s site. We strongly advise You to review the Privacy Policy of every site You visit.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We have no control over and assume no responsibility for the content, privacy policies or practices of any third party sites or services.</p>\r\n\r\n<h1>Changes to this Privacy Policy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We may update Our Privacy Policy from time to time. We will notify You of any changes by posting the new Privacy Policy on this page.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">We will let You know via email and/or a prominent notice on Our Service, prior to the change becoming effective and update the &quot;Last updated&quot; date at the top of this Privacy Policy.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>\r\n\r\n<h1>Contact Us</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0; margin: 0;\">If you have any questions about this Privacy Policy, You can contact us:</p>\r\n\r\n<ul style=\"list-style-type: disc;\">\r\n	<li style=\"margin: 0;\">By email: support@hausdesdoeners.com</li>\r\n</ul>', 'cms/64dcaa517f98c5ac0d4fdd17bdcd1eb8.png', 'cms/fa5f50d80b21de0c237dd5b5efe2f0c8.png', 162, 'en', 1, 1, 1, '2023-01-24 10:28:55', NULL, 'Privacy Policy', ''),
(5, 'Terms and Conditions', 'terms-and-conditions', '<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>Haus des Döners Restaurant</b>&nbsp;an Open Source Application and it is available for the usage of the end user for no cost at all. By choosing to use our service, you agree to our Privacy Policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">This page intends to provide our visitors with information regarding our policies on the collection, use, and disclosure of their personal information, in case they decide to use our service. All the information collected by us is used to provide and improve our service. Your personal information will not be used or shared with any third party, except as described in this Privacy Policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">The terms used in this privacy policy have the same meanings as in our Terms and Conditions, which is accessible at Haus des Döners unless otherwise defined in this Privacy Policy.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Information Collection and Use</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To provide you with a great user experience, while using our service, we may ask you to provide us with your personal information. The information collected will be retained by us and will be used only as stated in this privacy policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Haus des Döners also uses third-party services which may collect your personal information and use it for your identification.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Please find below the link to the privacy policy of our third-party service provider:<br />\r\nGoogle Play Services&nbsp;</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Log Data</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">While using our services if an unexpected error occurs, we collect data (through third-party products) from your phone which is known as &lsquo;Log Data.&rsquo; The collected data may include information like your IP address, the name of your device, operating system version, the time and date when the service is used, the configuration of the app when utilizing our Service, and the configuration of the application while utilizing our service and other such statistics.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Cookies</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Cookies are archives stored on your device with the small amount of information about the user and the website, it can be used as anonymous unique identifiers. These archives are sent to your browser from the websites visited by you and are stored on the internal memory of your device.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Haus des Döners does not use these cookies explicitly. However, it does uses third-party codes and libraries, which may use cookies to gather information to improve their service and user experience. You can choose to accept or decline these cookies and be in control of when and how these cookies are being used. If you refuse to use cookies by Haus des Döners, you might not be able to use some parts of the service.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Service Providers</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">We may utilize third-party companies and individuals for the following:</span></p>\r\n\r\n<ul class=\"ul1\" style=\"color: rgb(0, 0, 0); font-size: medium; text-align: start;\">\r\n	<li class=\"li2\" style=\"margin: 0px; text-align: justify; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Helvetica; color: rgb(38, 38, 38); -webkit-text-stroke: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To aid our service</span></li>\r\n	<li class=\"li2\" style=\"margin: 0px; text-align: justify; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Helvetica; color: rgb(38, 38, 38); -webkit-text-stroke: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To deliver the service on behalf of us</span></li>\r\n	<li class=\"li2\" style=\"margin: 0px; text-align: justify; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Helvetica; color: rgb(38, 38, 38); -webkit-text-stroke: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To execute related services</span></li>\r\n	<li class=\"li2\" style=\"margin: 0px; text-align: justify; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Helvetica; color: rgb(38, 38, 38); -webkit-text-stroke: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To support us in evaluating how our service is being used.</span></li>\r\n</ul>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">We want our users to be well informed that these third-parties have access to your personal data. They collect and use your information to perform the various tasks assigned to them, on our behalf. However, they are obligated not to use this information for any other purpose, and not to disclose it with others.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300))); \"><b>Security</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">We appreciate and value the level of trust you put in us by providing your personal information and therefore we are determined to use the best commercially acceptable means to protect it. But there is no mode of transmission on the internet or electronic storage that is 100% safe, secure and reliable, and we cannot guarantee its absolute security.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Links to Other Sites</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Our application contains links to other sites. By clicking on these link you will be navigated to their site. These external sites are not operated by us and therefore we strongly recommend you to review their privacy policies. We do not control those sites and accept no responsibility for the content, privacy policies, or practices of any such third-party sites.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Children&rsquo;s Privacy</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Our services do not address anyone below the age of thirteen. We do not collect any kind of personally identifiable data from children below the age of thirteen. If we find out that a child under the age of 13 has provided us with some personal data, we take immediate action to remove it from our servers.&nbsp;</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">If your child has accidentally provided us with personal information, kindly contact us so that we can take all the necessary steps to remove it.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Changes to This Privacy Policy</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">We may update our privacy policy at times. We advise you to review this page at regular intervals to stay informed. In case of any changes in our privacy policy, we will notify you by updating it on this page. Such changes would effective immediately after being posted here.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Contact Us</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Please feel free to contact us in case of any query or suggestions regarding our privacy policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Phone:&nbsp;<a href=\"tel:0123456789\">(+1) 012345 6789</a></span></p>\r\n\r\n<p class=\"p3\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(16, 109, 180); margin: 0px 0px 14px; color: rgb(16, 109, 180);\"><span class=\"s2\" style=\"font-kerning: none; color: rgb(38, 38, 38); -webkit-text-stroke-color: rgb(38, 38, 38);\">Email:&nbsp;<a href=\"mailto:support@hausdesdoeners.com\"><span class=\"s3\" style=\"font-kerning: none; color: rgb(16, 109, 180); -webkit-text-stroke-color: rgb(16, 109, 180);\">support@hausdesdoeners.com</span></a>.&nbsp;</span></p>\r\n\r\n<p class=\"p3\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(16, 109, 180); margin: 0px 0px 14px; color: rgb(16, 109, 180);\">&nbsp;</p>', 'cms/7e4871aecb98f6cd20988ec8287f9650.jpg', 'cms/15a902c1e9fe62e9e31e4ab1db69f303.png', 163, 'en', 0, 1, 1, '2023-01-24 10:34:38', NULL, '', ''),
(6, 'Contact Us', 'contact-us', '<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>&nbsp;Contact Us</strong></h2>\r\n\r\n<p>Please feel free to contact us in case of any queries or suggestions regarding our privacy policy.</p>\r\n\r\n<p>Phone:<a href=\"tel:0123456789\">(+1) 012345 6789</a></p>\r\n\r\n<p>Email:<a href=\"mailto:support@hausdesdoeners.com\" style=\"background-color: var(--highlight-bg); white-space: pre-wrap;\">support@hausdesdoeners.com</a><span style=\"background-color: var(--highlight-bg); color: var(--highlight-color); white-space: pre-wrap;\">.&nbsp;</span></p>\r\n\r\n<p class=\"p3\" style=\"margin: 0px 0px 14px; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; color: rgb(16, 109, 180); -webkit-text-stroke-color: rgb(16, 109, 180);\">&nbsp;</p>', 'cms/94fe2b7cb2db95136fb8ffa5231ab742.jpg', 'cms/13ce88634ff175c0e82e0a7d22804846.png', 161, 'en', 1, 1, 1, '2023-01-24 10:35:47', NULL, 'Contact Us', ''),
(7, 'About Us', 'about-us', '<p><strong>Haus des Döners Restaurant</strong>&nbsp;an Open Source Application and it is available for the usage of the end user for no cost at all. By choosing to use our service, you agree to our Privacy Policy.</p>\r\n\r\n<p>This page intends to provide our visitors with information regarding our policies on the collection, use, and disclosure of their personal information, in case they decide to use our service. All the information collected by us is used to provide and improve our service. Your personal information will not be used or shared with any third party, except as described in this Privacy Policy.</p>\r\n\r\n<p>The terms used in this privacy policy have the same meanings as in our Terms and Conditions, which is accessible at Haus des Döners unless otherwise defined in this Privacy Policy.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Information Collection and Use</strong></h2>\r\n\r\n<p>To provide you with a great user experience, while using our service, we may ask you to provide us with your personal information. The information collected will be retained by us and will be used only as stated in this privacy policy.</p>\r\n\r\n<p>Haus des Döners also uses third-party services which may collect your personal information and use it for your identification.</p>\r\n\r\n<p>Please find below the link to the privacy policy of our third-party service provider:<br />\r\nGoogle Play Services&nbsp;</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Log Data</strong></h2>\r\n\r\n<p>While using our services if an unexpected error occurs, we collect data (through third-party products) from your phone which is known as &lsquo;Log Data.&rsquo; The collected data may include information like your IP address, the name of your device, operating system version, the time and date when the service is used, the configuration of the app when utilizing our Service, and the configuration of the application while utilizing our service and other such statistics.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Cookies</strong></h2>\r\n\r\n<p>Cookies are archives stored on your device with the small amount of information about the user and the website, it can be used as anonymous unique identifiers. These archives are sent to your browser from the websites visited by you and are stored on the internal memory of your device.</p>\r\n\r\n<p>Haus des Döners does not use these cookies explicitly. However, it does uses third-party codes and libraries, which may use cookies to gather information to improve their service and user experience. You can choose to accept or decline these cookies and be in control of when and how these cookies are being used. If you refuse to use cookies by Haus des Döners, you might not be able to use some parts of the service.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Service Providers</strong></h2>\r\n\r\n<p>We may utilize third-party companies and individuals for the following:</p>\r\n\r\n<ul>\r\n	<li>To aid our service</li>\r\n	<li>To deliver the service on behalf of us</li>\r\n	<li>To execute related services</li>\r\n	<li>To support us in evaluating how our service is being used.</li>\r\n</ul>\r\n\r\n<p>We want our users to be well informed that these third-parties have access to your personal data. They collect and use your information to perform the various tasks assigned to them, on our behalf. However, they are obligated not to use this information for any other purpose, and not to disclose it with others.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Security</strong></h2>\r\n\r\n<p>We appreciate and value the level of trust you put in us by providing your personal information and therefore we are determined to use the best commercially acceptable means to protect it. But there is no mode of transmission on the internet or electronic storage that is 100% safe, secure and reliable, and we cannot guarantee its absolute security.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Links to Other Sites</strong></h2>\r\n\r\n<p>Our application contains links to other sites. By clicking on these link you will be navigated to their site. These external sites are not operated by us and therefore we strongly recommend you to review their privacy policies. We do not control those sites and accept no responsibility for the content, privacy policies, or practices of any such third-party sites.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Children&rsquo;s Privacy</strong></h2>\r\n\r\n<p>Our services do not address anyone below the age of thirteen. We do not collect any kind of personally identifiable data from children below the age of thirteen. If we find out that a child under the age of 13 has provided us with some personal data, we take immediate action to remove it from our servers.&nbsp;</p>\r\n\r\n<p>If your child has accidentally provided us with personal information, kindly contact us so that we can take all the necessary steps to remove it.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Changes to This Privacy Policy</strong></h2>\r\n\r\n<p>We may update our privacy policy at times. We advise you to review this page at regular intervals to stay informed. In case of any changes in our privacy policy, we will notify you by updating it on this page. Such changes would effective immediately after being posted here.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Contact Us</strong></h2>\r\n\r\n<p>Please feel free to contact us in case of any query or suggestions regarding our privacy policy.</p>\r\n\r\n<p>Phone&nbsp;<a href=\"tel:0123456789\">(+1) 01234 56789</a></p>\r\n\r\n<p>Email:&nbsp;<a href=\"mailto:support@hausdesdoeners.com\">support@hausdesdoeners.com</a></p>', 'cms/191623eb9ad0665388d84053836749a3.jpg', 'cms/073a2758e45168923dcdc80c32304d8e.png', 160, 'en', 1, 1, 1, '2023-01-25 08:25:42', NULL, 'About Us', 'About Us');
INSERT INTO `cms` (`entity_id`, `name`, `CMSSlug`, `description`, `image`, `cms_icon`, `content_id`, `language_slug`, `status`, `created_by`, `updated_by`, `updated_date`, `created_date`, `meta_title`, `meta_description`) VALUES
(29, 'سياسة الخصوصية', 'privacy-policy', '<h1>Privacy Policy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">This Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your information when You use the Service and tells You about Your privacy rights and how the law protects You.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We use Your Personal data to provide and improve the Service. By using the Service, You agree to the collection and use of information in accordance with this Privacy Policy.</p>\r\n\r\n<h1>Interpretation and Definitions</h1>\r\n\r\n<h2 style=\"width: 805.6px;\">Interpretation</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>\r\n\r\n<h2>Definitions</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">For the purposes of this Privacy Policy:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Account</strong>&nbsp;means a unique account created for You to access our Service or parts of our Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Affiliate</strong>&nbsp;means an entity that controls, is controlled by or is under common control with a party, where &quot;control&quot; means ownership of 50% or more of the shares, equity interest or other securities entitled to vote for election of directors or other managing authority.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Application</strong>&nbsp;means the software program provided by the Company downloaded by You on any electronic device, named Haus des Döners Restaurant</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Cookies</strong>&nbsp;are small files that are placed on Your computer, mobile device or any other device by a website, containing the details of Your browsing history on that website among its many uses.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Country</strong>&nbsp;refers to: Gujarat, India</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Device</strong>&nbsp;means any device that can access the Service such as a computer, a cellphone or a digital tablet.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Personal Data</strong>&nbsp;is any information that relates to an identified or identifiable individual.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Service</strong>&nbsp;refers to the Application or the Website or both.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Service Provider</strong>&nbsp;means any natural or legal person who processes the data on behalf of the Company. It refers to third-party companies or individuals employed by the Company to facilitate the Service, to provide the Service on behalf of the Company, to perform services related to the Service or to assist the Company in analyzing how the Service is used.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Third-party Social Media Service</strong>&nbsp;refers to any website or any social network website through which a User can log in or create an account to use the Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Usage Data</strong>&nbsp;refers to data collected automatically, either generated by the use of the Service or from the Service infrastructure itself (for example, the duration of a page visit).</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Website</strong>&nbsp;refers to Haus des Döners Restaurant, accessible from&nbsp;<a href=\"https://www.hausdesdoeners.com/\" rel=\"external nofollow noopener\" target=\"_blank\">https://www.hausdesdoeners.com/</a></p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>You</strong>&nbsp;means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</p>\r\n	</li>\r\n</ul>\r\n\r\n<h1>Collecting and Using Your Personal Data</h1>\r\n\r\n<h2 style=\"width: 805.6px;\">Types of Data Collected</h2>\r\n\r\n<h3>Personal Data</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">While using Our Service, We may ask You to provide Us with certain personally identifiable information that can be used to contact or identify You. Personally identifiable information may include, but is not limited to:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Email address</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">First name and last name</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Phone number</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Address, State, Province, ZIP/Postal code, City</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Usage Data</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3 style=\"width: 805.6px;\">Usage Data</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Usage Data is collected automatically when using the Service.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Usage Data may include information such as Your Device&#39;s Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that You visit, the time and date of Your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">When You access the Service by or through a mobile device, We may collect certain information automatically, including, but not limited to, the type of mobile device You use, Your mobile device unique ID, the IP address of Your mobile device, Your mobile operating system, the type of mobile Internet browser You use, unique device identifiers and other diagnostic data.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We may also collect information that Your browser sends whenever You visit our Service or when You access the Service by or through a mobile device.</p>\r\n\r\n<h3>Information from Third-Party Social Media Services</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company allows You to create an account and log in to use the Service through the following Third-party Social Media Services:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">Google</li>\r\n	<li style=\"margin: 0px;\">Facebook</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">If You decide to register through or otherwise grant us access to a Third-Party Social Media Service, We may collect Personal data that is already associated with Your Third-Party Social Media Service&#39;s account, such as Your name, Your email address, Your activities or Your contact list associated with that account.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">You may also have the option of sharing additional information with the Company through Your Third-Party Social Media Service&#39;s account. If You choose to provide such information and Personal Data, during registration or otherwise, You are giving the Company permission to use, share, and store it in a manner consistent with this Privacy Policy.</p>\r\n\r\n<h3>Information Collected while Using the Application</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">While using Our Application, in order to provide features of Our Application, We may collect, with Your prior permission:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Information regarding your location</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Pictures and other information from your Device&#39;s camera and photo library</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We use this information to provide features of Our Service, to improve and customize Our Service. The information may be uploaded to the Company&#39;s servers and/or a Service Provider&#39;s server or it may be simply stored on Your device.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">You can enable or disable access to this information at any time, through Your Device settings.</p>\r\n\r\n<h3>Tracking Technologies and Cookies</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We use Cookies and similar tracking technologies to track the activity on Our Service and store certain information. Tracking technologies used are beacons, tags, and scripts to collect and track information and to improve and analyze Our Service. The technologies We use may include:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\"><strong>Cookies or Browser Cookies.</strong>&nbsp;A cookie is a small file placed on Your Device. You can instruct Your browser to refuse all Cookies or to indicate when a Cookie is being sent. However, if You do not accept Cookies, You may not be able to use some parts of our Service. Unless you have adjusted Your browser setting so that it will refuse Cookies, our Service may use Cookies.</li>\r\n	<li style=\"margin: 0px;\"><strong>Flash Cookies.</strong>&nbsp;Certain features of our Service may use local stored objects (or Flash Cookies) to collect and store information about Your preferences or Your activity on our Service. Flash Cookies are not managed by the same browser settings as those used for Browser Cookies. For more information on how You can delete Flash Cookies, please read &quot;Where can I change the settings for disabling, or deleting local shared objects?&quot; available at&nbsp;<a href=\"https://helpx.adobe.com/flash-player/kb/disable-local-shared-objects-flash.html#main_Where_can_I_change_the_settings_for_disabling__or_deleting_local_shared_objects_\" rel=\"external nofollow noopener\" target=\"_blank\">https://helpx.adobe.com/flash-player/kb/disable-local-shared-objects-flash.html#main_Where_can_I_change_the_settings_for_disabling__or_deleting_local_shared_objects_</a></li>\r\n	<li style=\"margin: 0px;\"><strong>Web Beacons.</strong>&nbsp;Certain sections of our Service and our emails may contain small electronic files known as web beacons (also referred to as clear gifs, pixel tags, and single-pixel gifs) that permit the Company, for example, to count users who have visited those pages or opened an email and for other related website statistics (for example, recording the popularity of a certain section and verifying system and server integrity).</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Cookies can be &quot;Persistent&quot; or &quot;Session&quot; Cookies. Persistent Cookies remain on Your personal computer or mobile device when You go offline, while Session Cookies are deleted as soon as You close Your web browser.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We use both Session and Persistent Cookies for the purposes set out below:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Necessary / Essential Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Type: Session Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Purpose: These Cookies are essential to provide You with services available through the Website and to enable You to use some of its features. They help to authenticate users and prevent fraudulent use of user accounts. Without these Cookies, the services that You have asked for cannot be provided, and We only use these Cookies to provide You with those services.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Cookies Policy / Notice Acceptance Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Type: Persistent Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Purpose: These Cookies identify if users have accepted the use of cookies on the Website.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Functionality Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Type: Persistent Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Purpose: These Cookies allow us to remember choices You make when You use the Website, such as remembering your login details or language preference. The purpose of these Cookies is to provide You with a more personal experience and to avoid You having to re-enter your preferences every time You use the Website.</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">For more information about the cookies we use and your choices regarding cookies, please visit our Cookies Policy or the Cookies section of our Privacy Policy.</p>\r\n\r\n<h2>Use of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company may use Personal Data for the following purposes:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To provide and maintain our Service</strong>, including to monitor the usage of our Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To manage Your Account:</strong>&nbsp;to manage Your registration as a user of the Service. The Personal Data You provide can give You access to different functionalities of the Service that are available to You as a registered user.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>For the performance of a contract:</strong>&nbsp;the development, compliance and undertaking of the purchase contract for the products, items or services You have purchased or of any other contract with Us through the Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To contact You:</strong>&nbsp;To contact You by email, telephone calls, SMS, or other equivalent forms of electronic communication, such as a mobile application&#39;s push notifications regarding updates or informative communications related to the functionalities, products or contracted services, including the security updates, when necessary or reasonable for their implementation.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To provide You</strong>&nbsp;with news, special offers and general information about other goods, services and events which we offer that are similar to those that you have already purchased or enquired about unless You have opted not to receive such information.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To manage Your requests:</strong>&nbsp;To attend and manage Your requests to Us.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>For business transfers:</strong>&nbsp;We may use Your information to evaluate or conduct a merger, divestiture, restructuring, reorganization, dissolution, or other sale or transfer of some or all of Our assets, whether as a going concern or as part of bankruptcy, liquidation, or similar proceeding, in which Personal Data held by Us about our Service users is among the assets transferred.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>For other purposes</strong>: We may use Your information for other purposes, such as data analysis, identifying usage trends, determining the effectiveness of our promotional campaigns and to evaluate and improve our Service, products, services, marketing and your experience.</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We may share Your personal information in the following situations:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\"><strong>With Service Providers:</strong>&nbsp;We may share Your personal information with Service Providers to monitor and analyze the use of our Service, to contact You.</li>\r\n	<li style=\"margin: 0px;\"><strong>For business transfers:</strong>&nbsp;We may share or transfer Your personal information in connection with, or during negotiations of, any merger, sale of Company assets, financing, or acquisition of all or a portion of Our business to another company.</li>\r\n	<li style=\"margin: 0px;\"><strong>With Affiliates:</strong>&nbsp;We may share Your information with Our affiliates, in which case we will require those affiliates to honor this Privacy Policy. Affiliates include Our parent company and any other subsidiaries, joint venture partners or other companies that We control or that are under common control with Us.</li>\r\n	<li style=\"margin: 0px;\"><strong>With business partners:</strong>&nbsp;We may share Your information with Our business partners to offer You certain products, services or promotions.</li>\r\n	<li style=\"margin: 0px;\"><strong>With other users:</strong>&nbsp;when You share personal information or otherwise interact in the public areas with other users, such information may be viewed by all users and may be publicly distributed outside. If You interact with other users or register through a Third-Party Social Media Service, Your contacts on the Third-Party Social Media Service may see Your name, profile, pictures and description of Your activity. Similarly, other users will be able to view descriptions of Your activity, communicate with You and view Your profile.</li>\r\n	<li style=\"margin: 0px;\"><strong>With Your consent</strong>: We may disclose Your personal information for any other purpose with Your consent.</li>\r\n</ul>\r\n\r\n<h2>Retention of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company will retain Your Personal Data only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use Your Personal Data to the extent necessary to comply with our legal obligations (for example, if we are required to retain your data to comply with applicable laws), resolve disputes, and enforce our legal agreements and policies.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company will also retain Usage Data for internal analysis purposes. Usage Data is generally retained for a shorter period of time, except when this data is used to strengthen the security or to improve the functionality of Our Service, or We are legally obligated to retain this data for longer time periods.</p>\r\n\r\n<h2>Transfer of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Your information, including Personal Data, is processed at the Company&#39;s operating offices and in any other places where the parties involved in the processing are located. It means that this information may be transferred to &mdash; and maintained on &mdash; computers located outside of Your state, province, country or other governmental jurisdiction where the data protection laws may differ than those from Your jurisdiction.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Your consent to this Privacy Policy followed by Your submission of such information represents Your agreement to that transfer.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company will take all steps reasonably necessary to ensure that Your data is treated securely and in accordance with this Privacy Policy and no transfer of Your Personal Data will take place to an organization or a country unless there are adequate controls in place including the security of Your data and other personal information.</p>\r\n\r\n<h2>Disclosure of Your Personal Data</h2>\r\n\r\n<h3>Business Transactions</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">If the Company is involved in a merger, acquisition or asset sale, Your Personal Data may be transferred. We will provide notice before Your Personal Data is transferred and becomes subject to a different Privacy Policy.</p>\r\n\r\n<h3>Law enforcement</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Under certain circumstances, the Company may be required to disclose Your Personal Data if required to do so by law or in response to valid requests by public authorities (e.g. a court or a government agency).</p>\r\n\r\n<h3>Other legal requirements</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company may disclose Your Personal Data in the good faith belief that such action is necessary to:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">Comply with a legal obligation</li>\r\n	<li style=\"margin: 0px;\">Protect and defend the rights or property of the Company</li>\r\n	<li style=\"margin: 0px;\">Prevent or investigate possible wrongdoing in connection with the Service</li>\r\n	<li style=\"margin: 0px;\">Protect the personal safety of Users of the Service or the public</li>\r\n	<li style=\"margin: 0px;\">Protect against legal liability</li>\r\n</ul>\r\n\r\n<h2 style=\"width: 805.6px;\">Security of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The security of Your Personal Data is important to Us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While We strive to use commercially acceptable means to protect Your Personal Data, We cannot guarantee its absolute security.</p>\r\n\r\n<h1>Children&#39;s Privacy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Our Service does not address anyone under the age of 13. We do not knowingly collect personally identifiable information from anyone under the age of 13. If You are a parent or guardian and You are aware that Your child has provided Us with Personal Data, please contact Us. If We become aware that We have collected Personal Data from anyone under the age of 13 without verification of parental consent, We take steps to remove that information from Our servers.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">If We need to rely on consent as a legal basis for processing Your information and Your country requires consent from a parent, We may require Your parent&#39;s consent before We collect and use that information.</p>\r\n\r\n<h1>Links to Other Websites</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Our Service may contain links to other websites that are not operated by Us. If You click on a third party link, You will be directed to that third party&#39;s site. We strongly advise You to review the Privacy Policy of every site You visit.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We have no control over and assume no responsibility for the content, privacy policies or practices of any third party sites or services.</p>\r\n\r\n<h1>Changes to this Privacy Policy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We may update Our Privacy Policy from time to time. We will notify You of any changes by posting the new Privacy Policy on this page.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We will let You know via email and/or a prominent notice on Our Service, prior to the change becoming effective and update the &quot;Last updated&quot; date at the top of this Privacy Policy.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>\r\n\r\n<h1>Contact Us</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">If you have any questions about this Privacy Policy, You can contact us:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">By email: support@hausdesdoeners.com</li>\r\n</ul>', 'cms/b1fb1a8640d35c12ae63b369116c99a2.png', 'cms/64a6907337c122e7ce4f05227406aa99.png', 162, 'ar', 1, 1, 1, '2023-01-24 10:31:50', NULL, 'سياسة الخصوصية', '');
INSERT INTO `cms` (`entity_id`, `name`, `CMSSlug`, `description`, `image`, `cms_icon`, `content_id`, `language_slug`, `status`, `created_by`, `updated_by`, `updated_date`, `created_date`, `meta_title`, `meta_description`) VALUES
(30, 'Politique de confidentialité', 'privacy-policy', '<h1>Privacy Policy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">This Privacy Policy describes Our policies and procedures on the collection, use and disclosure of Your information when You use the Service and tells You about Your privacy rights and how the law protects You.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We use Your Personal data to provide and improve the Service. By using the Service, You agree to the collection and use of information in accordance with this Privacy Policy.</p>\r\n\r\n<h1>Interpretation and Definitions</h1>\r\n\r\n<h2 style=\"width: 805.6px;\">Interpretation</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The words of which the initial letter is capitalized have meanings defined under the following conditions. The following definitions shall have the same meaning regardless of whether they appear in singular or in plural.</p>\r\n\r\n<h2>Definitions</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">For the purposes of this Privacy Policy:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Account</strong>&nbsp;means a unique account created for You to access our Service or parts of our Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Affiliate</strong>&nbsp;means an entity that controls, is controlled by or is under common control with a party, where &quot;control&quot; means ownership of 50% or more of the shares, equity interest or other securities entitled to vote for election of directors or other managing authority.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Application</strong>&nbsp;means the software program provided by the Company downloaded by You on any electronic device, named Haus des Döners Restaurant</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Cookies</strong>&nbsp;are small files that are placed on Your computer, mobile device or any other device by a website, containing the details of Your browsing history on that website among its many uses.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Country</strong>&nbsp;refers to: Gujarat, India</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Device</strong>&nbsp;means any device that can access the Service such as a computer, a cellphone or a digital tablet.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Personal Data</strong>&nbsp;is any information that relates to an identified or identifiable individual.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Service</strong>&nbsp;refers to the Application or the Website or both.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Service Provider</strong>&nbsp;means any natural or legal person who processes the data on behalf of the Company. It refers to third-party companies or individuals employed by the Company to facilitate the Service, to provide the Service on behalf of the Company, to perform services related to the Service or to assist the Company in analyzing how the Service is used.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Third-party Social Media Service</strong>&nbsp;refers to any website or any social network website through which a User can log in or create an account to use the Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Usage Data</strong>&nbsp;refers to data collected automatically, either generated by the use of the Service or from the Service infrastructure itself (for example, the duration of a page visit).</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Website</strong>&nbsp;refers to Haus des Döners Restaurant, accessible from&nbsp;<a href=\"https://www.hausdesdoeners.com/\" rel=\"external nofollow noopener\" target=\"_blank\">https://www.hausdesdoeners.com/</a></p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>You</strong>&nbsp;means the individual accessing or using the Service, or the company, or other legal entity on behalf of which such individual is accessing or using the Service, as applicable.</p>\r\n	</li>\r\n</ul>\r\n\r\n<h1>Collecting and Using Your Personal Data</h1>\r\n\r\n<h2 style=\"width: 805.6px;\">Types of Data Collected</h2>\r\n\r\n<h3>Personal Data</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">While using Our Service, We may ask You to provide Us with certain personally identifiable information that can be used to contact or identify You. Personally identifiable information may include, but is not limited to:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Email address</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">First name and last name</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Phone number</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Address, State, Province, ZIP/Postal code, City</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Usage Data</p>\r\n	</li>\r\n</ul>\r\n\r\n<h3 style=\"width: 805.6px;\">Usage Data</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Usage Data is collected automatically when using the Service.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Usage Data may include information such as Your Device&#39;s Internet Protocol address (e.g. IP address), browser type, browser version, the pages of our Service that You visit, the time and date of Your visit, the time spent on those pages, unique device identifiers and other diagnostic data.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">When You access the Service by or through a mobile device, We may collect certain information automatically, including, but not limited to, the type of mobile device You use, Your mobile device unique ID, the IP address of Your mobile device, Your mobile operating system, the type of mobile Internet browser You use, unique device identifiers and other diagnostic data.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We may also collect information that Your browser sends whenever You visit our Service or when You access the Service by or through a mobile device.</p>\r\n\r\n<h3>Information from Third-Party Social Media Services</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company allows You to create an account and log in to use the Service through the following Third-party Social Media Services:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">Google</li>\r\n	<li style=\"margin: 0px;\">Facebook</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">If You decide to register through or otherwise grant us access to a Third-Party Social Media Service, We may collect Personal data that is already associated with Your Third-Party Social Media Service&#39;s account, such as Your name, Your email address, Your activities or Your contact list associated with that account.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">You may also have the option of sharing additional information with the Company through Your Third-Party Social Media Service&#39;s account. If You choose to provide such information and Personal Data, during registration or otherwise, You are giving the Company permission to use, share, and store it in a manner consistent with this Privacy Policy.</p>\r\n\r\n<h3>Information Collected while Using the Application</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">While using Our Application, in order to provide features of Our Application, We may collect, with Your prior permission:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Information regarding your location</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Pictures and other information from your Device&#39;s camera and photo library</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We use this information to provide features of Our Service, to improve and customize Our Service. The information may be uploaded to the Company&#39;s servers and/or a Service Provider&#39;s server or it may be simply stored on Your device.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">You can enable or disable access to this information at any time, through Your Device settings.</p>\r\n\r\n<h3>Tracking Technologies and Cookies</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We use Cookies and similar tracking technologies to track the activity on Our Service and store certain information. Tracking technologies used are beacons, tags, and scripts to collect and track information and to improve and analyze Our Service. The technologies We use may include:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\"><strong>Cookies or Browser Cookies.</strong>&nbsp;A cookie is a small file placed on Your Device. You can instruct Your browser to refuse all Cookies or to indicate when a Cookie is being sent. However, if You do not accept Cookies, You may not be able to use some parts of our Service. Unless you have adjusted Your browser setting so that it will refuse Cookies, our Service may use Cookies.</li>\r\n	<li style=\"margin: 0px;\"><strong>Flash Cookies.</strong>&nbsp;Certain features of our Service may use local stored objects (or Flash Cookies) to collect and store information about Your preferences or Your activity on our Service. Flash Cookies are not managed by the same browser settings as those used for Browser Cookies. For more information on how You can delete Flash Cookies, please read &quot;Where can I change the settings for disabling, or deleting local shared objects?&quot; available at&nbsp;<a href=\"https://helpx.adobe.com/flash-player/kb/disable-local-shared-objects-flash.html#main_Where_can_I_change_the_settings_for_disabling__or_deleting_local_shared_objects_\" rel=\"external nofollow noopener\" target=\"_blank\">https://helpx.adobe.com/flash-player/kb/disable-local-shared-objects-flash.html#main_Where_can_I_change_the_settings_for_disabling__or_deleting_local_shared_objects_</a></li>\r\n	<li style=\"margin: 0px;\"><strong>Web Beacons.</strong>&nbsp;Certain sections of our Service and our emails may contain small electronic files known as web beacons (also referred to as clear gifs, pixel tags, and single-pixel gifs) that permit the Company, for example, to count users who have visited those pages or opened an email and for other related website statistics (for example, recording the popularity of a certain section and verifying system and server integrity).</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Cookies can be &quot;Persistent&quot; or &quot;Session&quot; Cookies. Persistent Cookies remain on Your personal computer or mobile device when You go offline, while Session Cookies are deleted as soon as You close Your web browser.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We use both Session and Persistent Cookies for the purposes set out below:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Necessary / Essential Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Type: Session Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Purpose: These Cookies are essential to provide You with services available through the Website and to enable You to use some of its features. They help to authenticate users and prevent fraudulent use of user accounts. Without these Cookies, the services that You have asked for cannot be provided, and We only use these Cookies to provide You with those services.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Cookies Policy / Notice Acceptance Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Type: Persistent Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Purpose: These Cookies identify if users have accepted the use of cookies on the Website.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>Functionality Cookies</strong></p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Type: Persistent Cookies</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Administered by: Us</p>\r\n\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Purpose: These Cookies allow us to remember choices You make when You use the Website, such as remembering your login details or language preference. The purpose of these Cookies is to provide You with a more personal experience and to avoid You having to re-enter your preferences every time You use the Website.</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">For more information about the cookies we use and your choices regarding cookies, please visit our Cookies Policy or the Cookies section of our Privacy Policy.</p>\r\n\r\n<h2>Use of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company may use Personal Data for the following purposes:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To provide and maintain our Service</strong>, including to monitor the usage of our Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To manage Your Account:</strong>&nbsp;to manage Your registration as a user of the Service. The Personal Data You provide can give You access to different functionalities of the Service that are available to You as a registered user.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>For the performance of a contract:</strong>&nbsp;the development, compliance and undertaking of the purchase contract for the products, items or services You have purchased or of any other contract with Us through the Service.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To contact You:</strong>&nbsp;To contact You by email, telephone calls, SMS, or other equivalent forms of electronic communication, such as a mobile application&#39;s push notifications regarding updates or informative communications related to the functionalities, products or contracted services, including the security updates, when necessary or reasonable for their implementation.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To provide You</strong>&nbsp;with news, special offers and general information about other goods, services and events which we offer that are similar to those that you have already purchased or enquired about unless You have opted not to receive such information.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>To manage Your requests:</strong>&nbsp;To attend and manage Your requests to Us.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>For business transfers:</strong>&nbsp;We may use Your information to evaluate or conduct a merger, divestiture, restructuring, reorganization, dissolution, or other sale or transfer of some or all of Our assets, whether as a going concern or as part of bankruptcy, liquidation, or similar proceeding, in which Personal Data held by Us about our Service users is among the assets transferred.</p>\r\n	</li>\r\n	<li style=\"margin: 0px;\">\r\n	<p style=\"line-height: 2; padding: 0px; margin: 0px;\"><strong>For other purposes</strong>: We may use Your information for other purposes, such as data analysis, identifying usage trends, determining the effectiveness of our promotional campaigns and to evaluate and improve our Service, products, services, marketing and your experience.</p>\r\n	</li>\r\n</ul>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We may share Your personal information in the following situations:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\"><strong>With Service Providers:</strong>&nbsp;We may share Your personal information with Service Providers to monitor and analyze the use of our Service, to contact You.</li>\r\n	<li style=\"margin: 0px;\"><strong>For business transfers:</strong>&nbsp;We may share or transfer Your personal information in connection with, or during negotiations of, any merger, sale of Company assets, financing, or acquisition of all or a portion of Our business to another company.</li>\r\n	<li style=\"margin: 0px;\"><strong>With Affiliates:</strong>&nbsp;We may share Your information with Our affiliates, in which case we will require those affiliates to honor this Privacy Policy. Affiliates include Our parent company and any other subsidiaries, joint venture partners or other companies that We control or that are under common control with Us.</li>\r\n	<li style=\"margin: 0px;\"><strong>With business partners:</strong>&nbsp;We may share Your information with Our business partners to offer You certain products, services or promotions.</li>\r\n	<li style=\"margin: 0px;\"><strong>With other users:</strong>&nbsp;when You share personal information or otherwise interact in the public areas with other users, such information may be viewed by all users and may be publicly distributed outside. If You interact with other users or register through a Third-Party Social Media Service, Your contacts on the Third-Party Social Media Service may see Your name, profile, pictures and description of Your activity. Similarly, other users will be able to view descriptions of Your activity, communicate with You and view Your profile.</li>\r\n	<li style=\"margin: 0px;\"><strong>With Your consent</strong>: We may disclose Your personal information for any other purpose with Your consent.</li>\r\n</ul>\r\n\r\n<h2>Retention of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company will retain Your Personal Data only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use Your Personal Data to the extent necessary to comply with our legal obligations (for example, if we are required to retain your data to comply with applicable laws), resolve disputes, and enforce our legal agreements and policies.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company will also retain Usage Data for internal analysis purposes. Usage Data is generally retained for a shorter period of time, except when this data is used to strengthen the security or to improve the functionality of Our Service, or We are legally obligated to retain this data for longer time periods.</p>\r\n\r\n<h2>Transfer of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Your information, including Personal Data, is processed at the Company&#39;s operating offices and in any other places where the parties involved in the processing are located. It means that this information may be transferred to &mdash; and maintained on &mdash; computers located outside of Your state, province, country or other governmental jurisdiction where the data protection laws may differ than those from Your jurisdiction.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Your consent to this Privacy Policy followed by Your submission of such information represents Your agreement to that transfer.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company will take all steps reasonably necessary to ensure that Your data is treated securely and in accordance with this Privacy Policy and no transfer of Your Personal Data will take place to an organization or a country unless there are adequate controls in place including the security of Your data and other personal information.</p>\r\n\r\n<h2>Disclosure of Your Personal Data</h2>\r\n\r\n<h3>Business Transactions</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">If the Company is involved in a merger, acquisition or asset sale, Your Personal Data may be transferred. We will provide notice before Your Personal Data is transferred and becomes subject to a different Privacy Policy.</p>\r\n\r\n<h3>Law enforcement</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Under certain circumstances, the Company may be required to disclose Your Personal Data if required to do so by law or in response to valid requests by public authorities (e.g. a court or a government agency).</p>\r\n\r\n<h3>Other legal requirements</h3>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The Company may disclose Your Personal Data in the good faith belief that such action is necessary to:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">Comply with a legal obligation</li>\r\n	<li style=\"margin: 0px;\">Protect and defend the rights or property of the Company</li>\r\n	<li style=\"margin: 0px;\">Prevent or investigate possible wrongdoing in connection with the Service</li>\r\n	<li style=\"margin: 0px;\">Protect the personal safety of Users of the Service or the public</li>\r\n	<li style=\"margin: 0px;\">Protect against legal liability</li>\r\n</ul>\r\n\r\n<h2 style=\"width: 805.6px;\">Security of Your Personal Data</h2>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">The security of Your Personal Data is important to Us, but remember that no method of transmission over the Internet, or method of electronic storage is 100% secure. While We strive to use commercially acceptable means to protect Your Personal Data, We cannot guarantee its absolute security.</p>\r\n\r\n<h1>Children&#39;s Privacy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Our Service does not address anyone under the age of 13. We do not knowingly collect personally identifiable information from anyone under the age of 13. If You are a parent or guardian and You are aware that Your child has provided Us with Personal Data, please contact Us. If We become aware that We have collected Personal Data from anyone under the age of 13 without verification of parental consent, We take steps to remove that information from Our servers.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">If We need to rely on consent as a legal basis for processing Your information and Your country requires consent from a parent, We may require Your parent&#39;s consent before We collect and use that information.</p>\r\n\r\n<h1>Links to Other Websites</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">Our Service may contain links to other websites that are not operated by Us. If You click on a third party link, You will be directed to that third party&#39;s site. We strongly advise You to review the Privacy Policy of every site You visit.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We have no control over and assume no responsibility for the content, privacy policies or practices of any third party sites or services.</p>\r\n\r\n<h1>Changes to this Privacy Policy</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We may update Our Privacy Policy from time to time. We will notify You of any changes by posting the new Privacy Policy on this page.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">We will let You know via email and/or a prominent notice on Our Service, prior to the change becoming effective and update the &quot;Last updated&quot; date at the top of this Privacy Policy.</p>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>\r\n\r\n<h1>Contact Us</h1>\r\n\r\n<p style=\"line-height: 2; padding: 0px; margin: 0px;\">If you have any questions about this Privacy Policy, You can contact us:</p>\r\n\r\n<ul>\r\n	<li style=\"margin: 0px;\">By email: support@hausdesdoeners.com</li>\r\n</ul>', 'cms/286943894f694d8283532c1df25924b8.png', 'cms/3d5f8967f637dceb2e3ede40d1469f3f.png', 162, 'fr', 1, 1, 1, '2023-01-24 10:31:23', NULL, 'Politique de confidentialité', ''),
(31, 'Termes et conditions', 'terms-and-conditions', '<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>Haus des Döners Restaurant</b>&nbsp;an Open Source Application and it is available for the usage of the end user for no cost at all. By choosing to use our service, you agree to our Privacy Policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">This page intends to provide our visitors with information regarding our policies on the collection, use, and disclosure of their personal information, in case they decide to use our service. All the information collected by us is used to provide and improve our service. Your personal information will not be used or shared with any third party, except as described in this Privacy Policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">The terms used in this privacy policy have the same meanings as in our Terms and Conditions, which is accessible at Haus des Döners unless otherwise defined in this Privacy Policy.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Information Collection and Use</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To provide you with a great user experience, while using our service, we may ask you to provide us with your personal information. The information collected will be retained by us and will be used only as stated in this privacy policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Haus des Döners also uses third-party services which may collect your personal information and use it for your identification.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Please find below the link to the privacy policy of our third-party service provider:<br />\r\nGoogle Play Services&nbsp;</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Log Data</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">While using our services if an unexpected error occurs, we collect data (through third-party products) from your phone which is known as &lsquo;Log Data.&rsquo; The collected data may include information like your IP address, the name of your device, operating system version, the time and date when the service is used, the configuration of the app when utilizing our Service, and the configuration of the application while utilizing our service and other such statistics.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Cookies</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Cookies are archives stored on your device with the small amount of information about the user and the website, it can be used as anonymous unique identifiers. These archives are sent to your browser from the websites visited by you and are stored on the internal memory of your device.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Haus des Döners does not use these cookies explicitly. However, it does uses third-party codes and libraries, which may use cookies to gather information to improve their service and user experience. You can choose to accept or decline these cookies and be in control of when and how these cookies are being used. If you refuse to use cookies by Haus des Döners, you might not be able to use some parts of the service.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Service Providers</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">We may utilize third-party companies and individuals for the following:</span></p>\r\n\r\n<ul class=\"ul1\" style=\"color: rgb(0, 0, 0); font-size: medium; text-align: start;\">\r\n	<li class=\"li2\" style=\"margin: 0px; text-align: justify; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Helvetica; color: rgb(38, 38, 38); -webkit-text-stroke: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To aid our service</span></li>\r\n	<li class=\"li2\" style=\"margin: 0px; text-align: justify; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Helvetica; color: rgb(38, 38, 38); -webkit-text-stroke: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To deliver the service on behalf of us</span></li>\r\n	<li class=\"li2\" style=\"margin: 0px; text-align: justify; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Helvetica; color: rgb(38, 38, 38); -webkit-text-stroke: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To execute related services</span></li>\r\n	<li class=\"li2\" style=\"margin: 0px; text-align: justify; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; font-size: 14px; line-height: normal; font-family: Helvetica; color: rgb(38, 38, 38); -webkit-text-stroke: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To support us in evaluating how our service is being used.</span></li>\r\n</ul>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">We want our users to be well informed that these third-parties have access to your personal data. They collect and use your information to perform the various tasks assigned to them, on our behalf. However, they are obligated not to use this information for any other purpose, and not to disclose it with others.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300))); \"><b>Security</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">We appreciate and value the level of trust you put in us by providing your personal information and therefore we are determined to use the best commercially acceptable means to protect it. But there is no mode of transmission on the internet or electronic storage that is 100% safe, secure and reliable, and we cannot guarantee its absolute security.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Links to Other Sites</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Our application contains links to other sites. By clicking on these link you will be navigated to their site. These external sites are not operated by us and therefore we strongly recommend you to review their privacy policies. We do not control those sites and accept no responsibility for the content, privacy policies, or practices of any such third-party sites.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Children&rsquo;s Privacy</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Our services do not address anyone below the age of thirteen. We do not collect any kind of personally identifiable data from children below the age of thirteen. If we find out that a child under the age of 13 has provided us with some personal data, we take immediate action to remove it from our servers.&nbsp;</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">If your child has accidentally provided us with personal information, kindly contact us so that we can take all the necessary steps to remove it.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Changes to This Privacy Policy</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">We may update our privacy policy at times. We advise you to review this page at regular intervals to stay informed. In case of any changes in our privacy policy, we will notify you by updating it on this page. Such changes would effective immediately after being posted here.</span></p>\r\n\r\n<h2 class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none; font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><b>Contact Us</b></span></h2>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Please feel free to contact us in case of any query or suggestions regarding our privacy policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Phone:&nbsp;<a href=\"tel:0123456789\">(+1) 012345 6789</a></span></p>\r\n\r\n<p class=\"p3\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(16, 109, 180); margin: 0px 0px 14px; color: rgb(16, 109, 180);\"><span class=\"s2\" style=\"font-kerning: none; color: rgb(38, 38, 38); -webkit-text-stroke-color: rgb(38, 38, 38);\">Email:&nbsp;<a href=\"mailto:support@hausdesdoeners.com\"><span class=\"s3\" style=\"font-kerning: none; color: rgb(16, 109, 180); -webkit-text-stroke-color: rgb(16, 109, 180);\">support@hausdesdoeners.com</span></a>.&nbsp;</span></p>\r\n\r\n<p class=\"p3\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(16, 109, 180); margin: 0px 0px 14px; color: rgb(16, 109, 180);\">&nbsp;</p>', 'cms/7f9b43805cb723bce87f8d513c2ea48d.jpg', 'cms/f24f8376e0738173f7393d4bf88a13c8.png', 163, 'fr', 0, 1, 1, '2023-01-24 10:34:24', NULL, 'Termes et conditions', ''),
(32, 'الأحكام والشروط', 'terms-and-conditions', '<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>&nbsp;Contact Us</strong></h2>\r\n\r\n<p>Please feel free to contact us in case of any queries or suggestions regarding our privacy policy.</p>\r\n\r\n<p>Phone:<a href=\"tel:0123456789\">(+1) 012345 6789</a></p>\r\n\r\n<p>Email:<a href=\"mailto:support@hausdesdoeners.com\" style=\"background-color: var(--highlight-bg); white-space: pre-wrap;\">support@hausdesdoeners.com</a><span style=\"background-color: var(--highlight-bg); color: var(--highlight-color); white-space: pre-wrap;\">.&nbsp;</span></p>\r\n\r\n<p class=\"p3\" style=\"margin: 0px 0px 14px; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; color: rgb(16, 109, 180); -webkit-text-stroke-color: rgb(16, 109, 180);\">&nbsp;</p>', 'cms/45f752288f37cf6ef3c163de249ff7d3.jpg', 'cms/e36d10a4571b236936ce0cf56db53c65.png', 163, 'ar', 0, 1, 1, '2023-01-24 10:36:09', NULL, 'الأحكام والشروط', ''),
(33, 'اتصل بنا', 'contact-us', '<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / 1620));\"><strong>&nbsp;Contact Us</strong></h2>\r\n\r\n<p>Please feel free to contact us in case of any queries or suggestions regarding our privacy policy.</p>\r\n\r\n<p>Phone:<a href=\"tel:0123456789\">(+1) 01234 56789</a></p>\r\n\r\n<p>Email:<a href=\"mailto:support@hausdesdoeners.com\" style=\"background-color: var(--highlight-bg); white-space: pre-wrap;\">support@hausdesdoeners.com</a><span style=\"background-color: var(--highlight-bg); color: var(--highlight-color); white-space: pre-wrap;\">.&nbsp;</span></p>', 'cms/d6d949f37d60688e8b7c2d14a433d22f.jpg', 'cms/ff60b5dd486c2d4c137fdafaa27b5bab.png', 161, 'ar', 1, 1, 1, '2022-08-10 06:51:30', NULL, 'اتصل بنا', ''),
(34, 'Nous contacter', 'contact-us', '<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>&nbsp;Contact Us</strong></h2>\r\n\r\n<p>Please feel free to contact us in case of any queries or suggestions regarding our privacy policy.</p>\r\n\r\n<p>Phone:<a href=\"tel:0123456789\">(+1) 012345 6789</a></p>\r\n\r\n<p>Email:<a href=\"mailto:support@hausdesdoeners.com\" style=\"background-color: var(--highlight-bg); white-space: pre-wrap;\">support@hausdesdoeners.com</a><span style=\"background-color: var(--highlight-bg); color: var(--highlight-color); white-space: pre-wrap;\">.&nbsp;</span></p>\r\n\r\n<p class=\"p3\" style=\"margin: 0px 0px 14px; font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; color: rgb(16, 109, 180); -webkit-text-stroke-color: rgb(16, 109, 180);\">&nbsp;</p>', 'cms/22af71beb940624e09644e63f68bb018.jpg', 'cms/5f3638eb7c8dd987d937aa4f7d28a9c4.png', 161, 'fr', 1, 1, 1, '2023-01-24 10:35:55', NULL, 'Nous contacter', ''),
(35, 'Login with Facebook', 'login-with-facebook', '<p>&quot;Login with Facebook&quot; is the button that allows Facebook users to use their accounts to log in at or create accounts with www.hausdesdoeners.com website and Mobile APP.</p>\r\n\r\n<p>We have provided the facility to you the option to log out or delete your account entirely. You can delete you account from the website and mobile app. After login, there is a button to delete your account from www.hausdesdoeners.com. We are deleting all your personal datas.</p>\r\n\r\n<p>Contact</p>\r\n\r\n<p>E-mail: support@hausdesdoeners.com</p>\r\n\r\n<p>Telefon:0123456789</p>', NULL, 'cms/d888e1ea7664ddde4d57fd368c6487fd.png', 2194, 'en', 1, 1, 1, '2023-01-24 10:43:02', NULL, 'Login with Facebook', ''),
(36, 'Se connecter avec Facebook', 'login-with-facebook', '<p>Connexion avec Facebook &raquo; est le bouton qui permet aux utilisateurs de Facebook d&#39;utiliser leurs comptes pour se connecter ou cr&eacute;er des comptes avec le site Web et l&#39;application mobile&nbsp; www.hausdesdoeners.com.</p>\r\n\r\n<p>Nous vous avons fourni la possibilit&eacute; de vous d&eacute;connecter ou de supprimer compl&egrave;tement votre compte. Vous pouvez supprimer votre compte du site Web et de l&#39;application mobile. Apr&egrave;s la connexion, il y a un bouton pour supprimer votre compte de www.hausdesdoeners.com. Nous supprimons toutes vos donn&eacute;es personnelles.</p>\r\n\r\n<p>Contact</p>\r\n\r\n<p>Courriel : support@hausdesdoeners.com</p>\r\n\r\n<p>T&eacute;l&eacute;phone : 0123456789</p>', NULL, 'cms/4fc768544f1e1a7f1a58c9356ae9754d.png', 2194, 'fr', 1, 1, 1, '2023-01-24 10:45:47', NULL, 'Se connecter avec Facebook', ''),
(37, 'تسجيل الدخول باستخدام الفيسبوك', 'login-with-facebook', '<p>&quot;تسجيل الدخول باستخدام Facebook&quot; هو الزر الذي يسمح لمستخدمي Facebook باستخدام حساباتهم لتسجيل الدخول أو إنشاء حسابات باستخدام موقع ويب www.hausdesdoeners.com وتطبيق الهاتف المحمول.</p>\r\n\r\n<p>لقد قدمنا ​​لك الخيار لتسجيل الخروج أو حذف حسابك بالكامل. يمكنك حذف حسابك من الموقع الإلكتروني وتطبيق الهاتف. بعد تسجيل الدخول ، يوجد زر لحذف حسابك من www.hausdesdoeners.com. نحن نحذف جميع بياناتك الشخصية.</p>\r\n\r\n<p>اتصال</p>\r\n\r\n<p>البريد الإلكتروني: support@hausdesdoeners.com</p>\r\n\r\n<p>هاتف: 0123456789</p>', NULL, 'cms/ebaf6ab0f13f299e7787fe297593b4a6.png', 2194, 'ar', 1, 1, 1, '2023-01-24 10:46:52', NULL, 'تسجيل الدخول باستخدام الفيسبوك', '');
INSERT INTO `cms` (`entity_id`, `name`, `CMSSlug`, `description`, `image`, `cms_icon`, `content_id`, `language_slug`, `status`, `created_by`, `updated_by`, `updated_date`, `created_date`, `meta_title`, `meta_description`) VALUES
(38, 'À propos de nous', 'about-us', '<p><strong>Haus des Döners Restaurant</strong>&nbsp;an Open Source Application and it is available for the usage of the end user for no cost at all. By choosing to use our service, you agree to our Privacy Policy.</p>\r\n\r\n<p>This page intends to provide our visitors with information regarding our policies on the collection, use, and disclosure of their personal information, in case they decide to use our service. All the information collected by us is used to provide and improve our service. Your personal information will not be used or shared with any third party, except as described in this Privacy Policy.</p>\r\n\r\n<p>The terms used in this privacy policy have the same meanings as in our Terms and Conditions, which is accessible at Haus des Döners unless otherwise defined in this Privacy Policy.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Information Collection and Use</strong></h2>\r\n\r\n<p>To provide you with a great user experience, while using our service, we may ask you to provide us with your personal information. The information collected will be retained by us and will be used only as stated in this privacy policy.</p>\r\n\r\n<p>Haus des Döners also uses third-party services which may collect your personal information and use it for your identification.</p>\r\n\r\n<p>Please find below the link to the privacy policy of our third-party service provider:<br />\r\nGoogle Play Services&nbsp;</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Log Data</strong></h2>\r\n\r\n<p>While using our services if an unexpected error occurs, we collect data (through third-party products) from your phone which is known as &lsquo;Log Data.&rsquo; The collected data may include information like your IP address, the name of your device, operating system version, the time and date when the service is used, the configuration of the app when utilizing our Service, and the configuration of the application while utilizing our service and other such statistics.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Cookies</strong></h2>\r\n\r\n<p>Cookies are archives stored on your device with the small amount of information about the user and the website, it can be used as anonymous unique identifiers. These archives are sent to your browser from the websites visited by you and are stored on the internal memory of your device.</p>\r\n\r\n<p>Haus des Döners does not use these cookies explicitly. However, it does uses third-party codes and libraries, which may use cookies to gather information to improve their service and user experience. You can choose to accept or decline these cookies and be in control of when and how these cookies are being used. If you refuse to use cookies by Haus des Döners, you might not be able to use some parts of the service.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Service Providers</strong></h2>\r\n\r\n<p>We may utilize third-party companies and individuals for the following:</p>\r\n\r\n<ul>\r\n	<li>To aid our service</li>\r\n	<li>To deliver the service on behalf of us</li>\r\n	<li>To execute related services</li>\r\n	<li>To support us in evaluating how our service is being used.</li>\r\n</ul>\r\n\r\n<p>We want our users to be well informed that these third-parties have access to your personal data. They collect and use your information to perform the various tasks assigned to them, on our behalf. However, they are obligated not to use this information for any other purpose, and not to disclose it with others.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Security</strong></h2>\r\n\r\n<p>We appreciate and value the level of trust you put in us by providing your personal information and therefore we are determined to use the best commercially acceptable means to protect it. But there is no mode of transmission on the internet or electronic storage that is 100% safe, secure and reliable, and we cannot guarantee its absolute security.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Links to Other Sites</strong></h2>\r\n\r\n<p>Our application contains links to other sites. By clicking on these link you will be navigated to their site. These external sites are not operated by us and therefore we strongly recommend you to review their privacy policies. We do not control those sites and accept no responsibility for the content, privacy policies, or practices of any such third-party sites.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Children&rsquo;s Privacy</strong></h2>\r\n\r\n<p>Our services do not address anyone below the age of thirteen. We do not collect any kind of personally identifiable data from children below the age of thirteen. If we find out that a child under the age of 13 has provided us with some personal data, we take immediate action to remove it from our servers.&nbsp;</p>\r\n\r\n<p>If your child has accidentally provided us with personal information, kindly contact us so that we can take all the necessary steps to remove it.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Changes to This Privacy Policy</strong></h2>\r\n\r\n<p>We may update our privacy policy at times. We advise you to review this page at regular intervals to stay informed. In case of any changes in our privacy policy, we will notify you by updating it on this page. Such changes would effective immediately after being posted here.</p>\r\n\r\n<h2 style=\"font-size: calc(14px + 6 * ((100vw - 300px) / (1920 - 300)));\"><strong>Contact Us</strong></h2>\r\n\r\n<p>Please feel free to contact us in case of any query or suggestions regarding our privacy policy.</p>\r\n\r\n<p>Phone&nbsp;<a href=\"tel:0123456789\">(+1) 01234 56789</a></p>\r\n\r\n<p>Email:&nbsp;<a href=\"mailto:support@hausdesdoeners.com\">support@hausdesdoeners.com</a></p>', NULL, 'cms/6d74a1a10acbf8053fe5e759500a8110.png', 160, 'fr', 1, 1, 1, '2023-01-24 10:38:38', NULL, 'À propos de nous', ''),
(39, 'معلومات عنا', 'about-us', '<p>التزامنا<br />\r\n&quot;في عالم يقترب بشكل متزايد ، نسعى لتعزيز تناغم الطعام على عتبة الباب والمطبخ والهندسة المعمارية. كمنصة سريعة النمو لتوصيل الطعام في مختلف البلدان ، فإننا نلتزم بالوفاء بوعدنا بتقديم الأفضل للعالم.<br />\r\nمن نحن<br />\r\nwww.xx.hausdesdoeners عبارة عن منصة ملتزمة تعزز قدرات Haus des Döners من حيث الميزات والوظائف والأمان. www.xx.hausdesdoeners مفتوح للعملاء الراغبين في الاستمتاع بخدمة مطاعمهم المفضلة في المناطق المجاورة لهم.<br />\r\nرؤية<br />\r\nلخلق تجربة لا مثيل لها من خلال الحفاظ على أعلى معايير الجودة والنظافة والخدمة ورضا العملاء.<br />\r\nبعثة<br />\r\nلتقديم قيمة مضافة بشكل متكرر في جميع المجالات وبالتالي تحفيز ولاء المستفيدين. لالتقاط تجربة العلامة التجارية وإعادة إنشائها وتكرارها في أكبر عدد ممكن من المجالات.</p>', NULL, 'cms/5f7566223b8636b99d6b9c7428c78f65.png', 160, 'ar', 1, 1, 1, '2023-01-24 10:40:54', NULL, 'معلومات عنا', ''),
(40, 'Cookie Policy', 'cookie-policy', '<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><a href=\"https://www.hausdesdoeners.com\">Haus des Döners</a> and its affiliates (collectively, Haus des Döners, &ldquo;Us&rdquo; or &ldquo;We&rdquo;) understand that your privacy is important to you and are committed to being recognized by the technology you use. This Cookie Policy states that cookies, web beacons, pixels, explicit gifs, and other similar technologies (including &ldquo;Cookies and other technologies&rdquo;) may be stored and accessed from your device when you use or visit any website or product app by sending a link of this Policy (collectively, &ldquo;<a href=\"https://www.hausdesdoeners.com\">Haus des Döners</a>&rdquo;). This Cookie Policy should be read along with our <a href=\"https://hausdesdoeners.com/privacy-policy/\">Privacy Policy</a> and <a href=\"https://hausdesdoeners.com/terms-conditions/\">Terms and Conditions</a>.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>What are cookies?</b></span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">A cookie is a small text file that can be stored &amp; retrieved from your device when you visit one of our Product Site. Some tracking alogorithms work similarly to cookies and place small data files on your device or monitor your website activity so that we can collect information about how you consume our site and products. This allows our sites and products to see your device from some of the Site&rsquo;s users. The information provided here regarding cookies also applies to other tracking algorithms</span>.</p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>How do </b></span><strong>Haus des Döners</strong><span class=\"s1\" style=\"font-kerning: none;\"><b> sites use cookies and other tracking alogorithms?</b></span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Haus des Döners uses cookies and other tracking technologies to identify you and your interests, remember your preferences, and track your use of our sites. We also use cookies and other tracking algorithms to control access to certain content on our Sites, to protect our sites, and to process any requests you make about us.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">To manage our Sites and research purposes, Haus des Döners also has an agreement with third party service providers to track and analyze the use of statistics and volume information from our users of the Site. These third-party providers use persistent cookies to help us improve manage our Site content, user experience and analyze how users behave while accesing various parts of the product pages.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>First and Third Cookies</b></span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">&ldquo;First group cookies&rdquo; are Haus des Döners cookies and that Haus des Döners puts on your device. &ldquo;Third party cookies&rdquo; are cookies logged by Third Parties on your device through our Site. Haus des Döners may allow third-party service providers to send emails to users who have provided us with their contact information. To help measure and improve the performance of our email communications, and / or to determine if messages are turned on and that links are clicked, third-party service providers may set cookies on these users&rsquo; devices.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">For more information about how these companies collect and use information on our behalf, please see their privacy policy.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>We use the following types of cookies:</b></span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>Continuous Cookies</b>: We use persistent cookies to improve your experience of using ou product pages. This includes logging your acceptance of our Cookie Policy to delete the cookie message that appears first when you use the Haus des Döners Products Site.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>Session Cookies</b>: These cookies are temporary and are removed from your machine when your web browser is closed. Haus des Döners use session cookies to help us track online usage on our product pages.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">You may refuse to accept browser cookies by activating the appropriate settings in your respective browser. Unless you have configured your browser to not accept cookies, our system will set cookies when you direct your browser to our Site.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">Information collected through Sites and / or Cookies that may be stored on your computer will not be stored longer than required to achieve the purposes set out above. Our cookies serve the following purposes:</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>Type of cookie Purpose</b></span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>Required / Technically:</b></span></p>\r\n\r\n<p>These cookies are required to allow us to use our sites so that you can access them as requested. These cookies, for example, let us know that you have created an account and logged into that account to access Site content. These cookies also enable us to save your past actions while browsing and protecting our sites.</p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>Analysis / Operation:</b></span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">These cookies used by us or third-party service providers to analyze how the Haus des Döners site is used and functioning. For example, these cookies determine which pages are most frequently visited, and where our visitors are located. If you subscribe to a newsletter or otherwise from the site, these cookies may be related to you. These cookies include Google Analytics cookies.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>Functionality:</b></span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">The main purpose of these cookies allows us to configure the sites based on your preferences. These cookies allow us to &ldquo;remember you&rdquo; during visits. For example, we will see your username and remember how you have organized the sites and services, for example by adjusting the text size, fonts, languages ​​and other parts of the dynamic web pages, and providing you with the same preferences for future visits.</span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\"><b>How do I deny or revoke my consent to use cookies?</b></span></p>\r\n\r\n<p class=\"p1\" style=\"font-variant-numeric: normal; font-variant-east-asian: normal; font-stretch: normal; line-height: normal; font-family: Helvetica; -webkit-text-stroke-color: rgb(38, 38, 38); margin: 0px 0px 14px; color: rgb(38, 38, 38);\"><span class=\"s1\" style=\"font-kerning: none;\">If you don&rsquo;t want cookies to be discarded on your device, you can configure your Internet browser setting to reject all or other cookies and to warn you when a cookie is installed on your device.</span></p>', 'cms/9da6b59a5a2dd37e8ef7ab9ae82cef51.png', 'cms/0a7e5f87e70a99368b4fcb98dc3b7850.png', 1432, 'en', 0, 1, 1, '2023-01-24 10:49:42', NULL, 'Cookie Policy', 'Cookie Policy');

-- --------------------------------------------------------

--
-- Table structure for table `contactus_detail`
--

CREATE TABLE `contactus_detail` (
  `contact_id` int(11) NOT NULL,
  `first_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `rest_name` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `res_zip_code` varchar(50) DEFAULT NULL,
  `res_phone_number` varchar(50) DEFAULT NULL,
  `owners_phone_number` varchar(50) DEFAULT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `content_general`
--

CREATE TABLE `content_general` (
  `content_general_id` int(11) NOT NULL,
  `content_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `content_general`
--

INSERT INTO `content_general` (`content_general_id`, `content_type`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 'email_template', 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(3, 'email_template', 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00'),
(26, 'email_template', 1, '2019-09-06 11:41:37', 0, '0000-00-00 00:00:00'),
(27, 'email_template', 1, '2019-09-06 12:40:08', 0, '0000-00-00 00:00:00'),
(28, 'email_template', 1, '2019-09-06 12:45:21', 0, '0000-00-00 00:00:00'),
(30, 'email_template', 1, '2019-09-06 14:57:24', 0, '0000-00-00 00:00:00'),
(31, 'email_template', 1, '2019-09-06 15:33:05', 0, '0000-00-00 00:00:00'),
(95, 'email_template', 1, '2019-10-23 18:18:08', 0, '0000-00-00 00:00:00'),
(160, 'cms', 1, '2019-12-27 12:42:45', 0, '0000-00-00 00:00:00'),
(161, 'cms', 1, '2019-12-27 12:44:15', 0, '0000-00-00 00:00:00'),
(162, 'cms', 1, '2019-12-30 06:56:33', 0, '0000-00-00 00:00:00'),
(163, 'cms', 1, '2021-03-19 06:52:27', NULL, NULL),
(205, 'email_template', 1, '2020-02-12 12:49:07', 0, '0000-00-00 00:00:00'),
(388, 'food_type', 1, '2020-12-07 12:52:18', 0, '0000-00-00 00:00:00'),
(453, 'food_type', 1, '2020-12-29 00:00:00', 1, '2020-12-29 00:00:00'),
(546, 'food_type', 1, '2021-03-08 14:38:25', 0, '0000-00-00 00:00:00'),
(573, 'category', 1, '2021-03-24 02:02:47', NULL, NULL),
(589, 'restaurant', 1, '2021-03-29 21:32:04', 1, '2022-12-22 07:05:43'),
(596, 'menu', 1, '2021-03-31 10:27:40', NULL, NULL),
(600, 'menu', 1, '2021-03-31 12:03:16', NULL, NULL),
(601, 'menu', 1, '2021-03-31 12:08:36', NULL, NULL),
(603, 'menu', 1, '2021-03-31 12:14:33', NULL, NULL),
(914, 'email_template', 1, '2021-05-12 15:02:17', NULL, NULL),
(1124, 'package', 1, '2021-06-14 12:09:14', NULL, NULL),
(1125, 'package', 1, '2021-06-14 12:10:16', NULL, NULL),
(1129, 'menu', 1, '2021-06-14 12:18:57', NULL, NULL),
(1196, 'package', 1, '2021-06-15 20:52:53', NULL, NULL),
(1216, 'package', 1, '2021-06-16 19:41:35', NULL, NULL),
(1220, 'category', 1, '2021-06-17 14:51:00', NULL, NULL),
(1223, 'category', 1, '2021-06-17 14:55:53', NULL, NULL),
(1227, 'addons_category', 1, '2021-06-17 17:17:50', NULL, NULL),
(1229, 'addons_category', 1, '2021-06-17 17:18:48', NULL, NULL),
(1230, 'addons_category', 1, '2021-06-17 17:23:36', NULL, NULL),
(1233, 'menu', 1, '2021-06-18 17:17:24', NULL, NULL),
(1250, 'food_type', 1, '2021-06-21 15:59:45', NULL, NULL),
(1310, 'package', 1, '2021-06-23 14:47:43', NULL, NULL),
(1432, 'cms', 1, '2022-12-22 08:09:56', NULL, NULL),
(1534, 'restaurant', 27, '2021-07-07 16:47:14', 1, '2022-12-22 07:03:42'),
(1551, 'menu', 27, '2021-07-07 17:32:15', NULL, NULL),
(1556, 'menu', 482, '2021-07-07 19:27:06', NULL, NULL),
(1639, 'addons_category', 1, '2021-07-15 14:55:17', NULL, NULL),
(1652, 'category', 1, '2021-07-26 12:48:10', NULL, NULL),
(1677, 'menu', 1, '2021-08-06 09:46:06', NULL, NULL),
(1702, 'menu', 1, '2021-08-19 06:19:38', NULL, NULL),
(1722, 'faq_category', 1, '2021-09-01 13:10:24', NULL, NULL),
(1723, 'faq_category', 1, '2021-09-01 13:11:29', NULL, NULL),
(1724, 'faq_category', 1, '2021-09-01 13:13:21', NULL, NULL),
(1725, 'faqs', 1, '2021-09-01 13:15:17', NULL, NULL),
(1726, 'faqs', 1, '2021-09-01 13:19:08', NULL, NULL),
(1727, 'faqs', 1, '2021-09-01 13:21:36', NULL, NULL),
(1729, 'faqs', 1, '2021-09-01 13:23:56', NULL, NULL),
(1730, 'faqs', 1, '2021-09-01 13:30:59', NULL, NULL),
(1731, 'faqs', 1, '2021-09-01 13:34:09', NULL, NULL),
(1732, 'faqs', 1, '2021-09-01 13:35:03', NULL, NULL),
(1733, 'email_template', 1, '2021-09-03 12:58:30', NULL, NULL),
(1739, 'email_template', 1, '2021-09-14 11:44:18', NULL, NULL),
(1740, 'email_template', 1, '2021-09-20 09:20:02', NULL, NULL),
(1754, 'email_template', 1, '2021-09-29 05:16:25', NULL, NULL),
(1799, 'restaurant', 1, '2021-10-07 05:23:39', 1, '2022-12-22 07:04:41'),
(1848, 'category', 1, '2021-12-06 08:28:10', NULL, NULL),
(1996, 'restaurant', 1, '2022-02-17 10:01:57', 1, '2022-12-22 07:04:15'),
(2008, 'menu', 1, '2022-02-17 12:55:43', NULL, NULL),
(2009, 'menu', 1, '2022-02-18 07:52:11', NULL, NULL),
(2010, 'menu', 1, '2022-02-18 07:53:33', NULL, NULL),
(2011, 'menu', 1, '2022-02-18 07:57:57', NULL, NULL),
(2016, 'email_template', 1, '2022-03-28 06:20:01', NULL, NULL),
(2057, 'menu', 1, '2022-05-18 06:44:57', NULL, NULL),
(2069, 'cancel_reject_reason', 1, '2022-05-18 11:58:25', NULL, NULL),
(2070, 'cancel_reject_reason', 1, '2022-05-18 11:59:24', NULL, NULL),
(2071, 'cancel_reject_reason', 1, '2022-05-18 12:00:00', NULL, NULL),
(2076, 'recipe', 1, '2022-05-19 07:16:09', 1, '2022-05-19 07:16:09'),
(2077, 'recipe', 1, '2022-05-19 07:22:20', 1, '2022-05-19 07:22:20'),
(2078, 'recipe', 1, '2022-05-19 07:24:29', 1, '2022-05-19 07:24:29'),
(2079, 'recipe', 1, '2022-05-19 07:31:09', 1, '2022-05-19 07:31:09'),
(2080, 'recipe', 1, '2022-05-19 07:33:38', 1, '2022-05-19 07:33:38'),
(2082, 'cancel_reject_reason', 1, '2022-06-08 08:53:06', NULL, NULL),
(2085, 'email_template', 1, '2022-06-14 04:28:47', NULL, NULL),
(2136, 'cancel_reject_reason', 1, '2022-06-20 13:19:30', NULL, NULL),
(2137, 'cancel_reject_reason', 1, '2022-06-20 13:20:09', NULL, NULL),
(2138, 'cancel_reject_reason', 1, '2022-06-20 13:20:54', NULL, NULL),
(2139, 'cancel_reject_reason', 1, '2022-06-20 13:21:23', NULL, NULL),
(2142, 'menu', 1, '2022-06-21 17:34:29', NULL, NULL),
(2143, 'menu', 1, '2022-06-21 17:38:14', NULL, NULL),
(2144, 'cancel_reject_reason', 1, '2022-06-22 05:56:04', NULL, NULL),
(2166, 'menu', 1, '2022-06-29 11:11:00', NULL, NULL),
(2170, 'menu', 669, '2022-06-29 12:38:52', NULL, NULL),
(2194, 'cms', 1, '2022-08-09 07:24:40', NULL, NULL),
(2209, 'table', 1, '2022-12-21 14:03:25', NULL, NULL),
(2289, 'email_template', 1, '2022-12-22 08:33:52', NULL, NULL),
(2290, 'email_template', 1, '2022-12-22 08:33:52', NULL, NULL),
(2291, 'table', 1, '2022-12-22 07:35:28', NULL, NULL),
(2292, 'table', 1, '2022-12-22 07:35:38', NULL, NULL),
(2293, 'table', 1, '2022-12-22 07:35:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `iso` char(2) NOT NULL,
  `name` varchar(80) NOT NULL,
  `nicename` varchar(80) NOT NULL,
  `iso3` char(3) DEFAULT NULL,
  `numcode` smallint(6) DEFAULT NULL,
  `phonecode` int(5) NOT NULL,
  `set_default` tinyint(4) NOT NULL DEFAULT 0,
  `status` tinyint(4) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `iso`, `name`, `nicename`, `iso3`, `numcode`, `phonecode`, `set_default`, `status`, `updated_at`) VALUES
(1, 'AF', 'AFGHANISTAN', 'Afghanistan', 'AFG', 4, 93, 0, 0, '2022-02-10 12:14:31'),
(2, 'AL', 'ALBANIA', 'Albania', 'ALB', 8, 355, 0, 1, '2021-08-05 13:24:17'),
(3, 'DZ', 'ALGERIA', 'Algeria', 'DZA', 12, 213, 0, 1, '2021-08-05 13:24:20'),
(4, 'AS', 'AMERICAN SAMOA', 'American Samoa', 'ASM', 16, 1684, 0, 1, '2021-12-23 10:28:38'),
(5, 'AD', 'ANDORRA', 'Andorra', 'AND', 20, 376, 0, 1, '2022-02-10 12:15:06'),
(6, 'AO', 'ANGOLA', 'Angola', 'AGO', 24, 244, 0, 1, '2021-08-05 13:24:27'),
(7, 'AI', 'ANGUILLA', 'Anguilla', 'AIA', 660, 1264, 0, 1, '2021-08-05 13:24:30'),
(8, 'AQ', 'ANTARCTICA', 'Antarctica', NULL, NULL, 0, 0, 1, '2021-08-05 13:24:34'),
(9, 'AG', 'ANTIGUA AND BARBUDA', 'Antigua and Barbuda', 'ATG', 28, 1268, 0, 1, '2021-11-12 13:44:54'),
(10, 'AR', 'ARGENTINA', 'Argentina', 'ARG', 32, 54, 0, 1, '2021-11-12 13:44:57'),
(11, 'AM', 'ARMENIA', 'Armenia', 'ARM', 51, 374, 0, 0, '2021-05-25 09:45:23'),
(12, 'AW', 'ARUBA', 'Aruba', 'ABW', 533, 297, 0, 0, '2021-05-25 09:45:23'),
(13, 'AU', 'AUSTRALIA', 'Australia', 'AUS', 36, 61, 0, 1, '2022-07-14 10:39:19'),
(14, 'AT', 'AUSTRIA', 'Austria', 'AUT', 40, 43, 0, 0, '2021-05-25 09:45:23'),
(15, 'AZ', 'AZERBAIJAN', 'Azerbaijan', 'AZE', 31, 994, 0, 0, '2021-05-25 09:45:23'),
(16, 'BS', 'BAHAMAS', 'Bahamas', 'BHS', 44, 1242, 0, 0, '2021-05-25 09:45:23'),
(17, 'BH', 'BAHRAIN', 'Bahrain', 'BHR', 48, 973, 0, 0, '2021-05-25 09:45:23'),
(18, 'BD', 'BANGLADESH', 'Bangladesh', 'BGD', 50, 880, 0, 0, '2021-05-25 09:45:23'),
(19, 'BB', 'BARBADOS', 'Barbados', 'BRB', 52, 1246, 0, 0, '2021-05-25 09:45:23'),
(20, 'BY', 'BELARUS', 'Belarus', 'BLR', 112, 375, 0, 0, '2021-05-25 09:45:23'),
(21, 'BE', 'BELGIUM', 'Belgium', 'BEL', 56, 32, 0, 0, '2021-05-25 09:45:23'),
(22, 'BZ', 'BELIZE', 'Belize', 'BLZ', 84, 501, 0, 0, '2021-05-25 09:45:23'),
(23, 'BJ', 'BENIN', 'Benin', 'BEN', 204, 229, 0, 0, '2021-05-25 09:45:23'),
(24, 'BM', 'BERMUDA', 'Bermuda', 'BMU', 60, 1441, 0, 0, '2021-05-25 09:45:23'),
(25, 'BT', 'BHUTAN', 'Bhutan', 'BTN', 64, 975, 0, 0, '2021-05-25 09:45:23'),
(26, 'BO', 'BOLIVIA', 'Bolivia', 'BOL', 68, 591, 0, 0, '2021-05-25 09:45:23'),
(27, 'BA', 'BOSNIA AND HERZEGOVINA', 'Bosnia and Herzegovina', 'BIH', 70, 387, 0, 0, '2021-05-25 09:45:23'),
(28, 'BW', 'BOTSWANA', 'Botswana', 'BWA', 72, 267, 0, 0, '2021-05-25 09:45:23'),
(29, 'BV', 'BOUVET ISLAND', 'Bouvet Island', NULL, NULL, 0, 0, 0, '2021-05-25 09:45:23'),
(30, 'BR', 'BRAZIL', 'Brazil', 'BRA', 76, 55, 0, 0, '2021-05-25 09:45:23'),
(31, 'IO', 'BRITISH INDIAN OCEAN TERRITORY', 'British Indian Ocean Territory', NULL, NULL, 246, 0, 0, '2021-05-25 09:45:23'),
(32, 'BN', 'BRUNEI DARUSSALAM', 'Brunei Darussalam', 'BRN', 96, 673, 0, 0, '2021-05-25 09:45:23'),
(33, 'BG', 'BULGARIA', 'Bulgaria', 'BGR', 100, 359, 0, 0, '2021-05-25 09:45:23'),
(34, 'BF', 'BURKINA FASO', 'Burkina Faso', 'BFA', 854, 226, 0, 0, '2021-05-25 09:45:23'),
(35, 'BI', 'BURUNDI', 'Burundi', 'BDI', 108, 257, 0, 0, '2021-05-25 09:45:23'),
(36, 'KH', 'CAMBODIA', 'Cambodia', 'KHM', 116, 855, 0, 0, '2021-05-25 09:45:23'),
(37, 'CM', 'CAMEROON', 'Cameroon', 'CMR', 120, 237, 0, 0, '2021-05-25 09:45:23'),
(38, 'CA', 'CANADA', 'Canada', 'CAN', 124, 1, 0, 1, '2021-08-05 13:23:42'),
(39, 'CV', 'CAPE VERDE', 'Cape Verde', 'CPV', 132, 238, 0, 0, '2021-05-25 09:45:23'),
(40, 'KY', 'CAYMAN ISLANDS', 'Cayman Islands', 'CYM', 136, 1345, 0, 0, '2021-05-25 09:45:23'),
(41, 'CF', 'CENTRAL AFRICAN REPUBLIC', 'Central African Republic', 'CAF', 140, 236, 0, 0, '2021-05-25 09:45:23'),
(42, 'TD', 'CHAD', 'Chad', 'TCD', 148, 235, 0, 0, '2021-05-25 09:45:23'),
(43, 'CL', 'CHILE', 'Chile', 'CHL', 152, 56, 0, 0, '2021-05-25 09:45:23'),
(44, 'CN', 'CHINA', 'China', 'CHN', 156, 86, 0, 0, '2021-05-25 09:45:23'),
(45, 'CX', 'CHRISTMAS ISLAND', 'Christmas Island', NULL, NULL, 61, 0, 0, '2021-05-25 09:45:23'),
(46, 'CC', 'COCOS (KEELING) ISLANDS', 'Cocos (Keeling) Islands', NULL, NULL, 672, 0, 0, '2021-05-25 09:45:23'),
(47, 'CO', 'COLOMBIA', 'Colombia', 'COL', 170, 57, 0, 0, '2021-05-25 09:45:23'),
(48, 'KM', 'COMOROS', 'Comoros', 'COM', 174, 269, 0, 0, '2021-05-25 09:45:23'),
(49, 'CG', 'CONGO', 'Congo', 'COG', 178, 242, 0, 0, '2021-05-25 09:45:23'),
(50, 'CD', 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'Congo, the Democratic Republic of the', 'COD', 180, 242, 0, 0, '2021-05-25 09:45:23'),
(51, 'CK', 'COOK ISLANDS', 'Cook Islands', 'COK', 184, 682, 0, 0, '2021-05-25 09:45:23'),
(52, 'CR', 'COSTA RICA', 'Costa Rica', 'CRI', 188, 506, 0, 0, '2021-05-25 09:45:23'),
(53, 'CI', 'COTE D\'IVOIRE', 'Cote D\'Ivoire', 'CIV', 384, 225, 0, 0, '2021-05-25 09:45:23'),
(54, 'HR', 'CROATIA', 'Croatia', 'HRV', 191, 385, 0, 0, '2021-05-25 09:45:23'),
(55, 'CU', 'CUBA', 'Cuba', 'CUB', 192, 53, 0, 0, '2021-05-25 09:45:23'),
(56, 'CY', 'CYPRUS', 'Cyprus', 'CYP', 196, 357, 0, 0, '2021-05-25 09:45:23'),
(57, 'CZ', 'CZECH REPUBLIC', 'Czech Republic', 'CZE', 203, 420, 0, 0, '2021-05-25 09:45:23'),
(58, 'DK', 'DENMARK', 'Denmark', 'DNK', 208, 45, 0, 0, '2021-05-25 09:45:23'),
(59, 'DJ', 'DJIBOUTI', 'Djibouti', 'DJI', 262, 253, 0, 0, '2021-05-25 09:45:23'),
(60, 'DM', 'DOMINICA', 'Dominica', 'DMA', 212, 1767, 0, 1, '2021-08-05 13:23:55'),
(61, 'DO', 'DOMINICAN REPUBLIC', 'Dominican Republic', 'DOM', 214, 1809, 0, 0, '2021-05-25 09:45:23'),
(62, 'EC', 'ECUADOR', 'Ecuador', 'ECU', 218, 593, 0, 0, '2021-05-25 09:45:23'),
(63, 'EG', 'EGYPT', 'Egypt', 'EGY', 818, 20, 0, 0, '2021-05-25 09:45:23'),
(64, 'SV', 'EL SALVADOR', 'El Salvador', 'SLV', 222, 503, 0, 0, '2021-05-25 09:45:23'),
(65, 'GQ', 'EQUATORIAL GUINEA', 'Equatorial Guinea', 'GNQ', 226, 240, 0, 0, '2021-05-25 09:45:23'),
(66, 'ER', 'ERITREA', 'Eritrea', 'ERI', 232, 291, 0, 0, '2021-05-25 09:45:23'),
(67, 'EE', 'ESTONIA', 'Estonia', 'EST', 233, 372, 0, 0, '2021-05-25 09:45:23'),
(68, 'ET', 'ETHIOPIA', 'Ethiopia', 'ETH', 231, 251, 0, 0, '2021-05-25 09:45:23'),
(69, 'FK', 'FALKLAND ISLANDS (MALVINAS)', 'Falkland Islands (Malvinas)', 'FLK', 238, 500, 0, 0, '2021-05-25 09:45:23'),
(70, 'FO', 'FAROE ISLANDS', 'Faroe Islands', 'FRO', 234, 298, 0, 0, '2021-05-25 09:45:23'),
(71, 'FJ', 'FIJI', 'Fiji', 'FJI', 242, 679, 0, 0, '2021-05-25 09:45:23'),
(72, 'FI', 'FINLAND', 'Finland', 'FIN', 246, 358, 0, 0, '2021-05-25 09:45:23'),
(73, 'FR', 'FRANCE', 'France', 'FRA', 250, 33, 0, 0, '2021-05-25 09:45:23'),
(74, 'GF', 'FRENCH GUIANA', 'French Guiana', 'GUF', 254, 594, 0, 0, '2021-05-25 09:45:23'),
(75, 'PF', 'FRENCH POLYNESIA', 'French Polynesia', 'PYF', 258, 689, 0, 0, '2021-05-25 09:45:23'),
(76, 'TF', 'FRENCH SOUTHERN TERRITORIES', 'French Southern Territories', NULL, NULL, 0, 0, 0, '2021-05-25 09:45:23'),
(77, 'GA', 'GABON', 'Gabon', 'GAB', 266, 241, 0, 0, '2021-05-25 09:45:23'),
(78, 'GM', 'GAMBIA', 'Gambia', 'GMB', 270, 220, 0, 0, '2021-05-25 09:45:23'),
(79, 'GE', 'GEORGIA', 'Georgia', 'GEO', 268, 995, 0, 0, '2021-05-25 09:45:23'),
(80, 'DE', 'GERMANY', 'Germany', 'DEU', 276, 49, 0, 0, '2021-05-25 09:45:23'),
(81, 'GH', 'GHANA', 'Ghana', 'GHA', 288, 233, 0, 0, '2021-05-25 09:45:23'),
(82, 'GI', 'GIBRALTAR', 'Gibraltar', 'GIB', 292, 350, 0, 0, '2021-05-25 09:45:23'),
(83, 'GR', 'GREECE', 'Greece', 'GRC', 300, 30, 0, 0, '2021-05-25 09:45:23'),
(84, 'GL', 'GREENLAND', 'Greenland', 'GRL', 304, 299, 0, 0, '2021-05-25 09:45:23'),
(85, 'GD', 'GRENADA', 'Grenada', 'GRD', 308, 1473, 0, 0, '2021-05-25 09:45:23'),
(86, 'GP', 'GUADELOUPE', 'Guadeloupe', 'GLP', 312, 590, 0, 0, '2021-05-25 09:45:23'),
(87, 'GU', 'GUAM', 'Guam', 'GUM', 316, 1671, 0, 0, '2021-05-25 09:45:23'),
(88, 'GT', 'GUATEMALA', 'Guatemala', 'GTM', 320, 502, 0, 0, '2021-05-25 09:45:23'),
(89, 'GN', 'GUINEA', 'Guinea', 'GIN', 324, 224, 0, 0, '2021-05-25 09:45:23'),
(90, 'GW', 'GUINEA-BISSAU', 'Guinea-Bissau', 'GNB', 624, 245, 0, 0, '2021-05-25 09:45:23'),
(91, 'GY', 'GUYANA', 'Guyana', 'GUY', 328, 592, 0, 0, '2021-05-25 09:45:23'),
(92, 'HT', 'HAITI', 'Haiti', 'HTI', 332, 509, 0, 0, '2021-05-25 09:45:23'),
(93, 'HM', 'HEARD ISLAND AND MCDONALD ISLANDS', 'Heard Island and Mcdonald Islands', NULL, NULL, 0, 0, 0, '2021-05-25 09:45:23'),
(94, 'VA', 'HOLY SEE (VATICAN CITY STATE)', 'Holy See (Vatican City State)', 'VAT', 336, 39, 0, 0, '2021-05-25 09:45:23'),
(95, 'HN', 'HONDURAS', 'Honduras', 'HND', 340, 504, 0, 0, '2021-05-25 09:45:23'),
(96, 'HK', 'HONG KONG', 'Hong Kong', 'HKG', 344, 852, 0, 0, '2021-05-25 09:45:23'),
(97, 'HU', 'HUNGARY', 'Hungary', 'HUN', 348, 36, 0, 0, '2021-05-25 09:45:23'),
(98, 'IS', 'ICELAND', 'Iceland', 'ISL', 352, 354, 0, 0, '2021-05-25 09:45:23'),
(99, 'IN', 'INDIA', 'India', 'IND', 356, 91, 1, 1, '2022-11-29 07:46:08'),
(100, 'ID', 'INDONESIA', 'Indonesia', 'IDN', 360, 62, 0, 0, '2021-05-25 09:45:23'),
(101, 'IR', 'IRAN, ISLAMIC REPUBLIC OF', 'Iran, Islamic Republic of', 'IRN', 364, 98, 0, 0, '2021-05-25 09:45:23'),
(102, 'IQ', 'IRAQ', 'Iraq', 'IRQ', 368, 964, 0, 0, '2021-05-25 09:45:23'),
(103, 'IE', 'IRELAND', 'Ireland', 'IRL', 372, 353, 0, 0, '2021-05-25 09:45:23'),
(104, 'IL', 'ISRAEL', 'Israel', 'ISR', 376, 972, 0, 0, '2021-05-25 09:45:23'),
(105, 'IT', 'ITALY', 'Italy', 'ITA', 380, 39, 0, 0, '2021-05-25 09:45:23'),
(106, 'JM', 'JAMAICA', 'Jamaica', 'JAM', 388, 1876, 0, 0, '2021-05-25 09:45:23'),
(107, 'JP', 'JAPAN', 'Japan', 'JPN', 392, 81, 0, 0, '2021-05-25 09:45:23'),
(108, 'JO', 'JORDAN', 'Jordan', 'JOR', 400, 962, 0, 0, '2021-05-25 09:45:23'),
(109, 'KZ', 'KAZAKHSTAN', 'Kazakhstan', 'KAZ', 398, 7, 0, 0, '2021-05-25 09:45:23'),
(110, 'KE', 'KENYA', 'Kenya', 'KEN', 404, 254, 0, 0, '2021-05-25 09:45:23'),
(111, 'KI', 'KIRIBATI', 'Kiribati', 'KIR', 296, 686, 0, 0, '2021-05-25 09:45:23'),
(112, 'KP', 'KOREA, DEMOCRATIC PEOPLE\'S REPUBLIC OF', 'Korea, Democratic People\'s Republic of', 'PRK', 408, 850, 0, 0, '2021-05-25 09:45:23'),
(113, 'KR', 'KOREA, REPUBLIC OF', 'Korea, Republic of', 'KOR', 410, 82, 0, 0, '2021-05-25 09:45:23'),
(114, 'KW', 'KUWAIT', 'Kuwait', 'KWT', 414, 965, 0, 0, '2021-05-25 09:45:23'),
(115, 'KG', 'KYRGYZSTAN', 'Kyrgyzstan', 'KGZ', 417, 996, 0, 0, '2021-05-25 09:45:23'),
(116, 'LA', 'LAO PEOPLE\'S DEMOCRATIC REPUBLIC', 'Lao People\'s Democratic Republic', 'LAO', 418, 856, 0, 0, '2021-05-25 09:45:23'),
(117, 'LV', 'LATVIA', 'Latvia', 'LVA', 428, 371, 0, 0, '2021-05-25 09:45:23'),
(118, 'LB', 'LEBANON', 'Lebanon', 'LBN', 422, 961, 0, 0, '2021-05-25 09:45:23'),
(119, 'LS', 'LESOTHO', 'Lesotho', 'LSO', 426, 266, 0, 0, '2021-05-25 09:45:23'),
(120, 'LR', 'LIBERIA', 'Liberia', 'LBR', 430, 231, 0, 0, '2021-05-25 09:45:23'),
(121, 'LY', 'LIBYAN ARAB JAMAHIRIYA', 'Libyan Arab Jamahiriya', 'LBY', 434, 218, 0, 0, '2021-05-25 09:45:23'),
(122, 'LI', 'LIECHTENSTEIN', 'Liechtenstein', 'LIE', 438, 423, 0, 0, '2021-05-25 09:45:23'),
(123, 'LT', 'LITHUANIA', 'Lithuania', 'LTU', 440, 370, 0, 0, '2021-05-25 09:45:23'),
(124, 'LU', 'LUXEMBOURG', 'Luxembourg', 'LUX', 442, 352, 0, 0, '2021-05-25 09:45:23'),
(125, 'MO', 'MACAO', 'Macao', 'MAC', 446, 853, 0, 0, '2021-05-25 09:45:23'),
(126, 'MK', 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807, 389, 0, 0, '2021-05-25 09:45:23'),
(127, 'MG', 'MADAGASCAR', 'Madagascar', 'MDG', 450, 261, 0, 0, '2021-05-25 09:45:23'),
(128, 'MW', 'MALAWI', 'Malawi', 'MWI', 454, 265, 0, 0, '2021-05-25 09:45:23'),
(129, 'MY', 'MALAYSIA', 'Malaysia', 'MYS', 458, 60, 0, 0, '2021-05-25 09:45:23'),
(130, 'MV', 'MALDIVES', 'Maldives', 'MDV', 462, 960, 0, 0, '2021-05-25 09:45:23'),
(131, 'ML', 'MALI', 'Mali', 'MLI', 466, 223, 0, 0, '2021-05-25 09:45:23'),
(132, 'MT', 'MALTA', 'Malta', 'MLT', 470, 356, 0, 0, '2021-05-25 09:45:23'),
(133, 'MH', 'MARSHALL ISLANDS', 'Marshall Islands', 'MHL', 584, 692, 0, 0, '2021-05-25 09:45:23'),
(134, 'MQ', 'MARTINIQUE', 'Martinique', 'MTQ', 474, 596, 0, 0, '2021-05-25 09:45:23'),
(135, 'MR', 'MAURITANIA', 'Mauritania', 'MRT', 478, 222, 0, 0, '2021-05-25 09:45:23'),
(136, 'MU', 'MAURITIUS', 'Mauritius', 'MUS', 480, 230, 0, 0, '2021-05-25 09:45:23'),
(137, 'YT', 'MAYOTTE', 'Mayotte', NULL, NULL, 269, 0, 0, '2021-05-25 09:45:23'),
(138, 'MX', 'MEXICO', 'Mexico', 'MEX', 484, 52, 0, 0, '2021-05-25 09:45:23'),
(139, 'FM', 'MICRONESIA, FEDERATED STATES OF', 'Micronesia, Federated States of', 'FSM', 583, 691, 0, 0, '2021-05-25 09:45:23'),
(140, 'MD', 'MOLDOVA, REPUBLIC OF', 'Moldova, Republic of', 'MDA', 498, 373, 0, 0, '2021-05-25 09:45:23'),
(141, 'MC', 'MONACO', 'Monaco', 'MCO', 492, 377, 0, 0, '2021-05-25 09:45:23'),
(142, 'MN', 'MONGOLIA', 'Mongolia', 'MNG', 496, 976, 0, 0, '2021-05-25 09:45:23'),
(143, 'MS', 'MONTSERRAT', 'Montserrat', 'MSR', 500, 1664, 0, 0, '2021-05-25 09:45:23'),
(144, 'MA', 'MOROCCO', 'Morocco', 'MAR', 504, 212, 0, 0, '2021-05-25 09:45:23'),
(145, 'MZ', 'MOZAMBIQUE', 'Mozambique', 'MOZ', 508, 258, 0, 0, '2021-05-25 09:45:23'),
(146, 'MM', 'MYANMAR', 'Myanmar', 'MMR', 104, 95, 0, 0, '2021-05-25 09:45:23'),
(147, 'NA', 'NAMIBIA', 'Namibia', 'NAM', 516, 264, 0, 0, '2021-05-25 09:45:23'),
(148, 'NR', 'NAURU', 'Nauru', 'NRU', 520, 674, 0, 0, '2021-05-25 09:45:23'),
(149, 'NP', 'NEPAL', 'Nepal', 'NPL', 524, 977, 0, 0, '2021-05-25 09:45:23'),
(150, 'NL', 'NETHERLANDS', 'Netherlands', 'NLD', 528, 31, 0, 1, '2022-10-06 02:40:01'),
(151, 'AN', 'NETHERLANDS ANTILLES', 'Netherlands Antilles', 'ANT', 530, 599, 0, 0, '2021-05-25 09:45:23'),
(152, 'NC', 'NEW CALEDONIA', 'New Caledonia', 'NCL', 540, 687, 0, 0, '2021-05-25 09:45:23'),
(153, 'NZ', 'NEW ZEALAND', 'New Zealand', 'NZL', 554, 64, 0, 0, '2021-05-25 09:45:23'),
(154, 'NI', 'NICARAGUA', 'Nicaragua', 'NIC', 558, 505, 0, 0, '2021-05-25 09:45:23'),
(155, 'NE', 'NIGER', 'Niger', 'NER', 562, 227, 0, 0, '2021-05-25 09:45:23'),
(156, 'NG', 'NIGERIA', 'Nigeria', 'NGA', 566, 234, 0, 0, '2021-05-25 09:45:23'),
(157, 'NU', 'NIUE', 'Niue', 'NIU', 570, 683, 0, 0, '2021-05-25 09:45:23'),
(158, 'NF', 'NORFOLK ISLAND', 'Norfolk Island', 'NFK', 574, 672, 0, 0, '2021-05-25 09:45:23'),
(159, 'MP', 'NORTHERN MARIANA ISLANDS', 'Northern Mariana Islands', 'MNP', 580, 1670, 0, 0, '2021-05-25 09:45:23'),
(160, 'NO', 'NORWAY', 'Norway', 'NOR', 578, 47, 0, 0, '2021-05-25 09:45:23'),
(161, 'OM', 'OMAN', 'Oman', 'OMN', 512, 968, 0, 0, '2021-05-25 09:45:23'),
(162, 'PK', 'PAKISTAN', 'Pakistan', 'PAK', 586, 92, 0, 0, '2021-05-25 09:45:23'),
(163, 'PW', 'PALAU', 'Palau', 'PLW', 585, 680, 0, 0, '2021-05-25 09:45:23'),
(164, 'PS', 'PALESTINIAN TERRITORY, OCCUPIED', 'Palestinian Territory, Occupied', NULL, NULL, 970, 0, 0, '2021-05-25 09:45:23'),
(165, 'PA', 'PANAMA', 'Panama', 'PAN', 591, 507, 0, 0, '2021-05-25 09:45:23'),
(166, 'PG', 'PAPUA NEW GUINEA', 'Papua New Guinea', 'PNG', 598, 675, 0, 0, '2021-05-25 09:45:23'),
(167, 'PY', 'PARAGUAY', 'Paraguay', 'PRY', 600, 595, 0, 0, '2021-05-25 09:45:23'),
(168, 'PE', 'PERU', 'Peru', 'PER', 604, 51, 0, 0, '2021-05-25 09:45:23'),
(169, 'PH', 'PHILIPPINES', 'Philippines', 'PHL', 608, 63, 0, 0, '2021-05-25 09:45:23'),
(170, 'PN', 'PITCAIRN', 'Pitcairn', 'PCN', 612, 0, 0, 0, '2021-05-25 09:45:23'),
(171, 'PL', 'POLAND', 'Poland', 'POL', 616, 48, 0, 0, '2021-05-25 09:45:23'),
(172, 'PT', 'PORTUGAL', 'Portugal', 'PRT', 620, 351, 0, 0, '2021-05-25 09:45:23'),
(173, 'PR', 'PUERTO RICO', 'Puerto Rico', 'PRI', 630, 1787, 0, 0, '2021-05-25 09:45:23'),
(174, 'QA', 'QATAR', 'Qatar', 'QAT', 634, 974, 0, 0, '2021-05-25 09:45:23'),
(175, 'RE', 'REUNION', 'Reunion', 'REU', 638, 262, 0, 0, '2021-05-25 09:45:23'),
(176, 'RO', 'ROMANIA', 'Romania', 'ROM', 642, 40, 0, 0, '2021-05-25 09:45:23'),
(177, 'RU', 'RUSSIAN FEDERATION', 'Russian Federation', 'RUS', 643, 70, 0, 0, '2021-05-25 09:45:23'),
(178, 'RW', 'RWANDA', 'Rwanda', 'RWA', 646, 250, 0, 0, '2021-05-25 09:45:23'),
(179, 'SH', 'SAINT HELENA', 'Saint Helena', 'SHN', 654, 290, 0, 0, '2021-05-25 09:45:23'),
(180, 'KN', 'SAINT KITTS AND NEVIS', 'Saint Kitts and Nevis', 'KNA', 659, 1869, 0, 0, '2021-05-25 09:45:23'),
(181, 'LC', 'SAINT LUCIA', 'Saint Lucia', 'LCA', 662, 1758, 0, 0, '2021-05-25 09:45:23'),
(182, 'PM', 'SAINT PIERRE AND MIQUELON', 'Saint Pierre and Miquelon', 'SPM', 666, 508, 0, 0, '2021-05-25 09:45:23'),
(183, 'VC', 'SAINT VINCENT AND THE GRENADINES', 'Saint Vincent and the Grenadines', 'VCT', 670, 1784, 0, 0, '2021-05-25 09:45:23'),
(184, 'WS', 'SAMOA', 'Samoa', 'WSM', 882, 684, 0, 0, '2021-05-25 09:45:23'),
(185, 'SM', 'SAN MARINO', 'San Marino', 'SMR', 674, 378, 0, 0, '2021-05-25 09:45:23'),
(186, 'ST', 'SAO TOME AND PRINCIPE', 'Sao Tome and Principe', 'STP', 678, 239, 0, 0, '2021-05-25 09:45:23'),
(187, 'SA', 'SAUDI ARABIA', 'Saudi Arabia', 'SAU', 682, 966, 0, 0, '2021-05-25 09:45:23'),
(188, 'SN', 'SENEGAL', 'Senegal', 'SEN', 686, 221, 0, 0, '2021-05-25 09:45:23'),
(189, 'CS', 'SERBIA AND MONTENEGRO', 'Serbia and Montenegro', NULL, NULL, 381, 0, 0, '2021-05-25 09:45:23'),
(190, 'SC', 'SEYCHELLES', 'Seychelles', 'SYC', 690, 248, 0, 0, '2021-05-25 09:45:23'),
(191, 'SL', 'SIERRA LEONE', 'Sierra Leone', 'SLE', 694, 232, 0, 0, '2021-05-25 09:45:23'),
(192, 'SG', 'SINGAPORE', 'Singapore', 'SGP', 702, 65, 0, 0, '2021-05-25 09:45:23'),
(193, 'SK', 'SLOVAKIA', 'Slovakia', 'SVK', 703, 421, 0, 0, '2021-05-25 09:45:23'),
(194, 'SI', 'SLOVENIA', 'Slovenia', 'SVN', 705, 386, 0, 0, '2021-05-25 09:45:23'),
(195, 'SB', 'SOLOMON ISLANDS', 'Solomon Islands', 'SLB', 90, 677, 0, 0, '2021-05-25 09:45:23'),
(196, 'SO', 'SOMALIA', 'Somalia', 'SOM', 706, 252, 0, 0, '2021-05-25 09:45:23'),
(197, 'ZA', 'SOUTH AFRICA', 'South Africa', 'ZAF', 710, 27, 0, 0, '2021-05-25 09:45:23'),
(198, 'GS', 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS', 'South Georgia and the South Sandwich Islands', NULL, NULL, 0, 0, 0, '2021-05-25 09:45:23'),
(199, 'ES', 'SPAIN', 'Spain', 'ESP', 724, 34, 0, 0, '2021-05-25 09:45:23'),
(200, 'LK', 'SRI LANKA', 'Sri Lanka', 'LKA', 144, 94, 0, 0, '2021-05-25 09:45:23'),
(201, 'SD', 'SUDAN', 'Sudan', 'SDN', 736, 249, 0, 0, '2021-05-25 09:45:23'),
(202, 'SR', 'SURINAME', 'Suriname', 'SUR', 740, 597, 0, 0, '2021-05-25 09:45:23'),
(203, 'SJ', 'SVALBARD AND JAN MAYEN', 'Svalbard and Jan Mayen', 'SJM', 744, 47, 0, 0, '2021-05-25 09:45:23'),
(204, 'SZ', 'SWAZILAND', 'Swaziland', 'SWZ', 748, 268, 0, 0, '2021-05-25 09:45:23'),
(205, 'SE', 'SWEDEN', 'Sweden', 'SWE', 752, 46, 0, 0, '2021-05-25 09:45:23'),
(206, 'CH', 'SWITZERLAND', 'Switzerland', 'CHE', 756, 41, 0, 0, '2021-05-25 09:45:23'),
(207, 'SY', 'SYRIAN ARAB REPUBLIC', 'Syrian Arab Republic', 'SYR', 760, 963, 0, 0, '2021-05-25 09:45:23'),
(208, 'TW', 'TAIWAN, PROVINCE OF CHINA', 'Taiwan, Province of China', 'TWN', 158, 886, 0, 0, '2021-05-25 09:45:23'),
(209, 'TJ', 'TAJIKISTAN', 'Tajikistan', 'TJK', 762, 992, 0, 0, '2021-05-25 09:45:23'),
(210, 'TZ', 'TANZANIA, UNITED REPUBLIC OF', 'Tanzania, United Republic of', 'TZA', 834, 255, 0, 0, '2021-05-25 09:45:23'),
(211, 'TH', 'THAILAND', 'Thailand', 'THA', 764, 66, 0, 0, '2021-05-25 09:45:23'),
(212, 'TL', 'TIMOR-LESTE', 'Timor-Leste', NULL, NULL, 670, 0, 0, '2021-05-25 09:45:23'),
(213, 'TG', 'TOGO', 'Togo', 'TGO', 768, 228, 0, 0, '2021-05-25 09:45:23'),
(214, 'TK', 'TOKELAU', 'Tokelau', 'TKL', 772, 690, 0, 0, '2021-05-25 09:45:23'),
(215, 'TO', 'TONGA', 'Tonga', 'TON', 776, 676, 0, 0, '2021-05-25 09:45:23'),
(216, 'TT', 'TRINIDAD AND TOBAGO', 'Trinidad and Tobago', 'TTO', 780, 1868, 0, 0, '2021-05-25 09:45:23'),
(217, 'TN', 'TUNISIA', 'Tunisia', 'TUN', 788, 216, 0, 0, '2021-05-25 09:45:23'),
(218, 'TR', 'TURKEY', 'Turkey', 'TUR', 792, 90, 0, 0, '2021-05-25 09:45:23'),
(219, 'TM', 'TURKMENISTAN', 'Turkmenistan', 'TKM', 795, 7370, 0, 0, '2021-05-25 09:45:23'),
(220, 'TC', 'TURKS AND CAICOS ISLANDS', 'Turks and Caicos Islands', 'TCA', 796, 1649, 0, 0, '2021-05-25 09:45:23'),
(221, 'TV', 'TUVALU', 'Tuvalu', 'TUV', 798, 688, 0, 0, '2021-05-25 09:45:23'),
(222, 'UG', 'UGANDA', 'Uganda', 'UGA', 800, 256, 0, 0, '2021-05-25 09:45:23'),
(223, 'UA', 'UKRAINE', 'Ukraine', 'UKR', 804, 380, 0, 0, '2021-05-25 09:45:23'),
(224, 'AE', 'UNITED ARAB EMIRATES', 'United Arab Emirates', 'ARE', 784, 971, 0, 0, '2021-05-25 09:45:23'),
(225, 'GB', 'UNITED KINGDOM', 'United Kingdom', 'GBR', 826, 44, 0, 0, '2021-05-25 09:45:23'),
(226, 'US', 'UNITED STATES', 'United States', 'USA', 840, 1, 0, 1, '2022-11-29 07:46:08'),
(227, 'UM', 'UNITED STATES MINOR OUTLYING ISLANDS', 'United States Minor Outlying Islands', NULL, NULL, 1, 0, 0, '2021-05-25 09:45:23'),
(228, 'UY', 'URUGUAY', 'Uruguay', 'URY', 858, 598, 0, 0, '2021-05-25 09:45:23'),
(229, 'UZ', 'UZBEKISTAN', 'Uzbekistan', 'UZB', 860, 998, 0, 0, '2021-05-25 09:45:23'),
(230, 'VU', 'VANUATU', 'Vanuatu', 'VUT', 548, 678, 0, 0, '2021-05-25 09:45:23'),
(231, 'VE', 'VENEZUELA', 'Venezuela', 'VEN', 862, 58, 0, 0, '2021-05-25 09:45:23'),
(232, 'VN', 'VIET NAM', 'Viet Nam', 'VNM', 704, 84, 0, 0, '2021-05-25 09:45:23'),
(233, 'VG', 'VIRGIN ISLANDS, BRITISH', 'Virgin Islands, British', 'VGB', 92, 1284, 0, 0, '2021-05-25 09:45:23'),
(234, 'VI', 'VIRGIN ISLANDS, U.S.', 'Virgin Islands, U.s.', 'VIR', 850, 1340, 0, 0, '2021-05-25 09:45:23'),
(235, 'WF', 'WALLIS AND FUTUNA', 'Wallis and Futuna', 'WLF', 876, 681, 0, 0, '2021-05-25 09:45:23'),
(236, 'EH', 'WESTERN SAHARA', 'Western Sahara', 'ESH', 732, 212, 0, 0, '2021-05-25 09:45:23'),
(237, 'YE', 'YEMEN', 'Yemen', 'YEM', 887, 967, 0, 0, '2021-05-25 09:45:23'),
(238, 'ZM', 'ZAMBIA', 'Zambia', 'ZMB', 894, 260, 0, 0, '2021-05-25 09:45:23'),
(239, 'ZW', 'ZIMBABWE', 'Zimbabwe', 'ZWE', 716, 263, 0, 0, '2021-05-25 09:45:23'),
(240, 'RS', 'SERBIA', 'Serbia', 'SRB', 688, 381, 0, 0, '2021-05-25 09:45:23'),
(241, 'AP', 'ASIA PACIFIC REGION', 'Asia / Pacific Region', '0', 0, 0, 0, 0, '2021-05-25 09:45:23'),
(242, 'ME', 'MONTENEGRO', 'Montenegro', 'MNE', 499, 382, 0, 1, '2021-08-05 13:24:09'),
(243, 'AX', 'ALAND ISLANDS', 'Aland Islands', 'ALA', 248, 358, 0, 0, '2021-05-25 09:45:23'),
(244, 'BQ', 'BONAIRE, SINT EUSTATIUS AND SABA', 'Bonaire, Sint Eustatius and Saba', 'BES', 535, 599, 0, 0, '2021-05-25 09:45:23'),
(245, 'CW', 'CURACAO', 'Curacao', 'CUW', 531, 599, 0, 0, '2021-05-25 09:45:23'),
(246, 'GG', 'GUERNSEY', 'Guernsey', 'GGY', 831, 44, 0, 0, '2021-05-25 09:45:23'),
(247, 'IM', 'ISLE OF MAN', 'Isle of Man', 'IMN', 833, 44, 0, 0, '2021-05-25 09:45:23'),
(248, 'JE', 'JERSEY', 'Jersey', 'JEY', 832, 44, 0, 0, '2021-05-25 09:45:23'),
(249, 'XK', 'KOSOVO', 'Kosovo', '---', 0, 381, 0, 0, '2021-05-25 09:45:23'),
(250, 'BL', 'SAINT BARTHELEMY', 'Saint Barthelemy', 'BLM', 652, 590, 0, 0, '2021-05-25 09:45:23'),
(251, 'MF', 'SAINT MARTIN', 'Saint Martin', 'MAF', 663, 590, 0, 0, '2021-05-25 09:45:23'),
(252, 'SX', 'SINT MAARTEN', 'Sint Maarten', 'SXM', 534, 1, 0, 0, '2021-05-25 09:45:23'),
(253, 'SS', 'SOUTH SUDAN', 'South Sudan', 'SSD', 728, 211, 0, 0, '2021-05-25 09:45:23');

-- --------------------------------------------------------

--
-- Table structure for table `coupon`
--

CREATE TABLE `coupon` (
  `entity_id` int(11) NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `amount_type` enum('Percentage','Amount') DEFAULT NULL,
  `amount` decimal(20,2) DEFAULT NULL,
  `max_amount` decimal(20,2) DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `coupon_type` enum('free_delivery','discount_on_items','discount_on_cart','discount_on_combo','user_registration','dine_in','discount_on_categories') DEFAULT NULL,
  `show_in_home` tinyint(4) NOT NULL DEFAULT 0,
  `use_with_other_coupons` int(1) NOT NULL DEFAULT 0,
  `maximaum_use_per_users` int(5) NOT NULL DEFAULT 0,
  `maximaum_use` int(5) NOT NULL DEFAULT 0,
  `coupon_for_newuser` tinyint(4) NOT NULL DEFAULT 0 COMMENT '“Free Delivery on New User” Coupon or Give a Check Box on every coupon settings “Coupon Is applicable to new users only”',
  `updated_date` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `status` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `coupon`
--

INSERT INTO `coupon` (`entity_id`, `restaurant_id`, `name`, `description`, `image`, `amount_type`, `amount`, `max_amount`, `start_date`, `end_date`, `coupon_type`, `show_in_home`, `use_with_other_coupons`, `maximaum_use_per_users`, `maximaum_use`, `coupon_for_newuser`, `updated_date`, `updated_by`, `created_by`, `created_date`, `status`) VALUES
(202, NULL, 'DISCOUNT ONCART', '<p>DISCOUNT ON CART</p>', NULL, 'Percentage', '20.00', '10.00', '2022-08-31 18:30:00', '2022-09-01 17:45:00', 'discount_on_cart', 0, 0, 0, 0, 0, '2023-01-23 07:04:27', 1, 1, '2022-09-01 06:46:41', 1),
(203, NULL, 'FREE DELIVERY ', '<p>free delivery&nbsp;</p>', 'coupons/70185f591c4be6032a5de2c6b65a59cf.jpg', NULL, NULL, '20.00', '2022-08-31 18:30:00', '2022-11-10 23:55:00', 'free_delivery', 1, 0, 0, 0, 0, '2023-01-23 07:04:23', 1, 1, '2022-09-01 06:47:24', 1),
(205, NULL, 'CARTDIS', '<p>cart discount</p>', 'coupons/6e553af6b013b4e89e9394a8293c40e7.jpg', 'Percentage', '10.00', '10.00', '2022-11-10 11:15:00', '2023-03-09 22:45:00', 'discount_on_cart', 1, 0, 0, 0, 0, '2023-01-23 07:05:00', 1, 1, '2022-11-10 11:20:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `coupon_category_map`
--

CREATE TABLE `coupon_category_map` (
  `entity_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `category_content_id` int(11) NOT NULL,
  `discount_type` enum('Amount','Percentage') NOT NULL,
  `discount_value` decimal(20,2) NOT NULL,
  `minimum_amount` decimal(20,2) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_item_map`
--

CREATE TABLE `coupon_item_map` (
  `entity_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `package_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_restaurant_map`
--

CREATE TABLE `coupon_restaurant_map` (
  `entity_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL COMMENT 'restaurant content id',
  `coupon_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `coupon_restaurant_map`
--

INSERT INTO `coupon_restaurant_map` (`entity_id`, `restaurant_id`, `coupon_id`) VALUES
(442, 1799, 202),
(443, 1534, 203),
(448, 1799, 205),
(449, 1534, 205),
(450, 1996, 205),
(451, 589, 205);

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `currency_id` int(11) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `country_name` varchar(255) NOT NULL,
  `dial_code` varchar(255) DEFAULT NULL,
  `currency_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `currency_symbol` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `currency_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`currency_id`, `code`, `country_name`, `dial_code`, `currency_name`, `currency_symbol`, `currency_code`) VALUES
(1, 'AF', 'Afghanistan', '93', 'Afghan afghani', '؋', 'AFN'),
(2, 'AL', 'Albania', '355', 'Albanian lek', 'L', 'ALL'),
(3, 'DZ', 'Algeria', '213', 'Algerian dinar', 'د.ج', 'DZD'),
(4, 'AS', 'American Samoa', '1684', '', '', ''),
(5, 'AD', 'Andorra', '376', 'Euro', '€', 'EUR'),
(6, 'AO', 'Angola', '244', 'Angolan kwanza', 'Kz', 'AOA'),
(7, 'AI', 'Anguilla', '1264', 'East Caribbean dolla', '$', 'XCD'),
(8, 'AQ', 'Antarctica', '0', '', '', ''),
(9, 'AG', 'Antigua And Barbuda', '1268', 'East Caribbean dolla', '$', 'XCD'),
(10, 'AR', 'Argentina', '54', 'Argentine peso', '$', 'ARS'),
(11, 'AM', 'Armenia', '374', 'Armenian dram', '', 'AMD'),
(12, 'AW', 'Aruba', '297', 'Aruban florin', 'ƒ', 'AWG'),
(13, 'AU', 'Australia', '61', 'Australian dollar', '$', 'AUD'),
(14, 'AT', 'Austria', '43', 'Euro', '€', 'EUR'),
(15, 'AZ', 'Azerbaijan', '994', 'Azerbaijani manat', '', 'AZN'),
(16, 'BS', 'Bahamas The', '1242', '', '', ''),
(17, 'BH', 'Bahrain', '973', 'Bahraini dinar', '.د.ب', 'BHD'),
(18, 'BD', 'Bangladesh', '880', 'Bangladeshi taka', '৳', 'BDT'),
(19, 'BB', 'Barbados', '1246', 'Barbadian dollar', '$', 'BBD'),
(20, 'BY', 'Belarus', '375', 'Belarusian ruble', 'Br', 'BYR'),
(21, 'BE', 'Belgium', '32', 'Euro', '€', 'EUR'),
(22, 'BZ', 'Belize', '501', 'Belize dollar', '$', 'BZD'),
(23, 'BJ', 'Benin', '229', 'West African CFA fra', 'Fr', 'XOF'),
(24, 'BM', 'Bermuda', '1441', 'Bermudian dollar', '$', 'BMD'),
(25, 'BT', 'Bhutan', '975', 'Bhutanese ngultrum', 'Nu.', 'BTN'),
(26, 'BO', 'Bolivia', '591', 'Bolivian boliviano', 'Bs.', 'BOB'),
(27, 'BA', 'Bosnia and Herzegovina', '387', 'Bosnia and Herzegovi', 'KM or КМ', 'BAM'),
(28, 'BW', 'Botswana', '267', 'Botswana pula', 'P', 'BWP'),
(29, 'BV', 'Bouvet Island', '0', '', '', ''),
(30, 'BR', 'Brazil', '55', 'Brazilian real', 'R$', 'BRL'),
(31, 'IO', 'British Indian Ocean Territory', '246', 'United States dollar', '$', 'USD'),
(32, 'BN', 'Brunei', '673', 'Brunei dollar', '$', 'BND'),
(33, 'BG', 'Bulgaria', '359', 'Bulgarian lev', 'лв', 'BGN'),
(34, 'BF', 'Burkina Faso', '226', 'West African CFA fra', 'Fr', 'XOF'),
(35, 'BI', 'Burundi', '257', 'Burundian franc', 'Fr', 'BIF'),
(36, 'KH', 'Cambodia', '855', 'Cambodian riel', '៛', 'KHR'),
(37, 'CM', 'Cameroon', '237', 'Central African CFA ', 'Fr', 'XAF'),
(38, 'CA', 'Canada', '1', 'Canadian dollar', '$', 'CAD'),
(39, 'CV', 'Cape Verde', '238', 'Cape Verdean escudo', 'Esc or $', 'CVE'),
(40, 'KY', 'Cayman Islands', '1345', 'Cayman Islands dolla', '$', 'KYD'),
(41, 'CF', 'Central African Republic', '236', 'Central African CFA ', 'Fr', 'XAF'),
(42, 'TD', 'Chad', '235', 'Central African CFA ', 'Fr', 'XAF'),
(43, 'CL', 'Chile', '56', 'Chilean peso', '$', 'CLP'),
(44, 'CN', 'China', '86', 'Chinese yuan', '¥ or 元', 'CNY'),
(45, 'CX', 'Christmas Island', '61', '', '', ''),
(46, 'CC', 'Cocos (Keeling) Islands', '672', 'Australian dollar', '$', 'AUD'),
(47, 'CO', 'Colombia', '57', 'Colombian peso', '$', 'COP'),
(48, 'KM', 'Comoros', '269', 'Comorian franc', 'Fr', 'KMF'),
(49, 'CG', 'Congo', '242', '', '', ''),
(50, 'CD', 'Congo The Democratic Republic Of The', '242', '', '', ''),
(51, 'CK', 'Cook Islands', '682', 'New Zealand dollar', '$', 'NZD'),
(52, 'CR', 'Costa Rica', '506', 'Costa Rican colón', '₡', 'CRC'),
(53, 'CI', 'Cote D\'Ivoire (Ivory Coast)', '225', '', '', ''),
(54, 'HR', 'Croatia (Hrvatska)', '385', '', '', ''),
(55, 'CU', 'Cuba', '53', 'Cuban convertible pe', '$', 'CUC'),
(56, 'CY', 'Cyprus', '357', 'Euro', '€', 'EUR'),
(57, 'CZ', 'Czech Republic', '420', 'Czech koruna', 'Kč', 'CZK'),
(58, 'DK', 'Denmark', '45', 'Danish krone', 'kr', 'DKK'),
(59, 'DJ', 'Djibouti', '253', 'Djiboutian franc', 'Fr', 'DJF'),
(60, 'DM', 'Dominica', '1767', 'East Caribbean dolla', '$', 'XCD'),
(61, 'DO', 'Dominican Republic', '1809', 'Dominican peso', '$', 'DOP'),
(62, 'TP', 'East Timor', '670', 'United States dollar', '$', 'USD'),
(63, 'EC', 'Ecuador', '593', 'United States dollar', '$', 'USD'),
(64, 'EG', 'Egypt', '20', 'Egyptian pound', '£ or ج.م', 'EGP'),
(65, 'SV', 'El Salvador', '503', 'United States dollar', '$', 'USD'),
(66, 'GQ', 'Equatorial Guinea', '240', 'Central African CFA ', 'Fr', 'XAF'),
(67, 'ER', 'Eritrea', '291', 'Eritrean nakfa', 'Nfk', 'ERN'),
(68, 'EE', 'Estonia', '372', 'Euro', '€', 'EUR'),
(69, 'ET', 'Ethiopia', '251', 'Ethiopian birr', 'Br', 'ETB'),
(70, 'XA', 'External Territories of Australia', '61', '', '', ''),
(71, 'FK', 'Falkland Islands', '500', 'Falkland Islands pou', '£', 'FKP'),
(72, 'FO', 'Faroe Islands', '298', 'Danish krone', 'kr', 'DKK'),
(73, 'FJ', 'Fiji Islands', '679', '', '', ''),
(74, 'FI', 'Finland', '358', 'Euro', '€', 'EUR'),
(75, 'FR', 'France', '33', 'Euro', '€', 'EUR'),
(76, 'GF', 'French Guiana', '594', '', '', ''),
(77, 'PF', 'French Polynesia', '689', 'CFP franc', 'Fr', 'XPF'),
(78, 'TF', 'French Southern Territories', '0', '', '', ''),
(79, 'GA', 'Gabon', '241', 'Central African CFA ', 'Fr', 'XAF'),
(80, 'GM', 'Gambia The', '220', '', '', ''),
(81, 'GE', 'Georgia', '995', 'Georgian lari', 'ლ', 'GEL'),
(82, 'DE', 'Germany', '49', 'Euro', '€', 'EUR'),
(83, 'GH', 'Ghana', '233', 'Ghana cedi', '₵', 'GHS'),
(84, 'GI', 'Gibraltar', '350', 'Gibraltar pound', '£', 'GIP'),
(85, 'GR', 'Greece', '30', 'Euro', '€', 'EUR'),
(86, 'GL', 'Greenland', '299', '', '', ''),
(87, 'GD', 'Grenada', '1473', 'East Caribbean dolla', '$', 'XCD'),
(88, 'GP', 'Guadeloupe', '590', '', '', ''),
(89, 'GU', 'Guam', '1671', '', '', ''),
(90, 'GT', 'Guatemala', '502', 'Guatemalan quetzal', 'Q', 'GTQ'),
(91, 'XU', 'Guernsey and Alderney', '44', '', '', ''),
(92, 'GN', 'Guinea', '224', 'Guinean franc', 'Fr', 'GNF'),
(93, 'GW', 'Guinea-Bissau', '245', 'West African CFA fra', 'Fr', 'XOF'),
(94, 'GY', 'Guyana', '592', 'Guyanese dollar', '$', 'GYD'),
(95, 'HT', 'Haiti', '509', 'Haitian gourde', 'G', 'HTG'),
(96, 'HM', 'Heard and McDonald Islands', '0', '', '', ''),
(97, 'HN', 'Honduras', '504', 'Honduran lempira', 'L', 'HNL'),
(98, 'HK', 'Hong Kong S.A.R.', '852', '', '', ''),
(99, 'HU', 'Hungary', '36', 'Hungarian forint', 'Ft', 'HUF'),
(100, 'IS', 'Iceland', '354', 'Icelandic króna', 'kr', 'ISK'),
(101, 'IN', 'India', '91', 'Indian rupee', '&#2352;', 'INR'),
(102, 'ID', 'Indonesia', '62', 'Indonesian rupiah', 'Rp', 'IDR'),
(103, 'IR', 'Iran', '98', 'Iranian rial', '﷼', 'IRR'),
(104, 'IQ', 'Iraq', '964', 'Iraqi dinar', 'ع.د', 'IQD'),
(105, 'IE', 'Ireland', '353', 'Euro', '€', 'EUR'),
(106, 'IL', 'Israel', '972', 'Israeli new shekel', '₪', 'ILS'),
(107, 'IT', 'Italy', '39', 'Euro', '€', 'EUR'),
(108, 'JM', 'Jamaica', '1876', 'Jamaican dollar', '$', 'JMD'),
(109, 'JP', 'Japan', '81', 'Japanese yen', '¥', 'JPY'),
(110, 'XJ', 'Jersey', '44', 'British pound', '£', 'GBP'),
(111, 'JO', 'Jordan', '962', 'Jordanian dinar', 'د.ا', 'JOD'),
(112, 'KZ', 'Kazakhstan', '7', 'Kazakhstani tenge', '', 'KZT'),
(113, 'KE', 'Kenya', '254', 'Kenyan shilling', 'Sh', 'KES'),
(114, 'KI', 'Kiribati', '686', 'Australian dollar', '$', 'AUD'),
(115, 'KP', 'Korea North', '850', '', '', ''),
(116, 'KR', 'Korea South', '82', '', '', ''),
(117, 'KW', 'Kuwait', '965', 'Kuwaiti dinar', 'د.ك', 'KWD'),
(118, 'KG', 'Kyrgyzstan', '996', 'Kyrgyzstani som', 'лв', 'KGS'),
(119, 'LA', 'Laos', '856', 'Lao kip', '₭', 'LAK'),
(120, 'LV', 'Latvia', '371', 'Euro', '€', 'EUR'),
(121, 'LB', 'Lebanon', '961', 'Lebanese pound', 'ل.ل', 'LBP'),
(122, 'LS', 'Lesotho', '266', 'Lesotho loti', 'L', 'LSL'),
(123, 'LR', 'Liberia', '231', 'Liberian dollar', '$', 'LRD'),
(124, 'LY', 'Libya', '218', 'Libyan dinar', 'ل.د', 'LYD'),
(125, 'LI', 'Liechtenstein', '423', 'Swiss franc', 'Fr', 'CHF'),
(126, 'LT', 'Lithuania', '370', 'Euro', '€', 'EUR'),
(127, 'LU', 'Luxembourg', '352', 'Euro', '€', 'EUR'),
(128, 'MO', 'Macau S.A.R.', '853', '', '', ''),
(129, 'MK', 'Macedonia', '389', '', '', ''),
(130, 'MG', 'Madagascar', '261', 'Malagasy ariary', 'Ar', 'MGA'),
(131, 'MW', 'Malawi', '265', 'Malawian kwacha', 'MK', 'MWK'),
(132, 'MY', 'Malaysia', '60', 'Malaysian ringgit', 'RM', 'MYR'),
(133, 'MV', 'Maldives', '960', 'Maldivian rufiyaa', '.ރ', 'MVR'),
(134, 'ML', 'Mali', '223', 'West African CFA fra', 'Fr', 'XOF'),
(135, 'MT', 'Malta', '356', 'Euro', '€', 'EUR'),
(136, 'XM', 'Man (Isle of)', '44', '', '', ''),
(137, 'MH', 'Marshall Islands', '692', 'United States dollar', '$', 'USD'),
(138, 'MQ', 'Martinique', '596', '', '', ''),
(139, 'MR', 'Mauritania', '222', 'Mauritanian ouguiya', 'UM', 'MRO'),
(140, 'MU', 'Mauritius', '230', 'Mauritian rupee', '₨', 'MUR'),
(141, 'YT', 'Mayotte', '269', '', '', ''),
(142, 'MX', 'Mexico', '52', 'Mexican peso', '$', 'MXN'),
(143, 'FM', 'Micronesia', '691', 'Micronesian dollar', '$', ''),
(144, 'MD', 'Moldova', '373', 'Moldovan leu', 'L', 'MDL'),
(145, 'MC', 'Monaco', '377', 'Euro', '€', 'EUR'),
(146, 'MN', 'Mongolia', '976', 'Mongolian tögrög', '₮', 'MNT'),
(147, 'MS', 'Montserrat', '1664', 'East Caribbean dolla', '$', 'XCD'),
(148, 'MA', 'Morocco', '212', 'Moroccan dirham', 'د.م.', 'MAD'),
(149, 'MZ', 'Mozambique', '258', 'Mozambican metical', 'MT', 'MZN'),
(150, 'MM', 'Myanmar', '95', 'Burmese kyat', 'Ks', 'MMK'),
(151, 'NA', 'Namibia', '264', 'Namibian dollar', '$', 'NAD'),
(152, 'NR', 'Nauru', '674', 'Australian dollar', '$', 'AUD'),
(153, 'NP', 'Nepal', '977', 'Nepalese rupee', '₨', 'NPR'),
(154, 'AN', 'Netherlands Antilles', '599', '', '', ''),
(155, 'NL', 'Netherlands The', '31', '', '', ''),
(156, 'NC', 'New Caledonia', '687', 'CFP franc', 'Fr', 'XPF'),
(157, 'NZ', 'New Zealand', '64', 'New Zealand dollar', '$', 'NZD'),
(158, 'NI', 'Nicaragua', '505', 'Nicaraguan córdoba', 'C$', 'NIO'),
(159, 'NE', 'Niger', '227', 'West African CFA fra', 'Fr', 'XOF'),
(160, 'NG', 'Nigeria', '234', 'Nigerian naira', '₦', 'NGN'),
(161, 'NU', 'Niue', '683', 'New Zealand dollar', '$', 'NZD'),
(162, 'NF', 'Norfolk Island', '672', '', '', ''),
(163, 'MP', 'Northern Mariana Islands', '1670', '', '', ''),
(164, 'NO', 'Norway', '47', 'Norwegian krone', 'kr', 'NOK'),
(165, 'OM', 'Oman', '968', 'Omani rial', 'ر.ع.', 'OMR'),
(166, 'PK', 'Pakistan', '92', 'Pakistani rupee', '₨', 'PKR'),
(167, 'PW', 'Palau', '680', 'Palauan dollar', '$', ''),
(168, 'PS', 'Palestinian Territory Occupied', '970', '', '', ''),
(169, 'PA', 'Panama', '507', 'Panamanian balboa', 'B/.', 'PAB'),
(170, 'PG', 'Papua new Guinea', '675', 'Papua New Guinean ki', 'K', 'PGK'),
(171, 'PY', 'Paraguay', '595', 'Paraguayan guaraní', '₲', 'PYG'),
(172, 'PE', 'Peru', '51', 'Peruvian nuevo sol', 'S/.', 'PEN'),
(173, 'PH', 'Philippines', '63', 'Philippine peso', '₱', 'PHP'),
(174, 'PN', 'Pitcairn Island', '0', '', '', ''),
(175, 'PL', 'Poland', '48', 'Polish złoty', 'zł', 'PLN'),
(176, 'PT', 'Portugal', '351', 'Euro', '€', 'EUR'),
(177, 'PR', 'Puerto Rico', '1787', '', '', ''),
(178, 'QA', 'Qatar', '974', 'Qatari riyal', 'ر.ق', 'QAR'),
(179, 'RE', 'Reunion', '262', '', '', ''),
(180, 'RO', 'Romania', '40', 'Romanian leu', 'lei', 'RON'),
(181, 'RU', 'Russia', '70', 'Russian ruble', '', 'RUB'),
(182, 'RW', 'Rwanda', '250', 'Rwandan franc', 'Fr', 'RWF'),
(183, 'SH', 'Saint Helena', '290', 'Saint Helena pound', '£', 'SHP'),
(184, 'KN', 'Saint Kitts And Nevis', '1869', 'East Caribbean dolla', '$', 'XCD'),
(185, 'LC', 'Saint Lucia', '1758', 'East Caribbean dolla', '$', 'XCD'),
(186, 'PM', 'Saint Pierre and Miquelon', '508', '', '', ''),
(187, 'VC', 'Saint Vincent And The Grenadines', '1784', 'East Caribbean dolla', '$', 'XCD'),
(188, 'WS', 'Samoa', '684', 'Samoan tālā', 'T', 'WST'),
(189, 'SM', 'San Marino', '378', 'Euro', '€', 'EUR'),
(190, 'ST', 'Sao Tome and Principe', '239', 'São Tomé and Príncip', 'Db', 'STD'),
(191, 'SA', 'Saudi Arabia', '966', 'Saudi riyal', 'ر.س', 'SAR'),
(192, 'SN', 'Senegal', '221', 'West African CFA fra', 'Fr', 'XOF'),
(193, 'RS', 'Serbia', '381', 'Serbian dinar', 'дин. or din.', 'RSD'),
(194, 'SC', 'Seychelles', '248', 'Seychellois rupee', '₨', 'SCR'),
(195, 'SL', 'Sierra Leone', '232', 'Sierra Leonean leone', 'Le', 'SLL'),
(196, 'SG', 'Singapore', '65', 'Brunei dollar', '$', 'BND'),
(197, 'SK', 'Slovakia', '421', 'Euro', '€', 'EUR'),
(198, 'SI', 'Slovenia', '386', 'Euro', '€', 'EUR'),
(199, 'XG', 'Smaller Territories of the UK', '44', '', '', ''),
(200, 'SB', 'Solomon Islands', '677', 'Solomon Islands doll', '$', 'SBD'),
(201, 'SO', 'Somalia', '252', 'Somali shilling', 'Sh', 'SOS'),
(202, 'ZA', 'South Africa', '27', 'South African rand', 'R', 'ZAR'),
(203, 'GS', 'South Georgia', '0', '', '', ''),
(204, 'SS', 'South Sudan', '211', 'South Sudanese pound', '£', 'SSP'),
(205, 'ES', 'Spain', '34', 'Euro', '€', 'EUR'),
(206, 'LK', 'Sri Lanka', '94', 'Sri Lankan rupee', 'Rs or රු', 'LKR'),
(207, 'SD', 'Sudan', '249', 'Sudanese pound', 'ج.س.', 'SDG'),
(208, 'SR', 'Suriname', '597', 'Surinamese dollar', '$', 'SRD'),
(209, 'SJ', 'Svalbard And Jan Mayen Islands', '47', '', '', ''),
(210, 'SZ', 'Swaziland', '268', 'Swazi lilangeni', 'L', 'SZL'),
(211, 'SE', 'Sweden', '46', 'Swedish krona', 'kr', 'SEK'),
(212, 'CH', 'Switzerland', '41', 'Swiss franc', 'Fr', 'CHF'),
(213, 'SY', 'Syria', '963', 'Syrian pound', '£ or ل.س', 'SYP'),
(214, 'TW', 'Taiwan', '886', 'New Taiwan dollar', '$', 'TWD'),
(215, 'TJ', 'Tajikistan', '992', 'Tajikistani somoni', 'ЅМ', 'TJS'),
(216, 'TZ', 'Tanzania', '255', 'Tanzanian shilling', 'Sh', 'TZS'),
(217, 'TH', 'Thailand', '66', 'Thai baht', '฿', 'THB'),
(218, 'TG', 'Togo', '228', 'West African CFA fra', 'Fr', 'XOF'),
(219, 'TK', 'Tokelau', '690', '', '', ''),
(220, 'TO', 'Tonga', '676', 'Tongan paʻanga', 'T$', 'TOP'),
(221, 'TT', 'Trinidad And Tobago', '1868', 'Trinidad and Tobago ', '$', 'TTD'),
(222, 'TN', 'Tunisia', '216', 'Tunisian dinar', 'د.ت', 'TND'),
(223, 'TR', 'Turkey', '90', 'Turkish lira', '', 'TRY'),
(224, 'TM', 'Turkmenistan', '7370', 'Turkmenistan manat', 'm', 'TMT'),
(225, 'TC', 'Turks And Caicos Islands', '1649', 'United States dollar', '$', 'USD'),
(226, 'TV', 'Tuvalu', '688', 'Australian dollar', '$', 'AUD'),
(227, 'UG', 'Uganda', '256', 'Ugandan shilling', 'Sh', 'UGX'),
(228, 'UA', 'Ukraine', '380', 'Ukrainian hryvnia', '₴', 'UAH'),
(229, 'AE', 'United Arab Emirates', '971', 'United Arab Emirates', 'د.إ', 'AED'),
(230, 'GB', 'United Kingdom', '44', 'British pound', '£', 'GBP'),
(231, 'US', 'United States', '1', 'United States dollar', '$', 'USD'),
(232, 'UM', 'United States Minor Outlying Islands', '1', '', '', ''),
(233, 'UY', 'Uruguay', '598', 'Uruguayan peso', '$', 'UYU'),
(234, 'UZ', 'Uzbekistan', '998', 'Uzbekistani som', '', 'UZS'),
(235, 'VU', 'Vanuatu', '678', 'Vanuatu vatu', 'Vt', 'VUV'),
(236, 'VA', 'Vatican City State (Holy See)', '39', '', '', ''),
(237, 'VE', 'Venezuela', '58', 'Venezuelan bolívar', 'Bs F', 'VEF'),
(238, 'VN', 'Vietnam', '84', 'Vietnamese đồng', '₫', 'VND'),
(239, 'VG', 'Virgin Islands (British)', '1284', '', '', ''),
(240, 'VI', 'Virgin Islands (US)', '1340', '', '', ''),
(241, 'WF', 'Wallis And Futuna Islands', '681', '', '', ''),
(242, 'EH', 'Western Sahara', '212', '', '', ''),
(243, 'YE', 'Yemen', '967', 'Yemeni rial', '﷼', 'YER'),
(244, 'YU', 'Yugoslavia', '38', '', '', ''),
(245, 'ZM', 'Zambia', '260', 'Zambian kwacha', 'ZK', 'ZMW'),
(246, 'ZW', 'Zimbabwe', '263', 'Botswana pula', 'P', 'BWP');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_charge`
--

CREATE TABLE `delivery_charge` (
  `charge_id` int(11) NOT NULL,
  `area_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `lat_long` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `price_charge` decimal(20,2) DEFAULT NULL,
  `additional_delivery_charge` decimal(20,2) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `delivery_charge`
--

INSERT INTO `delivery_charge` (`charge_id`, `area_name`, `restaurant_id`, `lat_long`, `price_charge`, `additional_delivery_charge`, `created_by`, `created_date`, `updated_by`, `updated_date`, `is_masterdata`) VALUES
(83, 'Bhopal', 1534, '[23.22624,77.48725]~[23.22563,77.48614]~[23.22354,77.48751]~[23.2243,77.48877]~[23.22498,77.48872]~[23.22533,77.4888]~[23.2257,77.48827]', '10.00', '10.00', 1, '2021-07-20 07:36:08', NULL, NULL, '0'),
(126, '0-1000km', 589, '[25.00721,154.48558]~[-63.93677,13.86058]~[-0.70174,-21.82301]~[63.3133,-19.53785]~[77.00024,107.55199]', '2.90', '3.70', 1, '2022-05-19 09:29:35', NULL, NULL, '0'),
(127, 'USA', 1799, '[62.16362,-7.97346]~[-27.25821,-116.25471]~[30.86105,-174.26252]~[79.77265,178.35467]', '2.70', '3.90', 1, '2022-05-19 09:30:45', NULL, NULL, '0'),
(145, 'USA', 1996, '[-8.23058,-84.89079]~[2.98755,-64.16096]~[55.01608,-34.56071]~[70.24516,-71.75732]~[78.54012,-123.69442]~[70.20436,-174.22526]~[40.49874,-179.31038]~[5.38875,-133.33106]', '10.00', '5.00', 1, '2022-06-02 06:38:47', NULL, NULL, '0'),
(148, 'NYC', 2205, '[40.74264,-74.00989]~[40.74251,-73.97453]~[40.73495,-73.97787]~[40.72843,-73.97298]~[40.71109,-73.97762]~[40.70517,-74.0092]~[40.70081,-74.01647]~[40.70582,-74.01997]~[40.71357,-74.01918]~[40.71965,-74.01501]~[40.71714,-74.01358]~[40.72339,-74.01178]', '2.00', '1.00', 1, '2022-11-22 12:34:53', NULL, NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_method`
--

CREATE TABLE `delivery_method` (
  `delivery_method_id` int(11) NOT NULL,
  `delivery_method_slug` varchar(50) DEFAULT NULL,
  `display_name_en` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `delivery_method`
--

INSERT INTO `delivery_method` (`delivery_method_id`, `delivery_method_slug`, `display_name_en`, `status`) VALUES
(1, 'relay', 'Relay', 0),
(2, 'doordash', 'DoorDash', 0);

-- --------------------------------------------------------

--
-- Table structure for table `doordash_relay_details`
--

CREATE TABLE `doordash_relay_details` (
  `entity_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `delivery_method` enum('doordash','relay') DEFAULT NULL,
  `api_slug` varchar(50) DEFAULT NULL,
  `doordash_delivery_id` varchar(50) DEFAULT NULL,
  `relay_order_key` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `external_delivery_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'for both relay & doordash :: time().order_id sent to this key in api request',
  `delivery_fee` decimal(20,2) DEFAULT NULL COMMENT 'in cents',
  `currency_code` varchar(255) DEFAULT NULL,
  `pickup_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `delivery_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `delivery_tracking_url` varchar(255) DEFAULT NULL,
  `driver_details` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `api_request` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `api_response` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `callbacknoti_resp` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `driver_traking_map`
--

CREATE TABLE `driver_traking_map` (
  `traking_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `latitude` varchar(30) DEFAULT NULL,
  `longitude` varchar(30) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `driver_traking_map`
--

INSERT INTO `driver_traking_map` (`traking_id`, `driver_id`, `latitude`, `longitude`, `created_date`) VALUES
(246, 44, '23.771601666667', '86.75791', '2021-03-25 10:36:52'),
(247, 44, '23.7715855', '86.7579206', '2021-03-25 10:36:55'),
(248, 44, '23.7715772', '86.7579188', '2021-03-25 10:37:13'),
(249, 44, '23.771601666667', '86.75791', '2021-03-25 10:37:13'),
(250, 44, '23.771581', '86.7579243', '2021-03-25 10:37:51'),
(251, 44, '23.77157', '86.75794', '2021-03-25 10:39:34'),
(252, 44, '23.77157', '86.75794', '2021-03-25 10:39:37'),
(253, 44, '23.77157', '86.75794', '2021-03-25 10:39:40'),
(254, 44, '23.77157', '86.75794', '2021-03-25 10:39:53'),
(255, 44, '23.771613333333', '86.757711666667', '2021-03-25 10:40:12'),
(260, 44, '23.771623333333', '86.7577', '2021-03-25 10:58:16'),
(261, 44, '23.7715724', '86.7579229', '2021-03-25 10:58:16'),
(262, 44, '23.771623333333', '86.7577', '2021-03-25 10:58:17'),
(263, 44, '23.7715805', '86.7579243', '2021-03-25 10:59:10'),
(264, 44, '23.7715805', '86.7579243', '2021-03-25 11:19:24'),
(265, 44, '23.771623333333', '86.7577', '2021-03-25 11:19:24'),
(266, 44, '23.771623333333', '86.7577', '2021-03-25 11:19:25'),
(267, 44, '23.771623333333', '86.7577', '2021-03-25 11:19:50'),
(268, 44, '23.771582', '86.7579236', '2021-03-25 11:19:52'),
(269, 44, '23.771623333333', '86.7577', '2021-03-25 11:22:06'),
(270, 44, '23.77165', '86.757958333333', '2021-03-25 11:26:38'),
(272, 44, '26.1046', '74.3804', '2021-03-25 11:40:22'),
(273, 44, '26.1046', '74.3804', '2021-03-25 11:40:23'),
(274, 44, '26.1046', '74.3804', '2021-03-25 11:40:54'),
(275, 44, '26.1046', '74.3804', '2021-03-25 11:55:17'),
(276, 44, '26.1046', '74.3804', '2021-03-25 11:55:18'),
(277, 44, '26.1046', '74.3804', '2021-03-25 11:55:39'),
(278, 44, '26.1046', '74.3804', '2021-03-25 11:56:00'),
(279, 44, '26.1046', '74.3804', '2021-03-25 11:58:17'),
(280, 44, '26.1046', '74.3804', '2021-03-25 11:58:20'),
(281, 44, '26.1046', '74.3804', '2021-03-25 11:58:31'),
(282, 44, '26.1046', '74.3804', '2021-03-25 12:03:35'),
(283, 44, '26.1046', '74.3804', '2021-03-25 12:03:46'),
(284, 44, '23.7715823', '86.7579235', '2021-03-25 12:11:08'),
(285, 44, '23.7715811', '86.7579225', '2021-03-25 12:17:23'),
(286, 44, '23.77165', '86.757958333333', '2021-03-25 12:17:24'),
(287, 44, '23.7715886', '86.7579255', '2021-03-25 12:17:40'),
(288, 44, '23.77162', '86.757971666667', '2021-03-25 12:17:47'),
(289, 44, '23.771611666667', '86.757965', '2021-03-25 12:17:50'),
(290, 44, '23.771611666667', '86.757965', '2021-03-25 12:17:50'),
(291, 44, '26.1046', '74.3804', '2021-03-25 12:21:01'),
(317, 44, '23.7715795', '86.7579238', '2021-03-26 05:07:07'),
(318, 44, '23.771516666667', '86.757981666667', '2021-03-26 05:07:12'),
(319, 44, '23.771561666667', '86.757948333333', '2021-03-26 05:07:27'),
(320, 44, '23.771556666667', '86.757866666667', '2021-03-26 05:07:38'),
(321, 44, '23.77162', '86.757843333333', '2021-03-26 05:07:48'),
(322, 44, '23.771618333333', '86.758031666667', '2021-03-26 05:07:58'),
(397, 44, '23.7715788', '86.7579212', '2021-03-26 07:18:42'),
(398, 44, '23.771615', '86.757985', '2021-03-26 07:18:47'),
(399, 44, '23.7715788', '86.757922', '2021-03-26 07:19:05'),
(400, 44, '23.771615', '86.757985', '2021-03-26 07:19:10'),
(401, 44, '23.7715788', '86.7579212', '2021-03-26 07:19:33'),
(402, 44, '23.7715788', '86.7579212', '2021-03-26 07:19:34'),
(403, 44, '23.771615', '86.757985', '2021-03-26 07:19:34'),
(404, 44, '23.7715788', '86.7579212', '2021-03-26 07:19:34'),
(405, 44, '23.771615', '86.757985', '2021-03-26 07:19:42'),
(406, 44, '23.771615', '86.757985', '2021-03-26 07:20:13'),
(407, 44, '23.771615', '86.757985', '2021-03-26 07:20:17'),
(506, 44, '23.771718333333', '86.75791', '2021-03-30 06:15:07'),
(507, 44, '23.7715795', '86.7579238', '2021-03-30 06:15:10'),
(508, 44, '23.7715788', '86.757922', '2021-03-30 06:27:25'),
(578, 44, '23.771613333333', '86.758021666667', '2021-04-01 14:26:38'),
(579, 44, '23.7715788', '86.7579212', '2021-04-01 14:26:40'),
(580, 44, '23.771613333333', '86.758021666667', '2021-04-01 14:26:43'),
(581, 44, '23.7715825', '86.7579235', '2021-04-01 14:27:41'),
(582, 44, '23.771613333333', '86.758021666667', '2021-04-01 14:27:43'),
(586, 44, '23.771613333333', '86.758021666667', '2021-04-01 14:28:06'),
(587, 44, '23.771613333333', '86.758021666667', '2021-04-01 14:28:10'),
(593, 44, '23.771613333333', '86.758021666667', '2021-04-01 14:28:27'),
(594, 44, '23.7715815', '86.7579241', '2021-04-01 14:28:43'),
(632, 44, '23.7715789', '86.7579185', '2021-04-20 10:28:54'),
(633, 44, '23.7715789', '86.7579185', '2021-04-20 10:29:02'),
(634, 44, '23.7723427', '86.7571931', '2021-04-20 10:29:26'),
(876, 44, '23.7715876', '86.7579137', '2021-05-06 14:48:20'),
(1874, 44, '23.771574', '86.7579143', '2021-06-14 07:21:22'),
(1875, 44, '23.771574', '86.7579143', '2021-06-14 07:21:22'),
(1876, 44, '23.771574', '86.7579143', '2021-06-14 07:21:32'),
(1877, 44, '23.771574', '86.7579143', '2021-06-14 07:21:33'),
(1878, 44, '23.771574', '86.7579143', '2021-06-14 07:21:33'),
(1879, 44, '23.7715727', '86.7579115', '2021-06-14 07:21:44'),
(1880, 44, '23.771573', '86.7579099', '2021-06-14 07:53:15'),
(1881, 44, '23.7715727', '86.7579115', '2021-06-14 07:55:04'),
(1882, 44, '23.771573', '86.7579137', '2021-06-14 12:47:05'),
(1883, 44, '23.7715755', '86.7579141', '2021-06-14 13:04:27'),
(1884, 44, '23.7715755', '86.7579141', '2021-06-14 13:04:44'),
(1885, 44, '23.771575', '86.7579144', '2021-06-14 13:04:56'),
(1886, 44, '23.771575', '86.7579144', '2021-06-14 13:05:22'),
(1887, 44, '23.7727142', '86.7571931', '2021-06-14 13:06:21'),
(1888, 44, '23.7715727', '86.7579115', '2021-06-14 13:06:33'),
(1889, 44, '23.7715727', '86.7579115', '2021-06-14 13:06:33'),
(1890, 44, '23.7715745', '86.7579144', '2021-06-14 13:08:01'),
(1891, 44, '23.7715759', '86.7579137', '2021-06-14 13:08:02'),
(1892, 44, '23.7715766', '86.7579134', '2021-06-14 13:08:49'),
(1893, 44, '23.7715761', '86.7579136', '2021-06-14 13:08:50'),
(1894, 44, '23.771573', '86.7579137', '2021-06-14 13:09:47'),
(1895, 44, '23.771573', '86.7579137', '2021-06-14 13:09:48'),
(1896, 44, '23.771573', '86.7579137', '2021-06-14 13:09:49'),
(1897, 44, '23.771573', '86.7579137', '2021-06-14 13:09:52'),
(1898, 44, '23.7715768', '86.7579131', '2021-06-14 13:10:01'),
(1899, 44, '23.7715768', '86.7579131', '2021-06-14 13:10:01'),
(1900, 44, '23.7715768', '86.7579131', '2021-06-14 13:10:06'),
(1901, 44, '23.7715757', '86.7579138', '2021-06-14 13:10:10'),
(1902, 44, '23.7715764', '86.7579135', '2021-06-14 13:12:11'),
(1903, 44, '23.7715757', '86.7579138', '2021-06-14 13:12:12'),
(1904, 364, '14.4647966', '79.9886795', '2021-06-14 13:35:07'),
(1905, 364, '14.4647966', '79.9886795', '2021-06-14 13:35:07'),
(1906, 364, '14.4648034', '79.9886514', '2021-06-14 13:39:08'),
(1907, 364, '14.4648034', '79.9886514', '2021-06-14 13:39:08'),
(1908, 364, '14.4648029', '79.9886494', '2021-06-14 13:40:20'),
(1909, 364, '14.4648039', '79.9886522', '2021-06-14 13:42:22'),
(1910, 364, '14.464804', '79.9886488', '2021-06-14 13:42:45'),
(1911, 364, '14.4648042', '79.988647', '2021-06-14 13:43:20'),
(1958, 364, '14.4648048', '79.9886486', '2021-06-14 14:47:46'),
(1959, 364, '14.4647957', '79.9886802', '2021-06-14 14:47:46'),
(1960, 364, '14.4647957', '79.9886802', '2021-06-14 14:47:46'),
(1961, 364, '14.4647957', '79.9886802', '2021-06-14 14:48:10'),
(1962, 364, '14.4648041', '79.9886482', '2021-06-14 14:48:18'),
(1963, 364, '14.4648041', '79.9886482', '2021-06-14 14:48:19'),
(1964, 364, '14.4647966', '79.9886795', '2021-06-14 14:48:20'),
(1965, 364, '14.4648048', '79.9886489', '2021-06-14 14:48:52'),
(1966, 364, '14.4648048', '79.9886489', '2021-06-14 14:48:52'),
(1967, 364, '14.4648048', '79.9886489', '2021-06-14 14:48:54'),
(1968, 364, '14.4648048', '79.9886489', '2021-06-14 14:48:56'),
(1969, 364, '14.4647942', '79.98868', '2021-06-14 14:54:06'),
(1970, 364, '14.4647952', '79.9886803', '2021-06-14 14:54:31'),
(1971, 364, '14.464806', '79.9886512', '2021-06-14 14:56:28'),
(1972, 364, '14.464806', '79.9886512', '2021-06-14 14:56:41'),
(1973, 364, '14.4648036', '79.9886511', '2021-06-14 14:58:45'),
(1974, 364, '14.4648029', '79.9886507', '2021-06-14 14:58:45'),
(1975, 364, '14.464803', '79.988649', '2021-06-14 14:59:15'),
(1976, 364, '14.464803', '79.9886499', '2021-06-14 14:59:23'),
(1977, 364, '14.4647934', '79.9886791', '2021-06-14 15:24:50'),
(1978, 364, '14.4647957', '79.9886802', '2021-06-14 15:26:03'),
(1979, 364, '14.4648014', '79.9886488', '2021-06-14 15:26:16'),
(1980, 364, '14.4648018', '79.9886529', '2021-06-14 15:26:24'),
(1981, 364, '14.4648018', '79.9886529', '2021-06-14 15:26:24'),
(1982, 364, '14.4648018', '79.9886529', '2021-06-14 15:26:24'),
(1983, 364, '14.4647966', '79.9886795', '2021-06-14 15:26:44'),
(1984, 364, '14.464799', '79.9886704', '2021-06-14 15:27:31'),
(2023, 364, '14.464802', '79.9886509', '2021-06-15 05:10:33'),
(2024, 364, '14.4647975', '79.9886791', '2021-06-15 05:10:48'),
(2025, 364, '14.4647975', '79.9886791', '2021-06-15 05:10:48'),
(2035, 364, '14.4648049', '79.9886453', '2021-06-15 05:14:16'),
(2037, 364, '14.4648061', '79.9886536', '2021-06-15 05:14:30'),
(2845, 364, '14.4647976', '79.9886786', '2021-06-15 07:37:13'),
(2866, 364, '14.4647997', '79.9886616', '2021-06-15 09:41:43'),
(2867, 364, '14.4647974', '79.9886792', '2021-06-15 09:42:16'),
(2868, 364, '14.4648038', '79.988646', '2021-06-15 09:42:36'),
(2869, 364, '14.4648026', '79.9886543', '2021-06-15 09:43:03'),
(2870, 364, '14.4647949', '79.9886788', '2021-06-15 09:43:18'),
(2871, 364, '14.4648047', '79.9886497', '2021-06-15 09:44:19'),
(2872, 364, '14.4648041', '79.9886433', '2021-06-15 09:44:44'),
(2873, 364, '14.4647967', '79.9886787', '2021-06-15 09:45:00'),
(2874, 364, '14.4648029', '79.9886495', '2021-06-15 09:46:58'),
(2875, 364, '14.4647942', '79.98868', '2021-06-15 09:49:07'),
(2876, 364, '14.464801', '79.9886661', '2021-06-15 09:50:42'),
(2877, 364, '14.4648037', '79.9886511', '2021-06-15 09:52:03'),
(2878, 364, '14.4648023', '79.9886547', '2021-06-15 09:52:28'),
(2879, 364, '14.4648038', '79.9886488', '2021-06-15 09:53:18'),
(2880, 364, '14.4648041', '79.9886416', '2021-06-15 09:53:53'),
(2881, 364, '14.4648056', '79.9886519', '2021-06-15 09:55:17'),
(2882, 364, '14.4647936', '79.9886802', '2021-06-15 09:56:35'),
(2914, 364, '14.4648015', '79.9886498', '2021-06-15 10:43:29'),
(2917, 364, '14.464797', '79.9886794', '2021-06-15 10:44:25'),
(2925, 364, '14.4648038', '79.9886511', '2021-06-15 10:49:23'),
(2926, 364, '14.4648034', '79.9886516', '2021-06-15 10:50:20'),
(2927, 364, '14.4647973', '79.9886793', '2021-06-15 10:51:31'),
(2932, 364, '14.464801', '79.988651', '2021-06-15 10:52:38'),
(2933, 364, '14.4647968', '79.9886795', '2021-06-15 10:53:23'),
(2942, 364, '14.4648023', '79.9886541', '2021-06-15 10:56:47'),
(2945, 364, '14.4648009', '79.9886526', '2021-06-15 10:57:21'),
(2949, 44, '23.7715735', '86.7579141', '2021-06-15 11:04:17'),
(2950, 44, '23.7715735', '86.7579141', '2021-06-15 11:06:25'),
(2951, 364, '14.4648014', '79.9886499', '2021-06-15 11:07:42'),
(2952, 364, '14.4648016', '79.9886756', '2021-06-15 11:07:58'),
(2953, 364, '14.4648035', '79.9886513', '2021-06-15 11:09:46'),
(2956, 364, '14.4648029', '79.9886516', '2021-06-15 11:10:06'),
(2957, 364, '14.4648038', '79.9886512', '2021-06-15 11:10:36'),
(2958, 364, '14.4648038', '79.9886512', '2021-06-15 11:10:41'),
(2959, 364, '14.4648017', '79.9886556', '2021-06-15 11:10:52'),
(2960, 364, '14.4648017', '79.9886556', '2021-06-15 11:11:17'),
(2961, 364, '14.464802', '79.9886557', '2021-06-15 11:11:18'),
(2962, 364, '14.4647952', '79.9886803', '2021-06-15 11:13:11'),
(2963, 364, '14.4647952', '79.9886803', '2021-06-15 11:13:39'),
(2964, 364, '14.4648052', '79.9886458', '2021-06-15 11:14:34'),
(2965, 364, '14.4648047', '79.9886469', '2021-06-15 11:15:03'),
(2966, 364, '14.4647968', '79.9886795', '2021-06-15 11:16:33'),
(2967, 364, '14.4647968', '79.9886795', '2021-06-15 11:16:34'),
(2968, 364, '14.4647975', '79.988679', '2021-06-15 11:17:45'),
(2969, 364, '14.4647975', '79.988679', '2021-06-15 11:17:47'),
(2970, 364, '14.4647975', '79.988679', '2021-06-15 11:17:56'),
(2971, 364, '14.4648024', '79.9886494', '2021-06-15 11:17:56'),
(2972, 364, '14.4648013', '79.9886482', '2021-06-15 11:18:23'),
(2973, 364, '14.4648013', '79.9886482', '2021-06-15 11:18:23'),
(2974, 364, '14.4648002', '79.9886573', '2021-06-15 11:18:59'),
(2975, 364, '14.4647961', '79.98868', '2021-06-15 11:19:00'),
(2976, 364, '14.4648001', '79.9886528', '2021-06-15 11:19:31'),
(2977, 364, '14.4647928', '79.9886788', '2021-06-15 11:19:34'),
(2978, 364, '14.4648051', '79.9886489', '2021-06-15 11:19:50'),
(2979, 364, '14.4648024', '79.988652', '2021-06-15 11:20:55'),
(2980, 364, '14.4648027', '79.9886527', '2021-06-15 11:21:04'),
(2981, 364, '14.4648027', '79.9886527', '2021-06-15 11:21:11'),
(2982, 364, '14.4648027', '79.9886527', '2021-06-15 11:21:21'),
(2983, 364, '14.4648011', '79.9886593', '2021-06-15 11:21:33'),
(2984, 364, '14.4648014', '79.9886549', '2021-06-15 11:21:33'),
(2985, 364, '14.4648014', '79.9886549', '2021-06-15 11:21:46'),
(2986, 364, '14.4647982', '79.9886723', '2021-06-15 11:21:55'),
(2987, 364, '14.4647982', '79.9886723', '2021-06-15 11:22:01'),
(2988, 364, '14.464803', '79.9886506', '2021-06-15 11:22:02'),
(2989, 364, '14.464803', '79.9886506', '2021-06-15 11:22:02'),
(2992, 364, '14.464802', '79.9886549', '2021-06-15 11:22:19'),
(2993, 364, '14.464802', '79.9886549', '2021-06-15 11:22:21'),
(2996, 364, '14.4647952', '79.9886803', '2021-06-15 11:22:57'),
(2997, 364, '14.4647996', '79.9886679', '2021-06-15 11:24:08'),
(2998, 364, '14.464797', '79.9886794', '2021-06-15 11:24:51'),
(3021, 44, '23.7715728', '86.7579108', '2021-06-15 11:41:08'),
(3022, 44, '23.7715728', '86.7579108', '2021-06-15 11:41:20'),
(3023, 364, '14.464803', '79.9886476', '2021-06-15 11:47:24'),
(3024, 364, '14.464805', '79.988668333333', '2021-06-15 11:47:48'),
(3025, 364, '14.4648016', '79.9886476', '2021-06-15 11:47:59'),
(3026, 44, '23.771598333333', '86.757935', '2021-06-15 11:48:16'),
(3027, 44, '23.771573', '86.7579137', '2021-06-15 11:48:26'),
(3028, 44, '23.771598333333', '86.757935', '2021-06-15 11:49:21'),
(3029, 44, '23.771598333333', '86.757935', '2021-06-15 11:49:25'),
(3030, 44, '23.771598333333', '86.757935', '2021-06-15 11:49:27'),
(3032, 44, '23.771598333333', '86.757935', '2021-06-15 11:55:57'),
(3046, 44, '23.7715735', '86.7579141', '2021-06-15 12:03:07'),
(3047, 44, '23.7715728', '86.7579131', '2021-06-15 12:03:07'),
(3048, 44, '23.7715728', '86.7579108', '2021-06-15 12:03:07'),
(3049, 44, '23.7715735', '86.7579141', '2021-06-15 12:03:14'),
(3050, 44, '23.7715728', '86.7579108', '2021-06-15 12:03:14'),
(3051, 44, '23.7715728', '86.7579131', '2021-06-15 12:03:14'),
(3067, 44, '23.771598333333', '86.757935', '2021-06-15 12:25:09'),
(3068, 44, '23.771573', '86.7579137', '2021-06-15 12:25:09'),
(3069, 44, '23.771598333333', '86.757935', '2021-06-15 12:25:17'),
(3070, 44, '23.771598333333', '86.757935', '2021-06-15 12:25:21'),
(3071, 44, '23.771598333333', '86.757935', '2021-06-15 12:25:30'),
(3072, 44, '23.771598333333', '86.757935', '2021-06-15 12:25:36'),
(3073, 44, '23.771598333333', '86.757935', '2021-06-15 12:25:37'),
(3074, 44, '23.771598333333', '86.757935', '2021-06-15 12:25:55'),
(3075, 44, '23.771573', '86.7579137', '2021-06-15 12:25:56'),
(3076, 44, '23.771598333333', '86.757935', '2021-06-15 12:26:02'),
(3077, 44, '23.7715727', '86.7579115', '2021-06-15 12:26:03'),
(3078, 44, '23.771598333333', '86.757935', '2021-06-15 12:26:05'),
(3079, 44, '23.771598333333', '86.757935', '2021-06-15 12:26:50'),
(3080, 44, '23.7715728', '86.7579108', '2021-06-15 12:27:06'),
(3083, 44, '23.771598333333', '86.757935', '2021-06-15 12:27:39'),
(3084, 44, '23.771574', '86.7579143', '2021-06-15 12:27:40'),
(3087, 44, '23.771598333333', '86.757935', '2021-06-15 12:28:02'),
(3088, 44, '23.771598333333', '86.757935', '2021-06-15 12:28:04'),
(3092, 44, '23.7715728', '86.7579131', '2021-06-15 12:28:21'),
(3095, 44, '23.7715728', '86.7579131', '2021-06-15 12:28:28'),
(3096, 44, '23.7715728', '86.7579131', '2021-06-15 12:28:35'),
(3097, 44, '23.771598333333', '86.757935', '2021-06-15 12:28:36'),
(3098, 44, '23.771598333333', '86.757935', '2021-06-15 12:28:39'),
(3101, 44, '23.771598333333', '86.757935', '2021-06-15 12:29:33'),
(3106, 364, '14.464805', '79.988668333333', '2021-06-15 12:33:16'),
(3107, 364, '14.4648021', '79.988652', '2021-06-15 12:33:17'),
(3108, 364, '14.464805', '79.988668333333', '2021-06-15 12:33:20'),
(3109, 364, '14.464805', '79.988668333333', '2021-06-15 12:33:41'),
(3110, 364, '14.4648029', '79.9886488', '2021-06-15 12:33:42'),
(3134, 364, '14.464805', '79.988668333333', '2021-06-15 12:48:02'),
(3135, 364, '14.4647933', '79.9886789', '2021-06-15 12:48:03'),
(3138, 364, '14.464805', '79.988668333333', '2021-06-15 12:48:40'),
(3139, 364, '14.4647991', '79.9886696', '2021-06-15 12:48:40'),
(3145, 44, '23.771598333333', '86.757935', '2021-06-15 12:49:40'),
(3146, 44, '23.7715735', '86.7579141', '2021-06-15 12:49:40'),
(3152, 44, '23.771573', '86.7579099', '2021-06-15 12:50:50'),
(3153, 44, '23.771598333333', '86.757935', '2021-06-15 12:50:52'),
(3154, 44, '23.771598333333', '86.757935', '2021-06-15 12:51:03'),
(3155, 44, '23.771598333333', '86.757935', '2021-06-15 12:51:03'),
(3156, 44, '23.771598333333', '86.757935', '2021-06-15 12:51:09'),
(3157, 44, '23.771598333333', '86.757935', '2021-06-15 12:51:56'),
(3158, 44, '23.771573', '86.7579099', '2021-06-15 12:51:57'),
(3159, 44, '23.771598333333', '86.757935', '2021-06-15 12:52:24'),
(3160, 44, '23.7715727', '86.7579123', '2021-06-15 12:52:34'),
(3161, 44, '23.771598333333', '86.757935', '2021-06-15 12:52:39'),
(3164, 44, '23.771573', '86.7579137', '2021-06-15 12:52:54'),
(3165, 44, '23.7715728', '86.7579108', '2021-06-15 12:53:28'),
(3166, 44, '23.771598333333', '86.757935', '2021-06-15 12:53:38'),
(3167, 44, '23.771598333333', '86.757935', '2021-06-15 12:53:50'),
(3168, 44, '23.7715727', '86.7579115', '2021-06-15 12:54:38'),
(3169, 44, '23.771598333333', '86.757935', '2021-06-15 12:54:56'),
(3170, 44, '23.7715728', '86.7579108', '2021-06-15 12:54:56'),
(3172, 44, '23.7715727', '86.7579115', '2021-06-15 12:56:19'),
(3173, 44, '23.771598333333', '86.757935', '2021-06-15 12:56:30'),
(3174, 44, '23.7715727', '86.7579123', '2021-06-15 12:56:32'),
(3175, 44, '23.7715735', '86.7579141', '2021-06-15 12:58:11'),
(3192, 44, '23.771598333333', '86.757935', '2021-06-15 13:13:22'),
(3193, 44, '23.771573', '86.7579099', '2021-06-15 13:13:23'),
(3195, 44, '23.771598333333', '86.757935', '2021-06-15 13:13:29'),
(3197, 44, '23.7715728', '86.7579131', '2021-06-15 13:13:45'),
(3199, 44, '23.771598333333', '86.757935', '2021-06-15 13:14:12'),
(3200, 44, '23.771598333333', '86.757935', '2021-06-15 13:14:30'),
(3203, 44, '23.771598333333', '86.757935', '2021-06-15 13:14:41'),
(3210, 364, '14.4648045', '79.9886507', '2021-06-15 13:16:01'),
(3212, 364, '14.4648015', '79.9886513', '2021-06-15 13:17:27'),
(3220, 44, '23.7715728', '86.7579131', '2021-06-15 13:20:47'),
(3221, 44, '23.771598333333', '86.757935', '2021-06-15 13:20:59'),
(3223, 44, '23.771598333333', '86.757935', '2021-06-15 13:21:41'),
(3224, 44, '23.7715728', '86.7579131', '2021-06-15 13:21:41'),
(3225, 44, '23.771598333333', '86.757935', '2021-06-15 13:21:42'),
(3226, 44, '23.7715728', '86.7579131', '2021-06-15 13:21:42'),
(3227, 44, '23.771598333333', '86.757935', '2021-06-15 13:21:44'),
(3229, 44, '23.771574', '86.7579143', '2021-06-15 13:22:02'),
(3231, 44, '23.7715728', '86.7579131', '2021-06-15 13:22:23'),
(3240, 44, '23.7715728', '86.7579131', '2021-06-15 13:27:45'),
(3243, 44, '23.7715728', '86.7579108', '2021-06-15 13:28:04'),
(3261, 364, '14.4647951', '79.9886788', '2021-06-15 13:41:06'),
(3262, 364, '14.4647951', '79.9886788', '2021-06-15 13:41:06'),
(3301, 364, '14.4648048', '79.9886476', '2021-06-16 06:42:37'),
(3302, 364, '14.464785', '79.988643333333', '2021-06-16 06:42:40'),
(3303, 364, '14.464785', '79.988643333333', '2021-06-16 06:43:00'),
(3304, 364, '14.464803', '79.9886468', '2021-06-16 06:43:01'),
(3305, 364, '14.464785', '79.988643333333', '2021-06-16 06:45:13'),
(3306, 364, '14.4647929', '79.9886794', '2021-06-16 06:45:13'),
(3307, 364, '14.4648055', '79.9886486', '2021-06-16 06:45:25'),
(3308, 364, '14.464785', '79.988643333333', '2021-06-16 06:45:25'),
(3309, 364, '14.464785', '79.988643333333', '2021-06-16 07:08:52'),
(3310, 364, '14.4648034', '79.988653', '2021-06-16 07:08:52'),
(3311, 364, '14.4648034', '79.988653', '2021-06-16 07:08:52'),
(3312, 364, '14.464797', '79.9886794', '2021-06-16 07:33:13'),
(3313, 364, '14.4648229', '79.9886565', '2021-06-17 02:30:26'),
(3314, 364, '14.4648229', '79.9886565', '2021-06-17 02:30:26'),
(3315, 364, '14.464736666667', '79.988645', '2021-06-17 02:37:54'),
(3316, 364, '14.4648099', '79.9886667', '2021-06-17 02:37:55'),
(3317, 364, '14.4648152', '79.9886596', '2021-06-17 02:45:42'),
(3318, 364, '14.464736666667', '79.988645', '2021-06-17 02:45:42'),
(3319, 364, '14.4648186', '79.9886581', '2021-06-17 02:46:14'),
(3320, 364, '14.4648186', '79.9886581', '2021-06-17 02:46:14'),
(3321, 364, '14.4648186', '79.9886581', '2021-06-17 02:46:14'),
(3322, 364, '14.4648184', '79.9886586', '2021-06-17 02:49:56'),
(3323, 364, '14.4648244', '79.9886554', '2021-06-17 05:27:58'),
(3324, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:27:58'),
(3325, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:28:02'),
(3326, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:28:15'),
(3327, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:28:17'),
(3328, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:28:25'),
(3329, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:28:27'),
(3330, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:28:41'),
(3331, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:28:43'),
(3332, 364, '14.464896666667', '79.988566666667', '2021-06-17 05:28:50'),
(3333, 364, '14.464795', '79.9886789', '2021-06-17 05:32:32'),
(3334, 364, '14.4647939', '79.9886769', '2021-06-17 05:36:02'),
(3335, 364, '14.464693333333', '79.988633333333', '2021-06-17 05:36:04'),
(3356, 364, '14.4648209', '79.9886555', '2021-06-17 06:57:17'),
(3357, 364, '14.464693333333', '79.988633333333', '2021-06-17 06:57:17'),
(3358, 364, '14.464693333333', '79.988633333333', '2021-06-17 06:57:19'),
(3359, 364, '14.464693333333', '79.988633333333', '2021-06-17 06:57:59'),
(3360, 364, '14.464693333333', '79.988633333333', '2021-06-17 06:58:12'),
(3361, 364, '14.464693333333', '79.988633333333', '2021-06-17 06:58:19'),
(3362, 364, '14.464693333333', '79.988633333333', '2021-06-17 06:58:29'),
(3363, 364, '14.4648243', '79.9886532', '2021-06-17 06:58:30'),
(3364, 364, '14.4648192', '79.9886565', '2021-06-17 07:01:36'),
(3374, 364, '14.4647932', '79.9886766', '2021-06-17 10:19:20'),
(20398, 400, '7.9465', '1.0232', '2021-08-17 11:19:30'),
(20399, 400, '7.9465', '1.0232', '2021-08-17 11:19:38'),
(20400, 400, '7.9465', '1.0232', '2021-08-17 11:19:38'),
(20401, 400, '7.9465', '1.0232', '2021-08-17 11:20:10'),
(20402, 400, '7.9465', '1.0232', '2021-08-17 11:20:10'),
(20403, 400, '7.9465', '1.0232', '2021-08-17 11:20:32'),
(20404, 400, '7.9465', '1.0232', '2021-08-17 11:20:32'),
(20405, 400, '7.9465', '1.0232', '2021-08-17 11:21:26'),
(20406, 400, '7.9465', '1.0232', '2021-08-17 11:21:26'),
(20407, 400, '7.9465', '1.0232', '2021-08-17 11:22:02'),
(20408, 400, '7.9465', '1.0232', '2021-08-17 11:22:03'),
(20409, 400, '7.9465', '1.0232', '2021-08-17 12:06:59'),
(20410, 400, '7.9465', '1.0232', '2021-08-17 12:07:00'),
(20416, 400, '7.9465', '1.0232', '2021-08-17 12:13:54'),
(20417, 400, '7.9465', '1.0232', '2021-08-17 12:13:54'),
(20418, 400, '7.9465', '1.0232', '2021-08-17 12:14:13'),
(20432, 400, '7.9465', '1.0232', '2021-08-17 12:47:24'),
(20462, 400, '7.9465', '1.0232', '2021-08-17 12:58:36'),
(20463, 400, '7.9465', '1.0232', '2021-08-17 12:58:36'),
(20482, 400, '7.9465', '1.0232', '2021-08-17 13:11:54'),
(20483, 400, '7.9465', '1.0232', '2021-08-17 13:11:54'),
(20486, 400, '7.9465', '1.0232', '2021-08-17 13:12:15'),
(20487, 400, '7.9465', '1.0232', '2021-08-17 13:12:15'),
(20488, 400, '7.9465', '1.0232', '2021-08-17 13:12:38'),
(20489, 400, '7.9465', '1.0232', '2021-08-17 13:12:38'),
(26025, 391, '23.7715732', '86.757904', '2021-09-27 05:27:34'),
(26046, 391, '23.7715737', '86.7579043', '2021-09-30 06:00:25'),
(26047, 391, '23.7715767', '86.7579037', '2021-09-30 06:00:27'),
(26090, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:38:53'),
(26091, 391, '23.7715743', '86.7579046', '2021-10-01 05:38:53'),
(26092, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:38:55'),
(26093, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:39:28'),
(26094, 391, '23.7715732', '86.757904', '2021-10-01 05:39:42'),
(26095, 391, '23.7715732', '86.757904', '2021-10-01 05:39:42'),
(26096, 391, '23.7715732', '86.757904', '2021-10-01 05:39:42'),
(26097, 391, '23.7715761', '86.7579041', '2021-10-01 05:40:46'),
(26098, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:40:48'),
(26099, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:40:49'),
(26100, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:41:14'),
(26101, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:41:32'),
(26102, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:41:34'),
(26103, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:42:35'),
(26104, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:42:39'),
(26105, 391, '23.7715758', '86.7579041', '2021-10-01 05:42:39'),
(26106, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:42:54'),
(26107, 391, '23.7715756', '86.7579043', '2021-10-01 05:43:06'),
(26108, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:43:07'),
(26109, 391, '23.771556666667', '86.757798333333', '2021-10-01 05:43:08'),
(27413, 391, '23.7715729', '86.7579035', '2021-10-20 13:04:01'),
(27414, 391, '23.772725', '86.756918333333', '2021-10-20 13:04:01'),
(380559, 319, '23.0225', '72.5714', '2022-12-22 10:46:19'),
(380562, 319, '37.4217937', '-122.083922', '2023-01-30 08:15:36'),
(380563, 319, '37.4217937', '-122.083922', '2023-01-30 08:15:36'),
(380564, 319, '37.4217937', '-122.083922', '2023-01-30 08:15:40'),
(380565, 319, '37.4217937', '-122.083922', '2023-01-30 08:17:34'),
(380566, 319, '37.4217937', '-122.083922', '2023-01-30 08:17:39'),
(380567, 319, '37.4217937', '-122.083922', '2023-01-30 08:19:58'),
(380568, 319, '37.4217937', '-122.083922', '2023-01-30 08:19:58'),
(380569, 319, '37.4217937', '-122.083922', '2023-01-30 08:20:24'),
(380570, 319, '37.4217937', '-122.083922', '2023-01-30 08:21:16'),
(380571, 319, '37.4217937', '-122.083922', '2023-01-30 08:21:16'),
(380572, 319, '37.4217937', '-122.083922', '2023-01-30 08:24:19'),
(380573, 319, '37.4217937', '-122.083922', '2023-01-30 08:24:33'),
(380574, 319, '37.4217937', '-122.083922', '2023-01-30 08:24:41'),
(380575, 319, '37.4217937', '-122.083922', '2023-01-30 08:24:43'),
(380576, 319, '37.4217937', '-122.083922', '2023-01-30 08:24:53'),
(380577, 319, '37.4217937', '-122.083922', '2023-01-30 08:25:00'),
(380578, 319, '37.4217937', '-122.083922', '2023-01-30 08:25:07');

-- --------------------------------------------------------

--
-- Table structure for table `email_template`
--

CREATE TABLE `email_template` (
  `entity_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `email_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `subject` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `email_template`
--

INSERT INTO `email_template` (`entity_id`, `title`, `email_slug`, `subject`, `message`, `content_id`, `language_slug`, `status`) VALUES
(11, 'Forgot Password', 'forgot-password', 'Password Assistance', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">We received a request to reset the password associated with this email address. Click #forgotlink# to reset your password using our secure server.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 1, 'en', 1),
(14, 'Promotional Email', 'promotional-email', 'Promotional Email', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { background-color: #f7f0ee; padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; background: #f7f0ee; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } p { padding: 0 !important; margin: 0 !important } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px;font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><img alt=\"logo\" class=\"logo-default\" src=\"#img_url#\" style=\"font-size: 0pt; background-color: rgb(247, 240, 238); font-family: Arial, sans-serif, Roboto; text-align: justify;\" /></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td><!-- SECTION 1 -->\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"spacer\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td class=\"spacer\" height=\"10\" style=\"font-size:0pt; line-height:0pt; text-align:center; width:100%; min-width:100%\">&nbsp;</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n\r\n									<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"padding: 20px; text-align: center;\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td><b style=\"font-size: 30px\">Happy Easter 25% off</b></td>\r\n											</tr>\r\n											<tr style=\"height: 10px\">\r\n											</tr>\r\n											<tr>\r\n												<td>\r\n												<p style=\"font-size: 16px;\">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sit amet, consectetur adipisicing sed do eiusmod, Lorem ipsum dolor elit, sed do eiusmod</p>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n\r\n									<table align=\"center\" bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"padding: 20px; text-align: center;\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td>\r\n												<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\" text-align: center; width: 305px;\">\r\n													<tbody>\r\n														<tr>\r\n															<td><img alt=\"item-image\" src=\"https://www.hausdesdoeners.com//uploads/menu/Fried-Rice-en1660041299.jpg\" style=\"width: 100%;\" /></td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n												<td>\r\n												<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\" text-align: center; width: 305px; padding: 0 10px 0 10px;\">\r\n													<tbody>\r\n														<tr>\r\n															<td><b style=\"font-size: 20px;\">Italian Pizza</b>\r\n															<p>Ut enim ad minim veniam,quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodoconsequat. Duis aute irure dolor in reprehenderit in voluptate</p>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n											<tr style=\"height: 10px\">\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n\r\n									<table align=\"center\" bgcolor=\"#fff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"text-align: center;\" width=\"100%\">\r\n										<tbody>\r\n											<tr style=\"height: 50px\">\r\n											</tr>\r\n											<tr>\r\n												<td style=\"font-size: 24px; color: #000; font-weight: 500\">TASTY OFFERS IN THIS MONTHS</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n\r\n									<table align=\"center\" bgcolor=\"#fff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"padding: 20px; text-align: center;\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td>\r\n												<table align=\"center\" bgcolor=\"#fff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width: 180px;\">\r\n													<tbody>\r\n														<tr>\r\n															<td><img src=\"https://www.hausdesdoeners.com/uploads/menu/promo_email.jpg\" style=\"width: 100%;\" /></td>\r\n														</tr>\r\n														<tr style=\"height: 10px\">\r\n														</tr>\r\n														<tr>\r\n															<td style=\"font-size: 18px; font-weight: 500;\">FRENCH PIZZA</td>\r\n														</tr>\r\n														<tr style=\"height: 5px\">\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n												<td>\r\n												<table align=\"center\" bgcolor=\"#fff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width: 180px;\">\r\n													<tbody>\r\n														<tr>\r\n															<td><img src=\"https://www.hausdesdoeners.com/uploads/menu/promo_email.jpg\" style=\"width: 100%;\" /></td>\r\n														</tr>\r\n														<tr style=\"height: 10px\">\r\n														</tr>\r\n														<tr>\r\n															<td style=\"font-size: 18px; font-weight: 500;\">ITALIAN PIZZA</td>\r\n														</tr>\r\n														<tr style=\"height: 5px\">\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n												<td>\r\n												<table align=\"center\" bgcolor=\"#fff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width: 180px;\">\r\n													<tbody>\r\n														<tr>\r\n															<td><img src=\"https://www.hausdesdoeners.com/uploads/menu/promo_email.jpg\" style=\"width: 100%;\" /></td>\r\n														</tr>\r\n														<tr style=\"height: 10px\">\r\n														</tr>\r\n														<tr>\r\n															<td style=\"font-size: 18px; font-weight: 500;\">BRAZILIAN PIZZA</td>\r\n														</tr>\r\n														<tr style=\"height: 5px\">\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n\r\n									<table align=\"center\" bgcolor=\"#fff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"text-align: center;\" width=\"100%\">\r\n										<tbody>\r\n											<tr style=\"height: 50px\">\r\n											</tr>\r\n											<tr>\r\n												<td style=\"font-size: 24px; color: #000; font-weight: 500\">GET IN TOUCH</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n\r\n									<table align=\"center\" bgcolor=\"#fff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"text-align: center;padding: 20px 50px 20px 50px\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td>\r\n												<table align=\"center\" bgcolor=\"#e5e5e5\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"padding: 20px;\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td><a href=\"#\"><img src=\"https://www.hausdesdoeners.com/assets/admin/img/fb-icon.png\" /></a></td>\r\n															<td><a href=\"#\"><img src=\"https://www.hausdesdoeners.com/assets/admin/img/google-plus-icon.png\" /></a></td>\r\n															<td><a href=\"#\"><img src=\"https://www.hausdesdoeners.com/assets/admin/img/instagram-icon.png\" /></a></td>\r\n															<td><a href=\"#\"><img src=\"https://www.hausdesdoeners.com/assets/admin/img/twitter-icon.png\" style=\"width: 32px; height: 32px;\" /></a></td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									<!-- SECTION 1 END--></td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">&nbsp;\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td class=\"section\" style=\"padding:40px 25px 40px 25px\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<th class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\" width=\"260\">\r\n															<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n																<tbody>\r\n																	<tr>\r\n																		<td class=\"img\" style=\"font-size:0pt; line-height:0pt; text-align:left\">\r\n																		<div class=\"img-m-center\" style=\"font-size:0pt; line-height:0pt; text-align: center;\"><img alt=\"logo\" class=\"logo-default\" src=\"#img_url#\" style=\"font-size: 0pt; text-align: left; background-color: rgb(247, 240, 238); font-family: Arial, sans-serif, Roboto;\" /></div>\r\n																		</td>\r\n																	</tr>\r\n																</tbody>\r\n															</table>\r\n															</th>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#00000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 3, 'en', 1),
(17, 'User Added', 'user-added', 'Restaurant App: New User Added', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px;  font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Dear #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">You have been added to our Haus des Döners App</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Your Credentials are as follows :</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">UserName: #email#</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Password: #password#</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 26, 'en', 1),
(18, 'New Restaurant Alert', 'new-restaurant-alert', 'New Restaurant Alert', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Dear #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">New Restaurant #restaurant#, has been added. Please review.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 27, 'en', 1),
(19, 'Restaurant Details Update Alert', 'restaurant-details-update-alert', 'Restaurant Details Update Alert', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Dear #firstname# ,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">#restaurant# restaurant&#39;s details has been updated. Please review.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 28, 'en', 1),
(20, 'Email Update Alert', 'email-update-alert', 'Email Update Alert', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Dear #firstname# ,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Your email has been updated from #s_email# to #email#. Please review.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 30, 'en', 1),
(21, 'Change status Alert', 'change-status-alert', 'Change status Alert', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Dear #firstname# ,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Your account has been #status# by Haus des Döners admin.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 31, 'en', 1),
(22, 'Verify Account', 'verify-account', 'Verify Account Assistance', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">#your_otp# is your one time password for account verification in Haus des Döners.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 95, 'en', 1);
INSERT INTO `email_template` (`entity_id`, `title`, `email_slug`, `subject`, `message`, `content_id`, `language_slug`, `status`) VALUES
(25, 'Contact Us', 'contact-us', 'Contact Us', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Thank you for contacting us!&nbsp; One of our team member will contact you soon!</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 205, 'en', 1),
(36, 'Contact Us for Admin', 'contact-us-for-admin', 'Contact Us', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi Admin,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Name :&nbsp;<span style=\"color: rgb(52, 52, 52); font-family: Arial, sans-serif, Roboto; text-align: start;\">#firstname#<br />\r\n									Email :&nbsp;#email#<br />\r\n									Message : </span>#message#</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 914, 'en', 1),
(39, 'Mot de passe oublié', 'forgot-password', 'Password Assistance', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Salut #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Nous avons re&ccedil;u une demande de r&eacute;initialisation du mot de passe associ&eacute; &agrave; cette adresse e-mail. Cliquez sur #forgotlink# pour r&eacute;initialiser votre mot de passe &agrave; l&#39;aide de notre serveur s&eacute;curis&eacute;.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Meilleures salutations,<br />\r\n									&Eacute;quipe Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">droits d&#39;auteur &copy; <span style=\"color: rgb(255, 255, 255); font-size: 11px; text-align: center; background-color: rgb(0, 0, 0);\">#copy_years#&nbsp;</span> TOUS LES DROITS SONT R&Eacute;SERV&Eacute;S Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 1, 'fr', 1),
(40, 'هل نسيت كلمة السر', 'forgot-password', 'Password Assistance', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\" dir=\"rtl\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">مهلا #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">لقد تلقينا طلبًا لإعادة تعيين كلمة المرور المرتبطة بعنوان البريد الإلكتروني هذا. انقر فوق&nbsp; #forgotlink# لإعادة تعيين كلمة المرور الخاصة بك باستخدام خادمنا الآمن.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">مع أطيب التحيات،<br />\r\n									أكل الفريق</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 1, 'ar', 1),
(43, 'Account Verified', 'account-verified', 'Account Verified', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Dear #firstname# ,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Your account has been verified by Haus des Döners admin.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 1733, 'en', 1),
(44, 'Event Booking Reminder', 'event-booking-reminder', 'Event Booking Reminder', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hello #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">You have an event booking today at #time# in #Restaurant_name# Restaurant which is located at #Address# for #no_of_peoples# peoples.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Thank You,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 1739, 'en', 1),
(45, 'Guest Order Confirmation', 'guest-order-confirmation', 'Order Confirmation', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Thanks for Ordering with us.&nbsp;<br />\r\n									Your order has been placed successfully from &quot;#restaurant#&quot; with order id #order_id# and a Order Amount of #order_total# .<br />\r\n									Please find attached order invoice below.&nbsp;<span style=\"color: rgb(52, 52, 52); font-family: Arial, sans-serif, Roboto; text-align: start;\">#track_order#</span></p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 1740, 'en', 1),
(46, 'Table Booking Reminder', 'table-booking-reminder', 'Table Booking Reminder', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hello #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">You have an table booking today at #time# in #Restaurant_name# Restaurant which is located at #Address# for #no_of_peoples# peoples.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Thank You,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 1754, 'en', 1),
(47, 'Forgot Password OTP', 'forgot-password-otp', 'One Time Password', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 150px; height: 81px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">#your_otp# is your one time password to reset your password in Haus des Döners.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team <span style=\"color: rgb(52, 52, 52); font-family: Arial, sans-serif, Roboto; text-align: start;\">Haus des Döners</span></p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 2016, 'en', 1),
(48, 'Order Receive Alert', 'order-receive-alert', 'Order Receive Alert', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px;  font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hello Admin,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Congratulations! A new order is received please check the order details</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 2085, 'en', 1);
INSERT INTO `email_template` (`entity_id`, `title`, `email_slug`, `subject`, `message`, `content_id`, `language_slug`, `status`) VALUES
(50, 'Mot de passe oublié OTP', 'forgot-password-otp', 'Mot de passe à usage unique', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">#your_otp# is your one time password to reset your password in Haus des Döners.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 2016, 'fr', 1),
(51, 'نسيت كلمة المرور OTP', 'forgot-password-otp', 'كلمة السر لمرة واحدة', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px;  font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">#your_otp# is your one time password to reset your password in Haus des Döners.</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT &copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 2016, 'ar', 1),
(52, 'Order Cancelled', 'order-cancelled', 'Order Cancelled', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#base_url_email_temp#assets/admin/img/logo.png\" style=\"border-width: 0px; border-style: solid; width: 150px; height: 81px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\"><span style=\"font-family: Arial, sans-serif, Roboto; color: rgb(52, 52, 52); text-align: start;\">#cancelled_order_text#</span></p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team Haus des Döners</p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT&copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 2289, 'en', 1),
(53, 'Order Updated', 'order-updated', 'Order Updated', '<style media=\"screen\" type=\"text/css\">[style*=\"Roboto\"] { font-family: \'Roboto\', Arial, sans-serif !important } [style*=\"Oswald\"] { font-family: \'Oswald\', Arial, sans-serif !important } /* Linked Styles */ .body { padding: 0 !important; margin: 0 !important; display: block !important; min-width: 100% !important; width: 100% !important; -webkit-text-size-adjust: none; font-family: Arial, sans-serif, \'Roboto\'; } a { color: #ef5751; text-decoration: none } img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ } .text a { color: #ef5751 !important; text-decoration: underline !important; }\r\n</style>\r\n<div class=\"body\">\r\n<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n	<tbody>\r\n		<tr>\r\n			<td align=\"center\" valign=\"top\">\r\n			<table bgcolor=\"#ffffff\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td class=\"header\" style=\"width:650px; font-size:0pt; line-height:0pt; padding:30px 10px 30px 10px; margin:0; font-weight:normal; Margin:0\">\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<td align=\"center\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding-bottom: 0px;\">\r\n															<div class=\"img-center\" style=\"font-size:0pt; line-height:0pt; text-align:center\"><a href=\"#\" target=\"_blank\"><img alt=\"logo\" src=\"#img_url#\" style=\"border-width: 0px; border-style: solid; width: 169px; height: 58px;\" /> </a></div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</td>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td style=\"border: 1px solid #000000; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;\">\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Hi #firstname#,</p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\"><span style=\"font-family: Arial, sans-serif, Roboto; color: rgb(52, 52, 52); text-align: start;\">Your order ##order_id# has been updated by #updated_by#. #order_refund_text#</span></p>\r\n\r\n									<p style=\"font-size:14px; color:#343434; margin-bottom:10px;\">Best Regards,<br />\r\n									Team <span style=\"color: rgb(52, 52, 52); font-family: Arial, sans-serif, Roboto; text-align: start;\">Haus des Döners</span></p>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n\r\n			<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n				<tbody>\r\n					<tr>\r\n						<td align=\"center\">\r\n						<table bgcolor=\"#000000\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"mobile-shell\" width=\"650\">\r\n							<tbody>\r\n								<tr>\r\n									<td>\r\n									<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n										<tbody>\r\n											<tr>\r\n												<th bgcolor=\"#000000\" class=\"column\" style=\"font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; Margin:0\">\r\n												<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\r\n													<tbody>\r\n														<tr>\r\n															<td style=\"padding: 15px 25px 15px 25px;\">\r\n															<div class=\"text-footer-l\" style=\"color:#FFFFFF; font-family:Arial,sans-serif, \'Roboto\'; font-size:11px; line-height:16px; text-align:center;\">COPYRIGHT&copy; #copy_years# ALL RIGHTS RESERVED. Haus des Döners</div>\r\n															</td>\r\n														</tr>\r\n													</tbody>\r\n												</table>\r\n												</th>\r\n											</tr>\r\n										</tbody>\r\n									</table>\r\n									</td>\r\n								</tr>\r\n							</tbody>\r\n						</table>\r\n						</td>\r\n					</tr>\r\n				</tbody>\r\n			</table>\r\n			</td>\r\n		</tr>\r\n	</tbody>\r\n</table>\r\n</div>', 2290, 'en', 1);

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `entity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL COMMENT 'restaurant content id',
  `package_id` int(11) DEFAULT NULL COMMENT 'package content id',
  `name` varchar(255) DEFAULT NULL,
  `no_of_people` varchar(50) DEFAULT NULL,
  `booking_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `tax_rate` decimal(10,2) DEFAULT NULL,
  `tax_type` enum('Percentage','Amount') DEFAULT NULL,
  `coupon_amount` decimal(10,2) DEFAULT NULL,
  `coupon_type` enum('Percentage','Amount') DEFAULT NULL,
  `event_status` enum('pending','onGoing','completed','cancel','paid') DEFAULT NULL,
  `cancel_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `additional_request` varchar(255) DEFAULT NULL COMMENT 'comment box',
  `status` tinyint(4) NOT NULL,
  `invoice` varchar(255) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`entity_id`, `user_id`, `restaurant_id`, `package_id`, `name`, `no_of_people`, `booking_date`, `end_date`, `amount`, `subtotal`, `tax_rate`, `tax_type`, `coupon_amount`, `coupon_type`, `event_status`, `cancel_reason`, `additional_request`, `status`, `invoice`, `updated_by`, `updated_date`, `created_by`, `created_date`) VALUES
(2, 1103, 1534, NULL, NULL, '15', '2022-08-17 13:22:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, 1, NULL, NULL, NULL, 1103, '2022-08-12 13:23:46'),
(3, 1099, 1799, NULL, NULL, '5', '2022-08-26 10:55:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, 1, NULL, NULL, NULL, 1099, '2022-08-26 10:47:08'),
(4, 1102, 1799, NULL, NULL, '5', '2022-08-28 16:23:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, 1, NULL, NULL, NULL, 1102, '2022-08-27 16:23:56'),
(5, 1102, 1799, NULL, NULL, '5', '2022-08-30 16:24:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, 1, NULL, NULL, NULL, 1102, '2022-08-27 16:24:25'),
(6, 1101, 1534, NULL, NULL, '15', '2022-09-07 06:53:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, 1, NULL, NULL, NULL, 1101, '2022-09-01 06:53:33'),
(7, 1099, 1799, 1310, NULL, '5', '2022-09-17 23:04:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, 1, NULL, NULL, NULL, 1099, '2022-09-17 23:04:38'),
(11, 1102, 589, 0, 'Fabian Leon', '100', '2022-10-19 10:10:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, 1, NULL, NULL, NULL, 1102, '2022-10-19 09:55:46'),
(13, 1099, 1996, 1124, NULL, '5', '2022-10-30 06:30:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'paid', NULL, 'Test', 1, NULL, NULL, NULL, 1099, '2022-10-23 06:31:48'),
(16, 1170, 1534, NULL, NULL, '16', '2022-11-25 12:54:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, NULL, 1, NULL, NULL, NULL, 1170, '2022-11-25 12:52:21');

-- --------------------------------------------------------

--
-- Table structure for table `event_detail`
--

CREATE TABLE `event_detail` (
  `entity_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `restaurant_detail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `user_detail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `package_detail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `event_detail`
--

INSERT INTO `event_detail` (`entity_id`, `event_id`, `restaurant_detail`, `user_detail`, `package_detail`) VALUES
(2, 2, 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"69\";s:10:\"content_id\";s:4:\"1534\";s:4:\"name\";s:19:\"Parallax restaurant\";s:5:\"image\";s:47:\"restaurant/Parallax-restaurant-en1660020710.jpg\";s:12:\"phone_number\";s:10:\"7412741274\";s:10:\"phone_code\";s:2:\"91\";s:5:\"email\";s:19:\"parrr12@yopmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"12.55\";s:7:\"address\";s:51:\"PLANTA Queen, 15 W 27th St, New York, NY 10001, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:15:\"New York County\";s:8:\"latitude\";s:10:\"23.7727471\";s:9:\"longitude\";s:17:\"86.73220719999999\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"13.66\";s:16:\"service_fee_type\";s:10:\"Percentage\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{s:10:\"first_name\";s:4:\"Emma\";s:9:\"last_name\";s:1:\"E\";}', ''),
(3, 3, 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"97\";s:10:\"content_id\";s:4:\"1799\";s:4:\"name\";s:11:\"Autoservire\";s:5:\"image\";s:39:\"restaurant/Autoservire-en1660021252.jpg\";s:12:\"phone_number\";s:10:\"2245430221\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:19:\"autoser12@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"20.00\";s:7:\"address\";s:42:\"164-17 Union Tpke, Flushing, NY 11366, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:10:\"40.7219729\";s:9:\"longitude\";s:11:\"-73.8037212\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"30.00\";s:16:\"service_fee_type\";s:6:\"Amount\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{s:10:\"first_name\";s:7:\"Haus des Döners\";s:9:\"last_name\";s:8:\"Customer\";}', ''),
(4, 4, 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"97\";s:10:\"content_id\";s:4:\"1799\";s:4:\"name\";s:11:\"Autoservire\";s:5:\"image\";s:39:\"restaurant/Autoservire-en1660021252.jpg\";s:12:\"phone_number\";s:10:\"2245430221\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:19:\"autoser12@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"20.00\";s:7:\"address\";s:42:\"164-17 Union Tpke, Flushing, NY 11366, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:10:\"40.7219729\";s:9:\"longitude\";s:11:\"-73.8037212\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"30.00\";s:16:\"service_fee_type\";s:6:\"Amount\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{s:10:\"first_name\";s:6:\"Fabian\";s:9:\"last_name\";s:4:\"Leon\";}', ''),
(5, 5, 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"97\";s:10:\"content_id\";s:4:\"1799\";s:4:\"name\";s:11:\"Autoservire\";s:5:\"image\";s:39:\"restaurant/Autoservire-en1660021252.jpg\";s:12:\"phone_number\";s:10:\"2245430221\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:19:\"autoser12@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"20.00\";s:7:\"address\";s:42:\"164-17 Union Tpke, Flushing, NY 11366, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:10:\"40.7219729\";s:9:\"longitude\";s:11:\"-73.8037212\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"30.00\";s:16:\"service_fee_type\";s:6:\"Amount\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{s:10:\"first_name\";s:6:\"Fabian\";s:9:\"last_name\";s:4:\"Leon\";}', ''),
(6, 6, 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"69\";s:10:\"content_id\";s:4:\"1534\";s:4:\"name\";s:19:\"Parallax restaurant\";s:5:\"image\";s:47:\"restaurant/Parallax-restaurant-en1660020710.jpg\";s:12:\"phone_number\";s:10:\"7412741274\";s:10:\"phone_code\";s:2:\"91\";s:5:\"email\";s:19:\"parrr12@yopmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"12.55\";s:7:\"address\";s:51:\"PLANTA Queen, 15 W 27th St, New York, NY 10001, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:15:\"New York County\";s:8:\"latitude\";s:10:\"23.7727471\";s:9:\"longitude\";s:17:\"86.73220719999999\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"13.66\";s:16:\"service_fee_type\";s:10:\"Percentage\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{s:10:\"first_name\";s:6:\"Sophia\";s:9:\"last_name\";s:4:\"Emma\";}', ''),
(7, 7, 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"97\";s:10:\"content_id\";s:4:\"1799\";s:4:\"name\";s:11:\"Autoservire\";s:5:\"image\";s:39:\"restaurant/Autoservire-en1660021252.jpg\";s:12:\"phone_number\";s:10:\"2245430221\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:19:\"autoser12@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"20.00\";s:7:\"address\";s:42:\"164-17 Union Tpke, Flushing, NY 11366, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:10:\"40.7219729\";s:9:\"longitude\";s:11:\"-73.8037212\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"30.00\";s:16:\"service_fee_type\";s:6:\"Amount\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{s:10:\"first_name\";s:7:\"Haus des Döners\";s:9:\"last_name\";s:8:\"Customer\";}', 'a:3:{s:13:\"package_price\";s:7:\"2600.00\";s:12:\"package_name\";s:15:\"Classic Package\";s:14:\"package_detail\";s:403:\"<p><span style=\"color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 16px; text-align: left;\">Below are the restaurants participating in the CFCP program. Show your support by ordering from your local restaurants and sending a meal to a family in need. If you are a restaurant offering a Comfort Food Care Package, fill out this form to add your restaurant to our growing list.</span></p>\";}'),
(11, 11, 'O:8:\"stdClass\":14:{s:4:\"name\";s:14:\"Spice Symphony\";s:5:\"image\";s:42:\"restaurant/Spice-Symphony-en1660021045.jpg\";s:12:\"phone_number\";s:10:\"2126126607\";s:5:\"email\";s:17:\"spice23@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"18.37\";s:7:\"address\";s:53:\"110 Wall Street, 110 Wall St, New York, NY 10005, USA\";s:8:\"landmark\";N;s:7:\"zipcode\";s:6:\"905350\";s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:17:\"40.70491229999999\";s:9:\"longitude\";s:11:\"-74.0064032\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";}', 'a:2:{s:10:\"first_name\";s:6:\"Fabian\";s:9:\"last_name\";s:4:\"Leon\";}', ''),
(13, 13, 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:3:\"129\";s:10:\"content_id\";s:4:\"1996\";s:4:\"name\";s:18:\"Pizza Eforie North\";s:5:\"image\";s:27:\"restaurant/Starbelly-en.jpg\";s:12:\"phone_number\";s:10:\"2096096099\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:19:\"pizzaat12@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"12.00\";s:7:\"address\";s:44:\"1322 E Gun Hill Rd, The Bronx, NY 10469, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:10:\"40.8709741\";s:9:\"longitude\";s:11:\"-73.8476496\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"10.00\";s:16:\"service_fee_type\";s:6:\"Amount\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{s:10:\"first_name\";s:7:\"Haus des Döners\";s:9:\"last_name\";s:8:\"Customer\";}', 'a:3:{s:13:\"package_price\";s:7:\"2000.00\";s:12:\"package_name\";s:20:\"Premium Food Package\";s:14:\"package_detail\";s:191:\"<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, sans-serif; font-size: 13px; text-align: start; background-color: rgb(245, 245, 247);\">Premium Food Package</span></p>\";}'),
(16, 16, 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"69\";s:10:\"content_id\";s:4:\"1534\";s:4:\"name\";s:10:\"Las Palmas\";s:5:\"image\";s:47:\"restaurant/Parallax-restaurant-en1660020710.jpg\";s:12:\"phone_number\";s:10:\"7412741274\";s:10:\"phone_code\";s:2:\"91\";s:5:\"email\";s:19:\"parrr12@yopmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"12.55\";s:7:\"address\";s:51:\"PLANTA Queen, 15 W 27th St, New York, NY 10001, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:15:\"New York County\";s:8:\"latitude\";s:10:\"23.7727471\";s:9:\"longitude\";s:17:\"86.73220719999999\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"13.66\";s:16:\"service_fee_type\";s:10:\"Percentage\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{s:10:\"first_name\";s:6:\"Sophia\";s:9:\"last_name\";s:4:\"John\";}', '');

-- --------------------------------------------------------

--
-- Table structure for table `event_notification`
--

CREATE TABLE `event_notification` (
  `notification_id` int(11) NOT NULL,
  `last_event_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `event_count` int(11) NOT NULL,
  `view_status` tinyint(4) NOT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `event_notification`
--

INSERT INTO `event_notification` (`notification_id`, `last_event_id`, `admin_id`, `event_count`, `view_status`, `date`) VALUES
(1, 19, 1, 0, 1, '2022-12-23'),
(2, 17, 1095, 0, 1, '2022-12-06');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `entity_id` int(11) NOT NULL,
  `faq_category_id` int(11) DEFAULT NULL,
  `question` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `answer` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content_id` int(11) NOT NULL,
  `language_slug` varchar(5) NOT NULL,
  `status` tinyint(4) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`entity_id`, `faq_category_id`, `question`, `answer`, `content_id`, `language_slug`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 1, 'How to reach customer care ?', 'You can reach our customer care number +91 9999999999 for any of your queries (Available 10 AM to 9 PM except Saturdays and Sundays)\r\nYou can also email us your issue on support@hausdesdoeners.com\r\n\r\nNote: We value your privacy and your information is safe with us. Please do not reveal any personal information, bank account number, OTP etc. to another person. A Eatanace representative will never ask you for these details. Please do not reveal these details to fraudsters and imposters claiming to be calling on our behalf. Be vigilant and do not entertain phishing calls or emails.', 1725, 'en', 1, 1, '2021-09-01 13:15:17', 1, '2021-09-02 10:20:42'),
(2, 2, 'Qu\'est-ce que le Lorem Ipsum ?', 'Lorem Ipsum est simplement un texte factice de l\'industrie de l\'impression et de la composition. Lorem Ipsum est le texte factice standard de l\'industrie depuis les années 1500, lorsqu\'un imprimeur inconnu a pris une galère de caractères et l\'a brouillé pour en faire un livre spécimen de caractères. Il a survécu non seulement à cinq siècles, mais aussi au saut dans la composition électronique, restant essentiellement inchangé. Il a été popularisé dans les années 1960 avec la sortie de feuilles Letraset contenant des passages de Lorem Ipsum, et plus récemment avec des logiciels de PAO comme Aldus PageMaker incluant des versions de Lorem Ipsum.', 1725, 'fr', 1, 1, '2021-09-01 13:16:07', NULL, NULL),
(3, 3, 'ما هو لوريم إيبسوم؟', 'لوريم إيبسوم هو ببساطة نص شكلي يستخدم في صناعة الطباعة والتنضيد. كان Lorem Ipsum هو النص الوهمي القياسي في الصناعة منذ القرن الخامس عشر الميلادي ، عندما أخذت طابعة غير معروفة لوحًا من النوع وتدافعت عليه لعمل كتاب عينة. لقد صمد ليس فقط لخمسة قرون ، ولكن أيضًا القفزة في التنضيد الإلكتروني ، وظل دون تغيير جوهري. تم نشره في الستينيات من القرن الماضي مع إصدار أوراق Letraset التي تحتوي على مقاطع Lorem Ipsum ، ومؤخرًا مع برامج النشر المكتبي مثل Aldus PageMaker بما في ذلك إصدارات Lorem Ipsum.', 1725, 'ar', 1, 1, '2021-09-01 13:16:55', NULL, NULL),
(4, 7, 'Why do we use it?', 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).', 1726, 'en', 1, 1, '2021-09-01 13:19:08', NULL, '2021-09-06 10:33:41'),
(5, 8, 'Pourquoi l\'utilisons-nous?', 'C\'est un fait établi de longue date qu\'un lecteur sera distrait par le contenu lisible d\'une page en regardant sa mise en page. L\'intérêt d\'utiliser Lorem Ipsum est qu\'il a une distribution de lettres plus ou moins normale, par opposition à l\'utilisation de \'Content here, content here\', ce qui le fait ressembler à un anglais lisible. De nombreux logiciels de publication assistée par ordinateur et éditeurs de pages Web utilisent désormais Lorem Ipsum comme texte de modèle par défaut, et une recherche de « lorem ipsum » permettra de découvrir de nombreux sites Web encore à leurs balbutiements. Différentes versions ont évolué au fil des années, parfois par accident, parfois volontairement (humour injecté, etc.).', 1726, 'fr', 1, 1, '2021-09-01 13:19:53', 1, '2021-09-06 10:33:41'),
(6, 9, 'حيث أنها لا تأتي من؟', 'خلافًا للاعتقاد الشائع ، فإن Lorem Ipsum ليس مجرد نص عشوائي. لها جذور في قطعة من الأدب اللاتيني الكلاسيكي من 45 قبل الميلاد ، مما يجعلها أكثر من 2000 عام. قام ريتشارد مكلينتوك ، أستاذ اللغة اللاتينية في كلية هامبدن سيدني في فيرجينيا ، بالبحث عن واحدة من أكثر الكلمات اللاتينية غموضًا ، consectetur ، من مقطع لوريم إيبسوم ، وتصفح اقتباسات الكلمة في الأدب الكلاسيكي ، اكتشف المصدر الذي لا شك فيه. يأتي Lorem Ipsum من الأقسام 1.10.32 و 1.10.33 من \"de Finibus Bonorum et Malorum\" (أقصى الخير والشر) بقلم شيشرون ، الذي كتبه عام 45 قبل الميلاد. هذا الكتاب عبارة عن أطروحة حول نظرية الأخلاق ، وقد حظيت بشعبية كبيرة خلال عصر النهضة. السطر الأول من Lorem Ipsum ، \"Lorem ipsum dolor sit amet ..\" ، يأتي من سطر في القسم 1.10.32.', 1727, 'ar', 1, 1, '2021-09-01 13:21:36', NULL, NULL),
(7, 8, 'D\'où est ce que ça vient?', 'Contrairement à la croyance populaire, Lorem Ipsum n\'est pas simplement un texte aléatoire. Il a ses racines dans un morceau de littérature latine classique de 45 avant JC, ce qui en fait plus de 2000 ans. Richard McClintock, professeur de latin au Hampden-Sydney College en Virginie, a recherché l\'un des mots latins les plus obscurs, consectetur, dans un passage de Lorem Ipsum, et en parcourant les citations du mot dans la littérature classique, a découvert la source incontestable. Lorem Ipsum provient des sections 1.10.32 et 1.10.33 de \"de Finibus Bonorum et Malorum\" (Les Extrêmes du Bien et du Mal) de Cicéron, écrit en 45 av. Ce livre est un traité sur la théorie de l\'éthique, très populaire à la Renaissance. La première ligne de Lorem Ipsum, « Lorem ipsum dolor sit amet.. », provient d\'une ligne de la section 1.10.32.', 1727, 'fr', 1, 1, '2021-09-01 13:22:39', NULL, NULL),
(10, 2, 'Where can I get some?', 'Il existe de nombreuses variantes de passages de Lorem Ipsum disponibles, mais la majorité ont subi une altération sous une forme ou une autre, par l\'humour injecté ou des mots aléatoires qui ne semblent même pas légèrement crédibles. Si vous allez utiliser un passage de Lorem Ipsum, vous devez vous assurer qu\'il n\'y a rien d\'embarrassant caché au milieu du texte. Tous les générateurs Lorem Ipsum sur Internet ont tendance à répéter des morceaux prédéfinis si nécessaire, ce qui en fait le premier véritable générateur sur Internet. Il utilise un dictionnaire de plus de 200 mots latins, combiné à une poignée de structures de phrases modèles, pour générer Lorem Ipsum qui semble raisonnable. Le Lorem Ipsum généré est donc toujours exempt de répétition, d\'humour injecté, de mots non caractéristiques, etc.', 1731, 'fr', 1, 1, '2021-09-01 13:34:09', NULL, '2021-09-02 10:30:49'),
(11, 3, '، لكن الغالبية تعرضت للتغيير بشكل ما ، عن', 'هناك العديد من الأشكال المتاحة لنصوص لوريم إيبسوم ، لكن الغالبية تعرضت للتغيير بشكل ما ، عن طريق إدخال بعض الفكاهة أو الكلمات العشوائية التي لا تبدو قابلة للتصديق إلى حد ما. إذا كنت ستستخدم مقطعًا من لوريم إيبسوم ، فعليك التأكد من عدم وجود أي شيء محرج مخفي في منتصف النص. تميل جميع مولدات Lorem Ipsum على الإنترنت إلى تكرار الأجزاء المحددة مسبقًا حسب الضرورة ، مما يجعلها أول مولد حقيقي على الإنترنت. يستخدم قاموسًا يضم أكثر من 200 كلمة لاتينية ، جنبًا إلى جنب مع حفنة من تراكيب الجملة النموذجية ، لتوليد Lorem Ipsum الذي يبدو معقولًا. لذلك فإن لوريم إيبسوم الذي تم إنشاؤه يكون دائمًا خاليًا من التكرار أو الدعابة المحقونة أو الكلمات غير المميزة وما إلى ذلك.', 1732, 'ar', 1, 1, '2021-09-01 13:35:03', NULL, NULL),
(12, 1, 'How can I cancel my Order ?', 'If not accepted by restaurant, you can cancel your order within 90 secs of placing it in My Orders section.\r\n\r\nBeyond that we will do our best to accommodate your request if the order is not placed to the restaurant (Customer service number:  9999999999). Please note that we will have a right to charge a cancellation fee up to full order value to compensate our restaurant and delivery partners if your order has been confirmed.', 1731, 'en', 1, 1, '2021-09-02 10:30:39', 1, '2021-09-02 10:30:49');

-- --------------------------------------------------------

--
-- Table structure for table `faq_category`
--

CREATE TABLE `faq_category` (
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sequence` int(11) DEFAULT NULL,
  `content_id` int(11) NOT NULL,
  `language_slug` varchar(5) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `faq_category`
--

INSERT INTO `faq_category` (`entity_id`, `name`, `sequence`, `content_id`, `language_slug`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 'Most Asked', 1, 1722, 'en', 1, 1, '2021-09-01 13:10:24', 1, '2022-02-04 11:49:50'),
(2, 'Catégorie 1', 2, 1722, 'fr', 1, 1, '2021-09-01 13:10:47', NULL, '2022-02-04 11:49:50'),
(3, 'الفئة الأولى - test1', 3, 1722, 'ar', 1, 1, '2021-09-01 13:11:04', 1, '2022-02-04 11:49:50'),
(4, 'Food Ordering', 4, 1723, 'en', 1, 1, '2021-09-01 13:11:29', 1, '2021-09-02 10:12:06'),
(5, 'الفئة الثانية', 6, 1723, 'ar', 1, 1, '2021-09-01 13:11:44', NULL, NULL),
(6, 'Catégorie deux', 5, 1723, 'fr', 1, 1, '2021-09-01 13:12:04', NULL, NULL),
(7, 'Refund Policy', 7, 1724, 'en', 1, 1, '2021-09-01 13:13:21', 1, '2021-09-02 10:12:40'),
(8, 'Catégorie trois', 8, 1724, 'fr', 1, 1, '2021-09-01 13:13:42', NULL, NULL),
(9, 'الفئة الثالثة', 9, 1724, 'ar', 1, 1, '2021-09-01 13:13:58', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `food_type`
--

CREATE TABLE `food_type` (
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_veg` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-veg,0-non-veg',
  `food_type_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT ' 	Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `food_type`
--

INSERT INTO `food_type` (`entity_id`, `name`, `is_veg`, `food_type_image`, `content_id`, `language_slug`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`, `is_masterdata`) VALUES
(1, 'Non-Veg', 0, 'food_type/14e673837c8c098892207249b02b9652.jpg', 388, 'en', 1, 1, '2020-12-07 07:22:18', 1, '2023-01-23 07:10:43', '0'),
(10, 'Sea Food', 0, 'food_type/2253adcff011d388a5470bd31443cb94.jpg', 453, 'en', 1, 1, '2020-12-28 06:30:36', 1, '2023-01-23 07:11:07', '0'),
(39, 'Egg', 1, 'food_type/abdb589afcf5ab2957b918fa48788169.jpg', 546, 'en', 1, 1, '2021-03-23 13:09:06', 1, '2023-01-23 07:10:57', '0'),
(118, 'بيضة', 0, '', 546, 'ar', 1, 1, '2021-06-15 05:23:48', 1, '2021-06-17 14:43:38', '0'),
(119, 'مأكولات بحرية', 0, '', 453, 'ar', 1, 1, '2021-06-15 05:24:16', NULL, NULL, '0'),
(129, 'Fruit de mer', 0, '', 453, 'fr', 1, 1, '2021-06-15 15:09:43', 1, '2021-06-17 14:44:12', '0'),
(130, 'Œuf', 1, '', 546, 'fr', 1, 1, '2021-06-15 15:10:23', 1, '2021-06-17 14:43:17', '0'),
(140, 'الحبوب', 1, '', 388, 'ar', 1, 1, '2021-06-15 15:16:49', NULL, NULL, '0'),
(141, 'Non-végétarien', 0, '', 388, 'fr', 1, 1, '2021-06-17 09:11:22', NULL, NULL, '0'),
(152, 'Veg', 1, 'food_type/f307dad58701eacb4ef9318c1cfe52dc.jpg', 1250, 'en', 1, 1, '2021-06-21 10:29:45', 1, '2023-01-23 07:10:21', '0'),
(262, 'نباتي', 1, 'food_type/09cdc8b36823c46ae983e3fc212216b8.jpg', 1250, 'ar', 1, 1, '2022-08-09 05:40:24', 1, '2023-01-23 07:10:35', '0'),
(263, 'Légumes', 1, 'food_type/1cb2ae3ba833f29c3511cdc837bcf2da.jpg', 1250, 'fr', 1, 1, '2022-08-09 05:40:36', 1, '2023-01-23 07:10:29', '0');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int(11) NOT NULL,
  `language_name` varchar(100) NOT NULL,
  `language_slug` varchar(10) NOT NULL,
  `language_directory` varchar(100) NOT NULL,
  `language_code` varchar(20) DEFAULT NULL,
  `language_default` tinyint(1) DEFAULT 0,
  `active` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `language_name`, `language_slug`, `language_directory`, `language_code`, `language_default`, `active`) VALUES
(8, 'English', 'en', 'english', 'en_US', 1, 1),
(13, 'French', 'fr', 'french', 'fr_FR', 0, 1),
(14, 'Arabic', 'ar', 'arabic', 'ar_AR', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_addons_sequencemap`
--

CREATE TABLE `menu_addons_sequencemap` (
  `entity_id` int(11) NOT NULL,
  `restaurant_owner_id` int(11) NOT NULL,
  `restaurant_content_id` int(11) NOT NULL,
  `add_ons_content_id` int(11) NOT NULL COMMENT 'addons category content id',
  `sequence_no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `menu_addons_sequencemap`
--

INSERT INTO `menu_addons_sequencemap` (`entity_id`, `restaurant_owner_id`, `restaurant_content_id`, `add_ons_content_id`, `sequence_no`) VALUES
(3, 27, 1534, 1227, 1),
(4, 27, 1534, 1229, 2),
(5, 372, 1996, 1229, 1),
(6, 372, 1996, 1230, 2);

-- --------------------------------------------------------

--
-- Table structure for table `menu_category_sequencemap`
--

CREATE TABLE `menu_category_sequencemap` (
  `entity_id` int(11) NOT NULL,
  `restaurant_owner_id` int(11) NOT NULL,
  `restaurant_content_id` int(11) NOT NULL,
  `category_content_id` int(11) NOT NULL,
  `sequence_no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `menu_category_sequencemap`
--

INSERT INTO `menu_category_sequencemap` (`entity_id`, `restaurant_owner_id`, `restaurant_content_id`, `category_content_id`, `sequence_no`) VALUES
(1, 372, 1996, 1652, 1),
(2, 372, 1996, 1220, 2),
(3, 372, 1996, 1848, 3),
(7, 27, 1534, 1652, 1),
(8, 27, 1534, 1848, 2),
(9, 27, 1534, 1220, 3),
(10, 622, 1799, 1223, 1),
(11, 622, 1799, 1652, 2),
(12, 622, 1799, 573, 3),
(13, 622, 1799, 1848, 4);

-- --------------------------------------------------------

--
-- Table structure for table `menu_item_sequencemap`
--

CREATE TABLE `menu_item_sequencemap` (
  `entity_id` int(11) NOT NULL,
  `restaurant_owner_id` int(11) NOT NULL,
  `menu_content_id` int(11) NOT NULL,
  `sequence_no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `entity_id` int(11) NOT NULL,
  `notification_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notification_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`entity_id`, `notification_title`, `notification_description`, `content_id`, `language_slug`, `created_by`) VALUES
(87, 'Aut mollitia modi voluptas laboris quam ipsum ea error quis facilis', 'Rerum ut beatae quis\r\nAut mollitia modi voluptas laboris quam ipsum ea error quis facilis', NULL, NULL, 1),
(88, 'Brand New Offers', '1233', NULL, NULL, 1),
(89, 'Offer alert', 'Offer alert', NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications_users`
--

CREATE TABLE `notifications_users` (
  `map_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notifications_users`
--

INSERT INTO `notifications_users` (`map_id`, `notification_id`, `user_id`) VALUES
(459, 87, 1013),
(461, 88, 1037),
(462, 88, 1013),
(465, 89, 1108),
(466, 89, 1161),
(467, 89, 1154),
(468, 89, 1142),
(469, 89, 1120),
(470, 89, 1181),
(471, 89, 1139),
(472, 89, 1113),
(473, 89, 1133),
(474, 89, 1111),
(475, 89, 1116),
(476, 89, 1149),
(477, 89, 1164),
(478, 89, 1145),
(479, 89, 1118),
(480, 89, 1159),
(481, 89, 1179),
(482, 89, 1151),
(483, 89, 1178),
(484, 89, 1112),
(485, 89, 49),
(486, 89, 1135),
(487, 89, 1127),
(488, 89, 1099),
(489, 89, 1156),
(490, 89, 1103),
(491, 89, 1124),
(492, 89, 23),
(493, 89, 1117),
(494, 89, 1136),
(495, 89, 1150),
(496, 89, 1083),
(497, 89, 1152),
(498, 89, 1070),
(499, 89, 1155),
(500, 89, 1140),
(501, 89, 1106),
(502, 89, 314),
(503, 89, 1162),
(504, 89, 1128),
(505, 89, 1165),
(506, 89, 1172),
(507, 89, 1177),
(508, 89, 1158),
(509, 89, 1166),
(510, 89, 1119),
(511, 89, 1153),
(512, 89, 1114),
(513, 89, 1122),
(514, 89, 1134),
(515, 89, 1107),
(516, 89, 1146),
(517, 89, 1141),
(518, 89, 1129),
(519, 89, 1131),
(520, 89, 1132),
(521, 89, 1104),
(522, 89, 1147),
(523, 89, 1125),
(524, 89, 1126),
(525, 89, 1130),
(526, 89, 1105),
(527, 89, 1170),
(528, 89, 1163),
(529, 89, 1123),
(530, 89, 1121),
(531, 89, 1109),
(532, 89, 1110),
(533, 89, 1160),
(534, 89, 1167),
(535, 89, 1168),
(536, 89, 1169),
(537, 89, 1180),
(538, 89, 1115),
(539, 89, 1174),
(540, 89, 1143),
(541, 89, 1144),
(542, 89, 1157),
(543, 89, 364),
(544, 89, 1098),
(545, 89, 391),
(546, 89, 319),
(547, 89, 1148),
(548, 89, 1176),
(549, 89, 400),
(550, 89, 1175);

-- --------------------------------------------------------

--
-- Table structure for table `order_coupon_use`
--

CREATE TABLE `order_coupon_use` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `coupon_type` enum('Percentage','Amount') DEFAULT NULL,
  `coupon_amount` decimal(20,2) DEFAULT NULL,
  `coupon_discount` decimal(20,2) DEFAULT NULL,
  `coupon_name` varchar(200) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_detail`
--

CREATE TABLE `order_detail` (
  `entity_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `user_mobile_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `user_detail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `restaurant_detail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `item_detail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_updateorder` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_detail`
--

INSERT INTO `order_detail` (`entity_id`, `order_id`, `user_name`, `user_mobile_number`, `user_detail`, `restaurant_detail`, `item_detail`, `is_updateorder`) VALUES
(1, 1, 'Erik KK', '917537537537', 'a:2:{s:10:\"first_name\";s:4:\"Erik\";s:9:\"last_name\";s:2:\"KK\";}', 'O:8:\"stdClass\":21:{s:4:\"name\";s:14:\"Spice Symphony\";s:5:\"image\";s:42:\"restaurant/Spice-Symphony-en1660021045.jpg\";s:7:\"timings\";a:4:{s:4:\"open\";s:7:\"6:35 PM\";s:5:\"close\";s:7:\"6:25 PM\";s:3:\"off\";s:4:\"open\";s:7:\"closing\";s:4:\"Open\";}s:12:\"phone_number\";s:10:\"2126126607\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:17:\"spice23@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"18.37\";s:21:\"is_service_fee_enable\";s:1:\"1\";s:16:\"service_fee_type\";s:10:\"Percentage\";s:11:\"service_fee\";s:5:\"15.39\";s:7:\"address\";s:53:\"110 Wall Street, 110 Wall St, New York, NY 10005, USA\";s:8:\"landmark\";N;s:7:\"zipcode\";s:6:\"905350\";s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:17:\"40.70491229999999\";s:9:\"longitude\";s:11:\"-74.0064032\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:12:\"enable_hours\";s:1:\"1\";s:17:\"restaurant_status\";s:1:\"1\";}', 'a:2:{i:0;a:14:{s:9:\"item_name\";s:12:\"Pizza Salami\";s:15:\"menu_content_id\";s:3:\"603\";s:7:\"item_id\";s:3:\"167\";s:6:\"qty_no\";s:1:\"1\";s:4:\"rate\";s:5:\"21.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:1;s:12:\"is_customize\";i:0;s:13:\"is_combo_item\";s:1:\"0\";s:18:\"combo_item_details\";s:0:\"\";s:7:\"is_deal\";s:1:\"0\";s:8:\"subTotal\";d:21;s:9:\"itemTotal\";d:21;s:10:\"order_flag\";i:1;}i:1;a:15:{s:9:\"item_name\";s:5:\"Pizza\";s:15:\"menu_content_id\";s:4:\"2142\";s:7:\"item_id\";s:3:\"651\";s:6:\"qty_no\";s:1:\"1\";s:4:\"rate\";s:4:\"7.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:1;s:12:\"is_customize\";i:1;s:13:\"is_combo_item\";i:0;s:18:\"combo_item_details\";s:0:\"\";s:7:\"is_deal\";s:1:\"0\";s:8:\"subTotal\";d:16;s:9:\"itemTotal\";d:16;s:10:\"order_flag\";i:1;s:20:\"addons_category_list\";a:2:{i:0;a:3:{s:18:\"addons_category_id\";s:2:\"43\";s:15:\"addons_category\";s:6:\"Drinks\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:1:\"9\";s:12:\"add_ons_name\";s:5:\"pepsi\";s:13:\"add_ons_price\";s:4:\"5.00\";}}}i:1;a:3:{s:18:\"addons_category_id\";s:2:\"42\";s:15:\"addons_category\";s:4:\"Size\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:2:\"12\";s:12:\"add_ons_name\";s:5:\"small\";s:13:\"add_ons_price\";s:4:\"4.00\";}}}}}}', '0'),
(68, 68, 'Test  Test', '112345678912', 'a:12:{s:10:\"first_name\";s:5:\"Test \";s:9:\"last_name\";s:4:\"Test\";s:10:\"phone_code\";s:1:\"1\";s:12:\"phone_number\";s:11:\"12345678912\";s:5:\"email\";s:9:\"gg@gg.com\";s:7:\"address\";s:0:\"\";s:8:\"landmark\";s:0:\"\";s:7:\"zipcode\";s:0:\"\";s:4:\"city\";s:0:\"\";s:13:\"address_label\";s:0:\"\";s:8:\"latitude\";s:0:\"\";s:9:\"longitude\";s:0:\"\";}', 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"97\";s:10:\"content_id\";s:4:\"1799\";s:4:\"name\";s:11:\"Autoservire\";s:5:\"image\";s:39:\"restaurant/Autoservire-en1660021252.jpg\";s:12:\"phone_number\";s:10:\"2245430221\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:19:\"autoser12@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"20.00\";s:7:\"address\";s:42:\"164-17 Union Tpke, Flushing, NY 11366, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:10:\"40.7219729\";s:9:\"longitude\";s:11:\"-73.8037212\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"30.00\";s:16:\"service_fee_type\";s:6:\"Amount\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{i:0;a:14:{s:9:\"item_name\";s:12:\"Corn Chowder\";s:15:\"menu_content_id\";s:4:\"1677\";s:7:\"item_id\";s:3:\"463\";s:6:\"qty_no\";i:5;s:7:\"comment\";s:0:\"\";s:4:\"rate\";s:5:\"50.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:68;s:12:\"is_customize\";i:0;s:13:\"is_combo_item\";s:1:\"0\";s:18:\"combo_item_details\";s:0:\"\";s:9:\"itemTotal\";i:250;s:10:\"order_flag\";i:1;s:7:\"is_deal\";s:1:\"0\";}i:1;a:14:{s:9:\"item_name\";s:15:\"Stacey Sandoval\";s:15:\"menu_content_id\";s:4:\"2057\";s:7:\"item_id\";s:3:\"603\";s:6:\"qty_no\";i:1;s:7:\"comment\";s:0:\"\";s:4:\"rate\";s:6:\"388.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:68;s:12:\"is_customize\";i:0;s:13:\"is_combo_item\";s:1:\"0\";s:18:\"combo_item_details\";s:0:\"\";s:9:\"itemTotal\";i:388;s:10:\"order_flag\";i:1;s:7:\"is_deal\";s:1:\"0\";}}', '0'),
(69, 69, 'test test', '112345678912', 'a:12:{s:10:\"first_name\";s:4:\"test\";s:9:\"last_name\";s:4:\"test\";s:10:\"phone_code\";s:1:\"1\";s:12:\"phone_number\";s:11:\"12345678912\";s:5:\"email\";s:14:\"mail@gmail.com\";s:7:\"address\";s:0:\"\";s:8:\"landmark\";s:0:\"\";s:7:\"zipcode\";s:0:\"\";s:4:\"city\";s:0:\"\";s:13:\"address_label\";s:0:\"\";s:8:\"latitude\";s:0:\"\";s:9:\"longitude\";s:0:\"\";}', 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"69\";s:10:\"content_id\";s:4:\"1534\";s:4:\"name\";s:10:\"Las Palmas\";s:5:\"image\";s:47:\"restaurant/Parallax-restaurant-en1660020710.jpg\";s:12:\"phone_number\";s:10:\"7412741274\";s:10:\"phone_code\";s:2:\"91\";s:5:\"email\";s:19:\"parrr12@yopmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"12.55\";s:7:\"address\";s:51:\"PLANTA Queen, 15 W 27th St, New York, NY 10001, USA\";s:7:\"zipcode\";N;s:4:\"city\";s:15:\"New York County\";s:8:\"latitude\";s:10:\"23.7727471\";s:9:\"longitude\";s:17:\"86.73220719999999\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"13.66\";s:16:\"service_fee_type\";s:10:\"Percentage\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:0:{}', '1'),
(90, 90, 'Haus des Döners Customer', '16066060606', 'a:10:{s:10:\"first_name\";s:7:\"Haus des Döners\";s:9:\"last_name\";s:8:\"Customer\";s:10:\"address_id\";s:0:\"\";s:7:\"address\";s:0:\"\";s:8:\"landmark\";s:0:\"\";s:7:\"zipcode\";s:0:\"\";s:4:\"city\";s:0:\"\";s:13:\"address_label\";s:0:\"\";s:8:\"latitude\";s:0:\"\";s:9:\"longitude\";s:0:\"\";}', 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"17\";s:10:\"content_id\";s:3:\"589\";s:4:\"name\";s:14:\"Spice Symphony\";s:5:\"image\";s:42:\"restaurant/Spice-Symphony-en1660021045.jpg\";s:12:\"phone_number\";s:10:\"2126126607\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:17:\"spice23@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"18.37\";s:7:\"address\";s:53:\"110 Wall Street, 110 Wall St, New York, NY 10005, USA\";s:7:\"zipcode\";s:6:\"905350\";s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:17:\"40.70491229999999\";s:9:\"longitude\";s:11:\"-74.0064032\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"15.39\";s:16:\"service_fee_type\";s:10:\"Percentage\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:3:{i:0;a:15:{s:9:\"item_name\";s:5:\"Pizza\";s:15:\"menu_content_id\";s:4:\"2142\";s:7:\"item_id\";s:3:\"651\";s:6:\"qty_no\";i:1;s:7:\"comment\";s:0:\"\";s:4:\"rate\";s:4:\"7.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:90;s:12:\"is_customize\";i:1;s:13:\"is_combo_item\";i:0;s:18:\"combo_item_details\";s:0:\"\";s:7:\"is_deal\";s:1:\"0\";s:9:\"itemTotal\";i:18;s:10:\"order_flag\";i:1;s:20:\"addons_category_list\";a:2:{i:0;a:3:{s:18:\"addons_category_id\";s:2:\"43\";s:15:\"addons_category\";s:6:\"Drinks\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:2:\"64\";s:12:\"add_ons_name\";s:5:\"pepsi\";s:13:\"add_ons_price\";s:4:\"5.00\";}}}i:1;a:3:{s:18:\"addons_category_id\";s:2:\"42\";s:15:\"addons_category\";s:4:\"Size\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:2:\"68\";s:12:\"add_ons_name\";s:5:\"medum\";s:13:\"add_ons_price\";s:4:\"6.00\";}}}}}i:1;a:14:{s:9:\"item_name\";s:11:\"Pizza Combo\";s:15:\"menu_content_id\";s:4:\"1233\";s:7:\"item_id\";s:3:\"409\";s:6:\"qty_no\";i:2;s:7:\"comment\";s:0:\"\";s:4:\"rate\";s:5:\"60.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:90;s:12:\"is_customize\";i:0;s:13:\"is_combo_item\";s:1:\"1\";s:18:\"combo_item_details\";s:53:\"Paneer tikka pizza + Rustic Pizza + Pizza Margherita \";s:9:\"itemTotal\";i:120;s:10:\"order_flag\";i:1;s:7:\"is_deal\";s:1:\"0\";}i:2;a:14:{s:9:\"item_name\";s:27:\"Almond Flour Banana Muffins\";s:15:\"menu_content_id\";s:4:\"1129\";s:7:\"item_id\";s:3:\"392\";s:6:\"qty_no\";i:1;s:7:\"comment\";s:0:\"\";s:4:\"rate\";s:5:\"10.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:90;s:12:\"is_customize\";i:0;s:13:\"is_combo_item\";s:1:\"0\";s:18:\"combo_item_details\";s:0:\"\";s:9:\"itemTotal\";i:10;s:10:\"order_flag\";i:1;s:7:\"is_deal\";s:1:\"0\";}}', '0'),
(92, 92, 'Haus des Döners Customer', '16066060606', 'a:10:{s:10:\"first_name\";s:7:\"Haus des Döners\";s:9:\"last_name\";s:8:\"Customer\";s:10:\"address_id\";s:0:\"\";s:7:\"address\";s:0:\"\";s:8:\"landmark\";s:0:\"\";s:7:\"zipcode\";s:0:\"\";s:4:\"city\";s:0:\"\";s:13:\"address_label\";s:0:\"\";s:8:\"latitude\";s:0:\"\";s:9:\"longitude\";s:0:\"\";}', 'O:8:\"stdClass\":19:{s:9:\"entity_id\";s:2:\"17\";s:10:\"content_id\";s:3:\"589\";s:4:\"name\";s:14:\"Spice Symphony\";s:5:\"image\";s:42:\"restaurant/Spice-Symphony-en1660021045.jpg\";s:12:\"phone_number\";s:10:\"2126126607\";s:10:\"phone_code\";s:1:\"1\";s:5:\"email\";s:17:\"spice23@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"18.37\";s:7:\"address\";s:53:\"110 Wall Street, 110 Wall St, New York, NY 10005, USA\";s:7:\"zipcode\";s:6:\"905350\";s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:17:\"40.70491229999999\";s:9:\"longitude\";s:11:\"-74.0064032\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:11:\"service_fee\";s:5:\"15.39\";s:16:\"service_fee_type\";s:10:\"Percentage\";s:21:\"is_service_fee_enable\";s:1:\"1\";}', 'a:2:{i:0;a:15:{s:9:\"item_name\";s:5:\"Pizza\";s:15:\"menu_content_id\";s:4:\"2142\";s:7:\"item_id\";s:3:\"651\";s:6:\"qty_no\";i:1;s:7:\"comment\";s:0:\"\";s:4:\"rate\";s:4:\"7.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:92;s:12:\"is_customize\";i:1;s:13:\"is_combo_item\";i:0;s:18:\"combo_item_details\";s:0:\"\";s:7:\"is_deal\";s:1:\"0\";s:9:\"itemTotal\";i:23;s:10:\"order_flag\";i:1;s:20:\"addons_category_list\";a:2:{i:0;a:3:{s:18:\"addons_category_id\";s:2:\"43\";s:15:\"addons_category\";s:6:\"Drinks\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:2:\"66\";s:12:\"add_ons_name\";s:6:\"sprite\";s:13:\"add_ons_price\";s:5:\"12.00\";}}}i:1;a:3:{s:18:\"addons_category_id\";s:2:\"42\";s:15:\"addons_category\";s:4:\"Size\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:2:\"67\";s:12:\"add_ons_name\";s:5:\"small\";s:13:\"add_ons_price\";s:4:\"4.00\";}}}}}i:1;a:14:{s:9:\"item_name\";s:11:\"Pizza Combo\";s:15:\"menu_content_id\";s:4:\"1233\";s:7:\"item_id\";s:3:\"409\";s:6:\"qty_no\";i:1;s:7:\"comment\";s:0:\"\";s:4:\"rate\";s:5:\"60.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:92;s:12:\"is_customize\";i:0;s:13:\"is_combo_item\";s:1:\"1\";s:18:\"combo_item_details\";s:53:\"Paneer tikka pizza + Rustic Pizza + Pizza Margherita \";s:9:\"itemTotal\";i:60;s:10:\"order_flag\";i:1;s:7:\"is_deal\";s:1:\"0\";}}', '0'),
(93, 93, 'Erik KK', '917537537537', 'a:3:{s:10:\"first_name\";s:4:\"Erik\";s:9:\"last_name\";s:2:\"KK\";s:5:\"email\";s:19:\"erik123@yopmail.com\";}', 'O:8:\"stdClass\":25:{s:4:\"name\";s:10:\"Las Palmas\";s:5:\"image\";s:47:\"restaurant/Parallax-restaurant-en1660020710.jpg\";s:7:\"timings\";a:4:{s:4:\"open\";s:8:\"12:05 AM\";s:5:\"close\";s:8:\"11:55 PM\";s:3:\"off\";s:4:\"open\";s:7:\"closing\";s:4:\"Open\";}s:12:\"phone_number\";s:10:\"7412741274\";s:10:\"phone_code\";s:2:\"91\";s:5:\"email\";s:19:\"parrr12@yopmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"12.55\";s:21:\"is_service_fee_enable\";s:1:\"1\";s:16:\"service_fee_type\";s:10:\"Percentage\";s:11:\"service_fee\";s:5:\"13.66\";s:7:\"address\";s:51:\"PLANTA Queen, 15 W 27th St, New York, NY 10001, USA\";s:8:\"landmark\";N;s:7:\"zipcode\";N;s:4:\"city\";s:15:\"New York County\";s:8:\"latitude\";s:10:\"23.7727471\";s:9:\"longitude\";s:17:\"86.73220719999999\";s:15:\"currency_symbol\";s:1:\"$\";s:13:\"currency_code\";s:3:\"USD\";s:12:\"enable_hours\";s:1:\"1\";s:17:\"restaurant_status\";s:1:\"1\";s:24:\"is_creditcard_fee_enable\";s:1:\"0\";s:19:\"creditcard_fee_type\";s:6:\"Amount\";s:14:\"creditcard_fee\";s:4:\"0.00\";s:24:\"allow_scheduled_delivery\";s:1:\"0\";}', 'a:1:{i:0;a:16:{s:9:\"item_name\";s:11:\"Rainbowcake\";s:15:\"menu_content_id\";s:4:\"1556\";s:7:\"item_id\";s:3:\"441\";s:6:\"qty_no\";s:1:\"1\";s:7:\"comment\";s:0:\"\";s:4:\"rate\";s:5:\"10.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:93;s:12:\"is_customize\";i:1;s:13:\"is_combo_item\";i:0;s:18:\"combo_item_details\";s:0:\"\";s:7:\"is_deal\";s:1:\"0\";s:8:\"subTotal\";d:25;s:9:\"itemTotal\";d:25;s:10:\"order_flag\";i:1;s:20:\"addons_category_list\";a:1:{i:0;a:3:{s:18:\"addons_category_id\";s:2:\"42\";s:15:\"addons_category\";s:4:\"Size\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:3:\"117\";s:12:\"add_ons_name\";s:5:\"small\";s:13:\"add_ons_price\";s:5:\"15.00\";}}}}}}', '0'),
(94, 94, 'Erik KK', '917537537537', 'a:4:{s:10:\"first_name\";s:4:\"Erik\";s:9:\"last_name\";s:2:\"KK\";s:10:\"phone_code\";s:2:\"91\";s:13:\"mobile_number\";s:10:\"7537537537\";}', 'O:8:\"stdClass\":15:{s:4:\"name\";s:11:\"Autoservire\";s:5:\"image\";s:39:\"restaurant/Autoservire-en1660021252.jpg\";s:12:\"phone_number\";s:10:\"2245430221\";s:5:\"email\";s:19:\"autoser12@gmail.com\";s:11:\"amount_type\";s:10:\"Percentage\";s:6:\"amount\";s:5:\"20.00\";s:7:\"address\";s:42:\"164-17 Union Tpke, Flushing, NY 11366, USA\";s:8:\"landmark\";N;s:7:\"zipcode\";N;s:4:\"city\";s:8:\"New York\";s:8:\"latitude\";s:10:\"40.7219729\";s:9:\"longitude\";s:11:\"-73.8037212\";s:15:\"currency_symbol\";s:1:\"$\";s:10:\"phone_code\";s:1:\"1\";s:13:\"currency_code\";s:3:\"USD\";}', 'a:2:{i:0;a:12:{s:9:\"item_name\";s:10:\"Sandwich 5\";s:7:\"item_id\";s:3:\"731\";s:6:\"qty_no\";s:1:\"1\";s:4:\"rate\";s:5:\"10.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";i:94;s:12:\"is_customize\";i:1;s:13:\"is_combo_item\";i:0;s:18:\"combo_item_details\";s:0:\"\";s:9:\"itemTotal\";d:60;s:10:\"order_flag\";i:1;s:20:\"addons_category_list\";a:1:{i:0;a:3:{s:18:\"addons_category_id\";i:43;s:15:\"addons_category\";s:6:\"Drinks\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:3:\"145\";s:12:\"add_ons_name\";s:4:\"test\";s:13:\"add_ons_price\";s:5:\"50.00\";}}}}}i:1;a:12:{s:9:\"item_name\";s:10:\"Sandwich 5\";s:7:\"item_id\";s:3:\"731\";s:6:\"qty_no\";s:1:\"1\";s:4:\"rate\";s:5:\"10.00\";s:11:\"offer_price\";s:0:\"\";s:8:\"order_id\";N;s:12:\"is_customize\";i:1;s:13:\"is_combo_item\";i:0;s:18:\"combo_item_details\";s:0:\"\";s:9:\"itemTotal\";d:60;s:10:\"order_flag\";i:2;s:20:\"addons_category_list\";a:1:{i:0;a:3:{s:18:\"addons_category_id\";i:43;s:15:\"addons_category\";s:6:\"Drinks\";s:11:\"addons_list\";a:1:{i:0;a:3:{s:10:\"add_ons_id\";s:3:\"145\";s:12:\"add_ons_name\";s:4:\"test\";s:13:\"add_ons_price\";s:5:\"50.00\";}}}}}}', '1');

-- --------------------------------------------------------

--
-- Table structure for table `order_driver_map`
--

CREATE TABLE `order_driver_map` (
  `driver_map_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `is_accept` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1- accept, 2- reject ',
  `commission` decimal(10,2) DEFAULT NULL,
  `driver_commission` decimal(10,2) DEFAULT NULL,
  `cancel_reason` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `commission_status` enum('Unpaid','Paid') NOT NULL,
  `distance` decimal(10,2) DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `status_created_by` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_master`
--

CREATE TABLE `order_master` (
  `entity_id` int(11) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `restaurant_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `table_id` int(11) DEFAULT NULL,
  `total_rate` decimal(20,2) DEFAULT NULL,
  `subtotal` decimal(20,2) DEFAULT NULL,
  `tax_rate` decimal(20,2) DEFAULT NULL,
  `tax_type` enum('Percentage','Amount') DEFAULT NULL,
  `service_fee_type` enum('Percentage','Amount') DEFAULT NULL,
  `service_fee` decimal(20,2) DEFAULT NULL,
  `creditcard_fee_type` enum('Percentage','Amount') DEFAULT NULL,
  `creditcard_fee` decimal(20,2) DEFAULT NULL,
  `coupon_discount` decimal(20,2) DEFAULT NULL,
  `coupon_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `coupon_amount` decimal(20,2) DEFAULT NULL,
  `coupon_type` enum('Percentage','Amount') DEFAULT NULL,
  `used_earning` decimal(20,2) DEFAULT NULL,
  `order_delivery` enum('Delivery','PickUp','DineIn') NOT NULL,
  `payment_option` varchar(255) DEFAULT NULL,
  `admin_payment_option` varchar(255) DEFAULT NULL,
  `is_parcel_order` tinyint(4) NOT NULL DEFAULT 0,
  `payment_status` enum('paid','unpaid','pending','processing') DEFAULT NULL,
  `order_status` enum('placed','accepted','delivered','onGoing','cancel','preparing','pending','complete','ready','rejected') DEFAULT NULL,
  `accept_order_time` datetime DEFAULT NULL,
  `order_date` datetime DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `scheduled_time` time DEFAULT NULL,
  `slot_open_time` time DEFAULT NULL,
  `slot_close_time` time DEFAULT NULL,
  `is_delayed` tinyint(4) NOT NULL DEFAULT 0,
  `delayed_datetime` datetime DEFAULT NULL,
  `order_timestamp` varchar(255) DEFAULT NULL COMMENT 'for app',
  `status` int(11) DEFAULT NULL,
  `paid_status` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `order_no` int(11) DEFAULT NULL,
  `delivery_charge` decimal(20,2) DEFAULT NULL,
  `delivery_method` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `delivery_tracking_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'doordash/relay driver tracking url',
  `third_party_delivery_charge` decimal(20,2) DEFAULT NULL,
  `third_party_delivery_data` varchar(255) DEFAULT NULL COMMENT 'for doordash details to be displayed in admin panel',
  `extra_comment` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `delivery_instructions` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Delivery instructions for driver',
  `cancel_reason` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `reject_reason` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `invoice` varchar(255) DEFAULT NULL,
  `refunded_amount` float(10,2) DEFAULT NULL COMMENT 'in cents',
  `stripe_refund_id` varchar(100) DEFAULT NULL,
  `refund_status` varchar(50) DEFAULT NULL,
  `refund_reason` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_master`
--

INSERT INTO `order_master` (`entity_id`, `transaction_id`, `user_id`, `agent_id`, `restaurant_id`, `address_id`, `coupon_id`, `table_id`, `total_rate`, `subtotal`, `tax_rate`, `tax_type`, `service_fee_type`, `service_fee`, `creditcard_fee_type`, `creditcard_fee`, `coupon_discount`, `coupon_name`, `coupon_amount`, `coupon_type`, `used_earning`, `order_delivery`, `payment_option`, `admin_payment_option`, `is_parcel_order`, `payment_status`, `order_status`, `accept_order_time`, `order_date`, `scheduled_date`, `scheduled_time`, `slot_open_time`, `slot_close_time`, `is_delayed`, `delayed_datetime`, `order_timestamp`, `status`, `paid_status`, `order_no`, `delivery_charge`, `delivery_method`, `delivery_tracking_url`, `third_party_delivery_charge`, `third_party_delivery_data`, `extra_comment`, `delivery_instructions`, `cancel_reason`, `reject_reason`, `invoice`, `refunded_amount`, `stripe_refund_id`, `refund_status`, `refund_reason`, `created_by`, `created_date`, `updated_by`, `updated_date`) VALUES
(1, 'pi_3LUq1KDx91eqc6CT2a68HqwV', 23, NULL, 17, NULL, NULL, NULL, '49.49', '37.00', '18.37', 'Percentage', 'Percentage', '15.39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'PickUp', 'stripe', NULL, 0, NULL, 'complete', '2022-08-09 12:52:54', '2022-08-09 10:51:01', NULL, NULL, NULL, NULL, 1, NULL, NULL, 1, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-08-09 10:51:01', NULL, NULL),
(68, 'pi_3Lxpj3Dx91eqc6CT2Ai8KOjm', 0, NULL, 97, NULL, NULL, NULL, '795.60', '638.00', '20.00', 'Percentage', 'Amount', '30.00', NULL, NULL, NULL, '', NULL, NULL, '0.00', 'PickUp', 'stripe', NULL, 0, NULL, 'cancel', NULL, '2022-10-28 10:23:00', NULL, NULL, NULL, NULL, 1, NULL, '1666952630348', 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'due to a lack of supervision.', NULL, NULL, 795.60, 're_3Lxpj3Dx91eqc6CT2rEz4LPB', 'refunded', 'Refunded as the order wasn\'t accepted', NULL, '2022-10-28 10:23:48', 0, '2022-12-21 13:37:54'),
(69, '', 0, NULL, 69, NULL, NULL, NULL, '0.00', '0.00', '12.55', 'Percentage', 'Percentage', '13.66', NULL, NULL, '0.00', '', NULL, NULL, '0.00', 'PickUp', 'stripe', 'cod', 0, NULL, 'complete', '2022-10-28 12:00:32', '2022-10-28 10:29:00', NULL, NULL, NULL, NULL, 1, NULL, '1666952954071', 1, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, 'refund reason', NULL, '2022-10-28 10:29:13', 622, '2022-12-22 10:29:07'),
(90, '', 1099, NULL, 17, NULL, NULL, NULL, '217.97', '148.00', '18.37', 'Percentage', 'Percentage', '15.39', NULL, NULL, NULL, '', NULL, NULL, '0.00', 'PickUp', 'cod', NULL, 0, NULL, 'cancel', NULL, '2022-12-13 15:41:00', NULL, NULL, NULL, NULL, 1, NULL, '1670946109568', 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, 'tesekkurler', '', 'Wrong item ordered.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-13 15:41:48', NULL, NULL),
(92, '', 1099, NULL, 17, NULL, NULL, NULL, '111.02', '83.00', '18.37', 'Percentage', 'Percentage', '15.39', NULL, NULL, NULL, '', NULL, NULL, '0.00', 'PickUp', 'cod', NULL, 0, NULL, 'cancel', NULL, '2022-12-16 14:15:00', NULL, NULL, NULL, NULL, 1, NULL, '1671200153629', 0, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'due to a lack of supervision.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2022-12-16 14:15:54', NULL, NULL),
(93, 'pi_3MHiXrDx91eqc6CT192hg4Xj', 23, NULL, 69, NULL, NULL, NULL, '31.56', '25.00', '12.55', 'Percentage', 'Percentage', '13.66', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'PickUp', 'stripe', NULL, 0, NULL, 'cancel', '2022-12-22 06:47:28', '2022-12-22 06:46:39', NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 'paid', NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, 31.56, 're_3MHiXrDx91eqc6CT1XCwhqgw', 'refunded', 'test full refund<br/>test refund', NULL, '2022-12-22 06:46:39', 1, '2022-12-22 06:48:17'),
(94, NULL, 23, NULL, 97, 0, 0, 11, '185.00', '120.00', '20.00', 'Percentage', 'Amount', '30.00', NULL, '0.00', '0.00', 'D', '0.00', '', NULL, 'DineIn', 'cod', NULL, 0, NULL, 'accepted', '2022-12-22 06:48:45', '2022-12-22 09:20:00', NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, 'unpaid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2022-12-22 06:48:45', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_notification`
--

CREATE TABLE `order_notification` (
  `notification_id` int(11) NOT NULL,
  `last_order_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `order_count` int(11) NOT NULL,
  `dinein_count` int(11) DEFAULT NULL,
  `view_status` tinyint(4) NOT NULL,
  `dinein_view_status` tinyint(4) DEFAULT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_notification`
--

INSERT INTO `order_notification` (`notification_id`, `last_order_id`, `admin_id`, `order_count`, `dinein_count`, `view_status`, `dinein_view_status`, `date`) VALUES
(1, 94, 1, 0, 0, 1, 0, '2022-12-23'),
(2, 92, 1095, 0, 0, 1, 0, '2022-12-16'),
(3, 56, 631, 0, 0, 1, 1, '2022-10-11');

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

CREATE TABLE `order_status` (
  `status_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_status` enum('placed','preparing','delivered','onGoing','cancel','complete','accepted_by_restaurant','ready','rejected') DEFAULT NULL,
  `time` datetime DEFAULT NULL,
  `status_created_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`status_id`, `order_id`, `user_id`, `order_status`, `time`, `status_created_by`) VALUES
(1, 1, NULL, 'accepted_by_restaurant', '2022-08-09 12:52:55', 'MasterAdmin'),
(2, 1, NULL, 'preparing', '2022-08-09 13:06:17', 'MasterAdmin'),
(3, 1, NULL, 'ready', '2022-08-09 13:06:17', 'MasterAdmin'),
(4, 1, NULL, 'complete', '2022-08-09 13:06:17', 'MasterAdmin'),
(114, 69, NULL, 'accepted_by_restaurant', '2022-10-28 12:00:32', 'Branch Admin'),
(119, 69, NULL, 'preparing', '2022-10-28 12:57:51', 'Branch Admin'),
(120, 69, NULL, 'ready', '2022-10-28 12:57:56', 'Branch Admin'),
(121, 69, NULL, 'complete', '2022-10-28 12:58:00', 'Branch Admin'),
(183, 68, NULL, 'cancel', '2022-12-21 13:37:54', 'auto_cancelled'),
(185, 90, NULL, 'cancel', '2022-12-21 13:37:59', 'auto_cancelled'),
(187, 92, NULL, 'cancel', '2022-12-21 13:38:03', 'auto_cancelled'),
(188, 93, 1, 'accepted_by_restaurant', '2022-12-22 06:47:28', 'MasterAdmin'),
(189, 93, 1, 'ready', '2022-12-22 06:47:31', 'MasterAdmin'),
(190, 93, NULL, 'cancel', '2022-12-22 06:48:17', 'MasterAdmin'),
(191, 94, NULL, 'accepted_by_restaurant', '2022-12-22 06:48:45', 'MasterAdmin'),
(192, 90, 23, 'cancel', '2022-12-23 02:25:19', 'Customer');

-- --------------------------------------------------------

--
-- Table structure for table `partial_refund_log`
--

CREATE TABLE `partial_refund_log` (
  `prefund_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `transaction_id` varchar(150) DEFAULT NULL,
  `payment_option` varchar(100) DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refund_reason` varchar(255) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `partial_refund_log`
--

INSERT INTO `partial_refund_log` (`prefund_id`, `order_id`, `transaction_id`, `payment_option`, `refund_amount`, `refund_reason`, `created_date`, `created_by`) VALUES
(3, 68, 're_3Lxpj3Dx91eqc6CT2rEz4LPB', 'stripe', '795.60', 'Refunded as the order wasn\'t accepted', '2022-12-21 08:07:54', 1),
(4, 93, 're_3MHiXrDx91eqc6CT10dA35IL', 'stripe', '13.00', 'test refund', '2022-12-22 01:18:00', 1),
(5, 93, 're_3MHiXrDx91eqc6CT1XCwhqgw', 'stripe', '18.56', 'test full refund', '2022-12-22 01:18:17', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `payment_id` int(11) NOT NULL,
  `payment_gateway_slug` varchar(50) DEFAULT NULL,
  `display_name_en` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `display_name_fr` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `display_name_ar` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `enable_live_mode` tinyint(4) DEFAULT 0,
  `sandbox_client_id` varchar(255) DEFAULT NULL,
  `sandbox_client_secret` varchar(255) DEFAULT NULL,
  `live_client_id` varchar(255) DEFAULT NULL,
  `live_client_secret` varchar(255) DEFAULT NULL,
  `test_publishable_key` varchar(255) DEFAULT NULL,
  `test_secret_key` varchar(255) DEFAULT NULL,
  `test_webhook_secret` varchar(255) DEFAULT NULL COMMENT 'for stripe',
  `live_publishable_key` varchar(255) DEFAULT NULL,
  `live_secret_key` varchar(255) DEFAULT NULL,
  `live_webhook_secret` varchar(255) DEFAULT NULL COMMENT 'for stripe',
  `payment_url` varchar(255) DEFAULT NULL,
  `success_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `failure_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `return_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `sorting` tinyint(4) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `payment_method` text DEFAULT NULL,
  `valid_currency` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`payment_id`, `payment_gateway_slug`, `display_name_en`, `display_name_fr`, `display_name_ar`, `enable_live_mode`, `sandbox_client_id`, `sandbox_client_secret`, `live_client_id`, `live_client_secret`, `test_publishable_key`, `test_secret_key`, `test_webhook_secret`, `live_publishable_key`, `live_secret_key`, `live_webhook_secret`, `payment_url`, `success_url`, `failure_url`, `return_url`, `sorting`, `status`, `payment_method`, `valid_currency`) VALUES
(1, 'paypal', 'PayPal', 'PayPal', 'باي بال', 1, 'Enter Sandbox Client Id', 'Enter Sandbox Client Secret', 'Enter Live Client Id', 'Enter Live Client Secret', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, NULL, 'usd,inr'),
(2, 'stripe', 'Stripe', 'Bande', 'شريط', 1, NULL, NULL, NULL, NULL, 'Enter Test Publishable Key', 'Enter Test Test secret Key', 'Enter Test webhook secret Key', 'Enter Live Publishable Key', 'Enter Live secret Key', 'Enter Live webhook secret Key', NULL, NULL, NULL, NULL, 3, 1, NULL, 'usd,inr'),
(3, 'cod', 'Cash On Delivery', 'Paiement à la livraison', 'الدفع عند الاستلام', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `detail` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `ingredients` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `recipe_detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `food_type` varchar(255) NOT NULL,
  `youtube_video` varchar(320) DEFAULT NULL,
  `recipe_time` int(11) NOT NULL,
  `language_slug` varchar(5) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1-Active',
  `content_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `meta_title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `recipe`
--

INSERT INTO `recipe` (`entity_id`, `name`, `detail`, `slug`, `image`, `ingredients`, `recipe_detail`, `food_type`, `youtube_video`, `recipe_time`, `language_slug`, `status`, `content_id`, `created_by`, `updated_by`, `created_date`, `updated_date`, `meta_title`, `meta_description`, `is_masterdata`) VALUES
(74, 'Strawberry Mojito', 'There will be extra lime slices—use them to squeeze some extra juice into the drink, simply add them as a garnish, or use them to make more mojitos. You can easily adjust the sweetness, tartness, and booziness of the drink by adjusting the amount of', 'strawberry-mojito', 'recipe/976edfeb38b04784d76290b3602d528b.jpg', '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">There will be extra lime slices&mdash;use them to squeeze some extra juice into the drink, simply add them as a garnish, or use them to make more mojitos. You can easily adjust the sweetness, tartness, and booziness of the drink by adjusting the amount of sugar, lime, and soda water.</span></p>', '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">There will be extra lime slices&mdash;use them to squeeze some extra juice into the drink, simply add them as a garnish, or use them to make more mojitos. You can easily adjust the sweetness, tartness, and booziness of the drink by adjusting the amount of sugar, lime, and soda water.</span></p>', '39', 'https://www.youtube.com/watch?v=4KPCswUqOAU', 20, 'en', 1, 2076, 1, 1, '2022-05-19 07:16:09', '2023-01-30 19:35:23', 'Strawberry Mojito', 'There will be extra lime slices—use them to squeeze some extra juice into the drink, simply add them as a garnish, or use them to make more mojitos. You can eas', '0'),
(75, 'Jungle Bird Cocktail', '\"This cocktail has enjoyed a resurgence in the last decade, appearing on menus of trendy cocktail bars. If bitter isn’t your favorite sensation, tread lightly. While the strength of the rum and the sweetness of the pineapple serves as a strong found', 'jungle-bird-cocktail', 'recipe/a1fa73c1fab52f90ede459a6c21290df.jpg', '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">&quot;This cocktail has enjoyed a resurgence in the last decade, appearing on menus of trendy cocktail bars. If bitter isn&rsquo;t your favorite sensation, tread lightly. While the strength of the rum and the sweetness of the pineapple serves as a strong foundation, Campari is the center at which all other flavors surrender.</span></p>', '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">&quot;This cocktail has enjoyed a resurgence in the last decade, appearing on menus of trendy cocktail bars. If bitter isn&rsquo;t your favorite sensation, tread lightly. While the strength of the rum and the sweetness of the pineapple serves as a strong foundation, Campari is the center at which all other flavors surrender.</span></p>', '39', '', 20, 'en', 1, 2077, 1, 1, '2022-05-19 07:22:20', '2023-01-30 19:35:23', 'Jungle Bird Cocktail', 'Jungle Bird Cocktail', '0'),
(76, 'Italian Hot Dog Recipe', 'Italian Hot Dog Recipe', 'italian-hot-dog-recipe', 'recipe/c105a149b70abd330768dd8ba1107d3b.jpg', '<p class=\"comp mntl-sc-block mntl-sc-block-html\" id=\"mntl-sc-block_1-0\" style=\"box-sizing: border-box; margin: 0px 0px 1rem; padding: 0px; letter-spacing: -0.1px; counter-reset: section 0; text-align: left; color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px;\">Italian hot dogs are a New Jersey specialty made famous by Jimmy &quot;Buff&quot; Racioppi and his wife Mary. In the early 1930s, Mary Racioppi made the first Italian hot dogs and served them to Jimmy and his friends. The special hot dogs were so popular with their friends, that they opened a restaurant, &quot;Jimmy Buffs,&quot; featuring the hot dogs.</p>\r\n\r\n<div class=\"comp mntl-sc-block mntl-sc-block-adslot mntl-block\" id=\"mntl-sc-block_1-0-1\" style=\"box-sizing: border-box; margin: 0px; padding: 0px; text-align: left; color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px;\">\r\n<div class=\"comp ad-group-1 mntl-block\" id=\"ad-group-1_1-0\" style=\"box-sizing: border-box; margin: 0px; padding: 0px;\">\r\n<div class=\"comp scads-to-load right-rail__item billboard-sticky billboard1-sticky-dynamic billboard-sticky--sc mntl-sc-sticky-billboard scads-stick-in-parent scads-ad-placed\" data-height=\"1050\" id=\"billboard1-sticky-dynamic_1-0\" style=\"box-sizing: border-box; margin: 0px auto; padding: 0px; height: 1050px; position: absolute; width: 300px; right: -20rem; min-width: 300px; top: -11px;\">\r\n<div class=\"comp mntl-billboard mntl-sc-sticky-billboard-ad billboard1-dynamic mntl-dynamic-billboard mntl-gpt-dynamic-adunit mntl-gpt-adunit gpt billboard dynamic js-immediate-ad\" data-ad-height=\"600\" data-ad-width=\"300\" id=\"mntl-sc-sticky-billboard-ad_3-0\" style=\"box-sizing: border-box; margin: 0px; padding: 0px; text-align: center; min-width: 300px; min-height: calc(310px); transform: translateY(430px);\">&nbsp;</div>\r\n</div>\r\n</div>\r\n</div>', '<p class=\"comp mntl-sc-block mntl-sc-block-html\" id=\"mntl-sc-block_1-0\" style=\"box-sizing: border-box; margin: 0px 0px 1rem; padding: 0px; letter-spacing: -0.1px; counter-reset: section 0; text-align: left; color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px;\">Italian hot dogs are a New Jersey specialty made famous by Jimmy &quot;Buff&quot; Racioppi and his wife Mary. In the early 1930s, Mary Racioppi made the first Italian hot dogs and served them to Jimmy and his friends. The special hot dogs were so popular with their friends, that they opened a restaurant, &quot;Jimmy Buffs,&quot; featuring the hot dogs.</p>\r\n\r\n<div class=\"comp mntl-sc-block mntl-sc-block-adslot mntl-block\" id=\"mntl-sc-block_1-0-1\" style=\"box-sizing: border-box; margin: 0px; padding: 0px; text-align: left; color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px;\">\r\n<div class=\"comp ad-group-1 mntl-block\" id=\"ad-group-1_1-0\" style=\"box-sizing: border-box; margin: 0px; padding: 0px;\">\r\n<div class=\"comp scads-to-load right-rail__item billboard-sticky billboard1-sticky-dynamic billboard-sticky--sc mntl-sc-sticky-billboard scads-stick-in-parent scads-ad-placed\" data-height=\"1050\" id=\"billboard1-sticky-dynamic_1-0\" style=\"box-sizing: border-box; margin: 0px auto; padding: 0px; height: 1050px; position: absolute; width: 300px; right: -20rem; min-width: 300px; top: -11px;\">\r\n<div class=\"comp mntl-billboard mntl-sc-sticky-billboard-ad billboard1-dynamic mntl-dynamic-billboard mntl-gpt-dynamic-adunit mntl-gpt-adunit gpt billboard dynamic js-immediate-ad\" data-ad-height=\"600\" data-ad-width=\"300\" id=\"mntl-sc-sticky-billboard-ad_3-0\" style=\"box-sizing: border-box; margin: 0px; padding: 0px; text-align: center; min-width: 300px; min-height: calc(310px); transform: translateY(430px);\">&nbsp;</div>\r\n</div>\r\n</div>\r\n</div>', '39', '', 80, 'en', 1, 2078, 1, 1, '2022-05-19 07:24:29', '2023-01-30 19:35:23', 'Italian Hot Dog Recipe', 'Italian Hot Dog Recipe', '0'),
(77, 'Keto Crab Cakes Recipe', 'Because the texture of crab is an integral part of the cakes, refrain from over-mixing. You want to still see visible lumps of crab. If you find that they seem soft when you form them, you can add an additional teaspoon of coconut flour. You can pre', 'keto-crab-cakes-recipe', 'recipe/81613692e27cf5733b39fd32d7495ea7.jpg', '<div class=\"comp theme-recipetip text-passage mntl-sc-block lifestyle-sc-block-callout mntl-sc-block-callout mntl-block\" data-tracking-container=\"true\" data-tracking-id=\"mntl-sc-block-callout\" id=\"mntl-sc-block_3-0-27\" style=\"box-sizing: border-box; margin: 2rem 1.75rem; padding: 1.5rem 2rem 1.5rem 0px; font-size: 17px; line-height: 1.6875; position: relative; clear: both; background-color: rgb(244, 249, 254); text-align: left; color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif;\">\r\n<div class=\"comp text-passage mntl-sc-block-callout-body mntl-text-block\" id=\"mntl-sc-block-callout-body_1-0\" style=\"box-sizing: border-box; margin: 0px; padding: 0px 0px 0px 2rem; font-size: 1.0625rem; line-height: 1.6875;\">\r\n<ul style=\"box-sizing: border-box; margin: 0px 0px 0px -0.75rem; padding-right: 0px; padding-left: 0px; list-style: none;\">\r\n	<li style=\"box-sizing: border-box; margin: 1rem 0px; padding: 0px 0px 0px 2rem; line-height: 1.6; position: relative; display: table;\">Because the texture of crab is an integral part of the cakes, refrain from over-mixing. You want to still see visible lumps of crab.</li>\r\n	<li style=\"box-sizing: border-box; margin: 1rem 0px; padding: 0px 0px 0px 2rem; line-height: 1.6; position: relative; display: table;\">If you find that they seem soft when you form them, you can add an additional teaspoon of coconut flour</li>\r\n</ul>\r\n</div>\r\n</div>', '<div class=\"comp theme-recipetip text-passage mntl-sc-block lifestyle-sc-block-callout mntl-sc-block-callout mntl-block\" data-tracking-container=\"true\" data-tracking-id=\"mntl-sc-block-callout\" id=\"mntl-sc-block_3-0-27\" style=\"box-sizing: border-box; margin: 2rem 1.75rem; padding: 1.5rem 2rem 1.5rem 0px; font-size: 17px; line-height: 1.6875; position: relative; clear: both; background-color: rgb(244, 249, 254); text-align: left; color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif;\">\r\n<div class=\"comp text-passage mntl-sc-block-callout-body mntl-text-block\" id=\"mntl-sc-block-callout-body_1-0\" style=\"box-sizing: border-box; margin: 0px; padding: 0px 0px 0px 2rem; font-size: 1.0625rem; line-height: 1.6875;\">\r\n<ul style=\"box-sizing: border-box; margin: 0px 0px 0px -0.75rem; padding-right: 0px; padding-left: 0px; list-style: none;\">\r\n	<li style=\"box-sizing: border-box; margin: 1rem 0px; padding: 0px 0px 0px 2rem; line-height: 1.6; position: relative; display: table;\">Because the texture of crab is an integral part of the cakes, refrain from over-mixing. You want to still see visible lumps of crab.</li>\r\n	<li style=\"box-sizing: border-box; margin: 1rem 0px; padding: 0px 0px 0px 2rem; line-height: 1.6; position: relative; display: table;\">If you find that they seem soft when you form them, you can add an additional teaspoon of coconut flour.</li>\r\n	<li style=\"box-sizing: border-box; margin: 1rem 0px 0px; padding: 0px 0px 0px 2rem; line-height: 1.6; position: relative; display: table;\">&nbsp;</li>\r\n</ul>\r\n</div>\r\n</div>', '39', '', 20, 'en', 1, 2079, 1, 1, '2022-05-19 07:31:09', '2023-01-30 19:35:23', 'Keto Crab Cakes Recipe', '', '0'),
(78, 'Chochoyotes Recipe', 'You will find various chochoyote recipes in different regions of Mexico, mostly central and south Mexico. One element remains consistent, and that is the way they are always boiled in a liquid, whether that\'s a soup, broth, or mole. They can provide', 'chochoyotes-recipe', NULL, '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">You will find various chochoyote recipes in different regions of Mexico, mostly central and south Mexico. One element remains consistent, and that is the way they are always boiled in a liquid, whether that&#39;s a soup, broth, or mole. They can provide richness and texture to meatless dishes, and make it more filling. Adding chochoyotes to soup will thicken the broth, as they release a little bit of the starchy masa while they cook.</span></p>', '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">You will find various chochoyote recipes in different regions of Mexico, mostly central and south Mexico. One element remains consistent, and that is the way they are always boiled in a liquid, whether that&#39;s a soup, broth, or mole. They can provide richness and texture to meatless dishes, and make it more filling. Adding chochoyotes to soup will thicken the broth, as they release a little bit of the starchy masa while they cook.</span></p>', '189', '', 66, 'en', 1, 2080, 1, 1, '2022-05-19 07:33:38', '2023-01-30 19:35:23', 'Chochoyotes Recipe', '', '0'),
(79, 'Recette de gâteaux de crabe Keto', 'Parce que la texture du crabe fait partie intégrante des gâteaux, évitez de trop mélanger. Vous voulez toujours voir des morceaux de crabe visibles. Si vous trouvez qu\'ils semblent mous lorsque vous les formez, vous pouvez ajouter une cuillère à caf', 'keto-crab-cakes-recipe', 'recipe/b1fadc8134e12f536a685236be3436f0.jpg', '<p>Parce que la texture du crabe fait partie int&eacute;grante des g&acirc;teaux, &eacute;vitez de trop m&eacute;langer. Vous voulez toujours voir des morceaux de crabe visibles. Si vous trouvez qu&#39;ils semblent mous lorsque vous les formez, vous pouvez ajouter une cuill&egrave;re &agrave; caf&eacute; suppl&eacute;mentaire de farine de noix de coco. Vous pouvez pr&eacute;</p>', '<p>Parce que la texture du crabe fait partie int&eacute;grante des g&acirc;teaux, &eacute;vitez de trop m&eacute;langer. Vous voulez toujours voir des morceaux de crabe visibles.<br />\r\nSi vous trouvez qu&#39;ils semblent mous lorsque vous les formez, vous pouvez ajouter une cuill&egrave;re &agrave; caf&eacute; suppl&eacute;mentaire de farine de noix de coco.</p>', '263', '', 20, 'fr', 1, 2079, 1, NULL, '2022-08-09 06:39:20', '2023-01-30 19:35:23', 'Recette de gâteaux de crabe Keto', '', '0'),
(80, 'وصفة كيتو كراب كيك', 'نظرًا لأن قوام السلطعون جزء لا يتجزأ من الكعك ، امتنع عن الإفراط في الخلط. تريد أن ترى كتل السلطعون المرئية. إذا وجدت أنها تبدو ناعمة عند تكوينها ، يمكنك إضافة ملعقة صغيرة إضافية من دقيق جوز الهند. يمكنك مسبقا', 'keto-crab-cakes-recipe', 'recipe/4984d15eacdd548deef0105c5a1ae6fc.jpg', '<p>نظرًا لأن قوام السلطعون جزء لا يتجزأ من الكعك ، امتنع عن الإفراط في الخلط. تريد أن ترى كتل السلطعون المرئية. إذا وجدت أنها تبدو ناعمة عند تكوينها ، يمكنك إضافة ملعقة صغيرة إضافية من دقيق جوز الهند. يمكنك مسبقا</p>', '<p>نظرًا لأن قوام السلطعون جزء لا يتجزأ من الكعك ، امتنع عن الإفراط في الخلط. تريد أن ترى كتل السلطعون المرئية.<br />\r\nإذا وجدت أنها تبدو ناعمة عند تكوينها ، يمكنك إضافة ملعقة صغيرة إضافية من دقيق جوز الهند.</p>', '140', '', 20, 'ar', 1, 2079, 1, NULL, '2022-08-09 06:40:32', '2023-01-30 19:35:23', '???? ???? ???? ???', '', '0'),
(81, 'وصفة الشوشويوتس', 'ستجد العديد من وصفات chochoyote في مناطق مختلفة من المكسيك ، معظمها في وسط وجنوب المكسيك. يبقى عنصر واحد ثابتًا ، وهذه هي الطريقة التي يتم بها غليها دائمًا في سائل ، سواء كان ذلك حساءًا أو مرقًا أو شامة. يمكنهم تقديمها', 'chochoyotes-recipe', NULL, '<p>ستجد العديد من وصفات chochoyote في مناطق مختلفة من المكسيك ، معظمها في وسط وجنوب المكسيك. يبقى عنصر واحد ثابتًا ، وهذه هي الطريقة التي يتم بها غليها دائمًا في سائل ، سواء كان ذلك حساءًا أو مرقًا أو شامة. يمكن أن توفر الثراء والملمس للأطباق الخالية من اللحوم ، وتجعلها أكثر إشباعًا. ستؤدي إضافة اختيارات المرق إلى الحساء إلى تكثيف المرق ، حيث يطلقون القليل من الماسا النشوية أثناء الطهي.</p>', '<p>ستجد العديد من وصفات chochoyote في مناطق مختلفة من المكسيك ، معظمها في وسط وجنوب المكسيك. يبقى عنصر واحد ثابتًا ، وهذه هي الطريقة التي يتم بها غليها دائمًا في سائل ، سواء كان ذلك حساءًا أو مرقًا أو شامة. يمكن أن توفر الثراء والملمس للأطباق الخالية من اللحوم ، وتجعلها أكثر إشباعًا. ستؤدي إضافة اختيارات المرق إلى الحساء إلى تكثيف المرق ، حيث يطلقون القليل من الماسا النشوية أثناء الطهي.</p>', '140', '', 66, 'ar', 1, 2080, 1, NULL, '2022-08-09 06:42:18', '2023-01-30 19:35:23', '???? ??????????', '', '0'),
(82, 'Recette Chochoyotes', 'Vous trouverez diverses recettes de chochoyote dans différentes régions du Mexique, principalement le centre et le sud du Mexique. Un élément reste cohérent, et c\'est la façon dont ils sont toujours bouillis dans un liquide, qu\'il s\'agisse d\'une sou', 'chochoyotes-recipe', NULL, '<p>Vous trouverez diverses recettes de chochoyote dans diff&eacute;rentes r&eacute;gions du Mexique, principalement le centre et le sud du Mexique. Un &eacute;l&eacute;ment reste coh&eacute;rent, et c&#39;est la fa&ccedil;on dont ils sont toujours bouillis dans un liquide, qu&#39;il s&#39;agisse d&#39;une soupe, d&#39;un bouillon ou d&#39;une taupe. Ils peuvent donner de la richesse et de la texture aux plats sans viande et les rendre plus copieux. L&#39;ajout de chochoyotes &agrave; la soupe &eacute;paissira le bouillon, car ils lib&egrave;rent un peu de p&acirc;te f&eacute;culente pendant la cuisson.</p>', '<p>Vous trouverez diverses recettes de chochoyote dans diff&eacute;rentes r&eacute;gions du Mexique, principalement le centre et le sud du Mexique. Un &eacute;l&eacute;ment reste coh&eacute;rent, et c&#39;est la fa&ccedil;on dont ils sont toujours bouillis dans un liquide, qu&#39;il s&#39;agisse d&#39;une soupe, d&#39;un bouillon ou d&#39;une taupe. Ils peuvent donner de la richesse et de la texture aux plats sans viande et les rendre plus copieux. L&#39;ajout de chochoyotes &agrave; la soupe &eacute;paissira le bouillon, car ils lib&egrave;rent un peu de p&acirc;te f&eacute;culente pendant la cuisson.</p>', '129', '', 66, 'fr', 1, 2080, 1, NULL, '2022-08-09 06:42:27', '2023-01-30 19:35:23', 'Recette Chochoyotes', '', '0'),
(83, 'وصفة الهوت دوج الايطالية', 'وصفة الهوت دوج الايطالية', 'italian-hot-dog-recipe', 'recipe/81ef98f5bf396df32bb11f917db33097.jpg', '<p>الهوت دوج الإيطالية هي إحدى أصناف نيو جيرسي التي اشتهرت على يد جيمي &quot;باف&quot; راتشيوبي وزوجته ماري. في أوائل الثلاثينيات من القرن الماضي ، صنعت ماري راتشيوبي أول هوت دوج إيطالي وقدمها لجيمي وأصدقائه. كانت الهوت دوج الخاصة مشهورة جدًا مع أصدقائهم ، لدرجة أنهم افتتحوا مطعمًا ، &quot;Jimmy Buffs&quot; ، يضم الهوت دوج.</p>', '<p>الهوت دوج الإيطالية هي إحدى أصناف نيو جيرسي التي اشتهرت على يد جيمي &quot;باف&quot; راتشيوبي وزوجته ماري. في أوائل الثلاثينيات من القرن الماضي ، صنعت ماري راتشيوبي أول هوت دوج إيطالي وقدمها لجيمي وأصدقائه. كانت الهوت دوج الخاصة مشهورة جدًا مع أصدقائهم ، لدرجة أنهم افتتحوا مطعمًا ، &quot;Jimmy Buffs&quot; ، يضم الهوت دوج.</p>', '140', '', 80, 'ar', 1, 2078, 1, NULL, '2022-08-09 06:43:45', '2023-01-30 19:35:23', '???? ????? ??? ?????????', '', '0'),
(84, 'Recette de hot-dog italien', 'Recette de hot-dog italien', 'italian-hot-dog-recipe', 'recipe/8864af17f61487d8966336d91ed6e109.jpg', '<p>Les hot-dogs italiens sont une sp&eacute;cialit&eacute; du New Jersey rendue c&eacute;l&egrave;bre par Jimmy &quot;Buff&quot; Racioppi et sa femme Mary. Au d&eacute;but des ann&eacute;es 1930, Mary Racioppi fabriqua les premiers hot-dogs italiens et les servit &agrave; Jimmy et ses amis. Les hot-dogs sp&eacute;ciaux &eacute;taient si populaires aupr&egrave;s de leurs amis qu&#39;ils ont ouvert un restaurant, &quot;Jimmy Buffs&quot;, proposant des hot-dogs.</p>', '<p>Les hot-dogs italiens sont une sp&eacute;cialit&eacute; du New Jersey rendue c&eacute;l&egrave;bre par Jimmy &quot;Buff&quot; Racioppi et sa femme Mary. Au d&eacute;but des ann&eacute;es 1930, Mary Racioppi fabriqua les premiers hot-dogs italiens et les servit &agrave; Jimmy et ses amis. Les hot-dogs sp&eacute;ciaux &eacute;taient si populaires aupr&egrave;s de leurs amis qu&#39;ils ont ouvert un restaurant, &quot;Jimmy Buffs&quot;, proposant des hot-dogs.</p>', '129', '', 80, 'fr', 1, 2078, 1, NULL, '2022-08-09 06:43:48', '2023-01-30 19:35:23', 'Recette de hot-dog italien', '', '0'),
(85, 'كوكتيل جنغل بيرد', 'كوكتيل جنغل بيرد', 'jungle-bird-cocktail', 'recipe/b95bd3c089b02618cd59875e449e2dd8.jpg', '<p>تمتع هذا الكوكتيل بالانتعاش في العقد الماضي ، حيث ظهر في قوائم بارات الكوكتيل العصرية. إذا لم يكن المر هو إحساسك المفضل ، فاخرج برفق. في حين أن قوة الروم وحلاوة الأناناس تعمل كأساس قوي ، فإن Campari هو المركز الذي تستسلم فيه جميع النكهات الأخرى.</p>', '<p>تمتع هذا الكوكتيل بالانتعاش في العقد الماضي ، حيث ظهر في قوائم بارات الكوكتيل العصرية. إذا لم يكن المر هو إحساسك المفضل ، فاخرج برفق. في حين أن قوة الروم وحلاوة الأناناس تعمل كأساس قوي ، فإن Campari هو المركز الذي تستسلم فيه جميع النكهات الأخرى.</p>', '140', '', 20, 'ar', 1, 2077, 1, NULL, '2022-08-09 06:45:24', '2023-01-30 19:35:23', '?????? ???? ????', '', '0'),
(86, 'Cocktail d\'oiseaux de la jungle', 'Cocktail d\'oiseaux de la jungle', 'jungle-bird-cocktail', 'recipe/a46d4eb9c0d42c01019453c42c31f31b.jpg', '<p>Ce cocktail a connu une r&eacute;surgence au cours de la derni&egrave;re d&eacute;cennie, apparaissant sur les cartes des bars &agrave; cocktails branch&eacute;s. Si l&#39;amertume n&#39;est pas votre sensation pr&eacute;f&eacute;r&eacute;e, marchez l&eacute;g&egrave;rement. Alors que la force du rhum et la douceur de l&#39;ananas constituent une base solide, Campari est le centre auquel toutes les autres saveurs s&#39;abandonnent.</p>', '<p>Ce cocktail a connu une r&eacute;surgence au cours de la derni&egrave;re d&eacute;cennie, apparaissant sur les cartes des bars &agrave; cocktails branch&eacute;s. Si l&#39;amertume n&#39;est pas votre sensation pr&eacute;f&eacute;r&eacute;e, marchez l&eacute;g&egrave;rement. Alors que la force du rhum et la douceur de l&#39;ananas constituent une base solide, Campari est le centre auquel toutes les autres saveurs s&#39;abandonnent.</p>', '129', '', 66, 'fr', 1, 2077, 1, NULL, '2022-08-09 06:50:08', '2023-01-30 19:35:23', 'Cocktail d\'oiseaux de la jungle', '', '0'),
(87, 'Mojito à la fraise', 'Mojito à la fraise', 'strawberry-mojito', 'recipe/0ac613c400e2d88f5789d97c6a55a63e.jpg', '<p>Il y aura des tranches de citron vert suppl&eacute;mentaires - utilisez-les pour presser un peu de jus suppl&eacute;mentaire dans la boisson, ajoutez-les simplement comme garniture ou utilisez-les pour faire plus de mojitos. Vous pouvez facilement ajuster la douceur, l&#39;acidit&eacute; et l&#39;alcool de la boisson en ajustant la quantit&eacute; de sucre, de citron vert et d&#39;eau gazeuse.</p>', '<p>Il y aura des tranches de citron vert suppl&eacute;mentaires - utilisez-les pour presser un peu de jus suppl&eacute;mentaire dans la boisson, ajoutez-les simplement comme garniture ou utilisez-les pour faire plus de mojitos. Vous pouvez facilement ajuster la douceur, l&#39;acidit&eacute; et l&#39;alcool de la boisson en ajustant la quantit&eacute; de sucre, de citron vert et d&#39;eau gazeuse.</p>', '129', '', 20, 'fr', 1, 2076, 1, NULL, '2022-08-09 06:51:03', '2023-01-30 19:35:23', 'Mojito à la fraise', '', '0'),
(88, 'موهيتو فراولة', 'موهيتو فراولة', 'strawberry-mojito', 'recipe/f55c6886965957e891d33faecffb171f.jpg', '<p>سيكون هناك شرائح إضافية من الليمون - استخدمها لعصر بعض العصير الإضافي في المشروب ، أو أضفها ببساطة كزينة ، أو استخدمها لصنع المزيد من موهيتو. يمكنك بسهولة ضبط حلاوة المشروب وقوامه اللاذع وقوامه الخفيف عن طريق ضبط كمية السكر والجير ومياه الصودا.</p>', '<p>سيكون هناك شرائح إضافية من الليمون - استخدمها لعصر بعض العصير الإضافي في المشروب ، أو أضفها ببساطة كزينة ، أو استخدمها لصنع المزيد من موهيتو. يمكنك بسهولة ضبط حلاوة المشروب وقوامه اللاذع وقوامه الخفيف عن طريق ضبط كمية السكر والجير ومياه الصودا.</p>', '140', '', 20, 'ar', 1, 2076, 1, NULL, '2022-08-09 06:51:41', '2023-01-30 19:35:23', '?????? ??????', '', '0');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant`
--

CREATE TABLE `restaurant` (
  `entity_id` int(11) NOT NULL,
  `branch_entity_id` int(11) DEFAULT 0 COMMENT 'parent restaurant content id',
  `restaurant_owner_id` int(11) DEFAULT NULL,
  `branch_admin_id` int(11) DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `restaurant_slug` varchar(255) NOT NULL,
  `phone_code` varchar(20) DEFAULT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `capacity` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'event booking capacity',
  `no_of_table` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `no_of_hall` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `hall_capacity` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `amount_type` enum('Percentage','Amount') DEFAULT NULL COMMENT 'service tax type',
  `amount` decimal(20,2) DEFAULT NULL COMMENT 'service tax',
  `service_fee_type` enum('Percentage','Amount') DEFAULT NULL,
  `service_fee` decimal(20,2) DEFAULT NULL,
  `is_service_fee_enable` tinyint(4) DEFAULT NULL,
  `creditcard_fee_type` enum('Percentage','Amount') DEFAULT NULL,
  `creditcard_fee` decimal(20,2) DEFAULT NULL,
  `is_creditcard_fee_enable` tinyint(4) DEFAULT NULL,
  `allow_event_booking` tinyint(4) NOT NULL DEFAULT 0,
  `event_online_availability` decimal(10,2) DEFAULT NULL COMMENT 'in %',
  `event_minimum_capacity` int(11) DEFAULT NULL COMMENT 'event booking minimum capacity',
  `contractual_commission_type` enum('Percentage','Amount') DEFAULT NULL COMMENT 'for order mode pickup',
  `contractual_commission` decimal(10,2) DEFAULT NULL COMMENT 'for order mode pickup',
  `contractual_commission_type_delivery` enum('Percentage','Amount') DEFAULT NULL COMMENT 'for order mode delivery',
  `contractual_commission_delivery` decimal(10,2) DEFAULT NULL COMMENT 'for order mode delivery',
  `enable_hours` tinyint(4) NOT NULL,
  `timings` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `background_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_veg` tinyint(4) DEFAULT NULL,
  `food_type` varchar(150) DEFAULT NULL COMMENT 'After done update field name',
  `driver_commission` decimal(20,2) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1- active',
  `order_mode` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `type_of_res` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Halal,PartialHalal',
  `is_printer_available` tinyint(4) NOT NULL DEFAULT 0,
  `printer_paper_width` int(11) DEFAULT NULL,
  `printer_paper_height` int(11) DEFAULT NULL,
  `about_restaurant` text NOT NULL,
  `enable_table_booking` tinyint(4) NOT NULL DEFAULT 0,
  `table_booking_capacity` varchar(100) DEFAULT NULL,
  `table_online_availability` decimal(10,2) DEFAULT NULL COMMENT 'in %',
  `table_minimum_capacity` int(11) DEFAULT NULL COMMENT 'table booking minimum capacity',
  `allowed_days_table` int(11) DEFAULT NULL COMMENT 'allowed days for table booking',
  `allow_scheduled_delivery` tinyint(4) NOT NULL DEFAULT 0,
  `allowed_days_for_scheduling` int(11) DEFAULT NULL,
  `restaurant_rating` varchar(5) DEFAULT NULL,
  `restaurant_rating_count` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `offlinetime` int(11) NOT NULL DEFAULT 0 COMMENT 'value store in timestamp with selected minute',
  `enable_schedule` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0- no schedule time set, 1- schedule time set',
  `schedule_time` int(11) NOT NULL DEFAULT 0 COMMENT 'value store in timestamp with selected minute use for restaurant busy/normal',
  `schedule_mode` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0-normal,1-Busy and 2- Very Busy',
  `meta_title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `meta_description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `timezone_update` int(2) NOT NULL DEFAULT 0,
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant`
--

INSERT INTO `restaurant` (`entity_id`, `branch_entity_id`, `restaurant_owner_id`, `branch_admin_id`, `currency_id`, `name`, `restaurant_slug`, `phone_code`, `phone_number`, `email`, `capacity`, `no_of_table`, `no_of_hall`, `hall_capacity`, `amount_type`, `amount`, `service_fee_type`, `service_fee`, `is_service_fee_enable`, `creditcard_fee_type`, `creditcard_fee`, `is_creditcard_fee_enable`, `allow_event_booking`, `event_online_availability`, `event_minimum_capacity`, `contractual_commission_type`, `contractual_commission`, `contractual_commission_type_delivery`, `contractual_commission_delivery`, `enable_hours`, `timings`, `image`, `background_image`, `is_veg`, `food_type`, `driver_commission`, `content_id`, `language_slug`, `status`, `order_mode`, `type_of_res`, `is_printer_available`, `printer_paper_width`, `printer_paper_height`, `about_restaurant`, `enable_table_booking`, `table_booking_capacity`, `table_online_availability`, `table_minimum_capacity`, `allowed_days_table`, `allow_scheduled_delivery`, `allowed_days_for_scheduling`, `restaurant_rating`, `restaurant_rating_count`, `created_by`, `created_date`, `updated_by`, `updated_date`, `offlinetime`, `enable_schedule`, `schedule_time`, `schedule_mode`, `meta_title`, `meta_description`, `timezone_update`, `is_masterdata`) VALUES
(17, 0, 439, 523, 231, 'Spice Symphony', 'pizza-eforie-nord', '1', '2126126607', 'spice23@gmail.com', '100', NULL, NULL, NULL, 'Percentage', '18.37', 'Percentage', '15.39', 1, 'Amount', '0.00', 0, 1, '100.00', 5, 'Amount', '13.00', 'Percentage', '13.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:0:\"\";s:5:\"close\";s:0:\"\";s:3:\"off\";s:1:\"0\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/Spice-Symphony-en1660021045.jpg', 'restaurant_background/68d67840ff5d3be8ec392b4732fa8910.jpg', NULL, '39,1,10,152', NULL, 589, 'en', 1, 'PickUp,Delivery', '', 1, 50, 100, '<p>Spice Symphony</p>', 1, '50', '50.00', 2, 2, 0, NULL, NULL, NULL, 1, '2021-04-20 17:50:13', 1, '2022-12-22 07:00:06', 0, 0, 0, '0', 'Spice Symphony', 'Spice Symphony', 1, '0'),
(53, 0, 439, 523, 231, 'Symphonie d\'épices', 'pizza-eforie-nord', '1', '2126126607', 'spice23@gmail.com', '100', NULL, NULL, NULL, 'Percentage', '18.37', 'Percentage', '15.39', 1, 'Amount', '0.00', 0, 1, '100.00', 5, 'Amount', '13.00', 'Percentage', '13.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:0:\"\";s:5:\"close\";s:0:\"\";s:3:\"off\";s:1:\"0\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/Symphonie-dépices-fr1660021863.jpg', 'restaurant_background/4628b05d10af4ae9879bc7c2c33f8816.jpg', NULL, '129,263,141,130', NULL, 589, 'fr', 1, 'PickUp,Delivery', '', 0, NULL, NULL, '<p>Symphonie d&#39;&eacute;pices</p>', 1, '50', '50.00', 2, 2, 0, NULL, NULL, NULL, 1, '2021-06-23 07:39:35', 1, '2022-12-22 07:03:13', 0, 0, 0, '0', 'Symphonie d\'épices', 'Symphonie d\'épices', 1, '0'),
(54, 0, 439, 523, 231, 'سبايس سيمفوني', 'pizza-eforie-nord', '1', '2126126607', 'spice23@gmail.com', '100', NULL, NULL, NULL, 'Amount', '18.37', 'Amount', '15.39', 1, 'Amount', '0.00', 0, 1, '100.00', 5, 'Amount', '13.00', 'Percentage', '13.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:0:\"\";s:5:\"close\";s:0:\"\";s:3:\"off\";s:1:\"0\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/سبايس-سيمفوني-ar1660022011.jpg', 'restaurant_background/2e2c145823c9a48e7c8281b0082a55aa.jpg', NULL, '140,118,119,262', NULL, 589, 'ar', 1, 'PickUp,Delivery', '', 0, NULL, NULL, '<p>????? ???????</p>', 1, '50', '50.00', 2, 2, 0, NULL, NULL, NULL, 1, '2021-06-23 07:44:51', 1, '2022-12-22 07:05:43', 0, 0, 0, '0', 'سبايس سيمفوني', 'سبايس سيمفوني', 1, '0'),
(69, 0, 27, 631, 231, 'Las Palmas', 'las-palmas', '91', '7412741274', 'parrr12@yopmail.com', '20', NULL, NULL, NULL, 'Percentage', '12.55', 'Percentage', '13.66', 1, 'Amount', '0.00', 0, 1, '100.00', 15, 'Amount', '15.00', 'Percentage', '10.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/Parallax-restaurant-en1660020710.jpg', 'restaurant_background/463b37ea1094caee1c4814067b688307.jpg', NULL, '39,1,10,152', NULL, 1534, 'en', 1, 'PickUp,Delivery', '', 0, NULL, NULL, '<p>Las Palmas</p>', 1, '30', '50.00', 5, 6, 0, NULL, NULL, NULL, 27, '2021-07-07 11:17:14', 1, '2022-12-22 06:58:05', 0, 0, 0, '0', 'Las Palmas', 'Las Palmas', 1, '0'),
(97, 0, 622, 623, 231, 'Autoservire', 'sunny-side-up-diner', '1', '2245430221', 'autoser12@gmail.com', '60', NULL, NULL, NULL, 'Percentage', '20.00', 'Amount', '30.00', 1, 'Amount', '0.00', 0, 1, '100.00', 5, 'Amount', '10.00', 'Percentage', '10.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/Autoservire-en1660021252.jpg', 'restaurant_background/f819c8123ac51236d0984b358a3bef55.jpg', NULL, '39,1,10,152', NULL, 1799, 'en', 1, 'PickUp,Delivery', '', 1, 72, 250, '<p>Autoservire</p>', 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, '2021-10-07 05:23:39', 1, '2022-12-22 06:59:41', 0, 1, 1671708358, '1', 'Autoservire', 'Autoservire', 0, '0'),
(98, 0, 622, 623, 231, 'Autoservice', 'sunny-side-up-diner', '1', '2245430221', 'autoser12@gmail.com', '60', NULL, NULL, NULL, 'Percentage', '20.00', 'Amount', '30.00', 1, 'Amount', '0.00', 0, 1, '100.00', 5, 'Amount', '10.00', 'Percentage', '10.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/Autoservice-fr1660021626.jpg', 'restaurant_background/34d98c063b5304097ba051b83dda9725.jpg', NULL, '129,263,141,130', NULL, 1799, 'fr', 1, 'PickUp,Delivery', '', 1, 72, 250, '<p>Autoservice</p>', 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, '2021-10-07 05:30:20', 1, '2022-12-22 07:01:51', 0, 1, 1671708358, '1', 'Autoservice', 'Autoservice', 0, '0'),
(99, 0, 622, 623, 231, 'أوتوسيرفاير', 'sunny-side-up-diner', '1', '2245430221', 'autoser12@gmail.com', '60', NULL, NULL, NULL, 'Percentage', '20.00', 'Amount', '30.00', 1, 'Amount', '0.00', 0, 1, '100.00', 5, 'Amount', '10.00', 'Percentage', '10.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/أوتوسيرفاير-ar1660021736.jpg', 'restaurant_background/76bd63107cb6114fbde4492c07dce1df.jpg', NULL, '140,118,119,262', NULL, 1799, 'ar', 1, 'PickUp,Delivery', '', 1, 72, 250, '<p>???????????</p>', 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, '2021-10-07 05:33:54', 1, '2022-12-22 07:04:41', 0, 1, 1671708358, '1', 'أوتوسيرفاير', 'أوتوسيرفاير', 0, '0'),
(129, 0, 372, 509, 231, 'Pizza Eforie North', 'starbelly', '1', '2096096099', 'pizzaat12@gmail.com', '32', NULL, NULL, NULL, 'Percentage', '12.00', 'Amount', '10.00', 1, 'Amount', '0.00', 0, 1, '100.00', 3, 'Amount', '10.00', 'Percentage', '11.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/Starbelly-en.jpg', NULL, NULL, '39,1,10,152', NULL, 1996, 'en', 1, 'PickUp,Delivery', '', 1, 72, 250, '<p>Pizza Eforie North</p>', 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, '2022-02-17 10:01:57', 1, '2022-12-22 06:59:09', 0, 0, 0, '0', 'Pizza Eforie North', 'We’ve compiled a list of some of the most creative restaurant names that we could find, both here in the United States and around the world. We’re also giving y', 0, '0'),
(145, 0, 372, 509, 231, 'بيتزا إيفوري نورث', 'starbelly', '1', '2096096099', 'pizzaat12@gmail.com', '32', NULL, NULL, NULL, 'Percentage', '12.00', 'Amount', '10.00', 1, 'Amount', '0.00', 0, 1, '100.00', 3, 'Amount', '10.00', 'Percentage', '10.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/Starbully-ar1656509045.jpg', NULL, NULL, '140,118,119,262', NULL, 1996, 'ar', 1, 'PickUp,Delivery', '', 1, 72, 250, '<p>????? ?????? ????</p>', 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 669, '2022-06-29 13:24:05', 1, '2022-12-22 07:04:15', 0, 0, 0, '0', 'بيتزا إيفوري نورث', 'بيتزا إيفوري نورث', 0, '0'),
(149, 0, 372, 509, 231, 'Pizza Eforie Nord', 'starbelly', '1', '2096096099', 'pizzaat12@gmail.com', '32', NULL, NULL, NULL, 'Percentage', '12.00', 'Amount', '10.00', 1, 'Amount', '0.00', 0, 1, '100.00', 3, 'Amount', '10.00', 'Percentage', '10.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', NULL, NULL, NULL, '129,263,141,130', NULL, 1996, 'fr', 1, 'PickUp,Delivery', '', 1, 72, 250, '<p>Pizza Eforie Nord</p>', 0, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, '2022-08-09 05:03:42', 1, '2022-12-22 07:00:55', 0, 0, 0, '0', 'Pizza Eforie Nord', 'Pizza Eforie Nord', 0, '0'),
(150, 0, 27, 631, 231, 'Las Palmas', 'las-palmas', '91', '7412741274', 'parrr12@yopmail.com', '20', NULL, NULL, NULL, 'Percentage', '12.55', 'Percentage', '13.66', 1, 'Amount', '0.00', 0, 1, '100.00', 15, 'Amount', '15.00', 'Percentage', '15.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/Restaurant-Parallaxe-fr1660022174.jpg', NULL, NULL, '129,263,141,130', NULL, 1534, 'fr', 1, 'PickUp,Delivery', '', 0, NULL, NULL, '<p>Las Palmas</p>', 1, '30', '50.00', 5, 6, 0, NULL, NULL, NULL, 1, '2022-08-09 05:16:14', 1, '2022-12-22 07:00:29', 0, 0, 0, '0', 'Las Palmas', 'Las Palmas', 0, '0'),
(151, 0, 27, 631, 231, 'لاس بالماس', 'las-palmas', '91', '7412741274', 'parrr12@yopmail.com', '20', NULL, NULL, NULL, 'Percentage', '12.55', 'Percentage', '13.66', 1, 'Amount', '0.00', 0, 1, '100.00', 15, 'Amount', '15.00', 'Percentage', '15.00', 1, 'a:7:{s:6:\"monday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:7:\"tuesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:9:\"wednesday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"thursday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"friday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:8:\"saturday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}s:6:\"sunday\";a:3:{s:4:\"open\";s:5:\"18:35\";s:5:\"close\";s:5:\"18:25\";s:3:\"off\";s:1:\"1\";}}', 'restaurant/مطعم-بارالاكس-ar1660022394.jpg', 'restaurant_background/c011c723bcb1f61bd42306ff53996be4.jpg', NULL, '140,118,119,262', NULL, 1534, 'ar', 1, 'PickUp,Delivery', '', 0, NULL, NULL, '<p>??? ??????</p>', 1, '30', '50.00', 5, 6, 0, NULL, NULL, NULL, 1, '2022-08-09 05:19:54', 1, '2022-12-22 07:03:42', 0, 0, 0, '0', 'لاس بالماس', 'لاس بالماس', 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_address`
--

CREATE TABLE `restaurant_address` (
  `entity_id` int(11) NOT NULL,
  `resto_entity_id` int(11) NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `landmark` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `latitude` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `longitude` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `zipcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `country` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `state` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant_address`
--

INSERT INTO `restaurant_address` (`entity_id`, `resto_entity_id`, `address`, `landmark`, `latitude`, `longitude`, `zipcode`, `country`, `state`, `city`, `content_id`, `language_slug`) VALUES
(17, 17, '110 Wall Street, 110 Wall St, New York, NY 10005, USA', NULL, '40.70491229999999', '-74.0064032', '905350', 'United States', 'New York', 'New York', 589, 'en'),
(53, 53, '110 Wall Street, 110 Wall St, New York, NY 10005, USA', NULL, '40.7049028', '-74.0064171', '10005', 'United States', 'New York', 'New York', 589, 'fr'),
(54, 54, '110 Wall Street, 110 Wall St, New York, NY 10005, USA', NULL, '40.7049028', '-74.0064171', '10005', 'United States', 'New York', 'New York', 589, 'ar'),
(69, 69, 'PLANTA Queen, 15 W 27th St, New York, NY 10001, USA', NULL, '23.7727471', '86.73220719999999', '11729', 'United States', 'New York', 'New York County', 1534, 'en'),
(97, 97, '164-17 Union Tpke, Flushing, NY 11366, USA', NULL, '40.7219729', '-73.8037212', '11366', 'United States', 'New York', 'New York', 1799, 'en'),
(98, 98, '164-17 Union Tpke, Flushing, NY 11366, USA', NULL, '40.7219729', '-73.8037212', '11366', 'United States', 'New York', 'New York', 1799, 'fr'),
(99, 99, '164-17 Union Tpke, Flushing, NY 11366, USA', NULL, '40.7219729', '-73.8037212', '11366', 'United States', 'New York', 'New York', 1799, 'ar'),
(129, 129, '1322 E Gun Hill Rd, The Bronx, NY 10469, USA', NULL, '40.8709741', '-73.8476496', '10469', 'United States', 'New York', 'New York', 1996, 'en'),
(145, 145, '1322 E Gun Hill Rd, The Bronx, NY 10469, USA', NULL, '40.8709741', '-73.8476496', '10469', 'United States', 'New York', 'New York', 1996, 'ar'),
(149, 149, '1322 E Gun Hill Rd, The Bronx, NY 10469, USA', NULL, '40.8709741', '-73.8476496', '10469', 'United States', 'New York', 'new york', 1996, 'fr'),
(150, 150, '15 W 27th St, New York, NY 10001, USA', NULL, '40.7444668', '-73.9882868', '10001', 'United States', 'New York', 'New York', 1534, 'fr'),
(151, 151, '15 W 27th St, New York, NY 10001, USA', NULL, '40.7444668', '-73.9882868', '10001', 'United States', 'New York', 'New York', 1534, 'ar');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_branch_map`
--

CREATE TABLE `restaurant_branch_map` (
  `map_id` int(11) NOT NULL,
  `branch_admin_id` int(11) NOT NULL COMMENT 'admin of branch',
  `restaurant_content_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant_branch_map`
--

INSERT INTO `restaurant_branch_map` (`map_id`, `branch_admin_id`, `restaurant_content_id`) VALUES
(75, 523, 589),
(140, 631, 1534),
(149, 509, 1996),
(153, 623, 1799);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_delivery_method_map`
--

CREATE TABLE `restaurant_delivery_method_map` (
  `entity_id` int(11) NOT NULL,
  `restaurant_content_id` int(11) NOT NULL,
  `delivery_method_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_driver_map`
--

CREATE TABLE `restaurant_driver_map` (
  `map_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `restaurant_content_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant_driver_map`
--

INSERT INTO `restaurant_driver_map` (`map_id`, `driver_id`, `restaurant_content_id`) VALUES
(1904, 391, 589),
(1905, 391, 1534),
(1906, 391, 1799),
(1907, 391, 1996),
(1909, 319, 589),
(1910, 319, 1534),
(1911, 319, 1799),
(1912, 319, 1996);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_error_reports`
--

CREATE TABLE `restaurant_error_reports` (
  `entity_id` int(11) NOT NULL,
  `report_topic` text DEFAULT NULL,
  `reporter_email` varchar(255) DEFAULT NULL,
  `reporter_message` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant_error_reports`
--

INSERT INTO `restaurant_error_reports` (`entity_id`, `report_topic`, `reporter_email`, `reporter_message`, `created_date`) VALUES
(1, 'address', 'emma20@yopmail.com', 'Dwy5TQaB', '2022-08-10 14:33:58'),
(2, 'phone_number,menu', 'emma34@yopmail.com', '1233', '2022-08-12 13:13:19');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_menu_item`
--

CREATE TABLE `restaurant_menu_item` (
  `entity_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `item_slug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `price` decimal(20,2) DEFAULT NULL,
  `sku` varchar(20) DEFAULT NULL,
  `menu_detail` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ingredients` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `recipe_detail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `recipe_time` int(11) DEFAULT NULL,
  `availability` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `is_veg` tinyint(4) DEFAULT NULL,
  `is_combo_item` tinyint(4) DEFAULT 0,
  `food_type` varchar(150) DEFAULT NULL COMMENT 'After done update field name',
  `status` tinyint(4) DEFAULT NULL COMMENT '1 -active',
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `check_add_ons` tinyint(4) NOT NULL DEFAULT 0,
  `is_deal` tinyint(4) NOT NULL DEFAULT 0,
  `popular_item` tinyint(4) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `stock` tinyint(3) NOT NULL DEFAULT 1 COMMENT '1-in stock 0 out of stock',
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant_menu_item`
--

INSERT INTO `restaurant_menu_item` (`entity_id`, `restaurant_id`, `category_id`, `name`, `item_slug`, `price`, `sku`, `menu_detail`, `image`, `ingredients`, `recipe_detail`, `recipe_time`, `availability`, `is_veg`, `is_combo_item`, `food_type`, `status`, `content_id`, `language_slug`, `check_add_ons`, `is_deal`, `popular_item`, `created_by`, `created_date`, `updated_by`, `updated_date`, `stock`, `is_masterdata`) VALUES
(167, 17, 135, 'Pizza Salami', 'pizza-salami', '21.00', '2689', 'Red sauce,mozzarella,salam,450 Gr', 'menu/Pizza-Salami-en1660132208.jpg', '<p><span class=\"text\">Red sauce,mozzarella,salam,450 Gr</span></p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 0, '152', 1, 603, 'en', 0, 0, 0, 1, '2021-04-22 19:57:20', 1, '2022-08-10 12:31:54', 1, '0'),
(169, 69, 135, 'Pizza Prosciutto', 'pizza-prosciutto', '21.00', '2685', 'Tomato sauce, mozzarella, pressed ham, salami, mushrooms, olives, 450 gr', 'menu/Pizza-Prosciutto-en1660132084.jpg', '<p>Tomato sauce, mozzarella, pressed ham, 450 gr</p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 0, '152', 1, 601, 'en', 0, 0, 0, 1, '2021-04-22 20:04:46', 1, '2022-08-10 12:11:58', 1, '0'),
(170, 129, 135, 'Pizza Pollo', 'pizza-pollo', '24.00', '2683', 'Tomato sauce, mozzarella, chicken breast, 450 gr', 'menu/Pizza-Pollo-en1660041967.png', '<p>Tomato sauce, mozzarella, chicken breast, 450 gr</p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 0, '152', 1, 600, 'en', 0, 0, 0, 1, '2021-04-22 20:06:42', 1, '2022-08-10 12:32:59', 1, '0'),
(173, 97, 135, 'Pizza Carbonara', 'pizza-carbonara', '24.00', '2679', 'Tomato sauce, mozzarella, egg, pressed ham, 450 gr', 'menu/Pizza-Carbonara-en1660041924.png', '<p>Tomato sauce, mozzarella, egg, pressed ham, 450 gr</p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 0, '152', 1, 596, 'en', 0, 0, 0, 1, '2021-04-22 20:13:21', 1, '2022-08-10 12:34:40', 1, '0'),
(392, 17, 93, 'Almond Flour Banana Muffins', 'almond-flour-banana-muffins', '10.00', 'yes', 'These nutritious and delicious almond flour banana muffins are the perfect snack for an afternoon pick-me-up or a quick and easy breakfast. The recipe calls for a cup of mashed, very ripe bananas—the riper the banana, the more concentrated the flavo', 'menu/Almond-Flour-Banana-Muffins-en1660041694.jpg', '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">These nutritious and delicious&nbsp;</span><a data-component=\"link\" data-ordinal=\"1\" data-source=\"inlineLink\" data-type=\"internalLink\" href=\"https://www.thespruceeats.com/almond-flour-recipes-4175676\" style=\"box-sizing: border-box; text-decoration-line: none; background-image: linear-gradient(to right, rgb(0, 143, 185) 0px, rgb(0, 143, 185) 100%); background-position: 0px 97%; background-repeat: repeat-x; background-size: 100% 1px; color: rgb(0, 143, 185); transition: background-image 0.25s ease 0s, color 0.25s ease 0s; font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">almond flour</a><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">&nbsp;banana muffins are the perfect snack for an afternoon pick-me-up or a quick and easy breakfast. The recipe calls for a cup of mashed,&nbsp;</span><a data-component=\"link\" data-ordinal=\"2\" data-source=\"inlineLink\" data-type=\"internalLink\" href=\"https://www.thespruceeats.com/uses-for-overripe-bananas-1389214\" style=\"box-sizing: border-box; text-decoration-line: none; background-image: linear-gradient(to right, rgb(0, 143, 185) 0px, rgb(0, 143, 185) 100%); background-position: 0px 97%; background-repeat: repeat-x; background-size: 100% 1px; color: rgb(0, 143, 185); transition: background-image 0.25s ease 0s, color 0.25s ease 0s; font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">very ripe bananas</a><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; letter-spacing: -0.1px; text-align: left;\">&mdash;the riper the banana, the more concentrated the flavor and sweeter the muffin. It&#39;s a great way to use up bananas starting to go mushy.</span></p>\r\n', NULL, 15, 'Breakfast,Lunch,Dinner', NULL, 0, '152', 1, 1129, 'en', 0, 0, 1, 1, '2021-06-14 06:48:58', 1, '2022-08-10 12:36:13', 1, '0'),
(409, 17, 135, 'Pizza Combo', 'pizza-combo', '60.00', NULL, 'Paneer tikka pizza\r\nRustic Pizza\r\nPizza Margherita \r\n', 'menu/Pizza-Combo-en1660041672.png', '<p>Dough, Cheese, Seasoning, Veggies</p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 1, '39', 1, 1233, 'en', 0, 0, 1, 1, '2021-06-18 11:47:24', 1, '2022-08-10 12:37:56', 1, '0'),
(439, 69, 125, 'Seven Cheese Pizza', 'seven-cheese-pizza', '45.00', 'SCP15', 'An Exotic combination of White mozzarella, cream white cheese, cheddar, monetery jack, cream orange cheese, colby, orange cheddar with jalapeno dip.', 'menu/Seven-Cheese-Pizza-en1660130957.jpg', '<div style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 16px; color: rgb(0, 0, 0); font-family: Okra, Helvetica, sans-serif; text-align: start;\">\r\n<div class=\"sc-iDsUSg sjXEI\" style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 1.6rem; margin-bottom: 3.5rem;\">\r\n<div class=\"sc-1s0saks-17 bGrnCu\" style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 1.6rem; display: flex; flex-direction: row;\">\r\n<div class=\"sc-1s0saks-10 cYSFTJ\" style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 1.6rem; width: 796px; min-width: 1%;\">\r\n<p class=\"sc-1s0saks-12 hcROsL\" style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 1.4rem; margin: 0.5rem 0px; color: rgb(79, 79, 79); max-width: 75%; overflow-wrap: break-word;\">(An Exotic combination of White mozzarella, cream white cheese, cheddar, monetery jack, cream orange cheese, colby, orange cheddar with jalapeno dip</p>\r\n</div>\r\n</div>\r\n</div>\r\n</div>\r\n\r\n<div style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 16px; color: rgb(0, 0, 0); font-family: Okra, Helvetica, sans-serif; text-align: start;\">\r\n<div class=\"sc-iDsUSg sjXEI\" style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 1.6rem; margin-bottom: 3.5rem;\">\r\n<div class=\"sc-1s0saks-17 bGrnCu\" style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 1.6rem; display: flex; flex-direction: row;\">\r\n<div class=\"sc-1tx3445-1 bXZAXS sc-1s0saks-6 eEOGnT\" style=\"box-sizing: inherit; -webkit-tap-highlight-color: transparent; font-size: 1.6rem; width: 1.3rem; height: 1.3rem; border: 1px solid rgb(80, 181, 71); border-radius: 2px; display: flex; -webkit-box-align: center; align-items: center; -webkit-box-pack: center; justify-content: center; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; flex-shrink: 0; margin: 0.6rem 1rem 0px 0px;\" type=\"veg\">&nbsp;</div>\r\n</div>\r\n</div>\r\n</div>\r\n', NULL, 40, 'Breakfast,Lunch', NULL, 0, '152', 1, 1551, 'en', 1, 0, 0, 27, '2021-07-07 12:02:15', 1, '2022-08-10 12:39:49', 1, '0'),
(441, 69, 90, 'Rainbowcake', 'rainbowcake', '10.00', 'yes', 'cake', 'menu/Rainbowcake-en1660041426.jpg', '<p>cake</p>\r\n', NULL, 120, 'Lunch', NULL, 0, '39', 1, 1556, 'en', 1, 0, 0, 482, '2021-07-07 13:57:06', 1, '2022-08-10 12:41:30', 1, '0'),
(463, 97, 24, 'Corn Chowder', 'corn-chowder', '50.00', 'yes', 'Corn Chowder', NULL, '<p><a data-component=\"link\" data-ordinal=\"3\" data-source=\"inlineLink\" data-type=\"internalLink\" href=\"https://www.thespruceeats.com/new-england-corn-chowder-recipe-101196\" style=\"box-sizing: border-box; text-decoration-line: none; background-image: linear-gradient(to right, rgb(0, 143, 185) 0px, rgb(0, 143, 185) 100%); background-position: 0px 97%; background-repeat: repeat-x; background-size: 100% 1px; color: rgb(0, 143, 185); transition: background-image 0.25s ease 0s, color 0.25s ease 0s; font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; text-align: left;\">Corn Chowder</a><span style=\"color: rgb(0, 0, 0); font-family: &quot;Work Sans&quot;, Arial, sans-serif; font-size: 17px; text-align: left;\">&nbsp;</span></p>\r\n', NULL, 20, 'Lunch', NULL, 0, '152', 1, 1677, 'en', 0, 0, 1, 1, '2021-08-06 09:46:06', 1, '2022-08-10 12:42:53', 1, '0'),
(476, 129, 90, 'Fried Rice', 'fried-rice-1', '20.00', 'yes', 'Fried Rice', 'menu/Fried-Rice-en1660041299.jpg', '<p>Fried Rice</p>\r\n', NULL, 20, 'Lunch', NULL, 0, '1', 1, 1702, 'en', 0, 0, 0, 1, '2021-08-19 06:19:38', 1, '2022-08-10 11:14:51', 1, '0'),
(577, 129, 125, 'Udon noodles', 'udon-noodles', '20.00', '666kkk', 'Udon noodles', 'menu/Udon-noodles-en1660129732.jpg', '<p>Udon noodles</p>\r\n', NULL, 99, 'Breakfast', NULL, 0, '152', 1, 2008, 'en', 0, 0, 0, 1, '2022-02-17 12:55:43', 1, '2022-08-10 11:08:52', 1, '0'),
(578, 129, 125, 'Noodles', 'noodles', '5.00', 'yes', 'Noodles', NULL, '<p>details</p>\r\n', NULL, 50, 'Breakfast,Dinner', NULL, 0, '152', 1, 2009, 'en', 0, 0, 0, 1, '2022-02-18 07:52:11', 1, '2022-08-10 11:04:24', 1, '0'),
(579, 129, 125, 'Pizza', 'pizza', '26.00', 'yes', 'pizza', 'menu/Pizza-en1660129301.jpg', '<p>Pizza</p>\r\n', NULL, 56, 'Breakfast', NULL, 0, '152', 1, 2010, 'en', 0, 0, 0, 1, '2022-02-18 07:53:33', 1, '2022-08-10 11:01:41', 1, '0'),
(580, 97, 93, 'Chicken stew', 'chicken-stew', '30.00', 'yes', 'Chicken stew', 'menu/Chicken-stew-en1660041126.png', '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, sans-serif; font-size: 13px; text-align: start; background-color: rgb(245, 245, 247);\">Chicken stew</span></p>\r\n', NULL, 26, 'Lunch', NULL, 0, '152', 1, 2011, 'en', 0, 0, 0, 1, '2022-02-18 07:57:57', 1, '2022-08-10 10:56:15', 1, '0'),
(603, 97, 125, 'Stacey Sandoval', 'stacey-sandoval', '388.00', 'Obcaecati do vero il', 'Quis ut quis deserunt saepe qui eos', 'menu/Stacey-Sandoval-en1660041012.jpg', '<p>1223</p>\r\n', NULL, 61, 'Breakfast,Lunch,Dinner', NULL, 0, '152', 1, 2057, 'en', 0, 0, 1, 1, '2022-05-18 06:44:57', 1, '2022-08-10 10:52:00', 1, '0'),
(651, 17, 135, 'Pizza', 'pizza-9', '7.00', 'yes', 'True to its name, this layered shot takes on Superman\'s classic colors. It\'s fun to make and filled with a delicious combination of fruit flavors. The distinct layers also look amazing, so it\'s sure to impress everyone at your next party.   There ar', NULL, '<p>True to its name, this layered shot takes on Superman&#39;s classic colors. It&#39;s fun to make and filled with a delicious combination of fruit flavors. The distinct layers also look amazing, so it&#39;s sure to impress everyone at your next party.&nbsp; &nbsp;There ar</p>\r\n', NULL, 20, 'Breakfast,Lunch,Dinner', NULL, 0, '152', 1, 2142, 'en', 1, 0, 1, 1, '2022-06-21 17:34:29', 1, '2022-08-10 10:48:28', 1, '0'),
(652, 129, 90, 'Non Veg Soup', 'non-veg-soup', '89.00', 'Yes', 'soup', 'menu/Non-Veg-Soup-en1660040886.png', '<p>soup</p>\r\n', NULL, 2, 'Dinner', NULL, 0, '1', 1, 2143, 'en', 0, 0, 0, 1, '2022-06-21 17:38:14', 1, '2022-08-10 12:26:18', 1, '0'),
(668, 129, 135, 'Pizza', 'pizza-11', '100.00', 'pizzastar', 'Pizza', NULL, '<p>Tasty Pizza.</p>\r\n', NULL, 20, 'Breakfast,Lunch,Dinner', NULL, 0, '152', 1, 2166, 'en', 1, 0, 1, 1, '2022-06-29 11:11:00', 1, '2022-08-10 12:24:47', 1, '0'),
(672, 129, 135, 'Cakesweet', 'cakesweet', '100.00', NULL, 'Blackforest\r\nVanilla\r\n', 'menu/Cakesweet-en1660127104.jpg', '<p>Cake igredients</p>\r\n', NULL, 10, 'Breakfast,Lunch,Dinner', NULL, 1, '152', 1, 2170, 'en', 0, 0, 0, 669, '2022-06-29 12:38:52', 1, '2022-08-10 12:19:43', 1, '0'),
(673, 145, 154, 'كيكسويت', 'cakesweet', '100.00', NULL, 'كيك\r\nفانيلا\r\n', 'menu/كيكسويت-ar1660127135.jpg', '<div class=\"tw-ta-container F0azHf tw-lfl\" id=\"tw-target-text-container\" style=\"overflow: hidden; position: relative; outline: 0px; color: rgb(32, 33, 36); font-family: arial, sans-serif; font-size: 0px; text-align: start; background-color: rgb(248, 249, 250);\" tabindex=\"0\">\r\n<pre class=\"tw-data-text tw-text-large tw-ta\" data-placeholder=\"Translation\" dir=\"rtl\" id=\"tw-target-text\" style=\"unicode-bidi: isolate; font-size: 24px; line-height: 32px; background-color: transparent; border: none; padding: 2px 0.14em 2px 0px; position: relative; margin-top: -2px; margin-bottom: -2px; resize: none; font-family: inherit; overflow: hidden; width: 270px;\">\r\n<span class=\"Y2IQFc\" lang=\"ar\">مكون الكيك</span></pre>\r\n\r\n<div>&nbsp;</div>\r\n</div>\r\n\r\n<div class=\"tw-target-rmn tw-ta-container F0azHf tw-nfl\" id=\"tw-target-rmn-container\" style=\"overflow: hidden; position: relative; outline: 0px; color: rgb(32, 33, 36); font-family: arial, sans-serif; font-size: 0px; text-align: start; background-color: rgb(248, 249, 250);\">&nbsp;</div>\r\n', NULL, 10, 'Breakfast,Lunch,Dinner', NULL, 1, '262', 1, 2170, 'ar', 0, 0, 0, 669, '2022-06-29 13:25:52', 1, '2022-08-10 12:19:20', 1, '0'),
(687, 149, 164, 'Gâteau sucré', 'cakesweet', '100.00', NULL, 'forêt Noire\r\nVanille\r\n', 'menu/Gâteau-sucré-fr1660127059.jpg', '<p>Ingr&eacute;dients du g&acirc;teau</p>\r\n', NULL, 10, 'Breakfast,Lunch,Dinner', NULL, 1, '263', 1, 2170, 'fr', 0, 0, 0, 1, '2022-08-10 10:24:19', 1, '2022-08-10 12:19:23', 1, '0'),
(688, 149, 164, 'Pizza', 'pizza-11', '100.00', 'pizzastar', 'Pizza', NULL, '<p>Tasty pizza</p>\r\n', NULL, 20, 'Breakfast,Lunch,Dinner', NULL, 0, '263', 1, 2166, 'fr', 1, 0, 1, 1, '2022-08-10 10:29:31', 1, '2022-08-10 12:23:29', 1, '0'),
(689, 145, 154, 'بيتزا', 'pizza-11', '100.00', 'T0012', 'بيتزا', NULL, '<p>بيتزا</p>\r\n', NULL, 20, 'Breakfast,Dinner', NULL, 0, '140', 1, 2166, 'ar', 1, 0, 1, 1, '2022-08-10 10:33:32', 1, '2022-08-10 12:24:22', 1, '0'),
(690, 149, 107, 'Soupe sans légumes', 'non-veg-soup', '89.00', 'yes', 'Soupe sans légumes', 'menu/Soupe-sans-légumes-fr1660127741.png', '<p>Soupe sans l&eacute;gumes</p>\r\n', NULL, 26, 'Dinner', NULL, 0, '263', 1, 2143, 'fr', 0, 0, 0, 1, '2022-08-10 10:35:42', 1, '2022-08-10 12:26:16', 1, '0'),
(691, 151, 108, 'شوربة غير نباتية', 'non-veg-soup', '89.00', 'Yes', 'شوربة غير نباتية', 'menu/شوربة-غير-نباتية-ar1660127932.png', '<p>شوربة غير نباتية</p>\r\n', NULL, 26, 'Dinner', NULL, 0, '140', 1, 2143, 'ar', 0, 0, 1, 1, '2022-08-10 10:38:53', 1, '2022-08-10 12:26:13', 1, '0'),
(692, 53, 164, 'Pizza', 'pizza-9', '7.00', 'T123', 'Fidèle à son nom, cette photo en couches reprend les couleurs classiques de Superman. C\'est amusant à faire et rempli d\'une délicieuse combinaison de saveurs de fruits. Les couches distinctes sont également incroyables, il est donc sûr d\'impressionn', NULL, '<p>Fid&egrave;le &agrave; son nom, cette photo en couches reprend les couleurs classiques de Superman. C&#39;est amusant &agrave; faire et rempli d&#39;une d&eacute;licieuse combinaison de saveurs de fruits. Les couches distinctes sont &eacute;galement incroyables, il est donc s&ucirc;r d&#39;impressionner tout le monde lors de votre prochaine f&ecirc;te.</p>\r\n', NULL, 20, 'Dinner', NULL, 0, '129', 1, 2142, 'fr', 1, 0, 0, 1, '2022-08-10 10:43:25', NULL, NULL, 1, '0'),
(693, 54, 108, 'بيتزا', 'pizza-9', '7.00', 'T145', 'طبقًا لاسمها ، تأخذ هذه اللقطة متعددة الطبقات ألوان سوبرمان الكلاسيكية. من الممتع صنعها ومليئة بمزيج لذيذ من نكهات الفاكهة. تبدو الطبقات المميزة أيضًا مذهلة ، لذا فمن المؤكد أنها ستثير إعجاب الجميع في حفلتك القادمة.', NULL, '<p>طبقًا لاسمها ، تأخذ هذه اللقطة متعددة الطبقات ألوان سوبرمان الكلاسيكية. من الممتع صنعها ومليئة بمزيج لذيذ من نكهات الفاكهة. تبدو الطبقات المميزة أيضًا مذهلة ، لذا فمن المؤكد أنها ستثير إعجاب الجميع في حفلتك القادمة.</p>\r\n', NULL, 20, 'Lunch', NULL, 0, '119', 1, 2142, 'ar', 1, 0, 0, 1, '2022-08-10 10:48:26', NULL, NULL, 1, '0'),
(694, 98, 161, 'Stacey Sandoval', 'stacey-sandoval', '388.00', 'Ut T45', 'Stacey Sandoval', 'menu/Stacey-Sandoval-fr1660128628.jpg', '<p>Stacey Sandoval</p>\r\n', NULL, 25, 'Dinner', NULL, 0, '263', 1, 2057, 'fr', 0, 0, 0, 1, '2022-08-10 10:50:28', NULL, NULL, 1, '0'),
(695, 99, 114, 'ستايسي ساندوفال', 'stacey-sandoval', '388.00', 'T45', 'ستايسي ساندوفال', 'menu/ستايسي-ساندوفال-ar1660128706.jpg', '<p>ستايسي ساندوفال</p>\r\n', NULL, 56, 'Lunch', NULL, 0, '119', 1, 2057, 'ar', 0, 0, 0, 1, '2022-08-10 10:51:46', NULL, NULL, 1, '0'),
(696, 98, 100, 'Ragoût de poulet', 'chicken-stew', '30.00', 'yes', 'Ragoût de poulet', 'menu/Ragoût-de-poulet-fr1660128842.png', '<p>Rago&ucirc;t de poulet</p>\r\n', NULL, 56, 'Dinner', NULL, 0, '129', 1, 2011, 'fr', 0, 0, 0, 1, '2022-08-10 10:54:03', NULL, NULL, 1, '0'),
(697, 99, 108, 'يخنة الدجاج', 'chicken-stew', '30.00', 'T126', 'يخنة الدجاج', 'menu/يخنة-الدجاج-ar1660128940.png', '<p>يخنة الدجاج</p>\r\n', NULL, 26, 'Dinner', NULL, 0, '119', 1, 2011, 'ar', 0, 0, 0, 1, '2022-08-10 10:55:40', NULL, NULL, 1, '0'),
(698, 149, 161, 'Pizza', 'pizza', '26.00', 'Yes', 'Pizza', 'menu/Pizza-fr1660129159.jpg', '<p>Pizza</p>\r\n', NULL, 20, 'Dinner', NULL, 0, '141', 1, 2010, 'fr', 0, 0, 0, 1, '2022-08-10 10:59:19', NULL, NULL, 1, '0'),
(699, 145, 156, 'بيتزا', 'pizza', '26.00', 'yes', 'بيتزا', 'menu/بيتزا-ar1660129261.jpg', '<p>بيتزا</p>\r\n', NULL, 26, 'Lunch', NULL, 0, '119', 1, 2010, 'ar', 0, 0, 0, 1, '2022-08-10 11:00:41', 1, '2022-08-10 11:01:01', 1, '0'),
(700, 149, 113, 'Nouilles', 'noodles', '5.00', 'yes', 'Nouilles', NULL, '<p>Nouilles</p>\r\n', NULL, 50, 'Dinner', NULL, 0, '130', 1, 2009, 'fr', 0, 0, 0, 1, '2022-08-10 11:02:43', NULL, NULL, 1, '0'),
(701, 145, 156, 'المعكرونة', 'noodles', '5.00', 'yes', 'المعكرونة', NULL, '<p>المعكرونة</p>\r\n', NULL, 56, 'Dinner', NULL, 0, '262', 1, 2009, 'ar', 0, 0, 0, 1, '2022-08-10 11:04:10', NULL, NULL, 1, '0'),
(702, 149, 161, 'nouilles udon', 'udon-noodles', '20.00', 'T756', 'nouilles udon', 'menu/nouilles-udon-fr1660129628.jpg', '<p>nouilles udon</p>\r\n', NULL, 89, 'Dinner', NULL, 0, '141', 1, 2008, 'fr', 0, 0, 0, 1, '2022-08-10 11:07:08', NULL, NULL, 1, '0'),
(703, 145, 114, 'نودلز أودون', 'udon-noodles', '20.00', 'T123', 'نودلز أودون', NULL, '<p>نودلز أودون</p>\r\n', NULL, 58, 'Breakfast', NULL, 0, '140', 1, 2008, 'ar', 0, 0, 0, 1, '2022-08-10 11:08:37', NULL, NULL, 1, '0'),
(704, 149, 107, 'riz sauté', 'fried-rice-1', '20.00', 'yes', 'riz sauté', 'menu/riz-sauté-fr1660129969.jpg', '<p>riz saut&eacute;</p>\r\n', NULL, 56, 'Lunch', NULL, 0, '141', 1, 1702, 'fr', 0, 0, 0, 1, '2022-08-10 11:12:49', NULL, NULL, 1, '0'),
(705, 145, 154, 'أرز مقلي', 'fried-rice-1', '20.00', 'T45', 'أرز مقلي', NULL, '<p>أرز مقلي</p>\r\n', NULL, 20, 'Dinner', NULL, 0, '119', 1, 1702, 'ar', 0, 0, 0, 1, '2022-08-10 11:14:24', NULL, NULL, 1, '0'),
(706, 98, 113, 'Ragoût de maïs', 'corn-chowder', '50.00', 'Yes', 'Ragoût de maïs', NULL, '<p>Rago&ucirc;t de ma&iuml;s</p>\r\n', NULL, 20, 'Lunch', NULL, 0, '263', 1, 1677, 'fr', 0, 0, 1, 1, '2022-08-10 11:16:20', 1, '2022-08-10 12:42:27', 1, '0'),
(707, 99, 114, 'حساء الذرة', 'corn-chowder', '50.00', 'Yes', 'حساء الذرة', NULL, '<p>حساء الذرة</p>\r\n', NULL, 68, 'Lunch', NULL, 0, '262', 1, 1677, 'ar', 0, 0, 0, 1, '2022-08-10 11:17:49', 1, '2022-08-10 12:42:50', 1, '0'),
(708, 150, 107, 'Gâteau arc-en-ciel', 'rainbowcake', '10.00', 'yes', 'Gâteau arc-en-ciel', 'menu/Gâteau-arc-en-ciel-fr1660130466.jpg', '<p>G&acirc;teau arc-en-ciel</p>\r\n', NULL, 45, 'Lunch', NULL, 0, '130', 1, 1556, 'fr', 1, 0, 0, 1, '2022-08-10 11:21:06', 1, '2022-08-10 12:41:28', 1, '0'),
(709, 54, 108, 'صغير', 'rainbowcake', '10.00', 'Yes', 'صغير', NULL, '<p>صغير</p>\r\n', NULL, 54, 'Breakfast', NULL, 0, '118', 1, 1556, 'ar', 1, 0, 0, 1, '2022-08-10 11:23:31', 1, '2022-08-10 12:41:19', 1, '0'),
(710, 150, 161, 'Pizza aux sept fromages', 'seven-cheese-pizza', '45.00', 'SCP15', 'Pizza aux sept fromages', NULL, '<p>Pizza aux sept fromages</p>\r\n', NULL, 40, 'Breakfast,Lunch', NULL, 0, '263', 1, 1551, 'fr', 1, 0, 0, 1, '2022-08-10 11:26:04', 1, '2022-08-10 12:39:40', 1, '0'),
(711, 145, 154, 'بيتزا السبع اجبان', 'seven-cheese-pizza', '45.00', 'SCP15', 'بيتزا السبع اجبان', NULL, '<p>بيتزا السبع اجبان</p>\r\n', NULL, 9, 'Breakfast,Lunch', NULL, 0, '262', 1, 1551, 'ar', 1, 0, 0, 1, '2022-08-10 11:28:59', 1, '2022-08-10 12:39:47', 1, '0'),
(712, 53, 164, 'Pizzas combinées', 'pizza-combo', '60.00', NULL, 'Pizza paner tikka\r\nPizza rustique\r\nPizza Margarita\r\n', 'menu/Pizzas-combinées-fr1660131079.png', '<p>P&acirc;te, Fromage, Assaisonnement, L&eacute;gumes</p>\r\n', NULL, 30, 'Dinner', NULL, 1, '130', 1, 1233, 'fr', 0, 0, 0, 1, '2022-08-10 11:31:19', 1, '2022-08-10 12:37:43', 1, '0'),
(713, 54, 154, 'بيتزا كومبو', 'pizza-combo', '60.00', NULL, 'بيتزا بانير تكا\r\nبيتزا روستيك\r\nبيتزا مارجريتا\r\n', 'menu/بيتزا-كومبو-ar1660131257.png', '<p>عجين ، جبن ، توابل ، خضروات</p>\r\n', NULL, 60, 'Dinner', NULL, 1, '118', 1, 1233, 'ar', 0, 0, 0, 1, '2022-08-10 11:34:17', 1, '2022-08-10 12:37:53', 1, '0'),
(714, 53, 100, 'Muffins aux bananes et à la farine d\'amandes', 'almond-flour-banana-muffins', '10.00', 'yes', 'Ces muffins aux bananes et à la farine d\'amandes nutritifs et délicieux sont la collation parfaite pour un remontant l\'après-midi ou un petit-déjeuner rapide et facile. La recette demande une tasse de purée de bananes très mûres - plus la banane est', 'menu/Muffins-aux-bananes-et-à-la-farine-damandes-fr1660131450.jpg', '<p>Ces muffins aux bananes et &agrave; la farine d&#39;amandes nutritifs et d&eacute;licieux sont la collation parfaite pour un remontant l&#39;apr&egrave;s-midi ou un petit-d&eacute;jeuner rapide et facile. La recette demande une tasse de pur&eacute;e de bananes tr&egrave;s m&ucirc;res - plus la banane est m&ucirc;re, plus la saveur est concentr&eacute;e</p>\r\n', NULL, 15, 'Dinner', NULL, 0, '263', 1, 1129, 'fr', 0, 0, 0, 1, '2022-08-10 11:35:46', 1, '2022-08-10 12:36:09', 1, '0'),
(715, 54, 102, 'فطائر الموز بالطحين واللوز', 'almond-flour-banana-muffins', '10.00', 'Yes', 'فطائر الموز المغذية واللذيذة هذه هي الوجبة الخفيفة المثالية لوجبة إفطار سريعة أو سهلة. تتطلب الوصفة كوبًا من الموز المهروس الناضج جدًا - كلما نضج الموز ، زاد تركيز الفلافو', 'menu/فطائر-الموز-بالطحين-واللوز-ar1660131467.jpg', '<p>فطائر الموز المغذية واللذيذة هذه هي الوجبة الخفيفة المثالية لوجبة إفطار سريعة أو سهلة. تتطلب الوصفة كوبًا من الموز المهروس الناضج جدًا - كلما نضج الموز ، زاد تركيز الفلافو</p>\r\n', NULL, 6, 'Lunch', NULL, 0, '262', 1, 1129, 'ar', 0, 0, 1, 1, '2022-08-10 11:37:10', 1, '2022-08-10 12:36:06', 1, '0'),
(716, 98, 164, 'Pizza Carbonara', 'pizza-carbonara', '24.00', '2679', 'Sauce tomate, mozzarella, oeuf, jambon pressé, 450 gr', 'menu/Pizza-Carbonara-fr1660131596.png', '<p>Sauce tomate, mozzarella, oeuf, jambon press&eacute;, 450 gr</p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 0, '263', 1, 596, 'fr', 0, 0, 0, 1, '2022-08-10 11:39:56', 1, '2022-08-10 12:34:38', 1, '0'),
(717, 54, 156, 'بيتزا كاربونارا', 'pizza-carbonara', '24.00', '2679', 'صلصة طماطم ، موزاريلا ، بيض ، لحم خنزير مضغوط ، 450 غرام', 'menu/بيتزا-كاربونارا-ar1660133703.png', '<p>صلصة طماطم ، موزاريلا ، بيض ، لحم خنزير مضغوط ، 450 غرام</p>\r\n', NULL, 56, 'Breakfast,Lunch,Dinner', NULL, 0, '262', 1, 596, 'ar', 0, 0, 0, 1, '2022-08-10 11:41:56', 1, '2022-08-10 12:34:34', 1, '0'),
(718, 149, 164, 'Pizza au poulet', 'pizza-pollo', '24.00', '2683', 'Sauce tomate, mozzarella, blanc de poulet, 450 gr', NULL, '<p>Sauce tomate, mozzarella, blanc de poulet, 450 gr</p>\r\n', NULL, 26, 'Breakfast,Lunch,Dinner', NULL, 0, '263', 1, 600, 'fr', 0, 0, 0, 1, '2022-08-10 11:43:27', 1, '2022-08-10 12:32:55', 1, '0'),
(719, 99, 102, 'بيتزا الدجاج', 'pizza-pollo', '24.00', '2683', 'صلصة طماطم ، موزاريلا ، صدور دجاج ، 450 غرام', NULL, '<p>صلصة طماطم ، موزاريلا ، صدور دجاج ، 450 غرام</p>\r\n', NULL, 78, 'Breakfast,Lunch,Dinner', NULL, 0, '119', 1, 600, 'ar', 0, 0, 0, 1, '2022-08-10 11:44:48', 1, '2022-08-10 12:32:51', 1, '0'),
(720, 149, 164, 'Pizza Prosciutto', 'pizza-prosciutto', '21.00', '2685', 'Sauce tomate, mozzarella, jambon pressé, salami, champignons, olives, 450 gr', 'menu/Pizza-Prosciutto-fr1660132100.jpg', '<p>Sauce tomate, mozzarella, jambon press&eacute;, salami, champignons, olives, 450 gr</p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 0, '263', 1, 601, 'fr', 0, 0, 0, 1, '2022-08-10 11:46:15', 1, '2022-08-10 12:11:53', 1, '0'),
(721, 145, 156, 'بيتزا بروسيوتو', 'pizza-prosciutto', '21.00', '2685', 'بيتزا بروسيوتو', 'menu/بيتزا-بروسيوتو-ar1660132082.jpg', '<p>بيتزا بروسيوتو</p>\r\n', NULL, 20, 'Breakfast,Lunch,Dinner', NULL, 0, '262', 1, 601, 'ar', 0, 0, 0, 1, '2022-08-10 11:48:02', 1, '2022-08-10 12:11:29', 1, '0'),
(722, 53, 164, 'Pizza au salami', 'pizza-salami', '21.00', '2689', 'Sauce rouge,mozzarella,salam,450 Gr', 'menu/بيتزا-سلامي-fr1660132202.jpg', '<p>Sauce rouge,mozzarella,salam,450 Gr</p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 0, '263', 1, 603, 'fr', 0, 0, 0, 1, '2022-08-10 11:50:02', 1, '2022-08-10 12:31:57', 1, '0'),
(723, 54, 156, 'بيتزا سلامي', 'pizza-salami', '21.00', '2689', 'صلصة حمراء ، موتزاريلا ، سلام ، 450 غرام', 'menu/بيتزا-سلامي-ar1660132375.jpg', '<p>صلصة حمراء ، موتزاريلا ، سلام ، 450 غرام</p>\r\n', NULL, 30, 'Breakfast,Lunch,Dinner', NULL, 0, '262', 1, 603, 'ar', 0, 0, 0, 1, '2022-08-10 11:52:33', 1, '2022-08-10 12:31:50', 1, '0');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_menu_recipe_map`
--

CREATE TABLE `restaurant_menu_recipe_map` (
  `map_id` int(11) NOT NULL,
  `menu_content_id` int(11) NOT NULL,
  `recipe_content_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_menu_recipe_map`
--

INSERT INTO `restaurant_menu_recipe_map` (`map_id`, `menu_content_id`, `recipe_content_id`) VALUES
(2, 2193, 2080),
(3, 2169, 2077),
(15, 2010, 2076),
(23, 1556, 2079);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_menu_suggestion`
--

CREATE TABLE `restaurant_menu_suggestion` (
  `entity_id` int(11) NOT NULL,
  `restaurant_content_id` int(11) NOT NULL,
  `menu_content_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_package`
--

CREATE TABLE `restaurant_package` (
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `restaurant_id` int(11) NOT NULL COMMENT 'restaurant content id',
  `price` decimal(20,2) DEFAULT NULL,
  `detail` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `availability` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `language_slug` varchar(5) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant_package`
--

INSERT INTO `restaurant_package` (`entity_id`, `name`, `restaurant_id`, `price`, `detail`, `availability`, `image`, `status`, `content_id`, `language_slug`, `created_by`, `created_date`, `updated_by`, `updated_date`, `is_masterdata`) VALUES
(21, 'Premium Food Package', 1996, '2000.00', '<p><span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, sans-serif; font-size: 13px; text-align: start; background-color: rgb(245, 245, 247);\">Premium Food Package</span></p>', 'Lunch', NULL, 1, 1124, 'en', 1, '2021-06-14 06:39:14', 1, '2022-08-09 06:30:32', '0'),
(22, 'Weekend special offer', 1799, '3000.00', '<p>Weekend special offer&nbsp;</p>', 'Breakfast,Lunch,Dinner', NULL, 1, 1125, 'en', 1, '2021-06-14 06:40:16', 1, '2022-08-09 06:30:06', '0'),
(24, 'حزمة طعام خاصة', 589, '2000.00', '<p>&quot;استمتع بمزيجنا الرائع المكون من خمسة أنواع من الخضروات مع كمية زائدة من الإضافات والجبن اللزج! بانير تكا ، هلابينو ، زيتون أسود ، ذرة أمريكية وبصل مع جبنة موزاريلا على قشرة ناعمة بحجم 11 بوصة.&quot;</p>', 'Lunch,Dinner', NULL, 1, 1196, 'ar', 1, '2021-06-15 15:22:53', 1, '2022-08-09 06:28:09', '0'),
(25, 'Exotic Mexican Combo special offer', 1996, '2400.00', '<p><span style=\"color: rgb(77, 81, 86); font-family: arial, sans-serif; text-align: left;\">Choice Hotels has many hotels offering packages that include a great meal with your stay! Search Choice Hotels special&nbsp;</span><span style=\"font-weight: bold; color: rgb(95, 99, 104); font-family: arial, sans-serif; text-align: left;\">dining packages</span><span style=\"color: rgb(77, 81, 86); font-family: arial, sans-serif; text-align: left;\">&nbsp;for a great deal.</span></p>', 'Lunch', NULL, 1, 1216, 'fr', 1, '2021-06-16 14:11:35', 1, '2022-08-09 06:25:40', '0'),
(26, 'Classic Package', 1799, '2600.00', '<p><span style=\"color: rgb(68, 68, 68); font-family: Roboto, sans-serif; font-size: 16px; text-align: left;\">Below are the restaurants participating in the CFCP program. Show your support by ordering from your local restaurants and sending a meal to a family in need. If you are a restaurant offering a Comfort Food Care Package, fill out this form to add your restaurant to our growing list.</span></p>', 'Lunch', 'package/53cf7697ec49ab8842fd85a7ee56e701.jpg', 1, 1310, 'en', 1, '2021-06-23 09:17:43', 1, '2022-08-09 06:22:13', '0'),
(66, 'Forfait Classique', 1799, '2600.00', '<p>Vous trouverez ci-dessous les restaurants participant au programme CFCP. Montrez votre soutien en commandant dans vos restaurants locaux et en envoyant un repas &agrave; une famille dans le besoin. Si vous &ecirc;tes un restaurant offrant un forfait Comfort Food Care, remplissez ce formulaire pour ajouter votre restaurant &agrave; notre liste croissante.</p>', 'Lunch', 'package/51d290c17ddd5cac655f57807f1da8dc.jpg', 1, 1310, 'fr', 1, '2022-08-09 06:23:45', NULL, NULL, '0'),
(67, 'الباقة الكلاسيكية', 1799, '2600.00', '<p>فيما يلي المطاعم المشاركة في برنامج CFCP. أظهر دعمك من خلال الطلب من المطاعم المحلية وإرسال وجبة إلى الأسرة المحتاجة. إذا كنت مطعمًا يقدم حزمة Comfort Food Care ، فاملأ هذا النموذج لإضافة مطعمك إلى قائمتنا المتنامية.</p>', 'Lunch', 'package/df483b850153c80054641336953face6.jpg', 1, 1310, 'ar', 1, '2022-08-09 06:23:58', NULL, NULL, '0'),
(68, 'Exotic Mexican Combo special offer', 1996, '2400.00', '<p>Exotic Mexican Combo special offer</p>', 'Lunch', NULL, 1, 1216, 'en', 1, '2022-08-09 06:26:12', NULL, NULL, '0'),
(69, 'عرض خاص للكومبو المكسيكي الغريب', 1996, '2400.00', '<p>عرض خاص للكومبو المكسيكي الغريب</p>', 'Lunch', NULL, 1, 1216, 'ar', 1, '2022-08-09 06:26:40', NULL, NULL, '0'),
(70, 'SPECIAL Food Package', 589, '2000.00', '<p>SPECIAL Food Package</p>', 'Lunch,Dinner', NULL, 1, 1196, 'en', 1, '2022-08-09 06:27:25', NULL, NULL, '0'),
(71, 'Forfait Alimentaire SPÉCIAL', 589, '2000.00', '<p>Forfait Alimentaire SP&Eacute;CIAL</p>', 'Lunch,Dinner', NULL, 1, 1196, 'fr', 1, '2022-08-09 06:27:55', NULL, NULL, '0'),
(72, 'عرض خاص في عطلة نهاية الأسبوع', 1799, '3000.00', '<p>عرض خاص في عطلة نهاية الأسبوع</p>', 'Breakfast,Lunch,Dinner', NULL, 1, 1125, 'ar', 1, '2022-08-09 06:29:53', NULL, NULL, '0'),
(73, 'Offre spéciale week-end', 1799, '3000.00', '<p>Offre sp&eacute;ciale week-end</p>', 'Breakfast,Lunch,Dinner', NULL, 1, 1125, 'fr', 1, '2022-08-09 06:30:00', NULL, NULL, '0'),
(74, 'Forfait alimentaire haut de gamme', 1996, '2000.00', '<p>Forfait alimentaire haut de gamme</p>', 'Lunch', NULL, 1, 1124, 'fr', 1, '2022-08-09 06:30:54', NULL, NULL, '0'),
(75, 'باقة طعام مميزة', 1996, '2000.00', '<p>باقة طعام مميزة</p>', 'Lunch', NULL, 1, 1124, 'ar', 1, '2022-08-09 06:31:14', NULL, NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_payment_method_suggestion`
--

CREATE TABLE `restaurant_payment_method_suggestion` (
  `entity_id` int(11) NOT NULL,
  `restaurant_content_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `restaurant_payment_method_suggestion`
--

INSERT INTO `restaurant_payment_method_suggestion` (`entity_id`, `restaurant_content_id`, `payment_id`) VALUES
(22, 1534, 1),
(23, 1534, 2),
(24, 1534, 3),
(137, 589, 1),
(138, 589, 2),
(139, 589, 3),
(177, 1996, 1),
(178, 1996, 2),
(179, 1996, 3),
(205, 1799, 1),
(206, 1799, 2),
(207, 1799, 3);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `entity_id` int(11) NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `review` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `order_user_id` int(11) DEFAULT NULL COMMENT 'driver id',
  `status` tinyint(4) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `restaurant_content_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`entity_id`, `restaurant_id`, `user_id`, `order_id`, `review`, `rating`, `order_user_id`, `status`, `created_by`, `created_date`, `updated_by`, `updated_date`, `restaurant_content_id`) VALUES
(2, 69, 1138, 52, 'Best i ever had ', '5', 0, 1, 1138, '2022-10-04 06:15:49', NULL, NULL, 1534),
(3, 69, 1138, 58, 'Best i ever had ', '5', 0, 1, 1138, '2022-10-29 02:41:03', NULL, NULL, 1534),
(4, NULL, 319, NULL, '5', 'good', 23, 1, NULL, '2022-12-22 10:49:13', NULL, NULL, NULL),
(5, NULL, 319, NULL, '5', 'good', 23, 1, NULL, '2022-12-22 11:07:18', NULL, NULL, NULL),
(6, 97, 23, 90, 'On time delivery', '4', 0, 1, 23, '2022-12-23 04:38:38', NULL, NULL, 1799),
(7, 97, 23, 90, 'Polite behaviour', '4', 319, 1, NULL, '2022-12-23 04:38:38', NULL, NULL, 1799),
(8, 97, 23, NULL, 'Greate Food, very yummy dishes.', '4', NULL, 1, 23, '2022-12-23 04:40:50', NULL, NULL, 1799);

-- --------------------------------------------------------

--
-- Table structure for table `role_access`
--

CREATE TABLE `role_access` (
  `access_id` int(11) NOT NULL,
  `access_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Access Name',
  `controller_slug` varchar(255) DEFAULT NULL,
  `parent_access_id` int(11) DEFAULT NULL COMMENT 'refering same table primary key id',
  `is_hidden` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0 => visible\r\n 1 => hidden',
  `display_order` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `role_access`
--

INSERT INTO `role_access` (`access_id`, `access_name`, `controller_slug`, `parent_access_id`, `is_hidden`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'user_management', 'users', 0, '0', 2, '2022-07-21 04:06:46', NULL),
(2, 'view', 'view', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(3, 'add', 'add', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(4, 'edit', 'edit', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(5, 'active_deactive', 'ajaxdisable', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(6, 'delete', 'ajaxDelete', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(7, 'verify_user_account', 'VerifyAccount', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(8, 'view_order_count', 'view_orders', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(9, 'view_address', 'view_address', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(10, 'add_address', 'add_address', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(11, 'edit_address', 'edit_address', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(12, 'delete_address', 'ajaxDeleteAddress', 1, '0', NULL, '2022-07-21 04:06:46', NULL),
(13, 'admin_management', 'admin', 0, '0', 1, '2022-07-21 04:06:46', NULL),
(14, 'view', 'admin', 13, '0', NULL, '2022-07-21 04:06:46', NULL),
(15, 'add', 'add', 13, '0', NULL, '2022-07-21 04:06:46', NULL),
(16, 'edit', 'edit', 13, '0', NULL, '2022-07-21 04:06:46', NULL),
(17, 'active_deactive', 'ajaxdisable', 13, '0', NULL, '2022-07-21 04:06:46', NULL),
(18, 'driver_management', 'driver', 0, '0', 3, '2022-07-21 04:06:46', NULL),
(19, 'view', 'driver', 18, '0', NULL, '2022-07-21 04:06:46', NULL),
(20, 'add', 'add', 18, '0', NULL, '2022-07-21 04:06:46', NULL),
(21, 'edit', 'edit', 18, '0', NULL, '2022-07-21 04:06:46', NULL),
(22, 'active_deactive', 'ajaxdisable', 18, '0', NULL, '2022-07-21 04:06:46', NULL),
(23, 'export_report', 'driver_generate_report', 18, '0', NULL, '2022-07-21 04:06:46', NULL),
(24, 'view_commission', 'commission', 18, '0', NULL, '2022-07-21 04:06:46', NULL),
(25, 'view_review', 'review', 18, '0', NULL, '2022-07-21 04:06:46', NULL),
(26, 'view_tips', 'drivertip', 18, '0', NULL, '2022-07-21 04:06:46', NULL),
(33, 'restaurant_management', 'restaurant', 0, '0', 4, '2022-07-21 04:06:46', NULL),
(34, 'view', 'view', 33, '0', NULL, '2022-07-21 04:06:46', NULL),
(35, 'add', 'add', 33, '0', NULL, '2022-07-21 04:06:46', NULL),
(36, 'edit', 'edit', 33, '0', NULL, '2022-07-21 04:06:46', NULL),
(37, 'active_deactive', 'ajaxDisableAll', 33, '0', NULL, '2022-07-21 04:06:46', NULL),
(38, 'delete', 'ajaxDeleteAll', 33, '0', NULL, '2022-07-21 04:06:46', NULL),
(39, 'import_res', 'import_restaurant', 33, '0', NULL, '2022-07-21 04:06:46', NULL),
(40, 'online_offline', 'ajax_online_offline', 33, '0', NULL, '2022-07-21 04:06:46', NULL),
(41, 'food_type_management', 'food_type', 0, '0', 5, '2022-07-21 04:06:46', NULL),
(42, 'view', 'view', 41, '0', NULL, '2022-07-21 04:06:46', NULL),
(43, 'add', 'add', 41, '0', NULL, '2022-07-21 04:06:46', NULL),
(44, 'edit', 'edit', 41, '0', NULL, '2022-07-21 04:06:46', NULL),
(45, 'active_deactive', 'ajaxDisableAll', 41, '0', NULL, '2022-07-21 04:06:46', NULL),
(46, 'delete', 'ajaxDeleteAll', 41, '0', NULL, '2022-07-21 04:06:46', NULL),
(47, 'category_management', 'category', 0, '0', 6, '2022-07-21 04:06:46', NULL),
(48, 'view', 'view', 47, '0', NULL, '2022-07-21 04:06:46', NULL),
(49, 'add', 'add', 47, '0', NULL, '2022-07-21 04:06:46', NULL),
(50, 'edit', 'edit', 47, '0', NULL, '2022-07-21 04:06:46', NULL),
(51, 'active_deactive', 'ajaxDisableAll', 47, '0', NULL, '2022-07-21 04:06:46', NULL),
(52, 'delete', 'ajaxDeleteAll', 47, '0', NULL, '2022-07-21 04:06:46', NULL),
(53, 'addons_category_management', 'addons_category', 0, '0', 7, '2022-07-21 04:06:46', NULL),
(54, 'view', 'view', 53, '0', NULL, '2022-07-21 04:06:46', NULL),
(55, 'add', 'add', 53, '0', NULL, '2022-07-21 04:06:46', NULL),
(56, 'edit', 'edit', 53, '0', NULL, '2022-07-21 04:06:46', NULL),
(57, 'active_deactive', 'ajaxDisableAll', 53, '0', NULL, '2022-07-21 04:06:46', NULL),
(58, 'delete', 'ajaxDeleteAll', 53, '0', NULL, '2022-07-21 04:06:46', NULL),
(65, 'menu_management', 'restaurant_menu', 0, '0', 9, '2022-07-21 04:06:46', NULL),
(66, 'view', 'view_menu', 65, '0', NULL, '2022-07-21 04:06:46', NULL),
(67, 'add', 'add_menu', 65, '0', NULL, '2022-07-21 04:06:46', NULL),
(68, 'edit', 'edit_menu', 65, '0', NULL, '2022-07-21 04:06:46', NULL),
(69, 'active_deactive', 'ajaxDisableAll', 65, '0', NULL, '2022-07-21 04:06:46', NULL),
(70, 'delete', 'ajaxDeleteAll', 65, '0', NULL, '2022-07-21 04:06:46', NULL),
(71, 'import_menu', 'import_menu', 65, '0', NULL, '2022-07-21 04:06:46', NULL),
(72, 'stock_update', 'ajaxStockUpdate', 65, '0', NULL, '2022-07-21 04:06:46', NULL),
(73, 'manage_item_suggestion', 'menu_item_suggestion', 65, '0', NULL, '2022-07-21 04:06:46', NULL),
(74, 'rating_review_management', 'review', 0, '0', 11, '2022-07-21 04:06:46', NULL),
(75, 'view', 'view', 74, '0', NULL, '2022-07-21 04:06:46', NULL),
(76, 'delete', 'ajaxDelete', 74, '0', NULL, '2022-07-21 04:06:46', NULL),
(77, 'delivery_charge_management', 'delivery_charge', 0, '0', 12, '2022-07-21 04:06:46', NULL),
(78, 'view', 'view', 77, '0', NULL, '2022-07-21 04:06:46', NULL),
(79, 'add', 'add', 77, '0', NULL, '2022-07-21 04:06:46', NULL),
(80, 'edit', 'edit', 77, '0', NULL, '2022-07-21 04:06:46', NULL),
(81, 'delete', 'ajaxDeleteAll', 77, '0', NULL, '2022-07-21 04:06:46', NULL),
(82, 'order_management', 'order', 0, '0', 14, '2022-07-21 04:06:46', NULL),
(83, 'view', 'view', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(84, 'add', 'add', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(85, 'edit', 'edit_delivery_pickup_order_details', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(86, 'delete', 'ajaxDelete', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(87, 'update_status', 'updateOrderStatus', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(88, 'assign_driver', 'assignDriver', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(89, 'refund', 'ajaxinitiaterefund', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(90, 'print_receipt', 'print_receipt', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(91, 'get_invoice', 'getInvoice', 82, '0', NULL, '2022-07-21 04:06:46', NULL),
(98, 'coupon_management', 'coupon', 0, '0', 17, '2022-07-21 04:06:46', NULL),
(99, 'view', 'view', 98, '0', NULL, '2022-07-21 04:06:46', NULL),
(100, 'add', 'add', 98, '0', NULL, '2022-07-21 04:06:46', NULL),
(101, 'edit', 'edit', 98, '0', NULL, '2022-07-21 04:06:46', NULL),
(102, 'active_deactive', 'ajaxdisable', 98, '0', NULL, '2022-07-21 04:06:46', NULL),
(103, 'delete', 'ajaxDelete', 98, '0', NULL, '2022-07-21 04:06:46', NULL),
(104, 'notification_management', 'notification', 0, '0', 18, '2022-07-21 04:06:46', NULL),
(105, 'view', 'view', 104, '0', NULL, '2022-07-21 04:06:46', NULL),
(106, 'add', 'add', 104, '0', NULL, '2022-07-21 04:06:46', NULL),
(107, 'edit', 'edit', 104, '0', NULL, '2022-07-21 04:06:46', NULL),
(108, 'delete', 'ajaxdeleteNotification', 104, '0', NULL, '2022-07-21 04:06:46', NULL),
(109, 'slider_image_management', 'slider-image', 0, '0', 19, '2022-07-21 04:06:46', NULL),
(110, 'view', 'view', 109, '0', NULL, '2022-07-21 04:06:46', NULL),
(111, 'add', 'add', 109, '0', NULL, '2022-07-21 04:06:46', NULL),
(112, 'edit', 'edit', 109, '0', NULL, '2022-07-21 04:06:46', NULL),
(113, 'active_deactive', 'ajaxdisable', 109, '0', NULL, '2022-07-21 04:06:46', NULL),
(114, 'delete', 'ajaxDelete', 109, '0', NULL, '2022-07-21 04:06:46', NULL),
(115, 'content_management_system', 'cms', 0, '0', 20, '2022-07-21 04:06:46', NULL),
(116, 'view', 'view', 115, '0', NULL, '2022-07-21 04:06:46', NULL),
(117, 'edit', 'edit', 115, '0', NULL, '2022-07-21 04:06:46', NULL),
(118, 'active_deactive', 'ajaxDisableAll', 115, '0', NULL, '2022-07-21 04:06:46', NULL),
(119, 'delete', 'ajaxDeleteAll', 115, '0', NULL, '2022-07-21 04:06:46', NULL),
(120, 'system_option_management', 'system_option', 0, '0', 21, '2022-07-21 04:06:46', NULL),
(121, 'view', 'view', 120, '0', NULL, '2022-07-21 04:06:46', NULL),
(122, 'role_management', 'role', 0, '0', 22, '2022-07-21 04:06:46', NULL),
(123, 'view', 'view', 122, '0', NULL, '2022-07-21 04:06:46', NULL),
(124, 'add', 'add', 122, '0', NULL, '2022-07-21 04:06:46', NULL),
(125, 'edit', 'edit', 122, '0', NULL, '2022-07-21 04:06:46', NULL),
(126, 'active_deactive', 'ajaxDisable', 122, '0', NULL, '2022-07-21 04:06:46', NULL),
(127, 'email_template_management', 'email_template', 0, '0', 26, '2022-07-21 04:06:46', NULL),
(128, 'view', 'view', 127, '0', NULL, '2022-07-21 04:06:46', NULL),
(129, 'add', 'add', 127, '0', NULL, '2022-07-21 04:06:46', NULL),
(130, 'edit', 'edit', 127, '0', NULL, '2022-07-21 04:06:46', NULL),
(131, 'active_deactive', 'ajaxdisable', 127, '0', NULL, '2022-07-21 04:06:46', NULL),
(132, 'delete', 'ajaxDeleteAll', 127, '0', NULL, '2022-07-21 04:06:46', NULL),
(133, 'country_management', 'country', 0, '0', 27, '2022-07-21 04:06:46', NULL),
(134, 'view', 'view', 133, '0', NULL, '2022-07-21 04:06:46', NULL),
(135, 'active_deactive', 'ajaxdisable', 133, '0', NULL, '2022-07-21 04:06:46', NULL),
(136, 'reason_management', 'reason_management', 0, '0', 28, '2022-07-21 04:06:46', NULL),
(137, 'view', 'view', 136, '0', NULL, '2022-07-21 04:06:46', NULL),
(138, 'add', 'add', 136, '0', NULL, '2022-07-21 04:06:46', NULL),
(139, 'edit', 'edit', 136, '0', NULL, '2022-07-21 04:06:46', NULL),
(140, 'active_deactive', 'ajax_disable', 136, '0', NULL, '2022-07-21 04:06:46', NULL),
(141, 'delete', 'ajax_delete_all', 136, '0', NULL, '2022-07-21 04:06:46', NULL),
(142, 'payment_method_management', 'payment_method', 0, '0', 30, '2022-07-21 04:06:46', NULL),
(143, 'view', 'view', 142, '0', NULL, '2022-07-21 04:06:46', NULL),
(144, 'edit', 'edit', 142, '0', NULL, '2022-07-21 04:06:46', NULL),
(145, 'active_deactive', 'ajaxDisable', 142, '0', NULL, '2022-07-21 04:06:46', NULL),
(146, 'res_payment_method', 'manage_payment_method', 142, '0', NULL, '2022-07-21 04:06:46', NULL),
(147, 'delivery_method_management', 'delivery_method', 0, '1', 31, '2022-07-21 04:06:46', NULL),
(148, 'view', 'view', 147, '1', NULL, '2022-07-21 04:06:46', NULL),
(149, 'active_deactive', 'ajaxDisable', 147, '1', NULL, '2022-07-21 04:06:46', NULL),
(150, 'res_delivery_method', 'manage_delivery_method', 147, '1', NULL, '2022-07-21 04:06:46', NULL),
(151, 'faq_category_management', 'faq_category', 0, '0', 32, '2022-07-21 04:06:46', NULL),
(152, 'view', 'view', 151, '0', NULL, '2022-07-21 04:06:46', NULL),
(153, 'add', 'add', 151, '0', NULL, '2022-07-21 04:06:46', NULL),
(154, 'edit', 'edit', 151, '0', NULL, '2022-07-21 04:06:46', NULL),
(155, 'active_deactive', 'ajaxDisableAll', 151, '0', NULL, '2022-07-21 04:06:46', NULL),
(156, 'delete', 'ajaxDeleteAll', 151, '0', NULL, '2022-07-21 04:06:46', NULL),
(157, 'faq_questions_management', 'faqs', 0, '0', 33, '2022-07-21 04:06:46', NULL),
(158, 'view', 'view', 157, '0', NULL, '2022-07-21 04:06:46', NULL),
(159, 'add', 'add', 157, '0', NULL, '2022-07-21 04:06:46', NULL),
(160, 'edit', 'edit', 157, '0', NULL, '2022-07-21 04:06:46', NULL),
(161, 'active_deactive', 'ajaxDisableAll', 157, '0', NULL, '2022-07-21 04:06:46', NULL),
(162, 'delete', 'ajaxDeleteAll', 157, '0', NULL, '2022-07-21 04:06:46', NULL),
(163, 'user_log_management', 'user_log', 0, '0', 23, '2022-07-21 04:06:46', NULL),
(164, 'view', 'view', 163, '0', NULL, '2022-07-21 04:06:46', NULL),
(165, 'order_log_management', 'user_log', 0, '0', 24, '2022-07-28 13:28:46', NULL),
(166, 'view', 'order_log_view', 165, '0', NULL, '2022-07-28 13:28:46', NULL),
(167, 'contact_inquiries', 'contact_inquiries', 0, '0', 25, '2022-09-14 01:16:17', NULL),
(168, 'view', 'view', 167, '0', NULL, '2022-09-14 01:17:57', NULL),
(169, 'event_package_management', 'restaurant_package', 0, '0', 8, '2022-10-12 05:59:38', NULL),
(170, 'view', 'view_package', 169, '0', NULL, '2022-09-28 19:08:07', NULL),
(171, 'add', 'add_package', 169, '0', NULL, '2022-09-28 19:08:07', NULL),
(172, 'edit', 'edit_package', 169, '0', NULL, '2022-09-28 19:08:07', NULL),
(173, 'active_deactive', 'ajaxDisableAll', 169, '0', NULL, '2022-09-28 19:08:07', NULL),
(174, 'delete', 'ajaxDeleteAll', 169, '0', NULL, '2022-09-28 19:08:07', NULL),
(175, 'recipe_management', 'recipe', 0, '0', 10, '2022-10-12 06:03:18', NULL),
(176, 'view', 'view', 175, '0', NULL, '2022-09-28 19:08:07', NULL),
(177, 'add', 'add', 175, '0', NULL, '2022-09-28 19:08:07', NULL),
(178, 'edit', 'edit', 175, '0', NULL, '2022-09-28 19:08:07', NULL),
(179, 'active_deactive', 'ajaxDisableAll', 175, '0', NULL, '2022-09-28 19:08:07', NULL),
(180, 'delete', 'ajaxDeleteAll', 175, '0', NULL, '2022-09-28 19:08:07', NULL),
(181, 'table_management', 'table', 0, '1', 13, '2022-10-12 06:16:59', NULL),
(184, 'view', 'view', 181, '0', NULL, '2022-09-28 19:08:07', NULL),
(185, 'add', 'add', 181, '0', NULL, '2022-09-28 19:08:07', NULL),
(186, 'edit', 'edit', 181, '0', NULL, '2022-09-28 19:08:07', NULL),
(187, 'active_deactive', 'ajaxDisableAll', 181, '0', NULL, '2022-09-28 19:08:07', NULL),
(188, 'delete', 'ajaxDeleteAll', 181, '0', NULL, '2022-09-28 19:08:07', NULL),
(189, 'currentreservation_list', 'reservation_view', 181, '0', NULL, '2022-09-28 19:08:07', NULL),
(190, 'pastreservation_list', 'pastreservation_view', 181, '0', NULL, '2022-09-28 19:08:07', NULL),
(199, 'event_booking_management', 'event', 0, '0', 15, '2022-10-12 06:31:53', NULL),
(200, 'table_booking_management', 'book_table', 0, '0', 16, '2022-10-12 06:33:07', NULL),
(201, 'add_amount', 'addAmount', 199, '0', NULL, '2022-09-28 19:08:07', NULL),
(202, 'delete', 'ajaxDelete', 199, '0', NULL, '2022-09-28 19:08:07', NULL),
(203, 'export_report', 'generate_report', 199, '0', NULL, '2022-09-28 19:08:07', NULL),
(204, 'update_status', 'updateEventStatus', 199, '0', NULL, '2022-09-28 19:08:07', NULL),
(205, 'view', 'view', 199, '0', NULL, '2022-09-28 19:08:07', NULL),
(206, 'delete', 'ajaxDelete', 200, '0', NULL, '2022-09-28 19:08:07', NULL),
(207, 'update_status', 'updateTableStatus', 200, '0', NULL, '2022-09-28 19:08:07', NULL),
(208, 'view', 'view', 200, '0', NULL, '2022-09-28 19:08:07', NULL),
(209, 'restaurant_error_reports', 'restaurant_error_reports', 0, '0', 29, '2022-10-17 11:34:36', NULL),
(210, 'view', 'view', 209, '0', NULL, '2022-10-17 11:36:43', NULL),
(211, 'delete', 'ajaxdeleteReport', 209, '0', NULL, '2022-10-17 11:36:43', NULL),
(212, 'order_schedule_mode', 'order_schedule', 33, '0', NULL, '2022-10-21 06:22:05', NULL),
(213, 'export_order', 'export_order', 82, '0', NULL, '2022-11-08 11:52:31', NULL),
(214, 'download_qrcode', 'download_qrcode', 181, '0', NULL, '2022-11-08 12:23:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_access_rights`
--

CREATE TABLE `role_access_rights` (
  `role_access_rights_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `access_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='role-permission mapping';

--
-- Dumping data for table `role_access_rights`
--

INSERT INTO `role_access_rights` (`role_access_rights_id`, `role_id`, `access_id`) VALUES
(8424, 7, 13),
(8425, 7, 14),
(8426, 7, 15),
(8427, 7, 16),
(8428, 7, 17),
(8429, 7, 1),
(8430, 7, 2),
(8431, 7, 3),
(8432, 7, 4),
(8433, 7, 5),
(8434, 7, 6),
(8435, 7, 7),
(8436, 7, 8),
(8437, 7, 9),
(8438, 7, 10),
(8439, 7, 11),
(8440, 7, 12),
(8441, 7, 18),
(8442, 7, 19),
(8443, 7, 20),
(8444, 7, 21),
(8445, 7, 22),
(8446, 7, 23),
(8447, 7, 24),
(8448, 7, 25),
(8449, 7, 26),
(8450, 7, 33),
(8451, 7, 34),
(8452, 7, 35),
(8453, 7, 36),
(8454, 7, 37),
(8455, 7, 38),
(8456, 7, 39),
(8457, 7, 40),
(8458, 7, 212),
(8459, 7, 41),
(8460, 7, 42),
(8461, 7, 43),
(8462, 7, 44),
(8463, 7, 45),
(8464, 7, 46),
(8465, 7, 47),
(8466, 7, 48),
(8467, 7, 49),
(8468, 7, 50),
(8469, 7, 51),
(8470, 7, 52),
(8471, 7, 53),
(8472, 7, 54),
(8473, 7, 55),
(8474, 7, 56),
(8475, 7, 57),
(8476, 7, 58),
(8477, 7, 169),
(8478, 7, 170),
(8479, 7, 171),
(8480, 7, 172),
(8481, 7, 173),
(8482, 7, 174),
(8483, 7, 65),
(8484, 7, 66),
(8485, 7, 67),
(8486, 7, 68),
(8487, 7, 69),
(8488, 7, 70),
(8489, 7, 71),
(8490, 7, 72),
(8491, 7, 73),
(8492, 7, 175),
(8493, 7, 176),
(8494, 7, 177),
(8495, 7, 178),
(8496, 7, 179),
(8497, 7, 180),
(8498, 7, 74),
(8499, 7, 75),
(8500, 7, 76),
(8501, 7, 77),
(8502, 7, 78),
(8503, 7, 79),
(8504, 7, 80),
(8505, 7, 81),
(8506, 7, 181),
(8507, 7, 184),
(8508, 7, 185),
(8509, 7, 186),
(8510, 7, 187),
(8511, 7, 188),
(8512, 7, 189),
(8513, 7, 190),
(8514, 7, 214),
(8515, 7, 82),
(8516, 7, 83),
(8517, 7, 84),
(8518, 7, 85),
(8519, 7, 86),
(8520, 7, 87),
(8521, 7, 88),
(8522, 7, 89),
(8523, 7, 90),
(8524, 7, 91),
(8525, 7, 213),
(8526, 7, 199),
(8527, 7, 201),
(8528, 7, 202),
(8529, 7, 203),
(8530, 7, 204),
(8531, 7, 205),
(8532, 7, 200),
(8533, 7, 206),
(8534, 7, 207),
(8535, 7, 208),
(8536, 7, 98),
(8537, 7, 99),
(8538, 7, 100),
(8539, 7, 101),
(8540, 7, 102),
(8541, 7, 103),
(8542, 7, 104),
(8543, 7, 105),
(8544, 7, 106),
(8545, 7, 107),
(8546, 7, 108),
(8547, 7, 109),
(8548, 7, 110),
(8549, 7, 111),
(8550, 7, 112),
(8551, 7, 113),
(8552, 7, 114),
(8553, 7, 115),
(8554, 7, 116),
(8555, 7, 117),
(8556, 7, 118),
(8557, 7, 119),
(8558, 7, 120),
(8559, 7, 121),
(8560, 7, 122),
(8561, 7, 123),
(8562, 7, 124),
(8563, 7, 125),
(8564, 7, 126),
(8565, 7, 163),
(8566, 7, 164),
(8567, 7, 165),
(8568, 7, 166),
(8569, 7, 167),
(8570, 7, 168),
(8571, 7, 127),
(8572, 7, 128),
(8573, 7, 129),
(8574, 7, 130),
(8575, 7, 131),
(8576, 7, 132),
(8577, 7, 133),
(8578, 7, 134),
(8579, 7, 135),
(8580, 7, 136),
(8581, 7, 137),
(8582, 7, 138),
(8583, 7, 139),
(8584, 7, 140),
(8585, 7, 141),
(8586, 7, 209),
(8587, 7, 210),
(8588, 7, 211),
(8589, 7, 142),
(8590, 7, 143),
(8591, 7, 144),
(8592, 7, 145),
(8593, 7, 146),
(8594, 7, 151),
(8595, 7, 152),
(8596, 7, 153),
(8597, 7, 154),
(8598, 7, 155),
(8599, 7, 156),
(8600, 7, 157),
(8601, 7, 158),
(8602, 7, 159),
(8603, 7, 160),
(8604, 7, 161),
(8605, 7, 162),
(9775, 6, 13),
(9776, 6, 14),
(9777, 6, 15),
(9778, 6, 16),
(9779, 6, 17),
(9780, 6, 1),
(9781, 6, 2),
(9782, 6, 3),
(9783, 6, 4),
(9784, 6, 5),
(9785, 6, 6),
(9786, 6, 7),
(9787, 6, 8),
(9788, 6, 9),
(9789, 6, 10),
(9790, 6, 11),
(9791, 6, 12),
(9792, 6, 18),
(9793, 6, 19),
(9794, 6, 20),
(9795, 6, 21),
(9796, 6, 22),
(9797, 6, 23),
(9798, 6, 24),
(9799, 6, 25),
(9800, 6, 26),
(9801, 6, 33),
(9802, 6, 34),
(9803, 6, 35),
(9804, 6, 36),
(9805, 6, 37),
(9806, 6, 38),
(9807, 6, 39),
(9808, 6, 40),
(9809, 6, 212),
(9810, 6, 41),
(9811, 6, 42),
(9812, 6, 43),
(9813, 6, 44),
(9814, 6, 45),
(9815, 6, 46),
(9816, 6, 47),
(9817, 6, 48),
(9818, 6, 49),
(9819, 6, 50),
(9820, 6, 51),
(9821, 6, 52),
(9822, 6, 53),
(9823, 6, 54),
(9824, 6, 55),
(9825, 6, 56),
(9826, 6, 57),
(9827, 6, 58),
(9828, 6, 169),
(9829, 6, 170),
(9830, 6, 171),
(9831, 6, 172),
(9832, 6, 173),
(9833, 6, 174),
(9834, 6, 65),
(9835, 6, 66),
(9836, 6, 67),
(9837, 6, 68),
(9838, 6, 69),
(9839, 6, 70),
(9840, 6, 71),
(9841, 6, 72),
(9842, 6, 73),
(9843, 6, 175),
(9844, 6, 176),
(9845, 6, 177),
(9846, 6, 178),
(9847, 6, 179),
(9848, 6, 180),
(9849, 6, 74),
(9850, 6, 75),
(9851, 6, 76),
(9852, 6, 77),
(9853, 6, 78),
(9854, 6, 79),
(9855, 6, 80),
(9856, 6, 81),
(9857, 6, 181),
(9858, 6, 184),
(9859, 6, 185),
(9860, 6, 186),
(9861, 6, 187),
(9862, 6, 188),
(9863, 6, 189),
(9864, 6, 190),
(9865, 6, 214),
(9866, 6, 82),
(9867, 6, 83),
(9868, 6, 84),
(9869, 6, 85),
(9870, 6, 86),
(9871, 6, 87),
(9872, 6, 88),
(9873, 6, 89),
(9874, 6, 90),
(9875, 6, 91),
(9876, 6, 213),
(9877, 6, 199),
(9878, 6, 201),
(9879, 6, 202),
(9880, 6, 203),
(9881, 6, 204),
(9882, 6, 205),
(9883, 6, 200),
(9884, 6, 206),
(9885, 6, 207),
(9886, 6, 208),
(9887, 6, 98),
(9888, 6, 99),
(9889, 6, 100),
(9890, 6, 101),
(9891, 6, 102),
(9892, 6, 103),
(9893, 6, 104),
(9894, 6, 105),
(9895, 6, 106),
(9896, 6, 107),
(9897, 6, 108),
(9898, 6, 109),
(9899, 6, 110),
(9900, 6, 111),
(9901, 6, 112),
(9902, 6, 113),
(9903, 6, 114),
(9904, 6, 115),
(9905, 6, 116),
(9906, 6, 117),
(9907, 6, 118),
(9908, 6, 119),
(9909, 6, 120),
(9910, 6, 121),
(9911, 6, 122),
(9912, 6, 123),
(9913, 6, 124),
(9914, 6, 125),
(9915, 6, 126),
(9916, 6, 163),
(9917, 6, 164),
(9918, 6, 165),
(9919, 6, 166),
(9920, 6, 167),
(9921, 6, 168),
(9922, 6, 127),
(9923, 6, 128),
(9924, 6, 129),
(9925, 6, 130),
(9926, 6, 131),
(9927, 6, 132),
(9928, 6, 133),
(9929, 6, 134),
(9930, 6, 135),
(9931, 6, 136),
(9932, 6, 137),
(9933, 6, 138),
(9934, 6, 139),
(9935, 6, 140),
(9936, 6, 141),
(9937, 6, 209),
(9938, 6, 210),
(9939, 6, 211),
(9940, 6, 142),
(9941, 6, 143),
(9942, 6, 144),
(9943, 6, 145),
(9944, 6, 146),
(9945, 6, 151),
(9946, 6, 152),
(9947, 6, 153),
(9948, 6, 154),
(9949, 6, 155),
(9950, 6, 156),
(9951, 6, 157),
(9952, 6, 158),
(9953, 6, 159),
(9954, 6, 160),
(9955, 6, 161),
(9956, 6, 162),
(9957, 1, 13),
(9958, 1, 14),
(9959, 1, 15),
(9960, 1, 16),
(9961, 1, 17),
(9962, 1, 1),
(9963, 1, 2),
(9964, 1, 3),
(9965, 1, 4),
(9966, 1, 5),
(9967, 1, 6),
(9968, 1, 7),
(9969, 1, 8),
(9970, 1, 9),
(9971, 1, 10),
(9972, 1, 11),
(9973, 1, 12),
(9974, 1, 18),
(9975, 1, 19),
(9976, 1, 20),
(9977, 1, 21),
(9978, 1, 22),
(9979, 1, 23),
(9980, 1, 24),
(9981, 1, 25),
(9982, 1, 26),
(9983, 1, 33),
(9984, 1, 34),
(9985, 1, 35),
(9986, 1, 36),
(9987, 1, 37),
(9988, 1, 38),
(9989, 1, 39),
(9990, 1, 40),
(9991, 1, 212),
(9992, 1, 41),
(9993, 1, 42),
(9994, 1, 43),
(9995, 1, 44),
(9996, 1, 45),
(9997, 1, 46),
(9998, 1, 47),
(9999, 1, 48),
(10000, 1, 49),
(10001, 1, 50),
(10002, 1, 51),
(10003, 1, 52),
(10004, 1, 53),
(10005, 1, 54),
(10006, 1, 55),
(10007, 1, 56),
(10008, 1, 57),
(10009, 1, 58),
(10010, 1, 169),
(10011, 1, 170),
(10012, 1, 171),
(10013, 1, 172),
(10014, 1, 173),
(10015, 1, 174),
(10016, 1, 65),
(10017, 1, 66),
(10018, 1, 67),
(10019, 1, 68),
(10020, 1, 69),
(10021, 1, 70),
(10022, 1, 71),
(10023, 1, 72),
(10024, 1, 73),
(10025, 1, 175),
(10026, 1, 176),
(10027, 1, 177),
(10028, 1, 178),
(10029, 1, 179),
(10030, 1, 180),
(10031, 1, 74),
(10032, 1, 75),
(10033, 1, 76),
(10034, 1, 77),
(10035, 1, 78),
(10036, 1, 79),
(10037, 1, 80),
(10038, 1, 81),
(10039, 1, 181),
(10040, 1, 184),
(10041, 1, 185),
(10042, 1, 186),
(10043, 1, 187),
(10044, 1, 188),
(10045, 1, 189),
(10046, 1, 190),
(10047, 1, 214),
(10048, 1, 82),
(10049, 1, 83),
(10050, 1, 84),
(10051, 1, 85),
(10052, 1, 86),
(10053, 1, 87),
(10054, 1, 88),
(10055, 1, 89),
(10056, 1, 90),
(10057, 1, 91),
(10058, 1, 213),
(10059, 1, 199),
(10060, 1, 201),
(10061, 1, 202),
(10062, 1, 203),
(10063, 1, 204),
(10064, 1, 205),
(10065, 1, 200),
(10066, 1, 206),
(10067, 1, 207),
(10068, 1, 208),
(10069, 1, 98),
(10070, 1, 99),
(10071, 1, 100),
(10072, 1, 101),
(10073, 1, 102),
(10074, 1, 103),
(10075, 1, 104),
(10076, 1, 105),
(10077, 1, 106),
(10078, 1, 107),
(10079, 1, 108),
(10080, 1, 109),
(10081, 1, 110),
(10082, 1, 111),
(10083, 1, 112),
(10084, 1, 113),
(10085, 1, 114),
(10086, 1, 115),
(10087, 1, 116),
(10088, 1, 117),
(10089, 1, 118),
(10090, 1, 119),
(10091, 1, 120),
(10092, 1, 121),
(10093, 1, 122),
(10094, 1, 123),
(10095, 1, 124),
(10096, 1, 125),
(10097, 1, 126),
(10098, 1, 163),
(10099, 1, 164),
(10100, 1, 165),
(10101, 1, 166),
(10102, 1, 167),
(10103, 1, 168),
(10104, 1, 127),
(10105, 1, 128),
(10106, 1, 129),
(10107, 1, 130),
(10108, 1, 131),
(10109, 1, 132),
(10110, 1, 133),
(10111, 1, 134),
(10112, 1, 135),
(10113, 1, 136),
(10114, 1, 137),
(10115, 1, 138),
(10116, 1, 139),
(10117, 1, 140),
(10118, 1, 141),
(10119, 1, 209),
(10120, 1, 210),
(10121, 1, 211),
(10122, 1, 142),
(10123, 1, 143),
(10124, 1, 144),
(10125, 1, 145),
(10126, 1, 146),
(10127, 1, 151),
(10128, 1, 152),
(10129, 1, 153),
(10130, 1, 154),
(10131, 1, 155),
(10132, 1, 156),
(10133, 1, 157),
(10134, 1, 158),
(10135, 1, 159),
(10136, 1, 160),
(10137, 1, 161),
(10138, 1, 162),
(10139, 2, 14),
(10140, 2, 15),
(10141, 2, 16),
(10142, 2, 17),
(10143, 2, 13),
(10144, 2, 34),
(10145, 2, 35),
(10146, 2, 36),
(10147, 2, 37),
(10148, 2, 38),
(10149, 2, 39),
(10150, 2, 40),
(10151, 2, 212),
(10152, 2, 33),
(10153, 2, 48),
(10154, 2, 49),
(10155, 2, 50),
(10156, 2, 51),
(10157, 2, 52),
(10158, 2, 47),
(10159, 2, 54),
(10160, 2, 55),
(10161, 2, 56),
(10162, 2, 57),
(10163, 2, 58),
(10164, 2, 53),
(10165, 2, 66),
(10166, 2, 67),
(10167, 2, 68),
(10168, 2, 69),
(10169, 2, 70),
(10170, 2, 71),
(10171, 2, 72),
(10172, 2, 73),
(10173, 2, 65),
(10174, 2, 75),
(10175, 2, 83),
(10176, 2, 84),
(10177, 2, 85),
(10178, 2, 86),
(10179, 2, 87),
(10180, 2, 88),
(10181, 2, 89),
(10182, 2, 90),
(10183, 2, 91),
(10184, 2, 213),
(10185, 2, 82),
(10186, 3, 34),
(10187, 3, 35),
(10188, 3, 36),
(10189, 3, 37),
(10190, 3, 38),
(10191, 3, 39),
(10192, 3, 40),
(10193, 3, 212),
(10194, 3, 33),
(10195, 3, 48),
(10196, 3, 49),
(10197, 3, 50),
(10198, 3, 51),
(10199, 3, 52),
(10200, 3, 47),
(10201, 3, 54),
(10202, 3, 55),
(10203, 3, 56),
(10204, 3, 57),
(10205, 3, 58),
(10206, 3, 53),
(10207, 3, 66),
(10208, 3, 67),
(10209, 3, 68),
(10210, 3, 69),
(10211, 3, 70),
(10212, 3, 71),
(10213, 3, 72),
(10214, 3, 73),
(10215, 3, 65),
(10216, 3, 83),
(10217, 3, 84),
(10218, 3, 85),
(10219, 3, 86),
(10220, 3, 87),
(10221, 3, 88),
(10222, 3, 89),
(10223, 3, 90),
(10224, 3, 91),
(10225, 3, 213),
(10226, 3, 82);

-- --------------------------------------------------------

--
-- Table structure for table `role_master`
--

CREATE TABLE `role_master` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 => Inactive 1 => Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci COMMENT='roles for admin panel access';

--
-- Dumping data for table `role_master`
--

INSERT INTO `role_master` (`role_id`, `role_name`, `status`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 'Master Admin', 1, '2022-07-21 04:04:34', 0, '2022-12-22 03:09:01', 1, NULL),
(2, 'Restaurant Admin', 1, '2022-07-21 04:04:34', 0, '2022-12-22 03:09:07', 1, NULL),
(3, 'Branch Admin', 1, '2022-07-21 04:04:34', 0, '2022-12-22 03:09:15', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `slider_image`
--

CREATE TABLE `slider_image` (
  `entity_id` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `slider_image`
--

INSERT INTO `slider_image` (`entity_id`, `image`, `status`) VALUES
(77, 'slider-images/3a9b8b7d54da516afa8f8e9378e6f2bc.jpg', 1),
(78, 'slider-images/6223c489a39450b6f955107c358d874f.jpg', 1),
(79, 'slider-images/237a5481ed4c91ccc9819213e15aa00b.jpg', 1),
(80, 'slider-images/0fe74709bcf21be76c2227cb2c79f617.jpg', 1),
(81, 'slider-images/2b681059aa428b3f3c88f74cbcecde63.jpg', 1),
(82, 'slider-images/8f85168f1ec2338a415c4ee822a6d800.jpg', 1),
(83, 'slider-images/b43018c5a833262b3a3715d344b18271.jpg', 1),
(84, 'slider-images/8f12900b94f76ef9f89dfee3467ca831.jpg', 1),
(86, 'slider-images/ef29e55abb8962d537a00837b1ba850b.jpeg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stripe_callback_details`
--

CREATE TABLE `stripe_callback_details` (
  `entity_id` int(11) NOT NULL,
  `response_id` varchar(255) DEFAULT NULL COMMENT 'payment intent id or payment method id or customer id',
  `event_slug` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `stripe_resp_obj` longtext CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `full_response` longtext DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_option`
--

CREATE TABLE `system_option` (
  `SystemOptionID` int(11) NOT NULL,
  `OptionName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `OptionSlug` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `OptionValue` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FieldType` enum('text','radio','dropdown','checkbox','toggle','textarea','file') NOT NULL,
  `GroupID` int(11) NOT NULL,
  `Description` text NOT NULL,
  `IsHidden` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `CreadedDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdatedBy` int(11) DEFAULT NULL,
  `UpdatedDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `system_option`
--

INSERT INTO `system_option` (`SystemOptionID`, `OptionName`, `OptionSlug`, `OptionValue`, `FieldType`, `GroupID`, `Description`, `IsHidden`, `sort_order`, `CreatedBy`, `CreadedDate`, `UpdatedBy`, `UpdatedDate`) VALUES
(1, 'so_admin_email_address', 'Admin_Email_Address', 'info@hausdesdoeners.com', 'text', 1, 'sod_admin_email_address', 0, 1, 1, '2018-06-20 06:52:20', NULL, '2023-01-31 05:12:08'),
(7, 'so_email_from_name', 'Email_From_Name', 'Noreply', 'text', 1, 'sod_email_from_name', 0, 2, 1, '2016-07-18 15:24:16', NULL, '2023-01-31 05:12:08'),
(8, 'so_from_email_address', 'From_Email_Address', 'support@hausdesdoeners.com', 'text', 1, 'sod_from_email_address', 0, 3, 1, '2016-07-18 15:24:44', NULL, '2023-01-31 05:12:08'),
(9, 'so_driver_commission_less', 'driver_commission_less', '5', 'text', 3, 'sod_driver_commission_less', 0, 12, 1, '2019-08-12 06:59:20', NULL, '2023-01-31 05:12:08'),
(10, 'so_driver_commission_more', 'driver_commission_more', '50', 'text', 3, 'sod_driver_commission_more', 0, 13, 1, '2019-08-12 06:59:47', NULL, '2023-01-31 05:12:08'),
(11, 'so_enable_commission_of_driver', 'enable_commission_of_driver', '1', 'toggle', 3, 'sod_enable_commission_of_driver', 0, 11, 1, '2021-07-13 04:11:15', NULL, '2023-01-31 05:12:08'),
(14, 'so_facebook_url', 'facebook', 'https://www.facebook.com/hausdesdoeners/', 'text', 2, 'sod_facebook_url', 0, 10, 1, '2020-05-20 05:15:14', NULL, '2023-01-31 05:12:08'),
(15, 'so_twitter_url', 'twitter', 'https://twitter.com/hausdesdoeners/', 'text', 2, 'sod_twitter_url', 0, 11, 1, '2020-05-20 05:15:14', NULL, '2023-01-31 05:12:08'),
(16, 'so_linkedin_url', 'linkedin', 'https://www.linkedin.com/showcase/hausdesdoeners/', 'text', 2, 'sod_linkedin_url', 0, 12, 1, '2020-05-20 05:15:56', NULL, '2023-01-31 05:12:08'),
(17, 'so_minimum_range', 'minimum_range', '0', 'text', 3, 'sod_minimum_range', 1, 8, 1, '2020-10-21 14:42:37', NULL, '2021-07-27 12:11:19'),
(18, 'so_maximum_range', 'maximum_range', '5000000', 'text', 3, 'sod_maximum_range', 0, 9, 1, '2020-10-21 14:42:37', NULL, '2023-01-31 05:12:08'),
(19, 'so_gradient_dark_color', 'gradient_dark_color', '#17161a', 'text', 6, 'sod_gradient_dark_color', 0, 25, 1, '2020-10-22 07:42:27', NULL, '2023-01-31 05:12:08'),
(20, 'so_gradient_light_color', 'gradient_light_color', '#999999', 'text', 6, 'sod_gradient_light_color', 0, 26, 1, '2020-10-22 07:42:57', NULL, '2023-01-31 05:12:08'),
(21, 'so_app_store_url', 'app_store_url', 'https://apple.co/3wd6KhW', 'text', 2, 'sod_app_store_url', 0, 4, 1, '2020-10-28 07:03:23', NULL, '2023-01-31 05:12:08'),
(22, 'so_playstore_url', 'playstore_url', 'https://cutt.ly/ZnTMrZn', 'text', 2, 'sod_playstore_url', 0, 5, 1, '2020-10-28 07:03:23', NULL, '2023-01-31 05:12:08'),
(23, 'so_google_key', 'google_key', 'Enter Google Maps API key', 'text', 5, 'sod_google_key', 0, 27, 1, '2020-06-02 02:05:54', NULL, '2023-01-31 05:12:08'),
(24, 'so_minimum_subtotal', 'minimum_subtotal', '3', 'text', 4, 'sod_minimum_subtotal', 0, 16, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(25, 'so_min_redeem_point', 'min_redeem_point', '2', 'text', 4, 'sod_min_redeem_point', 0, 17, 1, '2021-03-03 03:14:46', NULL, '2023-01-31 05:12:08'),
(27, 'so_earning_1_point', 'earning_1_point', '10', 'text', 4, 'sod_earning_1_point', 0, 18, 1, '2021-03-03 03:16:41', NULL, '2023-01-31 05:12:08'),
(28, 'so_referral_amount', 'referral_amount', '5', 'text', 4, 'sod_referral_amount', 0, 19, 1, '2021-04-29 00:15:49', NULL, '2023-01-31 05:12:08'),
(29, 'so_default_currency', 'currency', '231', 'dropdown', 5, 'sod_default_currency', 0, 21, 1, '2021-03-09 04:35:27', NULL, '2023-01-31 05:12:08'),
(30, 'so_default_country', 'country', 'INDIA', 'dropdown', 5, 'sod_default_country', 1, 22, NULL, '2021-04-26 02:26:51', NULL, '2022-11-02 06:51:29'),
(31, 'so_phone_code', 'phone_code', '+91', 'text', 5, 'sod_phone_code', 1, 23, NULL, '2021-04-26 02:27:24', NULL, '2022-11-02 06:51:29'),
(32, 'so_enable_review', 'enable_review', '1', 'radio', 5, 'sod_enable_review', 0, 24, 1, '2021-05-12 03:10:20', NULL, '2023-01-31 05:12:08'),
(33, 'so_min_order_amount', 'min_order_amount', '100', 'text', 3, 'sod_min_order_amount', 0, 14, 1, '2021-05-17 05:57:46', NULL, '2023-01-31 05:12:08'),
(42, 'so_user_near_km', 'USER_NEAR_KM', '100000', 'text', 3, 'sod_user_near_km', 1, 15, NULL, '2021-06-04 07:14:28', NULL, '2023-01-31 05:12:08'),
(47, 'so_website_header_script', 'website_header_script', '', 'textarea', 7, 'sod_website_header_script', 0, 30, NULL, '2021-06-04 07:14:28', NULL, '2023-01-31 05:12:08'),
(48, 'so_website_body_script', 'website_body_script', '', 'textarea', 7, 'sod_website_body_script', 0, 31, NULL, '2021-06-04 07:14:28', NULL, '2023-01-31 05:12:08'),
(49, 'so_website_footer_script', 'website_footer_script', '', 'textarea', 7, 'sod_website_footer_script', 0, 32, NULL, '2021-06-04 07:14:28', NULL, '2023-01-31 05:12:08'),
(50, 'so_customer_app_android_live_version', 'customer_app_android_live_version', '1', 'text', 8, 'sod_customer_app_android_live_version', 0, 33, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(51, 'so_customer_app_android_force_version', 'customer_app_android_force_version', '1', 'text', 8, 'sod_customer_app_android_force_version', 0, 34, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(52, 'so_customer_app_ios_live_version', 'customer_app_ios_live_version', '1', 'text', 8, 'sod_customer_app_ios_live_version', 0, 35, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(53, 'so_customer_app_ios_force_version', 'customer_app_ios_force_version', '0', 'text', 8, 'sod_customer_app_ios_force_version', 0, 36, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(55, 'so_driver_app_android_live_version', 'driver_app_android_live_version', '1', 'text', 8, 'sod_driver_app_android_live_version', 0, 37, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(56, 'so_driver_app_android_force_version', 'driver_app_android_force_version', '1', 'text', 8, 'sod_driver_app_android_force_version', 0, 38, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(57, 'so_driver_app_ios_live_version', 'driver_app_ios_live_version', '1', 'text', 8, 'sod_driver_app_ios_live_version', 0, 39, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(58, 'so_driver_app_ios_force_version', 'driver_app_ios_force_version', '0', 'text', 8, 'sod_driver_app_ios_force_version', 0, 40, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(59, 'so_admin_app_android_live_version', 'admin_app_android_live_version', '1', 'text', 8, 'sod_admin_app_android_live_version', 0, 41, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(60, 'so_admin_app_android_force_version', 'admin_app_android_force_version', '1', 'text', 8, 'sod_admin_app_android_force_version', 0, 42, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(61, 'so_admin_app_ios_live_version', 'admin_app_ios_live_version', '1', 'text', 8, 'sod_admin_app_ios_live_version', 0, 43, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(62, 'so_admin_app_ios_force_version', 'admin_app_ios_force_version', '1', 'text', 8, 'sod_admin_app_ios_force_version', 0, 44, 1, '2021-03-03 03:11:37', NULL, '2023-01-31 05:12:08'),
(63, 'so_driver_app_store_url', 'driver_app_store_url', 'https://apps.apple.com/us/app/hausdesdoeners', 'text', 2, 'sod_driver_app_store_url', 0, 6, 1, '2020-10-28 07:03:23', NULL, '2023-01-31 05:12:08'),
(64, 'so_driver_playstore_url', 'driver_playstore_url', 'https://play.google.com/store/apps/details?id=com.hausdesdoeners', 'text', 2, 'sod_driver_playstore_url', 0, 7, 1, '2020-10-28 07:03:23', NULL, '2023-01-31 05:12:08'),
(65, 'so_admin_app_store_url', 'admin_app_store_url', 'https://apps.apple.com/us/app/hausdesdoeners', 'text', 2, 'sod_admin_app_store_url', 0, 8, 1, '2020-10-28 07:03:23', NULL, '2023-01-31 05:12:08'),
(66, 'so_admin_playstore_url', 'admin_playstore_url', 'https://play.google.com/store/apps/details?id=com.hausdesdoeners.admin', 'text', 2, 'sod_admin_playstore_url', 0, 9, 1, '2020-10-28 07:03:23', NULL, '2023-01-31 05:12:08'),
(67, 'so_driver_tip_amount', 'driver_tip_amount', '5\r\n6\r\n7\r\n', 'text', 9, 'sod_driver_tip_amount', 0, 45, 1, '2021-08-31 05:33:59', NULL, '2023-01-31 05:12:08'),
(68, 'so_language_file_mobile_app', 'language_file_mobile_app', 'uploads/language_import/c924767bcb795e5f55f658d341ca5fa8.xlsx', 'file', 10, 'sod_language_file_mobile_app', 0, 46, 1, '2021-09-07 01:10:17', NULL, '2023-01-30 08:30:42'),
(70, 'so_cancel_order_timer', 'cancel_order_timer', '60', 'text', 5, 'sod_cancel_order_timer', 0, 25, NULL, '2021-10-12 00:36:03', NULL, '2023-01-31 05:12:08'),
(71, 'so_automated_call_timer', 'automated_call_timer', '3', 'text', 5, 'sod_automated_call_timer', 0, 25, NULL, '2021-10-12 04:36:03', NULL, '2023-01-31 05:12:08'),
(72, 'so_contactus_phone', 'contactus_phone', '+1 (123) 456-7890', 'text', 5, 'sod_contactus_phone', 0, 27, NULL, '2021-04-26 11:57:24', NULL, '2023-01-31 05:12:08'),
(73, 'so_default_driver_tip', 'default_driver_tip', '6', 'text', 9, 'sod_default_driver_tip', 1, 47, 1, '2022-05-12 23:58:03', NULL, '2023-01-31 05:12:08'),
(74, 'so_auto_cancel_order_timer', 'auto_cancel_order_timer', '6', 'text', 5, 'sod_auto_cancel_order_timer', 0, 48, NULL, '2021-10-12 06:06:03', NULL, '2023-01-31 05:12:08'),
(75, 'so_delayed_order_timer', 'delayed_order_timer', '7', 'text', 5, 'sod_delayed_order_timer', 0, 49, NULL, '2021-10-12 06:06:03', NULL, '2023-01-31 05:12:08'),
(76, 'so_time_interval_for_scheduling', 'time_interval_for_scheduling', '15', 'text', 5, 'sod_time_interval_for_scheduling', 0, 50, 1, '2022-06-15 07:07:00', NULL, '2023-01-31 05:12:08'),
(77, 'so_maximum_range_pickup', 'maximum_range_pickup', '5000000', 'text', 3, 'sod_maximum_range_pickup', 0, 10, 1, '2020-10-28 20:14:44', NULL, '2023-01-31 05:12:08'),
(78, 'so_instagram_url', 'instagram', '#', 'text', 2, 'sod_instagram_url', 0, 13, 1, '2020-05-20 10:45:56', NULL, '2023-01-31 05:12:08'),
(79, 'so_schedule_verybusy_end', 'schedule_verybusy_end', '25', 'text', 11, 'sod_schedule_verybusy_end', 0, 38, 1, '2021-03-02 21:41:37', NULL, '2023-01-31 05:12:08'),
(80, 'so_schedule_verybusy_start', 'schedule_verybusy_start', '15', 'text', 11, 'sod_schedule_verybusy_start', 0, 37, 1, '2021-03-02 21:41:37', NULL, '2023-01-31 05:12:08'),
(81, 'so_schedule_busy_end', 'schedule_busy_end', '15', 'text', 11, 'sod_schedule_busy_end', 0, 36, 1, '2021-03-02 21:41:37', NULL, '2023-01-31 05:12:08'),
(82, 'so_schedule_busy_start', 'schedule_busy_start', '10', 'text', 11, 'sod_schedule_busy_start', 0, 35, 1, '2021-03-02 21:41:37', NULL, '2023-01-31 05:12:08'),
(83, 'so_schedule_normal_end', 'schedule_normal_end', '10', 'text', 11, 'sod_schedule_normal_end', 0, 34, 1, '2021-03-02 21:41:37', NULL, '2023-01-31 05:12:08'),
(84, 'so_schedule_normal_start', 'schedule_normal_start', '5', 'text', 11, 'sod_schedule_normal_start', 0, 33, 1, '2021-03-02 21:41:37', NULL, '2023-01-31 05:12:08'),
(85, 'so_distance_in', 'distance_in', '0', 'radio', 5, 'sod_distance_in', 0, 26, 1, '2022-10-11 12:26:33', NULL, '2023-01-31 05:12:08'),
(86, 'so_default_language', 'default_language', 'en', 'dropdown', 5, 'sod_default_language', 0, 27, NULL, '2022-10-11 12:32:42', NULL, '2023-01-31 05:12:08'),
(87, 'so_user_verification_type', 'user_verification_type', 'mobile', 'radio', 12, 'sod_user_verification_type', 1, 1, 1, '2022-10-11 13:24:01', NULL, '2022-10-11 01:34:01'),
(88, 'so_language_file_website', 'language_file_website', 'uploads/language_import/33b8d05edf8c41ca07fde6a4637f33eb.xlsx', 'file', 10, 'sod_language_file_website', 0, 46, 1, '2022-10-11 13:37:55', NULL, '2022-12-21 12:32:52'),
(89, 'so_google_webclient_id', 'google_webclient_id', 'Enter Google Web Client Id', 'text', 5, 'sod_google_webclient_id', 0, 26, 1, '2020-06-02 07:35:54', NULL, '2023-01-31 05:12:08');

-- --------------------------------------------------------

--
-- Table structure for table `system_option_group`
--

CREATE TABLE `system_option_group` (
  `GroupID` int(11) NOT NULL,
  `GroupName` varchar(150) NOT NULL COMMENT 'store language variable',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_option_group`
--

INSERT INTO `system_option_group` (`GroupID`, `GroupName`, `status`, `sort_order`) VALUES
(1, 'sg_email_configurations', 1, 1),
(2, 'sg_social_media_configurations', 1, 2),
(3, 'sg_delivery_location_onfigurations', 1, 3),
(4, 'sg_wallet_configurations', 1, 4),
(5, 'sg_social_admin_configurations', 1, 5),
(6, 'sg_color_configurations', 1, 6),
(7, 'sg_website_configurations', 1, 7),
(8, 'sg_live_app_version_configurations', 1, 8),
(9, 'sg_driver_tip_configurations', 1, 9),
(10, 'sg_language_file_configurations', 1, 10),
(11, 'sg_schedule_mode_configurations', 1, 12),
(12, 'sg_user_verification_configurations', 1, 11);

-- --------------------------------------------------------

--
-- Table structure for table `table_booking`
--

CREATE TABLE `table_booking` (
  `entity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_content_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `no_of_people` int(11) NOT NULL,
  `booking_date` date DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `amount` decimal(20,2) DEFAULT NULL,
  `booking_status` enum('awaiting','confirmed','cancelled') NOT NULL,
  `payment_status` enum('pending','paid','cancel') DEFAULT NULL,
  `cancel_reason` varchar(255) DEFAULT NULL,
  `additional_request` varchar(255) DEFAULT NULL COMMENT 'comment box',
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `table_booking`
--

INSERT INTO `table_booking` (`entity_id`, `user_id`, `restaurant_content_id`, `user_name`, `no_of_people`, `booking_date`, `start_time`, `end_time`, `amount`, `booking_status`, `payment_status`, `cancel_reason`, `additional_request`, `updated_by`, `updated_date`, `created_by`, `created_date`) VALUES
(2, 1102, 589, 'Fabian Leon', 2, '2022-08-14', '18:35:00', '02:00:00', NULL, 'awaiting', NULL, NULL, NULL, NULL, NULL, 1102, '2022-08-12 03:17:50'),
(3, 1103, 1534, 'Emma E', 5, '2022-08-13', '04:05:00', '13:55:00', NULL, 'confirmed', NULL, NULL, NULL, NULL, NULL, 1103, '2022-08-12 13:25:45'),
(5, 1101, 1534, 'Sophia Emma', 5, '2022-09-07', '04:30:00', '13:55:00', NULL, 'confirmed', NULL, NULL, NULL, NULL, NULL, 1101, '2022-09-01 06:54:12'),
(8, 1099, 1534, 'Haus des Döners Customer', 5, '2022-10-25', '06:35:00', '08:25:00', NULL, 'awaiting', NULL, NULL, NULL, NULL, NULL, 1099, '2022-10-24 23:54:56'),
(10, 1170, 1534, 'Sophia John', 5, '2022-11-28', '18:35:00', '18:25:00', NULL, 'awaiting', NULL, NULL, NULL, NULL, NULL, 1170, '2022-11-25 12:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `table_booking_notification`
--

CREATE TABLE `table_booking_notification` (
  `notification_id` int(11) NOT NULL,
  `last_tablebooking_id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `tablebooking_count` int(11) NOT NULL,
  `view_status` tinyint(4) NOT NULL,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `table_booking_notification`
--

INSERT INTO `table_booking_notification` (`notification_id`, `last_tablebooking_id`, `admin_id`, `tablebooking_count`, `view_status`, `date`) VALUES
(1, 10, 1, 0, 1, '2022-11-25'),
(2, 9, 1095, 0, 1, '2022-10-28');

-- --------------------------------------------------------

--
-- Table structure for table `table_master`
--

CREATE TABLE `table_master` (
  `entity_id` int(11) NOT NULL,
  `table_number` varchar(100) NOT NULL,
  `resto_entity_id` int(11) NOT NULL,
  `capacity` varchar(100) NOT NULL,
  `content_id` int(11) NOT NULL,
  `language_slug` varchar(5) NOT NULL,
  `qr_code` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1- active',
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `table_master`
--

INSERT INTO `table_master` (`entity_id`, `table_number`, `resto_entity_id`, `capacity`, `content_id`, `language_slug`, `qr_code`, `status`, `created_by`, `created_date`, `updated_at`) VALUES
(11, '15', 1799, '12', 2209, 'en', 'table/Autoservire-15.png', 1, NULL, '2022-12-21 14:03:25', '0000-00-00 00:00:00'),
(12, '15', 1534, '25', 2291, 'en', 'table/Las-Palmas-15.png', 1, NULL, '2022-12-22 07:35:28', '0000-00-00 00:00:00'),
(13, '21', 1996, '2', 2292, 'en', 'table/Pizza-Eforie-North-21.png', 1, NULL, '2022-12-22 07:35:38', '0000-00-00 00:00:00'),
(14, '2', 1996, '25', 2293, 'en', 'table/Pizza-Eforie-North-2.png', 1, NULL, '2022-12-22 07:35:45', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `table_status`
--

CREATE TABLE `table_status` (
  `entity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `table_master_id` int(11) NOT NULL,
  `resto_entity_id` int(11) NOT NULL,
  `status` enum('reject','approve') NOT NULL DEFAULT 'approve',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `table_status`
--

INSERT INTO `table_status` (`entity_id`, `user_id`, `content_id`, `table_master_id`, `resto_entity_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1099, 0, 4, 0, 'approve', '2022-08-23 12:07:14', NULL),
(2, 1099, 0, 3, 0, 'approve', '2022-08-25 14:24:04', NULL),
(3, 1099, 0, 5, 0, 'approve', '2022-08-25 14:27:49', NULL),
(4, 1099, 0, 1, 0, 'approve', '2022-09-26 14:00:34', NULL),
(5, 23, 0, 1, 0, 'approve', '2022-09-26 14:01:29', NULL),
(6, 23, 0, 1, 0, 'approve', '2022-09-26 14:02:13', NULL),
(7, 23, 0, 1, 0, 'approve', '2022-09-26 14:02:32', NULL),
(8, 1102, 0, 1, 0, 'approve', '2022-09-26 14:03:27', NULL),
(9, 1102, 0, 1, 0, 'approve', '2022-09-26 14:03:45', NULL),
(10, 23, 0, 1, 0, 'approve', '2022-09-26 14:05:55', NULL),
(11, 1099, 0, 1, 0, 'approve', '2022-09-26 14:06:51', NULL),
(12, 1099, 0, 1, 0, 'approve', '2022-09-26 14:14:15', NULL),
(13, 1102, 0, 1, 0, 'approve', '2022-09-26 21:21:42', NULL),
(14, 1102, 0, 1, 0, 'approve', '2022-09-26 21:38:49', NULL),
(15, 1102, 0, 2, 0, 'approve', '2022-09-26 21:45:31', NULL),
(16, 1102, 0, 1, 0, 'approve', '2022-09-26 21:46:14', NULL),
(17, 1102, 0, 1, 0, 'approve', '2022-09-26 21:49:38', NULL),
(18, 1102, 0, 1, 0, 'approve', '2022-09-26 21:50:28', NULL),
(19, 1102, 0, 1, 0, 'approve', '2022-09-27 07:34:07', NULL),
(20, 1102, 0, 1, 0, 'approve', '2022-09-27 07:34:43', NULL),
(21, 1102, 0, 1, 0, 'approve', '2022-09-27 07:35:25', NULL),
(22, 1102, 0, 1, 0, 'approve', '2022-09-27 07:36:40', NULL),
(23, 1102, 0, 1, 0, 'approve', '2022-09-27 07:37:09', NULL),
(24, 1102, 0, 1, 0, 'approve', '2022-09-27 07:38:00', NULL),
(25, 1102, 0, 1, 0, 'approve', '2022-09-27 07:41:56', NULL),
(26, 314, 0, 1, 0, 'approve', '2022-09-27 08:29:06', NULL),
(27, 1102, 0, 1, 0, 'approve', '2022-09-27 08:51:19', NULL),
(28, 1138, 0, 6, 0, 'approve', '2022-09-27 09:09:12', NULL),
(29, 1138, 0, 1, 0, 'approve', '2022-09-27 09:22:40', NULL),
(30, 1138, 0, 6, 0, 'approve', '2022-09-27 09:26:52', NULL),
(31, 314, 0, 7, 0, 'approve', '2022-09-27 09:33:52', NULL),
(32, 314, 0, 7, 0, 'approve', '2022-09-27 09:38:36', NULL),
(33, 1099, 0, 8, 0, 'approve', '2022-09-27 09:50:58', NULL),
(34, 314, 0, 9, 0, 'approve', '2022-09-27 09:54:22', NULL),
(35, 1099, 0, 8, 0, 'approve', '2022-09-27 10:00:41', NULL),
(36, 314, 0, 2, 0, 'approve', '2022-09-27 12:06:23', NULL),
(37, 1138, 0, 2, 0, 'approve', '2022-10-04 06:11:54', NULL),
(38, 1138, 0, 2, 0, 'approve', '2022-10-04 06:13:09', NULL),
(39, 1138, 0, 2, 0, 'approve', '2022-10-07 05:24:31', NULL),
(40, 1138, 0, 2, 0, 'approve', '2022-10-09 23:41:18', NULL),
(41, 1138, 0, 2, 0, 'approve', '2022-10-10 22:48:25', NULL),
(42, 1138, 0, 2, 0, 'approve', '2022-10-16 03:37:00', NULL),
(43, 1138, 0, 2, 0, 'approve', '2022-10-19 15:05:18', NULL),
(44, 1121, 0, 8, 0, 'approve', '2022-10-20 12:47:47', NULL),
(45, 1099, 0, 10, 0, 'approve', '2022-11-02 05:14:42', NULL),
(46, 1099, 0, 2, 0, 'approve', '2022-11-07 04:59:32', NULL),
(47, 1099, 0, 2, 0, 'approve', '2022-11-07 05:00:46', NULL),
(48, 1170, 0, 10, 0, 'approve', '2022-11-25 12:50:17', NULL),
(49, 23, 0, 11, 0, 'approve', '2022-12-22 06:48:45', NULL),
(50, 23, 0, 11, 0, 'approve', '2022-12-23 07:17:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tips`
--

CREATE TABLE `tips` (
  `entity_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `tips_transaction_id` varchar(100) DEFAULT NULL,
  `tip_percentage` float(10,2) DEFAULT NULL COMMENT 'selected percent value',
  `amount` float(10,2) NOT NULL COMMENT 'deducted tip amount',
  `refunded_amount` float(10,2) DEFAULT NULL COMMENT 'in cents',
  `stripe_refund_id` varchar(100) DEFAULT NULL,
  `paypal_refund_id` varchar(100) DEFAULT NULL,
  `payment_option` varchar(100) DEFAULT NULL,
  `refund_reason` varchar(255) DEFAULT NULL,
  `refund_status` varchar(50) DEFAULT NULL,
  `date` datetime NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `entity_id` int(11) NOT NULL,
  `parent_user_id` int(11) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `stripe_customer_id` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `phone_code` varchar(20) DEFAULT NULL,
  `mobile_number` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(320) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_type` varchar(255) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `login_type` enum('normal','facebook','google') DEFAULT NULL,
  `social_media_id` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1- active',
  `availability_status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1: online, 0: offline ',
  `active` tinyint(4) DEFAULT NULL COMMENT '1- active',
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0-not deleted  1-deleted',
  `image` varchar(255) DEFAULT NULL,
  `earning_points` decimal(20,2) DEFAULT NULL,
  `email_verification_code` varchar(255) DEFAULT NULL,
  `user_otp` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `notification` tinyint(4) DEFAULT 1,
  `notification_sound` tinyint(4) DEFAULT 1,
  `device_id` text DEFAULT NULL,
  `language_slug` varchar(10) DEFAULT NULL,
  `driver_temperature` varchar(255) DEFAULT NULL,
  `active_code` varchar(255) DEFAULT NULL,
  `referral_code` varchar(255) DEFAULT NULL,
  `referral_code_used` varchar(255) DEFAULT NULL,
  `wallet` decimal(10,2) NOT NULL DEFAULT 0.00,
  `restaurant_content_id` int(11) DEFAULT NULL COMMENT 'restaurant table content id field',
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL,
  `is_masterdata` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'Use to allow add/edit/delete permission'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`entity_id`, `parent_user_id`, `company_name`, `stripe_customer_id`, `first_name`, `last_name`, `phone_code`, `mobile_number`, `email`, `password`, `user_type`, `role_id`, `login_type`, `social_media_id`, `status`, `availability_status`, `active`, `is_deleted`, `image`, `earning_points`, `email_verification_code`, `user_otp`, `notification`, `notification_sound`, `device_id`, `language_slug`, `driver_temperature`, `active_code`, `referral_code`, `referral_code_used`, `wallet`, `restaurant_content_id`, `created_by`, `created_date`, `updated_by`, `updated_date`, `is_masterdata`) VALUES
(1, NULL, NULL, NULL, 'Master', 'Admin', '', '', 'support@hausdesdoeners.com', '2adbb4ebc97c8765ec5d735bb967fb49', 'MasterAdmin', 1, NULL, NULL, 1, 0, 0, 0, NULL, NULL, 'kcO3pUxnQWsJESd6iGb8LnhGv', NULL, NULL, 0, NULL, NULL, NULL, '86b7a0fc', NULL, NULL, '0.00', NULL, NULL, '2018-09-12 06:46:02', 1, '2022-02-14 11:42:58', '0'),
(23, NULL, NULL, NULL, 'Erik', 'John', '91', '7537537537', 'erik123@yopmail.com', '6ad9da02789d44c092b96a5c5b327ce2', 'User', NULL, NULL, NULL, 1, 0, 1, 0, 'profile/0b44fd188a48589182f80623c83130ac.jpeg', NULL, '9K6EjTUHVr7tiX1Pl5yJ23YDQMA', '123456', 0, 1, 'token', 'en', NULL, NULL, 'UaqIhT2D', NULL, '3095.00', NULL, NULL, '2021-03-22 12:26:37', 1, '2022-08-09 10:08:35', '0'),
(27, NULL, NULL, NULL, 'PARALLAX12', 'Cruz12344', '91', '7427427427', 'parallax123@yopmail.com', 'c02e93738dad49fb1178b52921835ade', 'Restaurant Admin', 2, NULL, NULL, 1, 0, 1, 0, 'profile/fa2e7fd0320b2bf80d8c4df60b54a42e.jpeg', NULL, 'EfXms2C8AY1jiFbZaGhe2ez3k', NULL, 0, 1, '', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, 1, '2021-03-23 07:09:08', 1, '2022-12-22 08:00:04', '0'),
(49, NULL, NULL, NULL, 'Devon', 'D', '91', '9999900000', 'vamsi1@yopmail.com', 'f3968b535e9cb6019a913920be807817', 'User', NULL, NULL, NULL, 1, 0, NULL, 0, 'profile/d6c1a494d4a24e623a391087203824b4.jpeg', NULL, '1IJivoXZajc3l5KUesqR49AHdy2', '123456', 1, 1, 'dogBjlFJyag:APA91bGPgFpvVvkS1vU3Z4FEYlAhu5Iwx6neCsJ_cT1pxuwTzdutZBa1jH3QuIDrVdFRn0rfVJsHdVf3EtaAY_ameH-wu2hxCSfEDZPESPUJnRc6fTgQ94kYYWese_Uigj67hlcQoz5I', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, NULL, '2021-03-26 09:44:48', 1, '2022-08-09 10:08:27', '0'),
(319, NULL, NULL, NULL, 'Mark', 'M', '91', '6060606060', 'testdriver@yopmail.com', '6ad9da02789d44c092b96a5c5b327ce2', 'Driver', NULL, NULL, NULL, 1, 1, 1, 0, 'profile/fde9d2fc5a0fc25fd69cbf63c9d3b170.png', NULL, 'JOkudRYb7X5ZmAox6STl319b0WY4', NULL, 1, 1, 'fT5eGG_4ToakrzhFdnrGQs:APA91bHSgDgA-Xp57P3-62LCepzvJTEtJ4-xWuIDD-P3Qjzs_n2Pkcjk5sJYtO_qYD5iqYlb9heALa7j-oxiGO0BZV_IxuJlZkytwAMgMVH9D-6559pDkZ16d81OjR7JkoGr_jvnCuq9', 'en', '2.40c', '959d57d5', NULL, NULL, '0.00', NULL, 1, '2021-06-01 08:32:46', 1, '2022-12-22 07:58:29', '0'),
(378, 1, NULL, NULL, 'RESTAURANT ADMIN', 'ADMIN', '91', '9999666696', 'restoadmin@yopmail.com', '88cba9da69ac9c435619575fe43b6dbd', 'Restaurant Admin', 2, NULL, NULL, 1, 0, 1, 0, 'profile/6d91279623be051d682d8363882ab2b3.png', NULL, NULL, NULL, 1, 1, '', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, 1, '2021-06-17 11:29:41', 1, '2022-12-22 08:00:32', '0'),
(391, NULL, NULL, NULL, 'Erik', 'Driver', '91', '7992203262', 'erik199@gmail.com', '512e7bc8ca075f8da2fd4b9a34f080e3', 'Driver', NULL, NULL, NULL, 1, 1, 1, 0, 'profile/985e12d94da32ba8c483b4d69ded67bb.jpeg', NULL, NULL, NULL, 1, 1, 'f9e2WBSUwEn-q5cP6-dWWM:APA91bGgNKNmLM5H9mdHl47K8lIzBWju758G2h8UPmhzpIfpQHyhSQqtGu0h2PrHSsdMholxEu_vi7M7U_8dLTcxIstfHeOTZj5CPTg7lIVmtQTXEh3QYVkvJT8zB6-DIWXPVfoZ4RZ3', 'en', '98.2*F - Checked at 3.00 PM - 22-07-2021', NULL, NULL, NULL, '0.00', NULL, 1, '2021-06-18 09:32:31', 1, '2022-12-22 07:58:11', '0'),
(400, NULL, NULL, NULL, 'Stephen', 'Smith', '1', '2063826303', 'sree719.ph@gmail.com', '512e7bc8ca075f8da2fd4b9a34f080e3', 'Driver', NULL, NULL, NULL, 1, 1, 1, 0, 'profile/53911a795ecd4f6c223846e487802d24.jpeg', NULL, NULL, NULL, 1, 1, 'cEYiUUaMRTipiAMcPsbWEz:APA91bE_d-ugjJ0iiOFsAPrydwPVpW_oKDcEc5TapSJHwk1ui7eDGCbHn0kilZyoifibwU6vStCO7M1vALYJOrUhWRGga7N4cgTjZk4gnmHaLpXkjwOhQKO8x0AOqRShq_9dmcFvZ3kZ', 'en', '98.2F - Checked at 3.00 PM - 21-07-2021', NULL, NULL, NULL, '0.00', NULL, 1, '2021-06-22 05:31:51', 1, '2022-12-22 07:58:03', '0'),
(439, 1, NULL, NULL, 'Admin', 'A', '91', '7777888899', 'admin12@yopmail.com', '88cba9da69ac9c435619575fe43b6dbd', 'Restaurant Admin', 2, NULL, NULL, 1, 0, 1, 0, 'profile/5c951124cd47fa4a4ba95a8795f2ecfb.JPEG', NULL, NULL, NULL, 1, 1, 'fxboi3NZSqavZMZWauYLKY:APA91bEt-niJNeQopOObnf2b7dwTE4djp2XVm36msj2tU7-6cjmB9cZinRXgTqAcAkFs5spb4O9yUA7svV3krEfw6Sidj2Xje3huzSE7P8q6Ht0Rz2YwAu0VcKyh-t2PVri9UzWGtCJ4', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, 1, '2021-06-24 11:14:42', 1, '2022-12-22 08:00:40', '0'),
(482, 372, NULL, NULL, 'RestaurantBranch', 'Adminadmin', '91', '9995559995', 'branch1@yopmail.com', '6ad9da02789d44c092b96a5c5b327ce2', 'Branch Admin', 3, NULL, NULL, 1, 0, 1, 0, 'profile/b41f8cfa8b8ceea8020995e1e89984bb.jpeg', NULL, NULL, NULL, 1, 1, '', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, 372, '2021-07-02 10:23:56', 1, '2022-12-22 08:00:54', '0'),
(515, 378, NULL, NULL, 'New', 'Branch Admin', '1', '7077190993', 'new7@yopmail.com', '5e41d7c0855d883873eeee597a2fca82', 'Branch Admin', 3, NULL, NULL, 1, 0, 1, 0, 'profile/d9fe5ae43470969831bc848f6fb703a7.jpeg', NULL, 'JP9RtaSvDOL2GHMZIzh1515IwoY4', NULL, 1, 1, '', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, 372, '2021-07-08 06:31:15', 1, '2022-12-22 08:01:09', '0'),
(523, 439, NULL, NULL, 'Jack', 'Lambda', '91', '9879879872', 'jack@yopmail.com', '6ad9da02789d44c092b96a5c5b327ce2', 'Branch Admin', 3, NULL, NULL, 1, 0, 1, 0, 'profile/360cb4829eb50c90e49d5e39b64d1ed6.jpeg', NULL, NULL, NULL, 1, 1, '', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, 1, '2021-07-09 12:26:23', 1, '2022-12-22 08:01:16', '0'),
(623, 622, NULL, NULL, 'Steve', 'Smith', '1', '2063826303', 'sree719.ph@gmail.com', '512e7bc8ca075f8da2fd4b9a34f080e3', 'Branch Admin', 3, NULL, NULL, 1, 0, 1, 0, 'profile/3f155d25ff64ed7cce6bcb1680ad9fb1.jpeg', NULL, 'PKjSnJhDuAg24i839aQM623nNZPK', NULL, 1, 1, 'd3ikWiN0SL2fFF3M_tdM4n:APA91bGMmFF1Qj8qRu0Wst-2bJoakxIZEoDPJwdFbthkMob5LWTN4mGcXwVFhBNuZScmN1DgwmTGTp24eL-mfpiWnN1sh5XM9tEiq_VrnSwznv6uAqi8hfbH2ygmQGsdr6CpHeyL-wCG', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, 1, '2021-08-24 07:02:28', 1, '2022-12-22 08:01:28', '0'),
(1083, NULL, NULL, NULL, 'Ios', 'User', '91', '3322110000', 'ios@user.com', '30cf6fcb5a798b82ecd365f59985bd65', 'User', NULL, NULL, NULL, 1, 0, 1, 0, 'profile/781f109d6aaeebda83c17e1b1275f772.jpeg', NULL, NULL, '123456', 1, 1, '', 'en', NULL, NULL, '0TOLQy7V', NULL, '0.00', NULL, NULL, '2022-07-26 11:31:27', 1, '2022-08-09 10:07:54', '0'),
(1097, 1, NULL, NULL, 'Restaurant', 'Admin', '1', '6266262626', 'adminautoservire@hausdesdoeners.com', 'eea36b2ff1e2fabc8e308f94157c00c6', 'Restaurant Admin', 2, NULL, NULL, 1, 0, 1, 0, 'profile/336b9616ee9ea5c2755726635f31cd5f.jpeg', NULL, NULL, NULL, 1, 1, 'fSJt5eb6R7SjOnxAor3GGq:APA91bHD23J90c4MzFsHBTfVQyF7BqsvThV5Wx1h6tL8oxR0gcBT-oNEbNY4xNdupnwSA9Zq-vlbfnd91SZZGEut1lYYsQ6rllMalsjXNrZDOtiJj5keiRhqxdLKDQ7uvkrzyiWOOfR0', 'en', NULL, NULL, NULL, NULL, '0.00', NULL, 1, '2022-08-09 09:43:59', NULL, NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

CREATE TABLE `user_address` (
  `entity_id` int(11) NOT NULL,
  `user_entity_id` int(11) NOT NULL,
  `address_label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `search_area` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `landmark` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `latitude` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `longitude` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `zipcode` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `country` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `state` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `city` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `saved_status` tinyint(4) DEFAULT NULL COMMENT '1 -saved',
  `is_main` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`entity_id`, `user_entity_id`, `address_label`, `address`, `search_area`, `landmark`, `latitude`, `longitude`, `zipcode`, `country`, `state`, `city`, `saved_status`, `is_main`) VALUES
(414, 23, NULL, 'Vandematram City, GF-18, B/H Vandematram Arcade, New SG Rd, nr. Shayona Tilak, Vandematram Arcade, Gota, Ahmedabad, Gujarat 382481, India', NULL, 'Vandematram Arcade', '23.0951483', '72.54626309999999', NULL, 'India', 'Gujarat', 'Ahmedabad', NULL, 0),
(563, 23, NULL, '667, Mahatma Gandhi Road, Risi Nagar, Imli Bazaar, Maharaja Tukoji Rao Holker Cloth Market, Indore, Madhya Pradesh, India', NULL, 'Indore', '22.719736', '75.857505', NULL, NULL, NULL, '', NULL, 0),
(564, 23, NULL, '667, Mahatma Gandhi Rd, Risi Nagar, Rajwada, Indore, Madhya Pradesh 452007, India', NULL, 'Indore', '22.7195625', '75.85768750000001', NULL, NULL, NULL, '', NULL, 0),
(572, 23, 'Home', 'C404 Dev puja residents complex, near roos wood taver, Jodhpur Village, Ahmedabad, Gujarat 380015, India', NULL, 'C404 Dev puja residents complex', '23.024349', '72.5301521', NULL, '', '', 'Jodhpur', NULL, 0),
(632, 23, '', 'Casa Vyoma Block-50-53, Gurukul, Ahmedabad, Gujarat 380052, India', NULL, '', '23.0424576', '72.5352448', NULL, NULL, NULL, NULL, NULL, 0),
(652, 23, '', 'Indraprastha Tower, Drive In Rd, Nilmani Society, Memnagar, Ahmedabad, Gujarat 380052, India', NULL, '', '23.0457344', '72.531968', NULL, NULL, NULL, NULL, NULL, 0),
(657, 23, '', '65, Dr Vikram Sarabhai Marg, opposite Sahajanand College, Polytechnic, Panjara Pol, Ambawadi, Ahmedabad, Gujarat 380015, India', NULL, '', '23.0264899', '72.54491209999999', NULL, NULL, NULL, NULL, NULL, 0),
(712, 23, '', '12, Vishwas City 1, Chanakyapuri, Ahmedabad, Gujarat 380061, India', NULL, '', '23.0719488', '72.5286912', NULL, NULL, NULL, NULL, NULL, 0),
(713, 23, '', '83, Local Rd, Umedpark Society, Ghatlodiya, Ahmedabad, Gujarat 380061, India', NULL, '', '23.0695746', '72.5323023', NULL, NULL, NULL, NULL, NULL, 0),
(720, 23, '', '18, Dhongre, Ravishankar Maharaj Rd, Shenbhai Nagar, Ahmedabad, Gujarat 380081, India', NULL, '', '23.0653952', '72.5286912', NULL, NULL, NULL, NULL, NULL, 0),
(741, 23, '', 'Ahmednagar Bus Depot, 3PMM+CJP, Maniknagar, Ahmednagar, Maharashtra 414001, India', NULL, '', '23.068672', '72.5352448', NULL, 'India', 'Maharashtra', 'Ahmednagar', NULL, 0),
(769, 23, '', '105, Vandematram Arcade, Gota, Ahmedabad, Gujarat 382481, India', NULL, '', '23.0952513', '72.5455748', NULL, NULL, NULL, NULL, NULL, 0),
(780, 49, NULL, '5, Kénitra, Morocco', NULL, NULL, '34.25251901179212', '-6.601093535412594', NULL, NULL, NULL, 'Kénitra', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_event_notifications`
--

CREATE TABLE `user_event_notifications` (
  `event_notification_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_slug` enum('event_pending','event_paid','event_cancelled') NOT NULL,
  `view_status` tinyint(4) NOT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_event_notifications`
--

INSERT INTO `user_event_notifications` (`event_notification_id`, `event_id`, `user_id`, `notification_slug`, `view_status`, `datetime`) VALUES
(1, 9, 1099, 'event_paid', 0, '2022-10-04 06:21:48'),
(2, 13, 1099, 'event_paid', 0, '2022-10-27 00:01:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_feedback`
--

CREATE TABLE `user_feedback` (
  `entity_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feedback_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `feedback_message` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

CREATE TABLE `user_log` (
  `user_log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `controller` varchar(255) NOT NULL,
  `function` varchar(255) NOT NULL,
  `user_ip` varchar(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`user_log_id`, `user_id`, `action`, `controller`, `function`, `user_ip`, `created_date`) VALUES
(76, 1, 'Master Admin edited role - Master Admin', 'role', 'edit', '::1', '2022-12-22 03:09:01'),
(77, 1, 'Master Admin edited role - Restaurant Admin', 'role', 'edit', '::1', '2022-12-22 03:09:07'),
(78, 1, 'Master Admin edited role - Branch Admin', 'role', 'edit', '::1', '2022-12-22 03:09:15'),
(79, 1, 'Master Admin edited Restaurant Admin - Hardik Patel', 'users', 'edit', '::1', '2022-12-22 04:30:05'),
(80, 1, 'Master Admin edited Restaurant Admin - Hardik Patel', 'users', 'edit', '::1', '2022-12-22 04:30:37'),
(81, 622, 'Hardik Patel partial refunded for order - 69 (ordered from: Las Palmas)', 'branch_admin_api', 'edit_order', '::1', '2022-12-22 04:59:07'),
(82, 622, 'Hardik Patel edited an order - 69', 'branch_admin_api', 'edit_order', '::1', '2022-12-22 04:59:07'),
(83, 1, 'Master Admin edited payment method - Stripe', 'payment_method', 'edit', '::1', '2023-01-05 23:45:46'),
(84, 1, 'Master Admin edited payment method - Stripe', 'payment_method', 'edit', '::1', '2023-01-05 23:46:50'),
(85, 1, 'Master Admin edited payment method - Stripe', 'payment_method', 'edit', '::1', '2023-01-05 23:47:05'),
(86, 1, 'Master Admin edited a CMS page - Privacy Policy', 'cms', 'edit', '::1', '2023-01-23 01:26:51'),
(87, 1, 'Master Admin edited a CMS page - Politique de confidentialité', 'cms', 'edit', '::1', '2023-01-23 01:27:21'),
(88, 1, 'Master Admin edited a CMS page - Terms and Conditions', 'cms', 'edit', '::1', '2023-01-23 01:28:45'),
(89, 1, 'Master Admin edited a CMS page - Cookie Policy', 'cms', 'edit', '::1', '2023-01-23 01:29:02'),
(90, 1, 'Master Admin edited a CMS page - Cookie Policy', 'cms', 'edit', '::1', '2023-01-23 01:29:16'),
(91, 1, 'Master Admin edited a CMS page - Privacy Policy', 'cms', 'edit', '::1', '2023-01-23 01:29:58'),
(92, 1, 'Master Admin edited a CMS page - Politique de confidentialité', 'cms', 'edit', '::1', '2023-01-23 01:30:09'),
(93, 1, 'Master Admin edited a CMS page - سياسة الخصوصية', 'cms', 'edit', '::1', '2023-01-23 01:30:22'),
(94, 1, 'Master Admin edited a CMS page - Terms and Conditions', 'cms', 'edit', '::1', '2023-01-23 01:30:44'),
(95, 1, 'Master Admin edited a CMS page - Termes et conditions', 'cms', 'edit', '::1', '2023-01-23 01:30:55'),
(96, 1, 'Master Admin edited a CMS page - Contact Us', 'cms', 'edit', '::1', '2023-01-23 01:31:15'),
(97, 1, 'Master Admin edited a CMS page - About Us', 'cms', 'edit', '::1', '2023-01-23 01:31:25'),
(98, 1, 'Master Admin edited a CMS page - Cookie Policy', 'cms', 'edit', '::1', '2023-01-23 01:31:32'),
(99, 1, 'Master Admin edited a CMS page - Login with Facebook', 'cms', 'edit', '::1', '2023-01-23 01:31:48'),
(100, 1, 'Master Admin edited a CMS page - Cookie Policy', 'cms', 'edit', '::1', '2023-01-23 01:31:52'),
(101, 1, 'Master Admin edited a CMS page - Login with Facebook', 'cms', 'edit', '::1', '2023-01-23 01:32:12'),
(102, 1, 'Master Admin edited a CMS page - الأحكام والشروط', 'cms', 'edit', '::1', '2023-01-23 01:33:18'),
(103, 1, 'Master Admin edited a CMS page - Nous contacter', 'cms', 'edit', '::1', '2023-01-23 01:33:31'),
(104, 1, 'Master Admin edited a CMS page - Contact Us', 'cms', 'edit', '::1', '2023-01-23 01:33:41'),
(105, 1, 'Master Admin edited a coupon - CARTDIS', 'coupon', 'edit', '::1', '2023-01-23 01:34:05'),
(106, 1, 'Master Admin edited a coupon - FREE DELIVERY ', 'coupon', 'edit', '::1', '2023-01-23 01:34:23'),
(107, 1, 'Master Admin edited a coupon - DISCOUNT ONCART', 'coupon', 'edit', '::1', '2023-01-23 01:34:27'),
(108, 1, 'Master Admin edited a coupon - CARTDIS', 'coupon', 'edit', '::1', '2023-01-23 01:35:00'),
(109, 1, 'Master Admin edited food type - Veg', 'food_type', 'edit', '::1', '2023-01-23 01:40:21'),
(110, 1, 'Master Admin edited food type - Légumes', 'food_type', 'edit', '::1', '2023-01-23 01:40:29'),
(111, 1, 'Master Admin edited food type - نباتي', 'food_type', 'edit', '::1', '2023-01-23 01:40:35'),
(112, 1, 'Master Admin edited food type - Non-Veg', 'food_type', 'edit', '::1', '2023-01-23 01:40:43'),
(113, 1, 'Master Admin edited food type - Egg', 'food_type', 'edit', '::1', '2023-01-23 01:40:57'),
(114, 1, 'Master Admin edited food type - Sea Food', 'food_type', 'edit', '::1', '2023-01-23 01:41:07'),
(115, 1, 'Master Admin edited a CMS page - Contact Us', 'cms', 'edit', '::1', '2023-01-24 04:46:40'),
(116, 1, 'Master Admin edited a CMS page - Privacy Policy', 'cms', 'edit', '::1', '2023-01-24 04:57:49'),
(117, 1, 'Master Admin edited a CMS page - Privacy Policy', 'cms', 'edit', '::1', '2023-01-24 04:58:18'),
(118, 1, 'Master Admin edited a CMS page - Privacy Policy', 'cms', 'edit', '::1', '2023-01-24 04:58:56'),
(119, 1, 'Master Admin edited a CMS page - Politique de confidentialité', 'cms', 'edit', '::1', '2023-01-24 05:01:23'),
(120, 1, 'Master Admin edited a CMS page - سياسة الخصوصية', 'cms', 'edit', '::1', '2023-01-24 05:01:50'),
(121, 1, 'Master Admin edited a CMS page - Terms and Conditions', 'cms', 'edit', '::1', '2023-01-24 05:03:15'),
(122, 1, 'Master Admin edited a CMS page - Termes et conditions', 'cms', 'edit', '::1', '2023-01-24 05:04:24'),
(123, 1, 'Master Admin edited a CMS page - Terms and Conditions', 'cms', 'edit', '::1', '2023-01-24 05:04:38'),
(124, 1, 'Master Admin edited a CMS page - الأحكام والشروط', 'cms', 'edit', '::1', '2023-01-24 05:04:52'),
(125, 1, 'Master Admin edited a CMS page - Contact Us', 'cms', 'edit', '::1', '2023-01-24 05:05:47'),
(126, 1, 'Master Admin edited a CMS page - Nous contacter', 'cms', 'edit', '::1', '2023-01-24 05:05:55'),
(127, 1, 'Master Admin edited a CMS page - الأحكام والشروط', 'cms', 'edit', '::1', '2023-01-24 05:06:09'),
(128, 1, 'Master Admin edited a CMS page - About Us', 'cms', 'edit', '::1', '2023-01-24 05:07:31'),
(129, 1, 'Master Admin edited a CMS page - À propos de nous', 'cms', 'edit', '::1', '2023-01-24 05:07:51'),
(130, 1, 'Master Admin edited a CMS page - About Us', 'cms', 'edit', '::1', '2023-01-24 05:08:21'),
(131, 1, 'Master Admin edited a CMS page - À propos de nous', 'cms', 'edit', '::1', '2023-01-24 05:08:39'),
(132, 1, 'Master Admin edited a CMS page - معلومات عنا', 'cms', 'edit', '::1', '2023-01-24 05:10:54'),
(133, 1, 'Master Admin edited a CMS page - Login with Facebook', 'cms', 'edit', '::1', '2023-01-24 05:11:36'),
(134, 1, 'Master Admin edited a CMS page - Login with Facebook', 'cms', 'edit', '::1', '2023-01-24 05:13:02'),
(135, 1, 'Master Admin edited a CMS page - Se connecter avec Facebook', 'cms', 'edit', '::1', '2023-01-24 05:15:47'),
(136, 1, 'Master Admin edited a CMS page - تسجيل الدخول باستخدام الفيسبوك', 'cms', 'edit', '::1', '2023-01-24 05:16:52'),
(137, 1, 'Master Admin edited a CMS page - Cookie Policy', 'cms', 'edit', '::1', '2023-01-24 05:19:42'),
(138, 1, 'Master Admin updated system options', 'system_option', 'view', '::1', '2023-01-24 05:21:40'),
(139, 1, 'Master Admin edited email template - Forgot Password', 'email_template', 'edit', '::1', '2023-01-24 05:23:15'),
(140, 1, 'Master Admin edited email template - Promotional Email', 'email_template', 'edit', '::1', '2023-01-24 05:23:53'),
(141, 1, 'Master Admin edited email template - User Added', 'email_template', 'edit', '::1', '2023-01-24 05:24:37'),
(142, 1, 'Master Admin edited email template - New Restaurant Alert', 'email_template', 'edit', '::1', '2023-01-24 05:25:23'),
(143, 1, 'Master Admin edited email template - Restaurant Details Update Alert', 'email_template', 'edit', '::1', '2023-01-24 05:26:21'),
(144, 1, 'Master Admin edited email template - Email Update Alert', 'email_template', 'edit', '::1', '2023-01-24 05:27:14'),
(145, 1, 'Master Admin edited email template - Change status Alert', 'email_template', 'edit', '::1', '2023-01-24 05:27:54'),
(146, 1, 'Master Admin edited email template - Verify Account', 'email_template', 'edit', '::1', '2023-01-24 05:28:41'),
(147, 1, 'Master Admin edited email template - Contact Us', 'email_template', 'edit', '::1', '2023-01-24 05:29:36'),
(148, 1, 'Master Admin edited email template - Contact Us for Admin', 'email_template', 'edit', '::1', '2023-01-24 05:30:07'),
(149, 1, 'Master Admin edited email template - Account Verified', 'email_template', 'edit', '::1', '2023-01-24 05:30:51'),
(150, 1, 'Master Admin edited email template - Event Booking Reminder', 'email_template', 'edit', '::1', '2023-01-24 05:31:24'),
(151, 1, 'Master Admin edited email template - Guest Order Confirmation', 'email_template', 'edit', '::1', '2023-01-24 05:32:01'),
(152, 1, 'Master Admin edited email template - Table Booking Reminder', 'email_template', 'edit', '::1', '2023-01-24 05:32:50'),
(153, 1, 'Master Admin edited email template - Forgot Password OTP', 'email_template', 'edit', '::1', '2023-01-24 05:33:35'),
(154, 1, 'Master Admin edited email template - Order Receive Alert', 'email_template', 'edit', '::1', '2023-01-24 05:34:47'),
(155, 1, 'Master Admin edited email template - Order Cancelled', 'email_template', 'edit', '::1', '2023-01-24 05:35:58'),
(156, 1, 'Master Admin edited email template - Order Updated', 'email_template', 'edit', '::1', '2023-01-24 05:36:39'),
(157, 1, 'Master Admin edited email template - Promotional Email', 'email_template', 'edit', '202.131.112', '2023-01-25 07:05:17'),
(158, 1, 'Master Admin edited email template - Promotional Email', 'email_template', 'edit', '202.131.112', '2023-01-25 08:20:37'),
(159, 1, 'Master Admin edited a CMS page - About Us', 'cms', 'edit', '202.131.112', '2023-01-25 08:25:42'),
(160, 1, 'Master Admin updated system options', 'system_option', 'view', '103.156.200', '2023-01-25 12:14:48'),
(161, 1, 'Master Admin updated system options', 'system_option', 'view', '103.156.200', '2023-01-25 12:16:36'),
(162, 1, 'Master Admin updated system options', 'system_option', 'view', '103.156.200', '2023-01-30 08:28:50'),
(163, 1, 'Master Admin updated system options', 'system_option', 'view', '103.156.200', '2023-01-30 08:30:42'),
(164, 1, 'Master Admin updated system options', 'system_option', 'view', '::1', '2023-01-30 23:42:08'),
(165, 1, 'Master Admin edited email template - Order Receive Alert', 'email_template', 'edit', '::1', '2023-02-08 08:13:13'),
(166, 1, 'Master Admin edited email template - Order Updated', 'email_template', 'edit', '::1', '2023-02-08 08:13:37'),
(167, 1, 'Master Admin edited email template - Forgot Password', 'email_template', 'edit', '::1', '2023-02-08 08:14:47'),
(168, 1, 'Master Admin edited email template - Mot de passe oublié', 'email_template', 'edit', '::1', '2023-02-08 08:15:01'),
(169, 1, 'Master Admin edited email template - هل نسيت كلمة السر', 'email_template', 'edit', '::1', '2023-02-08 08:15:18'),
(170, 1, 'Master Admin edited email template - Promotional Email', 'email_template', 'edit', '::1', '2023-02-08 08:15:45'),
(171, 1, 'Master Admin edited email template - User Added', 'email_template', 'edit', '::1', '2023-02-08 08:16:08'),
(172, 1, 'Master Admin edited email template - New Restaurant Alert', 'email_template', 'edit', '::1', '2023-02-08 08:16:24'),
(173, 1, 'Master Admin edited email template - Restaurant Details Update Alert', 'email_template', 'edit', '::1', '2023-02-08 08:16:35'),
(174, 1, 'Master Admin edited email template - Email Update Alert', 'email_template', 'edit', '::1', '2023-02-08 08:17:08'),
(175, 1, 'Master Admin edited email template - Change status Alert', 'email_template', 'edit', '::1', '2023-02-08 08:17:19'),
(176, 1, 'Master Admin edited email template - Verify Account', 'email_template', 'edit', '::1', '2023-02-08 08:17:42'),
(177, 1, 'Master Admin edited email template - Contact Us', 'email_template', 'edit', '::1', '2023-02-08 08:18:14'),
(178, 1, 'Master Admin edited email template - Contact Us for Admin', 'email_template', 'edit', '::1', '2023-02-08 08:18:32'),
(179, 1, 'Master Admin edited email template - نسيت كلمة المرور OTP', 'email_template', 'edit', '::1', '2023-02-08 08:19:55'),
(180, 1, 'Master Admin edited email template - Mot de passe oublié OTP', 'email_template', 'edit', '::1', '2023-02-08 08:20:12'),
(181, 1, 'Master Admin edited email template - Order Updated', 'email_template', 'edit', '::1', '2023-02-08 08:20:34'),
(182, 1, 'Master Admin edited email template - Order Cancelled', 'email_template', 'edit', '::1', '2023-02-08 08:20:53'),
(183, 1, 'Master Admin edited email template - Order Receive Alert', 'email_template', 'edit', '::1', '2023-02-08 08:21:05'),
(184, 1, 'Master Admin edited email template - Forgot Password OTP', 'email_template', 'edit', '::1', '2023-02-08 08:21:16'),
(185, 1, 'Master Admin edited email template - Table Booking Reminder', 'email_template', 'edit', '::1', '2023-02-08 08:21:27'),
(186, 1, 'Master Admin edited email template - Guest Order Confirmation', 'email_template', 'edit', '::1', '2023-02-08 08:21:44'),
(187, 1, 'Master Admin edited email template - Event Booking Reminder', 'email_template', 'edit', '::1', '2023-02-08 08:21:55'),
(188, 1, 'Master Admin edited email template - Account Verified', 'email_template', 'edit', '::1', '2023-02-08 08:22:07'),
(189, 1, 'Master Admin edited email template - Verify Account', 'email_template', 'edit', '::1', '2023-02-13 06:41:51'),
(190, 1, 'Master Admin edited email template - Verify Account', 'email_template', 'edit', '::1', '2023-02-13 06:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_order_notification`
--

CREATE TABLE `user_order_notification` (
  `user_notification_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'for cancelled or failed refund notifications',
  `notification_slug` enum('order_accepted','order_preparing','order_ongoing','order_delivered','order_canceled','order_rejected','order_ready','order_served','order_completed','order_updated','admin_order_created','order_rejected_refunded','order_canceled_refunded','order_initiated','tip_refund_initiated','order_refund_canceled','order_refund_failed','order_refund_pending','tip_refund_canceled','tip_refund_failed','tip_refund_pending') NOT NULL,
  `view_status` tinyint(4) NOT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_order_notification`
--

INSERT INTO `user_order_notification` (`user_notification_id`, `order_id`, `user_id`, `transaction_id`, `notification_slug`, `view_status`, `datetime`) VALUES
(2, 1, 23, NULL, 'order_completed', 0, '2022-08-09 13:06:17'),
(4, 2, 23, NULL, 'order_accepted', 0, '2022-08-09 18:19:05'),
(5, 3, 23, NULL, 'order_completed', 0, '2022-08-09 18:19:13'),
(6, 4, 49, NULL, 'admin_order_created', 0, '2022-08-09 18:40:37'),
(10, 5, 1101, NULL, 'order_completed', 0, '2022-08-10 14:25:08'),
(11, 6, 1101, NULL, 'order_rejected', 0, '2022-08-10 14:25:22'),
(12, 2, 23, NULL, 'order_preparing', 0, '2022-08-12 13:46:33'),
(13, 2, 23, NULL, '', 0, '2022-08-12 13:46:36'),
(14, 2, 23, NULL, 'order_canceled', 0, '2022-08-12 13:46:42'),
(15, 10, 1103, NULL, 'order_accepted', 0, '2022-08-12 14:07:05'),
(16, 10, 1103, NULL, 'order_preparing', 0, '2022-08-12 14:07:28'),
(17, 11, 314, NULL, 'admin_order_created', 0, '2022-08-16 12:05:26'),
(18, 11, 314, NULL, 'order_preparing', 0, '2022-08-16 12:06:30'),
(19, 7, 1101, NULL, 'order_accepted', 0, '2022-09-01 09:20:29'),
(20, 7, 1101, NULL, 'order_preparing', 0, '2022-09-01 09:21:08'),
(21, 7, 1101, NULL, 'order_ongoing', 0, '2022-09-01 09:21:34'),
(22, 7, 1101, NULL, 'order_delivered', 0, '2022-09-01 09:30:38'),
(23, 25, 1101, NULL, 'order_accepted', 0, '2022-09-01 09:32:46'),
(24, 25, 1101, NULL, 'order_preparing', 0, '2022-09-01 09:33:01'),
(25, 16, 1099, NULL, 'order_accepted', 0, '2022-09-10 21:09:38'),
(26, 21, 1099, NULL, 'order_accepted', 0, '2022-09-10 21:09:41'),
(30, 37, 1102, NULL, 'order_completed', 0, '2022-09-27 08:59:47'),
(31, 38, 1138, NULL, 'order_rejected', 0, '2022-09-27 09:03:05'),
(32, 36, 1102, NULL, 'order_rejected', 0, '2022-09-27 09:11:48'),
(36, 39, 1138, NULL, 'order_completed', 0, '2022-09-27 09:19:56'),
(37, 40, 1138, NULL, 'order_accepted', 0, '2022-09-27 09:24:15'),
(38, 42, 314, NULL, 'order_rejected', 0, '2022-09-27 09:38:19'),
(39, 40, 1138, NULL, 'order_preparing', 0, '2022-09-27 09:38:32'),
(41, 43, 314, NULL, 'order_rejected', 0, '2022-09-27 09:44:42'),
(42, 41, 1138, NULL, 'order_completed', 0, '2022-09-27 09:45:01'),
(43, 44, 314, NULL, 'order_rejected', 0, '2022-09-27 09:53:10'),
(45, 45, 1099, NULL, 'order_completed', 0, '2022-09-27 09:58:11'),
(49, 46, 314, NULL, 'order_completed', 0, '2022-09-27 10:13:51'),
(52, 47, 1099, NULL, 'order_completed', 0, '2022-09-27 10:14:13'),
(53, 48, 314, NULL, 'order_accepted', 0, '2022-09-27 12:08:01'),
(57, 52, 1138, NULL, 'order_completed', 0, '2022-10-04 06:15:23'),
(58, 53, 1138, NULL, 'order_accepted', 0, '2022-10-07 05:27:21'),
(59, 53, 1138, NULL, 'order_preparing', 0, '2022-10-07 05:28:52'),
(60, 53, 1138, NULL, 'order_served', 0, '2022-10-07 05:30:18'),
(64, 56, 1138, NULL, '', 0, '2022-10-11 09:44:43'),
(67, 32, 1099, NULL, 'order_updated', 0, '2022-10-17 07:39:47'),
(72, 58, 1138, NULL, 'order_completed', 0, '2022-10-19 14:59:34'),
(74, 60, 1121, NULL, 'order_completed', 0, '2022-10-20 12:50:58'),
(79, 66, 1099, NULL, '', 0, '2022-10-27 18:14:38'),
(80, 64, 1099, NULL, '', 0, '2022-10-27 18:14:49'),
(83, 59, 1099, NULL, '', 0, '2022-10-27 18:15:06'),
(86, 57, 1153, NULL, '', 0, '2022-10-27 18:15:21'),
(87, 65, 1099, NULL, 'order_accepted', 0, '2022-10-27 21:41:09'),
(92, 70, 0, NULL, '', 0, '2022-10-28 12:57:41'),
(95, 69, 0, NULL, '', 0, '2022-10-28 12:58:00'),
(96, 33, 1099, NULL, 'order_accepted', 0, '2022-11-02 04:56:55'),
(97, 33, 1099, NULL, 'order_preparing', 0, '2022-11-02 04:58:02'),
(98, 33, 1099, NULL, 'order_delivered', 0, '2022-11-06 12:32:02'),
(102, 72, 1099, NULL, 'order_completed', 0, '2022-11-07 05:00:19'),
(104, 75, 1099, NULL, '', 0, '2022-11-08 03:20:02'),
(108, 76, 1099, NULL, '', 0, '2022-11-14 12:34:03'),
(109, 77, 1099, NULL, 'order_rejected', 0, '2022-11-15 13:19:38'),
(110, 79, 0, NULL, 'order_delivered', 0, '2022-11-22 11:05:45'),
(111, 80, 1099, NULL, 'order_accepted', 0, '2022-11-22 12:40:08'),
(112, 85, 1170, NULL, 'order_accepted', 0, '2022-11-25 12:44:44'),
(113, 85, 1170, NULL, 'order_canceled', 0, '2022-11-25 12:45:00'),
(114, 84, 1170, NULL, 'order_accepted', 0, '2022-11-25 12:45:12'),
(115, 84, 1170, NULL, 'order_ongoing', 0, '2022-11-25 12:45:21'),
(116, 84, 1170, NULL, 'order_delivered', 0, '2022-11-25 12:49:06'),
(117, 78, 1099, NULL, 'order_accepted', 0, '2022-11-25 15:43:18'),
(118, 78, 1099, NULL, 'order_preparing', 0, '2022-11-25 15:43:43'),
(119, 78, 1099, NULL, 'order_delivered', 0, '2022-11-25 15:44:03'),
(120, 74, 1099, NULL, 'order_accepted', 0, '2022-11-25 15:44:17'),
(121, 74, 1099, NULL, 'order_preparing', 0, '2022-11-25 15:44:25'),
(122, 87, 1170, NULL, 'order_accepted', 0, '2022-11-29 17:01:04'),
(126, 89, 1099, NULL, '', 0, '2022-12-13 13:43:38'),
(127, 9, 1103, NULL, 'order_canceled', 0, '2022-12-21 13:37:12'),
(128, 18, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:15'),
(129, 26, 1125, NULL, 'order_canceled', 0, '2022-12-21 13:37:18'),
(130, 27, 314, NULL, 'order_canceled', 0, '2022-12-21 13:37:19'),
(131, 29, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:22'),
(132, 30, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:24'),
(133, 31, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:26'),
(134, 32, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:28'),
(135, 49, 1139, NULL, 'order_canceled', 0, '2022-12-21 13:37:31'),
(136, 51, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:33'),
(137, 54, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:41'),
(138, 54, 1099, 'pi_3LqswdDx91eqc6CT0X6Oz3J9', 'order_initiated', 0, '2022-12-21 13:37:41'),
(139, 61, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:43'),
(140, 61, 1099, 'pi_3LqswdDx91eqc6CT0X6Oz3J9', 'order_initiated', 0, '2022-12-21 13:37:44'),
(141, 67, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:50'),
(142, 67, 1099, 'pi_3Lw8ZADx91eqc6CT1H5EIfKF', 'order_initiated', 0, '2022-12-21 13:37:50'),
(143, 88, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:56'),
(144, 88, 1099, 'pi_3Lxpj3Dx91eqc6CT2Ai8KOjm', 'order_initiated', 0, '2022-12-21 13:37:56'),
(145, 90, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:37:59'),
(146, 90, 1099, 'pi_3Lxpj3Dx91eqc6CT2Ai8KOjm', 'order_initiated', 0, '2022-12-21 13:37:59'),
(147, 91, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:38:01'),
(148, 91, 1099, 'pi_3Lxpj3Dx91eqc6CT2Ai8KOjm', 'order_initiated', 0, '2022-12-21 13:38:01'),
(149, 92, 1099, NULL, 'order_canceled', 0, '2022-12-21 13:38:03'),
(150, 92, 1099, 'pi_3Lxpj3Dx91eqc6CT2Ai8KOjm', 'order_initiated', 0, '2022-12-21 13:38:04'),
(151, 93, 23, NULL, 'order_accepted', 0, '2022-12-22 06:47:28'),
(152, 93, 23, NULL, 'order_ongoing', 0, '2022-12-22 06:47:31'),
(153, 93, 23, 'pi_3MHiXrDx91eqc6CT192hg4Xj', 'order_initiated', 0, '2022-12-22 06:48:17'),
(154, 94, 23, NULL, 'admin_order_created', 0, '2022-12-22 06:48:46');

-- --------------------------------------------------------

--
-- Table structure for table `user_table_notifications`
--

CREATE TABLE `user_table_notifications` (
  `table_notification_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_slug` enum('table_awaiting','table_confirmed','table_cancelled') NOT NULL,
  `view_status` tinyint(4) NOT NULL,
  `datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_table_notifications`
--

INSERT INTO `user_table_notifications` (`table_notification_id`, `table_id`, `user_id`, `notification_slug`, `view_status`, `datetime`) VALUES
(1, 6, 1099, 'table_confirmed', 0, '2022-10-04 06:17:27'),
(2, 5, 1101, 'table_confirmed', 0, '2022-10-04 06:17:32'),
(3, 3, 1103, 'table_confirmed', 0, '2022-10-04 06:17:38'),
(4, 9, 1099, 'table_confirmed', 0, '2022-10-31 15:34:30');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_history`
--

CREATE TABLE `wallet_history` (
  `wallet_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `referee_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `credit` tinyint(4) NOT NULL DEFAULT 0,
  `debit` tinyint(4) NOT NULL DEFAULT 0,
  `wallet_transaction_id` varchar(255) DEFAULT NULL,
  `reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_date` timestamp NULL DEFAULT current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `updated_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `add_ons_category`
--
ALTER TABLE `add_ons_category`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`),
  ADD KEY `language_slug` (`language_slug`) USING BTREE,
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `add_ons_master`
--
ALTER TABLE `add_ons_master`
  ADD PRIMARY KEY (`add_ons_id`),
  ADD KEY `menu_add_ons_fk` (`menu_id`),
  ADD KEY `category_menu_fk` (`category_id`);

--
-- Indexes for table `admin_alerts`
--
ALTER TABLE `admin_alerts`
  ADD PRIMARY KEY (`alert_id`);

--
-- Indexes for table `agent_order_notification`
--
ALTER TABLE `agent_order_notification`
  ADD PRIMARY KEY (`agent_notification_id`);

--
-- Indexes for table `bookmark_restaurant`
--
ALTER TABLE `bookmark_restaurant`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `cancel_reject_reasons`
--
ALTER TABLE `cancel_reject_reasons`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `cart_detail`
--
ALTER TABLE `cart_detail`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`),
  ADD KEY `language_slug` (`language_slug`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `cms`
--
ALTER TABLE `cms`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`),
  ADD KEY `image` (`image`),
  ADD KEY `cms_icon` (`cms_icon`);

--
-- Indexes for table `contactus_detail`
--
ALTER TABLE `contactus_detail`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `content_general`
--
ALTER TABLE `content_general`
  ADD PRIMARY KEY (`content_general_id`),
  ADD KEY `content_type` (`content_type`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coupon`
--
ALTER TABLE `coupon`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`),
  ADD KEY `coupon_type` (`coupon_type`);

--
-- Indexes for table `coupon_category_map`
--
ALTER TABLE `coupon_category_map`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `fk_coupon_category_map_coupon_id` (`coupon_id`),
  ADD KEY `category_content_id` (`category_content_id`);

--
-- Indexes for table `coupon_item_map`
--
ALTER TABLE `coupon_item_map`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `coupon_item_fk` (`coupon_id`),
  ADD KEY `item_fk` (`item_id`),
  ADD KEY `package_fk` (`package_id`);

--
-- Indexes for table `coupon_restaurant_map`
--
ALTER TABLE `coupon_restaurant_map`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `coupon_res_fk` (`coupon_id`),
  ADD KEY `restaurnat_fk` (`restaurant_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`currency_id`);

--
-- Indexes for table `delivery_charge`
--
ALTER TABLE `delivery_charge`
  ADD PRIMARY KEY (`charge_id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `area_name` (`area_name`);

--
-- Indexes for table `delivery_method`
--
ALTER TABLE `delivery_method`
  ADD PRIMARY KEY (`delivery_method_id`);

--
-- Indexes for table `doordash_relay_details`
--
ALTER TABLE `doordash_relay_details`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `driver_traking_map`
--
ALTER TABLE `driver_traking_map`
  ADD PRIMARY KEY (`traking_id`),
  ADD KEY `latitude` (`latitude`),
  ADD KEY `longitude` (`longitude`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `title` (`title`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`),
  ADD KEY `no_of_people` (`no_of_people`);

--
-- Indexes for table `event_detail`
--
ALTER TABLE `event_detail`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `event_detail_fk` (`event_id`);

--
-- Indexes for table `event_notification`
--
ALTER TABLE `event_notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `faq_category_entity_id_faqs_faq_category_id` (`faq_category_id`);

--
-- Indexes for table `faq_category`
--
ALTER TABLE `faq_category`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `food_type`
--
ALTER TABLE `food_type`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`),
  ADD KEY `language_slug` (`language_slug`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `language_slug` (`language_slug`);

--
-- Indexes for table `menu_addons_sequencemap`
--
ALTER TABLE `menu_addons_sequencemap`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `add_ons_content_id` (`add_ons_content_id`),
  ADD KEY `sequence_no` (`sequence_no`),
  ADD KEY `restaurant_owner_id` (`restaurant_owner_id`);

--
-- Indexes for table `menu_category_sequencemap`
--
ALTER TABLE `menu_category_sequencemap`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `category_content_id` (`category_content_id`),
  ADD KEY `sequence_no` (`sequence_no`),
  ADD KEY `restaurant_owner_id` (`restaurant_owner_id`);

--
-- Indexes for table `menu_item_sequencemap`
--
ALTER TABLE `menu_item_sequencemap`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `notifications_users`
--
ALTER TABLE `notifications_users`
  ADD PRIMARY KEY (`map_id`),
  ADD KEY `user_notification_fk` (`notification_id`);

--
-- Indexes for table `order_coupon_use`
--
ALTER TABLE `order_coupon_use`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `order_detail_fk` (`order_id`),
  ADD KEY `user_name` (`user_name`),
  ADD KEY `user_mobile_number` (`user_mobile_number`);

--
-- Indexes for table `order_driver_map`
--
ALTER TABLE `order_driver_map`
  ADD PRIMARY KEY (`driver_map_id`),
  ADD KEY `order_driver_fk` (`order_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `order_master`
--
ALTER TABLE `order_master`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `custom_index` (`user_id`,`restaurant_id`,`order_status`) USING BTREE,
  ADD KEY `refund_status` (`refund_status`);

--
-- Indexes for table `order_notification`
--
ALTER TABLE `order_notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`status_id`),
  ADD KEY `order_status_fk` (`order_id`),
  ADD KEY `order_status` (`order_status`);

--
-- Indexes for table `partial_refund_log`
--
ALTER TABLE `partial_refund_log`
  ADD PRIMARY KEY (`prefund_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `restaurant`
--
ALTER TABLE `restaurant`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `name` (`name`),
  ADD KEY `food_type` (`food_type`),
  ADD KEY `language_slug` (`language_slug`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `restaurant_slug` (`restaurant_slug`);

--
-- Indexes for table `restaurant_address`
--
ALTER TABLE `restaurant_address`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `restaurant_address_fk` (`resto_entity_id`);

--
-- Indexes for table `restaurant_branch_map`
--
ALTER TABLE `restaurant_branch_map`
  ADD PRIMARY KEY (`map_id`);

--
-- Indexes for table `restaurant_delivery_method_map`
--
ALTER TABLE `restaurant_delivery_method_map`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `restaurant_driver_map`
--
ALTER TABLE `restaurant_driver_map`
  ADD PRIMARY KEY (`map_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `restaurant_error_reports`
--
ALTER TABLE `restaurant_error_reports`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `restaurant_menu_item`
--
ALTER TABLE `restaurant_menu_item`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `restaurant_menu_fk` (`restaurant_id`),
  ADD KEY `category_menu_fk` (`category_id`),
  ADD KEY `name` (`name`),
  ADD KEY `language_slug` (`language_slug`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `item_slug` (`item_slug`);

--
-- Indexes for table `restaurant_menu_recipe_map`
--
ALTER TABLE `restaurant_menu_recipe_map`
  ADD PRIMARY KEY (`map_id`);

--
-- Indexes for table `restaurant_menu_suggestion`
--
ALTER TABLE `restaurant_menu_suggestion`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `restaurant_content_id` (`restaurant_content_id`),
  ADD KEY `menu_content_id` (`menu_content_id`);

--
-- Indexes for table `restaurant_package`
--
ALTER TABLE `restaurant_package`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `package_restaurant_fk` (`restaurant_id`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `restaurant_payment_method_suggestion`
--
ALTER TABLE `restaurant_payment_method_suggestion`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `restaurant_content_id` (`restaurant_content_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `review_restaurant_fk` (`restaurant_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `order_user_id` (`order_user_id`);

--
-- Indexes for table `role_access`
--
ALTER TABLE `role_access`
  ADD PRIMARY KEY (`access_id`);

--
-- Indexes for table `role_access_rights`
--
ALTER TABLE `role_access_rights`
  ADD PRIMARY KEY (`role_access_rights_id`);

--
-- Indexes for table `role_master`
--
ALTER TABLE `role_master`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `slider_image`
--
ALTER TABLE `slider_image`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `stripe_callback_details`
--
ALTER TABLE `stripe_callback_details`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `system_option`
--
ALTER TABLE `system_option`
  ADD PRIMARY KEY (`SystemOptionID`),
  ADD KEY `OptionSlug` (`OptionSlug`);

--
-- Indexes for table `system_option_group`
--
ALTER TABLE `system_option_group`
  ADD PRIMARY KEY (`GroupID`);

--
-- Indexes for table `table_booking`
--
ALTER TABLE `table_booking`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `restaurant_content_id` (`restaurant_content_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_name` (`user_name`);

--
-- Indexes for table `table_booking_notification`
--
ALTER TABLE `table_booking_notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `table_master`
--
ALTER TABLE `table_master`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `table_status`
--
ALTER TABLE `table_status`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `tips`
--
ALTER TABLE `tips`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `first_name` (`first_name`),
  ADD KEY `last_name` (`last_name`),
  ADD KEY `user_type` (`user_type`),
  ADD KEY `login_type` (`login_type`);

--
-- Indexes for table `user_address`
--
ALTER TABLE `user_address`
  ADD PRIMARY KEY (`entity_id`),
  ADD KEY `address_user_fk` (`user_entity_id`);

--
-- Indexes for table `user_event_notifications`
--
ALTER TABLE `user_event_notifications`
  ADD PRIMARY KEY (`event_notification_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `notification_slug` (`notification_slug`);

--
-- Indexes for table `user_feedback`
--
ALTER TABLE `user_feedback`
  ADD PRIMARY KEY (`entity_id`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`user_log_id`);

--
-- Indexes for table `user_order_notification`
--
ALTER TABLE `user_order_notification`
  ADD PRIMARY KEY (`user_notification_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `notification_slug` (`notification_slug`);

--
-- Indexes for table `user_table_notifications`
--
ALTER TABLE `user_table_notifications`
  ADD PRIMARY KEY (`table_notification_id`);

--
-- Indexes for table `wallet_history`
--
ALTER TABLE `wallet_history`
  ADD PRIMARY KEY (`wallet_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `add_ons_category`
--
ALTER TABLE `add_ons_category`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `add_ons_master`
--
ALTER TABLE `add_ons_master`
  MODIFY `add_ons_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `admin_alerts`
--
ALTER TABLE `admin_alerts`
  MODIFY `alert_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `agent_order_notification`
--
ALTER TABLE `agent_order_notification`
  MODIFY `agent_notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookmark_restaurant`
--
ALTER TABLE `bookmark_restaurant`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cancel_reject_reasons`
--
ALTER TABLE `cancel_reject_reasons`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `cart_detail`
--
ALTER TABLE `cart_detail`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT for table `cms`
--
ALTER TABLE `cms`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `contactus_detail`
--
ALTER TABLE `contactus_detail`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_general`
--
ALTER TABLE `content_general`
  MODIFY `content_general_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2296;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `coupon`
--
ALTER TABLE `coupon`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT for table `coupon_category_map`
--
ALTER TABLE `coupon_category_map`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `coupon_item_map`
--
ALTER TABLE `coupon_item_map`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=431;

--
-- AUTO_INCREMENT for table `coupon_restaurant_map`
--
ALTER TABLE `coupon_restaurant_map`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=452;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `currency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `delivery_charge`
--
ALTER TABLE `delivery_charge`
  MODIFY `charge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `delivery_method`
--
ALTER TABLE `delivery_method`
  MODIFY `delivery_method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doordash_relay_details`
--
ALTER TABLE `doordash_relay_details`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `driver_traking_map`
--
ALTER TABLE `driver_traking_map`
  MODIFY `traking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=380579;

--
-- AUTO_INCREMENT for table `email_template`
--
ALTER TABLE `email_template`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `event_detail`
--
ALTER TABLE `event_detail`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `event_notification`
--
ALTER TABLE `event_notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `faq_category`
--
ALTER TABLE `faq_category`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `food_type`
--
ALTER TABLE `food_type`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `menu_addons_sequencemap`
--
ALTER TABLE `menu_addons_sequencemap`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `menu_category_sequencemap`
--
ALTER TABLE `menu_category_sequencemap`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `menu_item_sequencemap`
--
ALTER TABLE `menu_item_sequencemap`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `notifications_users`
--
ALTER TABLE `notifications_users`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=551;

--
-- AUTO_INCREMENT for table `order_coupon_use`
--
ALTER TABLE `order_coupon_use`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `order_driver_map`
--
ALTER TABLE `order_driver_map`
  MODIFY `driver_map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_master`
--
ALTER TABLE `order_master`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `order_notification`
--
ALTER TABLE `order_notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_status`
--
ALTER TABLE `order_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `partial_refund_log`
--
ALTER TABLE `partial_refund_log`
  MODIFY `prefund_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `restaurant`
--
ALTER TABLE `restaurant`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `restaurant_address`
--
ALTER TABLE `restaurant_address`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `restaurant_branch_map`
--
ALTER TABLE `restaurant_branch_map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `restaurant_delivery_method_map`
--
ALTER TABLE `restaurant_delivery_method_map`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_driver_map`
--
ALTER TABLE `restaurant_driver_map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1917;

--
-- AUTO_INCREMENT for table `restaurant_error_reports`
--
ALTER TABLE `restaurant_error_reports`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `restaurant_menu_item`
--
ALTER TABLE `restaurant_menu_item`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=732;

--
-- AUTO_INCREMENT for table `restaurant_menu_recipe_map`
--
ALTER TABLE `restaurant_menu_recipe_map`
  MODIFY `map_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `restaurant_menu_suggestion`
--
ALTER TABLE `restaurant_menu_suggestion`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_package`
--
ALTER TABLE `restaurant_package`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `restaurant_payment_method_suggestion`
--
ALTER TABLE `restaurant_payment_method_suggestion`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=208;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `role_access`
--
ALTER TABLE `role_access`
  MODIFY `access_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=215;

--
-- AUTO_INCREMENT for table `role_access_rights`
--
ALTER TABLE `role_access_rights`
  MODIFY `role_access_rights_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10227;

--
-- AUTO_INCREMENT for table `role_master`
--
ALTER TABLE `role_master`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `slider_image`
--
ALTER TABLE `slider_image`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `stripe_callback_details`
--
ALTER TABLE `stripe_callback_details`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_option`
--
ALTER TABLE `system_option`
  MODIFY `SystemOptionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `system_option_group`
--
ALTER TABLE `system_option_group`
  MODIFY `GroupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `table_booking`
--
ALTER TABLE `table_booking`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `table_booking_notification`
--
ALTER TABLE `table_booking_notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `table_master`
--
ALTER TABLE `table_master`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `table_status`
--
ALTER TABLE `table_status`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1197;

--
-- AUTO_INCREMENT for table `user_address`
--
ALTER TABLE `user_address`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=803;

--
-- AUTO_INCREMENT for table `user_event_notifications`
--
ALTER TABLE `user_event_notifications`
  MODIFY `event_notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_feedback`
--
ALTER TABLE `user_feedback`
  MODIFY `entity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
  MODIFY `user_log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `user_order_notification`
--
ALTER TABLE `user_order_notification`
  MODIFY `user_notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `user_table_notifications`
--
ALTER TABLE `user_table_notifications`
  MODIFY `table_notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `wallet_history`
--
ALTER TABLE `wallet_history`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `add_ons_master`
--
ALTER TABLE `add_ons_master`
  ADD CONSTRAINT `menu_add_ons_fk` FOREIGN KEY (`menu_id`) REFERENCES `restaurant_menu_item` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coupon_category_map`
--
ALTER TABLE `coupon_category_map`
  ADD CONSTRAINT `fk_coupon_category_map_coupon_id` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`entity_id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_item_map`
--
ALTER TABLE `coupon_item_map`
  ADD CONSTRAINT `coupon_item_fk` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coupon_restaurant_map`
--
ALTER TABLE `coupon_restaurant_map`
  ADD CONSTRAINT `coupon_res_fk` FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event_detail`
--
ALTER TABLE `event_detail`
  ADD CONSTRAINT `event_detail_fk` FOREIGN KEY (`event_id`) REFERENCES `event` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faqs`
--
ALTER TABLE `faqs`
  ADD CONSTRAINT `faq_category_entity_id_faqs_faq_category_id` FOREIGN KEY (`faq_category_id`) REFERENCES `faq_category` (`entity_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications_users`
--
ALTER TABLE `notifications_users`
  ADD CONSTRAINT `user_notification_fk` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `order_detail_fk` FOREIGN KEY (`order_id`) REFERENCES `order_master` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_driver_map`
--
ALTER TABLE `order_driver_map`
  ADD CONSTRAINT `order_driver_fk` FOREIGN KEY (`order_id`) REFERENCES `order_master` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_status`
--
ALTER TABLE `order_status`
  ADD CONSTRAINT `order_status_fk` FOREIGN KEY (`order_id`) REFERENCES `order_master` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `partial_refund_log`
--
ALTER TABLE `partial_refund_log`
  ADD CONSTRAINT `partial_refund_log_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_master` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_address`
--
ALTER TABLE `restaurant_address`
  ADD CONSTRAINT `restaurant_address_ibfk_1` FOREIGN KEY (`resto_entity_id`) REFERENCES `restaurant` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurant_driver_map`
--
ALTER TABLE `restaurant_driver_map`
  ADD CONSTRAINT `restaurant_driver_map_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `users` (`entity_id`);

--
-- Constraints for table `tips`
--
ALTER TABLE `tips`
  ADD CONSTRAINT `tips_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order_master` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
