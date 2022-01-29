<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Migrate_years_data extends CI_Migration
{


	public function up()
	{
		$this->migrate_years_data();
	}


	public function down()
	{
	}


	private function migrate_years_data()
	{
		$sql = "INSERT INTO sessions
				(name, date_start, date_end)
				SELECT CONCAT(YEAR(date_start), ' - ', YEAR(date_end)), date_start, date_end
				FROM academicyears
				WHERE date_start IS NOT NULL AND date_end IS NOT NULL";
		$this->db->query($sql);
	}


}
