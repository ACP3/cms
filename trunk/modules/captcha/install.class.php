<?php

class ACP3_CaptchaModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'captcha';
	private $schema_version = 30;

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

	protected function schemaUpdates() {
		return array();
	}
}