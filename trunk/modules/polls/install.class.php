<?php

class ACP3_PollsModuleInstaller extends ACP3_ModuleInstaller {

	public function createTables() {
		global $db;

		$queries = array(
			"CREATE TABLE `{pre}polls` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`question` VARCHAR(120) NOT NULL,
				`multiple` TINYINT(1) UNSIGNED NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`)
			) {engine};",
			"CREATE TABLE `{pre}poll_answers` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`text` VARCHAR(120) NOT NULL,
				`poll_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX `foreign_poll_id` (`poll_id`)
			) {engine};",
			"CREATE TABLE `{pre}poll_votes` (
				`poll_id` INT(10) UNSIGNED NOT NULL,
				`answer_id` INT(10) UNSIGNED NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				`ip` VARCHAR(40) NOT NULL,
				`time` DATETIME NOT NULL,
				INDEX (`poll_id`, `answer_id`, `user_id`)
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
		global $db;

		$queries = array(
			"DROP TABLE `{pre}poll_votes`;",
			"DROP TABLE `{pre}poll_answers`;",
			"DROP TABLE `{pre}polls`;",
		);

		$bool = false;
		foreach ($queries as $query) {
			$bool = $db->query($query, 0);
		}
		return (bool) $bool;
	}

	public function addSettings() {
		return true;
	}

	public function removeSettings() {
		return true;
	}

	public function addToModulesTable() {
		global $db;

		// Modul in die Modules-SQL-Tabelle eintragen
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('polls'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}