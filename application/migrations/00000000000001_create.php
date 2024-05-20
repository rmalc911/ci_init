<?php

defined('BASEPATH') or exit('No direct script access allowed');
// File name has to match datetime regex pattern /^\d{14}_(\w+)$/

class Migration_Create extends CI_Migration {

	public function up() {
		$this->db->query(
			"CREATE TABLE `users` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`user_name` VARCHAR(50) NOT NULL,
				`user_id` INT NOT NULL,
				`display_name` VARCHAR(250) NOT NULL,
				`user_mobile` VARCHAR(50) NOT NULL,
				`user_email` VARCHAR(100) NULL DEFAULT NULL,
				`user_type` VARCHAR(50) NULL DEFAULT NULL,
				`user_status` ENUM('1', '0') NOT NULL DEFAULT '1',
				`created_date` DATETIME NULL DEFAULT NULL,
				`updated_date` DATETIME NULL DEFAULT NULL,
				`login_password` VARCHAR(250) NOT NULL,
				`last_login` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE = InnoDB;"
		);
		$this->db->query(
			"CREATE TABLE `user_access_map` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`user` INT NOT NULL,
				`page` VARCHAR(150) NOT NULL,
				`view_data` enum('1', '0') NOT NULL DEFAULT '0',
				`add_data` enum('1', '0') NOT NULL DEFAULT '0',
				`edit_data` enum('1', '0') NOT NULL DEFAULT '0',
				`block_data` enum('1', '0') NOT NULL DEFAULT '0',
				`delete_data` enum('1', '0') NOT NULL DEFAULT '0',
				`updated_date` DATETIME NOT NULL,
				`updated_by` INT NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `user` (`user`, `page`),
				KEY `updated_by` (`updated_by`)
			) ENGINE = InnoDB;"
		);
		$this->db->query(
			"CREATE TABLE `admin_config` (
				`config_key` VARCHAR(50) NOT NULL,
				`config_value` TEXT NOT NULL,
				`config_date` DATETIME NOT NULL,
				`config_user` INT NOT NULL,
				PRIMARY KEY (`config_key`)
			) ENGINE = InnoDB;"
		);
		$this->db->query(
			"CREATE TABLE `contact_us` (
				`id` INT NOT NULL AUTO_INCREMENT,
				`contact_name` VARCHAR(250) NOT NULL,
				`contact_email` VARCHAR(250) NOT NULL,
				`contact_phone` VARCHAR(50) NOT NULL,
				`contact_subject` VARCHAR(250) NOT NULL,
				`contact_message` VARCHAR(500) NOT NULL,
				`submit_page` VARCHAR(50) NOT NULL,
				`contact_date` DATETIME NOT NULL,
				`contact_ip` VARCHAR(50) NOT NULL,
				PRIMARY KEY (`id`)
			) ENGINE = InnoDB;"
		);
		$this->db->query(
			"CREATE TABLE `contact_social_links` (
				`social_icon_class` VARCHAR(50) NOT NULL,
				`social_icon_url` VARCHAR(50) NOT NULL,
				PRIMARY KEY (`social_icon_class`, `social_icon_url`)
			) ENGINE = InnoDB;"
		);
	}

	public function down() {
		$this->dbforge->drop_table('users');
		$this->dbforge->drop_table('user_access_map');
		$this->dbforge->drop_table('admin_config');
		$this->dbforge->drop_table('contact_us');
		$this->dbforge->drop_table('contact_social_links');
	}
}
