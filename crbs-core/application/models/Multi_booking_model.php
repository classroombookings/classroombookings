<?php

// use app\components\bookings\Context;
use app\components\bookings\Slot;
// use app\components\bookings\exceptions\BookingValidationException;


class Multi_booking_model extends CI_Model
{


	protected $table = 'multi_bookings';

	// Error message
	private $error = FALSE;


	public function __construct()
	{
		$this->cleanup();
	}


	/**
	 * Housekeeping routine to clear out any entries that weren't deleted after
	 * successful booking creation.
	 *
	 */
	public function cleanup()
	{
		if ($this->userauth->user->user_id) {
			$user_id = $this->userauth->user->user_id;
			$last_time = (new DateTime())->modify('-6 hours');
			$this->db->delete($this->table, [
				'user_id' => $user_id,
				'created_at<=' => $last_time->format('Y-m-d H:i:s'),
			]);
		}
	}


	public function delete($mb_id)
	{
		$this->db->delete($this->table, [
			'mb_id' => $mb_id,
		]);
	}


	public function get_error()
	{
		return $this->error;
	}


	/**
	 * Get a multi-booking entry by ID and user ID.
	 *
	 */
	public function get($mb_id, $user_id)
	{
		$this->db->reset_query();

		$this->db->select([
			'mb.*',
			'w.name AS week__name',
			'w.bgcol AS week__bgcol',
		]);

		$this->db->from("{$this->table} mb");
		$this->db->join('weeks w', 'week_id', 'LEFT');

		$this->db->where('mb_id', $mb_id);
		$this->db->where('user_id', $user_id);
		$this->db->limit(1);

		$query = $this->db->get();

		if ($query->num_rows() === 0) return FALSE;

		$row = nest_object_keys($query->row());

		$slots = $this->get_slots($row);
		$row->slots = $slots;

		return $row;
	}


	/**
	 * Get the selected slots for a given Multi-booking result row.
	 *
	 */
	public function get_slots($mb)
	{
		$this->load->model('dates_model');

		$this->db->reset_query();

		$this->db->select([
			'mbs.*',
			'p.name AS period__name',
			'r.name AS room__name',
			'd.weekday AS weekday',
		]);

		$this->db->from("multi_bookings_slots mbs");
		$this->db->join('periods p', 'period_id', 'INNER');
		$this->db->join('rooms r', 'room_id', 'INNER');
		$this->db->join('dates d', 'date', 'INNER');

		$this->db->where('mb_id', $mb->mb_id);

		$query = $this->db->get();
		$result = $query->result();

		$out = [];

		foreach ($result as &$row) {

			$key = Slot::generate_key($row->date, $row->period_id, $row->room_id);
			$row->datetime = datetime_from_string($row->date);

			// Get the potential recurring dates for this slot
			$recurring_dates = $this->dates_model->get_recurring_dates($mb->session_id, $mb->week_id, $row->weekday);
			$row->recurring_dates = $recurring_dates;

			$out[ $key ] = nest_object_keys($row);
		}

		return $out;
	}


	/**
	 * Create a new multi-booking entry.
	 *
	 */
	public function create($data)
	{
		$slots = isset($data['slots']) ? $data['slots'] : [];

		if (empty($slots)) {
			$this->error = 'No slots provided.';
			return FALSE;
		}

		unset($data['slots']);

		if ( ! isset($data['user_id'])) {
			$data['user_id'] = $this->userauth->user->user_id;
		}

		$data['created_at'] = date('Y-m-d H:i:s');

		$this->db->trans_start();

		$ins = $this->db->insert($this->table, $data);

		if ( ! $ins) return FALSE;

		$id = $this->db->insert_id();

		// Insert slot rows
		//

		$rows = [];

		foreach ($slots as $slot) {
			$rows[] = [
				'mb_id' => $id,
				'date' => $slot['date'],
				'period_id' => $slot['period_id'],
				'room_id' => $slot['room_id'],
			];
		}

		$this->db->insert_batch('multi_bookings_slots', $rows);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) return FALSE;

		return $id;
	}



}
