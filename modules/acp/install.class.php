<?php

class ACP3_AcpModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'acp';
	private $schema_version = 30;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function removeResources() {
		return true;
	}

	protected function createTables() {
		return array();
	}

	protected function removeTables() {
		return array();
	}

	protected function settings() {
		return array();
	}

	protected function removeSettings() {
		return true;
	}

	protected function removeFromModulesTable() {
		return true;
	}

	protected function schemaUpdates() {
		return array();
	}
}