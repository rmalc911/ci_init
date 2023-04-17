CREATE TABLE `users` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_name` VARCHAR(50) NOT NULL,
	`display_name` VARCHAR(250) NOT NULL,
	`user_mobile` VARCHAR(50) NOT NULL,
	`user_email` VARCHAR(100) NULL DEFAULT NULL,
	`user_role` VARCHAR(50) NULL DEFAULT NULL,
	`user_status` ENUM('1', '0') NOT NULL DEFAULT '1',
	`created_date` DATETIME NULL DEFAULT NULL,
	`updated_date` DATETIME NULL DEFAULT NULL,
	`login_password` VARCHAR(250) NOT NULL,
	`last_login` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
CREATE TABLE `user_access_map` (
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
);
CREATE TABLE `admin_config` (
	`config_key` VARCHAR(50) NOT NULL,
	`config_value` TEXT NOT NULL,
	`config_date` DATETIME NOT NULL,
	`config_user` INT NOT NULL,
	PRIMARY KEY (`config_key`)
);
