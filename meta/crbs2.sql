-- Adminer 3.6.3 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE TABLE `d2lg` (
  `d2lg_d_id` int(10) unsigned NOT NULL,
  `d2lg_lg_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `unique` (`d2lg_d_id`,`d2lg_lg_id`),
  KEY `department_id` (`d2lg_d_id`),
  KEY `ldapgroup_id` (`d2lg_lg_id`),
  CONSTRAINT `d2lg_ibfk_1` FOREIGN KEY (`d2lg_d_id`) REFERENCES `departments` (`d_id`) ON DELETE CASCADE,
  CONSTRAINT `d2lg_ibfk_2` FOREIGN KEY (`d2lg_lg_id`) REFERENCES `ldap_groups` (`lg_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Departments - LDAP groups link table';


CREATE TABLE `departments` (
  `d_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `d_s_id` int(11) unsigned NOT NULL,
  `d_name` varchar(64) NOT NULL,
  `d_description` varchar(255) DEFAULT NULL,
  `d_colour` char(7) DEFAULT NULL COMMENT 'Hex colour value',
  `d_created_datetime` datetime DEFAULT NULL,
  `d_created_u_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`d_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='Departments';

INSERT INTO `departments` (`d_id`, `d_s_id`, `d_name`, `d_description`, `d_colour`, `d_created_datetime`, `d_created_u_id`) VALUES
(1,	1,	'English',	'',	'#8AE234',	'2008-12-19 00:00:00',	NULL),
(2,	1,	'Maths',	'',	'#EDD400',	'2008-12-19 00:00:00',	NULL),
(3,	1,	'Science',	'',	'#729FCF',	'2008-12-19 00:00:00',	NULL),
(4,	1,	'ICT',	'',	'#204A87',	'2008-12-19 00:00:00',	NULL),
(5,	1,	'Music',	'Only the music department.',	'#F57900',	'2008-12-19 00:00:00',	NULL),
(6,	1,	'Humanities',	'History or Geography',	'#4E9A06',	'2008-12-19 00:00:00',	NULL),
(7,	1,	'Art',	'',	'#EF2929',	'2008-12-19 00:00:00',	NULL),
(9,	1,	'RE',	'',	'#E9B96E',	'2008-12-19 00:00:00',	NULL),
(11,	1,	'Languages',	'',	'#AD7FA8',	'2009-01-09 00:00:00',	NULL),
(12,	1,	'PE',	'',	'#2E3436',	'2009-01-09 00:00:00',	NULL),
(13,	1,	'Technology',	'',	'#BABDB6',	'2009-01-09 00:00:00',	NULL);

CREATE TABLE `events` (
  `e_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `e_u_id` int(11) unsigned NOT NULL,
  `e_object_type` char(15) NOT NULL,
  `e_object_id` int(11) unsigned NOT NULL,
  `e_action` char(15) NOT NULL,
  `e_datetime` datetime NOT NULL,
  `e_description` text NOT NULL,
  `e_ip` char(15) NOT NULL,
  PRIMARY KEY (`e_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `g2lg` (
  `g2lg_g_id` int(11) unsigned NOT NULL,
  `g2lg_lg_id` int(11) unsigned NOT NULL,
  UNIQUE KEY `ldapgroup_id` (`g2lg_lg_id`),
  KEY `group_id` (`g2lg_g_id`),
  CONSTRAINT `g2lg_ibfk_3` FOREIGN KEY (`g2lg_g_id`) REFERENCES `groups` (`g_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `g2lg_ibfk_4` FOREIGN KEY (`g2lg_lg_id`) REFERENCES `ldap_groups` (`lg_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Map LDAP groups to local CRBS groups';

INSERT INTO `g2lg` (`g2lg_g_id`, `g2lg_lg_id`) VALUES
(2,	2321);

CREATE TABLE `groups` (
  `g_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `g_s_id` int(11) unsigned NOT NULL,
  `g_name` varchar(20) NOT NULL,
  `g_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`g_id`),
  UNIQUE KEY `g_s_id_g_name` (`g_s_id`,`g_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Groups table with settings and permiss';

INSERT INTO `groups` (`g_id`, `g_s_id`, `g_name`, `g_description`) VALUES
(1,	1,	'Administrators',	'Default group for administrator users'),
(2,	1,	'Teaching Staff',	'Teachers from LDAP'),
(4,	1,	'Support staff',	'');

CREATE TABLE `holidays` (
  `h_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `h_y_id` int(11) unsigned NOT NULL COMMENT 'The academic year that this holiday is relevant to',
  `h_date_start` date NOT NULL,
  `h_date_end` date NOT NULL,
  `h_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`h_id`),
  KEY `year_id` (`h_y_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='School holidays';

INSERT INTO `holidays` (`h_id`, `h_y_id`, `h_date_start`, `h_date_end`, `h_name`) VALUES
(4,	1,	'2009-01-12',	'2009-01-24',	'Foo'),
(5,	1,	'2009-01-22',	'2009-01-22',	'test2');

CREATE TABLE `ldap_groups` (
  `lg_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lg_s_id` int(11) unsigned NOT NULL,
  `lg_guid` char(32) COLLATE utf8_unicode_ci NOT NULL COMMENT 'LDAP group objectGUID',
  `lg_name` varchar(104) COLLATE utf8_unicode_ci NOT NULL COMMENT 'LDAP group name',
  `lg_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'LDAP group description',
  PRIMARY KEY (`lg_id`),
  UNIQUE KEY `lg_s_id_lg_guid` (`lg_s_id`,`lg_guid`)
) ENGINE=InnoDB AUTO_INCREMENT=2397 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Group names retrieved from LDAP';

INSERT INTO `ldap_groups` (`lg_id`, `lg_s_id`, `lg_guid`, `lg_name`, `lg_description`) VALUES
(2286,	1,	'87ac00292267b0499ad2d7ac4274e22f',	'Enterprise Admins',	'Designated administrators of the enterprise'),
(2287,	1,	'2dd7b787649dc742a7c248db22d066b5',	'Domain Admins',	'Designated administrators of the domain'),
(2288,	1,	'719c1f943122194390bb7c670f6e8616',	'Domain Users',	'All domain users'),
(2289,	1,	'a067396dd89fb245ab40f80d1eedde6a',	'Domain Guests',	'All domain guests'),
(2290,	1,	'ef9e70c5d5a8e042abb533554b802d97',	'Group Policy Creator Owners',	'Members in this group can modify group policy for the domain'),
(2291,	1,	'df60a61de3ed22459201682c16eb8af1',	'DnsUpdateProxy',	'DNS clients who are permitted to perform dynamic updates on behalf of some other clients (such as DHCP servers).'),
(2292,	1,	'081595a3c81e4445bb96493cced741a3',	'BBS RMMC AccessRight',	'BBS RMMC AccessRight'),
(2293,	1,	'e633002e222afc4f99db2370b258cbe6',	'BBS Staff Print Operators',	'BBS Staff Print Operators'),
(2294,	1,	'33d0514315bfa74c9252ad709c3b8be4',	'BBS Accessibility',	'BBS Accessibility'),
(2295,	1,	'250f65d1e0e0674ab7350fed0fc55f50',	'BBS Advanced Station Security',	'BBS Advanced Station Security'),
(2296,	1,	'9054068f20362a46ac33c49b71364729',	'BBS Authorised ManagerType',	'BBS Authorised ManagerType'),
(2297,	1,	'7cf8d309f1a81b498f9deb7f71f8395d',	'BBS CyberCafe StationType',	'BBS CyberCafe StationType'),
(2298,	1,	'f8ec2cd649959942b8251d6a349ecc92',	'BBS Password Management ManagerType',	'BBS Password Management ManagerType'),
(2299,	1,	'be504b9a6bc82543ae6f395256f4566d',	'BBS Printer Credits Management ManagerType',	'BBS Printer Credits Management ManagerType'),
(2300,	1,	'1074d20d55456f479b1c07b11d05d3f2',	'BBS Delegate ManagerType',	'BBS Delegate ManagerType'),
(2301,	1,	'9e591c9defc3d74884e1a8763a0eddf5',	'BBS EasyLink',	'BBS EasyLink'),
(2302,	1,	'be36a0ee21d3fe4fb2a9daa56c308788',	'BBS Education Management System',	'BBS Education Management System'),
(2303,	1,	'9cf45f0240f69a41b568ecd3ce9eb7f1',	'BBS Guest UserType',	'BBS Guest UserType'),
(2304,	1,	'457891109f3c5248bd3efaf93f68ba67',	'BBS Internet Disabled',	'BBS Internet Disabled'),
(2305,	1,	'24f621ffbfb504459e12f8bd7b8a76fb',	'BBS Legacy Application Users',	'BBS Legacy Application Users'),
(2306,	1,	'084782417948ec438000f1b33328f95d',	'BBS Local Administrators',	'BBS Local Administrators'),
(2307,	1,	'9158f9843540804193ad86cd75b8fdb0',	'BBS Managed Stations',	'BBS Managed Stations'),
(2313,	1,	'bd5c525ef5f3dc44bbd2b7bf1923446d',	'BBS Shared Desktop StationType',	'BBS Shared Desktop StationType'),
(2314,	1,	'1d4d4142f470e2408e5e274f09407d95',	'BBS Shared Laptop StationType',	'BBS Shared Laptop StationType'),
(2315,	1,	'5f82ad0288a59c47941b49f62127f2a0',	'BBS Staff UserType',	'BBS Staff UserType'),
(2316,	1,	'e590ef1c23a2db4badae68f2ea312bd8',	'BBS Standard Station Security',	'BBS Standard Station Security'),
(2317,	1,	'2701fca00234724aae212e5c04ae3aed',	'BBS Standard UserType',	'BBS Standard UserType'),
(2318,	1,	'9b212f03c6e04f41a40efd5df6a70a7c',	'BBS Station Setup',	'BBS Station Setup'),
(2319,	1,	'abab4beb72a38e43960821242a423987',	'BBS Students',	'BBS Students'),
(2320,	1,	'62aea8d02489ca43a8b97126628de4e7',	'BBS Tutor Users',	'Tutor users who are allowed to run RM Tutor'),
(2321,	1,	'1a9f293d2351a04a915b9ec7eb86f57e',	'BBS Teaching Staff',	'BBS Teaching Staff'),
(2322,	1,	'4ec43fbba097d64abf82bd87039ce409',	'BBS User Controller ManagerType',	'BBS User Controller ManagerType'),
(2323,	1,	'4a91a90e58fe004f9bbe3fe70bc1c24b',	'BBS EDI System',	'BBS EDI System'),
(2324,	1,	'9aaa9b1788bacd459f75cdfb6c81b648',	'BBS Finance System',	'BBS Finance System'),
(2325,	1,	'21ea770eda16964f812a1ab1a5536636',	'BBS MIS Manager',	'BBS MIS Manager'),
(2326,	1,	'4d4b3264b1cd6544bb4bfb168bae5f2c',	'BBS Network ManagerType',	'BBS Network ManagerType'),
(2327,	1,	'6d29689ba8cb6a49a62e77bf590060fe',	'BBS No GPO Security',	'BBS No GPO Security'),
(2328,	1,	'1f8d3176e4c57a489f1e233a6a26b02a',	'BBS Associates',	'BBS Associates'),
(2329,	1,	'2c59c11fd7afbf41b9166c35c74b39bd',	'BBS Remote Desktop Users',	'BBS Remote Desktop Users'),
(2330,	1,	'809523cc7fe5664da23a10f80a4ada38',	'BBS Build in Progress Station Security',	'BBS Build in Progress Station Security'),
(2331,	1,	'4ac7ef1a08d27f45b54fddd8515f7ef4',	'BBS Terminal Server StationType',	'BBS Terminal Server StationType'),
(2332,	1,	'7fb956321e52fa45af04052c50c94936',	'BBS Member Servers',	'BBS Member Servers'),
(2333,	1,	'84a899ca63fde246880b924c55fdcdc5',	'BBS FSD User',	'BBS FSD User'),
(2334,	1,	'a2070dbb4df80d4eb1e329defbf001a8',	'Policy Managers',	'RM User and Workstation Type Managers'),
(2335,	1,	'1ee017052f55a647beb5ab1c353aee02',	'GRMREP',	'Contains a list of program set servers'),
(2336,	1,	'7076c7b08eb5dd41a69242d51ef8bcb4',	'BBS Example Teachers',	'BBS Example Teachers'),
(2337,	1,	'dc498dcaca9a614fb1bcc6fdfbb9bdb0',	'BBS Centralised User Management ManagerType',	'BBS Centralised User Management ManagerType'),
(2338,	1,	'57c5b1d25081c843b0bb4aca74dbf5ba',	'BBS Build Warnings',	'BBS Build Warnings'),
(2339,	1,	'687a83ce3280a340a863587f6eebc60c',	'BBS Station Build',	'BBS Station Build'),
(2340,	1,	'6661b6be243392489b5cbf1c8de33eec',	'KLAdmins',	'Kaspersky Administration Kit administrators'),
(2341,	1,	'a0fb21d34ccc274a86ca570cd15a222c',	'KLOperators',	'Kaspersky Administration Kit operators'),
(2342,	1,	'fcc79c560922bf4f9835fc1aac214ad1',	'IWB Users',	'Users who use interactive whiteboards.'),
(2343,	1,	'2a57ae1686df1e42b3ffabcd0f21c4cd',	'Careers Teachers',	'Group who deliver the Careers teaching and learning'),
(2344,	1,	'31d4f7da202c3f4ca9f136ff7f076572',	'BBS Art and Design Teachers',	'BBS Art and Design Teachers'),
(2345,	1,	'7a1ac91e4b04a74d9c78c7e76b6adaf8',	'BBS Careers Teachers',	'BBS Careers Teachers'),
(2346,	1,	'ddd56548f9285d449890417b4b6d18c8',	'BBS English Teachers',	'BBS English Teachers'),
(2347,	1,	'a70c92d229d2ea48b6aa8328e83a6366',	'BBS Food Technology Teachers',	'BBS Food Technology Teachers'),
(2348,	1,	'08893768f9e95b4b8a05f23a520b7424',	'BBS Geography Teachers',	'BBS Geography Teachers'),
(2349,	1,	'ff480e127530cd46b67431cc1cb81913',	'BBS History Teachers',	'BBS History Teachers'),
(2350,	1,	'dd23f283eeada344accb2bcf4a881175',	'BBS ICT Teachers',	'BBS ICT Teachers'),
(2351,	1,	'962ba0b6f92ed747ae17f06b00572f79',	'BBS Languages Teachers',	'BBS Languages Teachers'),
(2352,	1,	'bedebd3fce5d1f408c240dd2ee9ee185',	'BBS Maths Teachers',	'BBS Maths Teachers'),
(2353,	1,	'8cb06349b7464041915aa4ba1f07fddc',	'BBS Music Teachers',	'BBS Music Teachers'),
(2354,	1,	'd10f9d457f819d4487d9c9d368f17498',	'BBS PE Teachers',	'BBS PE Teachers'),
(2355,	1,	'1afb7e6f65d4314ea7d437d2f929569d',	'BBS RE Teachers',	'BBS RE Teachers'),
(2356,	1,	'4d81ae6c212a6244948087573724b682',	'BBS Science Teachers',	'BBS Science Teachers'),
(2357,	1,	'76c825cbb0af6e418ac14fdb4db3f71f',	'BBS Technology Teachers',	'BBS Technology Teachers'),
(2358,	1,	'ebcc4405cc8b5741917c2fd7c74939c7',	'BBS Outdoor',	'Group for staff who deliver outdoor pursuits.'),
(2359,	1,	'ebc1619b980e56459f54b82cc536ce54',	'BBS Exams Officers',	'Group for RM SecureNet'),
(2360,	1,	'8c3895355b0c5a41a139910582dafa04',	'BBS Literacy Intervention Teachers',	'BBS Literacy Intervention Teachers'),
(2361,	1,	'cc1a512052299043b128c35b375eeda6',	'Literacy Students',	'Students who require Literacy Intervention software'),
(2362,	1,	'267ec16fb7771742b9657a3b2c86f9e7',	'BBS SSP Teachers',	'BBS SSP Teachers'),
(2363,	1,	'6517500b8552464a8e056ea22e140d14',	'BBS Truancy Call',	'Shortcuts for both Truancy Call and Call Parents'),
(2364,	1,	'501e78c3c32c1f4e84cff40fa4e6d6ef',	'BBS ICT Room Control Users',	'Users who should be able to control access in ICT suites.'),
(2365,	1,	'db527ece0b15644f8944b9d72c985040',	'BBS Student Work Viewer',	'Users who can access student work'),
(2366,	1,	'e7c99314bea55547acc792376cf7b05c',	'BBS PLO Teachers',	'BBS PLO Teachers'),
(2367,	1,	'2939215d28f8644b837c475d13b67dec',	'BBS Achievement Centre Teachers',	'BBS Achievement Centre Teachers'),
(2368,	1,	'937509ca1a2c904eac5412c7b933f1e6',	'BBS Eco Group',	'Members of the Eco group'),
(2369,	1,	'18b93d7c65a8e84cb697094114ac903d',	'BBS Personal Development Teachers',	'BBS Personal Development Teachers'),
(2370,	1,	'c9f7200be783774f9d7e5e4c2ca883e5',	'BBS Student Year 11',	'Year 11 student users'),
(2371,	1,	'35bb1b09167fd042b44e9283885560ce',	'BBS Student Year 10',	'Year 10 student users'),
(2372,	1,	'added19ba81384459064d6400b2bbcc0',	'BBS Student Year 9',	'Year 9 student users'),
(2373,	1,	'23c359642a241e42966d3986cb63c54b',	'BBS Student Year 8',	'Year 8 student users'),
(2374,	1,	'eb5465a3db720c4689f37c39c7c0d204',	'BBS Student Year 7',	'Year 7 student users'),
(2375,	1,	'8bd37b1fd519824eacbc74952e2dbe1c',	'Media Server System',	'Group who have access to the Media Streaming Server System'),
(2376,	1,	'59fec1e3b987504f9b6c959cb991a93c',	'BBS MAT Teachers',	'BBS MAT Teachers'),
(2377,	1,	'931395679381df48bd9389eef7547e5b',	'Library Admins',	'School Library users of QuicktrackPro4 software'),
(2378,	1,	'68b473da593ae3409e857d4beace82e4',	'BBS Kindle Reader',	'Group who receive the Kindle Reader shortcut.'),
(2379,	1,	'8d23e1ad10eeb44c850acbaf04b7d79a',	'BBS Student Teachers',	'SCITT Students Group'),
(2380,	1,	'61e886f27660d445a4f9c25531c5cb96',	'BBS Adobe Creative Suite Users',	'Shortcuts for Adobe Indesign Suite'),
(2381,	1,	'8a4472761cb3f043b11e40748dfdb205',	'BBS Genee Users',	'Shortcut for Visualisers '),
(2382,	1,	'e7821f70a91c4f4e84f393e3f8c78228',	'Technology Microscope User',	'Shortcut for Technology Microscope Users'),
(2383,	1,	'faf9044a4e182a48b3d94aa4d66cb0f5',	'BBS CCTV Users',	'Group of users who have access to the CCTV software'),
(2384,	1,	'd1e40696f0939742bb363be4625c5679',	'BBS Admin UserType',	'Admin UserType'),
(2385,	1,	'cba09dd605f0ff4caba11167d68da067',	'BBS Office Admin Users',	'Admin users with restricted access to the MIS systems'),
(2386,	1,	'd85e58f39b1cb54c9e277319cef77c08',	'BBS Reception users',	'Users of the reception area'),
(2387,	1,	'ae49eeabf479b14194c3909aefda283c',	'BBS HP100ENetbooks StationType',	'HP100ENetbooks StationType'),
(2388,	1,	'c3f194b535e8f3438ae77ac47d33624b',	'BBS Cover Supervisors',	'Security group for cover supervisors'),
(2389,	1,	'95d60116c638784b8c32f03e19b8ecd5',	'Domain Computers',	'All workstations and servers joined to the domain'),
(2390,	1,	'd3d4b6847e5b264d80d55554c8b76490',	'Domain Controllers',	'All domain controllers in the domain'),
(2391,	1,	'284463cf5f1aca42ba7834daa2efba4a',	'Schema Admins',	'Designated administrators of the schema'),
(2392,	1,	'04f3d174b88b714383c1118dc73efc9e',	'BBS Management Information System',	'BBS Management Information System'),
(2393,	1,	'f8fcca5eb77ef443a70900df74a36645',	'BBS No Station Security',	'BBS No Station Security'),
(2394,	1,	'92c2c38b5f0a7942b3389177385f1189',	'BBS Non-Teaching Staff',	'BBS Non-Teaching Staff'),
(2395,	1,	'0ce33f21d3ab9140aad09db00c258236',	'BBS Personal StationType',	'BBS Personal StationType'),
(2396,	1,	'2d615ef87aee8143b4fce63dcadd28ba',	'BBS RM Explorer UserType',	'BBS RM Explorer UserType');

CREATE TABLE `menu` (
  `m_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `m_parent_m_id` int(11) unsigned DEFAULT NULL,
  `m_uri` varchar(32) NOT NULL,
  `m_lang_key` char(16) NOT NULL,
  `m_permission` varchar(64) NOT NULL,
  `m_classes` varchar(64) NOT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `options` (
  `o_s_id` int(11) unsigned NOT NULL COMMENT 'School ID',
  `o_name` char(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Option name',
  `o_value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'Option value',
  PRIMARY KEY (`o_s_id`,`o_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='All settings';

INSERT INTO `options` (`o_s_id`, `o_name`, `o_value`) VALUES
(1,	'auth_anon_u_id',	'127'),
(1,	'auth_ldap_base',	'ou=teaching staff, ou=bbs, ou=establishments, dc=bbarrington, dc=internal; ou=system administrators, ou=bbs, ou=establishments, dc=bbarrington, dc=internal'),
(1,	'auth_ldap_enable',	'1'),
(1,	'auth_ldap_filter',	'(& (| (!(displayname=Administrator*)) (!(displayname=Admin*)) ) (cn=%u) )'),
(1,	'auth_ldap_g_id',	'4'),
(1,	'auth_ldap_groupid',	'2'),
(1,	'auth_ldap_host',	'localhost'),
(1,	'auth_ldap_loginupdate',	'0'),
(1,	'auth_ldap_port',	'389'),
(1,	'auth_ldap_update',	'0'),
(1,	'auth_preauth_email_domain',	'bishopbarrington.net'),
(1,	'auth_preauth_emaildomain',	'bishopbarrington.net'),
(1,	'auth_preauth_enable',	'1'),
(1,	'auth_preauth_g_id',	'4'),
(1,	'auth_preauth_groupid',	'0'),
(1,	'auth_preauth_key',	'9a79a9c7d19c13a2b809ffc98c1313f59cf9be8b'),
(1,	'school_name',	'Bishop Barrington School'),
(1,	'school_url',	'http://www.bishopbarrington.net'),
(1,	'timetable_cols',	'periods'),
(1,	'timetable_view',	'day');

CREATE TABLE `p2r` (
  `p2r_p_id` int(11) unsigned NOT NULL,
  `p2r_r_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`p2r_p_id`,`p2r_r_id`),
  KEY `role_id` (`p2r_r_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Permissions <-> Roles';

INSERT INTO `p2r` (`p2r_p_id`, `p2r_r_id`) VALUES
(1,	3),
(2,	3),
(3,	3),
(4,	3),
(9,	3),
(47,	3),
(48,	3),
(49,	3),
(1,	4),
(2,	4),
(3,	4),
(4,	4),
(5,	4),
(6,	4),
(7,	4),
(8,	4),
(9,	4),
(10,	4),
(11,	4),
(12,	4),
(13,	4),
(14,	4),
(15,	4),
(16,	4),
(17,	4),
(18,	4),
(19,	4),
(20,	4),
(21,	4),
(22,	4),
(23,	4),
(24,	4),
(25,	4),
(26,	4),
(27,	4),
(28,	4),
(29,	4),
(30,	4),
(31,	4),
(32,	4),
(33,	4),
(34,	4),
(35,	4),
(36,	4),
(37,	4),
(38,	4),
(39,	4),
(40,	4),
(41,	4),
(42,	4),
(43,	4),
(44,	4),
(45,	4),
(46,	4),
(47,	4),
(48,	4),
(49,	4),
(50,	4),
(51,	4),
(52,	4),
(53,	4),
(54,	4),
(55,	4),
(56,	4),
(57,	4),
(58,	4),
(59,	4),
(60,	4),
(61,	4),
(62,	4),
(63,	4),
(64,	4),
(65,	4),
(66,	4),
(67,	4),
(68,	4),
(69,	4),
(71,	4),
(1,	6),
(2,	6),
(4,	6),
(47,	6),
(48,	6),
(4,	71);

CREATE TABLE `periods` (
  `pd_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pd_y_id` int(11) unsigned NOT NULL,
  `pd_time_start` time NOT NULL,
  `pd_time_end` time NOT NULL,
  `pd_name` varchar(20) NOT NULL,
  `pd_days` varchar(255) NOT NULL COMMENT 'JSON-encoded array of weekdays the period is for',
  `pd_enabled` tinyint(1) NOT NULL COMMENT 'Boolean 1 or 0 if periods can be booked or not',
  PRIMARY KEY (`pd_id`),
  KEY `year_id` (`pd_y_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='Periods';

INSERT INTO `periods` (`pd_id`, `pd_y_id`, `pd_time_start`, `pd_time_end`, `pd_name`, `pd_days`, `pd_enabled`) VALUES
(9,	1,	'08:45:00',	'09:00:00',	'Registration',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(10,	1,	'09:00:00',	'10:00:00',	'Period 1',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(11,	1,	'10:00:00',	'11:00:00',	'Period 2',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(12,	1,	'11:00:00',	'11:15:00',	'Break',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(13,	1,	'11:15:00',	'12:15:00',	'Period 3',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(14,	1,	'12:15:00',	'13:20:00',	'Lunch',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(15,	1,	'13:20:00',	'14:20:00',	'Period 4',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(16,	1,	'14:20:00',	'15:20:00',	'Period 5',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(49,	1,	'15:20:00',	'18:30:00',	'After school',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(50,	5,	'08:45:00',	'09:00:00',	'Registration',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(51,	5,	'09:00:00',	'10:00:00',	'Period 1',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(52,	5,	'10:00:00',	'11:00:00',	'Period 2',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(53,	5,	'11:00:00',	'11:15:00',	'Break',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(54,	5,	'11:15:00',	'12:15:00',	'Period 3',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(55,	5,	'12:15:00',	'13:20:00',	'Lunch',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(56,	5,	'13:20:00',	'14:20:00',	'Period 4',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(57,	5,	'14:20:00',	'15:20:00',	'Period 5',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(58,	5,	'15:20:00',	'18:30:00',	'After school',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(59,	7,	'08:45:00',	'09:00:00',	'Registration',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(60,	7,	'09:00:00',	'10:00:00',	'Period 1',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(61,	7,	'10:00:00',	'11:00:00',	'Period 2',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(62,	7,	'11:00:00',	'11:15:00',	'Break',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(63,	7,	'11:15:00',	'12:15:00',	'Period 3',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(64,	7,	'12:15:00',	'13:20:00',	'Lunch',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	0),
(65,	7,	'13:20:00',	'14:20:00',	'Period 4',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(66,	7,	'14:20:00',	'15:20:00',	'Period 5',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1),
(67,	7,	'15:20:00',	'18:30:00',	'After school',	'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"3\";i:3;s:1:\"4\";i:4;s:1:\"5\";}',	1);

CREATE TABLE `permissions` (
  `p_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `p_section` char(16) NOT NULL,
  `p_name` varchar(64) NOT NULL,
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `name` (`p_name`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

INSERT INTO `permissions` (`p_id`, `p_section`, `p_name`) VALUES
(1,	'general',	'crbs.dashboard.view'),
(2,	'general',	'crbs.dashboard.viewdept'),
(3,	'general',	'crbs.dashboard.viewown'),
(4,	'general',	'crbs.account.view'),
(5,	'general',	'crbs.account.changepwd'),
(6,	'general',	'crbs.configure'),
(7,	'general',	'crbs.year.change'),
(8,	'general',	'crbs.rooms.exempt'),
(9,	'general',	'bookings.view'),
(10,	'general',	'bookings.create.one'),
(11,	'general',	'bookings.create.recur'),
(12,	'general',	'bookings.delete.one.own'),
(13,	'general',	'bookings.delete.one.recur'),
(14,	'general',	'bookings.create.proxy'),
(15,	'general',	'bookings.delete.recur.roomowner'),
(16,	'general',	'rooms.view'),
(17,	'general',	'rooms.add'),
(18,	'general',	'rooms.edit'),
(19,	'general',	'rooms.delete'),
(20,	'general',	'rooms.attrs'),
(21,	'general',	'rooms.permissions'),
(22,	'general',	'periods.view'),
(23,	'general',	'periods.add'),
(24,	'general',	'periods.edit'),
(25,	'general',	'periods.delete'),
(26,	'general',	'years.view'),
(27,	'general',	'years.add'),
(28,	'general',	'years.edit'),
(29,	'general',	'years.delete'),
(30,	'general',	'weeks.view'),
(31,	'general',	'weeks.add'),
(32,	'general',	'weeks.edit'),
(33,	'general',	'weeks.delete'),
(34,	'general',	'holidays.view'),
(35,	'general',	'holidays.add'),
(36,	'general',	'holidays.edit'),
(37,	'general',	'holidays.delete'),
(38,	'general',	'terms.view'),
(39,	'general',	'terms.add'),
(40,	'general',	'terms.edit'),
(41,	'general',	'terms.delete'),
(42,	'general',	'departments.view'),
(43,	'general',	'departments.add'),
(44,	'general',	'departments.edit'),
(45,	'general',	'departments.delete'),
(46,	'general',	'reports.view'),
(47,	'general',	'reports.view.department.own'),
(48,	'general',	'reports.view.department.all'),
(49,	'general',	'reports.view.room.own'),
(50,	'general',	'reports.view.room.all'),
(51,	'general',	'reports.view.all'),
(52,	'general',	'users.view'),
(53,	'general',	'users.add'),
(54,	'general',	'users.edit'),
(55,	'general',	'users.delete'),
(56,	'general',	'users.import'),
(57,	'general',	'groups.view'),
(58,	'general',	'groups.add'),
(59,	'general',	'groups.edit'),
(60,	'general',	'groups.delete'),
(61,	'general',	'permissions.view'),
(62,	'general',	'room.view'),
(63,	'general',	'room.booking.create.one'),
(64,	'general',	'room.booking.create.recur'),
(65,	'general',	'room.booking.create.one.proxy'),
(66,	'general',	'crbs.eventlog.view'),
(67,	'general',	'crbs.configure.authentication'),
(68,	'general',	'crbs.configure.settings'),
(69,	'users',	'users.edit.password'),
(70,	'quota',	'quota.view'),
(71,	'quota',	'quota.set.user');

CREATE TABLE `permissions_cache` (
  `pc_u_id` int(11) unsigned NOT NULL,
  `pc_permissions` text NOT NULL,
  PRIMARY KEY (`pc_u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `permissions_cache` (`pc_u_id`, `pc_permissions`) VALUES
(1,	'a:70:{i:1;s:19:\"crbs.dashboard.view\";i:2;s:23:\"crbs.dashboard.viewdept\";i:3;s:22:\"crbs.dashboard.viewown\";i:4;s:17:\"crbs.account.view\";i:5;s:22:\"crbs.account.changepwd\";i:6;s:14:\"crbs.configure\";i:7;s:16:\"crbs.year.change\";i:8;s:17:\"crbs.rooms.exempt\";i:9;s:13:\"bookings.view\";i:10;s:19:\"bookings.create.one\";i:11;s:21:\"bookings.create.recur\";i:12;s:23:\"bookings.delete.one.own\";i:13;s:25:\"bookings.delete.one.recur\";i:14;s:21:\"bookings.create.proxy\";i:15;s:31:\"bookings.delete.recur.roomowner\";i:16;s:10:\"rooms.view\";i:17;s:9:\"rooms.add\";i:18;s:10:\"rooms.edit\";i:19;s:12:\"rooms.delete\";i:20;s:11:\"rooms.attrs\";i:21;s:17:\"rooms.permissions\";i:22;s:12:\"periods.view\";i:23;s:11:\"periods.add\";i:24;s:12:\"periods.edit\";i:25;s:14:\"periods.delete\";i:26;s:10:\"years.view\";i:27;s:9:\"years.add\";i:28;s:10:\"years.edit\";i:29;s:12:\"years.delete\";i:30;s:10:\"weeks.view\";i:31;s:9:\"weeks.add\";i:32;s:10:\"weeks.edit\";i:33;s:12:\"weeks.delete\";i:34;s:13:\"holidays.view\";i:35;s:12:\"holidays.add\";i:36;s:13:\"holidays.edit\";i:37;s:15:\"holidays.delete\";i:38;s:10:\"terms.view\";i:39;s:9:\"terms.add\";i:40;s:10:\"terms.edit\";i:41;s:12:\"terms.delete\";i:42;s:16:\"departments.view\";i:43;s:15:\"departments.add\";i:44;s:16:\"departments.edit\";i:45;s:18:\"departments.delete\";i:46;s:12:\"reports.view\";i:47;s:27:\"reports.view.department.own\";i:48;s:27:\"reports.view.department.all\";i:49;s:21:\"reports.view.room.own\";i:50;s:21:\"reports.view.room.all\";i:51;s:16:\"reports.view.all\";i:52;s:10:\"users.view\";i:53;s:9:\"users.add\";i:54;s:10:\"users.edit\";i:55;s:12:\"users.delete\";i:56;s:12:\"users.import\";i:57;s:11:\"groups.view\";i:58;s:10:\"groups.add\";i:59;s:11:\"groups.edit\";i:60;s:13:\"groups.delete\";i:61;s:16:\"permissions.view\";i:62;s:9:\"room.view\";i:63;s:23:\"room.booking.create.one\";i:64;s:25:\"room.booking.create.recur\";i:65;s:29:\"room.booking.create.one.proxy\";i:66;s:18:\"crbs.eventlog.view\";i:67;s:29:\"crbs.configure.authentication\";i:68;s:23:\"crbs.configure.settings\";i:69;s:19:\"users.edit.password\";i:71;s:14:\"quota.set.user\";}'),
(130,	'a:8:{i:4;s:17:\"crbs.account.view\";i:3;s:22:\"crbs.dashboard.viewown\";i:49;s:21:\"reports.view.room.own\";i:2;s:23:\"crbs.dashboard.viewdept\";i:48;s:27:\"reports.view.department.all\";i:1;s:19:\"crbs.dashboard.view\";i:47;s:27:\"reports.view.department.own\";i:9;s:13:\"bookings.view\";}');

CREATE TABLE `quota` (
  `q_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `q_u_id` int(11) unsigned NOT NULL,
  `q_r_id` int(11) unsigned DEFAULT NULL,
  `q_type` char(16) COLLATE utf8_unicode_ci NOT NULL,
  `q_initial` smallint(5) unsigned NOT NULL DEFAULT '0',
  `q_value` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`q_id`),
  KEY `q_u_id` (`q_u_id`),
  KEY `q_r_id` (`q_r_id`),
  CONSTRAINT `quota_ibfk_1` FOREIGN KEY (`q_u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  CONSTRAINT `quota_ibfk_2` FOREIGN KEY (`q_r_id`) REFERENCES `roles` (`r_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


CREATE TABLE `r2d` (
  `r2d_r_id` int(11) unsigned NOT NULL,
  `r2d_d_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`r2d_r_id`,`r2d_d_id`),
  KEY `department_id` (`r2d_d_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `r2d` (`r2d_r_id`, `r2d_d_id`) VALUES
(10,	4);

CREATE TABLE `r2g` (
  `r2g_r_id` int(11) unsigned NOT NULL,
  `r2g_g_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`r2g_r_id`,`r2g_g_id`),
  KEY `group_id` (`r2g_g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `r2g` (`r2g_r_id`, `r2g_g_id`) VALUES
(4,	1),
(3,	2),
(11,	4);

CREATE TABLE `r2u` (
  `r2u_r_id` int(11) unsigned NOT NULL,
  `r2u_u_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`r2u_r_id`,`r2u_u_id`),
  KEY `user_id` (`r2u_u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `r2u` (`r2u_r_id`, `r2u_u_id`) VALUES
(4,	1),
(6,	127);

CREATE TABLE `roles` (
  `r_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `r_s_id` int(11) unsigned NOT NULL,
  `r_weight` smallint(5) unsigned DEFAULT '0',
  `r_name` varchar(20) NOT NULL,
  `r_booking_days_min` smallint(3) unsigned NOT NULL DEFAULT '0',
  `r_booking_days_max` smallint(3) unsigned NOT NULL DEFAULT '14',
  `r_quota_type` enum('day','week','month','concurrent') DEFAULT NULL,
  `r_quota_num` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`r_id`),
  UNIQUE KEY `name` (`r_name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`r_id`, `r_s_id`, `r_weight`, `r_name`, `r_booking_days_min`, `r_booking_days_max`, `r_quota_type`, `r_quota_num`) VALUES
(3,	1,	4,	'Teacher',	0,	14,	NULL,	NULL),
(4,	1,	1,	'Administrator',	0,	14,	NULL,	NULL),
(6,	1,	2,	'Head of department',	0,	14,	NULL,	NULL),
(10,	1,	3,	'ICT Dept',	0,	14,	NULL,	NULL),
(11,	1,	5,	'Non-teaching staff',	0,	14,	NULL,	NULL);

CREATE TABLE `room_attr_fields` (
  `raf_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `raf_name` varchar(20) NOT NULL,
  `raf_type` enum('text','select','check') NOT NULL COMMENT 'Text: textbox; Select: Choose one item from list; Check: Boolean on/off',
  `raf_select_options` text COMMENT 'JSON-encoded array of selectable options for drop-down type',
  PRIMARY KEY (`raf_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='Names of fields that can be assigned to rooms';

INSERT INTO `room_attr_fields` (`raf_id`, `raf_name`, `raf_type`, `raf_select_options`) VALUES
(13,	'Number of computers',	'text',	NULL),
(17,	'Colour printer',	'check',	NULL),
(18,	'Mono printer',	'check',	NULL),
(19,	'Scanner',	'check',	NULL),
(20,	'Dropdown',	'select',	'e3b9f91422c68deefb7a78b32dfc70d5');

CREATE TABLE `room_attr_values` (
  `rav_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Value ID',
  `rav_rm_id` int(11) unsigned NOT NULL COMMENT 'Room ID',
  `rav_raf_id` int(11) unsigned NOT NULL COMMENT 'Field ID',
  `rav_value` varchar(255) NOT NULL COMMENT 'Value',
  PRIMARY KEY (`rav_id`),
  UNIQUE KEY `attr` (`rav_rm_id`,`rav_raf_id`),
  KEY `field_id` (`rav_raf_id`),
  KEY `room_id` (`rav_rm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=231 DEFAULT CHARSET=utf8 COMMENT='Actual values of room fields for each room';

INSERT INTO `room_attr_values` (`rav_id`, `rav_rm_id`, `rav_raf_id`, `rav_value`) VALUES
(176,	1,	13,	'30'),
(177,	1,	17,	'1'),
(178,	1,	18,	'1'),
(179,	1,	19,	''),
(180,	3,	13,	'29'),
(181,	3,	17,	'1'),
(182,	3,	18,	'1'),
(183,	3,	19,	'1'),
(184,	6,	13,	'30'),
(185,	6,	17,	'1'),
(186,	6,	18,	'1'),
(187,	6,	19,	''),
(188,	5,	13,	'15'),
(189,	5,	17,	'1'),
(190,	5,	18,	''),
(191,	5,	19,	''),
(226,	2,	13,	'7'),
(227,	2,	20,	'35'),
(228,	2,	17,	'1'),
(229,	2,	18,	''),
(230,	2,	19,	'');

CREATE TABLE `room_categories` (
  `rc_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rc_name` varchar(25) NOT NULL,
  PRIMARY KEY (`rc_id`),
  UNIQUE KEY `name` (`rc_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Categories that rooms can belong to';

INSERT INTO `room_categories` (`rc_id`, `rc_name`) VALUES
(4,	'0'),
(1,	'ICT'),
(3,	'Maths'),
(2,	'Technology');

CREATE TABLE `room_permissions` (
  `rp_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rp_ref` char(10) NOT NULL COMMENT 'Unique reference for this entry',
  `rp_rm_id` int(11) unsigned NOT NULL COMMENT 'Room ID',
  `rp_type` enum('e','o','u','g','d','r') NOT NULL COMMENT 'E: everyone; O: owner; U: user; G: group; D: department, R: role',
  `rp_u_id` int(11) unsigned DEFAULT NULL COMMENT 'User ID',
  `rp_g_id` int(11) unsigned DEFAULT NULL COMMENT 'Group ID',
  `rp_d_id` int(11) unsigned DEFAULT NULL COMMENT 'Department ID',
  `rp_r_id` int(11) unsigned DEFAULT NULL COMMENT 'Role ID',
  `rp_permission` varchar(50) NOT NULL COMMENT 'A single permission',
  PRIMARY KEY (`rp_id`),
  KEY `user_id` (`rp_u_id`),
  KEY `group_id` (`rp_g_id`),
  KEY `department_id` (`rp_d_id`),
  KEY `room_id` (`rp_rm_id`),
  KEY `entry_ref` (`rp_ref`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8 COMMENT='Permission entries for various objects on different rooms';

INSERT INTO `room_permissions` (`rp_id`, `rp_ref`, `rp_rm_id`, `rp_type`, `rp_u_id`, `rp_g_id`, `rp_d_id`, `rp_r_id`, `rp_permission`) VALUES
(78,	'1:e:0',	1,	'e',	NULL,	NULL,	NULL,	NULL,	'bookings.view'),
(79,	'1:e:0',	1,	'e',	NULL,	NULL,	NULL,	NULL,	'bookings.create.one'),
(88,	'3:g:2',	3,	'g',	NULL,	2,	NULL,	NULL,	'bookings.view'),
(89,	'3:g:2',	3,	'g',	NULL,	2,	NULL,	NULL,	'bookings.create.one'),
(90,	'6:e:0',	6,	'e',	NULL,	NULL,	NULL,	NULL,	'bookings.view'),
(92,	'1:d:4',	1,	'd',	NULL,	NULL,	4,	NULL,	'bookings.create.one'),
(93,	'1:d:4',	1,	'd',	NULL,	NULL,	4,	NULL,	'bookings.create.recur'),
(94,	'1:d:4',	1,	'd',	NULL,	NULL,	4,	NULL,	'bookings.delete.own.one'),
(95,	'1:d:4',	1,	'd',	NULL,	NULL,	4,	NULL,	'bookings.delete.own.recur'),
(96,	'1:d:4',	1,	'd',	NULL,	NULL,	4,	NULL,	'bookings.edit.one'),
(97,	'1:d:4',	1,	'd',	NULL,	NULL,	4,	NULL,	'bookings.edit.recur'),
(98,	'1:o:0',	1,	'o',	NULL,	NULL,	NULL,	NULL,	'bookings.delete.other.one'),
(99,	'1:o:0',	1,	'o',	NULL,	NULL,	NULL,	NULL,	'bookings.delete.other.recur'),
(102,	'5:d:13',	5,	'd',	NULL,	NULL,	13,	NULL,	'bookings.view'),
(103,	'5:d:13',	5,	'd',	NULL,	NULL,	13,	NULL,	'bookings.create.one'),
(104,	'5:d:13',	5,	'd',	NULL,	NULL,	13,	NULL,	'bookings.delete.own.one'),
(105,	'5:d:13',	5,	'd',	NULL,	NULL,	13,	NULL,	'bookings.edit.one'),
(106,	'5:o:0',	5,	'o',	NULL,	NULL,	NULL,	NULL,	'bookings.delete.other.one');

CREATE TABLE `rooms` (
  `rm_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rm_rc_id` int(11) unsigned DEFAULT NULL COMMENT 'An optional category that the room can belong to',
  `rm_owner_u_id` int(11) unsigned DEFAULT NULL COMMENT 'Specifies an owner (user) of the room',
  `rm_order` tinyint(3) unsigned DEFAULT NULL COMMENT 'Order that the rooms appear in (optional)',
  `rm_name` varchar(20) NOT NULL,
  `rm_description` varchar(255) DEFAULT NULL,
  `rm_enabled` tinyint(1) NOT NULL COMMENT 'Boolean yes/no',
  `rm_capacity` tinyint(3) unsigned DEFAULT NULL,
  `rm_picture` char(32) DEFAULT NULL COMMENT 'Filename',
  `rm_created_datetime` datetime DEFAULT NULL COMMENT 'Date the entry was created',
  `rm_created_u_id` int(11) unsigned DEFAULT NULL COMMENT 'Created by User ID',
  PRIMARY KEY (`rm_id`),
  KEY `user_id` (`rm_owner_u_id`),
  KEY `category_id` (`rm_rc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='School rooms';

INSERT INTO `rooms` (`rm_id`, `rm_rc_id`, `rm_owner_u_id`, `rm_order`, `rm_name`, `rm_description`, `rm_enabled`, `rm_capacity`, `rm_picture`, `rm_created_datetime`, `rm_created_u_id`) VALUES
(1,	1,	NULL,	NULL,	'ICT1',	'ICT Suite',	1,	0,	'14ae70cf0379e7.#.JPG',	NULL,	NULL),
(2,	NULL,	NULL,	NULL,	'Room 16',	'',	0,	7,	'14ae7116cd7f20.#.JPG',	NULL,	NULL),
(3,	1,	1,	NULL,	'ICT2',	'Room 13',	1,	NULL,	'0',	NULL,	NULL),
(5,	2,	NULL,	NULL,	'RM39',	'Tech Suite',	1,	0,	'0',	'2009-02-13 00:00:00',	NULL),
(6,	1,	NULL,	NULL,	'ICT3',	'',	1,	NULL,	'0',	'2009-10-26 00:00:00',	NULL);

CREATE TABLE `schools` (
  `s_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `s_name` varchar(128) NOT NULL,
  `s_subdomain` char(32) NOT NULL,
  PRIMARY KEY (`s_id`),
  KEY `s_subdomain` (`s_subdomain`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `schools` (`s_id`, `s_name`, `s_subdomain`) VALUES
(1,	'Bishop Barrington School',	'bbs');

CREATE TABLE `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `sessions` (`session_id`, `ip_address`, `user_agent`, `last_activity`, `user_data`) VALUES
('1875fe3f9e7dc25f26017a305b4e4638',	'127.0.0.1',	'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.97 Safari/537.22',	1361738547,	'a:2:{s:9:\"user_data\";s:0:\"\";s:3:\"uri\";s:5:\"users\";}'),
('40fad26423f368deec7c099db85f2bd3',	'127.0.0.1',	'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.57 Safari/537.17',	1361737777,	'a:8:{s:9:\"user_data\";s:0:\"\";s:4:\"u_id\";s:1:\"1\";s:10:\"u_username\";s:5:\"admin\";s:7:\"u_email\";s:22:\"craig.rodway@gmail.com\";s:9:\"u_display\";s:12:\"Craig Rodway\";s:11:\"year_active\";s:2:\"12\";s:12:\"year_working\";s:2:\"12\";s:12:\"active_token\";s:40:\"79c3540d52f995dc8c070a0f12d773ae9d183ba5\";}');

CREATE TABLE `terms` (
  `t_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `t_y_id` int(11) unsigned NOT NULL COMMENT 'The academic year that this term belongs to',
  `t_date_start` date NOT NULL COMMENT 'Start date of the term',
  `t_date_end` date NOT NULL COMMENT 'End date of the term',
  `t_name` varchar(40) NOT NULL COMMENT 'Name of the term',
  PRIMARY KEY (`t_id`),
  UNIQUE KEY `uniquedates` (`t_date_start`,`t_date_end`),
  UNIQUE KEY `date_start` (`t_date_start`),
  UNIQUE KEY `date_end` (`t_date_end`),
  KEY `year_id` (`t_y_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='Term dates';

INSERT INTO `terms` (`t_id`, `t_y_id`, `t_date_start`, `t_date_end`, `t_name`) VALUES
(1,	1,	'2008-09-08',	'2008-10-24',	'Autumn'),
(2,	1,	'2009-01-05',	'2009-02-13',	'Spring A'),
(10,	1,	'2009-04-09',	'2009-05-16',	'Foo');

CREATE TABLE `time_slots` (
  `ts_y_id` int(10) unsigned NOT NULL,
  `ts_day_start` time NOT NULL COMMENT 'Start of day',
  `ts_day_end` time NOT NULL COMMENT 'End of day',
  `ts_interval` bigint(20) unsigned NOT NULL COMMENT 'Time (in seconds)',
  PRIMARY KEY (`ts_y_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `time_slots` (`ts_y_id`, `ts_day_start`, `ts_day_end`, `ts_interval`) VALUES
(5,	'08:30:00',	'16:30:00',	900),
(7,	'08:30:00',	'16:30:00',	900);

CREATE TABLE `u2d` (
  `u2d_u_id` int(11) unsigned NOT NULL,
  `u2d_d_id` int(11) unsigned NOT NULL,
  KEY `department_id` (`u2d_d_id`),
  KEY `assignment` (`u2d_u_id`,`u2d_d_id`),
  KEY `user_id` (`u2d_u_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Maps a user to multiple departments';

INSERT INTO `u2d` (`u2d_u_id`, `u2d_d_id`) VALUES
(19,	2),
(19,	4),
(22,	2),
(22,	12),
(121,	2),
(121,	3),
(121,	5),
(121,	12),
(127,	1),
(127,	2),
(127,	3),
(130,	1),
(130,	6),
(130,	7),
(132,	3);

CREATE TABLE `users` (
  `u_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `u_g_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Group that the user is a member of',
  `u_enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Boolean 1 or 0',
  `u_username` varchar(104) NOT NULL,
  `u_email` varchar(255) DEFAULT NULL,
  `u_password` varchar(100) DEFAULT NULL COMMENT 'Hash of password',
  `u_display` varchar(64) DEFAULT NULL,
  `u_last_login` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date the user last logged in',
  `u_last_activity` datetime DEFAULT NULL COMMENT 'Last time a page was accessed by this user',
  `u_auth_method` char(10) NOT NULL DEFAULT '0' COMMENT 'Authentication method for user (local/LDAP)',
  `u_created_datetime` datetime NOT NULL COMMENT 'Date the user was created',
  PRIMARY KEY (`u_id`),
  UNIQUE KEY `username` (`u_username`),
  KEY `ldap` (`u_auth_method`),
  KEY `group_id` (`u_g_id`)
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8 COMMENT='Main users table';

INSERT INTO `users` (`u_id`, `u_g_id`, `u_enabled`, `u_username`, `u_email`, `u_password`, `u_display`, `u_last_login`, `u_last_activity`, `u_auth_method`, `u_created_datetime`) VALUES
(1,	1,	1,	'admin',	'craig.rodway@gmail.com',	'$2a$08$hju2g7nuiPJqsyLHsidKguIkKe0nYuWOngQReVAwL43QgwwQifYzC',	'Craig Rodway',	'2013-02-24 20:29:40',	'2012-09-30 15:16:44',	'local',	'0000-00-00 00:00:00'),
(19,	2,	1,	'test.one',	'test.one@bishopbarrington.net',	NULL,	'Mr T One',	'2012-12-09 23:01:01',	'2009-05-19 11:55:14',	'ldap',	'2009-01-14 00:00:00'),
(22,	2,	1,	'test.three',	'test.three@bishopbarrington.net',	NULL,	'Mr T Three',	'2009-01-14 10:57:21',	'0000-00-00 00:00:00',	'ldap',	'2009-01-14 00:00:00'),
(24,	2,	1,	'test.two',	'test.two@bishopbarrington.net',	NULL,	'Mr T Two',	'2009-01-26 16:46:13',	'0000-00-00 00:00:00',	'ldap',	'2009-01-26 00:00:00'),
(130,	2,	1,	'teacher',	'teacher@bishopbarrington.net',	'5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8',	'Guest',	'2012-12-17 16:57:51',	'2011-10-28 17:32:02',	'local',	'2009-12-09 00:00:00'),
(133,	1,	1,	'craig.rodway',	'craig.rodway@bishopbarrington.net',	'$2a$08$bCc9V/tph3E/OZYdeInJLuIYYXATwgcTVivBYSTxoo1.VqObqIbfG',	'Mr Rodway',	'0000-00-00 00:00:00',	NULL,	'local',	'0000-00-00 00:00:00');

CREATE TABLE `users_active` (
  `ua_u_id` int(11) unsigned NOT NULL,
  `ua_token` char(40) NOT NULL,
  `ua_timestamp` datetime NOT NULL,
  UNIQUE KEY `ua_u_id_ua_token` (`ua_u_id`,`ua_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Keep track of current active users';


CREATE TABLE `week_dates` (
  `wd_w_id` int(11) unsigned NOT NULL COMMENT 'Week ID',
  `wd_y_id` int(11) unsigned NOT NULL COMMENT 'Year ID',
  `wd_date` date NOT NULL COMMENT 'Date (Monday)',
  UNIQUE KEY `date` (`wd_date`),
  KEY `week_id` (`wd_w_id`),
  KEY `year_id` (`wd_y_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Start-dates of weeks for timetable';

INSERT INTO `week_dates` (`wd_w_id`, `wd_y_id`, `wd_date`) VALUES
(11,	1,	'2008-09-08'),
(12,	1,	'2008-09-15'),
(11,	1,	'2008-09-22'),
(12,	1,	'2008-09-29'),
(11,	1,	'2008-10-06'),
(12,	1,	'2008-10-13'),
(11,	1,	'2008-10-20'),
(12,	1,	'2008-11-03'),
(11,	1,	'2008-11-10'),
(12,	1,	'2008-11-17'),
(11,	1,	'2008-11-24'),
(12,	1,	'2008-12-01'),
(11,	1,	'2008-12-08'),
(12,	1,	'2008-12-15'),
(11,	1,	'2009-01-05'),
(12,	1,	'2009-01-12'),
(11,	1,	'2009-01-19'),
(12,	1,	'2009-01-26'),
(11,	1,	'2009-02-02'),
(12,	1,	'2009-02-09'),
(11,	1,	'2009-02-23'),
(12,	1,	'2009-03-02'),
(11,	1,	'2009-03-09'),
(12,	1,	'2009-03-16'),
(11,	1,	'2009-03-23'),
(12,	1,	'2009-03-30'),
(11,	1,	'2009-04-20'),
(12,	1,	'2009-04-27'),
(11,	1,	'2009-05-04'),
(12,	1,	'2009-05-11'),
(11,	1,	'2009-05-18'),
(12,	1,	'2009-06-01'),
(11,	1,	'2009-06-08'),
(12,	1,	'2009-06-15'),
(11,	1,	'2009-06-22'),
(12,	1,	'2009-06-29'),
(11,	1,	'2009-07-06'),
(12,	1,	'2009-07-13'),
(11,	1,	'2009-07-20'),
(13,	5,	'2009-09-07'),
(14,	5,	'2009-09-14'),
(13,	5,	'2009-09-21'),
(14,	5,	'2009-09-28'),
(13,	5,	'2009-10-05'),
(14,	5,	'2009-10-12'),
(13,	5,	'2009-10-19'),
(14,	5,	'2009-11-02'),
(13,	5,	'2009-11-09'),
(14,	5,	'2009-11-16'),
(13,	5,	'2009-11-23'),
(14,	5,	'2009-11-30'),
(13,	5,	'2009-12-07'),
(14,	5,	'2009-12-14'),
(13,	5,	'2010-01-04'),
(14,	5,	'2010-01-11'),
(13,	5,	'2010-01-18'),
(14,	5,	'2010-01-25'),
(13,	5,	'2010-02-01'),
(14,	5,	'2010-02-08'),
(13,	5,	'2010-02-22'),
(14,	5,	'2010-03-01'),
(13,	5,	'2010-03-08'),
(14,	5,	'2010-03-15'),
(13,	5,	'2010-03-22'),
(14,	5,	'2010-03-29'),
(13,	5,	'2010-04-19'),
(14,	5,	'2010-04-26'),
(13,	5,	'2010-05-03'),
(14,	5,	'2010-05-10'),
(13,	5,	'2010-05-17'),
(14,	5,	'2010-05-24'),
(13,	5,	'2010-06-07'),
(14,	5,	'2010-06-14'),
(13,	5,	'2010-06-21'),
(14,	5,	'2010-06-28'),
(13,	5,	'2010-07-05'),
(14,	5,	'2010-07-12'),
(13,	5,	'2010-07-19'),
(15,	7,	'2010-09-06'),
(16,	7,	'2010-09-13'),
(15,	7,	'2010-09-20'),
(16,	7,	'2010-09-27'),
(15,	7,	'2010-10-04'),
(16,	7,	'2010-10-11'),
(15,	7,	'2010-10-18'),
(16,	7,	'2010-10-25'),
(15,	7,	'2010-11-01'),
(16,	7,	'2010-11-08'),
(15,	7,	'2010-11-15'),
(16,	7,	'2010-11-22'),
(15,	7,	'2010-11-29'),
(16,	7,	'2010-12-06'),
(15,	7,	'2010-12-13'),
(16,	7,	'2010-12-20'),
(15,	7,	'2010-12-27'),
(16,	7,	'2011-01-03'),
(15,	7,	'2011-01-10'),
(16,	7,	'2011-01-17'),
(15,	7,	'2011-01-24'),
(16,	7,	'2011-01-31'),
(15,	7,	'2011-02-07'),
(16,	7,	'2011-02-14'),
(15,	7,	'2011-02-21'),
(16,	7,	'2011-02-28'),
(15,	7,	'2011-03-07'),
(16,	7,	'2011-03-14'),
(15,	7,	'2011-03-21'),
(16,	7,	'2011-03-28'),
(15,	7,	'2011-04-04'),
(16,	7,	'2011-04-11'),
(15,	7,	'2011-04-18'),
(16,	7,	'2011-04-25'),
(15,	7,	'2011-05-02'),
(16,	7,	'2011-05-09'),
(15,	7,	'2011-05-16'),
(16,	7,	'2011-05-23'),
(15,	7,	'2011-05-30'),
(16,	7,	'2011-06-06'),
(15,	7,	'2011-06-13'),
(16,	7,	'2011-06-20'),
(15,	7,	'2011-06-27'),
(16,	7,	'2011-07-04'),
(15,	7,	'2011-07-11'),
(16,	7,	'2011-07-18'),
(15,	7,	'2011-07-25');

CREATE TABLE `weeks` (
  `w_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `w_y_id` int(10) unsigned NOT NULL,
  `w_name` varchar(20) NOT NULL,
  `w_colour` char(7) DEFAULT NULL COMMENT 'Hex colour value including hash',
  `w_created_datetime` datetime DEFAULT NULL,
  `w_created_u_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`w_id`),
  KEY `year_id` (`w_y_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='Week definitions for timetable weeks';

INSERT INTO `weeks` (`w_id`, `w_y_id`, `w_name`, `w_colour`, `w_created_datetime`, `w_created_u_id`) VALUES
(11,	1,	'Red Week',	'#EF2929',	'2009-01-25 00:00:00',	NULL),
(12,	1,	'Blue Week',	'#3465A4',	'2009-01-25 00:00:00',	NULL),
(13,	5,	'Red Week',	'#CC0000',	'2009-11-01 00:00:00',	NULL),
(14,	5,	'Blue Week',	'#3465A4',	'2009-11-01 00:00:00',	NULL),
(15,	7,	'Red Week',	'#CC0000',	'2011-01-01 00:00:00',	NULL),
(16,	7,	'Blue Week',	'#729FCF',	'2011-01-01 00:00:00',	NULL);

CREATE TABLE `years` (
  `y_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `y_s_id` int(11) unsigned NOT NULL,
  `y_date_start` date NOT NULL,
  `y_date_end` date NOT NULL,
  `y_name` varchar(20) NOT NULL,
  `y_current` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`y_id`),
  UNIQUE KEY `active` (`y_current`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='Academic year definitions';

INSERT INTO `years` (`y_id`, `y_s_id`, `y_date_start`, `y_date_end`, `y_name`, `y_current`) VALUES
(1,	1,	'2008-09-08',	'2009-07-23',	'2008 - 2009',	NULL),
(5,	1,	'2009-09-07',	'2010-07-23',	'2009 - 2010',	NULL),
(7,	1,	'2010-09-07',	'2011-07-22',	'2010 - 2011',	NULL),
(12,	1,	'2011-09-07',	'2012-07-20',	'2011 - 2012',	1);

CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_r2e` AS select `r2d`.`r2d_r_id` AS `r_id`,`r2d`.`r2d_d_id` AS `e_id`,'D' AS `e_type` from `r2d` union select `r2g`.`r2g_r_id` AS `r_id`,`r2g`.`r2g_g_id` AS `e_id`,'G' AS `e_type` from `r2g` union select `r2u`.`r2u_r_id` AS `r_id`,`r2u`.`r2u_u_id` AS `e_id`,'U' AS `e_type` from `r2u`;

-- 2013-03-16 19:38:07
