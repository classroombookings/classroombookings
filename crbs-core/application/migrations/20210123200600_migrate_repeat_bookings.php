<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_repeat_bookings extends CI_Migration
{


	public function up()
	{
		$sql = "INSERT INTO `bookings_repeat`
					(`session_id`, `period_id`, `room_id`, `user_id`, `department_id`, `week_id`, `weekday`, `notes`, `created_at`)
				SELECT
					d.session_id,
					leg.period_id,
					leg.room_id,
					IF(leg.user_id = 0, NULL, leg.user_id),	/* Ensure user exists or set NULL */
					IF(u.department_id = 0, NULL, u.department_id),
					leg.week_id,
					IF(leg.day_num = 0, 7, leg.day_num), /* Migrate from old date('w') weekdays to date('N') weekdays */
					leg.notes,
					NOW()
				FROM
					bookings_legacy leg
				LEFT JOIN users u ON leg.user_id = u.user_id
				INNER JOIN weeks w ON leg.week_id = w.week_id
				INNER JOIN rooms r ON leg.room_id = r.room_id
				INNER JOIN periods p ON leg.period_id = p.period_id
				INNER JOIN weekdates wd ON leg.week_id = wd.week_id
				INNER JOIN dates d ON wd.date = d.date
				WHERE leg.date IS NULL
				AND leg.day_num IS NOT NULL
				GROUP BY leg.booking_id";

		$this->db->query($sql);
	}


	public function down()
	{
	}



}
