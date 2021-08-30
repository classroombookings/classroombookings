<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Cancel_recurring_conflicts extends CI_Migration
{

	public function up()
	{
		$sql = "UPDATE bookings b
				INNER JOIN (
					SELECT b1.booking_id
					FROM bookings b1
					INNER JOIN bookings b2
						ON b2.session_id = b1.session_id
						AND b2.date = b1.date
						AND b2.period_id = b1.period_id
						AND b2.room_id = b1.room_id
					WHERE b1.booking_id <> b2.booking_id
				) dups ON dups.booking_id = b.booking_id
				SET b.status = 15
				WHERE b.repeat_id IS NOT NULL";

		$this->db->query($sql);
	}


	public function down()
	{
	}


}
