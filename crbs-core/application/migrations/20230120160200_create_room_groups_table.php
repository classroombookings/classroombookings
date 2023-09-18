<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_room_groups_table extends CI_Migration
{

	public function up()
	{
		$fields = [
			'room_group_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'pos' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'default' => 0,
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
		$this->dbforge->add_key('room_group_id', TRUE);

		$this->dbforge->create_table('room_groups', TRUE, array('ENGINE' => 'InnoDB'));
	}


	public function down()
	{
	}


}
