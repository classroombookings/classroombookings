<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Populate_dates_holidays extends CI_Migration
{


	public function up()
	{
		$sql = 'SELECT holidays.* FROM holidays
				INNER JOIN sessions USING (session_id)';
		$query = $this->db->query($sql);

		if ($query->num_rows() === 0) {
			return TRUE;
		}

		$date_rows = [];

		$rows = $query->result();
		$interval = new DateInterval('P1D');

		foreach ($rows as $row) {

			$start = DateTime::createFromFormat('!Y-m-d', $row->date_start);
			$end = DateTime::createFromFormat('!Y-m-d', $row->date_end);
			$end->modify('+1 day');
			$period = new DatePeriod($start, $interval, $end);

			foreach ($period as $date) {
				$date_rows[] = [
					'date' => $date->format('Y-m-d'),
					'holiday_id' => $row->holiday_id,
				];
			}

		}

		if (empty($date_rows)) {
			return TRUE;
		}

		$this->db->update_batch('dates', $date_rows, 'date');
	}


	public function down()
	{
	}


}
