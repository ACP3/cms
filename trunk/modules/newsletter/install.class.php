<?php

class ACP3_NewsletterModuleInstaller extends ACP3_ModuleInstaller {
	private $module_name = 'newsletter';
	private $schema_version = 32;

	public function __construct() {
		$this->special_resources = array(
			'acp_send' => 4,
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
			"CREATE TABLE `{pre}newsletters` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
				`date` DATETIME NOT NULL,
				`title` VARCHAR(120) NOT NULL,
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
			"DROP TABLE `{pre}newsletters`;"
		);
	}

	protected function settings() {
		return array(
			'mail' => '',
			'mailsig' => '',
		);
	}

	protected function schemaUpdates() {
		return array(
			31 => array(
				"RENAME TABLE `{pre}newsletter_archive` TO `{pre}newsletters`",
				"ALTER TABLE `{pre}newsletters` CHANGE `subject` `title` VARCHAR(120) {charset} NOT NULL",
			),
			32 => array(
				"INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES('', " . $this->getModuleId() . ", 'archive', '', 1);",
			)
		);
	}
}