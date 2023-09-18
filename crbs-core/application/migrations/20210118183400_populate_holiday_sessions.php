<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Populate_holiday_sessions extends CI_Migration
{


	public function up()
	{
		$sql = 'UPDATE holidays h
				SET session_id = (
					SELECT session_id
					FROM sessions s
					WHERE
					(
						h.date_start >= s.date_start
						AND h.date_end >= s.date_start
						AND h.date_start <= s.date_end
						AND h.date_end <= s.date_end
					)
					OR
					(
						h.date_start >= s.date_start
						OR
						h.date_end >= s.date_start
					)
					LIMIT 1
				) WHERE session_id IS NULL';

		$this->db->query($sql);
		return true;
	}


	public function down()
	{
	}


}
