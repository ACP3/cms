<?php
namespace ACP3\Modules\Search;
use ACP3\Core\ModuleInstaller;

class Installer extends ModuleInstaller {
	const MODULE_NAME = 'search';
	const SCHEMA_VERSION = 31;

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

	protected function schemaUpdates() {
		return array(
			31 => array(
				"INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', " . $this->getModuleId() . ", 'sidebar', '', 1);",
			)
		);
	}
}