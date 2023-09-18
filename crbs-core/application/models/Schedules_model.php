<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Schedules_model extends CI_Model
{


	protected $table = 'schedules';
	protected $primary_key = 'schedule_id';


	public function __construct()
	{
		parent::__construct();
	}


	public function get_all()
	{
		$query = $this->db->from($this->table)
			->order_by('name', 'ASC')
			->get();

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as &$row) {
				$row = $this->wake_value($row);
			}
			return $result;
		}

		return FALSE;
	}



	public function get($schedule_id)
	{
		$where = [ 'schedule_id' => $schedule_id ];

		$query = $this->db->get_where($this->table, $where, 1);

		if ($query->num_rows() === 1) {
			return $this->wake_value($query->row());
		}

		return FALSE;
	}


	/**
	 * Get the applied schedule for the session and room group combination.
	 *
	 */
	public function get_applied_schedule($session_id, $room_group_id)
	{
		if (empty($session_id)) return false;
		if (empty($room_group_id)) return false;

		$this->db->from($this->table);
		$this->db->select('*');
		$this->db->join('session_schedules ss', $this->table . '.schedule_id = ss.schedule_id', 'inner');
		$this->db->where('ss.session_id', $session_id);
		$this->db->where('ss.room_group_id', $room_group_id);
		$this->db->limit(1);
		$query = $this->db->get();

		return ($query->num_rows() == 1) ? $query->row() : [];
	}



	/**
	 * Add a new schedule.
	 *
	 */
	public function insert($data)
	{
		$data = $this->sleep_values($data);

		$insert = $this->db->insert($this->table, $data);

		return $insert ? $this->db->insert_id() : FALSE;
	}


	/**
	 * Update a schedule with given data.
	 *
	 */
	public function update($schedule_id, $data)
	{
		$data = $this->sleep_values($data);

		$where = ['schedule_id' => $schedule_id];

		$update = $this->db->update($this->table, $data, $where, 1);

		return $update;
	}


	/**
	 * Delete a single schedule
	 *
	 */
	public function delete($id)
	{
		$delete = $this->db->delete($this->table, [$this->primary_key => $id]);

		return $delete;
	}


	public function wake_value($row)
	{
		return $row;
	}


	public function sleep_values($data)
	{
		return $data;
	}


}
