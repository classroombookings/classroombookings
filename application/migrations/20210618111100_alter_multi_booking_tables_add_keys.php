<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_multi_booking_tables_add_keys extends CI_Migration
{


	public function up()
	{
		$this->add_keys();
	}

	private function add_keys()
	{
		$keys = [

			[
				'id' => 'fk_mb_user',
				'main_table' => 'multi_bookings',
				'main_col' => 'user_id',
				'ref_table' => 'users',
				'ref_col' => 'user_id',
				'delete' => 'CASCADE',
			],

			[
				'id' => 'fk_mbs_mb',
				'main_table' => 'multi_bookings_slots',
				'main_col' => 'mb_id',
				'ref_table' => 'multi_bookings',
				'ref_col' => 'mb_id',
				'delete' => 'CASCADE',
			],

			[
				'id' => 'fk_mbs_period',
				'main_table' => 'multi_bookings_slots',
				'main_col' => 'period_id',
				'ref_table' => 'periods',
				'ref_col' => 'period_id',
				'delete' => 'CASCADE',
			],

			[
				'id' => 'fk_mbs_room',
				'main_table' => 'multi_bookings_slots',
				'main_col' => 'room_id',
				'ref_table' => 'rooms',
				'ref_col' => 'room_id',
				'delete' => 'CASCADE',
			],

		];

		foreach ($keys as $key) {
			extract($key);
			$sql = "ALTER TABLE `{$main_table}`
					ADD CONSTRAINT `{$id}`
					FOREIGN KEY (`{$main_col}`)
					REFERENCES `{$ref_table}` (`{$ref_col}`)
					ON DELETE {$delete}
					ON UPDATE CASCADE";
			$this->db->query($sql);
		}

	}


	public function down()
	{
	}

}
