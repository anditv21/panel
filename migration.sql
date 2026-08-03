-- Upgrade an existing panel database to the current dev schema.
-- This file is only needed for existing installations. New installations use DB.sql.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

ALTER TABLE `login`
  ADD COLUMN IF NOT EXISTS `twofactor_status` int(1) NOT NULL DEFAULT 0;

ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `twofactor_enabled` int(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS `bio` varchar(30) NOT NULL DEFAULT 'N/A';

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dcid` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `delivered` int(11) NOT NULL DEFAULT 0,
  `time` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `rate_limit_blocks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bucket` varchar(100) NOT NULL,
  `actor` varchar(191) NOT NULL,
  `blocked_until` int(10) UNSIGNED NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_bucket_actor_block` (`bucket`,`actor`),
  KEY `idx_blocked_until` (`blocked_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `rate_limit_counters` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bucket` varchar(100) NOT NULL,
  `actor` varchar(191) NOT NULL,
  `window_start` int(10) UNSIGNED NOT NULL,
  `request_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `last_hit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_bucket_actor_window` (`bucket`,`actor`,`window_start`),
  KEY `idx_window_start` (`window_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `variables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;
