DROP TABLE IF EXISTS `academicyears`;
CREATE TABLE academicyears (
`school_id` int(6) unsigned NOT NULL,
`date_start` date NOT NULL,
`date_end` date NOT NULL,
PRIMARY KEY(`school_id`)
);

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE bookings (
  `booking_id` int(6) unsigned NOT NULL auto_increment,
  `school_id` int(6) unsigned NOT NULL,
  `period_id` int(6) unsigned NOT NULL,
  `week_id` int(6) unsigned default NULL,
  `day_num` int(1) default NULL,
  `room_id` int(6) unsigned NOT NULL,
  `user_id` int(6) unsigned NOT NULL,
  `date` date default NULL,
  `notes` varchar(100) NOT NULL,
  `cancelled` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`booking_id`),
  KEY `school_id` (`school_id`,`period_id`,`room_id`,`user_id`)
);


DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `department_id` int(6) unsigned NOT NULL auto_increment,
  `school_id` int(6) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  PRIMARY KEY  (`department_id`),
  KEY `school_id` (`school_id`)
);


DROP TABLE IF EXISTS `holidays`;
CREATE TABLE `holidays` (
  `holiday_id` int(6) unsigned NOT NULL auto_increment,
  `school_id` int(6) unsigned NOT NULL,
  `name` varchar(30) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  PRIMARY KEY  (`holiday_id`)
);


DROP TABLE IF EXISTS `periods`;
CREATE TABLE `periods` (
  `period_id` int(6) unsigned NOT NULL auto_increment,
  `school_id` int(6) unsigned NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `name` varchar(30) NOT NULL,
  `days` int(2) unsigned NOT NULL,
  `bookable` tinyint(1) NOT NULL,
  PRIMARY KEY  (`period_id`)
);


DROP TABLE IF EXISTS `quotas`;
CREATE TABLE `quotas` (
  `user_id` int(6) NOT NULL,
  `quota` int(3) NOT NULL
);


DROP TABLE IF EXISTS `roomfields`;
CREATE TABLE `roomfields` (
  `field_id` int(6) unsigned NOT NULL auto_increment,
  `school_id` int(6) unsigned NOT NULL,
  `name` varchar(64)NOT NULL,
  `type` enum('TEXT','SELECT','CHECKBOX','MULTI') NOT NULL,
  PRIMARY KEY  (`field_id`)
);


DROP TABLE IF EXISTS `roomoptions`;
CREATE TABLE `roomoptions` (
  `option_id` int(6) unsigned NOT NULL auto_increment,
  `field_id` int(6) unsigned NOT NULL,
  `value` varchar(64) NOT NULL,
  PRIMARY KEY  (`option_id`)
);


DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `room_id` int(6) unsigned NOT NULL auto_increment,
  `school_id` int(6) unsigned NOT NULL,
  `user_id` int(6) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `location` varchar(40) NOT NULL,
  `bookable` tinyint(1) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `photo` varchar(40) NOT NULL,
  PRIMARY KEY  (`room_id`),
  KEY `school_id` (`school_id`),
  KEY `user_id` (`user_id`)
);


DROP TABLE IF EXISTS `roomvalues`;
CREATE TABLE `roomvalues` (
  `value_id` int(6) unsigned NOT NULL auto_increment,
  `room_id` int(6) unsigned NOT NULL,
  `field_id` int(6) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`value_id`)
);


DROP TABLE IF EXISTS `school`;
CREATE TABLE `school` (
  `school_id` int(6) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `colour` char(6) default NULL,
  `logo` varchar(40) NOT NULL,
  `bia` int(3) unsigned NOT NULL,
  `d_columns` enum('periods','rooms','days') NOT NULL,
  `displaytype` enum('room','day') NOT NULL,
  PRIMARY KEY  (`school_id`)
);


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(6) unsigned NOT NULL auto_increment,
  `school_id` int(6) unsigned NOT NULL,
  `department_id` int(6) unsigned NOT NULL,
  `username` varchar(20) NOT NULL,
  `firstname` varchar(20)  default NULL,
  `lastname` varchar(20) default NULL,
  `email` varchar(255) NOT NULL,
  `password` char(40) NOT NULL,
  `authlevel` int(3) NOT NULL,
  `displayname` varchar(20)  default NULL,
  `ext` varchar(10) default NULL,
  `lastlogin` timestamp NOT NULL default '0000-00-00 00:00:00',
  `enabled` int(1) NOT NULL default '0',
  `created` date NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `authlevel` (`authlevel`),
  KEY `enabled` (`enabled`)
);


DROP TABLE IF EXISTS `weekdates`;
CREATE TABLE `weekdates` (
  `school_id` int(6) unsigned NOT NULL,
  `week_id` int(6) unsigned NOT NULL,
  `date` date NOT NULL,
  KEY `week_id` (`week_id`)
);


DROP TABLE IF EXISTS `weeks`;
CREATE TABLE `weeks` (
  `week_id` int(6) unsigned NOT NULL auto_increment,
  `school_id` int(6) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `fgcol` char(6) NOT NULL,
  `bgcol` char(6)  NOT NULL,
  `icon` varchar(255)  NOT NULL,
  PRIMARY KEY  (`week_id`)
);