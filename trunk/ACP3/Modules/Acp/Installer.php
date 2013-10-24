<?php

namespace ACP3\Modules\Acp;

use ACP3\Core\ModuleInstaller;

class Installer extends ModuleInstaller {

	const MODULE_NAME = 'acp';
	const SCHEMA_VERSION = 30;

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