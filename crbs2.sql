# HeidiSQL Dump 
#
# --------------------------------------------------------
# Host:                 127.0.0.1
# Database:             crbs2
# Server version:       5.0.51b-community-nt
# Server OS:            Win32
# Target-Compatibility: Standard ANSI SQL
# HeidiSQL version:     3.2 Revision: 1129
# --------------------------------------------------------

/*!40100 SET CHARACTER SET latin1;*/
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ANSI';*/
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;*/


#
# Database structure for database 'crbs2'
#

DROP DATABASE IF EXISTS "crbs2";
CREATE DATABASE "crbs2" /*!40100 DEFAULT CHARACTER SET latin1 */;

USE "crbs2";


#
# Table structure for table 'ci_sessions'
#

CREATE TABLE "ci_sessions" (
  "session_id" varchar(40) NOT NULL default '0',
  "ip_address" varchar(16) NOT NULL default '0',
  "user_agent" varchar(50) NOT NULL,
  "last_activity" int(10) unsigned NOT NULL default '0',
  "user_data" text NOT NULL,
  PRIMARY KEY  ("session_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='CodeIgniter sessions table'*/;



#
# Dumping data for table 'ci_sessions'
#

LOCK TABLES "ci_sessions" WRITE;
/*!40000 ALTER TABLE "ci_sessions" DISABLE KEYS;*/
INSERT INTO "ci_sessions" ("session_id", "ip_address", "user_agent", "last_activity", "user_data") VALUES
	('ac539b118e4f07c593a64ad3c60f46fc','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1231414954','a:1:{s:17:"group_permissions";s:0:"";}');
INSERT INTO "ci_sessions" ("session_id", "ip_address", "user_agent", "last_activity", "user_data") VALUES
	('d110909bdd379009e058558ca0c8e0da','127.0.0.1','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1;','1231414638','a:1:{s:17:"group_permissions";s:0:"";}');
/*!40000 ALTER TABLE "ci_sessions" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'departments'
#

CREATE TABLE "departments" (
  "department_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(64) NOT NULL,
  "description" varchar(255) default NULL,
  "colour" char(7) default NULL COMMENT 'Hex colour value',
  "created" date default NULL,
  PRIMARY KEY  ("department_id")
) AUTO_INCREMENT=14 /*!40100 DEFAULT CHARSET=latin1 COMMENT='School departments'*/;



#
# Dumping data for table 'departments'
#

LOCK TABLES "departments" WRITE;
/*!40000 ALTER TABLE "departments" DISABLE KEYS;*/
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('1','English','','#204A87','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('2','Maths','','#C4A000','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('3','Science','','#729FCF','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('4','ICT','','#BABDB6','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('5','Music','','#F57900','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('6','History','','#8F5902','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('7','Art','','#A40000','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('9','RE','','#EF2929','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('10','Geography','','#8AE234','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('11','Languages','','#AD7FA8','2009-01-09');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('12','PE','','#2E3436','2009-01-09');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('13','Technology','','#FCE94F','2009-01-09');
/*!40000 ALTER TABLE "departments" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'departments2ldapgroups'
#

CREATE TABLE "departments2ldapgroups" (
  "department_id" int(10) unsigned NOT NULL,
  "ldapgroup_id" int(10) unsigned NOT NULL,
  UNIQUE KEY "unique" ("department_id","ldapgroup_id"),
  KEY "department_id" ("department_id"),
  KEY "ldapgroup_id" ("ldapgroup_id")
) /*!40100 DEFAULT CHARSET=latin1*/;



#
# Dumping data for table 'departments2ldapgroups'
#

LOCK TABLES "departments2ldapgroups" WRITE;
/*!40000 ALTER TABLE "departments2ldapgroups" DISABLE KEYS;*/
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('1','1157');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('2','1154');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('2','1176');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('3','1147');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('3','1164');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('4','1149');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('5','1158');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('6','1162');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('6','1190');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('7','1150');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('9','1163');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('9','1186');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('10','1179');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('10','1190');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('11','1156');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('12','1159');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('12','1199');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('13','1144');
/*!40000 ALTER TABLE "departments2ldapgroups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'groups'
#

CREATE TABLE "groups" (
  "group_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(20) NOT NULL,
  "description" varchar(255) default NULL,
  "bookahead" smallint(3) unsigned default NULL COMMENT 'Days ahead users in this group can make a booking',
  "quota_num" int(10) unsigned default NULL COMMENT 'Default quota amount',
  "quota_type" enum('day','week','month','current') default NULL COMMENT 'Type of quota in use',
  "permissions" text COMMENT 'A PHP-serialize()''d chunk of data',
  "created" date NOT NULL COMMENT 'Date the group was created',
  PRIMARY KEY  ("group_id")
) AUTO_INCREMENT=10 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Groups table with settings and permiss; InnoDB free: 9216 kB'*/;



#
# Dumping data for table 'groups'
#

LOCK TABLES "groups" WRITE;
/*!40000 ALTER TABLE "groups" DISABLE KEYS;*/
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('1','Administrators','Default group for administrator users',0,NULL,NULL,'a:64:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:9:"configure";i:5;s:10:"changeyear";i:6;s:8:"bookings";i:7;s:19:"bookings.create.one";i:8;s:21:"bookings.create.recur";i:9;s:23:"bookings.delete.one.own";i:10;s:29:"bookings.delete.one.roomowner";i:11;s:31:"bookings.delete.recur.roomowner";i:12;s:22:"bookings.overwrite.one";i:13;s:24:"bookings.overwrite.recur";i:14;s:32:"bookings.overwrite.one.roomowner";i:15;s:34:"bookings.overwrite.recur.roomowner";i:16;s:5:"rooms";i:17;s:9:"rooms.add";i:18;s:10:"rooms.edit";i:19;s:12:"rooms.delete";i:20;s:12:"rooms.fields";i:21;s:19:"rooms.fields.values";i:22;s:8:"academic";i:23;s:5:"years";i:24;s:9:"years.add";i:25;s:10:"years.edit";i:26;s:12:"years.delete";i:27;s:7:"periods";i:28;s:11:"periods.add";i:29;s:12:"periods.edit";i:30;s:14:"periods.delete";i:31;s:5:"weeks";i:32;s:9:"weeks.add";i:33;s:10:"weeks.edit";i:34;s:12:"weeks.delete";i:35;s:19:"weeks.ayears.manage";i:36;s:16:"weeks.ayears.set";i:37;s:5:"terms";i:38;s:9:"terms.add";i:39;s:10:"terms.edit";i:40;s:12:"terms.delete";i:41;s:8:"holidays";i:42;s:12:"holidays.add";i:43;s:13:"holidays.edit";i:44;s:15:"holidays.delete";i:45;s:11:"departments";i:46;s:15:"departments.add";i:47;s:16:"departments.edit";i:48;s:18:"departments.delete";i:49;s:7:"reports";i:50;s:21:"reports.owndepartment";i:51;s:22:"reports.alldepartments";i:52;s:15:"reports.ownroom";i:53;s:16:"reports.allrooms";i:54;s:13:"reports.other";i:55;s:5:"users";i:56;s:9:"users.add";i:57;s:10:"users.edit";i:58;s:12:"users.delete";i:59;s:12:"users.import";i:60;s:6:"groups";i:61;s:10:"groups.add";i:62;s:11:"groups.edit";i:63;s:13:"groups.delete";}','0000-00-00');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('2','Teaching Staff','Teachers from LDAP',14,'6','day','a:7:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:8:"bookings";i:5;s:19:"bookings.create.one";i:6;s:23:"bookings.delete.one.own";}','0000-00-00');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('4','Support staff','',14,'6','current','a:7:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:8:"bookings";i:5;s:19:"bookings.create.one";i:6;s:23:"bookings.delete.one.own";}','2008-12-02');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('7','Guests','Default group for guests',0,'1','current','a:1:{i:0;s:8:"bookings";}','0000-00-00');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('9','Foo','',0,NULL,NULL,NULL,'2009-01-23');
/*!40000 ALTER TABLE "groups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'groups2ldapgroups'
#

CREATE TABLE "groups2ldapgroups" (
  "group_id" int(10) unsigned NOT NULL,
  "ldapgroup_id" int(10) unsigned NOT NULL,
  UNIQUE KEY "ldapgroup_id" ("ldapgroup_id"),
  KEY "group_id" ("group_id"),
  CONSTRAINT "groups2ldapgroups_ibfk_1" FOREIGN KEY ("group_id") REFERENCES "groups" ("group_id") ON DELETE CASCADE,
  CONSTRAINT "groups2ldapgroups_ibfk_2" FOREIGN KEY ("ldapgroup_id") REFERENCES "ldapgroups" ("ldapgroup_id") ON DELETE CASCADE
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Maps 1+ LDAp groups to 1 CRBS group'*/;



#
# Dumping data for table 'groups2ldapgroups'
#

LOCK TABLES "groups2ldapgroups" WRITE;
/*!40000 ALTER TABLE "groups2ldapgroups" DISABLE KEYS;*/
INSERT INTO "groups2ldapgroups" ("group_id", "ldapgroup_id") VALUES
	('2','1120');
INSERT INTO "groups2ldapgroups" ("group_id", "ldapgroup_id") VALUES
	('4','1118');
/*!40000 ALTER TABLE "groups2ldapgroups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'holidays'
#

CREATE TABLE "holidays" (
  "holiday_id" int(10) unsigned NOT NULL auto_increment,
  "year_id" int(10) unsigned NOT NULL COMMENT 'The academic year that this holiday is relevant to',
  "date_start" date NOT NULL,
  "date_end" date NOT NULL,
  "name" varchar(20) default NULL,
  PRIMARY KEY  ("holiday_id"),
  KEY "year_id" ("year_id"),
  CONSTRAINT "holidays_ibfk_1" FOREIGN KEY ("year_id") REFERENCES "years" ("year_id") ON DELETE CASCADE
) AUTO_INCREMENT=6 /*!40100 DEFAULT CHARSET=latin1 COMMENT='School holidays'*/;



#
# Dumping data for table 'holidays'
#

LOCK TABLES "holidays" WRITE;
/*!40000 ALTER TABLE "holidays" DISABLE KEYS;*/
INSERT INTO "holidays" ("holiday_id", "year_id", "date_start", "date_end", "name") VALUES
	('4','1','2009-01-12','2009-01-24','Foo');
INSERT INTO "holidays" ("holiday_id", "year_id", "date_start", "date_end", "name") VALUES
	('5','1','2009-01-22','2009-01-22','test2');
/*!40000 ALTER TABLE "holidays" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'ldapgroups'
#

CREATE TABLE "ldapgroups" (
  "ldapgroup_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(104) NOT NULL COMMENT 'Name of LDAP group (not full DN, just name part)',
  PRIMARY KEY  ("ldapgroup_id"),
  UNIQUE KEY "ldapgroup_id" ("ldapgroup_id","name")
) AUTO_INCREMENT=1222 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Group names retrieved from LDAP; InnoDB free: 9216 kB'*/;



#
# Dumping data for table 'ldapgroups'
#

LOCK TABLES "ldapgroups" WRITE;
/*!40000 ALTER TABLE "ldapgroups" DISABLE KEYS;*/
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1116','BBS Print Operators');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1117','BBS Staff Print Operators');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1118','BBS Non-Teaching Staff');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1119','BBS Students');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1120','BBS Teaching Staff');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1121','BBS Accessibility');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1122','BBS Internet Disabled');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1123','BBS Guest UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1124','BBS RM Explorer UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1125','BBS Restricted UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1126','BBS Standard UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1127','BBS Advanced UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1128','BBS Staff UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1129','BBS Advanced Staff UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1130','BBS Advanced Station Security');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1131','BBS Standard Station Security');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1132','BBS No Station Security');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1133','BBS Shared Laptop StationType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1134','BBS Managed Stations');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1135','BBS Authorised ManagerType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1136','BBS Delegate ManagerType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1137','BBS Shared Desktop StationType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1138','BBS Personal StationType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1139','BBS CyberCafe StationType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1140','BBS EasyLink');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1141','BBS Education Management System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1142','BBS Legacy Application Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1143','BBS Management Information System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1144','BBS Technology Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1145','BBS Local Administrators');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1146','BBS Station Setup');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1147','BBS Science Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1148','BBS Leisure and Tourism Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1149','BBS ICT Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1150','BBS Art Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1151','BBS CD Burning');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1152','BBS Textiles Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1153','BBS PowerDVD');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1154','BBS Maths Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1155','BBS Food Technology Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1156','BBS MFL Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1157','BBS English Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1158','BBS Music Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1159','BBS Physical Education Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1160','BBS Performing Arts Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1161','BBS Media Studies Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1162','BBS History Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1163','BBS Religious Education Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1164','BBS Science');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1165','BBS RMMC AccessRight');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1166','BBS User Controller ManagerType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1167','BBS EDI System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1168','BBS Finance System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1169','BBS Library System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1170','BBS MIS Manager');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1171','BBS Network ManagerType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1172','BBS Sleuth Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1173','BBS Staff Absences');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1174','BBS School Income');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1175','BBS RMSecurenet');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1176','BBS Maths');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1177','BBS Science Exam');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1178','BBS Admin Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1179','BBS Geography Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1180','BBS No GPO Security');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1181','BBS Associates');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1182','BBS Science year 11');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1183','BBS Science year 10');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1184','BBS Science Review');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1185','BBS Careers Teacher');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1186','BBS RE Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1187','BBS Detention DB Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1188','BBS Eregistration');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1189','BBS Interactive Whiteboard');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1190','BBS Humanities');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1191','BBS Science year 9');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1192','BBS Legal Team');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1193','BBS Leisure and Tourism');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1194','BBS QuarkXPress Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1195','BBS Quizdom');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1196','BBS BKSB');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1197','BBS Staff DAP UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1198','BBS Design Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1199','BBS PE Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1200','BBS Exam Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1201','Terminal Services Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1202','BBS Shared Desktop 1280 StationType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1203','BBS SecureNet');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1204','BBS Exam Officer');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1205','BBS Admin Staff UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1206','BBS AnyComms Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1207','BBS Careers Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1208','BBS BKSB Manager');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1209','BBS Copy of Advanced UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1210','BBS SEN Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1211','BBS SEN Students');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1212','BBS Childcare');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1213','BBS Truancy Call');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1214','BBS SSP');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1215','BBS Email disabled');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1216','BBS School Fund Officer');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1217','BBS IT authorised UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1218','BBS Copy of Restricted UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1219','BBS Encrypted Folder');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1220','BBS Guest 2 UserType');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('1221','BBS HSS Finance');
/*!40000 ALTER TABLE "ldapgroups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'periods'
#

CREATE TABLE "periods" (
  "period_id" int(10) unsigned NOT NULL auto_increment,
  "year_id" int(10) unsigned NOT NULL,
  "time_start" time NOT NULL,
  "time_end" time NOT NULL,
  "name" varchar(20) NOT NULL,
  "days" varchar(255) NOT NULL COMMENT 'Serialize() of the days that this period is set on',
  "bookable" tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0 if periods can be booked or not',
  PRIMARY KEY  ("period_id"),
  KEY "year_id" ("year_id"),
  CONSTRAINT "periods_ibfk_1" FOREIGN KEY ("year_id") REFERENCES "years" ("year_id") ON DELETE CASCADE
) AUTO_INCREMENT=68 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Periods'*/;



#
# Dumping data for table 'periods'
#

LOCK TABLES "periods" WRITE;
/*!40000 ALTER TABLE "periods" DISABLE KEYS;*/
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('9','1','08:45:00','09:00:00','Registration','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',0);
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('10','1','09:00:00','10:00:00','Period 1','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',1);
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('11','1','10:00:00','11:00:00','Period 2','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',1);
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('12','1','11:00:00','11:15:00','Break','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',0);
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('13','1','11:15:00','12:15:00','Period 3','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',1);
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('14','1','12:15:00','13:20:00','Lunch','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',0);
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('15','1','13:20:00','14:20:00','Period 4','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',1);
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('16','1','14:20:00','15:20:00','Period 5','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',1);
INSERT INTO "periods" ("period_id", "year_id", "time_start", "time_end", "name", "days", "bookable") VALUES
	('49','1','15:20:00','18:30:00','After school','a:5:{i:0;s:1:"1";i:1;s:1:"2";i:2;s:1:"3";i:3;s:1:"4";i:4;s:1:"5";}',1);
/*!40000 ALTER TABLE "periods" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'quota'
#

CREATE TABLE "quota" (
  "user_id" int(10) unsigned NOT NULL,
  "quota_num" int(10) unsigned NOT NULL,
  UNIQUE KEY "user_id" ("user_id"),
  CONSTRAINT "quota_ibfk_1" FOREIGN KEY ("user_id") REFERENCES "users" ("user_id") ON DELETE CASCADE
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Quota details'*/;



#
# Dumping data for table 'quota'
#

# (No data found.)



#
# Table structure for table 'rooms-fields'
#

CREATE TABLE "rooms-fields" (
  "field_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(50) NOT NULL,
  "type" enum('text','select','check','multi') NOT NULL,
  PRIMARY KEY  ("field_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Names of fields that can be assigned to rooms'*/;



#
# Dumping data for table 'rooms-fields'
#

# (No data found.)



#
# Table structure for table 'rooms-options'
#

CREATE TABLE "rooms-options" (
  "option_id" int(10) unsigned NOT NULL auto_increment,
  "value" varchar(50) NOT NULL,
  PRIMARY KEY  ("option_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Options for room drop-down fields'*/;



#
# Dumping data for table 'rooms-options'
#

# (No data found.)



#
# Table structure for table 'rooms-values'
#

CREATE TABLE "rooms-values" (
  "value_id" int(10) unsigned NOT NULL auto_increment,
  "room_id" int(10) unsigned NOT NULL,
  "field_id" int(10) unsigned NOT NULL,
  "value" varchar(255) NOT NULL,
  PRIMARY KEY  ("value_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Actual values of room fields for each room'*/;



#
# Dumping data for table 'rooms-values'
#

# (No data found.)



#
# Table structure for table 'rooms'
#

CREATE TABLE "rooms" (
  "room_id" int(10) unsigned NOT NULL auto_increment,
  "user_id" int(10) unsigned NOT NULL COMMENT 'Specifies an owner (user) of the room',
  "order" tinyint(3) unsigned default NULL COMMENT 'Order that the rooms appear in (optional)',
  "name" varchar(20) NOT NULL,
  "location" varchar(40) NOT NULL,
  "bookable" tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0',
  "notes" varchar(255) NOT NULL,
  "photo" char(32) NOT NULL COMMENT 'An md5 hash that references the file that is stored',
  PRIMARY KEY  ("room_id"),
  KEY "user_id" ("user_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='School rooms'*/;



#
# Dumping data for table 'rooms'
#

# (No data found.)



#
# Table structure for table 'settings-auth'
#

CREATE TABLE "settings-auth" (
  "preauthkey" char(40) default NULL COMMENT 'SHA1 hash to be used as preauth key',
  "preauthgroup_id" int(10) unsigned default NULL COMMENT 'Default group for accounts created automatically via preauth',
  "preauthemail" varchar(50) default NULL COMMENT 'Email domain for users created via preauth',
  "ldap" tinyint(1) unsigned NOT NULL default '0' COMMENT 'Boolean 1 or 0 for LDAP auth status',
  "ldaphost" varchar(50) default NULL COMMENT 'LDAP server hostname',
  "ldapport" int(5) unsigned default NULL COMMENT 'LDAP server TCP port',
  "ldapbase" text COMMENT 'Base DNs to search in LDAP',
  "ldapfilter" text COMMENT 'LDAP search query filter',
  "ldapgroup_id" int(10) unsigned default NULL COMMENT 'Default group for LDAP accounts',
  "ldaploginupdate" tinyint(1) unsigned default NULL COMMENT 'Boolean. Update user details on every login?'
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='LDAP configuration'*/;



#
# Dumping data for table 'settings-auth'
#

LOCK TABLES "settings-auth" WRITE;
/*!40000 ALTER TABLE "settings-auth" DISABLE KEYS;*/
INSERT INTO "settings-auth" ("preauthkey", "preauthgroup_id", "preauthemail", "ldap", "ldaphost", "ldapport", "ldapbase", "ldapfilter", "ldapgroup_id", "ldaploginupdate") VALUES
	('14ba16efe8ce9786aaa9ca15297f9dc802855476','4','bishopbarrington.net',1,'bbs-svr-001','389','ou=teaching staff, ou=bbs, ou=establishments, dc=bbarrington, dc=internal; ou=system administrators, ou=bbs, ou=establishments, dc=bbarrington, dc=internal','(& (| (!(displayname=Administrator*)) (!(displayname=Admin*)) ) (cn=%u) )','7',1);
/*!40000 ALTER TABLE "settings-auth" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'settings-main'
#

CREATE TABLE "settings-main" (
  "schoolname" varchar(100) default NULL COMMENT 'Name of school',
  "schoolurl" varchar(255) default NULL COMMENT 'Web address for school',
  "bd_mode" enum('room','day') default NULL COMMENT 'Mode of display for room booking table',
  "bd_col" enum('periods','rooms','days') default NULL COMMENT 'Columns on the booking table',
  "room_order" enum('alpha','order') default NULL COMMENT 'How to display rooms in the booking view'
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Global app settings'*/;



#
# Dumping data for table 'settings-main'
#

LOCK TABLES "settings-main" WRITE;
/*!40000 ALTER TABLE "settings-main" DISABLE KEYS;*/
INSERT INTO "settings-main" ("schoolname", "schoolurl", "bd_mode", "bd_col", "room_order") VALUES
	('Bishop Barrington School Sports With Mathematics College','','day','periods','alpha');
/*!40000 ALTER TABLE "settings-main" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'terms'
#

CREATE TABLE "terms" (
  "term_id" int(10) unsigned NOT NULL auto_increment,
  "year_id" int(10) unsigned NOT NULL COMMENT 'The academic year that this term belongs to',
  "date_start" date NOT NULL COMMENT 'Start date of the term',
  "date_end" date NOT NULL COMMENT 'End date of the term',
  "name" varchar(40) NOT NULL COMMENT 'Name of the term',
  PRIMARY KEY  ("term_id"),
  UNIQUE KEY "uniquedates" ("date_start","date_end"),
  UNIQUE KEY "date_start" ("date_start"),
  UNIQUE KEY "date_end" ("date_end"),
  KEY "year_id" ("year_id"),
  CONSTRAINT "terms_ibfk_1" FOREIGN KEY ("year_id") REFERENCES "years" ("year_id") ON DELETE CASCADE
) AUTO_INCREMENT=12 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Term dates'*/;



#
# Dumping data for table 'terms'
#

LOCK TABLES "terms" WRITE;
/*!40000 ALTER TABLE "terms" DISABLE KEYS;*/
INSERT INTO "terms" ("term_id", "year_id", "date_start", "date_end", "name") VALUES
	('1','1','2008-09-08','2008-10-24','Autumn');
INSERT INTO "terms" ("term_id", "year_id", "date_start", "date_end", "name") VALUES
	('2','1','2009-01-05','2009-02-13','Spring A');
INSERT INTO "terms" ("term_id", "year_id", "date_start", "date_end", "name") VALUES
	('10','1','2009-04-09','2009-05-16','Foo');
/*!40000 ALTER TABLE "terms" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'users'
#

CREATE TABLE "users" (
  "user_id" int(10) unsigned NOT NULL auto_increment,
  "group_id" int(10) unsigned NOT NULL COMMENT 'Group that the user is a member of',
  "enabled" tinyint(1) NOT NULL default '0' COMMENT 'Boolean 1 or 0',
  "username" varchar(104) NOT NULL,
  "email" varchar(255) default NULL,
  "password" char(40) default NULL COMMENT 'SHA1 hash of password',
  "displayname" varchar(64) default NULL,
  "cookiekey" char(40) default NULL COMMENT 'SHA1 hash if a cookie is required',
  "lastlogin" timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT 'Date the user last logged in',
  "ldap" tinyint(1) unsigned NOT NULL default '0' COMMENT 'Boolean 1 or 0 if user should authenticate via LDAP',
  "created" date NOT NULL COMMENT 'Date the user was created',
  PRIMARY KEY  ("user_id"),
  UNIQUE KEY "username" ("username"),
  KEY "ldap" ("ldap"),
  KEY "group_id" ("group_id"),
  CONSTRAINT "users_ibfk_1" FOREIGN KEY ("group_id") REFERENCES "groups" ("group_id")
) AUTO_INCREMENT=23 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Main users table'*/;



#
# Dumping data for table 'users'
#

LOCK TABLES "users" WRITE;
/*!40000 ALTER TABLE "users" DISABLE KEYS;*/
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('1','1',1,'admin','craig.rodway@gmail.com','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Craig Rodway',NULL,'2009-01-24 16:43:04',0,'0000-00-00');
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('3','9',1,'user1','','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Foo Number 1',NULL,'2008-12-19 23:06:20',0,'2008-11-27');
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('12','2',1,'craig.rodway','craig.rodway@bishopbarrington.net',NULL,'Mr Rodway',NULL,'2009-01-09 16:12:48',1,'2009-01-09');
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('19','2',1,'test.one','test.one@bishopbarrington.net',NULL,'Mr T One',NULL,'2009-01-14 11:17:50',1,'2009-01-14');
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('22','2',1,'test.three','test.three@bishopbarrington.net',NULL,'Mr T Three',NULL,'2009-01-14 10:56:57',1,'2009-01-14');
/*!40000 ALTER TABLE "users" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'users2departments'
#

CREATE TABLE "users2departments" (
  "user_id" int(10) unsigned NOT NULL,
  "department_id" int(10) unsigned NOT NULL,
  KEY "department_id" ("department_id"),
  KEY "assignment" ("user_id","department_id"),
  KEY "user_id" ("user_id"),
  CONSTRAINT "users2departments_ibfk_3" FOREIGN KEY ("department_id") REFERENCES "departments" ("department_id") ON DELETE CASCADE,
  CONSTRAINT "users2departments_ibfk_2" FOREIGN KEY ("user_id") REFERENCES "users" ("user_id") ON DELETE CASCADE
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Maps a user to multiple departments'*/;



#
# Dumping data for table 'users2departments'
#

# (No data found.)



#
# Table structure for table 'usersactive'
#

CREATE TABLE "usersactive" (
  "user_id" int(10) unsigned NOT NULL,
  "timestamp" int(11) unsigned NOT NULL,
  UNIQUE KEY "user_id" ("user_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Keep track of current active users'*/;



#
# Dumping data for table 'usersactive'
#

LOCK TABLES "usersactive" WRITE;
/*!40000 ALTER TABLE "usersactive" DISABLE KEYS;*/
INSERT INTO "usersactive" ("user_id", "timestamp") VALUES
	('1','1232920907');
/*!40000 ALTER TABLE "usersactive" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'weekdates'
#

CREATE TABLE "weekdates" (
  "week_id" int(10) unsigned NOT NULL,
  "year_id" int(10) unsigned NOT NULL,
  "date" date NOT NULL,
  UNIQUE KEY "date" ("date"),
  KEY "week_id" ("week_id"),
  KEY "year_id" ("year_id"),
  CONSTRAINT "weekdates_ibfk_2" FOREIGN KEY ("year_id") REFERENCES "years" ("year_id") ON DELETE CASCADE,
  CONSTRAINT "weekdates_ibfk_1" FOREIGN KEY ("week_id") REFERENCES "weeks" ("week_id") ON DELETE CASCADE
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Start-dates of weeks for timetable'*/;



#
# Dumping data for table 'weekdates'
#

LOCK TABLES "weekdates" WRITE;
/*!40000 ALTER TABLE "weekdates" DISABLE KEYS;*/
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('11','1','2008-09-01');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2008-09-08');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('11','1','2008-09-15');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2008-09-22');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('11','1','2008-09-29');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2008-10-06');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('11','1','2008-10-13');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('11','1','2008-10-27');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2008-11-03');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2008-11-17');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2008-12-01');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2008-12-15');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2008-12-29');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2009-01-12');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2009-01-26');
INSERT INTO "weekdates" ("week_id", "year_id", "date") VALUES
	('12','1','2009-02-09');
/*!40000 ALTER TABLE "weekdates" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'weeks'
#

CREATE TABLE "weeks" (
  "week_id" int(10) unsigned NOT NULL auto_increment,
  "year_id" int(10) unsigned NOT NULL,
  "name" varchar(20) NOT NULL,
  "colour" char(7) default NULL COMMENT 'Hex colour value including hash',
  "created" date default NULL,
  PRIMARY KEY  ("week_id"),
  KEY "year_id" ("year_id"),
  CONSTRAINT "weeks_ibfk_1" FOREIGN KEY ("year_id") REFERENCES "years" ("year_id") ON DELETE CASCADE
) AUTO_INCREMENT=16 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Week definitions for timetable weeks'*/;



#
# Dumping data for table 'weeks'
#

LOCK TABLES "weeks" WRITE;
/*!40000 ALTER TABLE "weeks" DISABLE KEYS;*/
INSERT INTO "weeks" ("week_id", "year_id", "name", "colour", "created") VALUES
	('11','1','Red Week','#EF2929','2009-01-25');
INSERT INTO "weeks" ("week_id", "year_id", "name", "colour", "created") VALUES
	('12','1','Blue Week','#729FCF','2009-01-25');
/*!40000 ALTER TABLE "weeks" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'years'
#

CREATE TABLE "years" (
  "year_id" int(10) unsigned NOT NULL auto_increment,
  "date_start" date NOT NULL,
  "date_end" date NOT NULL,
  "name" varchar(20) NOT NULL,
  "active" tinyint(1) unsigned default '0',
  PRIMARY KEY  ("year_id"),
  UNIQUE KEY "active" ("active")
) AUTO_INCREMENT=7 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Academic year definitions'*/;



#
# Dumping data for table 'years'
#

LOCK TABLES "years" WRITE;
/*!40000 ALTER TABLE "years" DISABLE KEYS;*/
INSERT INTO "years" ("year_id", "date_start", "date_end", "name", "active") VALUES
	('1','2008-09-08','2009-07-23','2008 - 2009',1);
INSERT INTO "years" ("year_id", "date_start", "date_end", "name", "active") VALUES
	('5','2009-09-07','2010-07-23','2009 - 2010',NULL);
INSERT INTO "years" ("year_id", "date_start", "date_end", "name", "active") VALUES
	('6','2007-09-01','2008-07-24','2007 - 2008',NULL);
/*!40000 ALTER TABLE "years" ENABLE KEYS;*/
UNLOCK TABLES;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE;*/
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;*/
