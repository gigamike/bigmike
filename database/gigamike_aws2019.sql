-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 14, 2019 at 10:43 PM
-- Server version: 5.6.41-84.1
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gigamike_aws2019`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `post` longtext NOT NULL,
  `created_datetime` datetime NOT NULL,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `modified_user_id` bigint(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand` varchar(255) NOT NULL,
  `description` longtext,
  `logo_name` varchar(255) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `modified_user_id` bigint(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `brand`, `description`, `logo_name`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(1, 'Optimum Nutrition', '100% Whey Protein (The Worlds Best Selling Protein Powder Supplement), Sports Nutrition Products and Fitness Supplements.', 'logo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(2, 'MuscleTech', 'At MuscleTech, our researchers are passionate about supplements. Our mission is to develop the most scientifically advanced and effective supplements to help you build muscle and strength, lose weight, and improve athletic performance.', 'logo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(4, 'Animal Pak', 'The True Original since 1983, the Animal Pak was developed to cover the wide backs of the hardest and heaviest trainers on the planet Earth.', 'logo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(5, 'Centrum', 'Centrum offers a range of multivitamins specifically formulated to include the essential vitamins and minerals for your body.', 'logo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(6, 'Unilab', 'Unilab is the maker of the biggest prescription, consumer healthcare and personal care brands in the Philippines.', 'logo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(7, 'Schiek', 'Sports, fitness, Lifting, weight, weight lifting, workout, belts, gloves, athletic, training, support, back, wrist, strap, Schiek fitness products have a reputation as being the best since 1991. Our products include: belts, gloves, lifting straps and miscellanous items.', 'logo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `created_datetime` datetime NOT NULL,
  `created_user_id` bigint(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `product_id`, `quantity`, `created_datetime`, `created_user_id`) VALUES
(1, 16, 1, '2019-02-17 12:48:42', 10),
(2, 21, 1, '2019-02-17 12:49:01', 10);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(255) NOT NULL,
  `photo_name` varchar(255) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `modified_user_id` bigint(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category`, `photo_name`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(1, 'Protein Whey', 'photo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(2, 'Multivitamins', 'photo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(3, 'Fat Burners', 'photo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(4, 'Pre-Workout', 'photo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(5, 'Post-Workout', 'photo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL),
(6, 'Accessories', 'photo.jpg', '0000-00-00 00:00:00', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE `country` (
  `id` int(10) UNSIGNED NOT NULL,
  `country_code` char(2) NOT NULL,
  `country_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `country_code`, `country_name`) VALUES
(1, 'A1', 'Anonymous Proxy'),
(2, 'A2', 'Satellite Provider'),
(3, 'O1', 'Other Country'),
(4, 'AD', 'Andorra'),
(5, 'AE', 'United Arab Emirates'),
(6, 'AF', 'Afghanistan'),
(7, 'AG', 'Antigua and Barbuda'),
(8, 'AI', 'Anguilla'),
(9, 'AL', 'Albania'),
(10, 'AM', 'Armenia'),
(11, 'AO', 'Angola'),
(12, 'AP', 'Asia/Pacific Region'),
(13, 'AQ', 'Antarctica'),
(14, 'AR', 'Argentina'),
(15, 'AS', 'American Samoa'),
(16, 'AT', 'Austria'),
(17, 'AU', 'Australia'),
(18, 'AW', 'Aruba'),
(19, 'AX', 'Aland Islands'),
(20, 'AZ', 'Azerbaijan'),
(21, 'BA', 'Bosnia and Herzegovina'),
(22, 'BB', 'Barbados'),
(23, 'BD', 'Bangladesh'),
(24, 'BE', 'Belgium'),
(25, 'BF', 'Burkina Faso'),
(26, 'BG', 'Bulgaria'),
(27, 'BH', 'Bahrain'),
(28, 'BI', 'Burundi'),
(29, 'BJ', 'Benin'),
(30, 'BL', 'Saint Bartelemey'),
(31, 'BM', 'Bermuda'),
(32, 'BN', 'Brunei Darussalam'),
(33, 'BO', 'Bolivia'),
(34, 'BQ', 'Bonaire, Saint Eustatius and Saba'),
(35, 'BR', 'Brazil'),
(36, 'BS', 'Bahamas'),
(37, 'BT', 'Bhutan'),
(38, 'BV', 'Bouvet Island'),
(39, 'BW', 'Botswana'),
(40, 'BY', 'Belarus'),
(41, 'BZ', 'Belize'),
(42, 'CA', 'Canada'),
(43, 'CC', 'Cocos (Keeling) Islands'),
(44, 'CD', 'Congo, The Democratic Republic of the'),
(45, 'CF', 'Central African Republic'),
(46, 'CG', 'Congo'),
(47, 'CH', 'Switzerland'),
(48, 'CI', 'Cote d\'Ivoire'),
(49, 'CK', 'Cook Islands'),
(50, 'CL', 'Chile'),
(51, 'CM', 'Cameroon'),
(52, 'CN', 'China'),
(53, 'CO', 'Colombia'),
(54, 'CR', 'Costa Rica'),
(55, 'CU', 'Cuba'),
(56, 'CV', 'Cape Verde'),
(57, 'CW', 'Curacao'),
(58, 'CX', 'Christmas Island'),
(59, 'CY', 'Cyprus'),
(60, 'CZ', 'Czech Republic'),
(61, 'DE', 'Germany'),
(62, 'DJ', 'Djibouti'),
(63, 'DK', 'Denmark'),
(64, 'DM', 'Dominica'),
(65, 'DO', 'Dominican Republic'),
(66, 'DZ', 'Algeria'),
(67, 'EC', 'Ecuador'),
(68, 'EE', 'Estonia'),
(69, 'EG', 'Egypt'),
(70, 'EH', 'Western Sahara'),
(71, 'ER', 'Eritrea'),
(72, 'ES', 'Spain'),
(73, 'ET', 'Ethiopia'),
(74, 'EU', 'Europe'),
(75, 'FI', 'Finland'),
(76, 'FJ', 'Fiji'),
(77, 'FK', 'Falkland Islands (Malvinas)'),
(78, 'FM', 'Micronesia, Federated States of'),
(79, 'FO', 'Faroe Islands'),
(80, 'FR', 'France'),
(81, 'GA', 'Gabon'),
(82, 'GB', 'United Kingdom'),
(83, 'GD', 'Grenada'),
(84, 'GE', 'Georgia'),
(85, 'GF', 'French Guiana'),
(86, 'GG', 'Guernsey'),
(87, 'GH', 'Ghana'),
(88, 'GI', 'Gibraltar'),
(89, 'GL', 'Greenland'),
(90, 'GM', 'Gambia'),
(91, 'GN', 'Guinea'),
(92, 'GP', 'Guadeloupe'),
(93, 'GQ', 'Equatorial Guinea'),
(94, 'GR', 'Greece'),
(95, 'GS', 'South Georgia and the South Sandwich Islands'),
(96, 'GT', 'Guatemala'),
(97, 'GU', 'Guam'),
(98, 'GW', 'Guinea-Bissau'),
(99, 'GY', 'Guyana'),
(100, 'HK', 'Hong Kong'),
(101, 'HM', 'Heard Island and McDonald Islands'),
(102, 'HN', 'Honduras'),
(103, 'HR', 'Croatia'),
(104, 'HT', 'Haiti'),
(105, 'HU', 'Hungary'),
(106, 'ID', 'Indonesia'),
(107, 'IE', 'Ireland'),
(108, 'IL', 'Israel'),
(109, 'IM', 'Isle of Man'),
(110, 'IN', 'India'),
(111, 'IO', 'British Indian Ocean Territory'),
(112, 'IQ', 'Iraq'),
(113, 'IR', 'Iran, Islamic Republic of'),
(114, 'IS', 'Iceland'),
(115, 'IT', 'Italy'),
(116, 'JE', 'Jersey'),
(117, 'JM', 'Jamaica'),
(118, 'JO', 'Jordan'),
(119, 'JP', 'Japan'),
(120, 'KE', 'Kenya'),
(121, 'KG', 'Kyrgyzstan'),
(122, 'KH', 'Cambodia'),
(123, 'KI', 'Kiribati'),
(124, 'KM', 'Comoros'),
(125, 'KN', 'Saint Kitts and Nevis'),
(126, 'KP', 'Korea, Democratic People\'s Republic of'),
(127, 'KR', 'Korea, Republic of'),
(128, 'KW', 'Kuwait'),
(129, 'KY', 'Cayman Islands'),
(130, 'KZ', 'Kazakhstan'),
(131, 'LA', 'Lao People\'s Democratic Republic'),
(132, 'LB', 'Lebanon'),
(133, 'LC', 'Saint Lucia'),
(134, 'LI', 'Liechtenstein'),
(135, 'LK', 'Sri Lanka'),
(136, 'LR', 'Liberia'),
(137, 'LS', 'Lesotho'),
(138, 'LT', 'Lithuania'),
(139, 'LU', 'Luxembourg'),
(140, 'LV', 'Latvia'),
(141, 'LY', 'Libyan Arab Jamahiriya'),
(142, 'MA', 'Morocco'),
(143, 'MC', 'Monaco'),
(144, 'MD', 'Moldova, Republic of'),
(145, 'ME', 'Montenegro'),
(146, 'MF', 'Saint Martin'),
(147, 'MG', 'Madagascar'),
(148, 'MH', 'Marshall Islands'),
(149, 'MK', 'Macedonia'),
(150, 'ML', 'Mali'),
(151, 'MM', 'Myanmar'),
(152, 'MN', 'Mongolia'),
(153, 'MO', 'Macao'),
(154, 'MP', 'Northern Mariana Islands'),
(155, 'MQ', 'Martinique'),
(156, 'MR', 'Mauritania'),
(157, 'MS', 'Montserrat'),
(158, 'MT', 'Malta'),
(159, 'MU', 'Mauritius'),
(160, 'MV', 'Maldives'),
(161, 'MW', 'Malawi'),
(162, 'MX', 'Mexico'),
(163, 'MY', 'Malaysia'),
(164, 'MZ', 'Mozambique'),
(165, 'NA', 'Namibia'),
(166, 'NC', 'New Caledonia'),
(167, 'NE', 'Niger'),
(168, 'NF', 'Norfolk Island'),
(169, 'NG', 'Nigeria'),
(170, 'NI', 'Nicaragua'),
(171, 'NL', 'Netherlands'),
(172, 'NO', 'Norway'),
(173, 'NP', 'Nepal'),
(174, 'NR', 'Nauru'),
(175, 'NU', 'Niue'),
(176, 'NZ', 'New Zealand'),
(177, 'OM', 'Oman'),
(178, 'PA', 'Panama'),
(179, 'PE', 'Peru'),
(180, 'PF', 'French Polynesia'),
(181, 'PG', 'Papua New Guinea'),
(182, 'PH', 'Philippines'),
(183, 'PK', 'Pakistan'),
(184, 'PL', 'Poland'),
(185, 'PM', 'Saint Pierre and Miquelon'),
(186, 'PN', 'Pitcairn'),
(187, 'PR', 'Puerto Rico'),
(188, 'PS', 'Palestinian Territory'),
(189, 'PT', 'Portugal'),
(190, 'PW', 'Palau'),
(191, 'PY', 'Paraguay'),
(192, 'QA', 'Qatar'),
(193, 'RE', 'Reunion'),
(194, 'RO', 'Romania'),
(195, 'RS', 'Serbia'),
(196, 'RU', 'Russian Federation'),
(197, 'RW', 'Rwanda'),
(198, 'SA', 'Saudi Arabia'),
(199, 'SB', 'Solomon Islands'),
(200, 'SC', 'Seychelles'),
(201, 'SD', 'Sudan'),
(202, 'SE', 'Sweden'),
(203, 'SG', 'Singapore'),
(204, 'SH', 'Saint Helena'),
(205, 'SI', 'Slovenia'),
(206, 'SJ', 'Svalbard and Jan Mayen'),
(207, 'SK', 'Slovakia'),
(208, 'SL', 'Sierra Leone'),
(209, 'SM', 'San Marino'),
(210, 'SN', 'Senegal'),
(211, 'SO', 'Somalia'),
(212, 'SR', 'Suriname'),
(213, 'SS', 'South Sudan'),
(214, 'ST', 'Sao Tome and Principe'),
(215, 'SV', 'El Salvador'),
(216, 'SX', 'Sint Maarten'),
(217, 'SY', 'Syrian Arab Republic'),
(218, 'SZ', 'Swaziland'),
(219, 'TC', 'Turks and Caicos Islands'),
(220, 'TD', 'Chad'),
(221, 'TF', 'French Southern Territories'),
(222, 'TG', 'Togo'),
(223, 'TH', 'Thailand'),
(224, 'TJ', 'Tajikistan'),
(225, 'TK', 'Tokelau'),
(226, 'TL', 'Timor-Leste'),
(227, 'TM', 'Turkmenistan'),
(228, 'TN', 'Tunisia'),
(229, 'TO', 'Tonga'),
(230, 'TR', 'Turkey'),
(231, 'TT', 'Trinidad and Tobago'),
(232, 'TV', 'Tuvalu'),
(233, 'TW', 'Taiwan'),
(234, 'TZ', 'Tanzania, United Republic of'),
(235, 'UA', 'Ukraine'),
(236, 'UG', 'Uganda'),
(237, 'UM', 'United States Minor Outlying Islands'),
(238, 'US', 'United States'),
(239, 'UY', 'Uruguay'),
(240, 'UZ', 'Uzbekistan'),
(241, 'VA', 'Holy See (Vatican City State)'),
(242, 'VC', 'Saint Vincent and the Grenadines'),
(243, 'VE', 'Venezuela'),
(244, 'VG', 'Virgin Islands, British'),
(245, 'VI', 'Virgin Islands, U.S.'),
(246, 'VN', 'Vietnam'),
(247, 'VU', 'Vanuatu'),
(248, 'WF', 'Wallis and Futuna'),
(249, 'WS', 'Samoa'),
(250, 'YE', 'Yemen'),
(251, 'YT', 'Mayotte'),
(252, 'ZA', 'South Africa'),
(253, 'ZM', 'Zambia'),
(254, 'ZW', 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `incentive`
--

CREATE TABLE `incentive` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `height_centimeters` decimal(10,2) NOT NULL,
  `weight_kilograms` decimal(10,2) NOT NULL,
  `bmi` decimal(10,2) NOT NULL,
  `bmi_category` varchar(255) NOT NULL,
  `incentive` decimal(10,2) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_user_id` bigint(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `incentive`
--

INSERT INTO `incentive` (`id`, `height_centimeters`, `weight_kilograms`, `bmi`, `bmi_category`, `incentive`, `created_datetime`, `created_user_id`) VALUES
(1, '142.00', '52.00', '25.80', 'Overweight', '0.00', '2019-01-01 00:00:00', 10),
(2, '142.00', '50.00', '25.80', 'Normal weight', '10.00', '2019-02-01 00:00:00', 10),
(3, '142.00', '52.00', '25.80', 'obese', '0.00', '2019-03-15 11:39:41', 10);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brand_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext,
  `photo_name1` varchar(255) DEFAULT NULL,
  `photo_name2` varchar(255) DEFAULT NULL,
  `photo_name3` varchar(255) DEFAULT NULL,
  `panorama` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(10) UNSIGNED NOT NULL,
  `discount_type` enum('amount','percentage') DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `modified_user_id` bigint(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `brand_id`, `name`, `description`, `photo_name1`, `photo_name2`, `photo_name3`, `panorama`, `price`, `stock`, `discount_type`, `discount`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(15, 1, 'Optimum Nutrition (ON) Gold Standard 100% Whey Protein Powder - 5 lbs, 2.27 kg (Double Rich Chocolate)', '24 grams of whey protein per serving with whey protein isolates as the primary ingredient and just 1 gram of sugar and 1 gram of fat\r\n5 grams of naturally occurring BCAAs and over 4 grams of glutamine and glutamic acid in each serving\r\nWhey protein microfractions from whey protein isolate and ultrafiltered whey protein concentrate\r\nUse first thing in the morning, before/after exercise or after weight training\r\nThere is no seal inside the cap,the product has a plastic shrink wrap seal around the cap (outside) for protection from contamination', 'photo1-700x400.jpg', 'photo2-700x400.jpg', 'photo3-700x400.jpg', NULL, '6049.00', 5, 'amount', '5.00', '2018-11-15 13:41:15', 11, NULL, NULL),
(16, 5, 'Centrum Multivitamin for Adults ', '(425 TOTAL TABLETS including a bonus travel size bottle)\r\nMultivitamin Supplement\r\nMultiminerals Supplement\r\nWhole Body Health\r\nEnergy, Immunity\r\nMetabolism', 'photo1-700x400.jpg', 'photo2-700x400.jpg', 'photo3-700x400.jpg', NULL, '1400.00', 15, 'amount', '5.00', '2018-11-15 13:41:15', 11, NULL, NULL),
(17, 7, 'Schiek Black Line Heavy Duty Wrist Wraps', '24-in long and 3-in wide wrist wraps\r\nHeavyweight cotton and elastic construction\r\nWide hook-and-loop closure\r\nRed and black\r\nMade in USA', 'photo1-700x400.jpg', 'photo2-700x400.jpg', 'photo3-700x400.jpg', NULL, '1276.69', 15, 'percentage', '2.00', '2018-11-15 13:41:15', 11, NULL, NULL),
(18, 6, 'Enervon C 30 tablets', 'Enervon is a nutritional supplement to help promote increased energy and enhance the immune system.', 'photo1-700x400.jpg', 'photo2-700x400.jpg', 'photo3-700x400.jpg', NULL, '300.00', 15, 'amount', '5.00', '2018-11-15 13:41:15', 11, NULL, NULL),
(19, 4, 'Animal Pak Multivitamin 30 Paks', 'Sports Nutrition Vitamins with Amino Acids, Antioxidants, Digestive Enzymes, Performance Complex - For Athletes and Bodybuilders - Immune Support, Recovery - 30 Paks', 'photo1-700x400.jpg', 'photo2-700x400.jpg', 'photo3-700x400.jpg', NULL, '1271.47', 15, 'amount', '5.00', '2018-11-15 13:41:15', 11, NULL, NULL),
(20, 2, 'MuscleTech Platinum Creatine Monohydrate Powder, 14.1oz (80 Servings)', '100% Pure Micronized Creatine Powder. MUSCLETECH PLATINUM 100% CREATINE - Each serving delivers 5 grams of premium HPLC-Tested micronized creatine monohydrate. No fillers, no added sugars – just best-quality, pure creatine powder\r\nSCIENTIFICALLY SUPERIOR - Best in category MuscleTech Platinum 100% Creatine features creatine monohydrate, the most researched form of creatine powder in sports nutrition\r\nCLINICALLY STUDIED RESULTS - Heavy-lifting trainers built 6 pounds of lean muscle in 6 weeks in a gold-standard, scientific study\r\nBUILD STRENGTH - In a separate study, test subjects increased their bench press strength by an amazing 18.6% in just 10 days\r\nOVERALL BENEFITS - 100% Micronized creatine monohydrate powder helps increase strength, build lean muscle & improve recovery', 'photo1-700x400.jpg', 'photo2-700x400.jpg', 'photo3-700x400.jpg', NULL, '1300.00', 15, 'amount', '5.00', '2018-11-15 13:41:15', 11, NULL, NULL),
(21, 2, 'Hydroxycut Hardcore Elite', '100ct, 100mg Coleus Forskohlii, 56.3mg Yohimbe, 200mg Green Coffee, 100mg L-Theanin ,200mg C.canephora Robusta\r\nWarning- contains a potent dose of stimulants. Do not exceed recommended dosage and refer to the warning statement below. If you are new to Hydroxycut Hardcore Elite, follow the dosing chart below\r\nExtreme Energy - Potent thermogenic driver (caffeine anhydrous)_ also jacks up energy levels for a boost in intensity - even after just one dose\r\nEnhanced Focus - Supports increased focus with its key thermogenic driver\r\nPowerful Weight Loss - Designed with a key weight loss ingredient, which has been shown to be effective in two scientific studies. Subjects taking the key ingredient (green coffee extract) lost 10.95 lbs. in 60 days with a low-calorie diet and 3.7 lbs. in an 8-week study with a calorie-reduced diet and moderate exercise. Most thermogenic formulas don\'t have any scientific studies backing their key weight loss ingredients', 'photo1-700x400.jpg', 'photo2-700x400.jpg', 'photo3-700x400.jpg', NULL, '1100.00', 15, 'amount', '5.00', '2018-11-15 13:41:15', 11, NULL, NULL),
(22, 1, 'Optimum Nutrition L-Glutamine Capsules', 'OPTIMUM NUTRITION L-Glutamine Muscle Recovery Capsules, 1000mg, 120 Count 1000MG OF PURE L-GLUTAMINE PER CAPSULE\r\nMOST ABUNDANT AMINO ACID IN THE BODY\r\nPLAYS AN IMPORTANT ROLE IN MUSCLE PROTEIN DEVELOPMENT\r\nDURING PROLONGED PERIODS OF INTENSE EXERCISE, GLUTAMINE LEVELS MAY BE DEPLETED\r\n3 SIZES AVAILABLE – 60ct, 120ct, 240ct', 'photo1-700x400.jpg', 'photo2-700x400.jpg', 'photo3-700x400.jpg', NULL, '600.00', 15, 'amount', '5.00', '2018-11-15 13:41:15', 11, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`id`, `product_id`, `category_id`) VALUES
(8, 15, 1),
(1, 16, 2),
(2, 17, 6),
(3, 18, 2),
(4, 19, 2),
(5, 20, 4),
(6, 21, 3),
(7, 22, 5);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('admin','supplier','member') CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `salt` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` enum('N','Y') NOT NULL,
  `mobile_no` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `country_id` int(10) UNSIGNED NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `credits` decimal(10,2) DEFAULT NULL,
  `created_datetime` datetime NOT NULL,
  `created_user_id` bigint(10) UNSIGNED NOT NULL,
  `modified_datetime` datetime DEFAULT NULL,
  `modified_user_id` bigint(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `role`, `email`, `password`, `first_name`, `last_name`, `salt`, `active`, `mobile_no`, `telephone`, `country_id`, `company_name`, `latitude`, `longitude`, `address`, `city`, `zip`, `credits`, `created_datetime`, `created_user_id`, `modified_datetime`, `modified_user_id`) VALUES
(1, 'admin', 'admin@gigamike.net', 'e698b4a0b08532cdff8443a4dd615588', 'Mik', 'Galon', 'kJ(26<$y>u01=1Su6V[t,GuDxMS=TCcAmkR%(V}FL/Wh?+_T`;', 'Y', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2014-12-30 11:40:44', 1, '2018-09-29 13:34:08', 0),
(10, 'member', 'member@gigamike.net', 'f4198c36bc89c8809a82f7b53c3bb8cb', 'Michael', 'Galon', '\\TF7K~7z?F3G2F1>\'MkRn)mY+W4=YoK6DpLoobj/*}V<DfYK4E', 'Y', '639086087306', NULL, 182, NULL, NULL, NULL, '290 Joseph St., Annex 41, Sun Valley', 'Pasay', '1700', '947110.00', '2018-10-17 08:08:10', 0, NULL, NULL),
(11, 'supplier', 'supplier@gigamike.net', '3dc63898f07898c047cf21ab021eb0e1', 'Michael', 'Galon', 'T-PeBza@]pLB&N3\\!R54~sZc`JhAzuPP#\"6E{wdYh2{m_/K```', 'Y', '639086087306', '635523474', 182, 'Gigamike.net', NULL, NULL, NULL, NULL, NULL, NULL, '2018-11-15 11:24:28', 0, NULL, NULL),
(12, 'supplier', 'supplier2@gigamike.net', 'f6ec215220e45250eade3d32990a3e21', 'Michael', 'Galon', 'i6pq~GKDA\'J<!<JrX_2qw+.\'-v4b]|\"H3r:3:eVZk\"uk=A_t\"p', 'Y', '3312312312', '1212313213', 182, 'Gigamike.net', NULL, NULL, '290 Joseph St., Annex 41', 'Pasay', '1234', NULL, '2018-11-16 07:19:45', 0, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `title` (`title`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `brand` (`brand`),
  ADD KEY `created_user_id` (`created_user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `created_user_id` (`created_user_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category` (`category`);

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_code` (`country_code`),
  ADD KEY `country_name` (`country_name`);

--
-- Indexes for table `incentive`
--
ALTER TABLE `incentive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_datetime` (`created_datetime`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_user_id` (`created_user_id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `price` (`price`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id_2` (`product_id`,`category_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `role` (`role`),
  ADD KEY `country_id` (`country_id`),
  ADD KEY `latitude` (`latitude`),
  ADD KEY `longitude` (`longitude`),
  ADD KEY `city` (`city`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `incentive`
--
ALTER TABLE `incentive`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
