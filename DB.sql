-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 25, 2023 at 07:53 PM
-- Server version: 10.3.38-MariaDB-0+deb10u1
-- PHP Version: 8.1.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `panel-edit`
--

-- --------------------------------------------------------

--
-- Table structure for table `cheat`
--

CREATE TABLE `cheat` (
  `status` int(1) NOT NULL DEFAULT 0,
  `version` float NOT NULL DEFAULT 0,
  `news` varchar(255) NOT NULL DEFAULT '0',
  `maintenance` int(1) NOT NULL DEFAULT 0,
  `frozen` int(1) NOT NULL DEFAULT 0,
  `freezingtime` int(13) NOT NULL,
  `invites` int(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cheat`
--

INSERT INTO `cheat` (`status`, `version`, `news`, `maintenance`, `frozen`, `freezingtime`, `invites`) VALUES
(0, 1, 'Welcome to znixv2-panel-edit by anditv21!', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `invites`
--

CREATE TABLE `invites` (
  `code` varchar(255) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `username` varchar(255) NOT NULL,
  `remembertoken` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shoutbox`
--

CREATE TABLE `shoutbox` (
  `uid` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shoutbox`
--

INSERT INTO `shoutbox` (`uid`, `message`, `time`, `id`) VALUES
(1, 'ShoutBox flushed by an admin.', 'Apr 25, 9:48 pm', 24);

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `code` varchar(255) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userlogs`
--

CREATE TABLE `userlogs` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userlogs`
--

INSERT INTO `userlogs` (`id`, `username`, `action`, `browser`, `os`, `ip`, `time`) VALUES
(75, 'admin', 'Flushed all logs', 'Chrome', 'Windows 10', '127.0.0.1', 'April 22 nd, 19:19'),
(76, 'admin', 'Login', 'Chrome', 'Windows 10', '127.0.0.1', 'April 22 nd, 19:26'),
(77, 'admin', 'Deleted token b7644388be236e53de9cc3502c3cf7bb', 'Chrome', 'Windows 10', '127.0.0.1', 'April 22 nd, 19:34'),
(78, 'admin', 'Login', 'Chrome', 'Windows 10', '127.0.0.1', 'April 24 th, 14:55'),
(79, 'admin', 'Login', 'Chrome', 'Windows 10', '127.0.0.1', 'April 25 th, 8:40'),
(80, 'admin', 'Deleted token a4188cc700b2663b98037e30e524d949', 'Chrome', 'Windows 10', '127.0.0.1', 'April 25 th, 8:41'),
(81, 'admin', 'Login', 'Chrome', 'Windows 10', '127.0.0.1', 'April 25 th, 13:09'),
(82, 'admin', 'Login', 'Chrome', 'Windows 10', '127.0.0.1', 'April 25 th, 14:26'),
(83, 'admin', 'Login', 'Chrome', 'Windows 10', '127.0.0.1', 'April 25 th, 18:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `hwid` varchar(255) DEFAULT NULL,
  `admin` int(1) NOT NULL DEFAULT 0,
  `supp` int(1) NOT NULL DEFAULT 0,
  `sub` date DEFAULT NULL,
  `frozen` int(1) NOT NULL DEFAULT 0,
  `banned` int(1) NOT NULL DEFAULT 0,
  `invitedBy` varchar(255) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `lastIP` varchar(255) DEFAULT NULL,
  `currentLogin` datetime DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `banreason` varchar(255) DEFAULT NULL,
  `resetcount` int(10) DEFAULT 0,
  `lastreset` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `hwid`, `admin`, `supp`, `sub`, `frozen`, `banned`, `invitedBy`, `createdAt`, `lastIP`, `currentLogin`, `lastLogin`, `banreason`, `resetcount`, `lastreset`) VALUES
(1, 'admin', '$2y$10$7wOzYc.AXpXc1nE/b0IqLOsP2w1cK9LZXDUi6hoSyuWBDj3DoBjOK', NULL, 1, 1, '2023-07-15', 0, 0, '', '2022-07-05 22:04:37', '127.0.0.1', '2022-09-16 18:20:32', '1899-07-14 14:26:44', 'none', 10, '2023-04-22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cheat`
--
ALTER TABLE `cheat`
  ADD PRIMARY KEY (`freezingtime`);

--
-- Indexes for table `invites`
--
ALTER TABLE `invites`
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD UNIQUE KEY `remembertoken` (`remembertoken`);

--
-- Indexes for table `shoutbox`
--
ALTER TABLE `shoutbox`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_2` (`id`),
  ADD KEY `id` (`id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `userlogs`
--
ALTER TABLE `userlogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `uid` (`uid`),
  ADD UNIQUE KEY `hwid` (`hwid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `shoutbox`
--
ALTER TABLE `shoutbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `userlogs`
--
ALTER TABLE `userlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
