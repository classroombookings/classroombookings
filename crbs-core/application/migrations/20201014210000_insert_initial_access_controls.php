<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_insert_initial_access_controls extends CI_Migration
{


	private $table_name = 'access_control';


	public function up()
	{
		$sql = "INSERT INTO access_control
				(target, target_id, actor, actor_id, permission, reference)
				SELECT 'R', room_id, 'A', NULL, 'view', CONCAT('R', room_id, '.', 'A', '.', 'view')
				FROM rooms";
		$this->db->query($sql);
	}


	public function down()
	{
	}


}
