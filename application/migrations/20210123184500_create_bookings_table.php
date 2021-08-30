<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_bookings_table extends CI_Migration
{


	public function up()
	{
		$fields = [
			'booking_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'repeat_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'session_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
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
			'user_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'department_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'date' => [
				'type' => 'DATE',
				'null' => FALSE,
			],
			'status' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => 10,
			],
			'notes' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			],
			'cancel_reason' => [
				'type' => 'TEXT',
				'null' => TRUE,
			],
			'cancelled_at' => [
				'type' => 'DATETIME',
				'null' => TRUE,
			],
			'cancelled_by' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => TRUE,
			],
			'created_by' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => TRUE,
			],
			'updated_by' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			],
		];

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('booking_id', TRUE);

		$this->dbforge->create_table('bookings', TRUE, array('ENGINE' => 'InnoDB'));

		$sql = "ALTER TABLE `bookings` ADD INDEX `idx_bookings_date` (`date`)";
		$this->db->query($sql);
	}


	public function down()
	{
	}


}
