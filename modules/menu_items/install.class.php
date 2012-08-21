<?php

class ACP3_MenuItemsModuleInstaller extends ACP3_ModuleInstaller {

	public function createTables() {
		return array(
			"CREATE TABLE `{pre}menu_items` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`mode` TINYINT(1) UNSIGNED NOT NULL,
				`block_id` INT(10) UNSIGNED NOT NULL,
				`root_id` INT(10) UNSIGNED NOT NULL,
				`parent_id` INT(10) UNSIGNED NOT NULL,
				`left_id` INT(10) UNSIGNED NOT NULL,
				`right_id` INT(10) UNSIGNED NOT NULL,
				`display` TINYINT(1) UNSIGNED NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`uri` VARCHAR(120) NOT NULL,
				`target` TINYINT(1) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX `foreign_block_id` (`block_id`)
			) {engine};",
			"CREATE TABLE `{pre}menu_items_blocks` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`index_name` VARCHAR(10) NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				PRIMARY KEY (`id`)
			) {engine};"
		);
	}

	public function removeTables() {
		return array(
			"DROP TABLE `{pre}menu_items_blocks`;",
			"DROP TABLE `{pre}menu_items`;"
		);
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('menu_items'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}