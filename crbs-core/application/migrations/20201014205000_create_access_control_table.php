<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_access_control_table extends CI_Migration
{


	private $table_name = 'access_control';


	public function up()
	{
		$this->create_table();
		$this->add_indexes();
	}


	public function down()
	{
	}


	private function create_table()
	{
		$fields = array(
			'id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'target' => array('type' => 'CHAR', 'constraint' => 1, 'null' => FALSE),
			'target_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE),
			'actor' => array('type' => 'CHAR', 'constraint' => 1, 'null' => FALSE),
			'actor_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => TRUE),
			'permission' => array('type' => 'VARCHAR', 'constraint' => 32, 'null' => FALSE),
			'reference' => array('type' => 'CHAR', 'constraint' => 56, 'null' => FALSE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table($this->table_name, TRUE, array('ENGINE' => 'InnoDB'));
	}


	private function add_indexes()
	{
		$sql = "ALTER TABLE `{$this->table_name}` ADD INDEX `idx_target_id` (`target`, `target_id`)";
		$this->db->query($sql);

		$sql = "ALTER TABLE `{$this->table_name}` ADD INDEX `idx_actor_id` (`actor`, `actor_id`)";
		$this->db->query($sql);

		$sql = "ALTER TABLE `{$this->table_name}` ADD INDEX `idx_permission` (`permission`)";
		$this->db->query($sql);

		$sql = "ALTER TABLE `{$this->table_name}` ADD UNIQUE INDEX `idx_reference` (`reference`)";
		$this->db->query($sql);
	}


}
