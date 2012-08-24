<?php

class ACP3_CaptchaModuleInstaller extends ACP3_ModuleInstaller {

	protected function removeResources() {
		return true;
	}

	protected function createTables() {
		return array();
	}

	protected function removeTables() {
		return array();
	}

	protected function addSettings() {
		return true;
	}

	protected function removeSettings() {
		return true;
	}

	protected function addToModulesTable() {
		global $db;

		// Modul in die Modules-SQL-Tabelle eintragen
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('captcha'), 'version' => 30, 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	protected function removeFromModulesTable() {
		return true;
	}

}