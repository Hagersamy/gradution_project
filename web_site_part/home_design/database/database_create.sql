-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 12, 2024 at 09:19 PM
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
-- Database: `android_pentest_academy`
--

-- --------------------------------------------------------

--
-- Table structure for table `functionality_for_roles`
--

CREATE TABLE `functionality_for_roles` (
  `id` int(11) NOT NULL,
  `role` enum('Hacker','Creator','Admin','Support') NOT NULL,
  `create_lab` tinyint(1) DEFAULT 0,
  `approve_lab` tinyint(1) DEFAULT 0,
  `resolve_tickets` tinyint(1) DEFAULT 0,
  `simulate_attacks` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `functionality_for_roles`
--

INSERT INTO `functionality_for_roles` (`id`, `role`, `create_lab`, `approve_lab`, `resolve_tickets`, `simulate_attacks`) VALUES
(1, 'Hacker', 0, 0, 0, 1),
(2, 'Creator', 1, 0, 0, 1),
(3, 'Admin', 1, 1, 1, 1),
(4, 'Support', 0, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `labs`
--

CREATE TABLE `labs` (
  `lab_id` int(11) NOT NULL,
  `labname` varchar(255) NOT NULL,
  `laburl` varchar(255) NOT NULL,
  `severity` enum('Low','Medium','High','Critical') NOT NULL,
  `Lab_score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `labs`
--

INSERT INTO `labs` (`lab_id`, `labname`, `laburl`, `severity`, `Lab_score`) VALUES
(1, 'SQL Injection Lab', 'https://academy.example.com/sql-injection', 'High', 0),
(2, 'XSS Lab', 'https://academy.example.com/xss-lab', 'Medium', 0),
(3, 'CSRF Lab', 'https://academy.example.com/csrf-lab', 'Low', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `role` enum('Hacker','Creator','Admin','Support') NOT NULL,
  `resetpass` varchar(450) DEFAULT NULL,
  `is_subscribed` tinyint(1) DEFAULT 0,
  `score` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `age`, `role`, `resetpass`, `is_subscribed`, `score`) VALUES
(1, 'john_doe', 'john@example.com', 'hashed_password', 25, 'Hacker', '0', 1, 0),
(2, 'creator_01', 'creator@example.com', 'hashed_password', 30, 'Creator', '0', 1, 0),
(3, 'admin_guru', 'admin@example.com', 'hashed_password', 40, 'Admin', '0', 1, 0),
(4, 'support_pro', 'support@example.com', 'hashed_password', 28, 'Support', '0', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_labs`
--

CREATE TABLE `user_labs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `lab_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `functionality_for_roles`
--
ALTER TABLE `functionality_for_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `labs`
--
ALTER TABLE `labs`
  ADD PRIMARY KEY (`lab_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_labs`
--
ALTER TABLE `user_labs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lab_id` (`lab_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `functionality_for_roles`
--
ALTER TABLE `functionality_for_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `labs`
--
ALTER TABLE `labs`
  MODIFY `lab_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_labs`
--
ALTER TABLE `user_labs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_labs`
--
ALTER TABLE `user_labs`
  ADD CONSTRAINT `user_labs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_labs_ibfk_2` FOREIGN KEY (`lab_id`) REFERENCES `labs` (`lab_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
