<?php

class ACP3_NewsletterModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'newsletter';
	private $schema_version = 30;

	public function __construct() {
		$this->special_resources = array(
			'acp_activate' => 3,
			'acp_sent' => 4,
		);
	}

	protected function getName() {
		return $this->module_name;
	}

	protected function getSchemaVersion() {
		return $this->schema_version;
	}

	protected function createTables() {
		return array(
			"CREATE TABLE `{pre}newsletter_accounts` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`mail` VARCHAR(120) NOT NULL,
				`hash` VARCHAR(32) NOT NULL,
				PRIMARY KEY (`id`)
			) {engine} {charset};",
			"CREATE TABLE `{pre}newsletter_archive` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`date` DATETIME NOT NULL,
				`subject` VARCHAR(120) NOT NULL,
				`text` TEXT NOT NULL,
				`status` TINYINT(1) UNSIGNED NOT NULL,
				`user_id` INT UNSIGNED NOT NULL,
				PRIMARY KEY (`id`)
			) {engine} {charset};"
		);
	}

	protected function removeTables() {
		return array(
			"DROP TABLE `{pre}newsletter_accounts`;",
			"DROP TABLE `{pre}newsletter_archive`;"
		);
	}

	protected function settings() {
		return array(
			'mail' => '',
			'mailsig' => '',
		);
	}

	protected function schemaUpdates() {
		return array();
	}
}