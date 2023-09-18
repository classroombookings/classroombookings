<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_sessions_add_default_schedule extends CI_Migration
{


	public function up()
	{
		$fields = [
			'default_schedule_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
				'after' => 'session_id',
			],
		];

		$this->dbforge->add_column('sessions', $fields);

		$sql = "ALTER TABLE `sessions`
				ADD CONSTRAINT `fk_sessions_default_schedule`
				FOREIGN KEY (`default_schedule_id`)
				REFERENCES `schedules` (`schedule_id`)
				ON DELETE SET NULL
				ON UPDATE CASCADE";
		$this->db->query($sql);

		$sql = "UPDATE sessions
				SET default_schedule_id = (SELECT schedule_id FROM schedules ORDER BY schedule_id ASC LIMIT 1)
				WHERE default_schedule_id IS NULL";
		$this->db->query($sql);
	}


	public function down()
	{
	}

}
