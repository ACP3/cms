<?php

class ACP3_CommentsModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'comments';
	private $schema_version = 30;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}comments` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ip` VARCHAR(40) NOT NULL,
				`date` DATETIME NOT NULL,
				`name` VARCHAR(20) NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				`message` TEXT NOT NULL,
				`module_id` INT(10) UNSIGNED NOT NULL,
				`entry_id` INT(10) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX (`module_id`, `entry_id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array("DROP TABLE `{pre}comments`;");
	}

	protected function settings() {
		return array(
			'dateformat' => 'long',
			'emoticons' => 1,
		);
	}

	protected function schemaUpdates() {
		return array();
	}
}