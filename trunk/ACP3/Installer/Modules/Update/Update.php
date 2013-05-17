<?php

namespace ACP3\Installer\Modules\Update;

use ACP3\Installer\Core;

/**
 * Description of Update
 *
 * @author goratsch
 */
class Update extends \ACP3\Installer\Core\InstallerModuleController {

	public function actionDb_update() {
		if (isset($_POST['update'])) {
			$results = array();
			// Zuerst die wichtigen System-Module aktualisieren...
			$update_first = array('system', 'permissions', 'users');
			foreach ($update_first as $row) {
				$result = Core\Functions::updateModule($row);
				$module = ucfirst($row);
				$text = \ACP3\Core\Registry::get('Lang')->t($result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'));
				$results[$module] = array(
					'text' => sprintf(\ACP3\Core\Registry::get('Lang')->t('db_update_text'), $module),
					'class' => $result === 1 ? 'success' : ($result === 0 ? 'important' : 'info'),
					'result_text' => \ACP3\Core\Registry::get('Lang')->t($result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
				);
			}

			// ...danach die Restlichen
			$modules = scandir(MODULES_DIR);
			foreach ($modules as $row) {
				if ($row !== '.' && $row !== '..' && in_array(strtolower($row), $update_first) === false) {
					$result = Core\Functions::updateModule($row);
					$module = ucfirst($row);
					$text = \ACP3\Core\Registry::get('Lang')->t($result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'));
					$results[$module] = array(
						'text' => sprintf(\ACP3\Core\Registry::get('Lang')->t('db_update_text'), $module),
						'class' => $result === 1 ? 'success' : ($result === 0 ? 'important' : 'info'),
						'result_text' => \ACP3\Core\Registry::get('Lang')->t($result === 1 ? 'db_update_success' : ($result === 0 ? 'db_update_error' : 'db_update_no_update'))
					);
				}
			}

			ksort($results);

			\ACP3\Core\Registry::get('View')->assign('results', $results);

			// Cache leeren
			\ACP3\Core\Cache::purge('minify');
			\ACP3\Core\Cache::purge('sql');
			\ACP3\Core\Cache::purge('tpl_compiled');
		}
	}

	public function actionDb_update_legacy() {
		if (isset($_POST['update'])) {
			define('NEW_VERSION', '4.0 SVN');
			if (defined('CONFIG_DB_VERSION') === false)
				define('CONFIG_DB_VERSION', (int) 0);

			if (defined('CONFIG_DATE_FORMAT_LONG') === false) {
				define('CONFIG_DATE_FORMAT_LONG', CONFIG_DATE_FORMAT);
				define('CONFIG_DATE_FORMAT_SHORT', 'd.m.Y');
			}
			if (defined('CONFIG_CACHE_IMAGES') == false) {
				define('CONFIG_CACHE_IMAGES', 1);
				define('CONFIG_CACHE_MINIFY', 3600);
				define('CONFIG_SEO_ALIASES', 1);
			}
			if (defined('CONFIG_MAILER_TYPE') === false) {
				define('CONFIG_MAILER_SMTP_AUTH', 0);
				define('CONFIG_MAILER_SMTP_HOST', '');
				define('CONFIG_MAILER_SMTP_PASSWORD', '');
				define('CONFIG_MAILER_SMTP_PORT', 25);
				define('CONFIG_MAILER_SMTP_SECURITY', 'none');
				define('CONFIG_MAILER_SMTP_USER', '');
				define('CONFIG_MAILER_TYPE', 'mail');
			}
			if (defined('CONFIG_SEO_ROBOTS') === false) {
				define('CONFIG_SEO_ROBOTS', 1);
			}

			$results = array();

			if (CONFIG_DB_VERSION < 1) {
				$queries = array(
					'UPDATE `{pre}menu_items` SET `mode` = 4 WHERE `uri` LIKE \'static_pages/list/id_%\' AND `mode` = 2;',
					'ALTER TABLE `{pre}users` ADD `date_format_long` VARCHAR(30) NOT NULL AFTER `skype`;',
					'ALTER TABLE `{pre}users` ADD `date_format_short` VARCHAR(30) NOT NULL AFTER `date_format_long`;',
					'ALTER TABLE `{pre}users` ADD `entries` TINYINT(2) UNSIGNED NOT NULL AFTER `language`;',
					'UPDATE `{pre}users` SET `date_format_long` = \'' . (defined('CONFIG_DATE_FORMAT_LONG') === true ? CONFIG_DATE_FORMAT_LONG : CONFIG_DATE_FORMAT) . '\', `date_format_short` = \'' . (defined('CONFIG_DATE_FORMAT_SHORT') === true ? CONFIG_DATE_FORMAT_SHORT : 'd.m.Y') . '\', `entries` = ' . ((int) CONFIG_ENTRIES) . ';',
					'UPDATE `{pre}access` SET `modules` =  \'access:16,acp:16,captcha:16,categories:16,comments:16,contact:16,emoticons:16,errors:16,feeds:16,files:16,gallery:16,guestbook:16,menu_items:16,news:16,newsletter:16,polls:16,search:16,static_pages:16,system:16,users:16\' WHERE `id` = 1;',
					'ALTER TABLE `{pre}guestbook` ADD `active` TINYINT(1) UNSIGNED NOT NULL AFTER `mail`;',
					'UPDATE `{pre}guestbook` SET `active` = 1;',
					'CREATE TABLE `{pre}aliases` (`uri` VARCHAR(255) NOT NULL, `alias` VARCHAR(100) NOT NULL, PRIMARY KEY (`uri`), UNIQUE KEY `alias` (`alias`)) {engine} {charset};',
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 1);
			}
			if (CONFIG_DB_VERSION < 2) {
				$queries = array(
					'RENAME TABLE `{pre}aliases` TO `{pre}seo`;',
					'ALTER TABLE `{pre}seo` ADD `keywords` VARCHAR(255) NOT NULL AFTER `alias`;',
					'ALTER TABLE `{pre}seo` ADD `description` VARCHAR(255) NOT NULL AFTER `keywords`;',
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 2);
			}
			if (CONFIG_DB_VERSION < 3) {
				$queries = array(
					'CREATE TABLE `{pre}settings` (`id` INT(10) unsigned NOT NULL AUTO_INCREMENT, `module` VARCHAR(40) NOT NULL, `name` VARCHAR(40) NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `module` (`module`,`name`)) {engine} {charset};',
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'categories', 'width', '120');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'categories', 'height', '80');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'categories', 'filesize', '40960');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'comments', 'dateformat', 'long');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'contact', 'mail', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'contact', 'address', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'contact', 'telephone', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'contact', 'fax', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'contact', 'disclaimer', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'contact', 'layout', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'emoticons', 'width', '32');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'emoticons', 'height', '32');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'emoticons', 'filesize', '10240');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'files', 'comments', '1');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'files', 'dateformat', 'long');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'files', 'sidebar', '5');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'colorbox', '1');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'comments', '1');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'width', '640');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'height', '480');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'thumbwidth', '160');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'thumbheight', '120');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'maxwidth', '2048');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'maxheight', '1536');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'filesize', '20971520');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'dateformat', 'long');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'gallery', 'sidebar', '5');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'guestbook', 'dateformat', 'long');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'guestbook', 'notify', '0');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'guestbook', 'notify_email', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'guestbook', 'emoticons', '1');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'guestbook', 'newsletter_integration', '0');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'news', 'comments', '1');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'news', 'dateformat', 'long');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'news', 'readmore', '1');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'news', 'readmore_chars', '350');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'news', 'sidebar', '5');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'newsletter', 'mail', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'newsletter', 'mailsig', '');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'users', 'entries_override', '1');",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'users', 'language_override', '1');",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 3);
			}
			if (CONFIG_DB_VERSION < 4) {
				$queries = array(
					'ALTER TABLE `{pre}news` ADD `user_id` INT UNSIGNED NOT NULL;',
					'ALTER TABLE `{pre}files` ADD `user_id` INT UNSIGNED NOT NULL;',
					'ALTER TABLE `{pre}gallery` ADD `user_id` INT UNSIGNED NOT NULL;',
					'ALTER TABLE `{pre}poll_question` ADD `user_id` INT UNSIGNED NOT NULL;',
					'ALTER TABLE `{pre}static_pages` ADD `user_id` INT UNSIGNED NOT NULL;',
					'ALTER TABLE `{pre}newsletter_archive` ADD `user_id` INT UNSIGNED NOT NULL;',
					'RENAME TABLE `{pre}poll_question` TO `{pre}polls`;',
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 4);

				$user = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT MIN(id) AS id FROM ' . DB_PRE . 'users');

				\ACP3\Core\Registry::get('Db')->executeUpdate('UPDATE ' . DB_PRE . 'files SET user_id = ?', array($user));
				\ACP3\Core\Registry::get('Db')->executeUpdate('UPDATE ' . DB_PRE . 'gallery SET user_id = ?', array($user));
				\ACP3\Core\Registry::get('Db')->executeUpdate('UPDATE ' . DB_PRE . 'news SET user_id = ?', array($user));
				\ACP3\Core\Registry::get('Db')->executeUpdate('UPDATE ' . DB_PRE . 'newsletter_archive SET user_id = ?', array($user));
				\ACP3\Core\Registry::get('Db')->executeUpdate('UPDATE ' . DB_PRE . 'polls SET user_id = ?', array($user));
				\ACP3\Core\Registry::get('Db')->executeUpdate('UPDATE ' . DB_PRE . 'static_pages SET user_id = ?', array($user));
			}
			if (CONFIG_DB_VERSION < 5) {
				$queries = array(
					'CREATE TABLE `{pre}modules` (`name` varchar(100) NOT NULL, `active` tinyint(1) unsigned NOT NULL, PRIMARY KEY (`name`)) {engine} {charset}',
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 5);

				$dir = scandir(MODULES_DIR);
				foreach ($dir as $row) {
					if ($row !== '.' && $row !== '..' && is_file(MODULES_DIR . $row . '/module.xml') === true) {
						\ACP3\Core\Registry::get('Db')->insert(DB_PRE . 'modules', array('name' => $row, 'active' => 1));
					}
				}
			}
			if (CONFIG_DB_VERSION < 10) {
				$queries = array(
					'DROP TABLE `{pre}access`',
					'DROP TABLE IF EXISTS `{pre}acl_role_privileges`;',
					'DROP TABLE IF EXISTS `{pre}acl_resources`;',
					'ALTER TABLE `{pre}users` DROP COLUMN `access`',
					'ALTER TABLE `{pre}modules` DROP PRIMARY KEY;',
					'ALTER TABLE `{pre}modules` ADD COLUMN `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;',
					'CREATE TABLE `{pre}acl_rules` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `role_id` int(10) unsigned NOT NULL, `module_id` int(10) unsigned NOT NULL, `privilege_id` int(10) unsigned NOT NULL, `permission` tinyint(1) unsigned NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `role_id` (`role_id`,`module_id`,`privilege_id`)) {engine} {charset};',
					'CREATE TABLE `{pre}acl_resources` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `module_id` int(10) unsigned NOT NULL, `page` varchar(255) NOT NULL, `params` varchar(255) NOT NULL, `privilege_id` int(10) unsigned NOT NULL, PRIMARY KEY (`id`)) {engine} {charset};',
					'CREATE TABLE `{pre}acl_privileges` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `key` varchar(100) NOT NULL, `description` varchar(100) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `key` (`key`)) {engine} {charset};',
					"INSERT INTO `{pre}acl_privileges` (`id`, `key`, `description`) VALUES (1, 'view', ''), (2, 'create', ''), (3, 'admin_view', ''), (4, 'admin_create', ''), (5, 'admin_edit', ''), (6, 'admin_delete', ''), (7, 'admin_settings', '');",
					'CREATE TABLE `{pre}acl_roles` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `parent_id` INT(10) NOT NULL, `left_id` int(10) unsigned NOT NULL, `right_id` int(10) unsigned NOT NULL, PRIMARY KEY (`id`)) {engine} {charset};',
					"INSERT INTO `{pre}acl_roles` (`id`, `name`, `parent_id`, `left_id`, `right_id`) VALUES (1, 'Gast', 0, 1, 8), (2, 'Mitglied', 1, 2, 7), (3, 'Autor', 2, 3, 6), (4, 'Administrator', 3, 4, 5);",
					'CREATE TABLE `{pre}acl_user_roles` (`user_id` int(10) unsigned NOT NULL, `role_id` int(10) unsigned NOT NULL, PRIMARY KEY (`user_id`,`role_id`)) {engine} {charset};',
					"INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (0, 1), (1, 4);",
					'ALTER TABLE `{pre}menu_items` ADD `parent_id` INT(10) NOT NULL AFTER `root_id`;',
					"TRUNCATE TABLE `{pre}acl_rules`;",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 10);

				$roles = \ACP3\Core\Registry::get('Db')->fetchAll('SELECT id, left_id, right_id FROM ' . DB_PRE . 'acl_roles');
				foreach ($roles as $row) {
					$parent_id = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'acl_roles WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1', array($row['left_id'], $row['right_id']));
					\ACP3\Core\Registry::get('Db')->update(DB_PRE . 'acl_roles', array('parent_id' => !empty($parent_id) ? $parent_id : 0), array('id' => $row['id']));
				}

				$pages = \ACP3\Core\Registry::get('Db')->fetchAll('SELECT id, left_id, right_id FROM ' . DB_PRE . 'menu_items');
				foreach ($pages as $row) {
					$parent_id = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'menu_items WHERE left_id < ? AND right_id > ? ORDER BY left_id DESC LIMIT 1', array($row['left_id'], $row['right_id']));
					\ACP3\Core\Registry::get('Db')->update(DB_PRE . 'menu_items', array('parent_id' => !empty($parent_id) ? $parent_id : 0), array('id' => $row['id']));
				}

				Core\Functions::resetResources();
			}
			if (CONFIG_DB_VERSION < 11) {
				// Neue Module Seiten für das Permission Modul
				Core\Functions::resetResources(2);
			}
			if (CONFIG_DB_VERSION < 12) {
				$queries = array(
					'CREATE TABLE `{pre}sessions` (`session_id` varchar(32) NOT NULL, `session_starttime` int(10) unsigned NOT NULL, `session_data` text NOT NULL, PRIMARY KEY (`session_id`)) {engine} {charset};'
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 12);
			}
			if (CONFIG_DB_VERSION < 13) {
				$queries = array(
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'news', 'category_in_breadcrumb', '1');",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 13);
			}
			if (CONFIG_DB_VERSION < 14) {
				$queries = array(
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'users', 'enable_registration', '1');",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 14);
			}
			if (CONFIG_DB_VERSION < 15) {
				$queries = array(
					"UPDATE `{pre}settings` SET name = 'overlay' WHERE module = 'gallery' AND name = 'colorbox';",
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'guestbook', 'overlay', '1');",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 15);
			}
			if (CONFIG_DB_VERSION < 16) {
				$queries = array(
					"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'comments', 'emoticons', '1');",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 16);
			}
			if (CONFIG_DB_VERSION < 17) {
				$queries = array(
					"ALTER TABLE `{pre}menu_items` DROP `start`, DROP `end`;",
					"ALTER TABLE `{pre}seo` DROP INDEX `alias`, ADD INDEX (`alias`);",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 17);
			}
			if (CONFIG_DB_VERSION < 18) {
				$queries = array(
					"ALTER TABLE `{pre}files` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
					"ALTER TABLE `{pre}gallery` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
					"ALTER TABLE `{pre}news` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
					"ALTER TABLE `{pre}polls` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
					"ALTER TABLE `{pre}static_pages` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 18);
			}
			if (CONFIG_DB_VERSION < 19) {
				$queries = array(
					"ALTER TABLE `{pre}users` ADD COLUMN `super_user` TINYINT(1) UNSIGNED NOT NULL AFTER `id`;",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 19);
			}
			if (CONFIG_DB_VERSION < 20) {
				$queries = array(
					"ALTER TABLE `{pre}seo` ADD COLUMN `robots` TINYINT(1) UNSIGNED NOT NULL AFTER `description`;",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 20);
			}
			if (CONFIG_DB_VERSION < 21) {
				$queries = array(
					"ALTER TABLE `{pre}users` CHANGE `time_zone` `time_zone` VARCHAR(100) NOT NULL;",
					"UPDATE `{pre}users` SET time_zone = 'Europe/Berlin';",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 21);
			}
			if (CONFIG_DB_VERSION < 22) {
				$mod_id = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array('access'));
				$queries = array(
					"INSERT INTO `{pre}acl_resources` (`id`, `module_id`, `page`, `params`, `privilege_id`) VALUES ('', '" . $mod_id . "', 'create_resources', '', 4);",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 22);
			}
			if (CONFIG_DB_VERSION < 23) {
				$queries = array(
					"ALTER TABLE `{pre}acl_roles` ADD COLUMN `root_id` INT UNSIGNED NOT NULL AFTER `name`;",
					"UPDATE `{pre}acl_roles` SET root_id = 1",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 23);
			}
			if (CONFIG_DB_VERSION < 24) {
				// Neue Modulseiten
				Core\Functions::resetResources(2);
			}
			if (CONFIG_DB_VERSION < 25) {
				$queries = array(
					"ALTER TABLE `{pre}comments` ADD COLUMN `date2` DATETIME NOT NULL AFTER `id`;",
					"UPDATE `{pre}comments` SET date2 = FROM_UNIXTIME(date);",
					"ALTER TABLE `{pre}comments` DROP `date`;",
					"ALTER TABLE `{pre}comments` CHANGE `date2` `date` DATETIME NOT NULL;",
					"ALTER TABLE `{pre}guestbook` ADD COLUMN `date2` DATETIME NOT NULL AFTER `id`;",
					"UPDATE `{pre}guestbook` SET date2 = FROM_UNIXTIME(date);",
					"ALTER TABLE `{pre}guestbook` DROP `date`;",
					"ALTER TABLE `{pre}guestbook` CHANGE `date2` `date` DATETIME NOT NULL;",
					"ALTER TABLE `{pre}files` ADD COLUMN `start2` DATETIME NOT NULL AFTER `id`, ADD COLUMN `end2` DATETIME NOT NULL AFTER `start2`;",
					"UPDATE `{pre}files` SET start2 = FROM_UNIXTIME(start), end2 = FROM_UNIXTIME(end);",
					"ALTER TABLE `{pre}files` DROP `start`, DROP `end`;",
					"ALTER TABLE `{pre}files` CHANGE `start2` `start` DATETIME NOT NULL, CHANGE `end2` `end` DATETIME NOT NULL;",
					"ALTER TABLE `{pre}gallery` ADD COLUMN `start2` DATETIME NOT NULL AFTER `id`, ADD COLUMN `end2` DATETIME NOT NULL AFTER `start2`;",
					"UPDATE `{pre}gallery` SET start2 = FROM_UNIXTIME(start), end2 = FROM_UNIXTIME(end);",
					"ALTER TABLE `{pre}gallery` DROP `start`, DROP `end`;",
					"ALTER TABLE `{pre}gallery` CHANGE `start2` `start` DATETIME NOT NULL, CHANGE `end2` `end` DATETIME NOT NULL;",
					"ALTER TABLE `{pre}news` ADD COLUMN `start2` DATETIME NOT NULL AFTER `id`, ADD COLUMN `end2` DATETIME NOT NULL AFTER `start2`;",
					"UPDATE `{pre}news` SET start2 = FROM_UNIXTIME(start), end2 = FROM_UNIXTIME(end);",
					"ALTER TABLE `{pre}news` DROP `start`, DROP `end`;",
					"ALTER TABLE `{pre}news` CHANGE `start2` `start` DATETIME NOT NULL, CHANGE `end2` `end` DATETIME NOT NULL;",
					"ALTER TABLE `{pre}newsletter_archive` ADD COLUMN `date2` DATETIME NOT NULL AFTER `id`;",
					"UPDATE `{pre}newsletter_archive` SET date2 = FROM_UNIXTIME(date);",
					"ALTER TABLE `{pre}newsletter_archive` DROP `date`;",
					"ALTER TABLE `{pre}newsletter_archive` CHANGE `date2` `date` DATETIME NOT NULL;",
					"ALTER TABLE `{pre}polls` ADD COLUMN `start2` DATETIME NOT NULL AFTER `id`, ADD COLUMN `end2` DATETIME NOT NULL AFTER `start2`;",
					"UPDATE `{pre}polls` SET start2 = FROM_UNIXTIME(start), end2 = FROM_UNIXTIME(end);",
					"ALTER TABLE `{pre}polls` DROP `start`, DROP `end`;",
					"ALTER TABLE `{pre}polls` CHANGE `start2` `start` DATETIME NOT NULL, CHANGE `end2` `end` DATETIME NOT NULL;",
					"ALTER TABLE `{pre}poll_votes` ADD COLUMN `time2` DATETIME NOT NULL AFTER `ip`;",
					"UPDATE `{pre}poll_votes` SET time2 = FROM_UNIXTIME(time);",
					"ALTER TABLE `{pre}poll_votes` DROP `time`;",
					"ALTER TABLE `{pre}poll_votes` CHANGE `time2` `time` DATETIME NOT NULL;",
					"ALTER TABLE `{pre}static_pages` ADD COLUMN `start2` DATETIME NOT NULL AFTER `id`, ADD COLUMN `end2` DATETIME NOT NULL AFTER `start2`;",
					"UPDATE `{pre}static_pages` SET start2 = FROM_UNIXTIME(start), end2 = FROM_UNIXTIME(end);",
					"ALTER TABLE `{pre}static_pages` DROP `start`, DROP `end`;",
					"ALTER TABLE `{pre}static_pages` CHANGE `start2` `start` DATETIME NOT NULL, CHANGE `end2` `end` DATETIME NOT NULL;",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 25);
			}
			if (CONFIG_DB_VERSION < 26) {
				// Neue Modulseiten
				Core\Functions::resetResources(2);
			}
			if (CONFIG_DB_VERSION < 27) {
				$queries = array(
					"DELETE FROM `{pre}settings` WHERE module = 'contact' AND name = 'layout';",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 27);
			}
			if (CONFIG_DB_VERSION < 28) {
				// Neue Modulseiten
				Core\Functions::resetResources(2);
			}
			if (CONFIG_DB_VERSION < 29) {
				$queries = array(
					"ALTER TABLE `{pre}categories` ADD `module_id` INT UNSIGNED NOT NULL AFTER `description`;",
					"UPDATE `{pre}categories` AS c SET c.module_id = (SELECT m.id FROM `{pre}modules` AS m WHERE m.name = c.module);",
					"ALTER TABLE `{pre}categories` ADD INDEX (`module_id`);",
					"ALTER TABLE `{pre}categories` DROP `module`;",
					"ALTER TABLE `{pre}comments` ADD `module_id` INT UNSIGNED NOT NULL AFTER `message`;",
					"UPDATE `{pre}comments` AS c SET c.module_id = (SELECT m.id FROM `{pre}modules` AS m WHERE m.name = c.module);",
					"ALTER TABLE `{pre}comments` ADD INDEX (`module_id`, `entry_id`);",
					"ALTER TABLE `{pre}comments` DROP `module`;",
					"ALTER TABLE `{pre}settings` ADD `module_id` INT UNSIGNED NOT NULL AFTER `id`;",
					"UPDATE `{pre}settings` AS s SET s.module_id = (SELECT m.id FROM `{pre}modules` AS m WHERE m.name = s.module);",
					"ALTER TABLE `{pre}settings` DROP INDEX `module`, ADD UNIQUE (`module_id`, `name`);",
					"ALTER TABLE `{pre}settings` DROP `module`;",
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 29);
			}
			if (CONFIG_DB_VERSION < 30) {
				$system_settings = array(
					'cache_images' => CONFIG_CACHE_IMAGES,
					'cache_minify' => CONFIG_CACHE_MINIFY,
					'date_format_long' => CONFIG_DATE_FORMAT_LONG,
					'date_format_short' => CONFIG_DATE_FORMAT_SHORT,
					'date_time_zone' => 'Europe/Berlin',
					'design' => CONFIG_DESIGN,
					'entries' => CONFIG_ENTRIES,
					'flood' => CONFIG_FLOOD,
					'homepage' => CONFIG_HOMEPAGE,
					'lang' => CONFIG_LANG,
					'mailer_smtp_auth' => (int) CONFIG_MAILER_SMTP_AUTH,
					'mailer_smtp_host' => CONFIG_MAILER_SMTP_HOST,
					'mailer_smtp_password' => CONFIG_MAILER_SMTP_HOST,
					'mailer_smtp_port' => CONFIG_MAILER_SMTP_PORT,
					'mailer_smtp_security' => CONFIG_MAILER_SMTP_SECURITY,
					'mailer_smtp_user' => CONFIG_MAILER_SMTP_HOST,
					'mailer_type' => CONFIG_MAILER_TYPE,
					'maintenance_message' => CONFIG_MAINTENANCE_MESSAGE,
					'maintenance_mode' => (int) CONFIG_MAINTENANCE_MODE,
					'seo_aliases' => (int) CONFIG_SEO_ALIASES,
					'seo_meta_description' => CONFIG_SEO_META_DESCRIPTION,
					'seo_meta_keywords' => CONFIG_SEO_META_KEYWORDS,
					'seo_mod_rewrite' => (int) CONFIG_SEO_MOD_REWRITE,
					'seo_robots' => CONFIG_SEO_ROBOTS,
					'seo_title' => CONFIG_SEO_TITLE,
					'version' => CONFIG_VERSION,
					'wysiwyg' => CONFIG_WYSIWYG == 'fckeditor' ? 'ckeditor' : CONFIG_WYSIWYG
				);

				$mod_id = \ACP3\Core\Registry::get('Db')->fetchColumn('SELECT id FROM ' . DB_PRE . 'modules WHERE name = ?', array('system'));
				foreach ($system_settings as $key => $value) {
					\ACP3\Core\Registry::get('Db')->insert(DB_PRE . 'settings', array('id' => '', 'module_id' => $mod_id, 'name' => $key, 'value' => $value));
				}

				// DB-Config anpassen
				$system_config = array(
					'db_host' => CONFIG_DB_HOST,
					'db_name' => CONFIG_DB_NAME,
					'db_password' => CONFIG_DB_PASSWORD,
					'db_pre' => CONFIG_DB_PRE,
					'db_user' => CONFIG_DB_USER,
				);
				Core\Functions::writeConfigFile($system_config);

				$queries = array(
					"ALTER TABLE `{pre}modules` ADD `version` TINYINT(3) UNSIGNED NOT NULL AFTER `name`;",
					// Interne DB-Schema-Version der Module
					"UPDATE `{pre}modules` SET version = 30;"
				);
				$results[] = Core\Functions::executeSqlQueries($queries, 30);
			}

			\ACP3\Core\Registry::get('View')->assign('results', $results);

			// Cache leeren
			\ACP3\Core\Cache::purge('sql');
			\ACP3\Core\Cache::purge('tpl_compiled');
			\ACP3\Core\Cache::purge('minify');
		}

		\ACP3\Core\Registry::get('View')->assign('legacy', true);
	}

}