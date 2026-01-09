<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_users_drop_authlevel extends CI_Migration
{


	public function up()
	{
		$this->dbforge->drop_column('users', 'authlevel');
	}


	public function down()
	{
	}




}
