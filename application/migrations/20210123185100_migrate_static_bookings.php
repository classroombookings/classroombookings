<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_static_bookings extends CI_Migration
{


	public function up()
	{
		$sql = "INSERT INTO `bookings`
					(`session_id`, `period_id`, `room_id`, `user_id`, `department_id`, `status`, `date`, `notes`, `created_at`)
				SELECT
					d.session_id,
					leg.period_id,
					leg.room_id,
					IF(leg.user_id = 0, NULL, leg.user_id),
					IF(u.department_id = 0, NULL, u.department_id),
					10 AS status,
					leg.date,
					leg.notes,
					NOW()
				FROM
					bookings_legacy leg
				LEFT JOIN dates d ON leg.date = d.date
				LEFT JOIN users u ON leg.user_id = u.user_id
				INNER JOIN rooms r ON leg.room_id = r.room_id
				INNER JOIN periods p ON leg.period_id = p.period_id
				WHERE leg.`date` IS NOT NULL";

		$this->db->query($sql);
	}


	public function down()
	{
	}


}
