CREATE TABLE `users` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`user_name` VARCHAR(50) NOT NULL,
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
CREATE TABLE `careers` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`career_name` VARCHAR(250) NOT NULL,
	`career_desc` TEXT NOT NULL,
	`career_desc_preview` VARCHAR(250) NOT NULL,
	`career_status` ENUM('1', '0') NOT NULL DEFAULT '1',
	`created_date` DATETIME NULL DEFAULT NULL,
	`updated_date` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
CREATE TABLE `career_applications` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`career_id` INT NOT NULL,
	`applicant_fname` VARCHAR(250) NOT NULL,
	`applicant_lname` VARCHAR(250) NOT NULL,
	`applicant_email` VARCHAR(250) NOT NULL,
	`applicant_phone` VARCHAR(15) NOT NULL,
	`applicant_resume` VARCHAR(250) NOT NULL,
	`applicant_about` VARCHAR(1000) NOT NULL,
	`date` DATETIME NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
CREATE TABLE `contact_us` (
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
) ENGINE = InnoDB;
CREATE TABLE `albums`(
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`album_name` VARCHAR(250) NOT NULL,
	`url_path` VARCHAR(250) NOT NULL,
	`album_status` ENUM('1', '0') NOT NULL DEFAULT '1',
	`created_date` DATETIME NULL DEFAULT NULL,
	`updated_date` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY(`id`),
	UNIQUE `album_name`(`album_name`)
) ENGINE = INNODB;
CREATE TABLE `media_images` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`album_id` int(11) NOT NULL,
	`image_caption` varchar(250) NOT NULL,
	`image_url` varchar(250) NOT NULL,
	`image_status` enum('1', '0') NOT NULL DEFAULT '1',
	`created_date` datetime DEFAULT NULL,
	`updated_date` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE = InnoDB;
CREATE TABLE `contact_social_links` (
	`social_icon_class` VARCHAR(50) NOT NULL,
	`social_icon_url` VARCHAR(50) NOT NULL,
	PRIMARY KEY (`social_icon_class`, `social_icon_url`)
) ENGINE = InnoDB;
-- xx --
