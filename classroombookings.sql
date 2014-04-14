DROP TABLE IF EXISTS `academicyears`;
CREATE TABLE `academicyears` (
  `school_id` int(6) unsigned NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  PRIMARY KEY (`school_id`)
);


DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `booking_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(6) unsigned DEFAULT NULL,
  `period_id` int(6) unsigned DEFAULT NULL,
  `week_id` int(6) unsigned DEFAULT NULL,
  `day_num` int(1) unsigned DEFAULT NULL,
  `room_id` int(6) unsigned DEFAULT NULL,
  `user_id` int(6) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  `notes` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cancelled` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`booking_id`),
  KEY `school_id` (`school_id`,`period_id`,`room_id`,`user_id`)
);


DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `department_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(6) unsigned DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`department_id`),
  KEY `school_id` (`school_id`)
);


DROP TABLE IF EXISTS `holidays`;
CREATE TABLE `holidays` (
  `holiday_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(6) unsigned DEFAULT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  PRIMARY KEY (`holiday_id`)
);


DROP TABLE IF EXISTS `periods`;
CREATE TABLE `periods` (
  `period_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(6) unsigned DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `days` int(2) unsigned DEFAULT NULL,
  `bookable` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`period_id`)
);


DROP TABLE IF EXISTS `quotas`;
CREATE TABLE `quotas` (
  `user_id` int(6) NOT NULL,
  `quota` int(3) NOT NULL
);


DROP TABLE IF EXISTS `roomfields`;
CREATE TABLE `roomfields` (
  `field_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(6) unsigned DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('TEXT','SELECT','CHECKBOX','MULTI') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`field_id`)
);


DROP TABLE IF EXISTS `roomoptions`;
CREATE TABLE `roomoptions` (
  `option_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` int(6) unsigned DEFAULT NULL,
  `value` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`option_id`)
);


DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `room_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(6) unsigned DEFAULT NULL,
  `user_id` int(6) unsigned DEFAULT NULL,
  `name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `location` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bookable` tinyint(1) DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`room_id`),
  KEY `school_id` (`school_id`),
  KEY `user_id` (`user_id`)
);


DROP TABLE IF EXISTS `roomvalues`;
CREATE TABLE `roomvalues` (
  `value_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(6) unsigned DEFAULT NULL,
  `field_id` int(6) unsigned DEFAULT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`value_id`)
);


DROP TABLE IF EXISTS `school`;
CREATE TABLE `school` (
  `school_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `colour` char(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `logo` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bia` int(3) unsigned DEFAULT '0',
  `d_columns` enum('periods','rooms','days') COLLATE utf8_unicode_ci DEFAULT NULL,
  `displaytype` enum('room','day') COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`school_id`)
);


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(6) unsigned DEFAULT NULL,
  `department_id` int(6) unsigned DEFAULT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` char(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `authlevel` int(3) DEFAULT NULL,
  `displayname` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ext` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastlogin` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enabled` int(1) NOT NULL DEFAULT '0',
  `created` date DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `authlevel` (`authlevel`),
  KEY `enabled` (`enabled`)
);


DROP TABLE IF EXISTS `weekdates`;
CREATE TABLE `weekdates` (
  `school_id` int(6) unsigned NOT NULL,
  `week_id` int(6) unsigned DEFAULT NULL,
  `date` date DEFAULT NULL,
  KEY `week_id` (`week_id`)
);


DROP TABLE IF EXISTS `weeks`;
CREATE TABLE `weeks` (
  `week_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `school_id` int(6) unsigned DEFAULT NULL,
  `name` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fgcol` char(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bgcol` char(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`week_id`)
);