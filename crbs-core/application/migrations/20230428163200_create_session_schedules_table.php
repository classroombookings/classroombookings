<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_session_schedules_table extends CI_Migration
{


	public function up()
	{
		$fields = [
			'session_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
			],
			'room_group_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'schedule_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
		];

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key(['session_id', 'room_group_id'], TRUE);

		$this->dbforge->create_table('session_schedules', TRUE, array('ENGINE' => 'InnoDB'));

		$sql = "ALTER TABLE `session_schedules`
				ADD CONSTRAINT `fk_session_schedules_session`
				FOREIGN KEY (`session_id`)
				REFERENCES `sessions` (`session_id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE";
		$this->db->query($sql);

		$sql = "ALTER TABLE `session_schedules`
				ADD CONSTRAINT `fk_session_schedules_room_group`
				FOREIGN KEY (`room_group_id`)
				REFERENCES `room_groups` (`room_group_id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE";
		$this->db->query($sql);

		$sql = "ALTER TABLE `session_schedules`
				ADD CONSTRAINT `fk_session_schedules_schedule`
				FOREIGN KEY (`schedule_id`)
				REFERENCES `schedules` (`schedule_id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE";
		$this->db->query($sql);

	}


	public function down()
	{
	}


}
