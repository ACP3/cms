<?php

class ACP3_CategoriesModuleInstaller extends ACP3_ModuleInstaller {

	public function removeResources() {
		return true;
	}

	public function createTables() {
		return array(
			"CREATE TABLE `{pre}categories` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(120) NOT NULL,
				`picture` VARCHAR(120) NOT NULL,
				`description` VARCHAR(120) NOT NULL,
				`module_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`)
			) {engine};"
		);
	}

	public function removeTables() {
		return array();
	}

	public function addSettings() {
		global $db;

		$queries = array(
			'width' => 100,
			'height' => 50,
			'filesize' => 40960
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('categories'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		return true;
	}

}