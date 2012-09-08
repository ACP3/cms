<?php

class ACP3_ContactModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'contact';
	private $schema_version = 31;

	public function __construct() {
		$this->special_resources = array(
			'acp_list' => 7
		);
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
		return array(
			'address' => '',
			'disclaimer' => '',
			'fax' => '',
			'mail' => '',
			'telephone' => '',
		);
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"UPDATE `{pre}acl_resources` SET privilege_id = 7 WHERE page = 'acp_list' AND module_id = " . $this->getModuleId() . ";"
			)
		);
	}
}