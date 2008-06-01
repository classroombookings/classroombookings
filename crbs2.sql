# HeidiSQL Dump 
#
# --------------------------------------------------------
# Host:                 127.0.0.1
# Database:             crbs2
# Server version:       5.0.51b-community-nt
# Server OS:            Win32
# Target-Compatibility: MySQL 4.0
# Extended INSERTs:     Y
# max_allowed_packet:   1048576
# HeidiSQL version:     3.0 Revision: 572
# --------------------------------------------------------

/*!40100 SET CHARACTER SET latin1*/;


#
# Database structure for database 'crbs2'
#

DROP DATABASE IF EXISTS `crbs2`;
CREATE DATABASE `crbs2` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `crbs2`;


#
# Table structure for table 'academicyears'
#

CREATE TABLE `academicyears` (
  `year_id` int(10) unsigned NOT NULL auto_increment,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`year_id`),
  UNIQUE KEY `year_id` (`year_id`),
  KEY `year_id_2` (`year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Academic year definitions';



#
# Dumping data for table 'academicyears'
#

/*!40000 ALTER TABLE `academicyears` DISABLE KEYS*/;
LOCK TABLES `academicyears` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `academicyears` ENABLE KEYS*/;


#
# Table structure for table 'departments'
#

CREATE TABLE `departments` (
  `department_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`department_id`),
  UNIQUE KEY `department_id` (`department_id`),
  KEY `department_id_2` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='School departments';



#
# Dumping data for table 'departments'
#

/*!40000 ALTER TABLE `departments` DISABLE KEYS*/;
LOCK TABLES `departments` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `departments` ENABLE KEYS*/;


#
# Table structure for table 'groups'
#

CREATE TABLE `groups` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `description` varchar(255) default NULL,
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `group_id` (`group_id`),
  KEY `group_id_2` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Security groups';



#
# Dumping data for table 'groups'
#

/*!40000 ALTER TABLE `groups` DISABLE KEYS*/;
LOCK TABLES `groups` WRITE;
INSERT INTO `groups` (`group_id`, `name`, `description`) VALUES ('1','Anonymous','No login is required');
UNLOCK TABLES;
/*!40000 ALTER TABLE `groups` ENABLE KEYS*/;


#
# Table structure for table 'holidays'
#

CREATE TABLE `holidays` (
  `holiday_id` int(10) unsigned NOT NULL auto_increment,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `name` varchar(50) default NULL,
  PRIMARY KEY  (`holiday_id`),
  UNIQUE KEY `year_id` (`holiday_id`),
  KEY `year_id_2` (`holiday_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='School holidays';



#
# Dumping data for table 'holidays'
#

/*!40000 ALTER TABLE `holidays` DISABLE KEYS*/;
LOCK TABLES `holidays` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `holidays` ENABLE KEYS*/;


#
# Table structure for table 'periods'
#

CREATE TABLE `periods` (
  `period_id` int(10) unsigned NOT NULL auto_increment,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `name` varchar(20) NOT NULL,
  `days` int(2) unsigned NOT NULL,
  `bookable` tinyint(1) NOT NULL,
  PRIMARY KEY  (`period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Periods';



#
# Dumping data for table 'periods'
#

/*!40000 ALTER TABLE `periods` DISABLE KEYS*/;
LOCK TABLES `periods` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `periods` ENABLE KEYS*/;


#
# Table structure for table 'permissions'
#

CREATE TABLE `permissions` (
  `permission_id` int(10) unsigned NOT NULL auto_increment,
  `action` varchar(20) NOT NULL,
  `menuname` varchar(20) default NULL,
  `url` varchar(50) default NULL,
  `order` int(2) unsigned default NULL,
  `admin-parent` int(10) unsigned default NULL,
  `admin-title` varchar(50) NOT NULL,
  `admin-desc` text,
  PRIMARY KEY  (`permission_id`),
  UNIQUE KEY `permission_id` (`permission_id`,`action`),
  KEY `permission_id_2` (`permission_id`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COMMENT='Permission descriptions';



#
# Dumping data for table 'permissions'
#

/*!40000 ALTER TABLE `permissions` DISABLE KEYS*/;
LOCK TABLES `permissions` WRITE;
INSERT INTO `permissions` (`permission_id`, `action`, `menuname`, `url`, `order`, `admin-parent`, `admin-title`, `admin-desc`) VALUES ('1','BKG_VIEW','Bookings','bookings',0,NULL,'View bookings',NULL),
	('2','BKG_MAKE',NULL,NULL,NULL,'1','Make a booking',NULL),
	('3','BKG_MAKE_RECUR',NULL,NULL,NULL,'1','Make recurring bookings',NULL),
	('4','BKG_MAKE_RECUR_OWNER',NULL,NULL,NULL,'1','Allow room owners to make recurring bookings',NULL),
	('5','ACC','Account','account',1,NULL,'Account',NULL),
	('6','CFG','Settings','settings',2,NULL,'Global settings',NULL),
	('7','RMS','Rooms','rooms',3,NULL,'Rooms',NULL),
	('8','PDS','Periods','periods',4,NULL,'Periods (school day)',NULL),
	('9','WKS','Weeks','weeks',5,NULL,'Timetable weeks',NULL),
	('10','WKS_ACYR',NULL,'weeks/academic-year',NULL,'8','Academic year',NULL),
	('11','HOL','Holidays','holidays',6,NULL,'School holidays',NULL),
	('12','DEP','Departments','departments',7,NULL,'Departments',NULL),
	('13','REP','Reports','reports',8,NULL,'Reports',NULL),
	('14','USR','Users','users',9,NULL,'User management',NULL),
	('15','USR_GRPS',NULL,'users/groups',NULL,'13','Group management',NULL),
	('16','WEL','','welcome',NULL,NULL,'Welcome page',NULL);
UNLOCK TABLES;
/*!40000 ALTER TABLE `permissions` ENABLE KEYS*/;


#
# Table structure for table 'permissions2groups'
#

CREATE TABLE `permissions2groups` (
  `permission_id` int(10) unsigned default NULL,
  `group_id` int(10) unsigned default NULL,
  KEY `permission_id` (`permission_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Link permissions with groups';



#
# Dumping data for table 'permissions2groups'
#

/*!40000 ALTER TABLE `permissions2groups` DISABLE KEYS*/;
LOCK TABLES `permissions2groups` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `permissions2groups` ENABLE KEYS*/;


#
# Table structure for table 'rooms-fields'
#

CREATE TABLE `rooms-fields` (
  `field_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `type` enum('TEXT','SELECT','CHECKBOX','MULTI') NOT NULL,
  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Names of fields that can be assigned to rooms';



#
# Dumping data for table 'rooms-fields'
#

/*!40000 ALTER TABLE `rooms-fields` DISABLE KEYS*/;
LOCK TABLES `rooms-fields` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `rooms-fields` ENABLE KEYS*/;


#
# Table structure for table 'rooms-options'
#

CREATE TABLE `rooms-options` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY  (`option_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



#
# Dumping data for table 'rooms-options'
#

/*!40000 ALTER TABLE `rooms-options` DISABLE KEYS*/;
LOCK TABLES `rooms-options` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `rooms-options` ENABLE KEYS*/;


#
# Table structure for table 'rooms-values'
#

CREATE TABLE `rooms-values` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `room_id` int(10) unsigned NOT NULL,
  `field_id` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Actual values of room fields for each room';



#
# Dumping data for table 'rooms-values'
#

/*!40000 ALTER TABLE `rooms-values` DISABLE KEYS*/;
LOCK TABLES `rooms-values` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `rooms-values` ENABLE KEYS*/;


#
# Table structure for table 'rooms'
#

CREATE TABLE `rooms` (
  `room_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `location` varchar(40) NOT NULL,
  `bookable` tinyint(1) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `photo` char(40) NOT NULL,
  PRIMARY KEY  (`room_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='School rooms';



#
# Dumping data for table 'rooms'
#

/*!40000 ALTER TABLE `rooms` DISABLE KEYS*/;
LOCK TABLES `rooms` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `rooms` ENABLE KEYS*/;


#
# Table structure for table 'settings-crbs'
#

CREATE TABLE `settings-crbs` (
  `schoolname` varchar(100) default NULL,
  `schoolurl` varchar(255) default NULL,
  `daysahead` int(3) default NULL,
  `bk-columns` enum('periods','rooms','days') default NULL,
  `bk-viewtype` enum('room','day') default NULL,
  `preauthkey` varchar(255) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Global app settings';



#
# Dumping data for table 'settings-crbs'
#

/*!40000 ALTER TABLE `settings-crbs` DISABLE KEYS*/;
LOCK TABLES `settings-crbs` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `settings-crbs` ENABLE KEYS*/;


#
# Table structure for table 'settings-ldap-rdns'
#

CREATE TABLE `settings-ldap-rdns` (
  `rdn_id` int(10) unsigned NOT NULL auto_increment,
  `string` varchar(255) NOT NULL,
  PRIMARY KEY  (`rdn_id`),
  UNIQUE KEY `dn_id` (`rdn_id`),
  KEY `dn_id_2` (`rdn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Settings for LDAP bind DNs';



#
# Dumping data for table 'settings-ldap-rdns'
#

/*!40000 ALTER TABLE `settings-ldap-rdns` DISABLE KEYS*/;
LOCK TABLES `settings-ldap-rdns` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `settings-ldap-rdns` ENABLE KEYS*/;


#
# Table structure for table 'settings-ldap'
#

CREATE TABLE `settings-ldap` (
  `serverhost` varchar(20) default NULL,
  `serverport` int(5) unsigned default NULL,
  `base` varchar(255) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='LDAP configuration';



#
# Dumping data for table 'settings-ldap'
#

/*!40000 ALTER TABLE `settings-ldap` DISABLE KEYS*/;
LOCK TABLES `settings-ldap` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `settings-ldap` ENABLE KEYS*/;


#
# Table structure for table 'users'
#

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) NOT NULL,
  `department_id` int(10) unsigned default NULL,
  `enabled` tinyint(1) NOT NULL default '0',
  `username` varchar(20) NOT NULL,
  `firstname` varchar(20) default NULL,
  `lastname` varchar(20) default NULL,
  `email` varchar(255) default NULL,
  `password` char(40) NOT NULL,
  `password-expire` datetime default NULL,
  `displayname` varchar(20) default NULL,
  `ext` varchar(10) default NULL,
  `cookiekey` char(40) default NULL,
  `lastlogin` timestamp NOT NULL default '0000-00-00 00:00:00',
  `ldap` tinyint(1) unsigned NOT NULL default '0',
  `created` date NOT NULL,
  PRIMARY KEY  (`user_id`),
  KEY `ldap` (`ldap`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COMMENT='Main users table';



#
# Dumping data for table 'users'
#

/*!40000 ALTER TABLE `users` DISABLE KEYS*/;
LOCK TABLES `users` WRITE;
INSERT INTO `users` (`user_id`, `group_id`, `department_id`, `enabled`, `username`, `firstname`, `lastname`, `email`, `password`, `password-expire`, `displayname`, `ext`, `cookiekey`, `lastlogin`, `ldap`, `created`) VALUES ('1',1,'0',1,'admin',NULL,NULL,'craig.rodway@gmail.com','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',NULL,NULL,NULL,NULL,'2008-02-02 13:09:55',0,'0000-00-00');
UNLOCK TABLES;
/*!40000 ALTER TABLE `users` ENABLE KEYS*/;


#
# Table structure for table 'weekdates'
#

CREATE TABLE `weekdates` (
  `week_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  KEY `week_id` (`week_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Start-dates of weeks for timetable';



#
# Dumping data for table 'weekdates'
#

/*!40000 ALTER TABLE `weekdates` DISABLE KEYS*/;
LOCK TABLES `weekdates` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `weekdates` ENABLE KEYS*/;


#
# Table structure for table 'weeks'
#

CREATE TABLE `weeks` (
  `week_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `fgcol` char(6) NOT NULL,
  `bgcol` char(6) NOT NULL,
  `icon` varchar(255) default NULL,
  PRIMARY KEY  (`week_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Week definitions for timetable weeks';



#
# Dumping data for table 'weeks'
#

/*!40000 ALTER TABLE `weeks` DISABLE KEYS*/;
LOCK TABLES `weeks` WRITE;
UNLOCK TABLES;
/*!40000 ALTER TABLE `weeks` ENABLE KEYS*/;
