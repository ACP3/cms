<?php

class ACP3_ErrorsModuleInstaller extends ACP3_ModuleInstaller {

	public function removeResources() {
		return true;
	}

	public function createTables() {
		return array();
	}

	public function removeTables() {
		return array();
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
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('errors'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		return true;
	}

}