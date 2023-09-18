<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Session_schedules_model extends CI_Model
{


	protected $table = 'session_schedules';


	public function __construct()
	{
		parent::__construct();
	}


	public function get_by_session($session_id)
	{
		$this->init_query();
		$this->db->where($this->table.'.session_id', $session_id);
		$query = $this->db->get();

		return ($query->num_rows() > 0) ? $query->result() : [];
	}


	public function get_by_group($room_group_id)
	{
		$query = $this->db->from($this->table)->where('room_group_id', $room_group_id)->get();

		return ($query->num_rows() > 0) ? $query->result() : [];
	}


	public function flatten_results($results)
	{
		$out = [];

		foreach ($results as $row) {
			$key = sprintf('session_%d_group_%d', $row->session_id, $row->room_group_id);
			$out[$key] = $row->schedule_id;
		}

		return $out;
	}


	private function init_query()
	{
		$this->db->from($this->table);
		$this->db->select($this->table.'.*');
		$this->db->select('sess.name AS session');
		$this->db->select('group.name AS group');
		$this->db->select('schedule.name AS schedule');
		$this->db->join('sessions sess', $this->table.'.session_id = sess.session_id', 'inner');
		$this->db->join('room_groups group', $this->table.'.room_group_id = group.room_group_id', 'inner');
		$this->db->join('schedules schedule', $this->table.'.schedule_id = schedule.schedule_id', 'inner');
	}


	public function update($defaults, $data)
	{
		$rows = [];

		foreach ($data as $row) {

			$values = array_merge($defaults, $row);

			if ( ! isset($values['session_id'])) continue;
			if ( ! isset($values['room_group_id'])) continue;
			if ( ! isset($values['schedule_id'])) continue;

			$row = [
				$values['session_id'],
				$values['room_group_id'],
				$values['schedule_id'],
			];

			$rowStr = sprintf('(%s)', implode(",", $row));
			$rows[] = $rowStr;
		}

		$valuesStr = implode(',', $rows);

		$sql = "INSERT INTO {$this->table}
				(`session_id`, `room_group_id`, `schedule_id`)
				VALUES {$valuesStr}
				ON DUPLICATE KEY UPDATE
					`session_id` = VALUES(`session_id`),
					`room_group_id` = VALUES(`room_group_id`),
					`schedule_id` = VALUES(`schedule_id`)
				";

		return $this->db->query($sql);
	}


}
