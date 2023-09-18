<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Sessions_model extends CI_Model
{


	protected $table = 'sessions';


	public function __construct()
	{
		parent::__construct();

		$this->load->model('dates_model');
		$this->load->model('holidays_model');
		$this->load->model('bookings_model');

		$this->check_current();
	}


	public function get_all_past($epoch = NULL)
	{
		$date = !empty($epoch) ? $epoch : date('Y-m-d');

		$sql = "SELECT * FROM {$this->table}
				WHERE date_end < ?
				ORDER BY date_start DESC";

		$query = $this->db->query($sql, [ $date ]);

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as &$row) {
				$row = $this->wake_value($row);
			}
			return $result;
		}

		return FALSE;
	}


	public function get_all_active($epoch = NULL)
	{
		$date = !empty($epoch) ? $epoch : date('Y-m-d');

		$sql = "SELECT * FROM {$this->table}
				WHERE date_end >= ?
				ORDER BY date_start ASC";

		$query = $this->db->query($sql, [ $date ]);

		if ($query->num_rows() > 0) {
			$result = $query->result();
			foreach ($result as &$row) {
				$row = $this->wake_value($row);
			}
			return $result;
		}

		return FALSE;
	}


	public function get_by_date($date, $not_session_id = NULL)
	{
		$dt = datetime_from_string($date);

		if ( ! $dt) {
			return FALSE;
		}

		$session_where = '';
		if (!empty($not_session_id)) {
			$not_session_id = (int) $not_session_id;
			$session_where = "AND session_id != {$not_session_id}";
		}

		$value = $dt->format('Y-m-d');
		$sql = "SELECT * FROM {$this->table}
				WHERE date_start <= ? AND date_end >= ?
				{$session_where}
				LIMIT 1";

		$query = $this->db->query($sql, [ $value, $value ]);
		if ($query->num_rows() === 1) {
			return $this->wake_value($query->row());
		}

		return FALSE;
	}


	/**
	 * Get the session marked as 'current' and is selectable.
	 *
	 */
	public function get_current()
	{
		$where = [
			'is_current' => 1,
			'is_selectable' => 1,
		];

		$query = $this->db->get_where($this->table, $where, 1);

		return ($query->num_rows() === 1)
			? $this->wake_value($query->row())
			: FALSE;
	}


	/**
	 * Get all selectable sessions.
	 *
	 */
	public function get_selectable()
	{
		$query = $this->db->from($this->table)
			->where('is_selectable', 1)
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


	/**
	 * Get one Session by ID
	 *
	 */
	public function get($session_id)
	{
		$where = [ 'session_id' => $session_id ];

		$query = $this->db->get_where($this->table, $where, 1);

		return ($query->num_rows() === 1)
			? $this->wake_value($query->row())
			: FALSE;
	}

	/**
	 * Get available Session by ID
	 *
	 */
	public function get_available_session($session_id)
	{
		$where = [
			'session_id' => $session_id,
			'is_selectable' => 1,
		];

		$query = $this->db->get_where($this->table, $where, 1);

		return ($query->num_rows() === 1)
			? $this->wake_value($query->row())
			: FALSE;
	}


	/**
	 * Add a new session record.
	 *
	 */
	public function insert($data)
	{
		$data = $this->sleep_values($data);

		$insert = $this->db->insert($this->table, $data);

		if ($insert) {
			$id = $this->db->insert_id();
			$this->auto_set_current();
			$this->dates_model->refresh_session($id);
			$this->dates_model->refresh_holidays($id);
			return $id;
		}

		return FALSE;
	}


	/**
	 * Update a session record with given data.
	 *
	 */
	public function update($session_id, $data)
	{
		$data = $this->sleep_values($data);

		$where = ['session_id' => $session_id];

		$update = $this->db->update($this->table, $data, $where, 1);

		if ($update) {
			$this->auto_set_current();
			$this->dates_model->refresh_session($session_id);
			$this->dates_model->refresh_holidays($session_id);
			$this->bookings_model->check_session_dates($session_id);
		}

		return $update;
	}



	public function delete($id)
	{
		$delete = $this->db->delete($this->table, ['session_id' => $id]);

		if ($delete) {
			$this->bookings_model->delete_by_session($id);
			$this->holidays_model->delete_by_session($id);
			$this->dates_model->delete_by_session($id);
		}

		return $delete;
	}


	/**
	 * Update the session entries, marking the current one with is_current.
	 *
	 * 'Current' is the session that started today or earlier, and ends today or after.
	 *
	 */
	public function auto_set_current()
	{
		$today = date('Y-m-d');

		$sql = "UPDATE {$this->table} SET is_current = 0;";
		$this->db->query($sql);

		$sql = "UPDATE {$this->table} SET is_current = 1
				WHERE date_start <= ? AND date_end >= ?
				LIMIT 1";
		$this->db->query($sql, [ $today, $today ]);

		$this->settings_model->set('session_auto_set_current_ts', time());
	}


	/**
	 * Check to see if the auto_set_current function should run to automatically set the current session.
	 *
	 */
	public function check_current()
	{
		$last_time = setting('session_auto_set_current_ts');
		$now = time();

		if (empty($last_time) || ($now - intval($last_time)) > TIME_DAY) {
			$this->auto_set_current();
		}
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
