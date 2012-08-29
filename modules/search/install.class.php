<?php

class ACP3_SearchModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'search';
	private $schema_version = 31;

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
		global $db;

		$module = $db->select('id', 'modules', 'name = \'' . $db->escape($this->getName()) . '\'');
		return array(
			31 => array(
				"INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', '" . $module[0]['id'] . "', 'sidebar', '', 1);",
			)
		);
	}
}