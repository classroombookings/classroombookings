<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_setting_date_format extends CI_Migration
{

	public function up()
	{
		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'date_format_long',
			'value' => 'l jS F Y',
		]);

		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'date_format_weekday',
			'value' => 'jS M',
		]);

		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'time_format_period',
			'value' => 'g:i',
		]);
	}


	public function down()
	{
	}


}
