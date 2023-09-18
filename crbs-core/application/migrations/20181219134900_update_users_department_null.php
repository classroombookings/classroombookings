<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_users_department_null extends CI_Migration
{


	public function up()
	{
		$fields = array(
			'department_id' => array(
				'name' => 'department_id',
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
			),
		);

		$this->dbforge->modify_column('users', $fields);
	}


	public function down()
	{
	}


}
