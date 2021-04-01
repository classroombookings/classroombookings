<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_sessions_set_current extends CI_Migration
{

	public function up()
	{
		$today = date('Y-m-d');

		$sql = "UPDATE `sessions` SET is_current = 0;";
		$this->db->query($sql);

		$sql = "UPDATE `sessions` SET is_current = 1
				WHERE date_start <= ? AND date_end >= ?
				LIMIT 1";
		$this->db->query($sql, [ $today, $today ]);
	}


	public function down()
	{
	}


}
