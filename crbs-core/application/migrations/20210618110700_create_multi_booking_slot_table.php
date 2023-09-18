<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_multi_booking_slot_table extends CI_Migration
{

	public function up()
	{
		$fields = [
			'mbs_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'mb_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'date' => [
				'type' => 'DATE',
				'null' => FALSE,
			],
			'period_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
			'room_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => FALSE,
			],
		];

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('mbs_id', TRUE);

		$this->dbforge->create_table('multi_bookings_slots', TRUE, array('ENGINE' => 'InnoDB'));
	}


	public function down()
	{
	}


}
