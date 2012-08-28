<?php

class ACP3_CategoriesModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'categories';
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
		return array(
			"CREATE TABLE `{pre}categories` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(120) NOT NULL,
				`picture` VARCHAR(120) NOT NULL,
				`description` VARCHAR(120) NOT NULL,
				`module_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array();
	}

	protected function settings() {
		return array(
			'width' => 100,
			'height' => 50,
			'filesize' => 40960
		);
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