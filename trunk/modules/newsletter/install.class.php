<?php

class ACP3_NewsletterModuleInstaller extends ACP3_ModuleInstaller {
	public function __construct() {
		$this->special_resources = array(
			'acp_activate' => 3,
			'acp_sent' => 4,
		);
	}

	public function createTables() {
		global $db;

		$queries = array(
			"CREATE TABLE `{pre}newsletter_accounts` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`mail` VARCHAR(120) NOT NULL,
				`hash` VARCHAR(32) NOT NULL,
				PRIMARY KEY (`id`)
			) {engine};",
			"CREATE TABLE `{pre}newsletter_archive` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`date` DATETIME NOT NULL,
				`subject` VARCHAR(120) NOT NULL,
				`text` TEXT NOT NULL,
				`status` TINYINT(1) UNSIGNED NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
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
		global $db;

		$queries = array(
			"DROP TABLE `{pre}newsletter_accounts`;",
			"DROP TABLE `{pre}newsletter_archive`;",
		);

		$bool = false;
		foreach ($queries as $query) {
			$bool = $db->query($query, 0);
		}
		return (bool) $bool;
	}

	public function addSettings() {
		global $db;

		$queries = array(
			'mail' => '',
			'mailsig' => '',
		);

		$bool = false;
		foreach ($queries as $key => $value) {
			$bool = $db->insert('settings', array('id' => '', 'module_id' => $this->module_id, 'name' => $key, 'value' => $value));
		}
		return (bool) $bool;
	}

	public function removeSettings() {
		global $db;

		return (bool) $db->delete('settings', 'module_id = ' . $this->module_id);
	}

	public function addToModulesTable() {
		global $db;

		// Modul in die Modules-SQL-Tabelle eintragen
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('newsletter'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}