<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Populate the 'dates' table from the session date range.
 * Also include the week_id where relevant.
 *
 */
class Migration_Migrate_dates_data extends CI_Migration
{

	private $weeks_by_date;


	public function up()
	{
		$sql = 'SELECT * FROM sessions';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) return true;

		$this->weeks_by_date = $this->get_weeks_by_date();

		foreach ($query->result() as $row) {
			$this->process_session($row);
		}

		return true;
	}


	private function process_session($session)
	{
		$rows = [];

		$date_start = DateTime::createFromFormat('!Y-m-d', $session->date_start);
		$date_end = DateTime::createFromFormat('!Y-m-d', $session->date_end);
		$date_end->modify('+1 day');
		$interval = new DateInterval('P1D');
		$period = new DatePeriod($date_start, $interval, $date_end);

		foreach ($period as $date) {

			$date_value = $this->db->escape($date->format('Y-m-d'));
			$weekday = $date->format('N');

			$date_ymd = $date->format('Y-m-d');

			// Find week ID
			$week_id = (isset($this->weeks_by_date[$date_ymd]))
				? $this->weeks_by_date[$date_ymd]
				: 'NULL';

			$str = sprintf('(%s, %d, %d, %s)', $date_value, $weekday, $session->session_id, $week_id);

			$rows[] = $str;

		}

		if (empty($rows)) return true;

		$values = implode(',', $rows);

		$sql = "INSERT INTO dates
				(`date`, `weekday`, `session_id`, `week_id`)
				VALUES {$values}
				ON DUPLICATE KEY UPDATE
					`date` = VALUES(`date`),
					`weekday` = VALUES(`weekday`),
					`session_id` = VALUES(`session_id`),
					`week_id` = VALUES(`week_id`)
				";

		$this->db->query($sql);
	}



	/**
	 * Get all current week associations, indexed by the monday date.
	 *
	 */
	private function get_weeks_by_date()
	{
		$weeks_by_date = [];

		// Get all weekdates that have a valid week
		$sql = 'SELECT * FROM weekdates
				INNER JOIN weeks USING (week_id)';
		$query = $this->db->query($sql);

		if ($query->num_rows() === 0) return [];

		foreach ($query->result() as $row) {

			$days = 0;
			$dt = DateTime::createFromFormat('!Y-m-d', $row->date);

			// Dates for the whole of the week
			//

			while ($days < 7) {
				$dt_ymd = $dt->format('Y-m-d');
				$weeks_by_date[ $dt_ymd ] = $row->week_id;
				$dt->modify('+1 day');
				$days++;
			}

			// $weeks_by_date[ $row->date ] = $row->week_id;
		}

		return $weeks_by_date;
	}



	public function down()
	{
	}


}
