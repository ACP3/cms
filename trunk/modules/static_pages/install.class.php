<?php

class ACP3_StaticPagesModuleInstaller extends ACP3_ModuleInstaller {

	public function createTables() {
		global $db;

		$queries = array(
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
			"DROP TABLE `{pre}static_pages`;",
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('static_pages'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}