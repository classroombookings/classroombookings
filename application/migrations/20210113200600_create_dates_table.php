<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_dates_table extends CI_Migration
{


	public function up()
	{
		$fields = [
			'date' => [
				'type' => 'DATE',
				'null' => FALSE,
			],
			'weekday' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'null' => FALSE,
			],
			'session_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'week_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'holiday_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
		];

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('date', TRUE);

		$this->dbforge->create_table('dates', TRUE, ['ENGINE' => 'InnoDB']);
	}


	public function down()
	{
	}


}
