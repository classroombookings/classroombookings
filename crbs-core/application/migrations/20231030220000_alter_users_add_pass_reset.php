<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_users_add_pass_reset extends CI_Migration
{

	public function up()
	{
		$fields = [
			'force_password_reset' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => '0',
			],
		];

		$this->dbforge->add_column('users', $fields);
	}



	public function down()
	{
	}


}
