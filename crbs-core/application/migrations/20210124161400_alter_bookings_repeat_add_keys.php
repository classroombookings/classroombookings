<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_bookings_repeat_add_keys extends CI_Migration
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
			[
				'field' => 'week_id',
				'table' => 'weeks',
			],
		];

		foreach ($fields as $config) {
			extract($config);
			$sql = "UPDATE bookings_repeat SET `{$field}` = NULL
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

			'fk_bookings_repeat_session' => [
				'bookings_repeat' => 'session_id',
				'table' => 'sessions',
				'foreign' => 'session_id',
				'delete' => 'CASCADE',
			],

			'fk_bookings_repeat_period' => [
				'bookings_repeat' => 'period_id',
				'table' => 'periods',
				'foreign' => 'period_id',
				'delete' => 'CASCADE',
			],

			'fk_bookings_repeat_room' => [
				'bookings_repeat' => 'room_id',
				'table' => 'rooms',
				'foreign' => 'room_id',
				'delete' => 'CASCADE',
			],

			'fk_bookings_repeat_user' => [
				'bookings_repeat' => 'user_id',
				'table' => 'users',
				'foreign' => 'user_id',
				'delete' => 'SET NULL',
			],

			'fk_bookings_repeat_department' => [
				'bookings_repeat' => 'department_id',
				'table' => 'departments',
				'foreign' => 'department_id',
				'delete' => 'SET NULL',
			],

			'fk_bookings_repeat_week' => [
				'bookings_repeat' => 'week_id',
				'table' => 'weeks',
				'foreign' => 'week_id',
				'delete' => 'CASCADE',
			],

			'fk_bookings_repeat_created_user' => [
				'bookings_repeat' => 'created_by',
				'table' => 'users',
				'foreign' => 'user_id',
				'delete' => 'SET NULL',
			],

			'fk_bookings_repeat_updated_user' => [
				'bookings_repeat' => 'updated_by',
				'table' => 'users',
				'foreign' => 'user_id',
				'delete' => 'SET NULL',
			],

		];

		foreach ($keys as $id => $key) {
			extract($key);
			$sql = "ALTER TABLE `bookings_repeat`
					ADD CONSTRAINT `{$id}`
					FOREIGN KEY (`{$bookings_repeat}`)
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
