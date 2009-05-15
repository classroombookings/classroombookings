-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 15, 2009 at 04:01 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6

SET FOREIGN_KEY_CHECKS=0;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `crbs2`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `department_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(64) NOT NULL,
  `description` varchar(255) default NULL,
  `colour` char(7) default NULL COMMENT 'Hex colour value',
  `created` date default NULL,
  PRIMARY KEY  (`department_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='School departments' AUTO_INCREMENT=15 ;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `name`, `description`, `colour`, `created`) VALUES
(1, 'English', '', '#339966', '2008-12-19'),
(2, 'Maths', '', '#FFCC00', '2008-12-19'),
(3, 'Science', '', '#33CCCC', '2008-12-19'),
(4, 'ICT', '', '#C0C0C0', '2008-12-19'),
(5, 'Music', '', '#008080', '2008-12-19'),
(6, 'History', '', '#993300', '2008-12-19'),
(7, 'Art', '', '#FF99CC', '2008-12-19'),
(9, 'RE', '', '#FF0000', '2008-12-19'),
(10, 'Geography', '', '#8AE234', '2008-12-19'),
(11, 'Languages', '', '#CC99FF', '2009-01-09'),
(12, 'PE', '', '#000080', '2009-01-09'),
(13, 'Technology', '', '#FFFF00', '2009-01-09');

-- --------------------------------------------------------

--
-- Table structure for table `departments2ldapgroups`
--

DROP TABLE IF EXISTS `departments2ldapgroups`;
CREATE TABLE `departments2ldapgroups` (
  `department_id` int(10) unsigned NOT NULL,
  `ldapgroup_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `unique` (`department_id`,`ldapgroup_id`),
  KEY `department_id` (`department_id`),
  KEY `ldapgroup_id` (`ldapgroup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `departments2ldapgroups`
--

INSERT INTO `departments2ldapgroups` (`department_id`, `ldapgroup_id`) VALUES
(1, 1157),
(2, 1154),
(2, 1176),
(3, 1147),
(3, 1164),
(4, 1149),
(5, 1158),
(6, 1162),
(6, 1190),
(7, 1150),
(9, 1163),
(9, 1186),
(10, 1179),
(10, 1190),
(11, 1156),
(12, 1159),
(12, 1199),
(13, 1144);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
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
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Groups table with settings and permiss; InnoDB free: 9216 kB' AUTO_INCREMENT=11 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `name`, `description`, `bookahead`, `quota_num`, `quota_type`, `permissions`, `created`) VALUES
(1, 'Administrators', 'Default group for administrator users', 0, NULL, NULL, 'a:62:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:9:"configure";i:5;s:8:"allrooms";i:6;s:8:"bookings";i:7;s:19:"bookings.create.one";i:8;s:21:"bookings.create.recur";i:9;s:23:"bookings.delete.one.own";i:10;s:29:"bookings.delete.one.roomowner";i:11;s:31:"bookings.delete.recur.roomowner";i:12;s:5:"rooms";i:13;s:9:"rooms.add";i:14;s:10:"rooms.edit";i:15;s:12:"rooms.delete";i:16;s:11:"rooms.attrs";i:17;s:18:"rooms.attrs.values";i:18;s:17:"rooms.permissions";i:19;s:8:"academic";i:20;s:5:"years";i:21;s:9:"years.add";i:22;s:10:"years.edit";i:23;s:12:"years.delete";i:24;s:7:"periods";i:25;s:11:"periods.add";i:26;s:12:"periods.edit";i:27;s:14:"periods.delete";i:28;s:5:"weeks";i:29;s:9:"weeks.add";i:30;s:10:"weeks.edit";i:31;s:12:"weeks.delete";i:32;s:19:"weeks.ayears.manage";i:33;s:16:"weeks.ayears.set";i:34;s:5:"terms";i:35;s:9:"terms.add";i:36;s:10:"terms.edit";i:37;s:12:"terms.delete";i:38;s:8:"holidays";i:39;s:12:"holidays.add";i:40;s:13:"holidays.edit";i:41;s:15:"holidays.delete";i:42;s:11:"departments";i:43;s:15:"departments.add";i:44;s:16:"departments.edit";i:45;s:18:"departments.delete";i:46;s:7:"reports";i:47;s:21:"reports.owndepartment";i:48;s:22:"reports.alldepartments";i:49;s:15:"reports.ownroom";i:50;s:16:"reports.allrooms";i:51;s:13:"reports.other";i:52;s:5:"users";i:53;s:9:"users.add";i:54;s:10:"users.edit";i:55;s:12:"users.delete";i:56;s:12:"users.import";i:57;s:6:"groups";i:58;s:10:"groups.add";i:59;s:11:"groups.edit";i:60;s:13:"groups.delete";i:61;s:11:"permissions";}', '0000-00-00'),
(2, 'Teaching Staff', 'Teachers from LDAP', 14, 6, 'day', 'a:7:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:8:"bookings";i:5;s:19:"bookings.create.one";i:6;s:23:"bookings.delete.one.own";}', '0000-00-00'),
(4, 'Support staff', '', 14, 6, 'current', 'a:7:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:8:"bookings";i:5;s:19:"bookings.create.one";i:6;s:23:"bookings.delete.one.own";}', '2008-12-02'),
(7, 'Guests', 'Default group for guests', 0, 1, 'current', 'a:1:{i:0;s:8:"bookings";}', '0000-00-00'),
(9, 'Foo', '', 0, NULL, NULL, NULL, '2009-01-23');

-- --------------------------------------------------------

--
-- Table structure for table `groups2ldapgroups`
--

DROP TABLE IF EXISTS `groups2ldapgroups`;
CREATE TABLE `groups2ldapgroups` (
  `group_id` int(10) unsigned NOT NULL,
  `ldapgroup_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `ldapgroup_id` (`ldapgroup_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Maps 1+ LDAp groups to 1 CRBS group';

--
-- Dumping data for table `groups2ldapgroups`
--

INSERT INTO `groups2ldapgroups` (`group_id`, `ldapgroup_id`) VALUES
(2, 1120),
(4, 1118);

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

DROP TABLE IF EXISTS `holidays`;
CREATE TABLE `holidays` (
  `holiday_id` int(10) unsigned NOT NULL auto_increment,
  `year_id` int(10) unsigned NOT NULL COMMENT 'The academic year that this holiday is relevant to',
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `name` varchar(20) default NULL,
  PRIMARY KEY  (`holiday_id`),
  KEY `year_id` (`year_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='School holidays' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`holiday_id`, `year_id`, `date_start`, `date_end`, `name`) VALUES
(4, 1, '2009-01-12', '2009-01-24', 'Foo'),
(5, 1, '2009-01-22', '2009-01-22', 'test2');

-- --------------------------------------------------------

--
-- Table structure for table `ldapgroups`
--

DROP TABLE IF EXISTS `ldapgroups`;
CREATE TABLE `ldapgroups` (
  `ldapgroup_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(104) NOT NULL COMMENT 'Name of LDAP group (not full DN, just name part)',
  PRIMARY KEY  (`ldapgroup_id`),
  UNIQUE KEY `ldapgroup_id` (`ldapgroup_id`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Group names retrieved from LDAP; InnoDB free: 9216 kB' AUTO_INCREMENT=1222 ;

--
-- Dumping data for table `ldapgroups`
--

INSERT INTO `ldapgroups` (`ldapgroup_id`, `name`) VALUES
(1116, 'BBS Print Operators'),
(1117, 'BBS Staff Print Operators'),
(1118, 'BBS Non-Teaching Staff'),
(1119, 'BBS Students'),
(1120, 'BBS Teaching Staff'),
(1121, 'BBS Accessibility'),
(1122, 'BBS Internet Disabled'),
(1123, 'BBS Guest UserType'),
(1124, 'BBS RM Explorer UserType'),
(1125, 'BBS Restricted UserType'),
(1126, 'BBS Standard UserType'),
(1127, 'BBS Advanced UserType'),
(1128, 'BBS Staff UserType'),
(1129, 'BBS Advanced Staff UserType'),
(1130, 'BBS Advanced Station Security'),
(1131, 'BBS Standard Station Security'),
(1132, 'BBS No Station Security'),
(1133, 'BBS Shared Laptop StationType'),
(1134, 'BBS Managed Stations'),
(1135, 'BBS Authorised ManagerType'),
(1136, 'BBS Delegate ManagerType'),
(1137, 'BBS Shared Desktop StationType'),
(1138, 'BBS Personal StationType'),
(1139, 'BBS CyberCafe StationType'),
(1140, 'BBS EasyLink'),
(1141, 'BBS Education Management System'),
(1142, 'BBS Legacy Application Users'),
(1143, 'BBS Management Information System'),
(1144, 'BBS Technology Teachers'),
(1145, 'BBS Local Administrators'),
(1146, 'BBS Station Setup'),
(1147, 'BBS Science Teachers'),
(1148, 'BBS Leisure and Tourism Teachers'),
(1149, 'BBS ICT Teachers'),
(1150, 'BBS Art Teachers'),
(1151, 'BBS CD Burning'),
(1152, 'BBS Textiles Teachers'),
(1153, 'BBS PowerDVD'),
(1154, 'BBS Maths Teachers'),
(1155, 'BBS Food Technology Teachers'),
(1156, 'BBS MFL Teachers'),
(1157, 'BBS English Teachers'),
(1158, 'BBS Music Teachers'),
(1159, 'BBS Physical Education Teachers'),
(1160, 'BBS Performing Arts Teachers'),
(1161, 'BBS Media Studies Teachers'),
(1162, 'BBS History Teachers'),
(1163, 'BBS Religious Education Teachers'),
(1164, 'BBS Science'),
(1165, 'BBS RMMC AccessRight'),
(1166, 'BBS User Controller ManagerType'),
(1167, 'BBS EDI System'),
(1168, 'BBS Finance System'),
(1169, 'BBS Library System'),
(1170, 'BBS MIS Manager'),
(1171, 'BBS Network ManagerType'),
(1172, 'BBS Sleuth Users'),
(1173, 'BBS Staff Absences'),
(1174, 'BBS School Income'),
(1175, 'BBS RMSecurenet'),
(1176, 'BBS Maths'),
(1177, 'BBS Science Exam'),
(1178, 'BBS Admin Users'),
(1179, 'BBS Geography Teachers'),
(1180, 'BBS No GPO Security'),
(1181, 'BBS Associates'),
(1182, 'BBS Science year 11'),
(1183, 'BBS Science year 10'),
(1184, 'BBS Science Review'),
(1185, 'BBS Careers Teacher'),
(1186, 'BBS RE Teachers'),
(1187, 'BBS Detention DB Users'),
(1188, 'BBS Eregistration'),
(1189, 'BBS Interactive Whiteboard'),
(1190, 'BBS Humanities'),
(1191, 'BBS Science year 9'),
(1192, 'BBS Legal Team'),
(1193, 'BBS Leisure and Tourism'),
(1194, 'BBS QuarkXPress Users'),
(1195, 'BBS Quizdom'),
(1196, 'BBS BKSB'),
(1197, 'BBS Staff DAP UserType'),
(1198, 'BBS Design Teachers'),
(1199, 'BBS PE Teachers'),
(1200, 'BBS Exam Users'),
(1201, 'Terminal Services Users'),
(1202, 'BBS Shared Desktop 1280 StationType'),
(1203, 'BBS SecureNet'),
(1204, 'BBS Exam Officer'),
(1205, 'BBS Admin Staff UserType'),
(1206, 'BBS AnyComms Users'),
(1207, 'BBS Careers Teachers'),
(1208, 'BBS BKSB Manager'),
(1209, 'BBS Copy of Advanced UserType'),
(1210, 'BBS SEN Teachers'),
(1211, 'BBS SEN Students'),
(1212, 'BBS Childcare'),
(1213, 'BBS Truancy Call'),
(1214, 'BBS SSP'),
(1215, 'BBS Email disabled'),
(1216, 'BBS School Fund Officer'),
(1217, 'BBS IT authorised UserType'),
(1218, 'BBS Copy of Restricted UserType'),
(1219, 'BBS Encrypted Folder'),
(1220, 'BBS Guest 2 UserType'),
(1221, 'BBS HSS Finance');

-- --------------------------------------------------------

--
-- Table structure for table `periods`
--

DROP TABLE IF EXISTS `periods`;
CREATE TABLE `periods` (
  `period_id` int(10) unsigned NOT NULL auto_increment,
  `year_id` int(10) unsigned NOT NULL,
  `time_start` time NOT NULL,
  `time_end` time NOT NULL,
  `name` varchar(20) NOT NULL,
  `days` varchar(255) NOT NULL COMMENT 'Serialize() of the days that this period is set on',
  `bookable` tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0 if periods can be booked or not',
  PRIMARY KEY  (`period_id`),
  KEY `year_id` (`year_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Periods' AUTO_INCREMENT=50 ;

--
-- Dumping data for table `periods`
--

INSERT INTO `periods` (`period_id`, `year_id`, `time_start`, `time_end`, `name`, `days`, `bookable`) VALUES
(9, 1, '08:45:00', '09:00:00', 'Registration', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 0),
(10, 1, '09:00:00', '10:00:00', 'Period 1', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 1),
(11, 1, '10:00:00', '11:00:00', 'Period 2', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 1),
(12, 1, '11:00:00', '11:15:00', 'Break', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 0),
(13, 1, '11:15:00', '12:15:00', 'Period 3', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 1),
(14, 1, '12:15:00', '13:20:00', 'Lunch', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 0),
(15, 1, '13:20:00', '14:20:00', 'Period 4', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 1),
(16, 1, '14:20:00', '15:20:00', 'Period 5', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 1),
(49, 1, '15:20:00', '18:30:00', 'After school', 'a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}', 1);

-- --------------------------------------------------------

--
-- Table structure for table `quota`
--

DROP TABLE IF EXISTS `quota`;
CREATE TABLE `quota` (
  `user_id` int(10) unsigned NOT NULL,
  `quota_num` int(10) unsigned NOT NULL,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Quota details';

--
-- Dumping data for table `quota`
--


-- --------------------------------------------------------

--
-- Table structure for table `room-permissions`
--

DROP TABLE IF EXISTS `room-permissions`;
CREATE TABLE `room-permissions` (
  `entry_id` int(10) unsigned NOT NULL auto_increment,
  `room_id` int(10) unsigned NOT NULL,
  `type` enum('e','o','u','g','d') NOT NULL COMMENT 'E: everyone; O: owner; U: user; G: group; D: department',
  `user_id` int(10) unsigned default NULL,
  `group_id` int(10) unsigned default NULL,
  `department_id` int(10) unsigned default NULL,
  `permissions` text NOT NULL,
  `hash` char(32) NOT NULL,
  PRIMARY KEY  (`entry_id`),
  UNIQUE KEY `hash` (`hash`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`),
  KEY `department_id` (`department_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Permission entries for various objects on different rooms' AUTO_INCREMENT=14 ;

--
-- Dumping data for table `room-permissions`
--

INSERT INTO `room-permissions` (`entry_id`, `room_id`, `type`, `user_id`, `group_id`, `department_id`, `permissions`, `hash`) VALUES
(6, 4, 'u', 121, NULL, NULL, 'a:1:{i:0;s:13:"bookings.view";}', '62082762a2271b7f2e05ce12c6ffe7ef'),
(10, 5, 'e', NULL, NULL, NULL, 'a:1:{i:0;s:13:"bookings.view";}', '73eadfcb737ae0f288fca3f4e03cb2fa'),
(11, 4, 'e', NULL, NULL, NULL, 'a:1:{i:0;s:13:"bookings.view";}', '39d40f538d89baaeea6ca1eb78dffad6'),
(12, 4, 'g', NULL, 1, NULL, 'a:10:{i:0;s:13:"bookings.view";i:1;s:19:"bookings.create.one";i:2;s:21:"bookings.create.recur";i:3;s:22:"bookings.create.behalf";i:4;s:23:"bookings.delete.own.one";i:5;s:25:"bookings.delete.own.recur";i:6;s:25:"bookings.delete.other.one";i:7;s:27:"bookings.delete.other.recur";i:8;s:17:"bookings.edit.one";i:9;s:19:"bookings.edit.recur";}', 'a2b539ebd74dae502c261cb6f4e32829'),
(13, 4, 'o', NULL, NULL, NULL, 'a:3:{i:0;s:13:"bookings.view";i:1;s:19:"bookings.create.one";i:2;s:21:"bookings.create.recur";}', 'fc8c066bd4e1bd039b21340119ce520e');

-- --------------------------------------------------------

--
-- Table structure for table `roomattrs-fields`
--

DROP TABLE IF EXISTS `roomattrs-fields`;
CREATE TABLE `roomattrs-fields` (
  `field_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(20) NOT NULL,
  `type` enum('text','select','check') NOT NULL COMMENT 'Text: textbox; Select: Choose one item from list; Check: Boolean on/off',
  `options_md5` char(32) default NULL,
  PRIMARY KEY  (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Names of fields that can be assigned to rooms' AUTO_INCREMENT=19 ;

--
-- Dumping data for table `roomattrs-fields`
--

INSERT INTO `roomattrs-fields` (`field_id`, `name`, `type`, `options_md5`) VALUES
(13, 'Text field 1', 'text', NULL),
(14, 'Text field 2', 'text', NULL),
(15, 'List box 1', 'select', '2fa179a7b39047609ae590d12642b797'),
(16, 'List box 2', 'select', '307751a5b1f09e282c251c82fcaaae5c'),
(17, 'Tick 1', 'check', NULL),
(18, 'Tick 2', 'check', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roomattrs-options`
--

DROP TABLE IF EXISTS `roomattrs-options`;
CREATE TABLE `roomattrs-options` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `field_id` int(10) unsigned NOT NULL COMMENT 'Field that this belongs to',
  `value` varchar(50) NOT NULL,
  PRIMARY KEY  (`option_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Options for room drop-down fields' AUTO_INCREMENT=33 ;

--
-- Dumping data for table `roomattrs-options`
--

INSERT INTO `roomattrs-options` (`option_id`, `field_id`, `value`) VALUES
(27, 15, 'One'),
(28, 15, 'Two'),
(29, 15, 'Three'),
(30, 16, 'Four'),
(31, 16, 'Five'),
(32, 16, 'Six');

-- --------------------------------------------------------

--
-- Table structure for table `roomattrs-values`
--

DROP TABLE IF EXISTS `roomattrs-values`;
CREATE TABLE `roomattrs-values` (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `room_id` int(10) unsigned NOT NULL,
  `field_id` int(10) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `attr` (`room_id`,`field_id`),
  KEY `field_id` (`field_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Actual values of room fields for each room' AUTO_INCREMENT=55 ;

--
-- Dumping data for table `roomattrs-values`
--

INSERT INTO `roomattrs-values` (`value_id`, `room_id`, `field_id`, `value`) VALUES
(49, 4, 15, '29'),
(50, 4, 16, '32'),
(51, 4, 13, ''),
(52, 4, 14, 'def'),
(53, 4, 17, '1'),
(54, 4, 18, '1');

-- --------------------------------------------------------

--
-- Table structure for table `roomcategories`
--

DROP TABLE IF EXISTS `roomcategories`;
CREATE TABLE `roomcategories` (
  `category_id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(25) NOT NULL,
  PRIMARY KEY  (`category_id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Categories that rooms can belong to' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `roomcategories`
--

INSERT INTO `roomcategories` (`category_id`, `name`) VALUES
(4, '0'),
(1, 'ICT'),
(3, 'Maths'),
(2, 'Technology');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `room_id` int(10) unsigned NOT NULL auto_increment,
  `category_id` int(10) unsigned default NULL COMMENT 'An optional category that the room can belong to',
  `user_id` int(10) unsigned default NULL COMMENT 'Specifies an owner (user) of the room',
  `order` tinyint(3) unsigned default NULL COMMENT 'Order that the rooms appear in (optional)',
  `name` varchar(20) NOT NULL,
  `description` varchar(40) default NULL,
  `bookable` tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0',
  `photo` char(32) default NULL COMMENT 'An md5 hash that references the file that is stored',
  `created` date default NULL COMMENT 'Date the entry was created',
  PRIMARY KEY  (`room_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='School rooms' AUTO_INCREMENT=6 ;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `category_id`, `user_id`, `order`, `name`, `description`, `bookable`, `photo`, `created`) VALUES
(1, 1, 121, NULL, 'ICT1', 'ICT Suite / Eve Winstanley', 1, '0', NULL),
(2, 2, 1, NULL, 'RM42', NULL, 1, NULL, NULL),
(3, 1, NULL, NULL, 'ICT2', 'Room 13 / Chris Hudson', 1, '0', NULL),
(4, NULL, NULL, NULL, 'Foobar', 'Foo3', 0, '0', NULL),
(5, 2, NULL, NULL, 'Tech Suite', 'Tech Suite', 0, NULL, '2009-02-13');

-- --------------------------------------------------------

--
-- Table structure for table `settings-auth`
--

DROP TABLE IF EXISTS `settings-auth`;
CREATE TABLE `settings-auth` (
  `preauthkey` char(40) default NULL COMMENT 'SHA1 hash to be used as preauth key',
  `preauthgroup_id` int(10) unsigned default NULL COMMENT 'Default group for accounts created automatically via preauth',
  `preauthemail` varchar(50) default NULL COMMENT 'Email domain for users created via preauth',
  `ldap` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Boolean 1 or 0 for LDAP auth status',
  `ldaphost` varchar(50) default NULL COMMENT 'LDAP server hostname',
  `ldapport` int(5) unsigned default NULL COMMENT 'LDAP server TCP port',
  `ldapbase` text COMMENT 'Base DNs to search in LDAP',
  `ldapfilter` text COMMENT 'LDAP search query filter',
  `ldapgroup_id` int(10) unsigned default NULL COMMENT 'Default group for LDAP accounts',
  `ldaploginupdate` tinyint(1) unsigned default NULL COMMENT 'Boolean. Update user details on every login?'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='LDAP configuration';

--
-- Dumping data for table `settings-auth`
--

INSERT INTO `settings-auth` (`preauthkey`, `preauthgroup_id`, `preauthemail`, `ldap`, `ldaphost`, `ldapport`, `ldapbase`, `ldapfilter`, `ldapgroup_id`, `ldaploginupdate`) VALUES
('5faeb3797924c7598daf85cd70816a2c66be6994', 7, 'bishopbarrington.net', 1, 'bbs-svr-001', 389, 'ou=teaching staff, ou=bbs, ou=establishments, dc=bbarrington, dc=internal; ou=system administrators, ou=bbs, ou=establishments, dc=bbarrington, dc=internal', '(& (| (!(displayname=Administrator*)) (!(displayname=Admin*)) ) (cn=%u) )', 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `settings-main`
--

DROP TABLE IF EXISTS `settings-main`;
CREATE TABLE `settings-main` (
  `schoolname` varchar(100) default NULL COMMENT 'Name of school',
  `schoolurl` varchar(255) default NULL COMMENT 'Web address for school',
  `bd_mode` enum('room','day') default NULL COMMENT 'Mode of display for room booking table',
  `bd_col` enum('periods','rooms','days') default NULL COMMENT 'Columns on the booking table',
  `room_order` enum('alpha','order') default NULL COMMENT 'How to display rooms in the booking view'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Global app settings';

--
-- Dumping data for table `settings-main`
--

INSERT INTO `settings-main` (`schoolname`, `schoolurl`, `bd_mode`, `bd_col`, `room_order`) VALUES
('Bishop Barrington School Sports With Mathematics College', '', 'day', 'periods', 'alpha');

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
CREATE TABLE `terms` (
  `term_id` int(10) unsigned NOT NULL auto_increment,
  `year_id` int(10) unsigned NOT NULL COMMENT 'The academic year that this term belongs to',
  `date_start` date NOT NULL COMMENT 'Start date of the term',
  `date_end` date NOT NULL COMMENT 'End date of the term',
  `name` varchar(40) NOT NULL COMMENT 'Name of the term',
  PRIMARY KEY  (`term_id`),
  UNIQUE KEY `uniquedates` (`date_start`,`date_end`),
  UNIQUE KEY `date_start` (`date_start`),
  UNIQUE KEY `date_end` (`date_end`),
  KEY `year_id` (`year_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Term dates' AUTO_INCREMENT=11 ;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`term_id`, `year_id`, `date_start`, `date_end`, `name`) VALUES
(1, 1, '2008-09-08', '2008-10-24', 'Autumn'),
(2, 1, '2009-01-05', '2009-02-13', 'Spring A'),
(10, 1, '2009-04-09', '2009-05-16', 'Foo');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL COMMENT 'Group that the user is a member of',
  `enabled` tinyint(1) NOT NULL default '0' COMMENT 'Boolean 1 or 0',
  `username` varchar(104) NOT NULL,
  `email` varchar(255) default NULL,
  `password` char(40) default NULL COMMENT 'SHA1 hash of password',
  `displayname` varchar(64) default NULL,
  `cookiekey` char(40) default NULL COMMENT 'SHA1 hash if a cookie is required',
  `lastlogin` timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT 'Date the user last logged in',
  `ldap` tinyint(1) unsigned NOT NULL default '0' COMMENT 'Boolean 1 or 0 if user should authenticate via LDAP',
  `created` date NOT NULL COMMENT 'Date the user was created',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `ldap` (`ldap`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Main users table' AUTO_INCREMENT=127 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `group_id`, `enabled`, `username`, `email`, `password`, `displayname`, `cookiekey`, `lastlogin`, `ldap`, `created`) VALUES
(1, 1, 1, 'admin', 'craig.rodway@gmail.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Craig Rodway', NULL, '2009-05-14 11:48:12', 0, '0000-00-00'),
(12, 2, 1, 'craig.rodway', 'craig.rodway@bishopbarrington.net', NULL, 'Mr Rodway', NULL, '2009-01-09 16:12:48', 1, '2009-01-09'),
(19, 2, 1, 'test.one', 'test.one@bishopbarrington.net', NULL, 'Mr T One', NULL, '2009-05-05 09:28:33', 1, '2009-01-14'),
(22, 2, 1, 'test.three', 'test.three@bishopbarrington.net', NULL, 'Mr T Three', NULL, '2009-01-14 10:56:57', 1, '2009-01-14'),
(24, 2, 1, 'test.two', 'test.two@bishopbarrington.net', NULL, 'Mr T Two', NULL, '2009-01-26 16:45:49', 1, '2009-01-26'),
(112, 2, 0, 'g.harrison100', 'g.harrison100@bishopbarrington.net', '39ccb32d95edfdbcd882f2b01809724ec640ea16', 'g.harrison100', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(113, 2, 0, 'j.gent100', 'j.gent100@bishopbarrington.net', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'j.gent100', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(114, 2, 0, 'k.hammerton100', 'k.hammerton100@bishopbarrington.net', 'be8ec20d52fdf21c23e83ba2bb7446a7fecb32ac', 'k.hammerton100', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(115, 2, 0, 'l.johnson100', 'l.johnson100@bishopbarrington.net', '3a56bca418737e68a7620591abd0e7e8484458a6', 'l.johnson100', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(116, 2, 0, 'm.bennett103', 'm.bennett103@bishopbarrington.net', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'm.bennett103', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(118, 2, 0, 'p.beighton100', 'p.beighton100@bishopbarrington.net', '32ba707d8ae992ced8648716fbd88002fc5be03a', 'p.beighton100', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(120, 2, 0, 'h.smith104', 'h.smith104@bishopbarrington.net', 'c06538faae9975cce73fc613a8370ba3ffb3d302', 'h.smith104', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(121, 2, 0, 'a.staff100', 'a.staff100@bishopbarrington.net', 'ef20a06d2c45dd9f6a58eacaa6b36d6fc89870a6', 'a.staff100', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(122, 2, 0, 'm.stokoe102', 'm.stokoe102@bishopbarrington.net', 'deaae441b2d1596d06f01725f930ed2f2e7277bd', 'm.stokoe102', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(123, 2, 0, 'j.thompson106', 'j.thompson106@bishopbarrington.net', '78c94605b024fc545b9100d2734dc4a4ae8a8335', 'j.thompson106', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(125, 2, 0, 'c.wearmouth100', 'c.wearmouth100@bishopbarrington.net', '9e907431a8d31fefe3c2d341ff8826624c954f15', 'c.wearmouth100', NULL, '0000-00-00 00:00:00', 0, '2009-01-30'),
(126, 2, 0, 'e.winstanley100', 'e.winstanley100@bishopbarrington.net', 'ea157601840a5b4953c2e95f5fd27223291122d6', 'e.winstanley100', NULL, '0000-00-00 00:00:00', 0, '2009-01-30');

-- --------------------------------------------------------

--
-- Table structure for table `users2departments`
--

DROP TABLE IF EXISTS `users2departments`;
CREATE TABLE `users2departments` (
  `user_id` int(10) unsigned NOT NULL,
  `department_id` int(10) unsigned NOT NULL,
  KEY `department_id` (`department_id`),
  KEY `assignment` (`user_id`,`department_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Maps a user to multiple departments';

--
-- Dumping data for table `users2departments`
--

INSERT INTO `users2departments` (`user_id`, `department_id`) VALUES
(19, 2),
(19, 2),
(19, 4);

-- --------------------------------------------------------

--
-- Table structure for table `usersactive`
--

DROP TABLE IF EXISTS `usersactive`;
CREATE TABLE `usersactive` (
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` int(11) unsigned NOT NULL,
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Keep track of current active users';

--
-- Dumping data for table `usersactive`
--

INSERT INTO `usersactive` (`user_id`, `timestamp`) VALUES
(1, 1242399632);

-- --------------------------------------------------------

--
-- Table structure for table `weekdates`
--

DROP TABLE IF EXISTS `weekdates`;
CREATE TABLE `weekdates` (
  `week_id` int(10) unsigned NOT NULL,
  `year_id` int(10) unsigned NOT NULL,
  `date` date NOT NULL,
  UNIQUE KEY `date` (`date`),
  KEY `week_id` (`week_id`),
  KEY `year_id` (`year_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Start-dates of weeks for timetable';

--
-- Dumping data for table `weekdates`
--

INSERT INTO `weekdates` (`week_id`, `year_id`, `date`) VALUES
(11, 1, '2008-09-08'),
(12, 1, '2008-09-15'),
(11, 1, '2008-09-22'),
(12, 1, '2008-09-29'),
(11, 1, '2008-10-06'),
(12, 1, '2008-10-13'),
(11, 1, '2008-10-20'),
(12, 1, '2008-11-03'),
(11, 1, '2008-11-10'),
(12, 1, '2008-11-17'),
(11, 1, '2008-11-24'),
(12, 1, '2008-12-01'),
(11, 1, '2008-12-08'),
(12, 1, '2008-12-15'),
(11, 1, '2009-01-05'),
(12, 1, '2009-01-12'),
(11, 1, '2009-01-19'),
(12, 1, '2009-01-26'),
(11, 1, '2009-02-02'),
(12, 1, '2009-02-09'),
(11, 1, '2009-02-23'),
(12, 1, '2009-03-02'),
(11, 1, '2009-03-09'),
(12, 1, '2009-03-16'),
(11, 1, '2009-03-23'),
(12, 1, '2009-03-30'),
(11, 1, '2009-04-20'),
(12, 1, '2009-04-27'),
(11, 1, '2009-05-04'),
(12, 1, '2009-05-11'),
(11, 1, '2009-05-18'),
(12, 1, '2009-06-01'),
(11, 1, '2009-06-08'),
(12, 1, '2009-06-15'),
(11, 1, '2009-06-22'),
(12, 1, '2009-06-29'),
(11, 1, '2009-07-06'),
(12, 1, '2009-07-13'),
(11, 1, '2009-07-20');

-- --------------------------------------------------------

--
-- Table structure for table `weeks`
--

DROP TABLE IF EXISTS `weeks`;
CREATE TABLE `weeks` (
  `week_id` int(10) unsigned NOT NULL auto_increment,
  `year_id` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL,
  `colour` char(7) default NULL COMMENT 'Hex colour value including hash',
  `created` date default NULL,
  PRIMARY KEY  (`week_id`),
  KEY `year_id` (`year_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Week definitions for timetable weeks' AUTO_INCREMENT=13 ;

--
-- Dumping data for table `weeks`
--

INSERT INTO `weeks` (`week_id`, `year_id`, `name`, `colour`, `created`) VALUES
(11, 1, 'Red Week', '#EF2929', '2009-01-25'),
(12, 1, 'Blue Week', '#729FCF', '2009-01-25');

-- --------------------------------------------------------

--
-- Table structure for table `years`
--

DROP TABLE IF EXISTS `years`;
CREATE TABLE `years` (
  `year_id` int(10) unsigned NOT NULL auto_increment,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `name` varchar(20) NOT NULL,
  `active` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`year_id`),
  UNIQUE KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='Academic year definitions' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `years`
--

INSERT INTO `years` (`year_id`, `date_start`, `date_end`, `name`, `active`) VALUES
(1, '2008-09-08', '2009-07-23', '2008 - 2009', 1),
(5, '2009-09-07', '2010-07-23', '2009 - 2010', NULL),
(6, '2007-09-01', '2008-07-24', '2007 - 2008', NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `groups2ldapgroups`
--
ALTER TABLE `groups2ldapgroups`
  ADD CONSTRAINT `groups2ldapgroups_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groups2ldapgroups_ibfk_2` FOREIGN KEY (`ldapgroup_id`) REFERENCES `ldapgroups` (`ldapgroup_id`) ON DELETE CASCADE;

--
-- Constraints for table `holidays`
--
ALTER TABLE `holidays`
  ADD CONSTRAINT `holidays_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `years` (`year_id`) ON DELETE CASCADE;

--
-- Constraints for table `periods`
--
ALTER TABLE `periods`
  ADD CONSTRAINT `periods_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `years` (`year_id`) ON DELETE CASCADE;

--
-- Constraints for table `quota`
--
ALTER TABLE `quota`
  ADD CONSTRAINT `quota_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `room-permissions`
--
ALTER TABLE `room-permissions`
  ADD CONSTRAINT `room-permissions_ibfk_4` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room-permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room-permissions_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `room-permissions_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `roomattrs-options`
--
ALTER TABLE `roomattrs-options`
  ADD CONSTRAINT `roomattrs-options_ibfk_1` FOREIGN KEY (`field_id`) REFERENCES `roomattrs-fields` (`field_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `roomattrs-values`
--
ALTER TABLE `roomattrs-values`
  ADD CONSTRAINT `roomattrs-values_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roomattrs-values_ibfk_2` FOREIGN KEY (`field_id`) REFERENCES `roomattrs-fields` (`field_id`) ON DELETE CASCADE;

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `roomcategories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `terms`
--
ALTER TABLE `terms`
  ADD CONSTRAINT `terms_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `years` (`year_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`);

--
-- Constraints for table `users2departments`
--
ALTER TABLE `users2departments`
  ADD CONSTRAINT `users2departments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users2departments_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE CASCADE;

--
-- Constraints for table `weekdates`
--
ALTER TABLE `weekdates`
  ADD CONSTRAINT `weekdates_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `weeks` (`week_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `weekdates_ibfk_2` FOREIGN KEY (`year_id`) REFERENCES `years` (`year_id`) ON DELETE CASCADE;

--
-- Constraints for table `weeks`
--
ALTER TABLE `weeks`
  ADD CONSTRAINT `weeks_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `years` (`year_id`) ON DELETE CASCADE;

SET FOREIGN_KEY_CHECKS=1;
