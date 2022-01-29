<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_sessions_table extends CI_Migration
{


	public function up()
	{
		$this->create_table();
	}


	public function down()
	{
	}


	private function create_table()
	{
		$fields = [
			'session_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
				'auto_increment' => TRUE,
			],
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'null' => FALSE,
			],
			'date_start' => [
				'type' => 'DATE',
				'null' => FALSE,
			],
			'date_end' => [
				'type' => 'DATE',
				'null' => FALSE,
			],
			'is_current' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'default' => 0,
			],
		];

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('session_id', TRUE);
		$this->dbforge->create_table('sessions', TRUE, ['ENGINE' => 'InnoDB']);
	}


}
