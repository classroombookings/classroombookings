<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_legacy_access_control extends CI_Migration
{

	public function up()
	{
		$rooms = $this->get_summary();
		if (empty($rooms)) return;

		$total_no_access = 0;
		$total_all_only = 0;
		$total_dept_only = 0;
		$total = count($rooms);
		foreach ($rooms as $row) {
			switch (true) {
				case ($row['has_any'] == 0 && $row['has_dept'] == 0): $total_no_access++; break;
				case ($row['has_any'] == 1 && $row['has_dept'] == 0): $total_all_only++; break;
				case ($row['has_any'] == 0 && $row['has_dept'] == 1): $total_dept_only++; break;
			}
		}

		if ($total == $total_all_only) {
			// We do not need to make any adjustments.
			// All users can see all rooms - no legacy access control entries that specify departments that need migrating.
			return;
		}

		if ($total_dept_only > 0 || $total_no_access > 0) {
			// 1 or more rooms that either:
			// 	a) have access control for a department with NO access for regular users; or
			// 	b) rooms that have no access control entries at all.
			//
			// For (a) and (b), we need to:
			// - remove the general 'room view' permission from Teacher role
			//
			// Then, for all rooms that has_any=1:
			// - add new acls for each room for the Teacher role with 'room.view', to replicate the old 'any logged-in user'.
			//
			// Then, for all rooms with has_dept=1:
			// - add new acls for each room for the specified department to replicate the old 'department' access.
			//

			$this->remove_teacher_role_room_view();

			$this->maybe_process_any_user($rooms);
			$this->maybe_process_departments();
		}
	}


	public function down()
	{
	}


	private function get_summary()
	{
		$sql = "SELECT
					room_id,
					SUM(IF(ac.actor='A',1,0)) AS has_any,
					SUM(IF(ac.actor='D',1,0)) AS has_dept
				FROM rooms r
				LEFT JOIN access_control ac ON ac.target_id = r.room_id AND ac.target='R'
				GROUP BY r.room_id
				";
		$query = $this->db->query($sql);
		$result = $query->result_array();
		return $result;
	}


	private function remove_teacher_role_room_view()
	{
		// Remove the 'room.view' permission from the new default Teacher role
		$sql = "DELETE FROM auth_roles_permissions
				WHERE role_id = 2
				AND permission_id IN (SELECT permission_id FROM auth_permissions WHERE name IN ('room.view', 'book_single.create'))
				";
		$this->db->query($sql);
	}


	private function maybe_process_any_user(array $rooms)
	{
		$acls = [];

		foreach ($rooms as $room) {

			if ($room['has_any'] < 1) continue;

			$acls[] = [
				'entity_type' => 'room',
				'entity_id' => $room['room_id'],
				'context_type' => 'role',
				'context_id' => 2,
				'permissions' => ['room.view', 'book_single.create'],
			];

		}

		$this->insert_acls($acls);
	}


	private function maybe_process_departments()
	{
		// Get all the rooms + departments that are configured in legacy access control
		$sql = "SELECT target_id AS room_id, actor_id AS department_id
				FROM access_control
				WHERE actor='D'";
		$query = $this->db->query($sql);
		$result = $query->result_array();

		$acls = [];

		foreach ($result as $row) {
			$acls[] = [
				'entity_type' => 'room',
				'entity_id' => $row['room_id'],
				'context_type' => 'department',
				'context_id' => $row['department_id'],
				'permissions' => ['room.view', 'book_single.create'],
			];
		}

		$this->insert_acls($acls);
	}


	private function insert_acls(array $acls)
	{
		if (empty($acls)) return;

		foreach ($acls as $acl) {
			$p = $acl['permissions'];
			unset($acl['permissions']);
			$result = $this->db->insert('auth_acl', $acl);
			if ($result) {
				$acl_id = $this->db->insert_id();
				$sql = "INSERT INTO auth_acl_permissions (acl_id, permission_id)
						SELECT {$acl_id}, permission_id
						FROM auth_permissions
						WHERE name IN ?";
				$this->db->query($sql, [ $p ]);
			}
		}
	}


}
