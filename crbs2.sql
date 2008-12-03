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
# Table structure for table 'academicyears'
#

CREATE TABLE "academicyears" (
  "year_id" int(10) unsigned NOT NULL auto_increment,
  "date_start" date NOT NULL,
  "date_end" date NOT NULL,
  "name" varchar(20) NOT NULL,
  "current" tinyint(1) unsigned NOT NULL default '0' COMMENT 'Sets the current academic year',
  PRIMARY KEY  ("year_id"),
  UNIQUE KEY "year_id" ("year_id"),
  KEY "year_id_2" ("year_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Academic year definitions'*/;



#
# Dumping data for table 'academicyears'
#

# (No data found.)



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
) /*!40100 DEFAULT CHARSET=latin1*/;



#
# Dumping data for table 'ci_sessions'
#

LOCK TABLES "ci_sessions" WRITE;
/*!40000 ALTER TABLE "ci_sessions" DISABLE KEYS;*/
INSERT INTO "ci_sessions" ("session_id", "ip_address", "user_agent", "last_activity", "user_data") VALUES
	('4a3e3433bc5388701a8e2f11f880655f','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1228263124','a:4:{s:7:"user_id";s:1:"1";s:8:"group_id";s:1:"1";s:8:"username";s:5:"admin";s:7:"display";s:5:"admin";}');
/*!40000 ALTER TABLE "ci_sessions" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'departments'
#

CREATE TABLE "departments" (
  "department_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(20) NOT NULL,
  "description" varchar(255) default NULL,
  PRIMARY KEY  ("department_id"),
  UNIQUE KEY "department_id" ("department_id"),
  KEY "department_id_2" ("department_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='School departments'*/;



#
# Dumping data for table 'departments'
#

# (No data found.)



#
# Table structure for table 'groups'
#

CREATE TABLE "groups" (
  "group_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(20) NOT NULL,
  "description" varchar(255) default NULL,
  "bookahead" smallint(3) unsigned default NULL,
  "quota_num" int(10) unsigned default NULL,
  "quota_type" enum('day','week','month','current') default NULL,
  "permissions" text,
  "created" date NOT NULL,
  PRIMARY KEY  ("group_id"),
  UNIQUE KEY "group_id" ("group_id"),
  KEY "group_id_2" ("group_id")
) AUTO_INCREMENT=9 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Security groups'*/;



#
# Dumping data for table 'groups'
#

LOCK TABLES "groups" WRITE;
/*!40000 ALTER TABLE "groups" DISABLE KEYS;*/
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('0','Guests','Default group for guests',0,'1','current','a:1:{i:0;s:8:"bookings";}','0000-00-00');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('1','Administrators','Default group for administrator users',0,NULL,NULL,'a:54:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:9:"configure";i:5;s:8:"bookings";i:6;s:19:"bookings.create.one";i:7;s:21:"bookings.create.recur";i:8;s:23:"bookings.delete.one.own";i:9;s:29:"bookings.delete.one.roomowner";i:10;s:31:"bookings.delete.recur.roomowner";i:11;s:22:"bookings.overwrite.one";i:12;s:24:"bookings.overwrite.recur";i:13;s:32:"bookings.overwrite.one.roomowner";i:14;s:34:"bookings.overwrite.recur.roomowner";i:15;s:5:"rooms";i:16;s:9:"rooms.add";i:17;s:10:"rooms.edit";i:18;s:12:"rooms.delete";i:19;s:12:"rooms.fields";i:20;s:19:"rooms.fields.values";i:21;s:7:"periods";i:22;s:11:"periods.add";i:23;s:12:"periods.edit";i:24;s:14:"periods.delete";i:25;s:5:"weeks";i:26;s:9:"weeks.add";i:27;s:10:"weeks.edit";i:28;s:12:"weeks.delete";i:29;s:19:"weeks.ayears.manage";i:30;s:16:"weeks.ayears.set";i:31;s:8:"holidays";i:32;s:12:"holidays.add";i:33;s:13:"holidays.edit";i:34;s:15:"holidays.delete";i:35;s:11:"departments";i:36;s:15:"departments.add";i:37;s:16:"departments.edit";i:38;s:18:"departments.delete";i:39;s:7:"reports";i:40;s:21:"reports.owndepartment";i:41;s:22:"reports.alldepartments";i:42;s:15:"reports.ownroom";i:43;s:16:"reports.allrooms";i:44;s:13:"reports.other";i:45;s:5:"users";i:46;s:9:"users.add";i:47;s:10:"users.edit";i:48;s:12:"users.delete";i:49;s:12:"users.import";i:50;s:6:"groups";i:51;s:10:"groups.add";i:52;s:11:"groups.edit";i:53;s:13:"groups.delete";}','0000-00-00');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('2','Teaching Staff','Teachers from LDAP',14,'6','day','a:7:{i:0;s:9:"dashboard";i:1;s:18:"dashboard.viewdept";i:2;s:17:"dashboard.viewown";i:3;s:9:"myprofile";i:4;s:8:"bookings";i:5;s:19:"bookings.create.one";i:6;s:23:"bookings.delete.one.own";}','0000-00-00');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('4','Support staff','',14,'6','current',NULL,'2008-12-02');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('5','SSP','',28,NULL,NULL,NULL,'2008-12-02');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('6','Reporters','',0,'0','day','a:8:{i:0;s:9:"dashboard";i:1;s:9:"myprofile";i:2;s:7:"reports";i:3;s:21:"reports.owndepartment";i:4;s:22:"reports.alldepartments";i:5;s:15:"reports.ownroom";i:6;s:16:"reports.allrooms";i:7;s:13:"reports.other";}','2008-12-02');
INSERT INTO "groups" ("group_id", "name", "description", "bookahead", "quota_num", "quota_type", "permissions", "created") VALUES
	('7','test','',0,NULL,NULL,NULL,'2008-12-02');
/*!40000 ALTER TABLE "groups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'holidays'
#

CREATE TABLE "holidays" (
  "holiday_id" int(10) unsigned NOT NULL auto_increment,
  "ayear_id" int(10) unsigned NOT NULL,
  "date_start" date NOT NULL,
  "date_end" date NOT NULL,
  "name" varchar(50) default NULL,
  PRIMARY KEY  ("holiday_id"),
  UNIQUE KEY "year_id" ("holiday_id"),
  KEY "year_id_2" ("holiday_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='School holidays'*/;



#
# Dumping data for table 'holidays'
#

# (No data found.)



#
# Table structure for table 'periods'
#

CREATE TABLE "periods" (
  "period_id" int(10) unsigned NOT NULL auto_increment,
  "ayear_id" int(10) unsigned default NULL,
  "time_start" time NOT NULL,
  "time_end" time NOT NULL,
  "name" varchar(20) NOT NULL,
  "days" int(2) unsigned NOT NULL,
  "bookable" tinyint(1) NOT NULL,
  PRIMARY KEY  ("period_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Periods'*/;



#
# Dumping data for table 'periods'
#

# (No data found.)



#
# Table structure for table 'permissions'
#

CREATE TABLE "permissions" (
  "permission_id" int(10) unsigned NOT NULL auto_increment,
  "action" varchar(20) NOT NULL,
  "name" varchar(50) default NULL,
  "desc" text,
  PRIMARY KEY  ("permission_id"),
  UNIQUE KEY "permission_id" ("permission_id","action"),
  KEY "permission_id_2" ("permission_id","action")
) AUTO_INCREMENT=5 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Permission descriptions'*/;



#
# Dumping data for table 'permissions'
#

LOCK TABLES "permissions" WRITE;
/*!40000 ALTER TABLE "permissions" DISABLE KEYS;*/
INSERT INTO "permissions" ("permission_id", "action", "name", "desc") VALUES
	('1','bookings/view','View bookings',NULL);
INSERT INTO "permissions" ("permission_id", "action", "name", "desc") VALUES
	('2','bookings/make-one','Create booking',NULL);
INSERT INTO "permissions" ("permission_id", "action", "name", "desc") VALUES
	('3','bookings/make-recur','Create timetable booking',NULL);
INSERT INTO "permissions" ("permission_id", "action", "name", "desc") VALUES
	('4','welcome','Welcome page',NULL);
/*!40000 ALTER TABLE "permissions" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'permissions2groups'
#

CREATE TABLE "permissions2groups" (
  "p_id" int(10) unsigned default NULL,
  "g_id" int(10) unsigned default NULL,
  KEY "permission_id" ("p_id","g_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Link permissions with groups'*/;



#
# Dumping data for table 'permissions2groups'
#

LOCK TABLES "permissions2groups" WRITE;
/*!40000 ALTER TABLE "permissions2groups" DISABLE KEYS;*/
INSERT INTO "permissions2groups" ("p_id", "g_id") VALUES
	(NULL,NULL);
INSERT INTO "permissions2groups" ("p_id", "g_id") VALUES
	(NULL,NULL);
INSERT INTO "permissions2groups" ("p_id", "g_id") VALUES
	('1','3');
INSERT INTO "permissions2groups" ("p_id", "g_id") VALUES
	('1','3');
INSERT INTO "permissions2groups" ("p_id", "g_id") VALUES
	('2','1');
INSERT INTO "permissions2groups" ("p_id", "g_id") VALUES
	('2','1');
INSERT INTO "permissions2groups" ("p_id", "g_id") VALUES
	('2','3');
INSERT INTO "permissions2groups" ("p_id", "g_id") VALUES
	('2','3');
/*!40000 ALTER TABLE "permissions2groups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'permissions_copy'
#

CREATE TABLE "permissions_copy" (
  "permission_id" int(10) unsigned NOT NULL auto_increment,
  "action" varchar(20) NOT NULL,
  "menuname" varchar(20) default NULL,
  "url" varchar(50) default NULL,
  "order" int(2) unsigned default NULL,
  "admin-parent" int(10) unsigned default NULL,
  "admin-title" varchar(50) NOT NULL,
  "admin-desc" text,
  PRIMARY KEY  ("permission_id"),
  UNIQUE KEY "permission_id" ("permission_id","action"),
  KEY "permission_id_2" ("permission_id","action")
) AUTO_INCREMENT=17 /*!40100 DEFAULT CHARSET=latin1*/;



#
# Dumping data for table 'permissions_copy'
#

LOCK TABLES "permissions_copy" WRITE;
/*!40000 ALTER TABLE "permissions_copy" DISABLE KEYS;*/
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('1','BKG_VIEW','Bookings','bookings','0',NULL,'View bookings',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('2','BKG_MAKE',NULL,NULL,NULL,'1','Make a booking',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('3','BKG_MAKE_RECUR',NULL,NULL,NULL,'1','Make recurring bookings',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('4','BKG_MAKE_RECUR_OWNER',NULL,NULL,NULL,'1','Allow room owners to make recurring bookings',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('5','ACC','Account','account','1',NULL,'Account',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('6','CFG','Settings','settings','2',NULL,'Global settings',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('7','RMS','Rooms','rooms','3',NULL,'Rooms',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('8','PDS','Periods','periods','4',NULL,'Periods (school day)',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('9','WKS','Weeks','weeks','5',NULL,'Timetable weeks',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('10','WKS_ACYR',NULL,'weeks/academic-year',NULL,'8','Academic year',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('11','HOL','Holidays','holidays','6',NULL,'School holidays',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('12','DEP','Departments','departments','7',NULL,'Departments',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('13','REP','Reports','reports','8',NULL,'Reports',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('14','USR','Users','users','9',NULL,'User management',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('15','USR_GRPS',NULL,'users/groups',NULL,'13','Group management',NULL);
INSERT INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('16','WEL','','welcome',NULL,NULL,'Welcome page',NULL);
/*!40000 ALTER TABLE "permissions_copy" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'rooms-fields'
#

CREATE TABLE "rooms-fields" (
  "field_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(50) NOT NULL,
  "type" enum('TEXT','SELECT','CHECKBOX','MULTI') NOT NULL,
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
) /*!40100 DEFAULT CHARSET=latin1*/;



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
  "user_id" int(10) unsigned NOT NULL,
  "name" varchar(20) NOT NULL,
  "location" varchar(40) NOT NULL,
  "bookable" tinyint(1) NOT NULL,
  "icon" varchar(255) NOT NULL,
  "notes" varchar(255) NOT NULL,
  "photo" char(40) NOT NULL,
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
  "preauthkey" char(40) default NULL,
  "ldap" tinyint(1) unsigned NOT NULL default '0',
  "ldaphost" varchar(50) default NULL,
  "ldapport" int(5) unsigned default NULL,
  "ldapbase" text,
  "ldapfilter" text,
  "ldapgroup_id" int(10) unsigned default NULL
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='LDAP configuration'*/;



#
# Dumping data for table 'settings-auth'
#

LOCK TABLES "settings-auth" WRITE;
/*!40000 ALTER TABLE "settings-auth" DISABLE KEYS;*/
INSERT INTO "settings-auth" ("preauthkey", "ldap", "ldaphost", "ldapport", "ldapbase", "ldapfilter", "ldapgroup_id") VALUES
	('fa14b12a82d5722cc47520008371ae93da6ef4f7',1,'bbs-svr-001','389','ou=teaching staff, ou=bbs, ou=establishments, dc=bbarrington, dc=internal; ou=system administrators, ou=bbs, ou=establishments, dc=bbarrington, dc=internal','(& (| (!(displayname=Administrator*)) (!(displayname=Admin*)) ) (cn=%u) )','1');
/*!40000 ALTER TABLE "settings-auth" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'settings-main'
#

CREATE TABLE "settings-main" (
  "schoolname" varchar(100) default NULL,
  "schoolurl" varchar(255) default NULL,
  "bd_mode" enum('room','day') default NULL,
  "bd_col" enum('periods','rooms','days') default NULL
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Global app settings'*/;



#
# Dumping data for table 'settings-main'
#

LOCK TABLES "settings-main" WRITE;
/*!40000 ALTER TABLE "settings-main" DISABLE KEYS;*/
INSERT INTO "settings-main" ("schoolname", "schoolurl", "bd_mode", "bd_col") VALUES
	('Bishop Barrington School Sports With Mathematics College','','day','periods');
/*!40000 ALTER TABLE "settings-main" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'users'
#

CREATE TABLE "users" (
  "user_id" int(10) unsigned NOT NULL auto_increment,
  "group_id" int(10) NOT NULL,
  "department_id" int(10) unsigned default NULL,
  "enabled" tinyint(1) NOT NULL default '0',
  "username" varchar(104) NOT NULL,
  "firstname" varchar(20) default NULL,
  "lastname" varchar(20) default NULL,
  "email" varchar(256) default NULL,
  "password" char(40) NOT NULL,
  "displayname" varchar(64) default NULL,
  "ext" varchar(10) default NULL,
  "cookiekey" char(40) default NULL,
  "lastlogin" timestamp NOT NULL default '0000-00-00 00:00:00',
  "ldap" tinyint(1) unsigned NOT NULL default '0',
  "created" date NOT NULL,
  PRIMARY KEY  ("user_id"),
  KEY "ldap" ("ldap")
) AUTO_INCREMENT=9 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Main users table'*/;



#
# Dumping data for table 'users'
#

LOCK TABLES "users" WRITE;
/*!40000 ALTER TABLE "users" DISABLE KEYS;*/
INSERT INTO "users" ("user_id", "group_id", "department_id", "enabled", "username", "firstname", "lastname", "email", "password", "displayname", "ext", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('1',1,'0',1,'admin',NULL,NULL,'craig.rodway@gmail.com','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',NULL,NULL,NULL,'2008-12-02 23:57:38',0,'0000-00-00');
INSERT INTO "users" ("user_id", "group_id", "department_id", "enabled", "username", "firstname", "lastname", "email", "password", "displayname", "ext", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('2',1,'0',0,'craig.rodway',NULL,NULL,'craig.rodway@bishopbarrington.net','354c0efe3f189e6bb078399d9a75ee5cc402f8f8','Craig Rodway',NULL,NULL,'2008-11-27 17:32:10',0,'2008-11-23');
INSERT INTO "users" ("user_id", "group_id", "department_id", "enabled", "username", "firstname", "lastname", "email", "password", "displayname", "ext", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('3',0,'0',1,'user1',NULL,NULL,'','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Foo Number 1',NULL,NULL,'0000-00-00 00:00:00',0,'2008-11-27');
INSERT INTO "users" ("user_id", "group_id", "department_id", "enabled", "username", "firstname", "lastname", "email", "password", "displayname", "ext", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('4',1,'0',1,'adrian.staff',NULL,NULL,'','8843d7f92416211de9ebb963ff4ce28125932878','',NULL,NULL,'0000-00-00 00:00:00',0,'2008-11-27');
INSERT INTO "users" ("user_id", "group_id", "department_id", "enabled", "username", "firstname", "lastname", "email", "password", "displayname", "ext", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('8',5,'0',1,'test.one',NULL,NULL,'test.one@bishopbarrington.net','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8','Mr T One',NULL,NULL,'2008-12-02 23:54:07',0,'2008-12-02');
/*!40000 ALTER TABLE "users" ENABLE KEYS;*/
UNLOCK TABLES;


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

/*!40101 SET SQL_MODE=@OLD_SQL_MODE;*/
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;*/
