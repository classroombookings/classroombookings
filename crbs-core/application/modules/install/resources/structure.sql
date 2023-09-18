
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

SET NAMES utf8mb4;

CREATE TABLE `academicyears` (
  `date_start` date NOT NULL,
  `date_end` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `access_control` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `target` char(1) CHARACTER SET utf8 NOT NULL,
  `target_id` int(6) unsigned NOT NULL,
  `actor` char(1) CHARACTER SET utf8 NOT NULL,
  `actor_id` int(6) unsigned DEFAULT NULL,
  `permission` varchar(32) CHARACTER SET utf8 NOT NULL,
  `reference` char(56) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_reference` (`reference`),
  KEY `idx_target_id` (`target`,`target_id`),
  KEY `idx_actor_id` (`actor`,`actor_id`),
  KEY `idx_permission` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `bookings` (
  `booking_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `repeat_id` int(6) unsigned DEFAULT NULL,
  `session_id` int(6) unsigned DEFAULT NULL,
  `period_id` int(6) unsigned NOT NULL,
  `room_id` int(6) unsigned NOT NULL,
  `user_id` int(6) unsigned DEFAULT NULL,
  `department_id` int(6) unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 10,
  `notes` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cancel_reason` text CHARACTER SET utf8 DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by` int(6) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(6) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(6) unsigned DEFAULT NULL,
  PRIMARY KEY (`booking_id`),
  KEY `fk_bookings_repeat` (`repeat_id`),
  KEY `fk_bookings_session` (`session_id`),
  KEY `fk_bookings_period` (`period_id`),
  KEY `fk_bookings_room` (`room_id`),
  KEY `fk_bookings_user` (`user_id`),
  KEY `fk_bookings_department` (`department_id`),
  KEY `fk_bookings_created_user` (`created_by`),
  KEY `fk_bookings_updated_user` (`updated_by`),
  CONSTRAINT `fk_bookings_created_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_period` FOREIGN KEY (`period_id`) REFERENCES `periods` (`period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_repeat` FOREIGN KEY (`repeat_id`) REFERENCES `bookings_repeat` (`repeat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_updated_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `bookings_repeat` (
  `repeat_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(6) unsigned DEFAULT NULL,
  `period_id` int(6) unsigned NOT NULL,
  `room_id` int(6) unsigned NOT NULL,
  `user_id` int(6) unsigned DEFAULT NULL,
  `department_id` int(6) unsigned DEFAULT NULL,
  `week_id` int(6) unsigned NOT NULL,
  `weekday` tinyint(1) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT 10,
  `notes` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cancel_reason` text CHARACTER SET utf8 DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancelled_by` int(6) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(6) unsigned DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(6) unsigned DEFAULT NULL,
  PRIMARY KEY (`repeat_id`),
  KEY `fk_bookings_repeat_session` (`session_id`),
  KEY `fk_bookings_repeat_period` (`period_id`),
  KEY `fk_bookings_repeat_room` (`room_id`),
  KEY `fk_bookings_repeat_user` (`user_id`),
  KEY `fk_bookings_repeat_department` (`department_id`),
  KEY `fk_bookings_repeat_week` (`week_id`),
  KEY `fk_bookings_repeat_created_user` (`created_by`),
  KEY `fk_bookings_repeat_updated_user` (`updated_by`),
  CONSTRAINT `fk_bookings_repeat_created_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_repeat_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_repeat_period` FOREIGN KEY (`period_id`) REFERENCES `periods` (`period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_repeat_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_repeat_session` FOREIGN KEY (`session_id`) REFERENCES `sessions` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_repeat_updated_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_repeat_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_bookings_repeat_week` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`week_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `dates` (
  `date` date NOT NULL,
  `weekday` tinyint(1) NOT NULL,
  `session_id` int(6) unsigned DEFAULT NULL,
  `week_id` int(6) unsigned DEFAULT NULL,
  `holiday_id` int(6) unsigned DEFAULT NULL,
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `departments` (
  `department_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `holidays` (
  `holiday_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(6) unsigned DEFAULT NULL,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  PRIMARY KEY (`holiday_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `lang` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `language` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT 'english',
  `set` varchar(255) CHARACTER SET utf8 NOT NULL,
  `key` varchar(255) CHARACTER SET utf8 NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `language_set` (`language`,`set`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `multi_bookings` (
  `mb_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(6) unsigned NOT NULL,
  `session_id` int(6) unsigned NOT NULL,
  `week_id` int(6) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `type` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `booking_user_id` int(6) unsigned DEFAULT NULL,
  `booking_department_id` int(6) unsigned DEFAULT NULL,
  `booking_notes` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`mb_id`),
  KEY `fk_mb_user` (`user_id`),
  CONSTRAINT `fk_mb_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `multi_bookings_slots` (
  `mbs_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mb_id` int(6) unsigned NOT NULL,
  `date` date NOT NULL,
  `period_id` int(6) unsigned NOT NULL,
  `room_id` int(6) unsigned NOT NULL,
  PRIMARY KEY (`mbs_id`),
  KEY `fk_mbs_mb` (`mb_id`),
  KEY `fk_mbs_period` (`period_id`),
  KEY `fk_mbs_room` (`room_id`),
  CONSTRAINT `fk_mbs_mb` FOREIGN KEY (`mb_id`) REFERENCES `multi_bookings` (`mb_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mbs_period` FOREIGN KEY (`period_id`) REFERENCES `periods` (`period_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mbs_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `periods` (
  `period_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `name` varchar(30) CHARACTER SET utf8 NOT NULL,
  `days` int(2) unsigned NOT NULL,
  `bookable` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `day_1` tinyint(1) unsigned DEFAULT 0,
  `day_2` tinyint(1) unsigned DEFAULT 0,
  `day_3` tinyint(1) unsigned DEFAULT 0,
  `day_4` tinyint(1) unsigned DEFAULT 0,
  `day_5` tinyint(1) unsigned DEFAULT 0,
  `day_6` tinyint(1) unsigned DEFAULT 0,
  `day_7` tinyint(1) unsigned DEFAULT 0,
  PRIMARY KEY (`period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `roomfields` (
  `field_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `type` varchar(30) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `roomoptions` (
  `option_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(6) unsigned NOT NULL,
  `value` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `rooms` (
  `room_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(6) unsigned DEFAULT NULL,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `location` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `bookable` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `icon` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `notes` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `photo` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`room_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `roomvalues` (
  `value_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(6) unsigned NOT NULL,
  `field_id` int(6) unsigned NOT NULL,
  `value` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `sessions` (
  `session_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `is_current` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `is_selectable` tinyint(1) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `settings` (
  `group` varchar(50) CHARACTER SET utf8 NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `value` text CHARACTER SET utf8 DEFAULT NULL,
  UNIQUE KEY `group_name` (`group`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `users` (
  `user_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `department_id` int(6) unsigned DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8 NOT NULL,
  `firstname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `lastname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `authlevel` tinyint(1) unsigned NOT NULL,
  `displayname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `ext` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `lastlogin` datetime DEFAULT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `authlevel` (`authlevel`),
  KEY `enabled` (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `weekdates` (
  `week_id` int(6) unsigned NOT NULL,
  `date` date NOT NULL,
  KEY `week_id` (`week_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `weeks` (
  `week_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8 NOT NULL,
  `fgcol` char(6) CHARACTER SET utf8 DEFAULT NULL,
  `bgcol` char(6) CHARACTER SET utf8 DEFAULT NULL,
  `icon` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`week_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

