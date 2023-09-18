<?php

use app\components\bookings\Context;
use app\components\bookings\Slot;
use app\components\bookings\exceptions\BookingValidationException;
use app\components\bookings\agent\UpdateAgent;


class Bookings_model extends CI_Model
{


	const STATUS_BOOKED = 10;
	const STATUS_CANCELLED = 15;

	protected $table = 'bookings';

	// Other objects to get/include with returned value
	private $include = [];

	// Error message
	private $error = FALSE;

	// private $all_periods;
	// private $periods_by_day_num;


	// Legacy:
	var $table_headings = '';
	var $table_rows = array();



	public function __construct()
	{
		$this->load->helper('result');
		$this->load->model('sessions_model');
	}


	public function get_error()
	{
		return $this->error;
	}


	public function include($objects)
	{
		if ( ! is_array($objects)) {
			$objects = [ $objects ];
		}

		$this->include = $objects;

		return $this;
	}


	public function get($booking_id)
	{
		$this->db->reset_query();

		$this->db->select([
			'b.*',
			'd.week_id AS week_id',
			'p.time_start',
			'p.time_end',
			'p.schedule_id',
		]);

		$this->db->from("{$this->table} b");
		$this->db->join('periods p', 'period_id', 'INNER');
		$this->db->join('dates d', 'date', 'LEFT');
		$this->db->join('weeks w', 'week_id', 'LEFT');

		$this->db->where('b.booking_id', $booking_id);
		$this->db->limit(1);

		$query = $this->db->get();

		if ($query->num_rows() === 1) {
			return $this->wake_value($query->row());
		}

		return FALSE;
	}


	/**
	 * Find all bookings in a repeating series.
	 *
	 */
	public function find_by_repeat($repeat_id)
	{
		$this->db->reset_query();

		$this->db->select([
			'b.*',
			'p.time_start',
			'p.time_end',
		]);

		$this->db->from("{$this->table} AS b");
		$this->db->join('periods p', 'period_id', 'INNER');
		$this->db->where('b.repeat_id', $repeat_id);

		$this->db->order_by('date', 'ASC');

		$query = $this->db->get();
		$result = $query->result();

		$out = [];

		foreach ($result as &$row) {
			$key = Slot::generate_key($row->date, $row->period_id, $row->room_id);
			$out[ $key ] = $this->wake_value($row);
		}

		return $out;
	}


	/**
	 * Given a list of dates, a Period ID and Room ID, get a list of active
	 * bookings that already exist for that criteria.
	 *
	 * @param $dates Array of dates in Y-m-d format to check for
	 *
	 */
	public function find_conflicts(array $dates, $period_id, $room_id)
	{
		if (empty($dates)) return false;

		$this->db->reset_query();

		$this->db->select([
			'b.booking_id',
			'b.repeat_id',
			'b.period_id',
			'b.room_id',
			'b.user_id',
			'b.date',
			'b.status',
			'b.notes',
			'p.time_start',
			'p.time_end',
		]);

		$this->db->select([
			'u.user_id AS user__user_id',
			'u.username AS user__username',
			'u.displayname AS user__displayname'
		], FALSE);

		$this->db->select([
			'r.room_id AS room__room_id',
			'r.name AS room__name',
		], FALSE);

		$this->db->from("{$this->table} AS b");
		$this->db->join('periods p', 'period_id', 'INNER');
		$this->db->join('users u', 'user_id', 'LEFT');
		$this->db->join('rooms r', 'room_id', 'LEFT');

		$this->db->where('b.period_id', $period_id);
		$this->db->where('b.room_id', $room_id);
		$this->db->where('b.status', self::STATUS_BOOKED);
		$this->db->where_in('b.date', $dates);

		$this->db->order_by('date', 'ASC');

		$query = $this->db->get();
		$result = $query->result();

		$out = [];

		foreach ($result as &$row) {
			$key = Slot::generate_key($row->date, $row->period_id, $row->room_id);
			$out[ $key ] = $this->wake_value($row);
		}

		return $out;
	}


	/**
	 * Find all bookings relevant to the provided Context.
	 *
	 * Context will include things like room, dates, session.
	 *
	 * @param Context $context Populated Context instance.
	 * @return array Array of bookings
	 *
	 */
	public function find_for_context(Context $context)
	{
		$this->db->reset_query();

		$this->db->select([
			'b.booking_id',
			'b.repeat_id',
			'b.period_id',
			'b.room_id',
			'b.user_id',
			'b.date',
			'b.status',
			'b.notes',
			'p.time_start',
			'p.time_end',
		]);

		$this->db->select([
			'u.user_id AS user__user_id',
			'u.username AS user__username',
			'u.displayname AS user__displayname'
		], FALSE);

		$this->db->select([
			'r.week_id AS repeat__week_id',
			'r.weekday AS repeat__weekday',
		], FALSE);

		$this->db->select([
			'w.week_id AS repeat_week__week_id',
			'w.name AS repeat_week__name',
			'w.fgcol AS repeat_week__fgcol',
			'w.bgcol AS repeat_week__bgcol',
		], FALSE);

		$this->db->from("{$this->table} AS b");
		$this->db->join('periods p', 'period_id', 'INNER');
		$this->db->join('users u', 'user_id', 'LEFT');
		$this->db->join('bookings_repeat r', 'repeat_id', 'LEFT');
		$this->db->join('weeks w', 'week_id', 'LEFT');

		$this->db->where('b.status', self::STATUS_BOOKED);
		if ($context->session) {
			$this->db->where('b.session_id', $context->session->session_id);
		} else {
			$this->db->where('b.session_id', -1);
		}

		switch ($context->display_type) {

			case 'day':
				$this->db->where('b.date', $context->datetime->format('Y-m-d'));
				break;

			case 'room':
				$this->db->where([
					'b.room_id' => ($context->room) ? $context->room->room_id : null,
				]);
				$this->db->where([
					'b.date >=' => $context->week_start->format('Y-m-d'),
					'b.date <=' => $context->week_end->format('Y-m-d'),
				]);
				break;
		}

		$query = $this->db->get();
		$result = $query->result();

		$out = [];

		foreach ($result as &$row) {
			$key = Slot::generate_key($row->date, $row->period_id, $row->room_id);
			$out[ $key ] = $this->wake_value($row);
		}

		return $out;
	}


	/**
	 * Check various parameters of a booking creation request to ensure it can
	 * be made, no conflicts will occur and all parameters are correct.
	 *
	 */
	public function validate_booking($data)
	{
		$sql = 'SELECT booking_id
				FROM bookings
				WHERE `date` = ?
				AND period_id = ?
				AND room_id = ?
				AND status = ?
				LIMIT 1';

		$query = $this->db->query($sql, [$data['date'], $data['period_id'], $data['room_id'], self::STATUS_BOOKED]);

		$row = $query->row();

		if ($query->num_rows() === 1 && $row->booking_id) {
			throw BookingValidationException::forExistingBooking();
		}

		return TRUE;
	}


	public function create($data)
	{
		try {
			$this->validate_booking($data);
		} catch (BookingValidationException $e) {
			$this->error = $e->getMessage();
			return FALSE;
		}

		$data = $this->sleep_values($data);

		$data['created_at'] = date('Y-m-d H:i:s');
		$data['created_by'] = $this->userauth->user->user_id;

		$ins = $this->db->insert($this->table, $data);

		return ($ins && $id = $this->db->insert_id())
			? $id
			: FALSE;
	}


	public function update($booking_id, $data, $edit_mode = UpdateAgent::EDIT_ONE)
	{
		$data = $this->sleep_values($data);

		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['updated_by'] = $this->userauth->user->user_id;

		switch ($edit_mode) {

			case UpdateAgent::EDIT_ONE:

				$where = [
					'booking_id' => $booking_id,
				];

				return $this->db->update($this->table, $data, $where, 1);

				break;

			case UpdateAgent::EDIT_FUTURE:

				$booking = $this->get($booking_id);

				$where = [
					'repeat_id' => $booking->repeat_id,
					'session_id' => $booking->session_id,
					'date >=' => $booking->date->format('Y-m-d'),
				];

				return $this->db->update($this->table, $data, $where);

				break;

			case UpdateAgent::EDIT_ALL:

				$booking = $this->get($booking_id);

				$where = [
					'repeat_id' => $booking->repeat_id,
					'session_id' => $booking->session_id,
				];

				$update_bk = $this->db->update($this->table, $data, $where);

				// Update repeat table with data
				$repeat_keys = [
					'user_id',
					'department_id',
					'notes',
					'updated_at',
					'updated_by',
				];

				// Ensure we only send valid data to repeat table
				$repeat_data = [];
				foreach ($repeat_keys as $k) {
					$repeat_data[$k] = isset($booking_data[$k]) ? $booking_data[$k] : NULL;
				}

				$update_rep = $this->db->update('bookings_repeat', $repeat_data, $where);

				return ($update_bk && $update_rep);

				break;
		}

		return FALSE;
	}


	/**
	 * Cancel a single instance of a booking.
	 *
	 */
	public function cancel_single($booking_id)
	{
		$data = [
			'status' => self::STATUS_CANCELLED,
			'cancelled_at' => date('Y-m-d H:i:s'),
			'cancelled_by' => $this->userauth->user->user_id,
		];

		return $this->db->update($this->table, $data, ['booking_id' => $booking_id], 1);
	}


	/**
	 * Cancel booking + future instances in series.
	 *
	 */
	public function cancel_future($booking_id)
	{
		$booking = $this->get($booking_id);

		if ( ! $booking->repeat_id) return FALSE;

		$data = [
			'status' => self::STATUS_CANCELLED,
			'cancelled_at' => date('Y-m-d H:i:s'),
			'cancelled_by' => $this->userauth->user->user_id,
		];

		$where = [
			'repeat_id' => $booking->repeat_id,
			'session_id' => $booking->session_id,
			'date >=' => $booking->date->format('Y-m-d'),
		];

		return $this->db->update($this->table, $data, $where);
	}


	public function cancel_all($booking_id)
	{
		$booking = $this->get($booking_id);

		if ( ! $booking->repeat_id) return FALSE;

		$data = [
			'status' => self::STATUS_CANCELLED,
			'cancelled_at' => date('Y-m-d H:i:s'),
			'cancelled_by' => $this->userauth->user->user_id,
		];

		$where = [
			'repeat_id' => $booking->repeat_id,
			'session_id' => $booking->session_id,
		];

		$update1 = $this->db->update($this->table, $data, $where);

		$update2 = $this->db->update('bookings_repeat', $data, $where, 1);

		return ($update1 && $update2);
	}


	public function wake_value($row)
	{
		$row = nest_object_keys($row);

		if (isset($row->period) && is_object($row->period)) {
			$row->time_start = $row->period->time_start;
			$row->time_end = $row->period->time_end;
		}

		$datetime_value = (empty($row->time_start))
			? $row->date
			: "{$row->date} {$row->time_start}"
			;

		$row->date = datetime_from_string($datetime_value);

		if (is_object($row->date)) {
			$row->time_start = datetime_from_string(sprintf('%s %s', $row->date->format('Y-m-d'), $row->time_start));
			$row->time_end = datetime_from_string(sprintf('%s %s', $row->date->format('Y-m-d'), $row->time_end));
		}

		foreach ($this->include as $include) {

			switch ($include) {

				case 'user':
					$this->load->model('users_model');
					$this->load->model('departments_model');
					$user = $this->users_model->get_by_id($row->user_id);
					unset($user->password);
					$row->user = $user;
					if ($row->user) {
						$row->user->department = ($user->department_id)
							? $this->departments_model->Get($user->department_id)
							: false;
					}
					break;

				case 'department':
					$this->load->model('departments_model');
					$row->department = isset($row->department_id)
						? $this->departments_model->Get($row->department_id)
						: false;
					break;

				case 'room':
					$this->load->model('rooms_model');
					$room = $this->rooms_model->get_by_id($row->room_id);
					$row->room = $room;
					$row->room->info = $this->rooms_model->room_info($room);
					$row->room->fields = $this->rooms_model->GetFields();
					$row->room->fieldvalues = $this->rooms_model->GetFieldValues($room->room_id);
					break;

				case 'week':
					$this->load->model('weeks_model');
					$row->week = isset($row->week_id)
						? $this->weeks_model->get($row->week_id)
						: false;
					break;

				case 'period':
					$this->load->model('periods_model');
					$row->period = $this->periods_model->get($row->period_id);
					break;

				case 'session':
					$this->load->model('sessions_model');
					$row->session = $this->sessions_model->get($row->session_id);
					break;

				case 'repeat':
					$this->load->model('bookings_repeat_model');
					$row->repeat = $this->bookings_repeat_model->get($row->repeat_id);
					break;

			}
		}

		return $row;
	}


	public function sleep_values($data)
	{
		if (isset($data['user_id'])) {
			$data['user_id'] = (!empty($data['user_id']))
				? (int) $data['user_id']
				: NULL;
		}

		if (isset($data['department_id'])) {
			$data['department_id'] = (!empty($data['department_id']))
				? (int) $data['department_id']
				: NULL;
		}

		if (isset($data['date'])) {
			$dt = datetime_from_string($data['date']);
			$data['date'] = $dt ? $dt->format('Y-m-d') : NULL;
		}

		return $data;
	}


	/**
	 * Given a session ID, delete any existing bookings that fall outside of its date range.
	 *
	 */
	public function check_session_dates($session_id)
	{
		$session = $this->sessions_model->get($session_id);
		if ( ! $session) return FALSE;

		$sql = "DELETE FROM {$this->table}
				WHERE session_id = ?
				AND (`date` < ? OR `date` > ?)";

		$this->db->query($sql, [
			$session->session_id,
			$session->date_start->format('Y-m-d'),
			$session->date_end->format('Y-m-d'),
		]);

		return $this->db->affected_rows();
	}


	/**
	 * Delete entries for a given session.
	 *
	 */
	public function delete_by_session($session_id)
	{
		return $this->db->delete($this->table, ['session_id' => $session_id]);
		return $this->db->delete('bookings_repeat', ['session_id' => $session_id]);
	}


	function ByRoomOwner($user_id)
	{
		$date = new \DateTime();
		$start = $date->format('Y-m-d');
		$date->modify('+14 days');
		$end = $date->format('Y-m-d');

		$this->db->reset_query();

		$this->db->select([
			'b.date',
			'b.notes',
		]);

		$this->db->select([
			'p.name AS period__name',
			'p.time_start AS period__time_start',
			'p.time_end AS period__time_end',
		], FALSE);

		$this->db->select([
			'r.room_id AS room__room_id',
			'r.name AS room__name',
		], FALSE);

		$this->db->select([
			'u.user_id AS user__user_id',
			'u.username AS user__username',
			'u.displayname AS user__displayname'
		], FALSE);

		$this->db->from("{$this->table} AS b");
		$this->db->join('periods p', 'period_id', 'INNER');
		$this->db->join('rooms r', 'room_id', 'INNER');
		$this->db->join('users u', 'b.user_id = u.user_id', 'INNER');

		$this->db->where('r.user_id', $user_id);
		$this->db->where('b.user_id!=', $user_id);
		$this->db->where('b.repeat_id IS NULL');
		$this->db->where('b.status', self::STATUS_BOOKED);
		$this->db->where('b.date>=', $start);
		$this->db->where('b.date<=', $end);

		$this->db->order_by('b.date', 'ASC');
		$this->db->order_by('p.time_start', 'ASC');

		$query = $this->db->get();
		$result = $query->result();

		if ($query->num_rows() == 0) return FALSE;

		foreach ($result as &$row) {
			$row = $this->wake_value($row);
		}

		return $result;
	}




	function ByUser($user_id)
	{
		$date = new \DateTime();
		$start = $date->format('Y-m-d');
		$time = $date->format('H:i') . ':00';
		$date->modify('+14 days');
		$end = $date->format('Y-m-d');

		$this->db->reset_query();

		$this->db->select([
			'b.date',
			'b.notes',
		]);

		$this->db->select([
			'p.name AS period__name',
			'p.time_start AS period__time_start',
			'p.time_end AS period__time_end',
		], FALSE);

		$this->db->select([
			'r.room_id AS room__room_id',
			'r.name AS room__name',
		], FALSE);

		$this->db->from("{$this->table} AS b");
		$this->db->join('periods p', 'period_id', 'INNER');
		$this->db->join('rooms r', 'room_id', 'INNER');

		$this->db->where('b.user_id', $user_id);
		$this->db->where('b.repeat_id IS NULL');
		$this->db->where('b.status', self::STATUS_BOOKED);

		$this->db->where('b.date<=', $end);

		$start = $this->db->escape($start);
		$end = $this->db->escape($end);
		$time = $this->db->escape($time);
		$this->db->where("( (b.date > {$start}) OR (b.date = {$start} AND p.time_start > {$time}) )");

		$this->db->order_by('b.date', 'ASC');
		$this->db->order_by('p.time_start', 'ASC');

		$query = $this->db->get();
		$result = $query->result();

		if ($query->num_rows() == 0) return FALSE;

		foreach ($result as &$row) {
			$row = $this->wake_value($row);
		}

		return $result;
	}


	public function CountScheduledByUser($user_id)
	{
		$today = date("Y-m-d");
		$time = date('H:i') . ':00';

		$sql = 'SELECT COUNT(booking_id) AS total
				FROM bookings
				INNER JOIN periods USING (period_id)
				WHERE bookings.user_id = ?
				AND bookings.status = 10
				AND bookings.date IS NOT NULL
				AND bookings.repeat_id IS NULL
				AND (
					(bookings.date > ?)	/* after today */
					OR
					(bookings.date = ? AND periods.time_start > ?) /* today, but after cur time */
				)';

		$query = $this->db->query($sql, [
			$user_id,
			$today,
			$today,
			$time
		]);

		$row = $query->row_array();
		return (int) $row['total'];
	}


	function TotalNum($user_id = 0)
	{
		$total = [];

		// All bookings by user, EVER!
		$sql = "SELECT COUNT(booking_id) AS total
				FROM bookings
				WHERE user_id = ?";
		$query = $this->db->query($sql, [$user_id]);
		$row = $query->row_array();
		$total['all'] = (int) $row['total'];

		// All bookings by user, for the current session
		$sql = "SELECT COUNT(b.booking_id) AS total
				FROM bookings b
				JOIN sessions s USING (session_id)
				WHERE b.user_id = ?
				AND s.is_current = 1";
		$query = $this->db->query($sql, [$user_id]);
		$row = $query->row_array();
		$total['session'] = (int) $row['total'];

		// All bookings up to and including today
		// $sql = "SELECT COUNT(booking_id) AS total
		// 		FROM bookings
		// 		WHERE bookings.user_id = ?
		// 		AND bookings.date <= ?";
		// $query = $this->db->query($sql, [$user_id, $today]);
		// $row = $query->row_array();
		// $total['todate'] = $row['total'];

		$total['active'] = $this->CountScheduledByUser($user_id);

		return $total;
	}




}
