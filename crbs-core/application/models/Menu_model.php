<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends CI_Model
{


	/**
	 * For header/footer in page.
	 *
	 */
	public function global()
	{
		$items = [];

		if ( ! $this->userauth->logged_in()) {
			return $items;
		}

		$items[] = [
			'label' => lang('booking.bookings'),
			'url' => site_url('bookings'),
			'icon' => 'calendar.png',
		];

		if (has_setup_permission()) {
			$items[] = [
				'label' => lang('setup.setup'),
				'url' => site_url('setup'),
				'icon' => 'school_manage_settings.png',
			];
		}

		$display = $this->userauth->user->displayname;
		$label = (!empty($display))
			? $this->userauth->user->displayname
			: $this->userauth->user->username;
		$items[] = [
			'label' => $label,	// 'Account',
			'url' => site_url('profile/edit'),
			'icon' => 'user.png',
		];

		$items[] = [
			'label' => lang('auth.log_out'),
			'url' => site_url('logout'),
			'icon' => 'logout.png',
		];

		return $items;
	}


	public function setup_menu()
	{
		$items = [];

		if (has_permission(Permission::SETUP_SETTINGS)) {
			$items['setup'][] = [
				'label' => lang('settings.settings'),
				'icon' => 'school_manage_settings.png',
				'url' => site_url('settings/general'),
			];
			$items['setup'][] = [
				'label' => lang('organisation.organisation'),
				'icon' => 'school_manage_details.png',
				'url' => site_url('settings/organisation'),
			];
			$items['setup'][] = [
				'label' => lang('language.language'),
				'icon' => 'world.png',
				'url' => site_url('setup/language'),
			];
		}

		if (has_permission(Permission::SETUP_SESSIONS)) {
			$items['timetable'][] = [
				'label' => lang('session.sessions'),
				'icon' => 'calendar_view_month.png',
				'url' => site_url('sessions'),
			];
		}

		if (has_permission(Permission::SETUP_SCHEDULES)) {
			$items['timetable'][] = [
				'label' => lang('schedule.schedules'),
				'icon' => 'school_manage_times.png',
				'url' => site_url('schedules'),
			];
		}

		if (has_permission(Permission::SETUP_TIMETABLE_WEEKS)) {
			$items['timetable'][] = [
				'label' => lang('week.timetable_weeks'),
				'icon' => 'school_manage_weeks.png',
				'url' => site_url('weeks'),
			];
		}

		if (has_permission(Permission::SETUP_ROOMS) || has_permission(Permission::SETUP_ROOMS_ACL)) {
			$items['resources'][] = [
				'label' => lang('room.rooms'),
				'icon' => 'school_manage_rooms.png',
				'url' => site_url('setup/rooms/groups'),
			];
			$items['resources'][] = [
				'label' => lang('custom_field.custom_fields'),
				'icon' => 'room_fields.png',
				'url' => site_url('setup/rooms/fields'),
			];
			$items['resources'][] = [
				'label' => lang('acl.access_checker'),
				'icon' => 'eye.png',
				'url' => site_url('setup/access_checker'),
			];
		}

		if (has_permission(Permission::SETUP_USERS)) {
			$items['users'][] = [
				'label' => lang('user.users'),
				'icon' => 'school_manage_users.png',
				'url' => site_url('users'),
			];
		}

		if (has_permission(Permission::SETUP_ROLES)) {
			$items['users'][] = [
				'label' => lang('role.roles'),
				'icon' => 'vcard_key.png',
				'url' => site_url('roles'),
			];
		}

		if (has_permission(Permission::SETUP_DEPARTMENTS)) {
			$items['users'][] = [
				'label' => lang('department.departments'),
				'icon' => 'school_manage_departments.png',
				'url' => site_url('departments'),
			];
		}

		if (has_permission(Permission::SETUP_AUTHENTICATION)) {
			$items['users'][] = [
				'label' => lang('auth.authentication'),
				'icon' => 'lock.png',
				'url' => site_url('settings/authentication/ldap'),
			];
		}

		if (has_permission(Permission::SYS_EXPORT_BOOKINGS)) {
			$items['setup'][] = [
				'label' => lang('export.export'),
				'icon' => 'table.png',
				'url' => site_url('export'),
			];
		}

		return $items;
	}


}
