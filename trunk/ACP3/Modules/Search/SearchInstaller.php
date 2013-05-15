<?php
namespace ACP3\Modules\Search;
use ACP3\Core\ModuleInstaller;

class SearchInstaller extends ModuleInstaller {
	private $module_name = 'search';
	private $schema_version = 31;

	public function __construct(\ACP3\Core\Pimple $injector) {
		parent::__construct($injector);
	}

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
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

	protected function schemaUpdates() {
		return array(
			31 => array(
				"INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', " . $this->getModuleId() . ", 'sidebar', '', 1);",
			)
		);
	}
}