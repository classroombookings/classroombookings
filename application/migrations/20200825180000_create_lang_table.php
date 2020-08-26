<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_lang_table extends CI_Migration
{


	public function up()
	{
		$this->create_table();
	}


	public function down()
	{
	}


	private function create_table()
	{
		$fields = array(
			'id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'language' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE, 'default' => 'english'),
			'set' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE),
			'key' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE),
			'text' => array('type' => 'TEXT', 'null' => FALSE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key(array('language', 'set'));
		$this->dbforge->create_table('lang', TRUE, array('ENGINE' => 'InnoDB'));
	}

}
