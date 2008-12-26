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
	('62bf49a6fcac75ddf549cb9046ea946f','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1230316732','a:6:{s:17:"group_permissions";a:59:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:9:"configure";i:5;s:8:"bookings";i:6;s:19:"bookings.create.one";i:7;s:21:"bookings.create.recur";i:8;s:23:"bookings.delete.one.own";i:9;s:29:"bookings.delete.one.roomowner";i:10;s:31:"bookings.delete.recur.roomowner";i:11;s:22:"bookings.overwrite.one";i:12;s:24:"bookings.overwrite.recur";i:13;s:32:"bookings.overwrite.one.roomowner";i:14;s:34:"bookings.overwrite.recur.roomowner";i:15;s:5:"rooms";i:16;s:9:"rooms.add";i:17;s:10:"rooms.edit";i:18;s:12:"rooms.delete";i:19;s:12:"rooms.fields";i:20;s:19:"rooms.fields.values";i:21;s:8:"academic";i:22;s:5:"years";i:23;s:9:"years.add";i:24;s:10:"years.edit";i:25;s:12:"years.delete";i:26;s:7:"periods";i:27;s:11:"periods.add";i:28;s:12:"periods.edit";i:29;s:14:"periods.delete";i:30;s:5:"weeks";i:31;s:9:"weeks.add";i:32;s:10:"weeks.edit";i:33;s:12:"weeks.delete";i:34;s:19:"weeks.ayears.manage";i:35;s:16:"weeks.ayears.set";i:36;s:8:"holidays";i:37;s:12:"holidays.add";i:38;s:13:"holidays.edit";i:39;s:15:"holidays.delete";i:40;s:11:"departments";i:41;s:15:"departments.add";i:42;s:16:"departments.edit";i:43;s:18:"departments.delete";i:44;s:7:"reports";i:45;s:21:"reports.owndepartment";i:46;s:22:"reports.alldepartments";i:47;s:15:"reports.ownroom";i:48;s:16:"reports.allrooms";i:49;s:13:"reports.other";i:50;s:5:"users";i:51;s:9:"users.add";i:52;s:10:"users.edit";i:53;s:12:"users.delete";i:54;s:12:"users.import";i:55;s:6:"groups";i:56;s:10:"groups.add";i:57;s:11:"groups.edit";i:58;s:13:"groups.delete";}s:3:"uri";s:14:"/academic/main";s:7:"user_id";s:1:"1";s:8:"group_id";s:1:"1";s:8:"username";s:5:"admin";s:7:"display";s:12:"Craig Rodway";}');
INSERT INTO "ci_sessions" ("session_id", "ip_address", "user_agent", "last_activity", "user_data") VALUES
	('b646d3547541d3583aa7c3bdfe59be88','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1230335845','a:2:{s:17:"group_permissions";s:0:"";s:3:"uri";s:15:"/academic/years";}');
INSERT INTO "ci_sessions" ("session_id", "ip_address", "user_agent", "last_activity", "user_data") VALUES
	('e19fc3c9c5331c26d5eb1e63d85d9f95','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1230309297','a:6:{s:17:"group_permissions";a:59:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:9:"configure";i:5;s:8:"bookings";i:6;s:19:"bookings.create.one";i:7;s:21:"bookings.create.recur";i:8;s:23:"bookings.delete.one.own";i:9;s:29:"bookings.delete.one.roomowner";i:10;s:31:"bookings.delete.recur.roomowner";i:11;s:22:"bookings.overwrite.one";i:12;s:24:"bookings.overwrite.recur";i:13;s:32:"bookings.overwrite.one.roomowner";i:14;s:34:"bookings.overwrite.recur.roomowner";i:15;s:5:"rooms";i:16;s:9:"rooms.add";i:17;s:10:"rooms.edit";i:18;s:12:"rooms.delete";i:19;s:12:"rooms.fields";i:20;s:19:"rooms.fields.values";i:21;s:8:"academic";i:22;s:5:"years";i:23;s:9:"years.add";i:24;s:10:"years.edit";i:25;s:12:"years.delete";i:26;s:7:"periods";i:27;s:11:"periods.add";i:28;s:12:"periods.edit";i:29;s:14:"periods.delete";i:30;s:5:"weeks";i:31;s:9:"weeks.add";i:32;s:10:"weeks.edit";i:33;s:12:"weeks.delete";i:34;s:19:"weeks.ayears.manage";i:35;s:16:"weeks.ayears.set";i:36;s:8:"holidays";i:37;s:12:"holidays.add";i:38;s:13:"holidays.edit";i:39;s:15:"holidays.delete";i:40;s:11:"departments";i:41;s:15:"departments.add";i:42;s:16:"departments.edit";i:43;s:18:"departments.delete";i:44;s:7:"reports";i:45;s:21:"reports.owndepartment";i:46;s:22:"reports.alldepartments";i:47;s:15:"reports.ownroom";i:48;s:16:"reports.allrooms";i:49;s:13:"reports.other";i:50;s:5:"users";i:51;s:9:"users.add";i:52;s:10:"users.edit";i:53;s:12:"users.delete";i:54;s:12:"users.import";i:55;s:6:"groups";i:56;s:10:"groups.add";i:57;s:11:"groups.edit";i:58;s:13:"groups.delete";}s:3:"uri";s:22:"/academic/years/edit/2";s:7:"user_id";s:1:"1";s:8:"group_id";s:1:"1";s:8:"username";s:5:"admin";s:7:"display";s:12:"Craig Rodway";}');
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
) AUTO_INCREMENT=11 /*!40100 DEFAULT CHARSET=latin1 COMMENT='School departments'*/;



#
# Dumping data for table 'departments'
#

LOCK TABLES "departments" WRITE;
/*!40000 ALTER TABLE "departments" DISABLE KEYS;*/
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('1','English','','#4E9A06','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('2','Maths','','#EDD400','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('3','Science','','#3465A4','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('4','ICT','','#BABDB6','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('5','Music','','#F57900','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('6','History','','#8F5902','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('7','Art','','#A40000','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('8','Admin','','#2E3436','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('9','RE','','#EF2929','2008-12-19');
INSERT INTO "departments" ("department_id", "name", "description", "colour", "created") VALUES
	('10','Geography','','#AD7FA8','2008-12-19');
/*!40000 ALTER TABLE "departments" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'departments2ldapgroups'
#

CREATE TABLE "departments2ldapgroups" (
  "department_id" int(10) unsigned NOT NULL,
  "ldapgroup_id" int(10) unsigned NOT NULL
) /*!40100 DEFAULT CHARSET=latin1*/;



#
# Dumping data for table 'departments2ldapgroups'
#

LOCK TABLES "departments2ldapgroups" WRITE;
/*!40000 ALTER TABLE "departments2ldapgroups" DISABLE KEYS;*/
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('8','887');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('8','860');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('7','832');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('1','839');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('6','844');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('4','831');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('2','858');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('2','836');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('5','840');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('9','868');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('9','845');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('3','846');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('3','829');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('10','861');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('10','872');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('6','844');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('7','832');
INSERT INTO "departments2ldapgroups" ("department_id", "ldapgroup_id") VALUES
	('7','832');
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
) AUTO_INCREMENT=8 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Groups table with settings and permiss; InnoDB free: 9216 kB'*/;



#
# Dumping data for table 'groups'
#

LOCK TABLES "groups" WRITE;
/*!40000 ALTER TABLE "groups" DISABLE KEYS;*/
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('1','Administrators','Default group for administrator users',0,NULL,NULL,'a:59:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:9:"configure";i:5;s:8:"bookings";i:6;s:19:"bookings.create.one";i:7;s:21:"bookings.create.recur";i:8;s:23:"bookings.delete.one.own";i:9;s:29:"bookings.delete.one.roomowner";i:10;s:31:"bookings.delete.recur.roomowner";i:11;s:22:"bookings.overwrite.one";i:12;s:24:"bookings.overwrite.recur";i:13;s:32:"bookings.overwrite.one.roomowner";i:14;s:34:"bookings.overwrite.recur.roomowner";i:15;s:5:"rooms";i:16;s:9:"rooms.add";i:17;s:10:"rooms.edit";i:18;s:12:"rooms.delete";i:19;s:12:"rooms.fields";i:20;s:19:"rooms.fields.values";i:21;s:8:"academic";i:22;s:5:"years";i:23;s:9:"years.add";i:24;s:10:"years.edit";i:25;s:12:"years.delete";i:26;s:7:"periods";i:27;s:11:"periods.add";i:28;s:12:"periods.edit";i:29;s:14:"periods.delete";i:30;s:5:"weeks";i:31;s:9:"weeks.add";i:32;s:10:"weeks.edit";i:33;s:12:"weeks.delete";i:34;s:19:"weeks.ayears.manage";i:35;s:16:"weeks.ayears.set";i:36;s:8:"holidays";i:37;s:12:"holidays.add";i:38;s:13:"holidays.edit";i:39;s:15:"holidays.delete";i:40;s:11:"departments";i:41;s:15:"departments.add";i:42;s:16:"departments.edit";i:43;s:18:"departments.delete";i:44;s:7:"reports";i:45;s:21:"reports.owndepartment";i:46;s:22:"reports.alldepartments";i:47;s:15:"reports.ownroom";i:48;s:16:"reports.allrooms";i:49;s:13:"reports.other";i:50;s:5:"users";i:51;s:9:"users.add";i:52;s:10:"users.edit";i:53;s:12:"users.delete";i:54;s:12:"users.import";i:55;s:6:"groups";i:56;s:10:"groups.add";i:57;s:11:"groups.edit";i:58;s:13:"groups.delete";}','0000-00-00');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('2','Teaching Staff','Teachers from LDAP',14,'6','day','a:7:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:8:"bookings";i:5;s:19:"bookings.create.one";i:6;s:23:"bookings.delete.one.own";}','0000-00-00');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('4','Support staff','',14,'6','current','a:7:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:8:"bookings";i:5;s:19:"bookings.create.one";i:6;s:23:"bookings.delete.one.own";}','2008-12-02');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('7','Guests','Default group for guests',0,'1','current','a:1:{i:0;s:8:"bookings";}','0000-00-00');
/*!40000 ALTER TABLE "groups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'groups2ldapgroups'
#

CREATE TABLE "groups2ldapgroups" (
  "group_id" int(10) unsigned NOT NULL,
  "ldapgroup_id" int(10) unsigned NOT NULL
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Maps 1+ LDAp groups to 1 CRBS group'*/;



#
# Dumping data for table 'groups2ldapgroups'
#

LOCK TABLES "groups2ldapgroups" WRITE;
/*!40000 ALTER TABLE "groups2ldapgroups" DISABLE KEYS;*/
INSERT INTO "groups2ldapgroups" ("group_id", "ldapgroup_id") VALUES
	('2','802');
INSERT INTO "groups2ldapgroups" ("group_id", "ldapgroup_id") VALUES
	('4','800');
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
  PRIMARY KEY  ("holiday_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='School holidays'*/;



#
# Dumping data for table 'holidays'
#

# (No data found.)



#
# Table structure for table 'ldapgroups'
#

CREATE TABLE "ldapgroups" (
  "ldapgroup_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(104) NOT NULL COMMENT 'Name of LDAP group (not full DN, just name part)',
  PRIMARY KEY  ("ldapgroup_id"),
  UNIQUE KEY "ldapgroup_id" ("ldapgroup_id","name")
) AUTO_INCREMENT=904 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Group names retrieved from LDAP; InnoDB free: 9216 kB'*/;



#
# Dumping data for table 'ldapgroups'
#

LOCK TABLES "ldapgroups" WRITE;
/*!40000 ALTER TABLE "ldapgroups" DISABLE KEYS;*/
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('798','BBS Print Operators');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('799','BBS Staff Print Operators');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('800','BBS Non-Teach Staff');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('801','BBS Students');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('802','BBS Teaching Staff');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('803','BBS Accessibility');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('804','BBS Internet Disabled');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('805','BBS Guest {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('806','BBS RM Explorer {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('807','BBS Restricted {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('808','BBS Standard {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('809','BBS Advanced {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('810','BBS Staff {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('811','BBS Advanced Staff {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('812','BBS Advanced {SS}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('813','BBS Standard {SS}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('814','BBS No {SS}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('815','BBS Shared LT {ST}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('816','BBS Managed Stations');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('817','BBS Authorised {MT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('818','BBS Delegate {MT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('819','BBS Shared {ST}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('820','BBS Personal {ST}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('821','BBS Cyber {ST}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('822','BBS EasyLink');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('823','BBS Education Mgmt {UR}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('824','BBS Legacy Apps {UR}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('825','BBS Management Information System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('826','BBS Technology {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('827','BBS Local Administrators');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('828','BBS Station Setup');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('829','BBS Science {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('830','BBS Leisure ~1 {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('831','BBS ICT {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('832','BBS Art {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('833','BBS CD Burning');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('834','BBS Textiles {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('835','BBS PowerDVD');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('836','BBS Maths {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('837','BBS Food Tec~1 {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('838','BBS MFL {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('839','BBS English {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('840','BBS Music {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('841','BBS Physical~1 {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('842','BBS Performi~1 {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('843','BBS Media St~1 {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('844','BBS History {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('845','BBS Religiou~1 {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('846','BBS Science');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('847','BBS RMMC {AR}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('848','BBS User Controller {MT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('849','BBS EDI System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('850','BBS Finance System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('851','BBS Library System');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('852','BBS MIS Manager');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('853','BBS Network {MT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('854','BBS Sleuth Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('855','BBS Staff Absences');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('856','BBS School Income');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('857','BBS RMSecurenet');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('858','BBS Maths');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('859','BBS Science Exam');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('860','BBS Admin Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('861','BBS Geography {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('862','BBS No GPO Security');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('863','BBS Associates');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('864','BBS Science year 11');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('865','BBS Science year 10');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('866','BBS Science Review');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('867','BBS Careers Teacher');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('868','BBS RE Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('869','BBS Detention DB U~1');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('870','BBS Eregistration');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('871','BBS Interactive Wh~1');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('872','BBS Humanities');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('873','BBS Science year 9');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('874','BBS Legal Team');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('875','BBS Leisure and To~1');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('876','BBS QuarkXPress Us~1');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('877','BBS Quizdom');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('878','BBS BKSB');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('879','BBS Staff DAP {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('880','BBS Design Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('881','BBS PE Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('882','BBS Exam Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('883','Terminal Services Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('884','BBS Shared De~1 {ST}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('885','BBS SecureNet');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('886','BBS Exam Officer');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('887','BBS Admin Staff {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('888','BBS AnyComms Users');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('889','BBS Careers {tch}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('890','BBS BKSB Manager');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('891','BBS Copy of A~1 {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('892','BBS SEN Teachers');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('893','BBS SEN Students');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('894','BBS Childcare');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('895','BBS Truancy Call');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('896','BBS SSP');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('897','BBS Email disabled');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('898','BBS School Fund Of~1');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('899','BBS IT author~1 {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('900','BBS Copy of R~1 {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('901','BBS Encrypted Folder');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('902','BBS Guest 2 {UT}');
INSERT INTO "ldapgroups" ("ldapgroup_id", "name") VALUES
	('903','BBS HSS Finance');
/*!40000 ALTER TABLE "ldapgroups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'periods'
#

CREATE TABLE "periods" (
  "period_id" int(10) unsigned NOT NULL auto_increment,
  "ayear_id" int(10) unsigned default NULL COMMENT 'The academic year that period belongs to',
  "time_start" time NOT NULL,
  "time_end" time NOT NULL,
  "name" varchar(20) NOT NULL,
  "days" varchar(255) NOT NULL COMMENT 'Serialize() of the days that this period is set on',
  "bookable" tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0 if periods can be booked or not',
  PRIMARY KEY  ("period_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Periods'*/;



#
# Dumping data for table 'periods'
#

# (No data found.)



#
# Table structure for table 'quota'
#

CREATE TABLE "quota" (
  "user_id" int(10) unsigned NOT NULL,
  "quota_num" int(10) unsigned NOT NULL,
  UNIQUE KEY "user_id" ("user_id")
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
  "ldap" tinyint(1) unsigned NOT NULL default '0' COMMENT 'Boolean 1 or 0 for LDAP auth status',
  "ldaphost" varchar(50) default NULL COMMENT 'LDAP server hostname',
  "ldapport" int(5) unsigned default NULL COMMENT 'LDAP server TCP port',
  "ldapbase" text COMMENT 'Base DNs to search in LDAP',
  "ldapfilter" text COMMENT 'LDAP search query filter',
  "ldapgroup_id" int(10) unsigned default NULL
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='LDAP configuration'*/;



#
# Dumping data for table 'settings-auth'
#

LOCK TABLES "settings-auth" WRITE;
/*!40000 ALTER TABLE "settings-auth" DISABLE KEYS;*/
INSERT INTO "settings-auth" ("preauthkey", "ldap", "ldaphost", "ldapport", "ldapbase", "ldapfilter", "ldapgroup_id") VALUES
	('d1ae873270604d2d2ea3221a4e632b96b1d3a914',1,'bbs-svr-001','389','ou=teaching staff, ou=bbs, ou=establishments, dc=bbarrington, dc=internal; ou=system administrators, ou=bbs, ou=establishments, dc=bbarrington, dc=internal','(& (| (!(displayname=Administrator*)) (!(displayname=Admin*)) ) (cn=%u) )','1');
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
# Table structure for table 'users'
#

CREATE TABLE "users" (
  "user_id" int(10) unsigned NOT NULL auto_increment,
  "group_id" int(10) NOT NULL COMMENT 'Group that the user is a member of',
  "enabled" tinyint(1) NOT NULL default '0' COMMENT 'Boolean 1 or 0',
  "username" varchar(104) NOT NULL,
  "email" varchar(255) default NULL,
  "password" char(40) NOT NULL COMMENT 'SHA1 hash of password',
  "displayname" varchar(64) default NULL,
  "cookiekey" char(40) default NULL COMMENT 'SHA1 hash if a cookie is required',
  "lastlogin" timestamp NOT NULL default '0000-00-00 00:00:00' COMMENT 'Date the user last logged in',
  "ldap" tinyint(1) unsigned NOT NULL default '0' COMMENT 'Boolean 1 or 0 if user should authenticate via LDAP',
  "created" date NOT NULL COMMENT 'Date the user was created',
  PRIMARY KEY  ("user_id"),
  KEY "ldap" ("ldap")
) AUTO_INCREMENT=9 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Main users table'*/;



#
# Dumping data for table 'users'
#

LOCK TABLES "users" WRITE;
/*!40000 ALTER TABLE "users" DISABLE KEYS;*/
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('1',1,1,'admin','craig.rodway@gmail.com','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Craig Rodway',NULL,'2008-12-26 18:39:22',0,'0000-00-00');
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('2',1,0,'craig.rodway','craig.rodway@bishopbarrington.net','354c0efe3f189e6bb078399d9a75ee5cc402f8f8','Craig Rodway',NULL,'2008-11-27 17:32:10',0,'2008-11-23');
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('3',4,1,'user1','','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Foo Number 1',NULL,'2008-12-19 23:06:20',0,'2008-11-27');
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('4',1,1,'adrian.staff','','8843d7f92416211de9ebb963ff4ce28125932878','',NULL,'0000-00-00 00:00:00',0,'2008-11-27');
INSERT INTO "users" ("user_id", "group_id", "enabled", "username", "email", "password", "displayname", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('8',2,1,'test.one','test.one@bishopbarrington.net','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Mr T One',NULL,'2008-12-25 23:23:26',0,'2008-12-02');
/*!40000 ALTER TABLE "users" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'users2departments'
#

CREATE TABLE "users2departments" (
  "user_id" int(10) unsigned NOT NULL,
  "department_id" int(10) unsigned NOT NULL,
  KEY "user_id" ("user_id","department_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Maps a user to multiple departments'*/;



#
# Dumping data for table 'users2departments'
#

# (No data found.)



#
# Table structure for table 'weekdates'
#

CREATE TABLE "weekdates" (
  "week_id" int(10) unsigned NOT NULL,
  "date" date NOT NULL,
  KEY "week_id" ("week_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Start-dates of weeks for timetable'*/;



#
# Dumping data for table 'weekdates'
#

# (No data found.)



#
# Table structure for table 'weeks'
#

CREATE TABLE "weeks" (
  "week_id" int(10) unsigned NOT NULL auto_increment,
  "ayear_id" int(10) unsigned default NULL,
  "name" varchar(20) NOT NULL,
  "fgcol" char(6) NOT NULL,
  "bgcol" char(6) NOT NULL,
  PRIMARY KEY  ("week_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Week definitions for timetable weeks'*/;



#
# Dumping data for table 'weeks'
#

# (No data found.)



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
) AUTO_INCREMENT=6 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Academic year definitions'*/;



#
# Dumping data for table 'years'
#

LOCK TABLES "years" WRITE;
/*!40000 ALTER TABLE "years" DISABLE KEYS;*/
INSERT INTO "years" ("year_id", "date_start", "date_end", "name", "active") VALUES
	('1','2008-09-08','2009-07-23','2008 - 2009',1);
INSERT INTO "years" ("year_id", "date_start", "date_end", "name", "active") VALUES
	('2','2007-09-03','2008-07-23','2007 - 2008',NULL);
INSERT INTO "years" ("year_id", "date_start", "date_end", "name", "active") VALUES
	('5','2009-09-07','2010-07-23','2009 - 2010',NULL);
/*!40000 ALTER TABLE "years" ENABLE KEYS;*/
UNLOCK TABLES;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE;*/
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;*/
