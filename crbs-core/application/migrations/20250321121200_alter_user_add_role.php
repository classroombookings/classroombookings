<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_user_add_role extends CI_Migration
{

	public function up()
	{
		$sql = "ALTER TABLE `users` ADD `role_id` int unsigned NULL AFTER `user_id`";
		$this->db->query($sql);
	}



	public function down()
	{
	}


}
