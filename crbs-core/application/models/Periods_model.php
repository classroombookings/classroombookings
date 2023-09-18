<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Periods_model extends CI_Model
{


	protected $table = 'periods';
	protected $primary_key = 'period_id';

	public $days = array();


	public function __construct()
	{
		parent::__construct();

		// $this->load->model('crud_model');

		$this->days[1] = 'Monday';
		$this->days[2] = 'Tuesday';
		$this->days[3] = 'Wednesday';
		$this->days[4] = 'Thursday';
		$this->days[5] = 'Friday';
		$this->days[6] = 'Saturday';
		$this->days[7] = 'Sunday';
	}


	public function get_all()
	{
		return $this->filtered();
	}


	public function get($period_id)
	{
		$where = [ 'period_id' => $period_id ];

		$query = $this->db->get_where($this->table, $where, 1);

		if ($query->num_rows() === 1) {
			return $this->wake_value($query->row());
		}

		return FALSE;
	}


	/**
	 * Get a list of periods in a given schedule
	 *
	 */
	public function get_by_schedule($schedule_id)
	{
		return $this->filtered(['schedule_id' => $schedule_id]);
	}


	public function filtered(array $filter = [])
	{
		$this->db->reset_query()
			->from($this->table)
			->order_by('time_start', 'ASC')
			->order_by('day_1', 'DESC')
			->order_by('day_2', 'DESC')
			->order_by('day_3', 'DESC')
			->order_by('day_4', 'DESC')
			->order_by('day_5', 'DESC')
			->order_by('day_6', 'DESC')
			->order_by('day_7', 'DESC')
			;

		$this->apply_filter($filter);

		$query = $this->db->get();

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as $row) {
				$out[ $row->period_id ] = $this->wake_value($row);
			}
			return $out;
		}

		return FALSE;
	}



	protected function apply_filter(array $filter = [])
	{
		if (array_key_exists($this->primary_key, $filter)) {
			$this->db->where($this->table . '.' . $this->primary_key, $filter[$this->primary_key]);
			$this->db->limit(1);
		}

		if (array_key_exists('schedule_id', $filter)) {
			$this->db->where($this->table . '.schedule_id', $filter['schedule_id']);
		}

		if (array_key_exists('day', $filter)) {
			$day = $filter['day'];
			if (is_numeric($day) && in_array($day, array_keys($this->days))) {
				$column = sprintf('day_%d', $day);
				$this->db->where($this->table . '.' . $column, '1');
			}
		}

		if (array_key_exists('bookable', $filter)) {
			$this->db->where('bookable', $filter['bookable']);
		}

		return;
	}


	public function insert($data = [])
	{
		$insert = $this->db->insert('periods', $data);
		return ($insert ? $this->db->insert_id() : FALSE);
	}


	public function update($period_id, $data = [])
	{
		$where = ['period_id' => $period_id];

		return $this->db->update('periods', $data, $where);
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
