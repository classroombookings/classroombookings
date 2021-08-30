<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_sessions_set_selectable extends CI_Migration
{

	public function up()
	{
		$today = date('Y-m-d');

		$sql = "UPDATE `sessions` SET is_selectable = 1
				WHERE (date_start <= ? AND date_end >= ?)
				OR (date_start > ?)";
		$this->db->query($sql, [ $today, $today, $today ]);
	}


	public function down()
	{
	}


}
