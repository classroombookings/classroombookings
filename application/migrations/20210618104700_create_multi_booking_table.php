<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_multi_booking_table extends CI_Migration
{

	public function up()
	{
		$fields = [
			'mb_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'user_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'session_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'week_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => TRUE,
			],
			'type' => [
				'type' => 'VARCHAR',
				'constraint' => 32,
				'null' => TRUE,
			],
			'booking_user_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'booking_department_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'booking_notes' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			],
		];

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('mb_id', TRUE);

		$this->dbforge->create_table('multi_bookings', TRUE, array('ENGINE' => 'InnoDB'));
	}


	public function down()
	{
	}


}
