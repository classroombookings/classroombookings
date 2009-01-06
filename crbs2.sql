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
	('3c6dfc16d5fa1a90295971d9fae1503f','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1231167830','a:1:{s:17:\"group_permissions\";s:0:\"\";}'),
	('9f928a9172ab93d7ce813a02825acd95','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) Ap','1231162738','a:7:{s:17:\"group_permissions\";a:60:{i:0;s:9:\"dashboard\";i:1;s:18:\"dashboard.viewdept\";i:2;s:17:\"dashboard.viewown\";i:3;s:9:\"myprofile\";i:4;s:9:\"configure\";i:5;s:10:\"changeyear\";i:6;s:8:\"bookings\";i:7;s:19:\"bookings.create.one\";i:8;s:21:\"bookings.create.recur\";i:9;s:23:\"bookings.delete.one.own\";i:10;s:29:\"bookings.delete.one.roomowner\";i:11;s:31:\"bookings.delete.recur.roomowner\";i:12;s:22:\"bookings.overwrite.one\";i:13;s:24:\"bookings.overwrite.recur\";i:14;s:32:\"bookings.overwrite.one.roomowner\";i:15;s:34:\"bookings.overwrite.recur.roomowner\";i:16;s:5:\"rooms\";i:17;s:9:\"rooms.add\";i:18;s:10:\"rooms.edit\";i:19;s:12:\"rooms.delete\";i:20;s:12:\"rooms.fields\";i:21;s:19:\"rooms.fields.values\";i:22;s:8:\"academic\";i:23;s:5:\"years\";i:24;s:9:\"years.add\";i:25;s:10:\"years.edit\";i:26;s:12:\"years.delete\";i:27;s:7:\"periods\";i:28;s:11:\"periods.add\";i:29;s:12:\"periods.edit\";i:30;s:14:\"periods.delete\";i:31;s:5:\"weeks\";i:32;s:9:\"weeks.add\";i:33;s:10:\"weeks.edit\";i:34;s:12:\"weeks.delete\";i:35;s:19:\"weeks.ayears.manage\";i:36;s:16:\"weeks.ayears.set\";i:37;s:8:\"holidays\";i:38;s:12:\"holidays.add\";i:39;s:13:\"holidays.edit\";i:40;s:15:\"holidays.delete\";i:41;s:11:\"departments\";i:42;s:15:\"departments.add\";i:43;s:16:\"departments.edit\";i:44;s:18:\"departments.delete\";i:45;s:7:\"reports\";i:46;s:21:\"reports.owndepartment\";i:47;s:22:\"reports.alldepartments\";i:48;s:15:\"reports.ownroom\";i:49;s:16:\"reports.allrooms\";i:50;s:13:\"reports.other\";i:51;s:5:\"users\";i:52;s:9:\"users.add\";i:53;s:10:\"users.edit\";i:54;s:12:\"users.delete\";i:55;s:12:\"users.import\";i:56;s:6:\"groups\";i:57;s:10:\"groups.add\";i:58;s:11:\"groups.edit\";i:59;s:13:\"groups.delete\";}s:7:\"user_id\";s:1:\"1\";s:8:\"group_id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:7:\"display\";s:12:\"Craig Rodway\";s:11:\"year_active\";s:1:\"1\";s:12:\"year_working\";s:1:\"1\";}'),
	('a39b8dd52142f04c4d4a9f9c21f676f2','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1231162815','a:7:{s:17:\"group_permissions\";a:60:{i:0;s:9:\"dashboard\";i:1;s:18:\"dashboard.viewdept\";i:2;s:17:\"dashboard.viewown\";i:3;s:9:\"myprofile\";i:4;s:9:\"configure\";i:5;s:10:\"changeyear\";i:6;s:8:\"bookings\";i:7;s:19:\"bookings.create.one\";i:8;s:21:\"bookings.create.recur\";i:9;s:23:\"bookings.delete.one.own\";i:10;s:29:\"bookings.delete.one.roomowner\";i:11;s:31:\"bookings.delete.recur.roomowner\";i:12;s:22:\"bookings.overwrite.one\";i:13;s:24:\"bookings.overwrite.recur\";i:14;s:32:\"bookings.overwrite.one.roomowner\";i:15;s:34:\"bookings.overwrite.recur.roomowner\";i:16;s:5:\"rooms\";i:17;s:9:\"rooms.add\";i:18;s:10:\"rooms.edit\";i:19;s:12:\"rooms.delete\";i:20;s:12:\"rooms.fields\";i:21;s:19:\"rooms.fields.values\";i:22;s:8:\"academic\";i:23;s:5:\"years\";i:24;s:9:\"years.add\";i:25;s:10:\"years.edit\";i:26;s:12:\"years.delete\";i:27;s:7:\"periods\";i:28;s:11:\"periods.add\";i:29;s:12:\"periods.edit\";i:30;s:14:\"periods.delete\";i:31;s:5:\"weeks\";i:32;s:9:\"weeks.add\";i:33;s:10:\"weeks.edit\";i:34;s:12:\"weeks.delete\";i:35;s:19:\"weeks.ayears.manage\";i:36;s:16:\"weeks.ayears.set\";i:37;s:8:\"holidays\";i:38;s:12:\"holidays.add\";i:39;s:13:\"holidays.edit\";i:40;s:15:\"holidays.delete\";i:41;s:11:\"departments\";i:42;s:15:\"departments.add\";i:43;s:16:\"departments.edit\";i:44;s:18:\"departments.delete\";i:45;s:7:\"reports\";i:46;s:21:\"reports.owndepartment\";i:47;s:22:\"reports.alldepartments\";i:48;s:15:\"reports.ownroom\";i:49;s:16:\"reports.allrooms\";i:50;s:13:\"reports.other\";i:51;s:5:\"users\";i:52;s:9:\"users.add\";i:53;s:10:\"users.edit\";i:54;s:12:\"users.delete\";i:55;s:12:\"users.import\";i:56;s:6:\"groups\";i:57;s:10:\"groups.add\";i:58;s:11:\"groups.edit\";i:59;s:13:\"groups.delete\";}s:7:\"user_id\";s:1:\"1\";s:8:\"group_id\";s:1:\"1\";s:8:\"username\";s:5:\"admin\";s:7:\"display\";s:12:\"Craig Rodway\";s:11:\"year_active\";s:1:\"1\";s:12:\"year_working\";s:1:\"1\";}');
/*!40000 ALTER TABLE `ci_sessions` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'departments'
#

CREATE TABLE `departments` (
  `department_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) default NULL,
  `colour` char(7) default NULL COMMENT 'Hex colour value',
  `created` date default NULL,
  PRIMARY KEY  (`department_id`)
) TYPE=InnoDB AUTO_INCREMENT=11 /*!40100 DEFAULT CHARSET=latin1 COMMENT='School departments'*/;



#
# Dumping data for table 'departments'
#

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS*/;
INSERT INTO `departments` (`department_id`, `name`, `description`, `colour`, `created`) VALUES
	('1','English','','#4E9A06','2008-12-19'),
	('2','Maths','','#EDD400','2008-12-19'),
	('3','Science','','#3465A4','2008-12-19'),
	('4','ICT','','#BABDB6','2008-12-19'),
	('5','Music','','#F57900','2008-12-19'),
	('6','History','','#8F5902','2008-12-19'),
	('7','Art','','#A40000','2008-12-19'),
	('9','RE','','#EF2929','2008-12-19'),
	('10','Geography','','#AD7FA8','2008-12-19');
/*!40000 ALTER TABLE `departments` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'departments2ldapgroups'
#

CREATE TABLE `departments2ldapgroups` (
  `department_id` int(10) unsigned NOT NULL,
  `ldapgroup_id` int(10) unsigned NOT NULL
) TYPE=InnoDB /*!40100 DEFAULT CHARSET=latin1*/;



#
# Dumping data for table 'departments2ldapgroups'
#

LOCK TABLES `departments2ldapgroups` WRITE;
/*!40000 ALTER TABLE `departments2ldapgroups` DISABLE KEYS*/;
INSERT INTO `departments2ldapgroups` (`department_id`, `ldapgroup_id`) VALUES
	('8','887'),
	('8','860'),
	('7','832'),
	('1','839'),
	('6','844'),
	('4','831'),
	('2','858'),
	('2','836'),
	('5','840'),
	('9','868'),
	('9','845'),
	('3','846'),
	('3','829'),
	('10','861'),
	('10','872'),
	('6','844'),
	('7','832'),
	('7','832');
/*!40000 ALTER TABLE `departments2ldapgroups` ENABLE KEYS*/;
UNLOCK TABLES;


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
  PRIMARY KEY  (`group_id`)
) TYPE=InnoDB AUTO_INCREMENT=8 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Groups table with settings and permiss; InnoDB free: 9216 kB'*/;



#
# Dumping data for table 'groups'
#

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS*/;
INSERT INTO `groups` (`group_id`, `name`, `description`, `bookahead`, `quota_num`, `quota_type`, `permissions`, `created`) VALUES
	('1','Administrators','Default group for administrator users',0,NULL,NULL,'a:60:{i:0;s:9:\"dashboard\";i:1;s:18:\"dashboard.viewdept\";i:2;s:17:\"dashboard.viewown\";i:3;s:9:\"myprofile\";i:4;s:9:\"configure\";i:5;s:10:\"changeyear\";i:6;s:8:\"bookings\";i:7;s:19:\"bookings.create.one\";i:8;s:21:\"bookings.create.recur\";i:9;s:23:\"bookings.delete.one.own\";i:10;s:29:\"bookings.delete.one.roomowner\";i:11;s:31:\"bookings.delete.recur.roomowner\";i:12;s:22:\"bookings.overwrite.one\";i:13;s:24:\"bookings.overwrite.recur\";i:14;s:32:\"bookings.overwrite.one.roomowner\";i:15;s:34:\"bookings.overwrite.recur.roomowner\";i:16;s:5:\"rooms\";i:17;s:9:\"rooms.add\";i:18;s:10:\"rooms.edit\";i:19;s:12:\"rooms.delete\";i:20;s:12:\"rooms.fields\";i:21;s:19:\"rooms.fields.values\";i:22;s:8:\"academic\";i:23;s:5:\"years\";i:24;s:9:\"years.add\";i:25;s:10:\"years.edit\";i:26;s:12:\"years.delete\";i:27;s:7:\"periods\";i:28;s:11:\"periods.add\";i:29;s:12:\"periods.edit\";i:30;s:14:\"periods.delete\";i:31;s:5:\"weeks\";i:32;s:9:\"weeks.add\";i:33;s:10:\"weeks.edit\";i:34;s:12:\"weeks.delete\";i:35;s:19:\"weeks.ayears.manage\";i:36;s:16:\"weeks.ayears.set\";i:37;s:8:\"holidays\";i:38;s:12:\"holidays.add\";i:39;s:13:\"holidays.edit\";i:40;s:15:\"holidays.delete\";i:41;s:11:\"departments\";i:42;s:15:\"departments.add\";i:43;s:16:\"departments.edit\";i:44;s:18:\"departments.delete\";i:45;s:7:\"reports\";i:46;s:21:\"reports.owndepartment\";i:47;s:22:\"reports.alldepartments\";i:48;s:15:\"reports.ownroom\";i:49;s:16:\"reports.allrooms\";i:50;s:13:\"reports.other\";i:51;s:5:\"users\";i:52;s:9:\"users.add\";i:53;s:10:\"users.edit\";i:54;s:12:\"users.delete\";i:55;s:12:\"users.import\";i:56;s:6:\"groups\";i:57;s:10:\"groups.add\";i:58;s:11:\"groups.edit\";i:59;s:13:\"groups.delete\";}','0000-00-00'),
	('2','Teaching Staff','Teachers from LDAP',14,'6','day','a:7:{i:0;s:9:\"dashboard\";i:1;s:18:\"dashboard.viewdept\";i:2;s:17:\"dashboard.viewown\";i:3;s:9:\"myprofile\";i:4;s:8:\"bookings\";i:5;s:19:\"bookings.create.one\";i:6;s:23:\"bookings.delete.one.own\";}','0000-00-00'),
	('4','Support staff','',14,'6','current','a:7:{i:0;s:9:\"dashboard\";i:1;s:18:\"dashboard.viewdept\";i:2;s:17:\"dashboard.viewown\";i:3;s:9:\"myprofile\";i:4;s:8:\"bookings\";i:5;s:19:\"bookings.create.one\";i:6;s:23:\"bookings.delete.one.own\";}','2008-12-02'),
	('7','Guests','Default group for guests',0,'1','current','a:1:{i:0;s:8:\"bookings\";}','0000-00-00');
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

LOCK TABLES `groups2ldapgroups` WRITE;
/*!40000 ALTER TABLE `groups2ldapgroups` DISABLE KEYS*/;
INSERT INTO `groups2ldapgroups` (`group_id`, `ldapgroup_id`) VALUES
	('2','802'),
	('4','800');
/*!40000 ALTER TABLE `groups2ldapgroups` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'holidays'
#

CREATE TABLE `holidays` (
  `holiday_id` int(10) unsigned NOT NULL auto_increment,
  `year_id` int(10) unsigned NOT NULL COMMENT 'The academic year that this holiday is relevant to',
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `name` varchar(20) default NULL,
  PRIMARY KEY  (`holiday_id`)
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
  UNIQUE KEY `ldapgroup_id` (`ldapgroup_id`,`name`)
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
  `year_id` int(10) unsigned default NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `name` varchar(20) NOT NULL,
  `days` varchar(255) NOT NULL COMMENT 'Serialize() of the days that this period is set on',
  `bookable` tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0 if periods can be booked or not',
  PRIMARY KEY  (`period_id`)
) TYPE=InnoDB AUTO_INCREMENT=59 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Periods'*/;



#
# Dumping data for table 'periods'
#

LOCK TABLES `periods` WRITE;
/*!40000 ALTER TABLE `periods` DISABLE KEYS*/;
INSERT INTO `periods` (`period_id`, `year_id`, `time_start`, `time_end`, `name`, `days`, `bookable`) VALUES
	('9','1','08:45:00','09:00:00','Registration','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',0),
	('10','1','09:00:00','10:00:00','Period 1','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',1),
	('11','1','10:00:00','11:00:00','Period 2','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',1),
	('12','1','11:00:00','11:15:00','Break','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',0),
	('13','1','11:15:00','12:15:00','Period 3','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',1),
	('14','1','12:15:00','13:20:00','Lunch','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',0),
	('15','1','13:20:00','14:20:00','Period 4','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',1),
	('16','1','14:20:00','15:20:00','Period 5','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',1),
	('49','1','15:20:00','18:30:00','After school','a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',1);
/*!40000 ALTER TABLE `periods` ENABLE KEYS*/;
UNLOCK TABLES;


#
# Table structure for table 'quota'
#

CREATE TABLE `quota` (
  `user_id` int(10) unsigned NOT NULL,
  `quota_num` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_id` (`user_id`)
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
) TYPE=InnoDB AUTO_INCREMENT=10 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Main users table'*/;



#
# Dumping data for table 'users'
#

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS*/;
INSERT INTO `users` (`user_id`, `group_id`, `enabled`, `username`, `email`, `password`, `displayname`, `cookiekey`, `lastlogin`, `ldap`, `created`) VALUES
	('1',1,1,'admin','craig.rodway@gmail.com','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Craig Rodway',NULL,'2009-01-05 13:40:21',0,'0000-00-00'),
	('2',1,0,'craig.rodway','craig.rodway@bishopbarrington.net','354c0efe3f189e6bb078399d9a75ee5cc402f8f8','Craig Rodway',NULL,'2008-11-27 17:32:10',0,'2008-11-23'),
	('3',4,1,'user1','','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Foo Number 1',NULL,'2008-12-19 23:06:20',0,'2008-11-27'),
	('4',1,1,'adrian.staff','','8843d7f92416211de9ebb963ff4ce28125932878','',NULL,'0000-00-00 00:00:00',0,'2008-11-27'),
	('8',2,1,'test.one','test.one@bishopbarrington.net','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Mr T One',NULL,'2008-12-25 23:23:26',0,'2008-12-02'),
	('9',1,0,'carlo98','','7d6706dde2c115b1dcbc66d5674d95246620bba4','',NULL,'2008-12-28 18:05:10',0,'2008-12-28');
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



#
# Table structure for table 'years'
#

CREATE TABLE `years` (
  `year_id` int(10) unsigned NOT NULL auto_increment,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `name` varchar(20) NOT NULL,
  `active` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`year_id`),
  UNIQUE KEY `active` (`active`)
) TYPE=InnoDB AUTO_INCREMENT=6 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Academic year definitions'*/;



#
# Dumping data for table 'years'
#

LOCK TABLES `years` WRITE;
/*!40000 ALTER TABLE `years` DISABLE KEYS*/;
INSERT INTO `years` (`year_id`, `date_start`, `date_end`, `name`, `active`) VALUES
	('1','2008-09-08','2009-07-23','2008 - 2009',1),
	('2','2007-09-03','2008-07-23','2007 - 2008',NULL),
	('5','2009-09-07','2010-07-23','2009 - 2010',NULL);
/*!40000 ALTER TABLE `years` ENABLE KEYS*/;
UNLOCK TABLES;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS*/;
