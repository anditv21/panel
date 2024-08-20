-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 20, 2024 at 05:45 PM
-- Server version: 10.5.21-MariaDB-0+deb11u1
-- PHP Version: 8.2.20

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
-- Table structure for table `adminlogs`
--

CREATE TABLE `adminlogs` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adminlogs`
--

INSERT INTO `adminlogs` (`id`, `username`, `action`, `ip`, `time`) VALUES
(27, 'admin', 'Turned discord re-link on', 'localhost', 'May 14 th, 16:02'),
(28, 'admin', 'Changed captcha service to 1', 'localhost', 'May 14 th, 16:02'),
(30, 'admin', 'Unbanned admin2 (2)', 'localhost', 'May 18 th, 21:07'),
(31, 'admin', 'Set the System status to offline', 'localhost', 'August 15 th, 1:01'),
(32, 'admin', 'Changed captcha service to 1', 'localhost', 'August 15 th, 1:01'),
(33, 'admin', 'Set the System status to online', 'localhost', 'August 15 th, 1:01'),
(34, 'admin', 'Changed captcha service to 1', 'localhost', 'August 15 th, 1:01'),
(35, 'admin', 'Unbanned admin2 (2)', 'localhost', 'August 15 th, 1:05'),
(36, 'admin', 'Unbanned admin2 (2)', 'localhost', 'August 15 th, 1:05'),
(37, 'admin', 'Deleted invitation with code GrwI5IwUMjOGPxj', 'localhost', 'August 15 th, 1:07'),
(38, 'admin', 'Generated an subscription code', 'localhost', 'August 15 th, 1:07'),
(39, 'admin', 'Changed captcha service to 0', 'localhost', 'August 15 th, 1:15');

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
('PXZwJcXf5zQ6myPg0zjJ', 'admin', '2023-09-27 09:06:56');

-- --------------------------------------------------------

--
-- Table structure for table `ip_whitelist`
--

CREATE TABLE `ip_whitelist` (
  `ip` varchar(255) NOT NULL,
  `createdBy` varchar(255) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `remembertoken` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `os` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `note` varchar(255) NOT NULL,
  `createdAt` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `remembertoken`, `ip`, `browser`, `os`, `time`, `note`, `createdAt`) VALUES
(92, 'test', '6bb0ec30b605e97f199e2f50fcb5cc35', 'localhost', 'Brave', 'Windows 10', '2024-08-16 01:09:27', 'none', '2024-08-15'),
(93, 'test', 'f203b79159666eb524f06c80d0566382', 'localhost', 'Brave', 'Windows 10', '2024-08-16 01:14:30', 'none', '2024-08-15'),
(94, 'test2', 'e9e0e3258d0c0527a92d536dffbb4edd', 'localhost', 'Brave', 'Windows 10', '2024-08-20 19:17:43', 'none', '2024-08-15'),
(95, 'admin', '8d53ff4b0d2af6fe36ee624b2eba9a31', 'localhost', 'Brave', 'Windows 10', '2024-08-20 19:17:55', 'none', '2024-08-20'),
(96, 'admin', '3f5d0ee5a49c191f174414256d2f2fb2', 'localhost', 'Brave', 'Windows 10', 'August 20 th, 19:35', 'none', '2024-08-20');

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
(1, 'ShoutBox flushed by an admin.', 'Aug 12, 8:07 pm', 35);

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
('Trail-bz6eCE8aovijilgrAulx', 'admin', '2024-08-14 23:07:47');

-- --------------------------------------------------------

--
-- Table structure for table `system`
--

CREATE TABLE `system` (
  `status` int(1) NOT NULL DEFAULT 0,
  `version` float NOT NULL DEFAULT 0,
  `news` varchar(255) NOT NULL DEFAULT '0',
  `maintenance` int(1) NOT NULL DEFAULT 0,
  `frozen` int(1) NOT NULL DEFAULT 0,
  `freezingtime` int(13) NOT NULL,
  `invites` int(1) NOT NULL DEFAULT 1,
  `shoutbox` int(11) NOT NULL DEFAULT 1,
  `discordlinking` int(1) NOT NULL DEFAULT 1,
  `discordlogging` int(1) NOT NULL DEFAULT 0,
  `relinkdiscord` int(1) NOT NULL DEFAULT 1,
  `cap_service` int(1) NOT NULL DEFAULT 1,
  `cap_key` varchar(255) DEFAULT NULL,
  `cap_secret` varchar(255) DEFAULT NULL,
  `embed_color` varchar(7) NOT NULL DEFAULT 'F03BEA'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `system`
--

INSERT INTO `system` (`status`, `version`, `news`, `maintenance`, `frozen`, `freezingtime`, `invites`, `shoutbox`, `discordlinking`, `discordlogging`, `relinkdiscord`, `cap_service`, `cap_key`, `cap_secret`, `embed_color`) VALUES
(0, 1, 'Welcome to the panel made by anditv21!', 0, 0, 0, 1, 0, 1, 0, 1, 0, NULL, NULL, 'ff00dd');

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
(580, 'admin2', 'Banned by admin', 'Brave', 'Windows 10', 'Staff/System', 'August 15 th, 1:05'),
(581, 'admin2', 'Unbanned by admin', 'Brave', 'Windows 10', 'Staff/System', 'August 15 th, 1:05'),
(586, 'test', 'Logged in', 'Brave', 'Windows 10', 'localhost', 'August 16 th, 1:09'),
(587, 'test', 'Login', 'Brave', 'Windows 10', 'localhost', 'August 16 th, 1:09'),
(588, 'test', 'Changed password', 'Brave', 'Windows 10', 'localhost', 'August 16 th, 1:11'),
(589, 'test', 'Logged in', 'Brave', 'Windows 10', 'localhost', 'August 16 th, 1:14'),
(590, 'test', 'Login', 'Brave', 'Windows 10', 'localhost', 'August 16 th, 1:14'),
(591, 'test2', 'Logged in', 'Brave', 'Windows 10', 'localhost', 'August 16 th, 1:35'),
(592, 'test2', 'Login', 'Brave', 'Windows 10', 'localhost', 'August 16 th, 1:35'),
(593, 'test2', 'Logged in via cookie', 'Brave', 'Windows 10', 'localhost', 'August 20 th, 19:17'),
(594, 'test2', 'Login', 'Brave', 'Windows 10', 'localhost', 'August 20 th, 19:17'),
(598, 'admin', 'Flushed all logs', 'Brave', 'Windows 10', 'localhost', 'August 20 th, 19:35'),
(599, 'admin', 'Logged in', 'Brave', 'Windows 10', 'localhost', 'August 20 th, 19:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `displayname` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `hwid` varchar(255) DEFAULT NULL,
  `admin` int(1) NOT NULL DEFAULT 0,
  `supp` int(1) NOT NULL DEFAULT 0,
  `sub` date DEFAULT NULL,
  `username_change` date DEFAULT NULL,
  `frozen` int(1) NOT NULL DEFAULT 0,
  `banned` int(1) NOT NULL DEFAULT 0,
  `invitedBy` varchar(255) NOT NULL,
  `createdAt` timestamp NULL DEFAULT current_timestamp(),
  `lastIP` varchar(255) DEFAULT NULL,
  `currentLogin` varchar(255) DEFAULT NULL,
  `lastLogin` varchar(255) DEFAULT NULL,
  `banreason` varchar(255) DEFAULT NULL,
  `resetcount` int(10) DEFAULT 0,
  `lastreset` text DEFAULT NULL,
  `invites` int(11) NOT NULL DEFAULT 0,
  `invitescount` int(11) NOT NULL DEFAULT 0,
  `discord_access_token` varchar(255) DEFAULT NULL,
  `discord_refresh_token` varchar(255) DEFAULT NULL,
  `dcid` varchar(255) DEFAULT NULL,
  `muted` int(1) NOT NULL DEFAULT 0,
  `loginfails` int(255) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `displayname`, `password`, `hwid`, `admin`, `supp`, `sub`, `username_change`, `frozen`, `banned`, `invitedBy`, `createdAt`, `lastIP`, `currentLogin`, `lastLogin`, `banreason`, `resetcount`, `lastreset`, `invites`, `invitescount`, `discord_access_token`, `discord_refresh_token`, `dcid`, `muted`, `loginfails`) VALUES
(1, 'admin', 'andi_arbeit', '$2y$10$7wOzYc.AXpXc1nE/b0IqLOsP2w1cK9LZXDUi6hoSyuWBDj3DoBjOK', 'fetter_bauer-c26b15bf-a96c', 1, 1, '2089-06-17', NULL, 0, 0, '', '2022-07-05 22:04:37', 'localhost', '2024-08-20 19:17:55', '2024-08-16 01:01:10', 'none', 13, '2023-07-30', 17, 0, NULL, NULL, NULL, 0, 0),
(2, 'admin2', NULL, '$argon2i$v=19$m=65536,t=4,p=1$SGkxUDJoU083enM2anBRNQ$Vnyxq9VgVQjNAVo8ugf50LjiUwrmcPAd8g/IdaERg/8', NULL, 0, 0, '2089-08-31', NULL, 0, 0, 'System', '2023-07-01 14:06:00', 'localhost', '2023-08-13 12:49:39', '2023-08-12 22:49:20', 'none', 1, '2024-05-13', 15, 0, NULL, NULL, NULL, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adminlogs`
--
ALTER TABLE `adminlogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invites`
--
ALTER TABLE `invites`
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `ip_whitelist`
--
ALTER TABLE `ip_whitelist`
  ADD UNIQUE KEY `ip` (`ip`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`),
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
-- Indexes for table `system`
--
ALTER TABLE `system`
  ADD PRIMARY KEY (`freezingtime`);

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
-- AUTO_INCREMENT for table `adminlogs`
--
ALTER TABLE `adminlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `shoutbox`
--
ALTER TABLE `shoutbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `userlogs`
--
ALTER TABLE `userlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=600;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
