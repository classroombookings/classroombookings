<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_periods_add_schedule extends CI_Migration
{


	public function up()
	{
		$fields = [
			'schedule_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
				'after' => 'period_id',
			],
		];

		$this->dbforge->add_column('periods', $fields);

		$sql = "ALTER TABLE `periods`
				ADD CONSTRAINT `fk_periods_schedule`
				FOREIGN KEY (`schedule_id`)
				REFERENCES `schedules` (`schedule_id`)
				ON DELETE CASCADE
				ON UPDATE CASCADE";
		$this->db->query($sql);

		$sql = "UPDATE periods
				SET schedule_id = 1
				WHERE schedule_id IS NULL";
		$this->db->query($sql);

		$fields = [
			'schedule_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
				'after' => 'period_id',
			],
		];

		$this->dbforge->modify_column('periods', $fields);
	}


	public function down()
	{
	}

}
