<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_rooms_add_group extends CI_Migration
{

	public function up()
	{
		$fields = [
			'room_group_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
				'after' => 'room_id',
			],
		];

		$this->dbforge->add_column('rooms', $fields);

		$sql = "ALTER TABLE `rooms`
				ADD CONSTRAINT `fk_rooms_group`
				FOREIGN KEY (`room_group_id`)
				REFERENCES `room_groups` (`room_group_id`)
				ON DELETE SET NULL
				ON UPDATE CASCADE";
		$this->db->query($sql);
	}



	public function down()
	{
	}


}
