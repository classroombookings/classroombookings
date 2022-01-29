<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Holidays_model extends CI_Model
{


	protected $table = 'holidays';


	public function __construct()
	{
		parent::__construct();
	}


	public function get_by_session($session_id)
	{
		$where = [ 'session_id' => $session_id ];

		$query = $this->db->from($this->table)
			->where($where)
			->order_by('date_start', 'ASC')
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


	public function get($holiday_id)
	{
		$where = [ 'holiday_id' => $holiday_id ];

		$query = $this->db->get_where($this->table, $where, 1);

		if ($query->num_rows() === 1) {
			return $this->wake_value($query->row());
		}

		return FALSE;
	}



	/**
	 * Add a new holiday.
	 *
	 */
	public function insert($data)
	{
		$data = $this->sleep_values($data);

		$insert = $this->db->insert($this->table, $data);

		if ($insert) {
			$id = $this->db->insert_id();
			$this->dates_model->refresh_holidays($data['session_id']);
			return $id;
		}

		return FALSE;
	}


	/**
	 * Update a holiday with given data.
	 *
	 */
	public function update($holiday_id, $data)
	{
		$data = $this->sleep_values($data);

		$where = ['holiday_id' => $holiday_id];

		$update = $this->db->update($this->table, $data, $where, 1);

		if ($update) {
			$this->dates_model->refresh_holidays($data['session_id']);
		}

		return $update;
	}



	/**
	 * Delete a single holiday
	 *
	 */
	public function delete($id)
	{
		$delete = $this->db->delete($this->table, ['holiday_id' => $id]);

		if ($delete) {
			$this->dates_model->clear('holiday_id', $id);
		}

		return $delete;
	}


	/**
	 * Delete entries for a given session.
	 *
	 */
	public function delete_by_session($session_id)
	{
		return $this->db->delete($this->table, ['session_id' => $session_id]);
	}


	public function wake_value($row)
	{
		$row->date_start = datetime_from_string($row->date_start);
		$row->date_end = datetime_from_string($row->date_end);

		return $row;
	}


	public function sleep_values($data)
	{
		if (array_key_exists('date_start', $data)) {
			$dt = datetime_from_string($data['date_start']);
			$data['date_start'] = $dt ? $dt->format('Y-m-d') : NULL;
		}

		if (array_key_exists('date_end', $data)) {
			$dt = datetime_from_string($data['date_end']);
			$data['date_end'] = $dt ? $dt->format('Y-m-d') : NULL;
		}

		return $data;
	}



}
