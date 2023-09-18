<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_bookings_add_keys extends CI_Migration
{


	public function up()
	{
		$this->set_orphans();
		$this->add_keys();
	}


	private function set_orphans()
	{
		// Update all foreign key values to NULL where the relation row does not exist
		$fields = [
			[
				'field' => 'session_id',
				'table' => 'sessions',
			],
			[
				'field' => 'period_id',
				'table' => 'periods',
			],
			[
				'field' => 'room_id',
				'table' => 'rooms',
			],
			[
				'field' => 'user_id',
				'table' => 'users',
			],
			[
				'field' => 'department_id',
				'table' => 'departments',
			],
		];

		foreach ($fields as $config) {
			extract($config);
			$sql = "UPDATE bookings SET `{$field}` = NULL
					WHERE `{$field}` IS NOT NULL
					AND `{$field}` NOT IN (
						SELECT DISTINCT(`{$field}`) FROM `{$table}`
					)";
			$this->db->query($sql);
		}
	}


	private function add_keys()
	{
		$keys = [

			'fk_bookings_repeat' => [
				'bookings' => 'repeat_id',
				'table' => 'bookings_repeat',
				'foreign' => 'repeat_id',
				'delete' => 'CASCADE',
			],

			'fk_bookings_session' => [
				'bookings' => 'session_id',
				'table' => 'sessions',
				'foreign' => 'session_id',
				'delete' => 'CASCADE',
			],

			'fk_bookings_period' => [
				'bookings' => 'period_id',
				'table' => 'periods',
				'foreign' => 'period_id',
				'delete' => 'CASCADE',
			],

			'fk_bookings_room' => [
				'bookings' => 'room_id',
				'table' => 'rooms',
				'foreign' => 'room_id',
				'delete' => 'CASCADE',
			],

			'fk_bookings_user' => [
				'bookings' => 'user_id',
				'table' => 'users',
				'foreign' => 'user_id',
				'delete' => 'SET NULL'
			],

			'fk_bookings_department' => [
				'bookings' => 'department_id',
				'table' => 'departments',
				'foreign' => 'department_id',
				'delete' => 'SET NULL'
			],

			'fk_bookings_created_user' => [
				'bookings' => 'created_by',
				'table' => 'users',
				'foreign' => 'user_id',
				'delete' => 'SET NULL',
			],

			'fk_bookings_updated_user' => [
				'bookings' => 'updated_by',
				'table' => 'users',
				'foreign' => 'user_id',
				'delete' => 'SET NULL',
			],

		];

		foreach ($keys as $id => $key) {
			extract($key);
			$sql = "ALTER TABLE `bookings`
					ADD CONSTRAINT `{$id}`
					FOREIGN KEY (`{$bookings}`)
					REFERENCES `{$table}` (`{$foreign}`)
					ON DELETE {$delete}
					ON UPDATE CASCADE";
			$this->db->query($sql);
		}
	}


	public function down()
	{
	}


}
