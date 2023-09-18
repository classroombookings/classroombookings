<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_settings extends CI_Migration
{


	public function up()
	{
		$this->create_table();
		$this->migrate_settings();
	}


	public function down()
	{
	}


	private function create_table()
	{
		$fields = array(
			'group' => array('type' => 'varchar', 'constraint' => 50, 'null' => FALSE),
			'name' => array('type' => 'varchar', 'constraint' => 50, 'null' => FALSE),
			'value' => array('type' => 'text', 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->create_table('settings', TRUE, array('ENGINE' => 'InnoDB'));

		$this->db->query("ALTER TABLE `settings` ADD UNIQUE `group_name` (`group`, `name`)");
	}


	private function migrate_settings()
	{
		$query_str = "SELECT * FROM school LIMIT 1";

		$query = $this->db->query($query_str);
		if ($query->num_rows() == 0) {
			return FALSE;
		}

		$row = $query->row();
		if (empty($row)) {
			return FALSE;
		}

		$group = 'crbs';

		$keys = array(
			'name',
			'website',
			'colour',
			'logo',
			'bia',
			'd_columns',
			'displaytype',
		);

		$data = array();

		foreach ($keys as $key)
		{
			$data[] = array(
				'group' => 'crbs',
				'name' => $key,
				'value' => $row->$key,
			);
		}

		$this->db->insert_batch('settings', $data);
	}


}
