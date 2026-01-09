<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_users_constraints_table extends CI_Migration
{

	public function up()
	{
		$sql = "CREATE TABLE `users_constraints` (
			`user_id` int unsigned NOT NULL,
			`max_active_bookings_type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'R',
			`max_active_bookings_value` int unsigned DEFAULT NULL,
			`range_min_type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'R',
			`range_min_value` int unsigned DEFAULT NULL,
			`range_max_type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'R',
			`range_max_value` int unsigned DEFAULT NULL,
			`recur_max_instances_type` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'R',
			`recur_max_instances_value` int unsigned DEFAULT NULL,
			PRIMARY KEY (`user_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

		$this->db->query($sql);
	}


	public function down()
	{
	}


}
