<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_default_room_group extends CI_Migration
{

	public function up()
	{
		$this->db->insert('room_groups', [
			'pos' => '0',
			'name' => 'All',
			'description' => null,
		]);

		$sql = "UPDATE rooms
				SET room_group_id = (
					SELECT room_group_id
					FROM room_groups
					WHERE name='All'
					LIMIT 1
				)
				WHERE room_group_id IS NULL";
		$this->db->query($sql);
	}



	public function down()
	{
	}


}
