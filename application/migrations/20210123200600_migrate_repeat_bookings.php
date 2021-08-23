<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_repeat_bookings extends CI_Migration
{


	public function up()
	{
		$sql = "INSERT INTO `bookings_repeat`
					(`period_id`, `room_id`, `user_id`, `department_id`, `week_id`, `weekday`, `notes`)
				SELECT
					leg.period_id,
					leg.room_id,
					leg.user_id,
					IF(u.department_id = 0, NULL, u.department_id),
					leg.week_id,
					leg.day_num,
					leg.notes
				FROM
					bookings_legacy leg
				LEFT JOIN users u ON leg.user_id = u.user_id
				INNER JOIN weeks w ON leg.week_id = w.week_id
				INNER JOIN rooms r ON leg.room_id = r.room_id
				INNER JOIN periods p ON leg.period_id = p.period_id
				WHERE leg.date IS NULL
				AND leg.day_num IS NOT NULL";

		$this->db->query($sql);

		$sql = "UPDATE `bookings_repeat`
				SET `session_id` = (
					SELECT session_id
					FROM dates d
					INNER JOIN weekdates wd ON d.date = wd.date
					WHERE wd.week_id = bookings_repeat.week_id
					LIMIT 1
				)";

		$this->db->query($sql);

		// Move from old date('w') weekdays to date('N') weekdays.
		// Only change is sunday, from 0 to 7.
		$sql = "UPDATE `bookings_repeat` SET `weekday` = 7 WHERE `weekday` = 0";
		$this->db->query($sql);
	}


	public function down()
	{
	}



}
