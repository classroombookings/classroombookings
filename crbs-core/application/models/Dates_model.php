<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Dates_model extends CI_Model
{


	protected $table = 'dates';

	private $_inlcude_period_count = FALSE;


	public function __construct()
	{
		parent::__construct();
		$this->load->model('sessions_model');
		$this->load->model('holidays_model');
		$this->load->model('weeks_model');
		$this->load->helper('date');
	}


	public function with_period_count()
	{
		$this->_inlcude_period_count = TRUE;
		return $this;
	}


	public function get_by_session($session_id)
	{
		$out = [];

		$this->db->reset_query();
		$this->db->select('dates.*');
		$this->db->from($this->table);
		$this->db->where(['session_id' => $session_id]);
		$this->db->group_by('date');

		if ($this->_inlcude_period_count) {
			$subquery = $this->get_periods_subquery();
			$this->db->select('COUNT(all_periods.period_id) AS period_count', FALSE);
			$this->db->join("({$subquery}) all_periods", 'weekday', 'LEFT');
			$this->_inlcude_period_count = FALSE;
		}

		$query = $this->db->get();

		if ($query->num_rows() == 0) {
			return $out;
		}

		$result = $query->result();

		foreach ($result as $row) {
			$out[ $row->date ] = $row;
		}

		return $out;
	}


	/**
	 * Get date info by start and end date range
	 *
	 */
	public function get_by_range($date_start, $date_end)
	{
		$date_start = datetime_from_string($date_start);
		$date_end = datetime_from_string($date_end);

		if ( ! $date_start && ! $date_end) {
			return FALSE;
		}

		$out = [];

		$this->db->reset_query();
		$this->db->select('dates.*');
		$this->db->from($this->table);
		$this->db->where([
			'date >=' => $date_start->format('Y-m-d'),
			'date <=' => $date_end->format('Y-m-d'),
		]);
		$this->db->group_by('date');

		if ($this->_inlcude_period_count) {
			// $subquery = $this->get_periods_subquery();
			// $this->db->select('COUNT(all_periods.period_id) AS period_count', FALSE);
			// $this->db->join("({$subquery}) all_periods", 'weekday', 'LEFT');
			$subquery = $this->get_period_count_subquery();
			$this->db->select('MAX(pc.period_count) AS period_count', FALSE);
			$this->db->join("({$subquery}) pc", 'weekday', 'LEFT');

			$this->_inlcude_period_count = FALSE;
		}

		$query = $this->db->get();

		if ($query->num_rows() == 0) {
			return $out;
		}

		$result = $query->result();

		foreach ($result as $row) {
			$out[ $row->date ] = $row;
		}

		return $out;
	}


	/**
	 * Get date records by single date.
	 *
	 */
	public function get_by_date($date)
	{
		$date = datetime_from_string($date);

		if ( ! $date) {
			return FALSE;
		}

		$this->db->reset_query();
		$this->db->select('dates.*');
		$this->db->from($this->table);
		$this->db->where(['date' => $date->format('Y-m-d')]);
		$this->db->limit(1);
		$this->db->group_by('date');

		if ($this->_inlcude_period_count) {
			$subquery = $this->get_period_count_subquery();
			$this->db->select('pc.period_count AS period_count', FALSE);
			$this->db->join("({$subquery}) pc", 'weekday', 'LEFT');
			$this->_inlcude_period_count = FALSE;
		}

		$query = $this->db->get();

		return $query->num_rows() === 1 ? $query->row() : FALSE;
	}


	/**
	 * Get all the recurring dates for a given session, weekday and Week ID.
	 *
	 * This is mainly used when creating bookings, and offering the selection
	 * of dates for when a recurring booking can begin or end on.
	 *
	 */
	public function get_recurring_dates($session_id, $week_id, $weekday)
	{
		$this->db->reset_query();
		$this->db->select('dates.*');
		$this->db->from($this->table);
		$this->db->where([
			'session_id' => $session_id,
			'week_id' => $week_id,
			'weekday' => $weekday,
		]);
		$this->db->where('holiday_id IS NULL');
		$this->db->group_by('date');

		if ($this->_inlcude_period_count) {
			$subquery = $this->get_period_count_subquery();
			$this->db->select('pc.period_count AS period_count', FALSE);
			$this->db->join("({$subquery}) pc", 'weekday', 'LEFT');
			$this->_inlcude_period_count = FALSE;
		}

		$query = $this->db->get();

		if ($query->num_rows() === 0) return FALSE;

		$result = $query->result();
		foreach ($result as &$row) {
			$row->date = datetime_from_string($row->date);
		}

		return $result;
	}


	/**
	 * Get the available dates either side of the given date.
	 *
	 * This is used for bookings grid navigation.
	 *
	 */
	public function get_prev_next($date, $range = 'day')
	{
		$date = datetime_from_string($date);

		if ( ! $date) {
			return FALSE;
		}

		$period_subquery = $this->get_period_count_subquery();

		$queries = [
			[
				'name' => 'prev',
				'where' => 'prev.date < m.date',
				'order' => 'prev.date DESC',
			],
			[
				'name' => 'next',
				'where' => 'next.date > m.date',
				'order' => 'next.date ASC',
			],
		];

		$unions = [];

		foreach ($queries as $q) {
			extract($q);
			$this->db->reset_query();
			$this->db->select("{$name}.*");
			$this->db->select("'{$name}' AS dir");
			$this->db->select("pc.period_count");
			$this->db->from("{$this->table} m");	// (m == 'main')
			$this->db->join("dates {$name}", 'session_id', 'INNER');
			$this->db->join("({$period_subquery}) pc", "{$name}.weekday = pc.weekday", 'INNER');
			$this->db->where('m.date', $date->format('Y-m-d'));
			$this->db->where($where);
			$this->db->where("{$name}.week_id IS NOT NULL");

			if ($range == 'week') {
				$this->db->where("ABS(DATEDIFF({$name}.date, m.date)) >= 7");
			} elseif ($range == 'day') {
				$this->db->where("{$name}.holiday_id IS NULL");
			}

			$this->db->having('pc.period_count > 0');
			$this->db->order_by($order);
			$this->db->limit(1);

			$unions[] = '(' . $this->db->get_compiled_select() . ')';
		}

		$sql = implode("\nUNION\n", $unions);

		$query = $this->db->query($sql);
		if ($query->num_rows() === 0) {
			return [];
		}

		$out = [];

		foreach ($query->result() as $row) {
			$out[ $row->dir ] = $row;
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

		$sql = "DELETE FROM {$this->table} WHERE session_id IS NULL";
		$this->db->query($sql);
	}


	public function first_bookable_date($session_id)
	{
		$sql = "SELECT `date`
				FROM {$this->table}
				WHERE session_id = ?
				AND week_id IS NOT NULL
				AND holiday_id IS NULL
				ORDER BY `date` ASC
				LIMIT 1";

		$query = $this->db->query($sql, [$session_id]);
		return ($query->num_rows() == 1 ? $query->row()->date : FALSE);
	}


	private function get_period_count_subquery()
	{
		$sql = [];

		foreach (range(1, 7) as $weekday) {
			$sql[] = "SELECT {$weekday} AS weekday, SUM(IF(period_id IS NULL, 0, 1)) AS period_count
					FROM periods
					WHERE day_{$weekday} = 1";
		}

		$unions = implode("\nUNION ALL\n", $sql);

		$sql = "SELECT `weekday`, (IF(period_count IS NULL, 0, period_count)) AS period_count
				FROM ({$unions}) period_counters";

		return $sql;
	}


	private function get_periods_subquery()
	{
		$sql = [];

		foreach (range(1, 7) as $weekday) {
			$sql[] = "SELECT period_id, {$weekday} AS weekday FROM periods WHERE day_{$weekday} = 1";
		}
		$sql_string = implode("\nUNION ALL\n", $sql);
		return $sql_string;
	}
/*
SELECT period_id, 1 AS weekday FROM periods WHERE day_1 = 1
UNION ALL
SELECT period_id, 2 AS weekday FROM periods WHERE day_2 = 1
UNION ALL
SELECT period_id, 3 AS weekday FROM periods WHERE day_3 = 1
UNION ALL
SELECT period_id, 4 AS weekday FROM periods WHERE day_4 = 1
UNION ALL
SELECT period_id, 5 AS weekday FROM periods WHERE day_5 = 1
UNION ALL
SELECT period_id, 6 AS weekday FROM periods WHERE day_6 = 1
UNION ALL
SELECT period_id, 7 AS weekday FROM periods WHERE day_7 = 1
) all_p ON dates.weekday = all_p.weekday
WHERE session_id = 2
GROUP BY date
	}*/


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
				'week_id' => !empty($week_id) ? (int) $week_id : NULL,
			];

		}

		return $this->db->update_batch($this->table, $dates, 'date');
	}


	public function apply_week($session_id, $week_id)
	{
		$session = $this->sessions_model->get($session_id);
		if ( ! $session) {
			return FALSE;
		}

		$sql = "UPDATE {$this->table} SET week_id = ? WHERE session_id = ?";
		return $this->db->query($sql, [$week_id, $session_id]);
	}


}
