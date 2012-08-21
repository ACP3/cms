<?php

class ACP3_NewsModuleInstaller extends ACP3_ModuleInstaller {

	public function createTables() {
		return array(
			"CREATE TABLE `{pre}news` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`headline` VARCHAR(120) NOT NULL,
				`text` TEXT NOT NULL,
				`readmore` TINYINT(1) UNSIGNED NOT NULL,
				`comments` TINYINT(1) UNSIGNED NOT NULL,
				`category_id` INT(10) UNSIGNED NOT NULL,
				`uri` VARCHAR(120) NOT NULL,
				`target` TINYINT(1) UNSIGNED NOT NULL,
				`link_title` VARCHAR(120) NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), FULLTEXT KEY `index` (`headline`,`text`), INDEX `foreign_category_id` (`category_id`)
			) {engine};"
		);
	}

	public function removeTables() {
		return array("DROP TABLE `{pre}news`;");
	}

	public function addSettings() {
		global $db;

		$queries = array(
			'comments' => 1,
			'dateformat' => 'long',
			'readmore' => 1,
			'readmore_chars' => 350,
			'sidebar' => 5,
			'category_in_breadcrumb' => 1
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('news'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}