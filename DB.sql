-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 25, 2023 at 09:18 PM
-- Server version: 10.3.38-MariaDB-0+deb10u1
-- PHP Version: 8.1.16

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

--
-- Dumping data for table `invites`
--

INSERT INTO `invites` (`code`, `createdBy`, `createdAt`) VALUES
('OnG7P0XG5cNpHBSGgu7x', 'admin', '2023-03-25 21:05:22');

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

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`username`, `remembertoken`, `ip`, `browser`, `os`, `time`) VALUES
('admin', '289228adb027722d5bad6992d05427e3', '127.0.0.1', 'Chrome', 'Windows 10', 'March 25 th, 21:44');

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `code` varchar(255) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subscription`
--

INSERT INTO `subscription` (`code`, `createdBy`, `createdAt`) VALUES
('1m-Ob72RqEIw64KwaXY9QVy', 'admin', '2023-03-25 21:14:31'),
('3m-wF01oPMkW8MwY0kMxLOb', 'admin', '2023-03-25 21:14:32'),
('Trail-LAjTXJeRC2jvwQxE0FVG', 'admin', '2023-03-25 21:14:32');

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
(33, 'admin', 'Flushed all logs', 'Chrome', 'Windows 10', '127.0.0.1', 'March 25 th, 21:45');

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
  `banreason` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `hwid`, `admin`, `supp`, `sub`, `frozen`, `banned`, `invitedBy`, `createdAt`, `lastIP`, `currentLogin`, `lastLogin`, `banreason`) VALUES
(1, 'admin', '$2y$10$7wOzYc.AXpXc1nE/b0IqLOsP2w1cK9LZXDUi6hoSyuWBDj3DoBjOK', NULL, 1, 1, '2023-01-30', 0, 0, '', '2022-07-05 22:04:37', '45.85.219.118', '2023-03-25 21:44:45', '2023-03-25 20:57:22', NULL);

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
-- AUTO_INCREMENT for table `userlogs`
--
ALTER TABLE `userlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
