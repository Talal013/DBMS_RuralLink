-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 26, 2025 at 11:27 AM
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
-- Database: `rurallink`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `area` varchar(255) DEFAULT NULL,
  `Job_type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_conversation`
--

CREATE TABLE `chat_conversation` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `channel_name` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `channel_name`, `is_read`, `timestamp`, `sent_at`) VALUES
(27, 1, 2, 'Hi', 'chat_channel_1_2', 0, '2025-01-25 07:16:19', '2025-01-25 02:16:19'),
(28, 2, 1, 'how are you?', 'chat_channel_1_2', 0, '2025-01-25 07:16:41', '2025-01-25 02:16:41'),
(29, 1, 2, 'I am fine. What about you?', 'chat_channel_1_2', 0, '2025-01-25 07:16:52', '2025-01-25 02:16:52'),
(30, 1, 10, 'hi', 'chat_channel_1_10', 0, '2025-01-25 08:43:37', '2025-01-25 03:43:37'),
(31, 10, 1, 'great', 'chat_channel_1_10', 0, '2025-01-25 08:43:45', '2025-01-25 03:43:45'),
(32, 1, 11, 'Hello trans', 'chat_channel_1_11', 0, '2025-01-25 08:47:05', '2025-01-25 03:47:05'),
(33, 11, 1, 'hi ', 'chat_channel_1_11', 0, '2025-01-25 08:47:13', '2025-01-25 03:47:13');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `job_type` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','completed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_posting`
--

CREATE TABLE `job_posting` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `job_type` varchar(255) DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `status` enum('pending','accepted','completed') DEFAULT 'pending',
  `agent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `accepted_by` int(11) DEFAULT NULL,
  `worker_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_posting`
--

INSERT INTO `job_posting` (`id`, `client_id`, `description`, `location`, `title`, `job_type`, `amount`, `status`, `agent_id`, `created_at`, `accepted_by`, `worker_id`) VALUES
(3, 2, 'Sapiente in sit seq', 'Consectetur quia of', 'Dolorem velit vitae ', 'Consequatur At qui ', 43.00, 'accepted', NULL, '2025-01-24 14:03:00', 1, NULL),
(4, 2, 'Sapiente in sit seqdf', 'Satkhira', 'Graphic Designer', 'Consequatur At qui ', 77.00, 'accepted', NULL, '2025-01-24 14:03:00', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_requests`
--

CREATE TABLE `job_requests` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(100) NOT NULL,
  `job_type` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('open','accepted','completed') DEFAULT 'open',
  `accepted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('mobile','cash') DEFAULT 'mobile',
  `agent_commission` decimal(10,2) DEFAULT NULL,
  `admin_commission` decimal(10,2) DEFAULT NULL,
  `worker_payment` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `worker_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `referred_by` int(11) NOT NULL,
  `referred_user` int(11) NOT NULL,
  `reward_points` int(11) DEFAULT 0,
  `reward_amount` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `referrals`
--

INSERT INTO `referrals` (`id`, `referred_by`, `referred_user`, `reward_points`, `reward_amount`, `created_at`) VALUES
(1, 1, 7, 10, 5.00, '2025-01-25 07:38:55'),
(2, 8, 9, 10, 5.00, '2025-01-25 07:48:28');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `reviewee_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `job_id`, `reviewer_id`, `reviewee_id`, `rating`, `comment`, `created_at`) VALUES
(9, 3, 2, 1, 4, 'comment from client', '2025-01-25 15:30:49'),
(10, 3, 1, 2, 5, 'comment from agent', '2025-01-25 15:31:32');

-- --------------------------------------------------------

--
-- Table structure for table `transportation`
--

CREATE TABLE `transportation` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('client','agent','worker','admin','transportation') NOT NULL,
  `referral_code` varchar(10) DEFAULT NULL,
  `referred_by` varchar(10) DEFAULT NULL,
  `reward_points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `nid` varchar(50) DEFAULT NULL,
  `status` enum('pending','approved') NOT NULL DEFAULT 'pending',
  `accepted_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `password`, `role`, `referral_code`, `referred_by`, `reward_points`, `created_at`, `first_name`, `last_name`, `phone`, `email`, `address`, `nid`, `status`, `accepted_by`) VALUES
(1, '$2y$10$TzPjZMLubEoRRrd6sKQUSOiIBTlYmVriZIizVp0e3/xZNRNSw4zoi', 'agent', '83449', NULL, 0, '2025-01-24 13:37:56', 'Mr', 'Agent', '01602646902', 'agent@gmail.com', '', '7657575', '', NULL),
(2, '$2y$10$bcTaes2J5K4om4snILtHIuquAHuUOMNxxiFn373CQUfhWZkUbNQYq', 'client', '78677', NULL, 0, '2025-01-24 13:38:51', 'Mr', 'Client', '345435435', 'client@gmail.com', '', '435345345', '', NULL),
(5, '$2y$10$e4UmG0RU5ZUaexkFXRT6keZ.oVEyqmjBx9CknxYtJ1p4FyjtN3Gay', 'worker', '65563', NULL, 0, '2025-01-25 04:30:49', 'mr', 'Worker', '+1 (513) 914-68', 'w@gmail.com', 'Old Satkhira, Alia Madrasa Para', '7657577657', '', NULL),
(7, '$2y$10$JfJx8/iNgndze4YqgsK/SeHwv7G7YrjprR92r6T51dSxiRrzIrhf2', 'client', '23160', NULL, 0, '2025-01-25 07:38:55', 'Maya', 'Padilla', '+1 (755) 828-88', 'difidasuxe@mailinator.com', '', '80867867', '', NULL),
(8, '$2y$10$AyCFMd3YtVOwJDQPwvUHnuKk5ScMZ85wVulAOOF/I9pBKUFOYhzAe', 'client', '32443', NULL, 0, '2025-01-25 07:46:47', 'Yuri', 'Livingston', '+1 (576) 696-82', 'tufiqipyw@mailinator.com', '', '8655666', '', NULL),
(9, '$2y$10$TzPjZMLubEoRRrd6sKQUSOiIBTlYmVriZIizVp0e3/xZNRNSw4zoi', 'worker', '39450', NULL, 0, '2025-01-25 07:48:28', 'Logan', 'Carey', '+1 (783) 157-39', 'pybyt@mailinator.com', 'Old Satkhira, Alia Madrasa Para', '6864566', '', NULL),
(11, '$2y$10$t3UluqlH8dSdDdNsv/yxHO31/noIv1gn/dHExDl/9Tz95hnYhr5T.', 'transportation', '37644', NULL, 0, '2025-01-25 08:45:14', 'Stephanie', 'Powell', '+1 (116) 923-88', 't@gmail.com', '', '56757556', 'approved', 1),
(12, '$2y$10$NmVN8eRNFAiRF1wjFVnbjOXLrAFYY5w2iiv/ctEufotvQWMzh8KWu', 'admin', '46330', NULL, 0, '2025-01-26 04:34:46', 'Garrett', 'Stephens', '+1 (966) 653-69', 'admin@gmail.com', '', '546546', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `worker`
--

CREATE TABLE `worker` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skills` text DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `rating` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `chat_conversation`
--
ALTER TABLE `chat_conversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `agent_id` (`agent_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `job_posting`
--
ALTER TABLE `job_posting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `fk_accepted_by` (`accepted_by`);

--
-- Indexes for table `job_requests`
--
ALTER TABLE `job_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `accepted_by` (`accepted_by`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `fk_worker_id` (`worker_id`);

--
-- Indexes for table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referred_by` (`referred_by`),
  ADD KEY `referred_user` (`referred_user`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `reviewer_id` (`reviewer_id`),
  ADD KEY `reviewee_id` (`reviewee_id`);

--
-- Indexes for table `transportation`
--
ALTER TABLE `transportation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `worker`
--
ALTER TABLE `worker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `agent`
--
ALTER TABLE `agent`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_conversation`
--
ALTER TABLE `chat_conversation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_posting`
--
ALTER TABLE `job_posting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `job_requests`
--
ALTER TABLE `job_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `transportation`
--
ALTER TABLE `transportation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `worker`
--
ALTER TABLE `worker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `job_posting`
--
ALTER TABLE `job_posting`
  ADD CONSTRAINT `fk_accepted_by` FOREIGN KEY (`accepted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_worker_id` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`);
COMMIT;



- Dashboard View for Aggregated Data
CREATE VIEW dashboard AS
SELECT 
    u.id AS user_id,
    u.username AS user_name,
    u.role,
    COUNT(DISTINCT jp.id) AS total_jobs_posted,
    COUNT(DISTINCT CASE WHEN jp.status = 'completed' THEN jp.id END) AS completed_jobs,
    COUNT(DISTINCT CASE WHEN jp.status = 'accepted' THEN jp.id END) AS ongoing_jobs,
    COUNT(DISTINCT r.id) AS total_reviews,
    AVG(r.rating) AS average_rating,
    SUM(DISTINCT p.agent_commission) AS total_agent_commission,
    SUM(DISTINCT p.admin_commission) AS total_admin_commission
FROM 
    users u
LEFT JOIN 
    client c ON u.id = c.user_id
LEFT JOIN 
    agent a ON u.id = a.user_id
LEFT JOIN 
    job_posting jp ON (jp.client_id = c.id OR jp.agent_id = a.id)
LEFT JOIN 
    reviews r ON jp.id = r.job_id
LEFT JOIN 
    payment p ON jp.id = p.job_id
GROUP BY 
    u.id, u.username, u.role;


