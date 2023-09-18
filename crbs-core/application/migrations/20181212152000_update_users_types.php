<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_users_types extends CI_Migration
{


	public function up()
	{
		$fields = array(
			'username' => array(
				'name' => 'username',
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'firstname' => array(
				'name' => 'firstname',
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'lastname' => array(
				'name' => 'lastname',
				'type' => 'VARCHAR',
				'constraint' => '255',
			),
			'email' => array(
				'name' => 'email',
				'type' => 'VARCHAR',
				'constraint' => '255',
				'null' => TRUE,
			),
			'authlevel' => array(
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
			),
			'displayname' => array(
				'name' => 'displayname',
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			),
			'ext' => array(
				'name' => 'ext',
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => TRUE,
			),
			'lastlogin' => array(
				'name' => 'lastlogin',
				'type' => 'DATETIME',
				'null' => TRUE,
			),
			'enabled' => array(
				'name' => 'enabled',
				'type' => 'TINYINT',
				'constraint' => 1,
				'unsigned' => TRUE,
				'null' => FALSE,
				'default' => '1',
			),
			'created' => array(
				'name' => 'created',
				'type' => 'DATETIME',
				'null' => TRUE,
			),
		);

		$this->dbforge->modify_column('users', $fields);
	}


	public function down()
	{
	}


}
