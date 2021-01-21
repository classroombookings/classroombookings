<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Dates_model extends CI_Model
{


	protected $table = 'dates';


	public function __construct()
	{
		parent::__construct();
		$this->load->model('sessions_model');
		$this->load->model('holidays_model');
		$this->load->model('weeks_model');
	}


	public function get($session_id)
	{
		$out = [];

		$sql = "SELECT * FROM {$this->table} WHERE session_id = ?";
		$query = $this->db->query($sql, [$session_id]);
		if ($query->num_rows() == 0) {
			return $out;
		}

		foreach ($query->result() as $row) {
			$out[ $row->date ] = $row;
		}

		return $out;
	}


	/**
	 * Refresh the dates table with all dates in session (date_start -> date_end).
	 *
	 * If dates do not exist, they will be added.
	 * If they do, they will be updated.
	 * The given $session_id will be applied to each date entry.
	 *
	 */
	public function refresh_session($session_id)
	{
		$session = $this->sessions_model->get($session_id);
		if ( ! $session) {
			return FALSE;
		}

		$sql = "UPDATE {$this->table} SET session_id = NULL WHERE session_id = ?";
		$this->db->query($sql, $session->session_id);

		$last_day = clone $session->date_end;
		$last_day->modify('+1 day');
		$interval = new DateInterval('P1D');
		$period = new DatePeriod($session->date_start, $interval, $last_day);
		$rows = [];

		foreach ($period as $value) {
			$date_value = $this->db->escape($value->format('Y-m-d'));
			$weekday = $value->format('N');
			$str = "({$date_value}, {$weekday}, {$session->session_id})";
			$rows[] = $str;
		}

		$values = implode(',', $rows);

		$sql = "INSERT INTO {$this->table}
				(`date`, `weekday`, `session_id`)
				VALUES {$values}
				ON DUPLICATE KEY UPDATE
				`date` = VALUES(`date`),
				`weekday` = VALUES(`weekday`),
				`session_id` = VALUES(`session_id`)";

		$this->db->query($sql);

		$sql = 'DELETE FROM dates WHERE session_id IS NULL';
		$this->db->query($sql);
	}


	/**
	 * Delete entries for a given session.
	 *
	 */
	public function delete_by_session($session_id)
	{
		return $this->db->delete($this->table, ['session_id' => $session_id]);
	}


	/**
	 * Set values to NULL for given key and value.
	 * Used when deleting weeks or holidays.
	 *
	 */
	public function clear($key, $value)
	{
		$data = [$key => NULL];
		$where = [$key => $value];
		return $this->db->update($this->table, $data, $where);
	}


	/**
	 * Update the dates table with holiday IDs as appropriate.
	 *
	 * @param  int $year_id		ID of year to get holidays for
	 * @param  int $holiday_id		ID of single holiday to update. Optional.
	 *
	 * Each holiday:
	 * Update dates set holiday_id = NULL where holiday_id = holiday_id
	 *
	 * Get dates and update table with holiday_id
	 *
	 */
	public function refresh_holidays($session_id)
	{
		$session = $this->sessions_model->get($session_id);
		if ( ! $session) {
			return FALSE;
		}

		$sql = "UPDATE {$this->table} SET holiday_id = NULL WHERE session_id = ?";
		$this->db->query($sql, $session->session_id);

		$sql = 'SELECT * FROM holidays WHERE session_id = ?';
		$query = $this->db->query($sql, [$session_id]);
		if ($query->num_rows() === 0) {
			return FALSE;
		}

		$holidays = $query->result();

		$interval = new DateInterval('P1D');
		$dates = [];

		foreach ($holidays as $holiday) {

			$start_date = new DateTime($holiday->date_start);
			$end_date = new DateTime($holiday->date_end);
			$end_date->modify('+1 day');
			$period = new DatePeriod($start_date, $interval, $end_date);

			foreach ($period as $key => $value) {
				$dates[] = [
					'date' => $value->format('Y-m-d'),
					'holiday_id' => $holiday->holiday_id,
				];
			}
		}

		if (empty($dates)) {
			return TRUE;
		}

		return $this->db->update_batch($this->table, $dates, 'date');
	}


	/**
	 * Take date <=> week_id associations and update table
	 *
	 * @param  int $year_id		ID of year to update date week assignments for.
	 * @param  array $data		2D array of date => week_id
	 *
	 */
	public function set_weeks($session_id, $data = [])
	{
		$session = $this->sessions_model->get($session_id);
		if ( ! $session) {
			return FALSE;
		}

		// Set dates if not already there
		$sql = "SELECT `date` FROM {$this->table} WHERE session_id = ?";
		$query = $this->db->query($sql, [$session_id]);
		if ($query->num_rows() == 0) {
			$this->refresh_session($session_id);
		}

		// Clear existing assignments for all dates in the year
		$sql = "UPDATE {$this->table} SET week_id = NULL WHERE session_id = ?";
		$this->db->query($sql, $session->session_id);

		foreach ($data as $date => $week_id) {

			$dt = DateTime::createFromFormat('!Y-m-d', $date);

			// Skip date if it is outside of the year
			if ($dt < $session->date_start || $dt > $session->date_end) {
				continue;
			}

			$dates[] = [
				'date' => $date,
				'week_id' => strlen($week_id) ? (int) $week_id : NULL,
			];

		}

		return $this->db->update_batch($this->table, $dates, 'date');
	}


}
