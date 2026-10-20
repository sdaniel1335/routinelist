CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` varchar(100) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `routines` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int UNSIGNED NOT NULL,
  `title` varchar(600) NOT NULL,
  `description` text NOT NULL,
  `rule` varchar(20) NOT NULL,
  `weekday` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `day_number` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `month_number` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `week_number` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `season` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `sort_order` smallint UNSIGNED NOT NULL DEFAULT 0,
  KEY `user_order` (`user_id`,`sort_order`),
  CONSTRAINT `fk_routines_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `routine_calendar` (
  `user_id` int UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `is_workday` tinyint UNSIGNED NOT NULL,
  PRIMARY KEY (`user_id`,`date`),
  CONSTRAINT `fk_routine_calendar_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- admin / admin
INSERT IGNORE INTO `users` (`username`,`password_hash`) VALUES ('admin','$2y$12$EOg47Nwzf6dRaggt3cx6YuD9v6p.pa.vJy96i1x/ZvsQGBr8uYkNe');
