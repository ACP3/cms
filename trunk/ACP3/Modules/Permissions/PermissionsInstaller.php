<?php
namespace ACP3\Modules\Permissions;
use ACP3\Core\ModuleInstaller;

class PermissionsInstaller extends ModuleInstaller {
	private $module_name = 'permissions';
	private $schema_version = 31;

	public function __construct(\ACP3\Core\Pimple $injector) {
		parent::__construct($injector);
	}

	public function renameModule() {
		return array(
			31 => "UPDATE `{pre}modules` SET name = 'permissions' WHERE name = 'access';"
		);
	}

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