<?php

namespace ACP3\Modules\Permissions;

use ACP3\Core\Modules;

class Installer extends Modules\Installer {

	const MODULE_NAME = 'permissions';
	const SCHEMA_VERSION = 31;

	public function renameModule() {
		return array(
			31 => "UPDATE `{pre}modules` SET name = 'permissions' WHERE name = 'access';"
		);
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
