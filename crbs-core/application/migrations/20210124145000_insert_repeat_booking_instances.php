<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_repeat_booking_instances extends CI_Migration
{


	public function up()
	{
		$sql = "INSERT INTO bookings (
					`repeat_id`,
					`session_id`,
					`period_id`,
					`room_id`,
					`user_id`,
					`department_id`,
					`status`,
					`date`,
					`notes`
				)
				SELECT
					`r`.`repeat_id`,
					`r`.`session_id`,
					`r`.`period_id`,
					`r`.`room_id`,
					`r`.`user_id`,
					`r`.`department_id`,
					10 AS `status`,
					`dates`.`date`,
					`r`.`notes`
				FROM bookings_repeat r
				RIGHT JOIN dates ON `r`.`session_id` = `dates`.`session_id`
				WHERE 1=1
				AND `r`.`week_id` = `dates`.`week_id`
				AND `r`.`weekday` = `dates`.`weekday`";

		$this->db->query($sql);
	}


	public function down()
	{
	}


}
