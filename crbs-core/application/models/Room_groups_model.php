<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Room_groups_model extends CI_Model
{


	protected $table = 'room_groups';
	protected $primary_key = 'room_group_id';


	public function __construct()
	{
		parent::__construct();
	}



	public function get_all()
	{
		$sql = "SELECT
					rg.*,
					COUNT(r.room_id) AS room_count
				FROM {$this->table} rg
				LEFT JOIN rooms r USING (room_group_id)
				GROUP BY rg.room_group_id
				ORDER BY rg.pos ASC, rg.name ASC
				";

		$query = $this->db->query($sql);

		$out = [];

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as $row) {
				$out[$row->room_group_id] = $this->wake_value($row);
			}
			return $out;
		}

		return [];
	}


	/**
	 * Get a list of groups for a given user based on which rooms they can view.
	 *
	 */
	public function get_bookable($for_user_id)
	{
		$out = [];

		// Get the access control EXISTS query to filter the rooms
		$permission = Access_control_model::ACCESS_VIEW;
		$exists_sql = $this->access_control_model->get_rooms_exists($for_user_id, $permission, 'r.room_id');
		$where_exists = sprintf('EXISTS (%s)', $exists_sql);

		$this->db->reset_query();

		$this->db->select('rg.*');
		$this->db->select('COUNT(r.room_id) AS room_count');
		$this->db->from($this->table . ' rg');
		$this->db->join('rooms AS r', 'room_group_id', 'LEFT');
		$this->db->where('r.bookable', 1);

		$this->db->where($where_exists);

		$this->db->group_by('rg.room_group_id');
		$this->db->order_by('rg.pos', 'asc');
		$this->db->order_by('rg.name', 'asc');

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as $row) {
				$out[ $row->room_group_id ] = $this->wake_value($row);
			}
		}

		return $out;
	}


	/**
	 * Get one Room Cataegory by ID
	 *
	 */
	public function get($id)
	{
		$where = [ $this->primary_key => $id ];

		$query = $this->db->get_where($this->table, $where, 1);

		return ($query->num_rows() === 1)
			? $this->wake_value($query->row())
			: FALSE;
	}


	/**
	 * Add a new room category
	 *
	 */
	public function insert($data)
	{
		if ( ! isset($data['pos'])) {
			$data['pos'] = $this->get_pos();
		}

		$data = $this->sleep_values($data);

		$insert = $this->db->insert($this->table, $data);

		if ($insert) {
			$id = $this->db->insert_id();
			return $id;
		}

		return FALSE;
	}


	/**
	 * Update a room category with given data.
	 *
	 */
	public function update($id, $data)
	{
		$data = $this->sleep_values($data);

		$where = [ $this->primary_key => $id];

		$update = $this->db->update($this->table, $data, $where, 1);

		return $update;
	}



	public function delete($id)
	{
		$delete = $this->db->delete($this->table, [$this->primary_key => $id]);

		return $delete;
	}


	private function get_pos()
	{
		$sql = "SELECT MAX(pos) AS pos FROM {$this->table}";
		$query = $this->db->query($sql);
		$row = $query->row();
		return (int) $row->pos;
	}


	public function wake_value($row)
	{
		return $row;
	}


	public function sleep_values($data)
	{
		return $data;
	}


	public function update_pos($data)
	{
		return $this->db->update_batch($this->table, $data, 'room_group_id');
	}


}
