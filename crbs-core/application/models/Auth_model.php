<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Auth_model extends CI_Model
{

	private array $_cache = [];


	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Get an individual ACL which will be for a room/room group and specific user/role/department.
	 *
	 */
	public function get_acl(int $acl_id)
	{
		$sql = "
			SELECT a.*,
			CASE a.entity_type
				WHEN 'room' THEN erm.name
				WHEN 'room_group' THEN erg.name
			END AS entity_label,
			CASE a.context_type
				WHEN 'user' THEN cu.username
				WHEN 'role' THEN cr.name
				WHEN 'department' THEN cd.name
			END AS context_label
			FROM auth_acl a
			LEFT JOIN users cu ON a.context_type = 'user' AND a.context_id = cu.user_id
			LEFT JOIN auth_roles cr ON a.context_type = 'role' AND a.context_id = cr.role_id
			LEFT JOIN departments cd ON a.context_type = 'department' AND a.context_id = cd.department_id
			LEFT JOIN rooms erm ON a.entity_type = 'room' AND a.entity_id = erm.room_id
			LEFT JOIN room_groups erg ON a.entity_type = 'room_group' AND a.entity_id = erg.room_group_id
			WHERE a.acl_id = ?
		";

		$query = $this->db->query($sql, [$acl_id]);
		$acl = $query->row();

		if (empty($acl)) return null;

		// Load permissions
		$sql = "SELECT p.permission_id, p.name
				FROM auth_permissions p
				INNER JOIN auth_acl_permissions aclp USING (permission_id)
				WHERE aclp.acl_id = ?";
		$query = $this->db->query($sql, [$acl_id]);
		$permissions = [];
		if ($query->num_rows() > 0) {
			foreach ($query->result_array() as $row) {
				$permissions[ $row['permission_id'] ] = $row['name'];
			}
		}

		$acl->permissions = $permissions;

		return $acl;
	}


	public function insert_acl(array $acl_data, array $permissions)
	{
		$this->db->trans_start();

		$res = $this->db->insert('auth_acl', $acl_data);
		if ($res) {
			$id = $this->db->insert_id();
			$this->set_acl_permissions($id, $permissions);
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			return false;
		}

		return $id;
	}


	public function set_acl_permissions(int $acl_id, array $permissions)
	{
		$sql = "DELETE FROM auth_acl_permissions WHERE acl_id = ?";
		$this->db->query($sql, $acl_id);

		$permissions = array_filter($permissions);
		$permissions = array_filter($permissions, strlen(...));

		if (empty($permissions)) return true;

		$rows = [];
		foreach ($permissions as $permission_id) {
			$rows[] = [
				'acl_id' => $acl_id,
				'permission_id' => $permission_id,
			];
		}
		if ( ! empty($rows)) {
			$this->db->insert_batch('auth_acl_permissions', $rows);
		}

		return true;
	}


	public function delete_acl(int $acl_id)
	{
		$sql = "DELETE FROM auth_acl_permissions WHERE acl_id = ?";
		$res_permissions = $this->db->query($sql, $acl_id);

		$sql = "DELETE FROM auth_acl WHERE acl_id = ?";
		$res_acl = $this->db->query($sql, $acl_id);

		return $res_permissions && $res_acl;
	}


	/**
	 * Get all permissions defined at the user's role
	 *
	 */
	public function get_permissions(int $user_id)
	{
		$this->db->reset_query();
		$this->db->select('p.*');
		$this->db->from('permissions p');
		$this->db->join('roles_permissions rp', 'p.permission_id = rp.permission_id', 'inner');
		$this->db->join('roles r', 'rp.role_id = r.role_id', 'inner');
		$this->db->join('users u', 'r.role_id = u.role_id', 'inner');
		$this->db->where('u.user_id', $user_id);

		$query = $this->db->get();
		$result = $query->result_array();
		return results_to_assoc($result, 'permission_id', 'name');
	}


	public function user_has_permission(int $user_id, string $permission)
	{
		$this->db->reset_query();
		// $this->db->select('ap.name');
		$this->db->from('auth_permissions ap');
		$this->db->join('auth_roles_permissions arp', 'ap.permission_id = arp.permission_id', 'inner');
		$this->db->join('auth_roles ar', 'arp.role_id = ar.role_id', 'inner');
		$this->db->join('users u', 'ar.role_id = u.role_id', 'inner');
		$this->db->where('u.user_id', $user_id);
		$this->db->where('ap.name', $permission);

		// $query = $this->db->get();
		return $this->db->count_all_results() == 1;
		// return $query->num_rows() == 1;
	}


	public function get_acl_for_entity(string $type, int $id)
	{
		$type_val = $this->db->escape($type);
		$id_val = $this->db->escape($id);

		$sql = "
			SELECT
				a.acl_id,
				a.context_type,
				a.context_id,
				CASE
					WHEN u.displayname IS NULL OR u.displayname = '' THEN u.username
					ELSE CONCAT(u.username, ' (', u.displayname, ')')
				END AS label
			FROM auth_acl a
			INNER JOIN users u ON a.context_id = u.user_id
			WHERE a.context_type = 'user'
			AND a.entity_type = {$type_val}
			AND a.entity_id = {$id_val}
			UNION ALL
			SELECT a.acl_id, a.context_type, a.context_id, r.name AS label
			FROM auth_acl a
			INNER JOIN auth_roles r ON a.context_id = r.role_id
			WHERE a.context_type = 'role'
			AND a.entity_type = {$type_val}
			AND a.entity_id = {$id_val}
			UNION ALL
			SELECT a.acl_id, a.context_type, a.context_id, d.name AS label
			FROM auth_acl a
			INNER JOIN departments d ON a.context_id = d.department_id
			WHERE a.context_type = 'department'
			AND a.entity_type = {$type_val}
			AND a.entity_id = {$id_val}
		";

		$query = $this->db->query($sql);
		$result = $query->result();
		return $result;
	}


	public function user_room_permissions(int $user_id, int $room_id)
	{
		$cache_key = sprintf("user_room_perms_u%d_r%d", $user_id, $room_id);

		if ( ! isset($this->_cache[$cache_key])) {

			$user_id_val = $this->db->escape($user_id);
			$room_id_val = $this->db->escape($room_id);

			$this->db->reset_query();

			$this->db->select('ap.name AS permission');
			$this->db->select('acl.*');

			$this->db->from('auth_permissions ap');
			$this->db->join('auth_acl_permissions aap', 'permission_id', 'inner');
			$this->db->join('auth_acl acl', 'acl_id', 'inner');
			$this->db->join('users u', "user_id={$user_id_val}", 'left');
			$this->db->join('rooms r', "room_id={$room_id_val}", 'left');

			$this->db->group_start();
			$this->db->where("(entity_type = 'room_group' AND entity_id = r.room_group_id)");
			$this->db->or_where("(entity_type = 'room' AND entity_id = r.room_id)");
			$this->db->group_end();

			$this->db->group_start();
			$this->db->where("(acl.context_type = 'role' AND acl.context_id = u.role_id)");
			$this->db->or_where("(acl.context_type = 'user' AND acl.context_id = u.user_id)");
			$this->db->or_where("(acl.context_type = 'department' AND acl.context_id = u.department_id)");
			$this->db->group_end();

			$query = $this->db->get();
			$result = $query->result_array();

			$this->_cache[$cache_key] = $result;
		}

		return $this->_cache[$cache_key];
	}


	public function rooms_for_user_subquery(int $user_id)
	{
		$permission = Permission::ROOM_VIEW;
		$permission_val = $this->db->escape($permission);
		$user_id_val = $this->db->escape($user_id);

		$sql = "SELECT r.room_id
				FROM rooms r
				CROSS JOIN (
					SELECT acl.entity_type, acl.entity_id
					FROM auth_permissions ap
					INNER JOIN auth_acl_permissions aap USING(permission_id)
					INNER JOIN auth_acl acl USING(acl_id)
					INNER JOIN users u ON user_id = {$user_id_val}
					WHERE 1=1
					AND ap.name = {$permission_val}
					AND (
					    (acl.context_type = 'role' AND acl.context_id = u.role_id)
					    OR
					    (acl.context_type = 'user' AND acl.context_id = u.user_id)
					    OR
					    (acl.context_type = 'department' AND acl.context_id = u.department_id)
					    )
				) roomacl
				WHERE 1=1
				AND CASE roomacl.entity_type
					WHEN 'room_group' THEN roomacl.entity_id = r.room_group_id
					WHEN 'room' THEN roomacl.entity_id = r.room_id
				END";

		return $sql;
	}


}
