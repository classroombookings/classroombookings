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

CREATE DATABASE /*!32312 IF NOT EXISTS*/ "crbs2" /*!40100 DEFAULT CHARACTER SET latin1 */;

USE "crbs2";


#
# Table structure for table 'academicyears'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "academicyears" (
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

CREATE TABLE /*!32312 IF NOT EXISTS*/ "ci_sessions" (
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
REPLACE INTO "ci_sessions" ("session_id", "ip_address", "user_agent", "last_activity", "user_data") VALUES
	('0e84098adf3784a8a322a2f3e6198dc7','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1227657060','');
REPLACE INTO "ci_sessions" ("session_id", "ip_address", "user_agent", "last_activity", "user_data") VALUES
	('67e83bed5fa7730b7439b792ef8cb757','127.0.0.1','Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv','1227649533','');
/*!40000 ALTER TABLE "ci_sessions" ENABLE KEYS;*/
UNLOCK TABLES;


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
) AUTO_INCREMENT=4 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Security groups'*/;



#
# Dumping data for table 'groups'
#

LOCK TABLES "groups" WRITE;
/*!40000 ALTER TABLE "groups" DISABLE KEYS;*/
REPLACE INTO "groups" ("group_id", "name", "description") VALUES
	('1','Administrators','Default group for administrator users');
REPLACE INTO "groups" ("group_id", "name", "description") VALUES
	('2','Foo','Teachers from LDAP');
REPLACE INTO "groups" ("group_id", "name", "description") VALUES
	('3','Guests','Default group for guests');
/*!40000 ALTER TABLE "groups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'holidays'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "holidays" (
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

CREATE TABLE /*!32312 IF NOT EXISTS*/ "periods" (
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

CREATE TABLE /*!32312 IF NOT EXISTS*/ "permissions" (
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
REPLACE INTO "permissions" ("permission_id", "action", "name", "desc") VALUES
	('1','bookings/view','View bookings',NULL);
REPLACE INTO "permissions" ("permission_id", "action", "name", "desc") VALUES
	('2','bookings/make-one','Create booking',NULL);
REPLACE INTO "permissions" ("permission_id", "action", "name", "desc") VALUES
	('3','bookings/make-recur','Create timetable booking',NULL);
REPLACE INTO "permissions" ("permission_id", "action", "name", "desc") VALUES
	('4','welcome','Welcome page',NULL);
/*!40000 ALTER TABLE "permissions" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'permissions2groups'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "permissions2groups" (
  "p_id" int(10) unsigned default NULL,
  "g_id" int(10) unsigned default NULL,
  KEY "permission_id" ("p_id","g_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Link permissions with groups'*/;



#
# Dumping data for table 'permissions2groups'
#

LOCK TABLES "permissions2groups" WRITE;
/*!40000 ALTER TABLE "permissions2groups" DISABLE KEYS;*/
REPLACE INTO "permissions2groups" ("p_id", "g_id") VALUES
	(NULL,NULL);
REPLACE INTO "permissions2groups" ("p_id", "g_id") VALUES
	(NULL,NULL);
REPLACE INTO "permissions2groups" ("p_id", "g_id") VALUES
	('1','3');
REPLACE INTO "permissions2groups" ("p_id", "g_id") VALUES
	('1','3');
REPLACE INTO "permissions2groups" ("p_id", "g_id") VALUES
	('2','1');
REPLACE INTO "permissions2groups" ("p_id", "g_id") VALUES
	('2','1');
REPLACE INTO "permissions2groups" ("p_id", "g_id") VALUES
	('2','3');
REPLACE INTO "permissions2groups" ("p_id", "g_id") VALUES
	('2','3');
/*!40000 ALTER TABLE "permissions2groups" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'permissions_copy'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "permissions_copy" (
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
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('1','BKG_VIEW','Bookings','bookings','0',NULL,'View bookings',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('2','BKG_MAKE',NULL,NULL,NULL,'1','Make a booking',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('3','BKG_MAKE_RECUR',NULL,NULL,NULL,'1','Make recurring bookings',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('4','BKG_MAKE_RECUR_OWNER',NULL,NULL,NULL,'1','Allow room owners to make recurring bookings',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('5','ACC','Account','account','1',NULL,'Account',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('6','CFG','Settings','settings','2',NULL,'Global settings',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('7','RMS','Rooms','rooms','3',NULL,'Rooms',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('8','PDS','Periods','periods','4',NULL,'Periods (school day)',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('9','WKS','Weeks','weeks','5',NULL,'Timetable weeks',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('10','WKS_ACYR',NULL,'weeks/academic-year',NULL,'8','Academic year',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('11','HOL','Holidays','holidays','6',NULL,'School holidays',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('12','DEP','Departments','departments','7',NULL,'Departments',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('13','REP','Reports','reports','8',NULL,'Reports',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('14','USR','Users','users','9',NULL,'User management',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('15','USR_GRPS',NULL,'users/groups',NULL,'13','Group management',NULL);
REPLACE INTO "permissions_copy" ("permission_id", "action", "menuname", "url", "order", "admin-parent", "admin-title", "admin-desc") VALUES
	('16','WEL','','welcome',NULL,NULL,'Welcome page',NULL);
/*!40000 ALTER TABLE "permissions_copy" ENABLE KEYS;*/
UNLOCK TABLES;


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

# (No data found.)



#
# Table structure for table 'settings-auth'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "settings-auth" (
  "ldap" tinyint(1) unsigned NOT NULL default '0',
  "ldaphost" varchar(50) default NULL,
  "ldapport" int(5) unsigned default NULL,
  "ldapbase" text,
  "preauthkey" char(40) default NULL,
  "ldapgroup_id" int(10) unsigned default NULL
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='LDAP configuration'*/;



#
# Dumping data for table 'settings-auth'
#

LOCK TABLES "settings-auth" WRITE;
/*!40000 ALTER TABLE "settings-auth" DISABLE KEYS;*/
REPLACE INTO "settings-auth" ("ldap", "ldaphost", "ldapport", "ldapbase", "preauthkey", "ldapgroup_id") VALUES
	(0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE "settings-auth" ENABLE KEYS;*/
UNLOCK TABLES;


#
# Table structure for table 'settings-ldap-rdns'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "settings-ldap-rdns" (
  "rdn_id" int(10) unsigned NOT NULL auto_increment,
  "string" varchar(255) NOT NULL,
  PRIMARY KEY  ("rdn_id"),
  UNIQUE KEY "dn_id" ("rdn_id"),
  KEY "dn_id_2" ("rdn_id")
) /*!40100 DEFAULT CHARSET=latin1 COMMENT='Settings for LDAP bind DNs'*/;



#
# Dumping data for table 'settings-ldap-rdns'
#

# (No data found.)



#
# Table structure for table 'settings-main'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "settings-main" (
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
REPLACE INTO "settings-main" ("schoolname", "schoolurl", "bd_mode", "bd_col") VALUES
	('Bishop Barrington School Sports With Mathematics College','','day','periods');
/*!40000 ALTER TABLE "settings-main" ENABLE KEYS;*/
UNLOCK TABLES;


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
) AUTO_INCREMENT=3 /*!40100 DEFAULT CHARSET=latin1 COMMENT='Main users table'*/;



#
# Dumping data for table 'users'
#

LOCK TABLES "users" WRITE;
/*!40000 ALTER TABLE "users" DISABLE KEYS;*/
REPLACE INTO "users" ("user_id", "group_id", "department_id", "enabled", "username", "firstname", "lastname", "email", "password", "password-expire", "displayname", "ext", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('1',1,'0',1,'admin',NULL,NULL,'craig.rodway@gmail.com','5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',NULL,NULL,NULL,NULL,'2008-11-24 13:05:51',0,'0000-00-00');
REPLACE INTO "users" ("user_id", "group_id", "department_id", "enabled", "username", "firstname", "lastname", "email", "password", "password-expire", "displayname", "ext", "cookiekey", "lastlogin", "ldap", "created") VALUES
	('2',2,'0',0,'craig',NULL,NULL,'craig.rodway@bishopbarrington.net','test',NULL,NULL,NULL,NULL,'0000-00-00 00:00:00',0,'2008-11-23');
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

# (No data found.)



#
# Table structure for table 'weeks'
#

CREATE TABLE /*!32312 IF NOT EXISTS*/ "weeks" (
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
