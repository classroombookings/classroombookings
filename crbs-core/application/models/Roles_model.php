<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Roles_model extends CI_Model
{


	protected $table = 'auth_roles';
	protected $primary_key = 'role_id';

	private array $_cache = [];


	public function __construct()
	{
		parent::__construct();
	}



	public function get_all()
	{
		$sql = "SELECT
					r.*,
					COUNT(DISTINCT u.user_id) AS user_count
				FROM {$this->table} r
				LEFT JOIN users u USING (role_id)
				GROUP BY r.role_id
				ORDER BY r.name ASC
				";

		$query = $this->db->query($sql);

		$out = [];

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as $row) {
				$out[$row->role_id] = $this->wake_value($row);
			}
			return $out;
		}

		return [];
	}


	public function options()
	{
		$all = $this->get_all();
		$ids = array_keys($all);
		$names = array_column($all, 'name');
		return array_combine($ids, $names);
	}


	/**
	 * Get Role by ID
	 *
	 */
	public function get($id)
	{
		$this->db->join('auth_roles_permissions arp', $this->table.'.role_id = arp.role_id', 'left');
		$this->db->join('auth_permissions ap', 'arp.permission_id = ap.permission_id', 'left');

		$this->db->select($this->table.'.*');
		$this->db->select("GROUP_CONCAT(arp.permission_id) AS permission_ids");
		$this->db->select("GROUP_CONCAT(ap.name) AS permission_names");

		$this->db->group_by($this->table.'.'.$this->primary_key);

		$where = [ $this->table.'.'.$this->primary_key => $id ];
		$query = $this->db->get_where($this->table, $where, 1);

		return ($query->num_rows() === 1)
			? $this->wake_value($query->row())
			: FALSE;
	}


	public function get_permission_names(int $role_id)
	{
		$cache_key = "perm_names_role_{$role_id}";
		if ( ! isset($this->_cache[$cache_key])) {
			$sql = "SELECT ap.name
					FROM auth_permissions ap
					INNER JOIN auth_roles_permissions arp USING(permission_id)
					WHERE arp.role_id = ?
					";
			$query = $this->db->query($sql, [$role_id]);
			$result = $query->result_array();
			$permissions = array_column($result, 'name');
			$this->_cache[$cache_key] = $permissions;
		}

		return $this->_cache[$cache_key];
	}


	/**
	 * Add a new role
	 *
	 */
	public function insert($data)
	{
		$data = $this->sleep_values($data);

		$insert = $this->db->insert($this->table, $data);

		if ($insert) {
			$id = $this->db->insert_id();
			return $id;
		}

		$this->_cache = [];

		return FALSE;
	}


	/**
	 * Update a role with given data.
	 *
	 */
	public function update($id, $data)
	{
		$data = $this->sleep_values($data);

		$where = [ $this->primary_key => $id];

		$update = $this->db->update($this->table, $data, $where, 1);

		$this->_cache = [];

		return $update;
	}


	public function delete($role_id)
	{
		// Update users to remove role membership
		$sql = "UPDATE users SET role_id = NULL WHERE role_id = ?";
		$this->db->query($sql, [$role_id]);

		// Remove any ACLs that were for this role
		$sql = "DELETE FROM auth_acl WHERE context_type='role' AND context_id=?";
		$this->db->query($sql, [$role_id]);

		$delete = $this->db->delete($this->table, [$this->primary_key => $role_id]);

		$this->_cache = [];

		return $delete;
	}


	public function wake_value($row)
	{
		$permission_ids = $row->permission_ids ?? [];
		if (is_string($permission_ids)) {
			$row->permission_ids = explode(',', $permission_ids);
		}

		$permission_names = $row->permission_names ?? [];
		if (is_string($permission_names)) {
			$row->permission_names = explode(',', $permission_names);
		}

		return $row;
	}


	public function sleep_values($data)
	{
		$constraints = [
			'max_active_bookings',
			'range_min',
			'range_max',
			'recur_max_instances',
		];

		foreach ($constraints as $prop) {
			if (array_key_exists($prop, $data)) {
				$value = trim((string) $data[$prop]);
				$data[$prop] = is_numeric($value)
					? abs(intval($value))
					: null;
			}
		}

		return $data;
	}


	/**
	 * Set permissions for role.
	 *
	 * @param int   $role_id     Role ID
	 * @param array $permissions 2D array of permission IDs to set.
	 *
	 */
	public function set_permissions(int $role_id, array $permissions)
	{
		$sql = 'DELETE FROM auth_roles_permissions WHERE role_id = ?';
		$this->db->query($sql, $role_id);

		if (empty($permissions)) return true;

		$rows = [];

		foreach ($permissions as $permission_id) {
			$rows[] = [
				'role_id' => $role_id,
				'permission_id' => $permission_id,
			];
		}

		$result = true;

		if ( ! empty($rows)) {
			$result = $this->db->insert_batch('auth_roles_permissions', $rows);
		}

		$this->_cache = [];
		return $result;
	}


}
