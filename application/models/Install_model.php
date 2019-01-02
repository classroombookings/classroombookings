<?php

class Install_model extends CI_Model
{


	private $migration_version = '20181219134900';


	private function get_attributes()
	{
		return array('ENGINE' => 'InnoDB');
	}


	public function run()
	{
		$status = array();
		$errors = array();

		$methods = array(
			'academicyears',
			'bookings',
			'departments',
			'holidays',
			'periods',
			'roomfields',
			'roomoptions',
			'rooms',
			'roomvalues',
			'settings',
			'users',
			'weekdates',
			'weeks',
			'migrations',
		);

		foreach ($methods as $method) {
			$name = "install_{$method}";
			$status[ $method ] = $this->$name();
			$err = $this->db->error()['message'];
			if ($err) {
				$errors[ $method ] = "{$method}: $err";
			}
		}

		return $errors;
	}


	private function install_migrations()
	{
		$this->dbforge->add_field(array(
			'version' => array('type' => 'BIGINT', 'constraint' => 20),
		));

		$this->dbforge->create_table('migrations', TRUE);

		return $this->db->insert('migrations', array('version' => $this->migration_version));
	}


	private function install_academicyears()
	{
		$fields = array(
			'date_start' => array('type' => 'date', 'null' => FALSE),
			'date_end' => array('type' => 'date', 'null' => FALSE),
		);

		$this->dbforge->add_field($fields);

		return $this->dbforge->create_table('academicyears', TRUE, $this->get_attributes());
	}


	private function install_bookings()
	{
		$fields = array(
			'booking_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'period_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE),
			'week_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => TRUE),
			'day_num' => array('type' => 'TINYINT', 'constraint' => 1, 'unsigned' => TRUE, 'null' => TRUE),
			'room_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE),
			'user_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => TRUE),
			'date' => array('type' => 'DATE', 'null' => TRUE),
			'notes' => array('type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE),
			'cancelled' => array('type' => 'TINYINT', 'constraint' => 1, 'unsigned' => TRUE, 'null' => FALSE, 'default' => '0'),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('booking_id', TRUE);
		$this->dbforge->add_key(array('period_id', 'room_id', 'user_id'));

		return $this->dbforge->create_table('bookings', TRUE, $this->get_attributes());
	}


	private function install_departments()
	{
		$fields = array(
			'department_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'name' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE),
			'description' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'icon' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('department_id', TRUE);

		return $this->dbforge->create_table('departments', TRUE, $this->get_attributes());
	}


	private function install_holidays()
	{
		$fields = array(
			'holiday_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'name' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE),
			'date_start' => array('type' => 'DATE', 'null' => FALSE),
			'date_end' => array('type' => 'DATE', 'null' => FALSE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('holiday_id', TRUE);

		return $this->dbforge->create_table('holidays', TRUE, $this->get_attributes());
	}


	private function install_periods()
	{
		$fields = array(
			'period_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'time_start' => array('type' => 'TIME', 'null' => FALSE),
			'time_end' => array('type' => 'TIME', 'null' => FALSE),
			'name' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => FALSE),
			'days' => array('type' => 'INT', 'constraint' => 2, 'unsigned' => TRUE, 'null' => FALSE),
			'bookable' => array('type' => 'TINYINT', 'constraint' => 1, 'unsigned' => TRUE, 'null' => FALSE, 'default' => 0),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('period_id', TRUE);

		return $this->dbforge->create_table('periods', TRUE, $this->get_attributes());
	}


	private function install_roomfields()
	{
		$fields = array(
			'field_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'name' => array('type' => 'VARCHAR', 'constraint' => 64, 'null' => TRUE),
			'type' => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('field_id', TRUE);

		return $this->dbforge->create_table('roomfields', TRUE, $this->get_attributes());
	}


	private function install_roomoptions()
	{
		$fields = array(
			'option_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'field_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE),
			'value' => array('type' => 'VARCHAR', 'constraint' => 64, 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('option_id', TRUE);

		return $this->dbforge->create_table('roomoptions', TRUE, $this->get_attributes());
	}


	private function install_rooms()
	{
		$fields = array(
			'room_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'user_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => TRUE),
			'name' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => FALSE),
			'location' => array('type' => 'VARCHAR', 'constraint' => 40, 'null' => TRUE),
			'bookable' => array('type' => 'TINYINT', 'constraint' => 1, 'unsigned' => TRUE, 'null' => FALSE, 'default' => 0),
			'icon' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'notes' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'photo' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('room_id', TRUE);
		$this->dbforge->add_key('user_id');

		return $this->dbforge->create_table('rooms', TRUE, $this->get_attributes());
	}


	private function install_roomvalues()
	{
		$fields = array(
			'value_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'room_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE),
			'field_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE),
			'value' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('value_id', TRUE);

		return $this->dbforge->create_table('roomvalues', TRUE, $this->get_attributes());
	}


	private function install_settings()
	{
		$fields = array(
			'group' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE),
			'name' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE),
			'value' => array('type' => 'TEXT', 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$res = $this->dbforge->create_table('settings', TRUE, $this->get_attributes());

		$this->db->query("ALTER TABLE `settings` ADD UNIQUE `group_name` (`group`, `name`)");

		return $res;
	}


	private function install_users()
	{
		$fields = array(
			'user_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'department_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => TRUE),
			'username' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE),
			'firstname' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'lastname' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'email' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'password' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'authlevel' => array('type' => 'TINYINT', 'constraint' => 1, 'unsigned' => TRUE, 'null' => FALSE),
			'displayname' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'ext' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
			'lastlogin' => array('type' => 'DATETIME', 'null' => TRUE),
			'enabled' => array('type' => 'TINYINT', 'constraint' => 1, 'unsigned' => TRUE, 'null' => FALSE, 'default' => '1'),
			'created' => array('type' => 'DATETIME', 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('user_id', TRUE);
		$this->dbforge->add_key('authlevel');
		$this->dbforge->add_key('enabled');

		return $this->dbforge->create_table('users', TRUE, $this->get_attributes());
	}


	private function install_weekdates()
	{
		$fields = array(
			'week_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => FALSE),
			'date' => array('type' => 'DATE', 'null' => FALSE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('week_id');

		return $this->dbforge->create_table('weekdates', TRUE, $this->get_attributes());
	}


	private function install_weeks()
	{
		$fields = array(
			'week_id' => array('type' => 'INT', 'constraint' => 6, 'unsigned' => TRUE, 'null' => FALSE, 'auto_increment' => TRUE),
			'name' => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => FALSE),
			'fgcol' => array('type' => 'CHAR', 'constraint' => 6, 'null' => TRUE),
			'bgcol' => array('type' => 'CHAR', 'constraint' => 6, 'null' => TRUE),
			'icon' => array('type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE),
		);

		$this->dbforge->add_field($fields);

		$this->dbforge->add_key('week_id', TRUE);

		return $this->dbforge->create_table('weeks', TRUE, $this->get_attributes());
	}


}
