-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 01, 2025 at 12:15 AM
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
-- Database: `kalpanapurdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `crimes`
--

CREATE TABLE `crimes` (
  `crime_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nerve_cost` int(11) NOT NULL,
  `success_rate` decimal(5,2) NOT NULL COMMENT 'Percentage success rate (e.g., 75.00)',
  `reward_min` int(11) NOT NULL COMMENT 'Minimum cash reward',
  `reward_max` int(11) NOT NULL COMMENT 'Maximum cash reward',
  `critical_failure_rate` decimal(5,2) NOT NULL COMMENT 'Chance of critical failure (e.g., 10.00)',
  `experience_gain` int(11) NOT NULL COMMENT 'Experience points for successful completion',
  `description` text DEFAULT NULL COMMENT 'Optional description of the crime',
  `storyline` text DEFAULT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crimes`
--

INSERT INTO `crimes` (`crime_id`, `name`, `nerve_cost`, `success_rate`, `reward_min`, `reward_max`, `critical_failure_rate`, `experience_gain`, `description`, `storyline`, `category_id`) VALUES
(1, 'Shoplift', 2, 75.00, 100, 300, 5.00, 1, 'Steal small items from a local store.', 'Stealing small items can be quick and rewarding if you’re careful.', 1),
(2, 'Pickpocket', 4, 65.00, 200, 500, 10.00, 2, 'Target unsuspecting pedestrians for quick cash.', 'Snatching wallets off unsuspecting victims requires precision.', 1),
(3, 'Bicycle Theft', 5, 60.00, 300, 700, 12.00, 3, 'Steal an unattended bicycle.', 'Spot an unlocked bicycle and make a quick getaway.', 1),
(4, 'Wallet Swap', 6, 50.00, 400, 800, 15.00, 4, 'Replace someone’s wallet with an empty one.', 'Switching wallets unnoticed can be a risky business.', 1),
(5, 'Jewelry Store Robbery', 12, 50.00, 1000, 5000, 20.00, 5, 'Plan a smash-and-grab at a jewelry store.', 'Carefully timing and smashing your way into riches.', 2),
(6, 'Museum Artifact Theft', 15, 40.00, 2000, 8000, 25.00, 6, 'Infiltrate a museum to steal artifacts.', 'Stealing history can be lucrative, but security is tight.', 2),
(7, 'Bank Vault Break-in', 20, 30.00, 5000, 15000, 30.00, 8, 'Break into a secure bank vault.', 'Precision and nerves of steel are required for this high-stakes heist.', 2),
(8, 'Train Heist', 25, 25.00, 7000, 20000, 35.00, 10, 'Hijack a train and loot its cargo.', 'A daring crime involving coordination and luck.', 2),
(9, 'Black Market Deal', 10, 60.00, 1000, 4000, 10.00, 4, 'Facilitate an illegal trade deal.', 'Operate in the shadows to broker dangerous deals.', 3),
(10, 'Kidnapping', 15, 50.00, 2000, 6000, 15.00, 5, 'Abduct someone for ransom.', 'A high-risk, high-reward crime that takes planning.', 3),
(11, 'Drug Smuggling', 18, 45.00, 3000, 8000, 20.00, 6, 'Transport narcotics across borders.', 'Navigate dangers to move illegal substances for profit.', 3),
(12, 'Arms Trafficking', 22, 40.00, 5000, 15000, 25.00, 8, 'Transport weapons to buyers.', 'Smuggling weapons involves high risks and higher rewards.', 3),
(13, 'Phishing Scam', 8, 70.00, 500, 1500, 8.00, 3, 'Steal data with a fake website.', 'Trick victims into providing sensitive information.', 4),
(14, 'Hack a Social Network', 12, 60.00, 1000, 3000, 12.00, 5, 'Access private data on social platforms.', 'A risky but lucrative crime targeting social platforms.', 4),
(15, 'Ransomware Attack', 18, 50.00, 3000, 9000, 20.00, 7, 'Lock systems and demand ransom.', 'Targeting businesses with high stakes ransomware attacks.', 4),
(16, 'Corporate Espionage', 25, 40.00, 5000, 15000, 25.00, 10, 'Steal secrets from corporations.', 'The rewards are immense, but the risk is higher.', 4),
(17, 'Spread Fake News', 10, 65.00, 1000, 3000, 10.00, 4, 'Manipulate opinions by creating fake reports.', 'Deceive the public with fabricated information.', 5),
(18, 'Rig Election', 20, 40.00, 5000, 15000, 25.00, 8, 'Alter the outcome of a local election.', 'Shaping politics comes with massive rewards and risks.', 5),
(19, 'Blackmail Politician', 25, 35.00, 8000, 20000, 30.00, 10, 'Use sensitive info to manipulate a leader.', 'Pressure politicians for money or influence.', 5),
(20, 'Embezzle Funds', 30, 30.00, 10000, 30000, 35.00, 12, 'Divert public money to private accounts.', 'An elaborate crime with huge stakes and rewards.', 5),
(21, 'Infiltrate Base', 20, 50.00, 7000, 15000, 20.00, 7, 'Gain access to a government facility.', 'Entering high-security areas is no easy feat.', 6),
(22, 'Steal Classified Files', 25, 45.00, 10000, 20000, 25.00, 9, 'Retrieve sensitive documents.', 'The rewards are big, but the risks are bigger.', 6),
(23, 'Assassinate Target', 30, 40.00, 20000, 40000, 30.00, 12, 'Eliminate a high-value individual.', 'The ultimate risk with unmatched rewards.', 6),
(24, 'Frame Rival Agent', 35, 35.00, 25000, 50000, 35.00, 15, 'Plant evidence to ruin another operative.', 'A clever crime that requires precision and wit.', 6);

-- --------------------------------------------------------

--
-- Table structure for table `crime_categories`
--

CREATE TABLE `crime_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crime_categories`
--

INSERT INTO `crime_categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'Petty Crimes', NULL),
(2, 'Major Heists', NULL),
(3, 'Organized Crime', NULL),
(4, 'Cybercrime', NULL),
(5, 'Political Manipulation', NULL),
(6, 'Espionage', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `education_categories`
--

CREATE TABLE `education_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education_categories`
--

INSERT INTO `education_categories` (`category_id`, `name`, `description`) VALUES
(1, 'Business', 'Courses to enhance business skills and financial acumen.'),
(2, 'Science', 'Courses exploring scientific principles and technologies.'),
(3, 'Fitness', 'Courses to improve physical health and training knowledge.');

-- --------------------------------------------------------

--
-- Table structure for table `education_courses`
--

CREATE TABLE `education_courses` (
  `course_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `length` int(11) NOT NULL COMMENT 'Duration in hours/days',
  `reward` varchar(255) DEFAULT NULL COMMENT 'Reward for completing the course',
  `is_master` tinyint(1) DEFAULT 0 COMMENT '1 if this is the master course for the category'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education_courses`
--

INSERT INTO `education_courses` (`course_id`, `category_id`, `name`, `length`, `reward`, `is_master`) VALUES
(1, 1, 'Introduction to Accounting', 24, 'Unlock advanced finance jobs', 0),
(2, 1, 'Advanced Marketing Strategies', 48, 'Increase negotiation skill', 0),
(3, 1, 'Master of Business Administration', 72, 'Unlock master business jobs', 1),
(4, 2, 'Basic Chemistry', 24, 'Unlock access to chemical labs', 0),
(5, 2, 'Applied Physics', 48, 'Enhance crafting efficiency', 0),
(6, 2, 'Master of Science', 72, 'Unlock advanced scientific research', 1),
(7, 3, 'Fundamentals of Exercise', 24, 'Increase gym stat gains', 0),
(8, 3, 'Advanced Nutrition', 48, 'Boost energy regeneration', 0),
(9, 3, 'Fitness Mastery', 72, 'Unlock fitness-related perks', 1);

-- --------------------------------------------------------

--
-- Table structure for table `factions`
--

CREATE TABLE `factions` (
  `faction_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `rival_faction_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `factions`
--

INSERT INTO `factions` (`faction_id`, `name`, `description`, `rival_faction_id`) VALUES
(1, 'Mafia', 'An underground criminal organization.', NULL),
(2, 'Police', 'The law enforcement agency.', NULL),
(3, 'Religious', 'A group of devout individuals.', NULL),
(4, 'Scientific', 'Innovators and researchers.', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Weapon','Armor','Medical','Consumable','Special','Misc') NOT NULL,
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attributes`)),
  `is_tradeable` tinyint(1) DEFAULT 1,
  `is_consumable` tinyint(1) DEFAULT 0,
  `buy_price` int(11) DEFAULT 0,
  `sell_price` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `name`, `description`, `category`, `attributes`, `is_tradeable`, `is_consumable`, `buy_price`, `sell_price`) VALUES
(1, 'Health Potion', 'Restores 50 HP', 'Medical', '{\"heal\": 50}', 1, 1, 0, 0),
(2, 'Nerve Booster', 'Restores 5 Nerve', 'Consumable', '{\"nerve\": 5}', 1, 1, 0, 0),
(3, 'Energy Drink', 'Restores 20 Energy', 'Consumable', '{\"energy\": 20}', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `jail`
--

CREATE TABLE `jail` (
  `userid` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `jail_time_remaining` int(11) NOT NULL COMMENT 'Time in minutes',
  `last_update` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `name`, `description`) VALUES
(1, 'Medical', 'Work in the medical field, healing and saving lives.'),
(2, 'Education', 'Teach and inspire others through education.'),
(3, 'Grocer', 'Manage inventory and customers at a grocery store.');

-- --------------------------------------------------------

--
-- Table structure for table `job_positions`
--

CREATE TABLE `job_positions` (
  `position_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `min_intelligence` int(11) DEFAULT 0,
  `min_manual_labor` int(11) DEFAULT 0,
  `min_endurance` int(11) DEFAULT 0,
  `salary` int(11) NOT NULL,
  `intelligence_reward` int(11) DEFAULT 0,
  `manual_labor_reward` int(11) DEFAULT 0,
  `endurance_reward` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_positions`
--

INSERT INTO `job_positions` (`position_id`, `job_id`, `title`, `min_intelligence`, `min_manual_labor`, `min_endurance`, `salary`, `intelligence_reward`, `manual_labor_reward`, `endurance_reward`) VALUES
(1, 1, 'Nurse', 30, 10, 20, 500, 5, 2, 3),
(2, 1, 'Doctor', 70, 20, 40, 1000, 10, 4, 6),
(3, 1, 'Chief Surgeon', 90, 30, 60, 2000, 15, 5, 8),
(4, 2, 'Teacher', 40, 10, 10, 600, 0, 0, 0),
(5, 2, 'Principal', 80, 20, 20, 1200, 0, 0, 0),
(6, 2, 'Dean', 100, 30, 30, 2500, 0, 0, 0),
(7, 3, 'Cashier', 10, 30, 10, 400, 0, 0, 0),
(8, 3, 'Manager', 30, 50, 20, 800, 0, 0, 0),
(9, 3, 'Regional Manager', 50, 70, 30, 1500, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `loan_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `amount_borrowed` int(11) NOT NULL,
  `remaining_balance` int(11) NOT NULL,
  `daily_payment` int(11) NOT NULL,
  `due_date` date NOT NULL,
  `start_date` date NOT NULL,
  `last_interest_date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`loan_id`, `userid`, `amount_borrowed`, `remaining_balance`, `daily_payment`, `due_date`, `start_date`, `last_interest_date`) VALUES
(1, 1, 5000, 0, 550, '2025-02-01', '2025-01-22', '2025-01-22'),
(2, 1, 100, 0, 0, '2025-02-01', '2025-01-01', '2025-01-22'),
(3, 1, 500000, 0, 0, '2025-02-01', '2025-01-22', '2025-01-22'),
(4, 1, 1000, 0, 0, '2025-02-01', '2025-01-22', '2025-01-22'),
(5, 1, 500000, 0, 0, '2025-02-01', '2025-01-22', '2025-01-22'),
(6, 1, 50, 0, 0, '2025-02-01', '2025-01-22', '2025-01-22'),
(7, 1, 50000, 0, 0, '2025-02-01', '2025-01-22', '2025-01-22'),
(8, 1, 500000, 0, 0, '2025-02-07', '2025-01-28', '2025-01-28');

-- --------------------------------------------------------

--
-- Table structure for table `mail`
--

CREATE TABLE `mail` (
  `mail_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL COMMENT 'UserID of the sender',
  `receiver_id` int(11) NOT NULL COMMENT 'UserID of the receiver',
  `subject` varchar(255) NOT NULL COMMENT 'Subject of the message',
  `message` text NOT NULL COMMENT 'Content of the message',
  `sent_at` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Timestamp of when the message was sent',
  `read` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Whether the message has been read (0=No, 1=Yes)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mail`
--

INSERT INTO `mail` (`mail_id`, `sender_id`, `receiver_id`, `subject`, `message`, `sent_at`, `read`) VALUES
(2, 1, 1, 'Test Subject', 'Hello Justin.', '2025-01-24 21:40:25', 1),
(3, 1, 1, 'dojdowjd', 'wojdwojdwd', '2025-01-31 03:48:38', 0),
(4, 2, 1, 'iejifenkans', 'oudhnfknsdfk', '2025-01-31 03:49:05', 0);

-- --------------------------------------------------------

--
-- Table structure for table `npcs`
--

CREATE TABLE `npcs` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `page` varchar(50) NOT NULL,
  `file` varchar(50) NOT NULL,
  `faction_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `npcs`
--

INSERT INTO `npcs` (`id`, `name`, `description`, `page`, `file`, `faction_id`) VALUES
(1, 'John Flex', 'The energetic and motivational gym trainer.', 'gym', 'johnf.php', 1),
(2, 'Vignesh Chakraborty', 'Left-wing fanatic.', 'fup_office', 'vigneshc.php', 1);

-- --------------------------------------------------------

--
-- Table structure for table `npc_boosts`
--

CREATE TABLE `npc_boosts` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `npc` varchar(255) NOT NULL,
  `boost_end_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `npc_boosts`
--

INSERT INTO `npc_boosts` (`id`, `userid`, `npc`, `boost_end_time`) VALUES
(1, 1, 'John Flex', 1738869069),
(7, 2, 'John Flex', 1738816237);

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `max_happiness` int(11) NOT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `name`, `price`, `max_happiness`, `availability`, `description`) VALUES
(1, 'Shack', 50000, 500, 1, 'A humble shack for beginners.'),
(2, 'Apartment', 200000, 800, 1, 'A cozy apartment in the city.'),
(3, 'Villa', 800000, 1200, 1, 'A luxurious villa with a pool.'),
(4, 'Mansion', 5000000, 2000, 1, 'A grand mansion for the elite.'),
(5, 'Private Island', 20000000, 3000, 1, 'An exclusive private island.');

-- --------------------------------------------------------

--
-- Table structure for table `standings`
--

CREATE TABLE `standings` (
  `userid` int(11) NOT NULL,
  `faction_id` int(11) NOT NULL,
  `value` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `standings`
--

INSERT INTO `standings` (`userid`, `faction_id`, `value`) VALUES
(2, 1, 0.75),
(2, 2, 0),
(2, 3, 0),
(2, 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `energy` int(11) DEFAULT 100,
  `nerve` int(11) DEFAULT 0,
  `last_energy_update` datetime DEFAULT current_timestamp(),
  `last_nerve_update` datetime DEFAULT current_timestamp(),
  `strength` int(11) DEFAULT 1,
  `agility` int(11) DEFAULT 1,
  `defense` int(11) DEFAULT 1,
  `dexterity` int(11) DEFAULT 1,
  `happiness` int(11) DEFAULT 500,
  `crime_experience` int(11) DEFAULT 0,
  `last_happiness_update` datetime DEFAULT current_timestamp(),
  `intelligence` int(11) DEFAULT 1,
  `manual_labor` int(11) DEFAULT 1,
  `endurance` int(11) DEFAULT 1,
  `money` int(11) DEFAULT 500,
  `bank_balance` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `username`, `password`, `created_at`, `energy`, `nerve`, `last_energy_update`, `last_nerve_update`, `strength`, `agility`, `defense`, `dexterity`, `happiness`, `crime_experience`, `last_happiness_update`, `intelligence`, `manual_labor`, `endurance`, `money`, `bank_balance`) VALUES
(1, 'Justin', '$2y$10$pOV1/Ms6pndUR7t/u8eFrenrxQAG4uQlAOy2.h8Nvl.KrjP7f9a92', '2025-01-17 17:20:59', 0, 7, '2025-02-01 00:09:22', '2025-02-01 00:10:24', 1676, 555, 457, 423, 2930, 1056, '2025-02-01 00:10:25', 126, 48, 72, 1247593, 1476000),
(2, 'Joel', '$2y$10$MvJfCbX9ITwEoVffHNQvd.nhnpgRC/AQIwV9EKF1h6G0lAqsJKd/e', '2025-01-25 18:44:57', 100, 35, '2025-01-31 03:48:55', '2025-01-31 03:48:55', 32, 27, 17, 1, 3000, 5, '2025-01-31 03:48:55', 1, 1, 1, 0, 2147483647);

-- --------------------------------------------------------

--
-- Table structure for table `user_education`
--

CREATE TABLE `user_education` (
  `userid` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `progress` int(11) DEFAULT 0 COMMENT 'Progress percentage',
  `completed` tinyint(1) DEFAULT 0 COMMENT '1 if the course is completed',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_education`
--

INSERT INTO `user_education` (`userid`, `course_id`, `progress`, `completed`, `start_time`, `end_time`) VALUES
(1, 1, 0, 1, '2025-01-20 13:26:08', '2025-01-20 13:26:09'),
(1, 2, 0, 1, '2025-01-20 13:27:13', '2025-01-20 14:06:13'),
(1, 3, 0, 1, '2025-01-20 23:13:48', '2025-01-21 00:25:48'),
(1, 4, 0, 1, '2025-01-21 11:52:42', '2025-01-21 12:16:42'),
(1, 5, 0, 1, '2025-01-21 23:49:50', '2025-01-22 00:37:50'),
(1, 6, 0, 1, '2025-01-20 19:54:38', '2025-01-20 21:06:38'),
(1, 7, 0, 1, '2025-01-21 11:10:08', '2025-01-21 11:34:08'),
(1, 8, 0, 1, '2025-01-22 06:24:49', '2025-01-22 07:12:49'),
(1, 9, 0, 1, '2025-01-23 13:17:07', '2025-01-23 14:29:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_items`
--

CREATE TABLE `user_items` (
  `userid` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_items`
--

INSERT INTO `user_items` (`userid`, `item_id`, `quantity`) VALUES
(1, 3, 945);

-- --------------------------------------------------------

--
-- Table structure for table `user_jobs`
--

CREATE TABLE `user_jobs` (
  `userid` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `performance` decimal(5,2) DEFAULT 0.00,
  `last_pay_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_jobs`
--

INSERT INTO `user_jobs` (`userid`, `job_id`, `position_id`, `performance`, `last_pay_date`) VALUES
(1, 1, 3, 0.00, '2025-01-30 09:56:44'),
(2, 3, 1, 0.00, '2025-01-30 10:01:30');

-- --------------------------------------------------------

--
-- Table structure for table `user_properties`
--

CREATE TABLE `user_properties` (
  `userid` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `is_moved_in` tinyint(1) DEFAULT 0,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_properties`
--

INSERT INTO `user_properties` (`userid`, `property_id`, `is_moved_in`, `quantity`) VALUES
(1, 4, 0, 1),
(1, 1, 0, 1),
(1, 5, 1, 1),
(2, 5, 1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crimes`
--
ALTER TABLE `crimes`
  ADD PRIMARY KEY (`crime_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `crime_categories`
--
ALTER TABLE `crime_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `education_categories`
--
ALTER TABLE `education_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `education_courses`
--
ALTER TABLE `education_courses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `factions`
--
ALTER TABLE `factions`
  ADD PRIMARY KEY (`faction_id`),
  ADD KEY `rival_faction_id` (`rival_faction_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `jail`
--
ALTER TABLE `jail`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`);

--
-- Indexes for table `job_positions`
--
ALTER TABLE `job_positions`
  ADD PRIMARY KEY (`position_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`loan_id`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `mail`
--
ALTER TABLE `mail`
  ADD PRIMARY KEY (`mail_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `npcs`
--
ALTER TABLE `npcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_npcs_factions` (`faction_id`);

--
-- Indexes for table `npc_boosts`
--
ALTER TABLE `npc_boosts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userid` (`userid`,`npc`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `standings`
--
ALTER TABLE `standings`
  ADD PRIMARY KEY (`userid`,`faction_id`),
  ADD KEY `faction_id` (`faction_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_education`
--
ALTER TABLE `user_education`
  ADD PRIMARY KEY (`userid`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `user_items`
--
ALTER TABLE `user_items`
  ADD PRIMARY KEY (`userid`,`item_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `user_jobs`
--
ALTER TABLE `user_jobs`
  ADD PRIMARY KEY (`userid`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `position_id` (`position_id`);

--
-- Indexes for table `user_properties`
--
ALTER TABLE `user_properties`
  ADD KEY `property_id` (`property_id`),
  ADD KEY `user_properties_ibfk_1` (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `crimes`
--
ALTER TABLE `crimes`
  MODIFY `crime_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `crime_categories`
--
ALTER TABLE `crime_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `education_categories`
--
ALTER TABLE `education_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `education_courses`
--
ALTER TABLE `education_courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `factions`
--
ALTER TABLE `factions`
  MODIFY `faction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_positions`
--
ALTER TABLE `job_positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mail`
--
ALTER TABLE `mail`
  MODIFY `mail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `npcs`
--
ALTER TABLE `npcs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `npc_boosts`
--
ALTER TABLE `npc_boosts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `crimes`
--
ALTER TABLE `crimes`
  ADD CONSTRAINT `crimes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `crime_categories` (`category_id`);

--
-- Constraints for table `education_courses`
--
ALTER TABLE `education_courses`
  ADD CONSTRAINT `education_courses_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `education_categories` (`category_id`);

--
-- Constraints for table `factions`
--
ALTER TABLE `factions`
  ADD CONSTRAINT `factions_ibfk_1` FOREIGN KEY (`rival_faction_id`) REFERENCES `factions` (`faction_id`);

--
-- Constraints for table `job_positions`
--
ALTER TABLE `job_positions`
  ADD CONSTRAINT `job_positions_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`);

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`);

--
-- Constraints for table `mail`
--
ALTER TABLE `mail`
  ADD CONSTRAINT `mail_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `mail_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Constraints for table `npcs`
--
ALTER TABLE `npcs`
  ADD CONSTRAINT `fk_npcs_factions` FOREIGN KEY (`faction_id`) REFERENCES `factions` (`faction_id`);

--
-- Constraints for table `npc_boosts`
--
ALTER TABLE `npc_boosts`
  ADD CONSTRAINT `npc_boosts_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Constraints for table `standings`
--
ALTER TABLE `standings`
  ADD CONSTRAINT `standings_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `standings_ibfk_2` FOREIGN KEY (`faction_id`) REFERENCES `factions` (`faction_id`);

--
-- Constraints for table `user_education`
--
ALTER TABLE `user_education`
  ADD CONSTRAINT `user_education_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `user_education_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `education_courses` (`course_id`);

--
-- Constraints for table `user_items`
--
ALTER TABLE `user_items`
  ADD CONSTRAINT `user_items_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_jobs`
--
ALTER TABLE `user_jobs`
  ADD CONSTRAINT `user_jobs_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `user_jobs_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`),
  ADD CONSTRAINT `user_jobs_ibfk_3` FOREIGN KEY (`position_id`) REFERENCES `job_positions` (`position_id`);

--
-- Constraints for table `user_properties`
--
ALTER TABLE `user_properties`
  ADD CONSTRAINT `user_properties_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`),
  ADD CONSTRAINT `user_properties_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
