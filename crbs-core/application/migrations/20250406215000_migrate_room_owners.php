<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_room_owners extends CI_Migration
{

	public function up()
	{
		$rooms = $this->get_rooms_with_owners();
		if (empty($rooms)) return;

		foreach ($rooms as $room) {
			$this->create_owner_acl($room);
		}
	}


	public function down()
	{
	}


	private function get_rooms_with_owners()
	{
		$sql = "SELECT room_id, user_id
				FROM rooms r
				INNER JOIN users u USING (user_id)
				WHERE r.user_id IS NOT NULL
				";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}


	private function create_owner_acl(array $room)
	{
		$acl = [
			'entity_type' => 'room',
			'entity_id' => $room['room_id'],
			'context_type' => 'user',
			'context_id' => $room['user_id'],
		];

		$result = $this->db->insert('auth_acl', $acl);

		if ($result) {
			$acl_id = $this->db->insert_id();
			$sql = "INSERT INTO auth_acl_permissions (acl_id, permission_id)
					SELECT {$acl_id}, permission_id
					FROM auth_permissions
					WHERE name = 'book_single.cancel_other_booking'
					LIMIT 1";
			$this->db->query($sql);
		}
	}


}
