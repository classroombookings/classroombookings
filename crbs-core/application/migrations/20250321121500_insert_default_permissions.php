<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Insert_default_permissions extends CI_Migration
{

	public function up()
	{
		$sql = "INSERT INTO `auth_permissions` (`name`) VALUES
			('system.bypass_maintenance_mode'),
			('system.export_bookings'),
			('system.view_all_sessions'),
			('setup.authentication'),
			('setup.departments'),
			('setup.roles'),
			('setup.rooms'),
			('setup.rooms_acl'),
			('setup.schedules'),
			('setup.sessions'),
			('setup.settings'),
			('setup.timetable_weeks'),
			('setup.users'),
			('room.view'),
			('book_single.create'),
			('book_single.edit_other_booking'),
			('book_single.cancel_other_booking'),
			('book_single.set_user'),
			('book_single.set_department'),
			('book_single.view_other_users'),
			('book_single.view_other_notes'),
			('book_recur.create'),
			('book_recur.edit_other_booking'),
			('book_recur.cancel_other_booking'),
			('book_recur.set_user'),
			('book_recur.set_department'),
			('book_recur.view_other_users'),
			('book_recur.view_other_notes')
		";

		$this->db->query($sql);
	}


	public function down()
	{
	}


}
