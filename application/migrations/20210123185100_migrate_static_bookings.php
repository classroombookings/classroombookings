<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_static_bookings extends CI_Migration
{


	public function up()
	{
		$sql = "INSERT INTO `bookings`
					(`session_id`, `period_id`, `room_id`, `user_id`, `department_id`, `status`, `date`, `notes`)
				SELECT
					`dates`.`session_id`,
					`period_id`,
					`room_id`,
					`user_id`,
					IF(department_id = 0, NULL, department_id),
					10 AS status,
					`date`,
					`notes`
				FROM
					bookings_legacy legacy
				LEFT JOIN dates USING (`date`)
				LEFT JOIN users USING (`user_id`)
				WHERE `legacy`.`date` IS NOT NULL";

		$this->db->query($sql);
	}


	public function down()
	{
	}


}
