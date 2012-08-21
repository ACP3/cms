<?php

class ACP3_StaticPagesModuleInstaller extends ACP3_ModuleInstaller {

	public function createTables() {
		return array(
			"CREATE TABLE `{pre}static_pages` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`text` TEXT NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`)
			) {engine};"
		);
	}

	public function removeTables() {
		return array("DROP TABLE `{pre}static_pages`;");
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('static_pages'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}