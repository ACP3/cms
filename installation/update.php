<?php
/**
 * Updater
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
header('Content-type: text/plain; charset=UTF-8');

define('IN_ACP3', true);

define('ACP3_ROOT', realpath(dirname(__FILE__) . '/../') . '/');
define('INCLUDES_DIR', ACP3_ROOT . 'includes/');
define('MODULES_DIR', ACP3_ROOT . 'modules/');

set_include_path(get_include_path() . PATH_SEPARATOR . ACP3_ROOT . 'includes/classes/');
spl_autoload_extensions('.class.php');
spl_autoload_register();

require ACP3_ROOT . 'includes/config.php';

define('NEW_VERSION', '4.0 SVN');
define('PHP_SELF', '');

if (defined('CONFIG_DB_VERSION') === false) {
	define('CONFIG_DB_VERSION', (int) 0);
}

$db = new db();
$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);
if ($handle !== true) {
	exit($handle);
}

/**
 * Führt die Datenbankschema-Änderungen durch
 *
 * @param array $queries
 *	Array mit durchführenden Datenbankschema-Änderungen
 * @param integer $version
 *	Version der Datenbank, auf welche aktualisiert werden soll
 */
function executeSqlQueries(array $queries, $version)
{
	global $db;
	static $current_version = 0;

	if ($current_version === 0)
		$current_version = (int) CONFIG_DB_VERSION;

	printf('Aktualisierung der Datenbank von Version %d auf %d: ', $current_version, $version);
	$success = true;

	$engine = 'ENGINE=MyISAM';
	$charset = 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`';

	$db->link->beginTransaction();

	foreach($queries as $row) {
		if (!empty($row)) {
			$row = str_replace(array('{engine}', '{charset}'), array($engine, $charset), $row);
			$bool = $db->query($row, 3);
			if ($bool === false && defined('DEBUG') === true && DEBUG === true) {
				$success = false;
				print "\n";
			}
		}
	}

	$db->link->commit();

	echo ($success === true ? 'Die Datenbank wurde erfolgreich aktualisiert!' : 'Mindestens eine Datenbankänderung konnte nicht durchgeführt werden!') . "\n\n";
	echo '----------------------------' . "\n\n";

	$current_version = $version;
}

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
	echo executeSqlQueries($queries, 1);
}
if (CONFIG_DB_VERSION < 2) {
	$queries = array(
		'RENAME TABLE `{pre}aliases` TO `{pre}seo`;',
		'ALTER TABLE `{pre}seo` ADD `keywords` VARCHAR(255) NOT NULL AFTER `alias`;',
		'ALTER TABLE `{pre}seo` ADD `description` VARCHAR(255) NOT NULL AFTER `keywords`;',
	);
	echo executeSqlQueries($queries, 2);
}
if (CONFIG_DB_VERSION < 3) {
	$queries = array(
		'CREATE TABLE `{pre}settings` (`id` INT(10) unsigned NOT NULL AUTO_INCREMENT, `module` VARCHAR(40) NOT NULL, `name` VARCHAR(40) NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `module` (`module`,`name`)) {engine} {charset};',
	);
	echo executeSqlQueries($queries, 3);

	$dir = scandir(MODULES_DIR);
	foreach ($dir as $row) {
		if ($row !== '.' && $row !== '..' && is_file(MODULES_DIR . $row . '/module.xml') === true) {
			$settings = xml::parseXmlFile(MODULES_DIR . $row . '/module.xml', 'settings');
			if (!empty($settings)) {
				foreach ($settings as $key => $value) {
					$db->insert('settings', array('id' => '','module' => $row, 'name' => $key, 'value' => $value));
				}
			}
		}
	}
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
	echo executeSqlQueries($queries, 4);

	$user = $db->select('MIN(id) AS id', 'users');

	$db->update('files', array('user_id' => $user[0]['id']));
	$db->update('gallery', array('user_id' => $user[0]['id']));
	$db->update('news', array('user_id' => $user[0]['id']));
	$db->update('newsletter_archive', array('user_id' => $user[0]['id']));
	$db->update('polls', array('user_id' => $user[0]['id']));
	$db->update('static_pages', array('user_id' => $user[0]['id']));
}
if (CONFIG_DB_VERSION < 5) {
	$queries = array(
		'CREATE TABLE `{pre}modules` (`name` varchar(100) NOT NULL, `active` tinyint(1) unsigned NOT NULL, PRIMARY KEY (`name`)) {engine} {charset}',
	);
	echo executeSqlQueries($queries, 5);

	$dir = scandir(MODULES_DIR);
	foreach ($dir as $row) {
		if ($row !== '.' && $row !== '..' && is_file(MODULES_DIR . $row . '/module.xml') === true) {
			$db->insert('modules', array('name' => $row, 'active' => 1));
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
	);
	echo executeSqlQueries($queries, 10);

	$roles = $db->select('id, left_id, right_id', 'acl_roles');
	foreach ($roles as $row) {
		$parent = $db->select('id', 'acl_roles', 'left_id < ' . $row['left_id'] . ' AND right_id > ' . $row['right_id'], 'left_id DESC', 1);
		$db->update('acl_roles', array('parent_id' => !empty($parent) ? $parent[0]['id'] : 0), 'id = \'' . $row['id'] . '\'');
	}

	$pages = $db->select('id, left_id, right_id', 'menu_items');
	foreach ($pages as $row) {
		$parent = $db->select('id', 'menu_items', 'left_id < ' . $row['left_id'] . ' AND right_id > ' . $row['right_id'], 'left_id DESC', 1);
		$db->update('menu_items', array('parent_id' => !empty($parent) ? $parent[0]['id'] : 0), 'id = \'' . $row['id'] . '\'');
	}

	$db->link->beginTransaction();

	// Bestehende Tabellen leeren
	$db->query('TRUNCATE TABLE {pre}modules', 0);
	$db->query('TRUNCATE TABLE {pre}acl_resources', 0);
	$db->query('TRUNCATE TABLE {pre}acl_rules', 0);

	$special_resources = array(
		'comments' => array(
			'create' => 2,
		),
		'gallery' => array(
			'add_picture' => 4,
		),
		'guestbook' => array(
			'create' => 2,
		),
		'newsletter' => array(
			'compose' => 4,
			'create' => 2,
			'adm_activate' => 3,
			'sent' => 4,
		),
		'system' => array(
			'configuration' => 7,
			'designs' => 7,
			'extensions' => 7,
			'languages' => 7,
			'maintenance' => 7,
			'modules' => 7,
			'sql_export' => 7,
			'sql_import' => 7,
			'sql_optimisation' => 7,
			'update_check' => 3,
		),
		'users' => array(
			'edit_profile' => 1,
			'edit_settings' => 1,
		),
	);

	// Moduldaten in die ACL schreiben
	$modules = scandir(MODULES_DIR);
	foreach ($modules as $row) {
		if ($row !== '.' && $row !== '..' && is_file(MODULES_DIR . $row . '/module.xml') === true) {
			$module = scandir(MODULES_DIR . $row . '/');
			$db->insert('modules', array('id' => '', 'name' => $row, 'active' => 1));
			$mod_id = $db->link->lastInsertId();

			if (is_file(MODULES_DIR . $row . '/extensions/search.php') === true)
				$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id, 'page' => 'extensions/search', 'params' => '', 'privilege_id' => 1));
			if (is_file(MODULES_DIR . $row . '/extensions/feeds.php') === true)
				$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id, 'page' => 'extensions/feeds', 'params' => '', 'privilege_id' => 1));

			foreach ($module as $file) {
				if ($file !== '.' && $file !== '..' && is_file(MODULES_DIR . $row . '/' . $file) === true && strpos($file, '.php') !== false) {
					$file = substr($file, 0, -4);
					if (isset($special_resources[$row][$file])) {
						$privilege_id = $special_resources[$row][$file];
					} else {
						$privilege_id = 1;
						if (strpos($file, 'adm_list') === 0)
							$privilege_id = 3;
						if (strpos($file, 'create') === 0 || strpos($file, 'order') === 0)
							$privilege_id = 4;
						if (strpos($file, 'edit') === 0)
							$privilege_id = 5;
						if (strpos($file, 'delete') === 0)
							$privilege_id = 6;
						if (strpos($file, 'settings') === 0)
							$privilege_id = 7;
					}
					$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id, 'page' => $file, 'params' => '', 'privilege_id' => $privilege_id));
				}
			}
		}
	}

	$roles = $db->select('id', 'acl_roles');
	$modules = $db->select('id', 'modules');
	$privileges = $db->select('id', 'acl_privileges');

	foreach ($roles as $role) {
		foreach ($modules as $module) {
			foreach ($privileges as $privilege) {
				$permission = 0;
				if ($role['id'] == 1 && ($privilege['id'] == 1 || $privilege['id'] == 2))
					$permission = 1;
				if ($role['id'] > 1 && $role['id'] < 4)
					$permission = 2;
				if ($role['id'] == 3 && $privilege['id'] == 3)
					$permission = 1;
				if ($role['id'] == 4)
					$permission = 1;

				$db->insert('acl_rules', array('id' => '', 'role_id' => $role['id'], 'module_id' => $module['id'], 'privilege_id' => $privilege['id'], 'permission' => $permission));
			}
		}
	}

	$db->link->commit();
}
if (CONFIG_DB_VERSION < 11) {
	$queries = array(
		"INSERT INTO {pre}acl_resources (`id`, `path`, `privilege_id`) VALUES ('', 'access/order/', 5);"
	);
	echo executeSqlQueries($queries, 11);

	$mod_id = $db->select('id', 'modules', 'name = \'access\'');

	$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => 'adm_list_resources', 'privilege_id' => 3));
	$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => 'edit_resource', 'privilege_id' => 5));
	$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => 'delete_resources', 'privilege_id' => 6));
}
if (CONFIG_DB_VERSION < 12) {
	$queries = array(
		'CREATE TABLE `{pre}sessions` (`session_id` varchar(32) NOT NULL, `session_starttime` int(10) unsigned NOT NULL, `session_data` text NOT NULL, PRIMARY KEY (`session_id`)) {engine} {charset};'
	);
	echo executeSqlQueries($queries, 12);
}
if (CONFIG_DB_VERSION < 13) {
	$queries = array(
		"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'news', 'category_in_breadcrumb', '1');",
	);
	echo executeSqlQueries($queries, 13);
}
if (CONFIG_DB_VERSION < 14) {
	$queries = array(
		"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'users', 'enable_registration', '1');",
	);
	echo executeSqlQueries($queries, 14);
}
if (CONFIG_DB_VERSION < 15) {
	$queries = array(
		"UPDATE `{pre}settings` SET name = 'overlay' WHERE module = 'gallery' AND name = 'colorbox';",
		"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'guestbook', 'overlay', '1');",
	);
	echo executeSqlQueries($queries, 15);
}
if (CONFIG_DB_VERSION < 16) {
	$queries = array(
		"INSERT INTO `{pre}settings` (`id`, `module`, `name`, `value`) VALUES ('', 'comments', 'emoticons', '1');",
	);
	echo executeSqlQueries($queries, 16);
}
if (CONFIG_DB_VERSION < 17) {
	$queries = array(
		"ALTER TABLE `{pre}menu_items` DROP `start`, DROP `end`;",
		"ALTER TABLE `{pre}seo` DROP INDEX `alias`, ADD INDEX (`alias`);",
	);
	echo executeSqlQueries($queries, 17);
}
if (CONFIG_DB_VERSION < 18) {
	$queries = array(
		"ALTER TABLE `{pre}files` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
		"ALTER TABLE `{pre}gallery` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
		"ALTER TABLE `{pre}news` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
		"ALTER TABLE `{pre}polls` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
		"ALTER TABLE `{pre}static_pages` CHANGE `start` `start` INT UNSIGNED NOT NULL, CHANGE `end` `end` INT UNSIGNED NOT NULL;",
	);
	echo executeSqlQueries($queries, 18);
}
if (CONFIG_DB_VERSION < 19) {
	$queries = array(
		"ALTER TABLE `{pre}users` ADD COLUMN `super_user` TINYINT(1) UNSIGNED NOT NULL AFTER `id`;",
	);
	echo executeSqlQueries($queries, 19);
}

// Konfigurationsdatei aktualisieren
$config = array(
	'db_version' => 19,
	'maintenance_mode' => (bool) CONFIG_MAINTENANCE_MODE,
	'seo_mod_rewrite' => (bool) CONFIG_SEO_MOD_REWRITE,
);

if (defined('CONFIG_DATE_FORMAT') === true && CONFIG_DB_VERSION == 0) {
	$config['wysiwyg'] = CONFIG_WYSIWYG == 'fckeditor' ? 'ckeditor' : CONFIG_WYSIWYG;
	$config['date_format_long'] = CONFIG_DATE_FORMAT;
	$config['date_format_short'] = 'd.m.Y';

	define('CONFIG_DATE_FORMAT_LONG', CONFIG_DATE_FORMAT);
	define('CONFIG_DATE_FORMAT_SHORT', $config['date_format_short']);
}
if (defined('CONFIG_CACHE_IMAGES') == false) {
	define('CONFIG_CACHE_IMAGES', true);
	define('CONFIG_CACHE_MINIFY', 3600);
	define('CONFIG_SEO_ALIASES', true);
}
if (defined('CONFIG_MAILER_TYPE') === false) {
	define('CONFIG_MAILER_SMTP_AUTH', false);
	define('CONFIG_MAILER_SMTP_HOST', '');
	define('CONFIG_MAILER_SMTP_PASSWORD', '');
	define('CONFIG_MAILER_SMTP_PORT', 25);
	define('CONFIG_MAILER_SMTP_SECURITY', 'none');
	define('CONFIG_MAILER_SMTP_USER', '');
	define('CONFIG_MAILER_TYPE', 'mail');
}
print config::system($config) === true ? 'Konfigurationsdatei erfolgreich aktualisiert!' : 'Die Konfigurationsdatei konnte nicht aktualisiert werden!';

// Cache leeren
cache::purge();
cache::purge('tpl_compiled');