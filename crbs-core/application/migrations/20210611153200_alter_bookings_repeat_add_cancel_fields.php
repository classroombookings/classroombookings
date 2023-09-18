<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_bookings_repeat_add_cancel_fields extends CI_Migration
{


	public function up()
	{
		$fields = [
			'status' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => 10,
				'after' => 'weekday',
			],
			'cancel_reason' => [
				'type' => 'TEXT',
				'null' => TRUE,
				'after' => 'notes',
			],
			'cancelled_at' => [
				'type' => 'DATETIME',
				'null' => TRUE,
				'after' => 'cancel_reason',
			],
			'cancelled_by' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
				'after' => 'cancelled_at',
			],
		];

		$this->dbforge->add_column('bookings_repeat', $fields);
	}


	public function down()
	{
	}


}
