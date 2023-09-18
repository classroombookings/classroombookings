<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_setting_bookings_show_name extends CI_Migration
{

	public function up()
	{
		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'bookings_show_user_recurring',
			'value' => '1',
		]);

		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'bookings_show_user_single',
			'value' => '1',
		]);
	}


	public function down()
	{
	}


}
