<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Alter_holidays_add_session extends CI_Migration
{


	public function up()
	{
		$fields = [
			'session_id' => [
				'type' => 'INT',
				'constraint' => 6,
				'unsigned' => TRUE,
				'null' => TRUE,
				'after' => 'holiday_id',
			],
		];

		$this->dbforge->add_column('holidays', $fields);
	}


	public function down()
	{
	}


}
