<?php

class ACP3_CaptchaModuleInstaller extends ACP3_ModuleInstaller {

	public function removeResources() {
		return true;
	}

	public function createTables() {
		return true;
	}

	public function removeTables() {
		return true;
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('captcha'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		return true;
	}

}