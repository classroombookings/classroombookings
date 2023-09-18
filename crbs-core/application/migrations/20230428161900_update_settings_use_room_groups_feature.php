<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_settings_use_room_groups_feature extends CI_Migration
{


	public function up()
	{
		$data = ['group' => 'features', 'name' => 'room_groups', 'value' => '1'];
		$this->db->replace('settings', $data);
	}


	public function down()
	{
	}

}
