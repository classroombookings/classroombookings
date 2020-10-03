<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_setting_login_message extends CI_Migration
{

	public function up()
	{
		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'login_message_enabled',
			'value' => '0',
		]);

		$this->db->insert('settings', [
			'group' => 'crbs',
			'name' => 'login_message_text',
			'value' => '',
		]);
	}


	public function down()
	{
	}


}
