# HeidiSQL Dump 
#
# --------------------------------------------------------
# Host:                 127.0.0.1
# Database:             crbs2
# Server version:       5.0.51a-community-nt
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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ "crbs2" /*!40100 DEFAULT CHARACTER SET latin1 */;

USE "crbs2";


#
# Table structure for table 'academicyears'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "academicyears" (
  "year_id" int(10) unsigned NOT NULL auto_increment,
  "date_start" date NOT NULL,
  "date_end" date NOT NULL,
  "name" varchar(50) default NULL,
  PRIMARY KEY  ("year_id"),
  UNIQUE KEY "year_id" ("year_id"),
  KEY "year_id_2" ("year_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Academic year definitions'*/;



#
# Dumping data for table 'academicyears'
#

TRUNCATE TABLE "academicyears";
# (No data found.)



#
# Table structure for table 'departments'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "departments" (
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

TRUNCATE TABLE "departments";
# (No data found.)



#
# Table structure for table 'groups'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "groups" (
  "group_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(20) NOT NULL,
  "description" varchar(255) default NULL,
  PRIMARY KEY  ("group_id"),
  UNIQUE KEY "group_id" ("group_id"),
  KEY "group_id_2" ("group_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Security groups'*/;



#
# Dumping data for table 'groups'
#

TRUNCATE TABLE "groups";
LOCK TABLES "groups" WRITE;
/*!40000 ALTER TABLE "groups" DISABLE KEYS;*/
INSERT INTO "groups" ("group_id", "name", "description") VALUES
	('0','Anonymous','No login is required');
/*!40000 ALTER TABLE "groups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'holidays'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "holidays" (
  "holiday_id" int(10) unsigned NOT NULL auto_increment,
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

TRUNCATE TABLE "holidays";
# (No data found.)



#
# Table structure for table 'periods'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "periods" (
  "period_id" int(10) unsigned NOT NULL auto_increment,
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

TRUNCATE TABLE "periods";
# (No data found.)



#
# Table structure for table 'permissions'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "permissions" (
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
) AUTO_INCREMENT=17 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Permission descriptions'*/;



#
# Dumping data for table 'permissions'
#

TRUNCATE TABLE "permissions";
LOCK TABLES "permissions" WRITE;
/*!40000 ALTER TABLE "permissions" DISABLE KEYS;*/
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('1','BKG_VIEW','Bookings','bookings','0',NULL,'View bookings',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('2','BKG_MAKE',NULL,NULL,NULL,'1','Make a booking',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('3','BKG_MAKE_RECUR',NULL,NULL,NULL,'1','Make recurring bookings',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('4','BKG_MAKE_RECUR_OWNER',NULL,NULL,NULL,'1','Allow room owners to make recurring bookings',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('5','ACC','Account','account','1',NULL,'Account',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('6','CFG','Settings','settings','2',NULL,'Global settings',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('7','RMS','Rooms','rooms','3',NULL,'Rooms',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('8','PDS','Periods','periods','4',NULL,'Periods (school day)',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('9','WKS','Weeks','weeks','5',NULL,'Timetable weeks',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('10','WKS_ACYR',NULL,'weeks/academic-year',NULL,'8','Academic year',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('11','HOL','Holidays','holidays','6',NULL,'School holidays',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('12','DEP','Departments','departments','7',NULL,'Departments',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('13','REP','Reports','reports','8',NULL,'Reports',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('14','USR','Users','users','9',NULL,'User management',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('15','USR_GRPS',NULL,'users/groups',NULL,'13','Group management',NULL);
INSERT INTO "permissions" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('16','WEL','','welcome',NULL,NULL,'Welcome page',NULL);
/*!40000 ALTER TABLE "permissions" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'permissions2groups'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "permissions2groups" (
  "permission_id" int(10) unsigned default NULL,
  "group_id" int(10) unsigned default NULL,
  KEY "permission_id" ("permission_id","group_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Link permissions with groups'*/;



#
# Dumping data for table 'permissions2groups'
#

TRUNCATE TABLE "permissions2groups";
# (No data found.)



#
# Table structure for table 'rooms-fields'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "rooms-fields" (
  "field_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(50) NOT NULL,
  "type" enum('TEXT','SELECT','CHECKBOX','MULTI') NOT NULL,
  PRIMARY KEY  ("field_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Names of fields that can be assigned to rooms'*/;



#
# Dumping data for table 'rooms-fields'
#

TRUNCATE TABLE "rooms-fields";
# (No data found.)



#
# Table structure for table 'rooms-options'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "rooms-options" (
  "option_id" int(10) unsigned NOT NULL auto_increment,
  "value" varchar(50) NOT NULL,
  PRIMARY KEY  ("option_id")
) /*!40100 DEFAULT CHARSET=latin1*/;



#
# Dumping data for table 'rooms-options'
#

TRUNCATE TABLE "rooms-options";
# (No data found.)



#
# Table structure for table 'rooms-values'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "rooms-values" (
  "value_id" int(10) unsigned NOT NULL auto_increment,
  "room_id" int(10) unsigned NOT NULL,
  "field_id" int(10) unsigned NOT NULL,
  "value" varchar(255) NOT NULL,
  PRIMARY KEY  ("value_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Actual values of room fields for each room'*/;



#
# Dumping data for table 'rooms-values'
#

TRUNCATE TABLE "rooms-values";
# (No data found.)



#
# Table structure for table 'rooms'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "rooms" (
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

TRUNCATE TABLE "rooms";
# (No data found.)



#
# Table structure for table 'settings-crbs'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "settings-crbs" (
  "schoolname" varchar(100) default NULL,
  "schoolurl" varchar(255) default NULL,
  "daysahead" int(3) default NULL,
  "bk-columns" enum('periods','rooms','days') default NULL,
  "bk-viewtype" enum('room','day') default NULL,
  "preauthkey" varchar(255) default NULL
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Global app settings'*/;



#
# Dumping data for table 'settings-crbs'
#

TRUNCATE TABLE "settings-crbs";
# (No data found.)



#
# Table structure for table 'settings-ldap'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "settings-ldap" (
  "serverhost" varchar(20) default NULL,
  "serverport" int(5) unsigned default NULL,
  "base" varchar(255) default NULL,
  "binduser" varchar(50) default NULL,
  "bindpass" varchar(50) default NULL
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='LDAP configuration'*/;



#
# Dumping data for table 'settings-ldap'
#

TRUNCATE TABLE "settings-ldap";
# (No data found.)



#
# Table structure for table 'users'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "users" (
  "user_id" int(10) unsigned NOT NULL auto_increment,
  "group_id" int(10) NOT NULL,
  "department_id" int(10) unsigned default NULL,
  "enabled" tinyint(1) NOT NULL default '0',
  "username" varchar(20) NOT NULL,
  "firstname" varchar(20) default NULL,
  "lastname" varchar(20) default NULL,
  "email" varchar(255) default NULL,
  "password" char(40) NOT NULL,
  "password-expire" datetime default NULL,
  "displayname" varchar(20) default NULL,
  "ext" varchar(10) default NULL,
  "cookiekey" char(40) default NULL,
  "lastlogin" timestamp NOT NULL default '0000-00-00 00:00:00',
  "ldap" tinyint(1) unsigned NOT NULL default '0',
  "created" date NOT NULL,
  PRIMARY KEY  ("user_id"),
  KEY "ldap" ("ldap")
) AUTO_INCREMENT=2 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Main users table'*/;



#
# Dumping data for table 'users'
#

TRUNCATE TABLE "users";
LOCK TABLES "users" WRITE;
/*!40000 ALTER TABLE "users" DISABLE KEYS;*/
INSERT INTO "users" ("user_id", "group_id", "department_id", "enabled", "username", "firstname", "lastname", "email", "password", "password-expire", "displayname", "ext", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('1',1,'0',1,'admin',NULL,NULL,'craig.rodway@gmail.com','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',NULL,NULL,NULL,NULL,'2008-02-02 13:09:55',0,'0000-00-00');
/*!40000 ALTER TABLE "users" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'weekdates'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "weekdates" (
  "week_id" int(10) unsigned NOT NULL,
  "date" date NOT NULL,
  KEY "week_id" ("week_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Start-dates of weeks for timetable'*/;



#
# Dumping data for table 'weekdates'
#

TRUNCATE TABLE "weekdates";
# (No data found.)



#
# Table structure for table 'weeks'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "weeks" (
  "week_id" int(10) unsigned NOT NULL auto_increment,
  "name" varchar(20) NOT NULL,
  "fgcol" char(6) NOT NULL,
  "bgcol" char(6) NOT NULL,
  "icon" varchar(255) default NULL,
  PRIMARY KEY  ("week_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Week definitions for timetable weeks'*/;



#
# Dumping data for table 'weeks'
#

TRUNCATE TABLE "weeks";
# (No data found.)

/*!40101 SET SQL_MODE=@OLD_SQL_MODE;*/
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;*/
