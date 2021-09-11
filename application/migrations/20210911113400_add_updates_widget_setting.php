<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_updates_widget_setting extends CI_Migration
{

	public function up()
	{
		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'headway_widget_enabled',
			'value' => '1',
		]);
	}


	public function down()
	{
	}


}
