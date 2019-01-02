<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_users extends CI_Migration
{


	public function up()
	{
		$fields = array(
			'password' => array(
				'name' => 'password',
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
		);

		$this->dbforge->modify_column('users', $fields);
	}


	public function down()
	{
	}


}
