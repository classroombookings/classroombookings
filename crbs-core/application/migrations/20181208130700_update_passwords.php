<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Update_passwords extends CI_Migration
{


	public function up()
	{
		$this->migrate_passwords();
	}


	public function down()
	{
	}


	private function migrate_passwords()
	{
		$sql = 'SELECT user_id, password FROM users';
		$query = $this->db->query($sql);
		if ($query->num_rows() == 0) {
			return FALSE;
		}

		$result = $query->result_array();

		foreach ($result as $row) {
			if (strlen($row['password']) == 40) {
				// is sha1
				$new_password = 'sha1:' . password_hash($row['password'], PASSWORD_DEFAULT);
				$this->db->set('password', $new_password);
				$this->db->where('user_id', $row['user_id']);
				$this->db->update('users');
			}
		}
	}


}
