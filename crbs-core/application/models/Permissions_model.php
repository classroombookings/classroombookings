<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Permissions_model extends CI_Model
{


	protected $table = 'auth_permissions';
	protected $primary_key = 'permission_id';

	const SCOPE_SYSTEM = 'system';
	const SCOPE_BOOKINGS = 'bookings';


	public function __construct()
	{
		parent::__construct();
	}



	public function get_all()
	{
		$query = $this->db->get($this->table);

		$out = [];

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as $row) {
				$out[$row->permission_id] = $row->name;
			}
			return $out;
		}

		return [];
	}


	public function get_grouped()
	{
		$query = $this->db->get($this->table);

		$out = [];

		$group_order = [
			'system',
			'setup',
			'room',
			'book_single',
			'book_recur',
		];
		$permission_order = [
			'create',
			'edit_other_booking',
			'cancel_other_booking',
			'set_user',
			'set_department',
			'view_other_notes',
			'view_other_users',
		];

		if ($query->num_rows() > 0) {
			$result = $query->result();
			$all = [];
			foreach ($result as $row) {
				[$group, $name] = explode('.', (string) $row->name);
				$all[$group][$row->permission_id] = $row->name;
			}
			foreach ($group_order as $k) {
				$permissions = $all[$k];
				uasort($permissions, function($a, $b) use ($permission_order) {
					[, $a_name] = explode('.', $a);
					[, $b_name] = explode('.', $b);
					// print_r($a);
					$pos_a = array_search($a_name, $permission_order);
					$pos_b = array_search($b_name, $permission_order);
					return $pos_a - $pos_b;
				});
				$out[$k] = $permissions;
			}

			return $out;
		}

		return [];
	}


	public function get_scoped($scope = null)
	{
		$out = [
			self::SCOPE_SYSTEM => [],
			self::SCOPE_BOOKINGS => [],
		];

		$all = $this->get_grouped();
		foreach ($all as $group => $items) {
			switch ($group) {
				case 'setup':
				case 'system':
					$out[self::SCOPE_SYSTEM][$group] = $items;
					break;
				case 'room':
				case 'book_single':
				case 'book_recur':
					$out[self::SCOPE_BOOKINGS][$group] = $items;
					break;
			}
		}

		return is_null($scope) ? $out : $out[$scope];
	}





}
