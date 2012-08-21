<?php

class ACP3_SystemModuleInstaller extends ACP3_ModuleInstaller {
	public function __construct() {
		$this->special_resources = array(
			'acp_configuration' => 7,
			'acp_designs' => 7,
			'acp_extensions' => 7,
			'acp_languages' => 7,
			'acp_maintenance' => 7,
			'acp_modules' => 7,
			'acp_sql_export' => 7,
			'acp_sql_import' => 7,
			'acp_update_check' => 3,
		);
	}

	public function removeResources() {
		return true;
	}

	public function createTables() {
		return array(
			"CREATE TABLE `{pre}modules` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(100) NOT NULL,
				`active` tinyint(1) unsigned NOT NULL,
				PRIMARY KEY (`id`)
			) {engine};",
			"CREATE TABLE `{pre}seo` (
				`uri` varchar(255) NOT NULL,
				`alias` varchar(100) NOT NULL,
				`keywords` varchar(255) NOT NULL,
				`description` varchar(255) NOT NULL,
				`robots` TINYINT(1) UNSIGNED NOT NULL,
				PRIMARY KEY (`uri`), INDEX (`alias`)
			) {engine};",
			"CREATE TABLE `{pre}sessions` (
				`session_id` varchar(32) NOT NULL,
				`session_starttime` int(10) unsigned NOT NULL,
				`session_data` text NOT NULL,
				PRIMARY KEY (`session_id`)
			) {engine};",
			"CREATE TABLE `{pre}settings` (
				`id` INT(10) unsigned NOT NULL AUTO_INCREMENT,
				`module_id` INT(10) NOT NULL,
				`name` VARCHAR(40) NOT NULL,
				`value` TEXT NOT NULL,
				PRIMARY KEY (`id`), UNIQUE KEY (`module_id`,`name`)
			) {engine};",
			// ACL
			"CREATE TABLE `{pre}acl_privileges` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`key` varchar(100) NOT NULL,
				`description` varchar(100) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `key` (`key`)
			) {engine};",
			"CREATE TABLE`{pre}acl_resources` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`module_id` int(10) unsigned NOT NULL,
				`page` varchar(255) NOT NULL,
				`params` varchar(255) NOT NULL,
				`privilege_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`)
			) {engine};",
			"CREATE TABLE`{pre}acl_roles` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(100) NOT NULL,
				`root_id` int(10) unsigned NOT NULL,
				`parent_id` int(10) unsigned NOT NULL,
				`left_id` int(10) unsigned NOT NULL,
				`right_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id`)
			) {engine};",
			"CREATE TABLE`{pre}acl_rules` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`role_id` int(10) unsigned NOT NULL,
				`module_id` int(10) unsigned NOT NULL,
				`privilege_id` int(10) unsigned NOT NULL,
				`permission` tinyint(1) unsigned NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `role_id` (`role_id`,`module_id`,`privilege_id`)
			) {engine};",
			"CREATE TABLE`{pre}acl_user_roles` (
				`user_id` int(10) unsigned NOT NULL,
				`role_id` int(10) unsigned NOT NULL,
				PRIMARY KEY (`user_id`,`role_id`)
			) {engine};",
			// Default Privilegien & Benutzer-Rollen
			"INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (1, 'view', '')",
			"INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (2, 'create', '')",
			"INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (3, 'admin_view', '')",
			"INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (4, 'admin_create', '')",
			"INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (5, 'admin_edit', '')",
			"INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (6, 'admin_delete', '')",
			"INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (7, 'admin_settings', '');",
			"INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (1, 'Gast', 1, 0, 1, 8)",
			"INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (2, 'Mitglied', 1, 1, 2, 7)",
			"INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (3, 'Autor', 1, 2, 3, 6)",
			"INSERT INTO `{pre}acl_roles` (`id`, `name`, `root_id`, `parent_id`, `left_id`, `right_id`) VALUES (4, 'Administrator', 1, 3, 4, 5);",
			"INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (0, 1)",
			"INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (1, 4);"
		);
	}

	public function removeTables() {
		return array();
	}

	public function addSettings() {
		return true;
	}

	public function removeSettings() {
		return true;
	}

	public function addToModulesTable() {
		global $db;

		// Modul in die Modules-SQL-Tabelle eintragen
		$bool = $db->insert('modules', array('id' => '', 'name' => $db->escape('system'), 'active' => 1));
		$this->module_id = $db->link->lastInsertId();

		return (bool) $bool;
	}

	public function removeFromModulesTable() {
		return true;
	}

}