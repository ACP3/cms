<?php

class ACP3_ContactModuleInstaller extends ACP3_ModuleInstaller {

	protected function createTables() {
		return array();
	}

	protected function removeTables() {
		return array();
	}

	protected function addSettings() {
		global $db;

		$queries = array(
			'address' => '',
			'disclaimer' => '',
			'fax' => '',
			'mail' => '',
			'telephone' => '',
		);

		$bool = false;
		foreach ($queries as $key => $value) {
			$bool = $db->insert('settings', array('id' => '', 'module_id' => $this->module_id, 'name' => $key, 'value' => $value));
		}
		return (bool) $bool;
	}

	protected function removeSettings() {
		global $db;

		return (bool) $db->delete('settings', 'module_id = ' . $this->module_id);
	}

	protected function addToModulesTable() {
		global $db;

		// Modul in die Modules-SQL-Tabelle eintragen
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('contact'), 'version' => 30, 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	protected function removeFromModulesTable() {
		global $db;

		return (bool) $db->delete('modules', 'id = ' . $this->module_id);
	}

}