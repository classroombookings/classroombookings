<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access_control_model extends CI_Model
{


	const TARGET_ROOM = 'R';
	const TARGET_CATEGORY = 'C';

	const ACTOR_AUTHENTICATED = 'A';
	const ACTOR_GUEST = 'G';
	const ACTOR_DEPARTMENT = 'D';
	const ACTOR_USER = 'U';

	const ACCESS_VIEW = 'view';
	const ACCESS_CREATE_STATIC = 'create_static';
	const ACCESS_CREATE_RECURRING = 'create_recurring';
	const ACCESS_DELETE_STATIC = 'delete_static';
	const ACCESS_DELETE_RECURRING = 'delete_recurring';

	protected $table = 'access_control';


	public function __construct()
	{
		parent::__construct();

		$this->load->helper('result');
	}


	public static function get_targets()
	{
		return [
			self::TARGET_ROOM => 'Room',
		];
	}


	public static function get_actors()
	{
		return [
			self::ACTOR_AUTHENTICATED => 'Any logged-in user',
			self::ACTOR_DEPARTMENT => 'Department',
		];
	}


	public static function get_permissions()
	{
		return [
			self::ACCESS_VIEW => 'View',
		];
	}


	public static function get_access()
	{
		return [
			self::ACCESS_VIEW => 'View',
		];
	}


	public function get_all_items($filter = [])
	{
		$where = [];
		$where_str = '';

		if (array_key_exists('room_id', $filter) && !empty($filter['room_id'])) {
			$room_id = $this->db->escape($filter['room_id']);
			$where[] = "(target = 'R' AND target_id = {$room_id})";
		}

		if (array_key_exists('actor', $filter) && !empty($filter['actor'])) {
			$actor = $this->db->escape($filter['actor']);
			$where[] = "(actor = {$actor})";
		}

		$actor = element('actor', $filter);
		if ($actor == 'D' && array_key_exists('department_id', $filter) && !empty($filter['department_id'])) {
			$department_id = $this->db->escape($filter['department_id']);
			$where[] = "(actor = 'D' AND actor_id = {$department_id})";
		}

		if ( ! empty($where)) {
			$where_str = "\nAND\n" . implode("\nAND\n", $where);
		}

		$sql = "SELECT ac.*,
					r.name AS 'room.name',
					d.name AS 'department.name'
				FROM `{$this->table}` ac
				LEFT JOIN rooms r ON target = 'R' AND target_id = room_id
				LEFT JOIN departments d ON actor_id = department_id AND actor = 'D'
				WHERE 1=1
				{$where_str}
				ORDER BY r.name ASC, actor ASC, d.name ASC
				";

		$query = $this->db->query($sql);
		$result = $query->result();
		foreach ($result as &$row) {
			$row = nest_object_keys($row);
		}

		return $result;
	}


	public function group_items($items)
	{
		$targets = self::get_targets();
		$actors = self::get_actors();
		$permissions = self::get_permissions();

		$out = [];

		foreach ($items as $item) {

			list($target, $actor, $permission) = explode('.', $item->reference);

			$out[ $target ]['type'] = $targets[$item->target];
			$out[ $target ]['name'] = $item->room->name;

			$actor_name = $actors[$item->actor];
			switch ($item->actor) {
				case self::ACTOR_DEPARTMENT:
					$actor_name = $item->department->name;
				break;
			}

			$out[ $target ]['actors'][ $actor ]['type'] = $actors[$item->actor];
			$out[ $target ]['actors'][ $actor ]['name'] = $actor_name;
			$out[ $target ]['actors'][ $actor ]['permissions'][] =
			$item->permission_name = $permissions[$item->permission];
			$out[ $target ]['actors'][ $actor ]['items'][] = $item;

		}

		return $out;

	}


	public function get_rooms_subquery($user_id, $permission)
	{
		$user_id = $this->db->escape($user_id);
		$permission = $this->db->escape($permission);

		$sql = "SELECT target_id
				FROM {$this->table}
				LEFT JOIN users u ON user_id = {$user_id}
				WHERE target = 'R'
				AND permission = {$permission}
				AND
				(
					(actor = 'A')
					OR
					(u.user_id IS NOT NULL AND actor = 'U' AND actor_id = u.user_id)
					OR
					(u.department_id IS NOT NULL AND actor = 'D' AND actor_id = u.department_id)
				)";

		return $sql;
	}


	/**
	 * Return a subquery to be used with an EXISTS() expression.
	 *
	 * The exists query will include all rooms that the user ($user_id) has $permission on.
	 *
	 */
	public function get_rooms_exists(int $user_id, $permission, string $match_on = 'rooms.room_id')
	{
		if ( ! is_array($permission)) {
			$permissions = [$permission];
		} else {
			$permissions = $permission;
		}

		$this->db->reset_query();
		$this->db->select($this->table.'.id');
		$this->db->from($this->table);
		$this->db->join('users u', 'user_id = ' . $this->db->escape($user_id), 'inner');
		// $this->db->join('users u', null, 'cross');

		$this->db
			->group_start()
				->where('u.authlevel', ADMINISTRATOR)
				->or_group_start()
					->where('target', self::TARGET_ROOM)
					->where($this->table . '.target_id = ' . $match_on)
					->where_in('permission', $permissions)
					->group_start()
						->or_group_start()
							->where('actor', self::ACTOR_AUTHENTICATED)
							->where('actor_id IS NULL')
						->group_end()
						->or_group_start()
							->where('actor', self::ACTOR_DEPARTMENT)
							->where('actor_id = u.department_id')
							->where('u.department_id IS NOT NULL')
						->group_end()
						->or_group_start()
							->where('actor', self::ACTOR_USER)
							->where('actor_id = u.user_id')
							->where('u.user_id IS NOT NULL')
						->group_end()
					->group_end()
				->group_end()
			->group_end();

		return $this->db->get_compiled_select();
	}


	/**
	 * Get the rooms accessible to $user_id with the given $permission.
	 * Returns the Room IDs only.
	 *
	 */
	/*public function get_accessible_rooms($user_id, $permission)
	{
		$user_id = $this->db->escape($user_id);
		$permission = $this->db->escape($permission);

		$sql = "SELECT target_id
				FROM {$this->table}
				LEFT JOIN users u ON user_id = {$user_id}
				WHERE target = 'R'
				AND permission = {$permission}
				AND
				(
					(actor = 'A')
					OR
					(u.user_id IS NOT NULL AND actor = 'U' AND actor_id = u.user_id)
					OR
					(u.department_id IS NOT NULL AND actor = 'D' AND actor_id = u.department_id)
				)";

		$query = $this->db->query($sql);

		$room_ids = [];
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$room_ids[] = $row->target_id;
			}
		}

		return array_unique($room_ids);
	}*/


	public function exists($reference)
	{
		$sql = "SELECT id FROM {$this->table} WHERE reference = ? LIMIT 1";
		$query = $this->db->query($sql, $reference);
		return ($query->num_rows() === 1);
	}


	public function get_reference($data)
	{
		$reference = sprintf("%s%d.%s%s.%s", $data['target'], $data['target_id'], $data['actor'], $data['actor_id'], $data['permission']);
		return $reference;
	}


	public function add_entry($data)
	{
		$data['reference'] = $this->get_reference($data);

		$insert = $this->db->insert($this->table, $data);
		return $insert ? $this->db->insert_id() : FALSE;
	}


	public function delete_entry($id)
	{
		return $this->db->delete($this->table, ['id' => $id]);
	}


	public function delete_where($params)
	{
		return $this->db->delete($this->table, $params);
	}


}
