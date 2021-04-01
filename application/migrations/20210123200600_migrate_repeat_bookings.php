<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_repeat_bookings extends CI_Migration
{


	public function up()
	{
		$sql = "INSERT INTO `bookings_repeat`
					(`period_id`, `room_id`, `user_id`, `department_id`, `week_id`, `weekday`, `notes`)
				SELECT
					`period_id`,
					`room_id`,
					`user_id`,
					IF(department_id = 0, NULL, department_id),
					`week_id`,
					`day_num`,
					`notes`
				FROM
					bookings_legacy legacy
				LEFT JOIN users USING (`user_id`)
				INNER JOIN weeks USING (`week_id`)
				WHERE `legacy`.`date` IS NULL
				AND `legacy`.`day_num` IS NOT NULL";

		$this->db->query($sql);

		$sql = "UPDATE `bookings_repeat`
				SET `session_id` = (SELECT `session_id` FROM `sessions` WHERE is_current = 1 LIMIT 1)";

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
