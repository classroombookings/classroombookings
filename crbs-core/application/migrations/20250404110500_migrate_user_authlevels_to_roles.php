<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_user_authlevels_to_roles extends CI_Migration
{

	public function up()
	{
		$sql = "UPDATE users SET role_id = 1 WHERE authlevel = 1 AND role_id IS NULL";
		$this->db->query($sql);

		$sql = "UPDATE users SET role_id = 2 WHERE authlevel = 2 AND role_id IS NULL";
		$this->db->query($sql);
	}


	public function down()
	{
	}

}
