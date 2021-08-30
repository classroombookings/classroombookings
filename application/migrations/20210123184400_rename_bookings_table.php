<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Rename_bookings_table extends CI_Migration
{


	public function up()
	{
		$this->dbforge->rename_table('bookings', 'bookings_legacy');
	}


	public function down()
	{
	}


}
