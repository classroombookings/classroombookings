<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_default_roles extends CI_Migration
{

	public function up()
	{
		$this->do_admin();
		$this->do_teacher();
	}


	public function down()
	{
	}


	private function do_admin()
	{
		$sql = "INSERT INTO `auth_roles`
				(`role_id`, `name`, `description`, `max_active_bookings`, `range_min`, `range_max`, `recur_max_instances`)
				VALUES
				(1, 'Administrator', 'Administrator', NULL, NULL, NULL, NULL)
				";
		$this->db->query($sql);

		$sql = "INSERT INTO auth_roles_permissions (role_id, permission_id)
				SELECT 1, permission_id
				FROM auth_permissions
				";
		$this->db->query($sql);
	}


	private function do_teacher()
	{
		$sql = "INSERT INTO `auth_roles`
				(`role_id`, `name`, `description`, `max_active_bookings`, `range_min`, `range_max`, `recur_max_instances`)
				VALUES
				(2, 'Teacher', 'Teacher', NULL, NULL, NULL, NULL)
				";
		$this->db->query($sql);

		//

		$teacher_permissions = [
			'room.view',
			'book_single.create',
			'book_single.view_other_notes',
			'book_recur.view_other_notes',
		];

		// Migrate global user-visibility settings over to a permissions
		//
		$sql = "SELECT `name`
				FROM `settings`
				WHERE `group` = 'crbs'
				AND `name` IN ('bookings_show_user_recurring', 'bookings_show_user_single')
				AND `value` = 1";
		$query = $this->db->query($sql);

		if ($query->num_rows() > 0) {
			$rows = $query->result_array();
			$settings = array_column($rows, 'name');
			if (in_array('bookings_show_user_single', $settings)) {
				$teacher_permissions[] = 'book_single.view_other_users';
			}
			if (in_array('bookings_show_user_recurring', $settings)) {
				$teacher_permissions[] = 'book_recur.view_other_users';
			}
		}

		$sql = "INSERT INTO auth_roles_permissions (role_id, permission_id)
				SELECT 2, permission_id
				FROM auth_permissions
				WHERE name IN ?";
		$this->db->query($sql, [$teacher_permissions]);
	}


}
