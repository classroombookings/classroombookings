# HeidiSQL Dump 
#
# --------------------------------------------------------
# Host:                 127.0.0.1
# Database:             crbs2
# Server version:       5.0.51b-community-nt
# Server OS:            Win32
# Target-Compatibility: MySQL 4.0
# max_allowed_packet:   1048576
# HeidiSQL version:     3.2 Revision: 1129
# --------------------------------------------------------

/*!40100 SET CHARACTER SET latin1*/;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0*/;


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
  `name` varchar(20) NOT NULL,
  `current` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Sets the current academic year',
  PRIMARY KEY  (`year_id`),
  UNIQUE KEY `year_id` (`year_id`),
  KEY `year_id_2` (`year_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Academic year definitions'*/;



#
# Dumping data for table 'academicyears'
#

# (No data found.)



#
# Table structure for table 'ci_sessions'
#

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL default '0',
  `ip_address` varchar(16) NOT NULL default '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `user_data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='CodeIgniter sessions table'*/;



#
# Dumping data for table 'ci_sessions'
#

LOCK TABLES `ci_sessions` WRITE;
/*!40000 ALTER TABLE `ci_sessions` DISABLE KEYS*/;
INSERT INTO `ci_sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
	('5188de117ade0091aa38d0540625073e','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1229516298','a:4:{s:7:\"user_id\";s:1:\"1\";s:8:\"group_id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:7:\"display\";s:5:\"admin\";}');
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS*/;
UNLOCK TABLES;


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
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='School departments'*/;



#
# Dumping data for table 'departments'
#

# (No data found.)



#
# Table structure for table 'groups'
#

CREATE TABLE `groups` (
  `group_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `description` varchar(255) default NULL,
  `bookahead` smallint(3) unsigned default NULL COMMENT 'Days ahead users in this group can make a booking',
  `quota_num` int(10) unsigned default NULL COMMENT 'Default quota amount',
  `quota_type` enum('day','week','month','current') default NULL COMMENT 'Type of quota in use',
  `permissions` text COMMENT 'A PHP-serialize()''d chunk of data',
  `created` date NOT NULL COMMENT 'Date the group was created',
  PRIMARY KEY  (`group_id`),
  UNIQUE KEY `group_id` (`group_id`),
  KEY `group_id_2` (`group_id`)
) TYPE=InnoDB AUTO_INCREMENT=7 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Groups table with settings and permiss; InnoDB free: 9216 kB'*/;



#
# Dumping data for table 'groups'
#

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS*/;
INSERT INTO `groups` (`group_id`, `name`, `description`, `bookahead`, `quota_num`, `quota_type`, `permissions`, `created`) VALUES
	('0','Guests','Default group for guests',0,'1','current','a:1:{i:0;s:8:\"bookings\";}','0000-00-00'),
	('1','Administrators','Default group for administrator users',0,NULL,NULL,'a:54:{i:0;s:9:\"dashboard\";i:1;s:18:\"dashboard.viewdept\";i:2;s:17:\"dashboard.viewown\";i:3;s:9:\"myprofile\";i:4;s:9:\"configure\";i:5;s:8:\"bookings\";i:6;s:19:\"bookings.create.one\";i:7;s:21:\"bookings.create.recur\";i:8;s:23:\"bookings.delete.one.own\";i:9;s:29:\"bookings.delete.one.roomowner\";i:10;s:31:\"bookings.delete.recur.roomowner\";i:11;s:22:\"bookings.overwrite.one\";i:12;s:24:\"bookings.overwrite.recur\";i:13;s:32:\"bookings.overwrite.one.roomowner\";i:14;s:34:\"bookings.overwrite.recur.roomowner\";i:15;s:5:\"rooms\";i:16;s:9:\"rooms.add\";i:17;s:10:\"rooms.edit\";i:18;s:12:\"rooms.delete\";i:19;s:12:\"rooms.fields\";i:20;s:19:\"rooms.fields.values\";i:21;s:7:\"periods\";i:22;s:11:\"periods.add\";i:23;s:12:\"periods.edit\";i:24;s:14:\"periods.delete\";i:25;s:5:\"weeks\";i:26;s:9:\"weeks.add\";i:27;s:10:\"weeks.edit\";i:28;s:12:\"weeks.delete\";i:29;s:19:\"weeks.ayears.manage\";i:30;s:16:\"weeks.ayears.set\";i:31;s:8:\"holidays\";i:32;s:12:\"holidays.add\";i:33;s:13:\"holidays.edit\";i:34;s:15:\"holidays.delete\";i:35;s:11:\"departments\";i:36;s:15:\"departments.add\";i:37;s:16:\"departments.edit\";i:38;s:18:\"departments.delete\";i:39;s:7:\"reports\";i:40;s:21:\"reports.owndepartment\";i:41;s:22:\"reports.alldepartments\";i:42;s:15:\"reports.ownroom\";i:43;s:16:\"reports.allrooms\";i:44;s:13:\"reports.other\";i:45;s:5:\"users\";i:46;s:9:\"users.add\";i:47;s:10:\"users.edit\";i:48;s:12:\"users.delete\";i:49;s:12:\"users.import\";i:50;s:6:\"groups\";i:51;s:10:\"groups.add\";i:52;s:11:\"groups.edit\";i:53;s:13:\"groups.delete\";}','0000-00-00'),
	('2','Teaching Staff','Teachers from LDAP',14,'6','day','a:7:{i:0;s:9:\"dashboard\";i:1;s:18:\"dashboard.viewdept\";i:2;s:17:\"dashboard.viewown\";i:3;s:9:\"myprofile\";i:4;s:8:\"bookings\";i:5;s:19:\"bookings.create.one\";i:6;s:23:\"bookings.delete.one.own\";}','0000-00-00'),
	('4','Support staff','',14,'6','current',NULL,'2008-12-02'),
	('6','Reporters','',0,'0','day','a:8:{i:0;s:9:\"dashboard\";i:1;s:9:\"myprofile\";i:2;s:7:\"reports\";i:3;s:21:\"reports.owndepartment\";i:4;s:22:\"reports.alldepartments\";i:5;s:15:\"reports.ownroom\";i:6;s:16:\"reports.allrooms\";i:7;s:13:\"reports.other\";}','2008-12-02');
/*!40000 ALTER TABLE `groups` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'groups2ldapgroups'
#

CREATE TABLE `groups2ldapgroups` (
  `group_id` int(10) unsigned NOT NULL,
  `ldapgroup_id` int(10) unsigned NOT NULL
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Maps 1+ LDAp groups to 1 CRBS group'*/;



#
# Dumping data for table 'groups2ldapgroups'
#

# (No data found.)



#
# Table structure for table 'holidays'
#

CREATE TABLE `holidays` (
  `holiday_id` int(10) unsigned NOT NULL auto_increment,
  `ayear_id` int(10) unsigned NOT NULL COMMENT 'The academic year that this holiday is relevant to',
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `name` varchar(20) default NULL,
  PRIMARY KEY  (`holiday_id`),
  UNIQUE KEY `year_id` (`holiday_id`),
  KEY `year_id_2` (`holiday_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='School holidays'*/;



#
# Dumping data for table 'holidays'
#

# (No data found.)



#
# Table structure for table 'ldapgroups'
#

CREATE TABLE `ldapgroups` (
  `ldapgroup_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(104) NOT NULL COMMENT 'Name of LDAP group (not full DN, just name part)',
  PRIMARY KEY  (`ldapgroup_id`),
  UNIQUE KEY `ldapgroup_id` (`ldapgroup_id`,`name`),
  KEY `ldapgroup_id_2` (`ldapgroup_id`)
) TYPE=InnoDB AUTO_INCREMENT=904 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Group names retrieved from LDAP; InnoDB free: 9216 kB'*/;



#
# Dumping data for table 'ldapgroups'
#

LOCK TABLES `ldapgroups` WRITE;
/*!40000 ALTER TABLE `ldapgroups` DISABLE KEYS*/;
INSERT INTO `ldapgroups` (`ldapgroup_id`, `name`) VALUES
	('798','BBS Print Operators'),
	('799','BBS Staff Print Operators'),
	('800','BBS Non-Teach Staff'),
	('801','BBS Students'),
	('802','BBS Teaching Staff'),
	('803','BBS Accessibility'),
	('804','BBS Internet Disabled'),
	('805','BBS Guest {UT}'),
	('806','BBS RM Explorer {UT}'),
	('807','BBS Restricted {UT}'),
	('808','BBS Standard {UT}'),
	('809','BBS Advanced {UT}'),
	('810','BBS Staff {UT}'),
	('811','BBS Advanced Staff {UT}'),
	('812','BBS Advanced {SS}'),
	('813','BBS Standard {SS}'),
	('814','BBS No {SS}'),
	('815','BBS Shared LT {ST}'),
	('816','BBS Managed Stations'),
	('817','BBS Authorised {MT}'),
	('818','BBS Delegate {MT}'),
	('819','BBS Shared {ST}'),
	('820','BBS Personal {ST}'),
	('821','BBS Cyber {ST}'),
	('822','BBS EasyLink'),
	('823','BBS Education Mgmt {UR}'),
	('824','BBS Legacy Apps {UR}'),
	('825','BBS Management Information System'),
	('826','BBS Technology {tch}'),
	('827','BBS Local Administrators'),
	('828','BBS Station Setup'),
	('829','BBS Science {tch}'),
	('830','BBS Leisure ~1 {tch}'),
	('831','BBS ICT {tch}'),
	('832','BBS Art {tch}'),
	('833','BBS CD Burning'),
	('834','BBS Textiles {tch}'),
	('835','BBS PowerDVD'),
	('836','BBS Maths {tch}'),
	('837','BBS Food Tec~1 {tch}'),
	('838','BBS MFL {tch}'),
	('839','BBS English {tch}'),
	('840','BBS Music {tch}'),
	('841','BBS Physical~1 {tch}'),
	('842','BBS Performi~1 {tch}'),
	('843','BBS Media St~1 {tch}'),
	('844','BBS History {tch}'),
	('845','BBS Religiou~1 {tch}'),
	('846','BBS Science'),
	('847','BBS RMMC {AR}'),
	('848','BBS User Controller {MT}'),
	('849','BBS EDI System'),
	('850','BBS Finance System'),
	('851','BBS Library System'),
	('852','BBS MIS Manager'),
	('853','BBS Network {MT}'),
	('854','BBS Sleuth Users'),
	('855','BBS Staff Absences'),
	('856','BBS School Income'),
	('857','BBS RMSecurenet'),
	('858','BBS Maths'),
	('859','BBS Science Exam'),
	('860','BBS Admin Users'),
	('861','BBS Geography {tch}'),
	('862','BBS No GPO Security'),
	('863','BBS Associates'),
	('864','BBS Science year 11'),
	('865','BBS Science year 10'),
	('866','BBS Science Review'),
	('867','BBS Careers Teacher'),
	('868','BBS RE Teachers'),
	('869','BBS Detention DB U~1'),
	('870','BBS Eregistration'),
	('871','BBS Interactive Wh~1'),
	('872','BBS Humanities'),
	('873','BBS Science year 9'),
	('874','BBS Legal Team'),
	('875','BBS Leisure and To~1'),
	('876','BBS QuarkXPress Us~1'),
	('877','BBS Quizdom'),
	('878','BBS BKSB'),
	('879','BBS Staff DAP {UT}'),
	('880','BBS Design Teachers'),
	('881','BBS PE Teachers'),
	('882','BBS Exam Users'),
	('883','Terminal Services Users'),
	('884','BBS Shared De~1 {ST}'),
	('885','BBS SecureNet'),
	('886','BBS Exam Officer'),
	('887','BBS Admin Staff {UT}'),
	('888','BBS AnyComms Users'),
	('889','BBS Careers {tch}'),
	('890','BBS BKSB Manager'),
	('891','BBS Copy of A~1 {UT}'),
	('892','BBS SEN Teachers'),
	('893','BBS SEN Students'),
	('894','BBS Childcare'),
	('895','BBS Truancy Call'),
	('896','BBS SSP'),
	('897','BBS Email disabled'),
	('898','BBS School Fund Of~1'),
	('899','BBS IT author~1 {UT}'),
	('900','BBS Copy of R~1 {UT}'),
	('901','BBS Encrypted Folder'),
	('902','BBS Guest 2 {UT}'),
	('903','BBS HSS Finance');
/*!40000 ALTER TABLE `ldapgroups` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'periods'
#

CREATE TABLE `periods` (
  `period_id` int(10) unsigned NOT NULL auto_increment,
  `ayear_id` int(10) unsigned default NULL COMMENT 'The academic year that period belongs to',
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `name` varchar(20) NOT NULL,
  `days` int(2) unsigned NOT NULL COMMENT 'Bitmask of the days that this period is set on',
  `bookable` tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0 if periods can be booked or not',
  PRIMARY KEY  (`period_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Periods'*/;



#
# Dumping data for table 'periods'
#

# (No data found.)



#
# Table structure for table 'quota'
#

CREATE TABLE `quota` (
  `user_id` int(10) unsigned NOT NULL,
  `quota_num` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_id` (`user_id`),
  KEY `user_id_2` (`user_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Quota details'*/;



#
# Dumping data for table 'quota'
#

# (No data found.)



#
# Table structure for table 'rooms-fields'
#

CREATE TABLE `rooms-fields` (
  `field_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `type` enum('text','select','check','multi') NOT NULL,
  PRIMARY KEY  (`field_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Names of fields that can be assigned to rooms'*/;



#
# Dumping data for table 'rooms-fields'
#

# (No data found.)



#
# Table structure for table 'rooms-options'
#

CREATE TABLE `rooms-options` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY  (`option_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Options for room drop-down fields'*/;



#
# Dumping data for table 'rooms-options'
#

# (No data found.)



#
# Table structure for table 'rooms-values'
#

CREATE TABLE `rooms-values` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `room_id` int(10) unsigned NOT NULL,
  `field_id` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`value_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Actual values of room fields for each room'*/;



#
# Dumping data for table 'rooms-values'
#

# (No data found.)



#
# Table structure for table 'rooms'
#

CREATE TABLE `rooms` (
  `room_id` int(10) unsigned NOT NULL auto_increment,
  `user_id` int(10) unsigned NOT NULL COMMENT 'Specifies an owner (user) of the room',
  `order` tinyint(3) unsigned default NULL COMMENT 'Order that the rooms appear in (optional)',
  `name` varchar(20) NOT NULL,
  `location` varchar(40) NOT NULL,
  `bookable` tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0',
  `notes` varchar(255) NOT NULL,
  `photo` char(32) NOT NULL COMMENT 'An md5 hash that references the file that is stored',
  PRIMARY KEY  (`room_id`),
  KEY `user_id` (`user_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='School rooms'*/;



#
# Dumping data for table 'rooms'
#

# (No data found.)



#
# Table structure for table 'settings-auth'
#

CREATE TABLE `settings-auth` (
  `preauthkey` char(40) default NULL COMMENT 'SHA1 hash to be used as preauth key',
  `ldap` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Boolean 1 or 0 for LDAP auth status',
  `ldaphost` varchar(50) default NULL COMMENT 'LDAP server hostname',
  `ldapport` int(5) unsigned default NULL COMMENT 'LDAP server TCP port',
  `ldapbase` text COMMENT 'Base DNs to search in LDAP',
  `ldapfilter` text COMMENT 'LDAP search query filter',
  `ldapgroup_id` int(10) unsigned default NULL
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='LDAP configuration'*/;



#
# Dumping data for table 'settings-auth'
#

LOCK TABLES `settings-auth` WRITE;
/*!40000 ALTER TABLE `settings-auth` DISABLE KEYS*/;
INSERT INTO `settings-auth` (`preauthkey`, `ldap`, `ldaphost`, `ldapport`, `ldapbase`, `ldapfilter`, `ldapgroup_id`) VALUES
	('d1ae873270604d2d2ea3221a4e632b96b1d3a914',1,'bbs-svr-001','389','ou=teaching staff, ou=bbs, ou=establishments, dc=bbarrington, dc=internal; ou=system administrators, ou=bbs, ou=establishments, dc=bbarrington, dc=internal','(& (| (!(displayname=Administrator*)) (!(displayname=Admin*)) ) (cn=%u) )','1');
/*!40000 ALTER TABLE `settings-auth` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'settings-main'
#

CREATE TABLE `settings-main` (
  `schoolname` varchar(100) default NULL COMMENT 'Name of school',
  `schoolurl` varchar(255) default NULL COMMENT 'Web address for school',
  `bd_mode` enum('room','day') default NULL COMMENT 'Mode of display for room booking table',
  `bd_col` enum('periods','rooms','days') default NULL COMMENT 'Columns on the booking table',
  `room_order` enum('alpha','order') default NULL COMMENT 'How to display rooms in the booking view'
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Global app settings'*/;



#
# Dumping data for table 'settings-main'
#

LOCK TABLES `settings-main` WRITE;
/*!40000 ALTER TABLE `settings-main` DISABLE KEYS*/;
INSERT INTO `settings-main` (`schoolname`, `schoolurl`, `bd_mode`, `bd_col`, `room_order`) VALUES
	('Bishop Barrington School Sports With Mathematics College','','day','periods','alpha');
/*!40000 ALTER TABLE `settings-main` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'users'
#

CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) NOT NULL COMMENT 'Group that the user is a member of',
  `enabled` tinyint(1) NOT NULL default '0' COMMENT 'Boolean 1 or 0',
  `username` varchar(104) NOT NULL,
  `email` varchar(255) default NULL,
  `password` char(40) NOT NULL COMMENT 'SHA1 hash of password',
  `displayname` varchar(64) default NULL,
  `cookiekey` char(40) default NULL COMMENT 'SHA1 hash if a cookie is required',
  `lastlogin` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT 'Date the user last logged in',
  `ldap` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Boolean 1 or 0 if user should authenticate via LDAP',
  `created` date NOT NULL COMMENT 'Date the user was created',
  PRIMARY KEY  (`user_id`),
  KEY `ldap` (`ldap`)
) TYPE=InnoDB AUTO_INCREMENT=9 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Main users table'*/;



#
# Dumping data for table 'users'
#

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS*/;
INSERT INTO `users` (`user_id`, `group_id`, `enabled`, `username`, `email`, `password`, `displayname`, `cookiekey`, `lastlogin`, `ldap`, `created`) VALUES
	('1',1,1,'admin','craig.rodway@gmail.com','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',NULL,NULL,'2008-12-17 11:00:25',0,'0000-00-00'),
	('2',1,0,'craig.rodway','craig.rodway@bishopbarrington.net','354c0efe3f189e6bb078399d9a75ee5cc402f8f8','Craig Rodway',NULL,'2008-11-27 17:32:10',0,'2008-11-23'),
	('3',0,1,'user1','','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Foo Number 1',NULL,'0000-00-00 00:00:00',0,'2008-11-27'),
	('4',1,1,'adrian.staff','','8843d7f92416211de9ebb963ff4ce28125932878','',NULL,'0000-00-00 00:00:00',0,'2008-11-27'),
	('8',0,1,'test.one','test.one@bishopbarrington.net','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Mr T One',NULL,'2008-12-02 23:54:07',0,'2008-12-02');
/*!40000 ALTER TABLE `users` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'users2departments'
#

CREATE TABLE `users2departments` (
  `user_id` int(10) unsigned NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  KEY `user_id` (`user_id`,`department_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Maps a user to multiple departments'*/;



#
# Dumping data for table 'users2departments'
#

# (No data found.)



#
# Table structure for table 'weekdates'
#

CREATE TABLE `weekdates` (
  `week_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  KEY `week_id` (`week_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Start-dates of weeks for timetable'*/;



#
# Dumping data for table 'weekdates'
#

# (No data found.)



#
# Table structure for table 'weeks'
#

CREATE TABLE `weeks` (
  `week_id` int(10) unsigned NOT NULL auto_increment,
  `ayear_id` int(10) unsigned default NULL,
  `name` varchar(20) NOT NULL,
  `fgcol` char(6) NOT NULL,
  `bgcol` char(6) NOT NULL,
  PRIMARY KEY  (`week_id`)
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1 COMMENT='Week definitions for timetable weeks'*/;



#
# Dumping data for table 'weeks'
#

# (No data found.)

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS*/;
