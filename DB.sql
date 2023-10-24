-- phpMyAdmin SQL Dump
-- version 5.0.4deb2+deb11u1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 24, 2023 at 06:33 PM
-- Server version: 10.5.21-MariaDB-0+deb11u1
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
('PXZwJcXf5zQ6myPg0zjJ', 'admin', '2023-09-27 09:06:56'),
('yOh20NjgdZ5ruCtH1m8X', 'admin', '2023-09-27 09:06:56');

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
  `note` varchar(255) NOT NULL
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
  `discordlogging` int(1) NOT NULL DEFAULT 0,
  `relinkdiscord` int(1) NOT NULL DEFAULT 1,
  `cap_service` int(1) NOT NULL DEFAULT 1,
  `cap_key` varchar(255) DEFAULT NULL,
  `cap_secret` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `system`
--

INSERT INTO `system` (`status`, `version`, `news`, `maintenance`, `frozen`, `freezingtime`, `invites`, `shoutbox`, `discordlinking`, `discordlogging`, `relinkdiscord`, `cap_service`, `cap_key`, `cap_secret`) VALUES
(0, 1, 'Welcome to znixv2-panel-edit by anditv21!', 0, 0, 0, 1, 0, 1, 0, 1, 0, 'test', 'test2');

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
(435, 'admin', 'Flushed all logs', 'Google Chrome', 'Windows 10', 'localhost', 'October 24 th, 19:28');

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
  `muted` int(1) NOT NULL DEFAULT 0,
  `loginfails` int(255) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uid`, `username`, `displayname`, `password`, `hwid`, `admin`, `supp`, `sub`, `username_change`, `frozen`, `banned`, `invitedBy`, `createdAt`, `lastIP`, `currentLogin`, `lastLogin`, `banreason`, `resetcount`, `lastreset`, `invites`, `invitescount`, `discord_access_token`, `discord_refresh_token`, `dcid`, `muted`, `loginfails`) VALUES
(1, 'admin', 'andi_arbeit', '$2y$10$7wOzYc.AXpXc1nE/b0IqLOsP2w1cK9LZXDUi6hoSyuWBDj3DoBjOK', NULL, 1, 1, '2023-06-01', NULL, 0, 0, '', '2022-07-05 22:04:37', 'localhost', '2023-10-24 19:28:46', '2023-10-24 19:25:12', 'none', 13, '2023-07-30', 26, 0, NULL, NULL, NULL, 0, 0),
(2, 'admin2', NULL, '$argon2i$v=19$m=65536,t=4,p=1$dUNwRW5vNkJ1S1FubGJjRg$0hKtX7rVveuPpCeatmqb2iX55kEo/qBERXkZkiGGJ8E', NULL, 0, 0, '2089-04-28', NULL, 0, 0, 'System', '2023-07-01 14:06:00', 'localhost', '2023-08-13 12:49:39', '2023-08-12 22:49:20', 'none', 0, NULL, 15, 0, NULL, NULL, NULL, 0, 0);

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `shoutbox`
--
ALTER TABLE `shoutbox`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `userlogs`
--
ALTER TABLE `userlogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=436;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
