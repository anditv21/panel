-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 18, 2023 at 05:04 PM
-- Server version: 10.5.19-MariaDB-0+deb11u2
-- PHP Version: 7.4.33

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
('ucT4mRGCjfPhmy5', 'admin', '2023-07-01 14:05:14'),
('uQlxfN9b8eWE1l6', 'admin', '2023-07-25 11:46:13'),
('zJzs49Z92tvnRbg', 'admin', '2023-07-25 11:46:13'),
('yce3USspTOquBiB', 'admin', '2023-07-25 11:46:13'),
('R5zYVZAhQT4b52Y', 'admin', '2023-08-13 10:51:25');

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
  `note` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `remembertoken`, `ip`, `browser`, `os`, `time`, `note`) VALUES
(9, 'admin2', '98d2f2fb8bc439b4c97d693365581299', 'localhost', 'Chrome', 'Windows 10', 'August 12 th, 22:45', 'none'),
(11, 'admin2', '7874dcb8ea1a362aa21b6a79c26f7c6b', 'localhost', 'Chrome', 'Windows 10', 'August 13 th, 12:49', 'none'),
(21, 'admin', 'd8011300858122bc23bb612daa8abc47', 'localhost', 'Chrome', 'Windows 10', 'August 18 th, 19:03', 'none');

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
  `discordlogging` int(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `system`
--

INSERT INTO `system` (`status`, `version`, `news`, `maintenance`, `frozen`, `freezingtime`, `invites`, `shoutbox`, `discordlinking`, `discordlogging`) VALUES
(0, 1, 'Welcome to znixv2-panel-edit by anditv21!', 0, 0, 0, 1, 0, 0, 0);

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
(278, 'admin2', 'Flushed all logs', 'Chrome', 'Windows 10', 'localhost', 'August 12 th, 22:46'),
(279, 'admin2', 'Logged in', 'Chrome', 'Windows 10', 'localhost', 'August 12 th, 22:49'),
(280, 'admin2', 'Login', 'Chrome', 'Windows 10', 'localhost', 'August 12 th, 22:49'),
(281, 'admin2', 'Unbanned by admin', 'Chrome', 'Windows 10', 'Staff/System', 'August 12 th, 22:55'),
(282, 'admin2', 'Muted by admin', 'Chrome', 'Windows 10', 'Staff/System', 'August 12 th, 22:58'),
(283, 'admin2', 'Mute removed by admin', 'Chrome', 'Windows 10', 'Staff/System', 'August 12 th, 22:58'),
(284, 'admin2', 'Banned by admin', 'Chrome', 'Windows 10', 'Staff/System', 'August 12 th, 23:00'),
(289, 'admin2', 'Unbanned by admin', 'Chrome', 'Windows 10', 'Staff/System', 'August 13 th, 12:49'),
(293, 'admin2', 'Banned by admin', 'Chrome', 'Windows 10', 'Staff/System', 'August 13 th, 12:51'),
(294, 'admin2', 'Unbanned by admin', 'Chrome', 'Windows 10', 'Staff/System', 'August 13 th, 12:52'),
(322, 'admin', 'Flushed all logs', 'Chrome', 'Windows 10', 'localhost', 'August 18 th, 19:03'),
(323, 'admin', 'Logged out of other devices', 'Chrome', 'Windows 10', 'localhost', 'August 18 th, 19:03');

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
  `currentLogin` datetime DEFAULT NULL,
  `lastLogin` datetime DEFAULT NULL,
  `banreason` varchar(255) DEFAULT NULL,
  `resetcount` int(10) DEFAULT 0,
  `lastreset` text DEFAULT NULL,
  `invites` int(11) NOT NULL DEFAULT 0,
  `invitescount` int(11) NOT NULL DEFAULT 0,
  `discord_access_token` varchar(255) DEFAULT NULL,
  `discord_refresh_token` varchar(255) DEFAULT NULL,
  `dcid` varchar(255) DEFAULT NULL,
  `muted` int(1) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `displayname`, `password`, `hwid`, `admin`, `supp`, `sub`, `username_change`, `frozen`, `banned`, `invitedBy`, `createdAt`, `lastIP`, `currentLogin`, `lastLogin`, `banreason`, `resetcount`, `lastreset`, `invites`, `invitescount`, `discord_access_token`, `discord_refresh_token`, `dcid`, `muted`) VALUES
(1, 'admin', 'andi_arbeit', '$2y$10$7wOzYc.AXpXc1nE/b0IqLOsP2w1cK9LZXDUi6hoSyuWBDj3DoBjOK', 'e7b81f23-815f-433f-8cb7-bbb5c41596ef', 1, 1, '2023-08-01', NULL, 0, 0, '', '2022-07-05 22:04:37', 'localhost', '2023-08-18 19:03:30', '2023-08-18 18:25:11', 'none', 13, '2023-07-30', 26, 0, 'TiBCBHgMXBvzj4vy7PCRKf0CMPBjmx', 'EOFn7RNO3kzfUdhYR3GhdESXilzJRm', '854024514781315082', 0),
(2, 'admin2', NULL, '$argon2i$v=19$m=65536,t=4,p=1$dUNwRW5vNkJ1S1FubGJjRg$0hKtX7rVveuPpCeatmqb2iX55kEo/qBERXkZkiGGJ8E', NULL, 0, 0, '2089-04-28', NULL, 0, 0, 'System', '2023-07-01 14:06:00', 'localhost', '2023-08-13 12:49:39', '2023-08-12 22:49:20', 'none', 0, NULL, 15, 0, NULL, '', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `invites`
--
ALTER TABLE `invites`
  ADD UNIQUE KEY `code` (`code`);

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
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `shoutbox`
--
ALTER TABLE `shoutbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `userlogs`
--
ALTER TABLE `userlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=324;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
