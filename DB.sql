-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 19, 2022 at 12:24 PM
-- Server version: 10.3.36-MariaDB-0+deb10u2
-- PHP Version: 8.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `panel`
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
  `invites` int(1) NOT NULL DEFAULT 1,
  `injectcount` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cheat`
--

INSERT INTO `cheat` (`status`, `version`, `news`, `maintenance`, `frozen`, `freezingtime`, `invites`, `injectcount`) VALUES
(0, 1, 'Welcome to znixv2-panel-edit by anditv21!', 0, 1, 1666182207, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `invites`
--

CREATE TABLE `invites` (
  `code` varchar(255) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `invites`
--

INSERT INTO `invites` (`code`, `createdBy`, `createdAt`) VALUES
('7xWvD19OJoKUii8RbJ23', 'admin', '2022-10-01 17:33:54'),
('2y56jmudDZKJowBWeZZ9', 'admin', '2022-10-01 18:50:15'),
('ljJOJPArVSvCNw3KPUKl', 'admin', '2022-10-01 18:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `shoutbox`
--

CREATE TABLE `shoutbox` (
  `id` int(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `uid` int(255) NOT NULL,
  `msg` varchar(255) NOT NULL,
  `time` datetime(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `shoutbox`
--

INSERT INTO `shoutbox` (`id`, `user`, `uid`, `msg`, `time`) VALUES
(1, 'System', 0, 'Hi', '2022-08-22 02:24:14.863472');

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `code` varchar(255) NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `dcid` int(11) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `subscription`
--

INSERT INTO `subscription` (`code`, `used`, `dcid`, `createdBy`, `createdAt`) VALUES
('Trail-6DMUlLXHQ38YbaM2A9A2', 1, 0, 'admin', '2022-10-19 11:57:23');

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
  `dcid` int(11) NOT NULL,
  `frozen` int(1) NOT NULL,
  `banned` int(1) NOT NULL DEFAULT 0,
  `invitedBy` varchar(255) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `lastIP` varchar(255) DEFAULT NULL,
  `currentLogin` datetime DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `remembertoken` varchar(50) DEFAULT NULL,
  `banreason` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `password`, `hwid`, `admin`, `supp`, `sub`, `dcid`, `frozen`, `banned`, `invitedBy`, `createdAt`, `lastIP`, `currentLogin`, `lastLogin`, `remembertoken`, `banreason`) VALUES
(1, 'admin', '$2y$10$Z1lyC8jFwsuTs8AM3NjoledhS8R89xlid/vaTQMCrI2FOsaTih5X6', 'hwid', 1, 1, '2023-04-07', 0, 1, 0, '', '2022-07-05 22:04:37', NULL, '2022-10-19 13:20:18', '2022-10-19 08:20:54', '2acf9618be70312d41ea28f2720099a0', 'test');

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
-- Indexes for table `shoutbox`
--
ALTER TABLE `shoutbox`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD UNIQUE KEY `code` (`code`);

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
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
