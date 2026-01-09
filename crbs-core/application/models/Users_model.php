<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Users_model extends CI_Model
{

	protected $table = 'users';
	protected $primary_key = 'user_id';


	protected $fields = [
		'user_id',
		'department_id',
		'role_id',
		'username',
		'firstname',
		'lastname',
		'email',
		'displayname',
		'ext',
		'lastlogin',
		'enabled',
		'created',
	];

	protected $sort_map = [
		'department' => 'd.name',
		'role' => 'ar.name',
		'username' => 'username',
		'displayname' => 'displayname',
		'lastlogin' => 'lastlogin',
		'enabled' => 'enabled',
	];


	public function __construct()
	{
		parent::__construct();
		$this->load->helper('result');
	}


	public function get_by_role($role_id)
	{
		$where = ['role_id' => $role_id];
		$this->db->order_by('username', 'ASC');
		$query = $this->db->get_where('users', $where);
		return $query->result_array();
	}


	/**
	 * Get an active/enabled user by the given username.
	 *
	 * @param  string $username Username
	 * @return  mixed FALSE on failure or DB row on success.
	 *
	 */
	public function get_by_username($username, $require_enabled = TRUE)
	{
		if (empty($username)) {
			return FALSE;
		}

		$where = [
			'username' => $username,
		];

		if ($require_enabled) {
			$where['enabled'] = 1;
		}

		$query = $this->db->get_where('users', $where, 1);

		if ($query->num_rows() === 1) {
			return $query->row();
		}

		return FALSE;
	}


	/**
	 * Get an active/enabled user by the given ID.
	 *
	 * @param  int $id User ID
	 * @return  mixed FALSE on failure or DB row on success.
	 *
	 */
	public function get_by_id($id)
	{
		$where = [
			'user_id' => $id,
			'enabled' => 1,
		];

		$query = $this->db->get_where('users', $where, 1);

		if ($query->num_rows() === 1) {
			return $query->row();
		}

		return FALSE;
	}


	/**
	 * Set a user password.
	 *
	 * @param  mixed $user ID or User row object.
	 * @param  string $password New password to set.
	 *
	 */
	public function set_password($user, $password)
	{
		if (is_object($user)) {
			$user_id = $user->user_id;
		} elseif (is_numeric($user)) {
			$user_id = $user;
		}

		if ( ! isset($user_id)) {
			return FALSE;
		}

		$user_data = [
			'password' => password_hash($password, PASSWORD_DEFAULT),
		];

		$where = [ 'user_id' => $user_id ];

		return $this->db->update('users', $user_data, $where);
	}


	public function insert($user_data = [])
	{
		$user_data = $this->sleep_values($user_data);
		$insert = $this->db->insert('users', $user_data);
		$result = ($insert ? $this->db->insert_id() : false);
		if ( ! $result) return $result;
		$this->save_constraints($result);
		return $result;
	}


	public function update($user_id, $user_data = [])
	{
		$where = ['user_id' => (int) $user_id];

		$user_data = $this->sleep_values($user_data);

		return $this->db->update('users', $user_data, $where);
	}


	public function count(array $filter = [])
	{
		$this->db->reset_query();
		$this->db->from('users');
		$this->db->join('auth_roles ar', $this->table.'.role_id = ar.role_id', 'left');
		$this->db->join('departments d', $this->table.'.department_id = d.department_id', 'left');
		$this->apply_filter($filter);
		return $this->db->count_all_results();
	}


	public function filtered(array $filter = [])
	{
		$out = [];

		$this->db->reset_query();
		$this->db->select($this->table.'.*');
		$this->db->select(trim("
           CASE
           		WHEN displayname IS NOT NULL AND displayname != ''
           			THEN CONCAT(username, ' (', displayname, ')')
           		WHEN (firstname IS NOT NULL AND firstname != '') AND (lastname IS NOT NULL AND lastname != '')
           			THEN CONCAT(username, ' (f', firstname, ' l', lastname, ')')
           		ELSE username
           	END AS user_full
		"));
		$this->db->select('ar.name AS role');
		$this->db->select('d.name AS department');
		$this->db->from($this->table);
		$this->db->join('auth_roles ar', $this->table.'.role_id = ar.role_id', 'left');
		$this->db->join('departments d', $this->table.'.department_id = d.department_id', 'left');
		$this->db->group_by($this->table . '.' . $this->primary_key);

		$this->apply_filter($filter);

		if (array_key_exists('limit', $filter)) {
			$this->db->limit($filter['limit']);
		}
		if (array_key_exists('offset', $filter)) {
			$this->db->offset($filter['offset']);
		}

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as $row) {
				$out[ $row->user_id ] = $this->wake_value($row);
			}
		}

		return $out;
	}


	protected function apply_filter(array $filter = [])
	{
		if (array_key_exists('sort', $filter)) {
			$sorts = parse_sort($filter['sort'], $this->sort_map);
			if ( ! is_null($sorts)) {
				$this->db->order_by($sorts);
			}
		}

		if (array_key_exists($this->primary_key, $filter)) {
			$this->db->where($this->table . '.' . $this->primary_key, $filter[$this->primary_key]);
			$this->db->limit(1);
		}

		if (array_key_exists('role_id', $filter) && ! empty($filter['role_id'])) {
			$this->db->where($this->table . '.role_id', $filter['role_id']);
		}

		if (array_key_exists('department_id', $filter) && ! empty($filter['department_id'])) {
			$this->db->where($this->table . '.department_id', $filter['department_id']);
		}

		if (array_key_exists('search', $filter) && ! empty($filter['search'])) {
			$this->db->group_start();
			$this->db->or_like('email', $filter['search']);
			$this->db->or_like('username', $filter['search']);
			$this->db->or_like('firstname', $filter['search']);
			$this->db->or_like('lastname', $filter['search']);
			$this->db->or_like('displayname', $filter['search']);
			$this->db->group_end();
		}

		return;
	}


	public function wake_value($row)
	{
		return $row;
	}


	public function sleep_values($data)
	{
		$allow_null = [
			'role_id',
			'department_id',
			'firstname',
			'lastname',
			'email',
			'password',
			'displayname',
			'ext',
			'lastlogin',
			'created',
			'force_password_reset',
			// 'max_active_bookings_value',
		];

		foreach ($allow_null as $field) {
			if (array_key_exists($field, $data) && $data[$field] == '') {
				$data[$field] = null;
			}
		}

		return $data;
	}


	public function get_constraints(int $user_id)
	{
		static $_constraints_by_user;

		if ( ! isset($_constraints_by_user[$user_id])) {

			$sql = "SELECT
						CASE uc.max_active_bookings_type
							WHEN 'R' THEN ar.max_active_bookings
							WHEN 'U' THEN uc.max_active_bookings_value
							WHEN 'X' THEN NULL
							ELSE NULL
						END AS 'max_active_bookings',
						CASE uc.range_min_type
							WHEN 'R' THEN ar.range_min
							WHEN 'U' THEN uc.range_min_value
							WHEN 'X' THEN NULL
							ELSE NULL
						END AS 'range_min',
						CASE uc.range_max_type
							WHEN 'R' THEN ar.range_max
							WHEN 'U' THEN uc.range_max_value
							WHEN 'X' THEN NULL
							ELSE NULL
						END AS 'range_max',
						CASE uc.recur_max_instances_type
							WHEN 'R' THEN ar.recur_max_instances
							WHEN 'U' THEN uc.recur_max_instances_value
							WHEN 'X' THEN NULL
							ELSE NULL
						END AS 'recur_max_instances'
					FROM users u
					LEFT JOIN users_constraints uc USING (user_id)
					LEFT JOIN auth_roles ar ON u.role_id = ar.role_id
					WHERE u.user_id = ?
					LIMIT 1
					";
			$query = $this->db->query($sql, [$user_id]);
			$row = $query->row_array();
			$_constraints_by_user[$user_id] = $row;
		}

		return $_constraints_by_user[$user_id];
	}


	public function get_scheduled_booking_count(int $user_id)
	{
		static $_bookings_by_user;

		if (!isset($_bookings_by_user[$user_id])) {

			$today = date("Y-m-d");
			$time = date('H:i') . ':00';

			$sql = "SELECT COUNT(booking_id) AS total
					FROM bookings
					INNER JOIN periods USING (period_id)
					INNER JOIN rooms USING (room_id)
					WHERE 1 = 1
					AND bookings.user_id = ?
					AND bookings.created_by = bookings.user_id
					AND bookings.status = 10
					AND bookings.date IS NOT NULL
					AND bookings.repeat_id IS NULL
					AND (
						(bookings.date > ?)	/* after today */
						OR
						(bookings.date = ? AND periods.time_start > ?) /* today, but after cur time */
					)
					";

			$query = $this->db->query($sql, [
				$user_id,
				$today,
				$today,
				$time
			]);

			$row = $query->row_array();
			$_bookings_by_user[$user_id] = (int) $row['total'];
		}

		return sprintf('%d', $_bookings_by_user[$user_id]);
	}


	public function user_constraints_raw($user_id)
	{
		$query = $this->db->get_where('users_constraints', ['user_id' => $user_id], 1);
		return $query->num_rows() == 1 ? $query->row() : null;
	}


	public function save_constraints($user_id, $values = null)
	{
		$props = [
			'max_active_bookings',
			'range_min',
			'range_max',
			'recur_max_instances',
		];

		$data = [];

		if ( ! is_array($values)) {
			foreach ($props as $k) {
				$data["{$k}_type"] = 'R';
				$data["{$k}_value"] = null;
			}
		} else {
			foreach ($props as $k) {
				$type_key = "{$k}_type";
				$value_key = "{$k}_value";
				$data[$type_key] = 'R';
				$data[$value_key] = null;
				if (array_key_exists($type_key, $values)) {
					$data[$type_key] = $values[$type_key];
				}
				if ($data[$type_key] == 'U') {
					$data[$value_key] = $values[$value_key];
				}
			}
		}

		$data['user_id'] = $user_id;

		$result = $this->db->replace('users_constraints', $data);
		return $result;
	}


	function Get($user_id = NULL, $pp = 10, $start = 0)
	{
		if ($user_id == NULL) {
			return $this->crud_model->Get('users', NULL, NULL, NULL, 'enabled asc, username asc', $pp, $start);
		} else {
			return $this->crud_model->Get('users', 'user_id', $user_id);
		}
	}


	function Add($data)
	{
		throw new BadMethodCallException("Deprecated.");
		$query = $this->db->insert('users', $data);
		return ($query ? $this->db->insert_id() : $query);
	}


	function Edit($user_id, $data)
	{
		throw new BadMethodCallException("Deprecated.");
		$this->db->where('user_id', $user_id);
		$result = $this->db->update('users', $data);
		return ($result ? $user_id : FALSE);
	}


	/**
	 * Delete a user
	 *
	 * @param   int   $id   ID of user to delete
	 *
	 */
	function Delete($id)
	{
		// Remove any ACLs that were for this user
		$sql = "DELETE FROM auth_acl WHERE context_type = 'user' AND context_id = ?";
		$this->db->query($sql, [$id]);

		// Remove bookings
		$sql = "DELETE FROM bookings WHERE user_id = ?";
		$this->db->query($sql, [$id]);
		$sql = "DELETE FROM bookings_repeat WHERE user_id = ?";
		$this->db->query($sql, [$id]);

		// Remove room ownership
		$sql = "UPDATE rooms SET user_id = NULL WHERE user_id = ?";
		$this->db->query($sql, [$id]);

		// Remove constraints
		$sql = "DELETE FROM users_constraints WHERE user_id = ?";
		$this->db->query($sql, [$id]);

		// Remove user
		$sql = "DELETE FROM users WHERE user_id = ? LIMIT 1";
		return $this->db->query($sql, [$id]);
	}


}
