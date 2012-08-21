<?php

class ACP3_GalleryModuleInstaller extends ACP3_ModuleInstaller {

	public function createTables() {
		return array(
			"CREATE TABLE `{pre}gallery` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`name` VARCHAR(120) NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`)
			) {engine};",
			"CREATE TABLE `{pre}gallery_pictures` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`pic` INT(10) UNSIGNED NOT NULL,
				`gallery_id` INT(10) UNSIGNED NOT NULL,
				`file` VARCHAR(120) NOT NULL,
				`description` TEXT NOT NULL,
				`comments` TINYINT(1) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX `foreign_gallery_id` (`gallery_id`)
			) {engine};"
		);
	}

	public function removeTables() {
		return array(
			"DROP TABLE `{pre}gallery_pictures`;",
			"DROP TABLE `{pre}gallery`;"
		);
	}

	public function addSettings() {
		global $db;

		$queries = array(
			'width' => 640,
			'height' => 480,
			'thumbwidth' => 160,
			'thumbheight' => 120,
			'maxwidth' => 2048,
			'maxheight' => 1536,
			'filesize' => 20971520,
			'overlay' => 1,
			'comments' => 1,
			'dateformat' => 'long',
			'sidebar' => 5,
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('gallery'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}