<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_session_schedules_data extends CI_Migration
{


	public function up()
	{
		$sql = "INSERT INTO session_schedules (session_id, room_group_id, schedule_id)
				SELECT
					s.session_id,
					g.room_group_id,
					s.default_schedule_id
				FROM sessions s
				CROSS JOIN room_groups g
				WHERE s.default_schedule_id IS NOT NULL
				ORDER BY session_id ASC, room_group_id ASC
				";
		$this->db->query($sql);
	}


	public function down()
	{
	}

}
