<?php

class ACP3_UsersModuleInstaller extends ACP3_ModuleInstaller {

	public function removeResources() {
		return true;
	}

	public function createTables() {
		global $db;

		$queries = array(
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
			) {engine};"
		);

		$engine = 'ENGINE=MyISAM CHARACTER SET `utf8` COLLATE `utf8_general_ci`';
		$bool = false;
		foreach ($queries as $query) {
			$bool = $db->query(str_replace('{engine}', $engine, $query), 0);
		}

		return (bool) $bool;
	}

	public function removeTables() {
		return true;
	}

	public function addSettings() {
		global $db;

		$queries = array(
			'enable_registration' => 1,
			'entries_override' => 1,
			'language_override' => 1,
		);

		$bool = false;
		foreach ($queries as $key => $value) {
			$bool = $db->insert('settings', array('id' => '', 'module_id' => $this->module_id, 'name' => $key, 'value' => $value));
		}
		return (bool) $bool;
	}

	public function removeSettings() {
		return true;
	}

	public function addToModulesTable() {
		global $db;

		// Modul in die Modules-SQL-Tabelle eintragen
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('users'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		return true;
	}

}