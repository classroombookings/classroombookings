<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_auth_tables extends CI_Migration
{

	public function up()
	{
		$this->create_roles();
		$this->create_permissions();
		$this->create_roles_permissions();
		$this->create_acl();
		$this->create_acl_permissions();
	}



	public function down()
	{
	}


	private function create_roles()
	{
		$sql = "CREATE TABLE `auth_roles` (
			`role_id` int unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
			`description` text COLLATE utf8mb4_unicode_ci,
			`max_active_bookings` int unsigned DEFAULT NULL,
			`range_min` int unsigned DEFAULT NULL,
			`range_max` int unsigned DEFAULT NULL,
			`recur_max_instances` int unsigned DEFAULT NULL,
			PRIMARY KEY (`role_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

		$this->db->query($sql);
	}


	private function create_permissions()
	{
		$sql = "CREATE TABLE `auth_permissions` (
			`permission_id` int unsigned NOT NULL AUTO_INCREMENT,
			`name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
			PRIMARY KEY (`permission_id`),
			UNIQUE KEY `uniq_permission_name` (`name`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

		$this->db->query($sql);
	}


	private function create_roles_permissions()
	{
		$sql = "CREATE TABLE `auth_roles_permissions` (
			`role_id` int unsigned NOT NULL,
			`permission_id` int unsigned NOT NULL,
			PRIMARY KEY (`role_id`,`permission_id`),
			KEY `permission_id` (`permission_id`),
			CONSTRAINT `fk_role_permission_role` FOREIGN KEY (`role_id`) REFERENCES `auth_roles` (`role_id`) ON DELETE CASCADE,
			CONSTRAINT `fk_role_permission_permission` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`permission_id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

		$this->db->query($sql);
	}


	private function create_acl()
	{
		$sql = "CREATE TABLE `auth_acl` (
			`acl_id` int unsigned NOT NULL AUTO_INCREMENT,
			`entity_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
			`entity_id` int unsigned NOT NULL,
			`context_type` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
			`context_id` int unsigned NOT NULL,
			PRIMARY KEY (`acl_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

		$this->db->query($sql);
	}


	private function create_acl_permissions()
	{
		$sql = "CREATE TABLE `auth_acl_permissions` (
			`acl_id` int unsigned NOT NULL,
			`permission_id` int unsigned NOT NULL,
			PRIMARY KEY (`acl_id`,`permission_id`),
			KEY `permission_id` (`permission_id`),
			CONSTRAINT `fk_acl_permissions_acl` FOREIGN KEY (`acl_id`) REFERENCES `auth_acl` (`acl_id`) ON DELETE CASCADE,
			CONSTRAINT `fk_acl_permissions_permission` FOREIGN KEY (`permission_id`) REFERENCES `auth_permissions` (`permission_id`) ON DELETE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

		$this->db->query($sql);
	}


}
