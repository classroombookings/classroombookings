<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_remove_legacy_bypass_rac_permission extends CI_Migration
{


	public function up()
	{
		$sql = "DELETE FROM auth_permissions WHERE name = 'system.bypass_room_access'";
		$this->db->query($sql);
	}


	public function down()
	{
	}




}
