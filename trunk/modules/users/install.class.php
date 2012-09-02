<?php

class ACP3_UsersModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'users';
	private $schema_version = 31;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function removeResources() {
		return true;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}users` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`super_user` TINYINT(1) UNSIGNED NOT NULL,
				`nickname` VARCHAR(30) NOT NULL,
				`pwd` VARCHAR(53) NOT NULL,
				`login_errors` TINYINT(1) UNSIGNED NOT NULL,
				`realname` VARCHAR(80) NOT NULL,
				`gender` VARCHAR(3) NOT NULL,
				`birthday` VARCHAR(16) NOT NULL,
				`birthday_format` TINYINT(1) UNSIGNED NOT NULL,
				`mail` VARCHAR(120) NOT NULL,
				`website` VARCHAR(120) NOT NULL,
				`icq` VARCHAR(11) NOT NULL,
				`msn` VARCHAR(120) NOT NULL,
				`skype` VARCHAR(30) NOT NULL,
				`date_format_long` VARCHAR(30) NOT NULL,
				`date_format_short` VARCHAR(30) NOT NULL,
				`time_zone` VARCHAR(100) NOT NULL,
				`language` VARCHAR(10) NOT NULL,
				`entries` TINYINT(2) UNSIGNED NOT NULL,
				`draft` TEXT NOT NULL,
				PRIMARY KEY (`id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array();
	}

	protected function settings() {
		return array(
			'enable_registration' => 1,
			'entries_override' => 1,
			'language_override' => 1,
			'mail' => ''
		);
	}

	protected function removeSettings() {
		return true;
	}

	protected function removeFromModulesTable() {
		return true;
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"INSERT INTO `{pre}settings` (`id`, `module_id`, `name`, `value`) VALUES ('', " . $this->getModuleId() . ", 'mail', '');",
			)
		);
	}
}