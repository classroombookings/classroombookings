<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_dates_data extends CI_Migration
{


	public function up()
	{
		// Get all weekdates that have a valid week
		$sql = 'SELECT * FROM weekdates
				INNER JOIN weeks USING (week_id)';
		$query = $this->db->query($sql);

		if ($query->num_rows() === 0) {
			return true;
		}

		$rows = $query->result();

		$dates = [];

		foreach ($rows as $row) {

			$dt = DateTime::createFromFormat('!Y-m-d', $row->date);
			$days = 0;

			while ($days < 7) {
				// Add all dates for the rest of the week
				$dates[] = [
					'date' => $dt->format('Y-m-d'),
					'weekday' => $dt->format('N'),
					'week_id' => $row->week_id,
				];
				$dt->modify('+1 day');
				$days++;
			}
		}
		// Add all date rows that already exist in weekdates
		$this->db->insert_batch('dates', $dates, 'date');

		// Update the rows with the matching session
		$sql = 'UPDATE dates
				SET session_id = (
					SELECT session_id
					FROM sessions
					WHERE date_start <= dates.date AND date_end >= dates.date
					LIMIT 1
				)
				WHERE dates.session_id IS NULL
				';
		$this->db->query($sql);

		// Delete any orphaned dates that don't match a session
		$sql = 'DELETE FROM dates WHERE session_id IS NULL';
		$this->db->query($sql);
	}


	public function down()
	{
	}


}
