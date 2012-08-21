<?php

class ACP3_CommentsModuleInstaller extends ACP3_ModuleInstaller {

	public function createTables() {
		return array(
			"CREATE TABLE `{pre}comments` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ip` VARCHAR(40) NOT NULL,
				`date` DATETIME NOT NULL,
				`name` VARCHAR(20) NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				`message` TEXT NOT NULL,
				`module_id` INT(10) UNSIGNED NOT NULL,
				`entry_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX (`module_id`, `entry_id`)
			) {engine};"
		);
	}

	public function removeTables() {
		return array("DROP TABLE `{pre}comments`;");
	}

	public function addSettings() {
		global $db;

		$queries = array(
			'dateformat' => 'long',
			'emoticons' => 1,
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('comments'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}