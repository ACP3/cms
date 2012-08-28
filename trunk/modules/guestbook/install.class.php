<?php

class ACP3_GuestbookModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'guestbook';
	private $schema_version = 30;

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}guestbook` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`date` DATETIME NOT NULL,
				`ip` VARCHAR(40) NOT NULL,
				`name` VARCHAR(20) NOT NULL,
				`user_id` INT(10) UNSIGNED NOT NULL,
				`message` TEXT NOT NULL,
				`website` VARCHAR(120) NOT NULL,
				`mail` VARCHAR(120) NOT NULL,
				`active` TINYINT(1) UNSIGNED NOT NULL,
				PRIMARY KEY (`id`), INDEX `foreign_user_id` (`user_id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array("DROP TABLE `{pre}guestbook`;");
	}

	protected function settings() {
		return array(
			'dateformat' => 'long',
			'notify' => 0,
			'notify_email' => '',
			'emoticons' => 1,
			'newsletter_integration' => 0,
			'overlay' => 1
		);
	}

	protected function schemaUpdates() {
		return array();
	}
}