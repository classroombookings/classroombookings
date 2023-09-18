<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_schedules_table extends CI_Migration
{


	public function up()
	{
		$fields = [
			'schedule_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'type' => [
				'type' => 'VARCHAR',
				'constraint' => 20,
				'default' => 'periods',
				'null' => FALSE,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 32,
				'null' => FALSE,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
		];

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('schedule_id', TRUE);

		$this->dbforge->create_table('schedules', TRUE, array('ENGINE' => 'InnoDB'));
	}


	public function down()
	{
	}

}
