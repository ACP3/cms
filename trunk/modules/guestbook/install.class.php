<?php

class ACP3_GuestbookModuleInstaller extends ACP3_ModuleInstaller {

	public function createTables() {
		return array(
			"CREATE TABLE `{pre}guestbook` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`date` DATETIME NOT NULL,
				`ip` VARCHAR(40) NOT NULL,
				`name` VARCHAR(20) NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				`message` TEXT NOT NULL,
				`website` VARCHAR(120) NOT NULL,
				`mail` VARCHAR(120) NOT NULL,
				`active` TINYINT(1) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX `foreign_user_id` (`user_id`)
			) {engine};"
		);
	}

	public function removeTables() {
		return array("DROP TABLE `{pre}guestbook`;");
	}

	public function addSettings() {
		global $db;

		$queries = array(
			'dateformat' => 'long',
			'notify' => 0,
			'notify_email' => '',
			'emoticons' => 1,
			'newsletter_integration' => 0,
			'overlay' => 1
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('guestbook'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}