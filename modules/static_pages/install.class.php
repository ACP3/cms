<?php

class ACP3_StaticPagesModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'static_pages';
	private $schema_version = 30;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}static_pages` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`start` DATETIME NOT NULL,
				`end` DATETIME NOT NULL,
				`title` VARCHAR(120) NOT NULL,
				`text` TEXT NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), FULLTEXT KEY `index` (`title`, `text`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array("DROP TABLE `{pre}static_pages`;");
	}

	protected function settings() {
		return array();
	}

	protected function removeSettings() {
		return true;
	}

	protected function schemaUpdates() {
		return array();
	}
}