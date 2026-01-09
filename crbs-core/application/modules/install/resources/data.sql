SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

INSERT INTO `auth_permissions` (`permission_id`, `name`) VALUES
(24,	'book_recur.cancel_other_booking'),
(22,	'book_recur.create'),
(23,	'book_recur.edit_other_booking'),
(26,	'book_recur.set_department'),
(25,	'book_recur.set_user'),
(28,	'book_recur.view_other_notes'),
(27,	'book_recur.view_other_users'),
(17,	'book_single.cancel_other_booking'),
(15,	'book_single.create'),
(16,	'book_single.edit_other_booking'),
(19,	'book_single.set_department'),
(18,	'book_single.set_user'),
(21,	'book_single.view_other_notes'),
(20,	'book_single.view_other_users'),
(14,	'room.view'),
(4,	'setup.authentication'),
(5,	'setup.departments'),
(6,	'setup.roles'),
(7,	'setup.rooms'),
(8,	'setup.rooms_acl'),
(9,	'setup.schedules'),
(10,	'setup.sessions'),
(11,	'setup.settings'),
(12,	'setup.timetable_weeks'),
(13,	'setup.users'),
(1,	'system.bypass_maintenance_mode'),
(2,	'system.export_bookings'),
(3,	'system.view_all_sessions');

INSERT INTO `auth_roles` (`role_id`, `name`, `description`, `max_active_bookings`, `range_min`, `range_max`, `recur_max_instances`) VALUES
(1,	'Administrator',	'Administrator',	NULL,	NULL,	NULL,	NULL),
(2,	'Teacher',	'Teacher',	NULL,	NULL,	NULL,	NULL);

INSERT INTO `auth_roles_permissions` (`role_id`, `permission_id`) VALUES
(1,	1),
(1,	2),
(1,	3),
(1,	4),
(1,	5),
(1,	6),
(1,	7),
(1,	8),
(1,	9),
(1,	10),
(1,	11),
(1,	12),
(1,	13),
(1,	14),
(2,	14),
(1,	15),
(2,	15),
(1,	16),
(1,	17),
(1,	18),
(1,	19),
(1,	20),
(1,	21),
(2,	21),
(1,	22),
(1,	23),
(1,	24),
(1,	25),
(1,	26),
(1,	27),
(1,	28),
(2,	28);

INSERT INTO `room_groups` (`room_group_id`, `pos`, `name`, `description`) VALUES
(1,	0,	'All',	NULL);

INSERT INTO `schedules` (`schedule_id`, `type`, `name`, `description`) VALUES
(1,	'periods',	'Periods',	NULL);

INSERT INTO `weeks` (`week_id`, `name`, `fgcol`, `bgcol`, `icon`) VALUES
(1,	'Timetable',	'',	'71AAE3',	NULL);

-- Insert session

SET @dt = CURDATE();

SET @start_year = CASE
	WHEN MONTH(@dt) < 9 THEN YEAR(DATE_SUB(@dt, INTERVAL 1 YEAR))
	WHEN MONTH(@dt) >= 9 THEN YEAR(@dt)
END;

SET @end_year = CASE
	WHEN MONTH(@dt) < 9 THEN YEAR(@dt)
	WHEN MONTH(@dt) >= 9 THEN YEAR(DATE_ADD(@dt, INTERVAL 1 YEAR))
END;

SET @name = CONCAT_WS(' - ', @start_year, @end_year);

SET @start_date = CONCAT_WS('-', @start_year, '09', '01');
SET @end_date = CONCAT_WS('-', @end_year, '07','31');

INSERT INTO sessions SET
	session_id = 1,
	default_schedule_id = 1,
	name = @name,
	date_start = @start_date,
	date_end = @end_date,
	is_current = 1,
	is_selectable = 1;

-- Insert dates

SELECT date_start, date_end INTO @start, @end FROM sessions WHERE session_id = 1 LIMIT 1;

INSERT INTO dates (`date`,`weekday`,`session_id`,`week_id`)
SELECT
	`date`,
	ELT(DAYOFWEEK(`date`), '7', '1', '2', '3', '4', '5', '6') AS weekday,
	1,
	1
FROM (
	SELECT ADDDATE(@start, INTERVAL @i:=@i+1 DAY) AS 'date'
	FROM (
		SELECT a.a
		FROM (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS a
		CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS b
		CROSS JOIN (SELECT 0 AS a UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS c
	) a
	JOIN (SELECT @i := -1) r1
	WHERE
	@i < DATEDIFF(@end, @start)
) all_dates;
