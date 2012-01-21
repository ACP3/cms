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

define('ACP3_ROOT', dirname(__FILE__) . '/../');
define('INCLUDES_DIR', ACP3_ROOT . 'includes/');
define('MODULES_DIR', ACP3_ROOT . 'modules/');

set_include_path(get_include_path() . PATH_SEPARATOR . ACP3_ROOT . 'includes/classes/');
spl_autoload_extensions('.class.php');
spl_autoload_register();

require ACP3_ROOT . 'includes/config.php';

define('NEW_VERSION', '4.0 SVN');
define('PHP_SELF', '');

if (!defined('CONFIG_DB_VERSION')) {
	define('CONFIG_DB_VERSION', 0);
}

$db = new db();
$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);
if ($handle !== true) {
	exit($handle);
}

$queries = array(
	1 => array(
		0 => 'UPDATE `{pre}menu_items` SET `mode` = 4 WHERE `uri` LIKE \'static_pages/list/id_%\' AND `mode` = 2;',
		1 => 'ALTER TABLE `{pre}users` ADD `date_format_long` VARCHAR(30) NOT NULL AFTER `skype`;',
		2 => 'ALTER TABLE `{pre}users` ADD `date_format_short` VARCHAR(30) NOT NULL AFTER `date_format_long`;',
		3 => 'ALTER TABLE `{pre}users` ADD `entries` TINYINT(2) UNSIGNED NOT NULL AFTER `language`;',
		4 => 'UPDATE `{pre}users` SET `date_format_long` = \'' . (defined('CONFIG_DATE_FORMAT_LONG') ? CONFIG_DATE_FORMAT_LONG : CONFIG_DATE_FORMAT) . '\', `date_format_short` = \'' . (defined('CONFIG_DATE_FORMAT_SHORT') ? CONFIG_DATE_FORMAT_SHORT : 'd.m.Y') . '\', `entries` = ' . ((int) CONFIG_ENTRIES) . ';',
		5 => 'UPDATE `{pre}access` SET `modules` =  \'access:16,acp:16,captcha:16,categories:16,comments:16,contact:16,emoticons:16,errors:16,feeds:16,files:16,gallery:16,guestbook:16,menu_items:16,news:16,newsletter:16,polls:16,search:16,static_pages:16,system:16,users:16\' WHERE `id` = 1;',
		6 => 'ALTER TABLE `{pre}guestbook` ADD `active` TINYINT(1) UNSIGNED NOT NULL AFTER `mail`;',
		7 => 'UPDATE `{pre}guestbook` SET `active` = 1;',
		8 => 'CREATE TABLE `{pre}aliases` (`uri` VARCHAR(255) NOT NULL, `alias` VARCHAR(100) NOT NULL, PRIMARY KEY (`uri`), UNIQUE KEY `alias` (`alias`)) {engine} {charset};',
	),
	2 => array(
		0 => 'RENAME TABLE `{pre}aliases` TO `{pre}seo`;',
		1 => 'ALTER TABLE `{pre}seo` ADD `keywords` VARCHAR(255) NOT NULL AFTER `alias`;',
		2 => 'ALTER TABLE `{pre}seo` ADD `description` VARCHAR(255) NOT NULL AFTER `keywords`;',
	),
	3 => array(
		0 => 'CREATE TABLE `{pre}settings` (`id` INT(10) unsigned NOT NULL AUTO_INCREMENT, `module` VARCHAR(40) NOT NULL, `name` VARCHAR(40) NOT NULL, `value` TEXT NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `module` (`module`,`name`)) {engine};',
	),
	4 => array(
		0 => 'ALTER TABLE `{pre}news` ADD `user_id` INT UNSIGNED NOT NULL;',
		1 => 'ALTER TABLE `{pre}files` ADD `user_id` INT UNSIGNED NOT NULL;',
		2 => 'ALTER TABLE `{pre}gallery` ADD `user_id` INT UNSIGNED NOT NULL;',
		3 => 'ALTER TABLE `{pre}poll_question` ADD `user_id` INT UNSIGNED NOT NULL;',
		4 => 'ALTER TABLE `{pre}static_pages` ADD `user_id` INT UNSIGNED NOT NULL;',
		5 => 'ALTER TABLE `{pre}newsletter_archive` ADD `user_id` INT UNSIGNED NOT NULL;',
		6 => 'RENAME TABLE `{pre}poll_question` TO `{pre}polls`;',
	),
	5 => array(
		0 => 'CREATE TABLE `{pre}modules` (`name` varchar(100) NOT NULL, `active` tinyint(1) unsigned NOT NULL, PRIMARY KEY (`name`)) {engine}',
	),
	6 => array(
		0 => 'DROP TABLE `{pre}access`',
		1 => 'ALTER TABLE `{pre}users` DROP COLUMN `access`',
		2 => 'CREATE TABLE `{pre}acl_privileges` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `key` varchar(100) NOT NULL, `name` varchar(100) NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `key` (`key`)) {engine};',
		3 => 'CREATE TABLE `{pre}acl_resources` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `path` varchar(255) NOT NULL, `privilege_id` int(10) unsigned NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `path` (`path`)) {engine};',
		4 => 'CREATE TABLE `{pre}acl_roles` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `name` varchar(100) NOT NULL, `left_id` int(10) unsigned NOT NULL, `right_id` int(10) unsigned NOT NULL, PRIMARY KEY (`id`)) {engine};',
		5 => 'CREATE TABLE `{pre}acl_role_privileges` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `role_id` int(10) unsigned NOT NULL, `privilege_id` int(10) unsigned NOT NULL, `value` tinyint(1) unsigned NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `role_id` (`role_id`,`privilege_id`)) {engine};',
		6 => 'CREATE TABLE `{pre}acl_user_roles` (`user_id` int(10) unsigned NOT NULL, `role_id` int(10) unsigned NOT NULL, PRIMARY KEY (`user_id`,`role_id`)) {engine};',
		7 => "INSERT INTO `{pre}acl_privileges` (`id`, `key`, `name`) VALUES (1, 'view', ''), (2, 'create', ''), (3, 'admin_view', ''), (4, 'admin_create', ''), (5, 'admin_edit', ''), (6, 'admin_delete', ''), (7, 'admin_settings', '');",
		8 => "INSERT INTO `{pre}acl_resources` (`id`, `path`, `privilege_id`) VALUES
			(3, 'access/adm_list/', 3),
			(4, 'access/create/', 4),
			(5, 'access/delete/', 6),
			(6, 'access/edit/', 5),
			(7, 'access/functions/', 1),
			(8, 'acp/adm_list/', 3),
			(9, 'captcha/image/', 1),
			(10, 'categories/adm_list/', 3),
			(11, 'categories/create/', 4),
			(12, 'categories/delete/', 6),
			(13, 'categories/edit/', 5),
			(14, 'categories/functions/', 1),
			(15, 'categories/settings/', 7),
			(16, 'comments/adm_list/', 3),
			(17, 'comments/create/', 2),
			(18, 'comments/delete_comments/', 1),
			(19, 'comments/delete_comments_per_module/', 1),
			(20, 'comments/edit/', 5),
			(21, 'comments/functions/', 1),
			(22, 'comments/settings/', 7),
			(23, 'contact/adm_list/', 3),
			(24, 'contact/imprint/', 1),
			(25, 'contact/list/', 1),
			(26, 'emoticons/adm_list/', 3),
			(27, 'emoticons/create/', 4),
			(28, 'emoticons/delete/', 6),
			(29, 'emoticons/edit/', 5),
			(30, 'emoticons/functions/', 1),
			(31, 'emoticons/settings/', 7),
			(32, 'errors/403/', 1),
			(33, 'errors/404/', 1),
			(34, 'feeds/list/', 1),
			(35, 'files/adm_list/', 3),
			(36, 'files/create/', 4),
			(37, 'files/delete/', 6),
			(38, 'files/details/', 1),
			(39, 'files/edit/', 5),
			(40, 'files/files/', 1),
			(41, 'files/functions/', 1),
			(42, 'files/list/', 1),
			(43, 'files/settings/', 7),
			(44, 'files/sidebar/', 1),
			(45, 'gallery/add_picture/', 1),
			(46, 'gallery/adm_list/', 3),
			(47, 'gallery/create/', 4),
			(48, 'gallery/delete_gallery/', 1),
			(49, 'gallery/delete_picture/', 1),
			(50, 'gallery/details/', 1),
			(51, 'gallery/edit_gallery/', 1),
			(52, 'gallery/edit_picture/', 1),
			(53, 'gallery/functions/', 1),
			(54, 'gallery/image/', 1),
			(55, 'gallery/list/', 1),
			(56, 'gallery/order/', 1),
			(57, 'gallery/pics/', 1),
			(58, 'gallery/settings/', 7),
			(59, 'gallery/sidebar/', 1),
			(60, 'guestbook/adm_list/', 3),
			(61, 'guestbook/create/', 4),
			(62, 'guestbook/delete/', 6),
			(63, 'guestbook/edit/', 5),
			(64, 'guestbook/list/', 1),
			(65, 'guestbook/settings/', 7),
			(66, 'menu_items/adm_list/', 3),
			(67, 'menu_items/adm_list_blocks/', 1),
			(68, 'menu_items/create/', 4),
			(69, 'menu_items/create_block/', 1),
			(70, 'menu_items/delete/', 6),
			(71, 'menu_items/delete_blocks/', 1),
			(72, 'menu_items/edit/', 5),
			(73, 'menu_items/edit_block/', 1),
			(74, 'menu_items/functions/', 1),
			(75, 'menu_items/order/', 1),
			(76, 'news/adm_list/', 3),
			(77, 'news/create/', 4),
			(78, 'news/delete/', 6),
			(79, 'news/details/', 1),
			(80, 'news/edit/', 5),
			(81, 'news/functions/', 1),
			(82, 'news/list/', 1),
			(83, 'news/settings/', 7),
			(84, 'news/sidebar/', 1),
			(85, 'newsletter/activate/', 1),
			(86, 'newsletter/adm_activate/', 1),
			(87, 'newsletter/adm_list/', 3),
			(88, 'newsletter/adm_list_archive/', 1),
			(89, 'newsletter/compose/', 1),
			(90, 'newsletter/create/', 4),
			(91, 'newsletter/delete/', 6),
			(92, 'newsletter/delete_archive/', 1),
			(93, 'newsletter/edit_archive/', 1),
			(94, 'newsletter/functions/', 1),
			(95, 'newsletter/send/', 1),
			(96, 'newsletter/settings/', 7),
			(97, 'polls/adm_list/', 3),
			(98, 'polls/create/', 4),
			(99, 'polls/delete/', 6),
			(100, 'polls/edit/', 5),
			(101, 'polls/list/', 1),
			(102, 'polls/result/', 1),
			(103, 'polls/sidebar/', 1),
			(104, 'polls/vote/', 1),
			(105, 'search/list/', 1),
			(106, 'static_pages/adm_list/', 3),
			(107, 'static_pages/create/', 4),
			(108, 'static_pages/delete/', 6),
			(109, 'static_pages/edit/', 5),
			(110, 'static_pages/functions/', 1),
			(111, 'static_pages/list/', 1),
			(112, 'system/adm_list/', 3),
			(113, 'system/configuration/', 1),
			(114, 'system/designs/', 1),
			(115, 'system/extensions/', 1),
			(116, 'system/languages/', 1),
			(117, 'system/maintenance/', 1),
			(118, 'system/modules/', 1),
			(119, 'system/server_config/', 1),
			(120, 'system/sql_export/', 1),
			(121, 'system/sql_import/', 1),
			(122, 'system/sql_optimisation/', 1),
			(123, 'system/update_check/', 1),
			(124, 'users/adm_list/', 3),
			(125, 'users/create/', 4),
			(126, 'users/delete/', 6),
			(127, 'users/edit/', 5),
			(128, 'users/edit_profile/', 1),
			(129, 'users/edit_settings/', 7),
			(130, 'users/forgot_pwd/', 1),
			(131, 'users/functions/', 1),
			(132, 'users/home/', 1),
			(133, 'users/list/', 1),
			(134, 'users/login/', 1),
			(135, 'users/logout/', 1),
			(136, 'users/register/', 1),
			(137, 'users/settings/', 7),
			(138, 'users/sidebar/', 1),
			(139, 'users/view_profile/', 1),
			(140, 'news/extensions/feeds/', 1),
			(141, 'news/extensions/search/', 1),
			(142, 'files/extensions/feeds/', 1),
			(143, 'files/extensions/search/', 1),
			(144, 'static_pages/extensions/search/', 1);",
		9 => "INSERT INTO `{pre}acl_roles` (`id`, `name`, `left_id`, `right_id`) VALUES (1, 'Gast', 1, 8), (2, 'Mitglied', 2, 7), (3, 'Autor', 3, 6), (4, 'Administrator', 4, 5);",
		10 => "INSERT INTO `{pre}acl_role_privileges` (`id`, `role_id`, `privilege_id`, `value`) VALUES (1, 1, 1, 1), (2, 1, 2, 1), (3, 1, 3, 0), (4, 1, 4, 0), (5, 1, 5, 0), (6, 1, 6, 0), (7, 1, 7, 0), (8, 2, 1, 2), (9, 2, 2, 2), (10, 2, 3, 2), (11, 2, 4, 2), (12, 2, 5, 2), (13, 2, 6, 2), (14, 2, 7, 2), (15, 3, 1, 2), (16, 3, 2, 2), (17, 3, 3, 1), (18, 3, 4, 1), (19, 3, 5, 1), (20, 3, 6, 1), (21, 3, 7, 2), (22, 4, 1, 2), (23, 4, 2, 2), (24, 4, 3, 2), (25, 4, 4, 2), (26, 4, 5, 2), (27, 4, 6, 2), (28, 4, 7, 1);",
		11 => "INSERT INTO `{pre}acl_user_roles` (`user_id`, `role_id`) VALUES (0, 1), (1, 4);",
	),
	7 => array(
		0 => "INSERT INTO {pre}acl_resources (`id`, `path`, `privilege_id`) VALUES ('', 'access/order/', 5);"
	),
	8 => array(
		0 => 'DELETE FROM {pre}acl_resources WHERE path = "system/server_config/"',
	),
	9 => array(
		0 => 'ALTER TABLE `{pre}acl_roles` ADD `parent_id` INT(10) NOT NULL AFTER `name`;',
		1 => 'ALTER TABLE `{pre}menu_items` ADD `parent_id` INT(10) NOT NULL AFTER `root_id`;',
	),
	10 => array(
		0 => 'ALTER TABLE `{pre}modules` DROP PRIMARY KEY;',
		1 => 'ALTER TABLE `{pre}modules` ADD COLUMN `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;',
		2 => 'DROP TABLE `{pre}acl_role_privileges`;',
		3 => 'CREATE TABLE`{pre}acl_rules` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `role_id` int(10) unsigned NOT NULL, `module_id` int(10) unsigned NOT NULL, `privilege_id` int(10) unsigned NOT NULL, `permission` tinyint(1) unsigned NOT NULL, PRIMARY KEY (`id`), UNIQUE KEY `role_id` (`role_id`,`module_id`,`privilege_id`)) {engine};',
		4 => 'DROP TABLE `{pre}acl_resources`;',
		5 => 'CREATE TABLE`{pre}acl_resources` (`id` int(10) unsigned NOT NULL AUTO_INCREMENT, `module_id` int(10) unsigned NOT NULL, `page` varchar(255) NOT NULL, `params` varchar(255) NOT NULL, `privilege_id` int(10) unsigned NOT NULL, PRIMARY KEY (`id`)) {engine};',
		6 => 'ALTER TABLE `{pre}acl_privileges` CHANGE `name` `description` VARCHAR(100) NOT NULL;',
	),
	11 => array(),
);

// Änderungen am DB Schema vornehmen
if (!empty($queries[CONFIG_DB_VERSION + 1])) {
	print "Aktualisierung der Datenbank:\n\n";

	$bool = null;

	$engine = 'ENGINE=MyISAM';
	$charset = 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`';

	$db->link->beginTransaction();

	$c_queries = count($queries);
	for ($i = CONFIG_DB_VERSION + 1; $i <= $c_queries; ++$i) {
		if (!empty($queries[$i])) {
			foreach ($queries[$i] as $row) {
				$row = str_replace(array('{engine}', '{charset}'), array($db->prefix, $engine, $charset), $row);
				$bool = $db->query($row, 3);
				if ($bool === null && defined('DEBUG') && DEBUG) {
					print "\n";
				}
			}
		}
	}

	$db->link->commit();

	print "\n" . ($bool ? 'Die Datenbank wurde erfolgreich aktualisiert!' : 'Mindestens eine Datenbankänderung konnte nicht durchgeführt werden!') . "\n";
	print "\n----------------------------\n\n";
}

if (CONFIG_DB_VERSION < 1) {
	$auth = new auth();
	$uri = new uri();
	$lang = new lang();
	require ACP3_ROOT . 'includes/functions.php';

	// URI-Aliase für die Statischen Seiten erzeugen
	$pages = $db->select('id, title', 'static_pages');
	$c_pages = count($pages);

	$db->link->beginTransaction();
	for ($i = 0; $i < $c_pages; ++$i) {
		seo::insertUriAlias(makeStringUrlSafe($pages[$i]['title']), 'static_pages/list/id_' . $pages[$i]['id']);
	}
	$db->link->commit();

	// URI-Aliase für die News erzeugen
	$news = $db->select('id, headline', 'news');
	$c_news = count($news);

	$db->link->beginTransaction();
	for ($i = 0; $i < $c_news; ++$i) {
		seo::insertUriAlias(makeStringUrlSafe($news[$i]['headline']), 'news/details/id_' . $news[$i]['id']);
	}
	$db->link->commit();

	// URI-Aliase für die Fotogalerien erzeugen
	require_once ACP3_ROOT . 'modules/gallery/functions.php';
	$galleries = $db->select('id, name', 'gallery');
	$c_galleries = count($galleries);

	$db->link->beginTransaction();
	for ($i = 0; $i < $c_galleries; ++$i) {
		seo::insertUriAlias(makeStringUrlSafe($galleries[$i]['name']), 'gallery/pics/id_' . $galleries[$i]['id']);
		generatePictureAliases($galleries[$i]['id']);
	}
	$db->link->commit();

	// URI-Aliase für die Downloads erzeugen
	$files = $db->select('id, link_title', 'files');
	$c_files = count($files);

	$db->link->beginTransaction();
	for ($i = 0; $i < $c_files; ++$i) {
		seo::insertUriAlias(makeStringUrlSafe($files[$i]['link_title']), 'files/details/id_' . $files[$i]['id']);
	}
	$db->link->commit();
}
if (CONFIG_DB_VERSION < 3) {
	$directories = scandir(MODULES_DIR);
	$count_dir = count($directories);
	for ($i = 0; $i < $count_dir; ++$i) {
		$settings = xml::parseXmlFile(MODULES_DIR . $directories[$i] . '/module.xml', 'settings');
		if (!empty($settings)) {
			foreach ($settings as $key => $value) {
				$db->insert('settings', array('id' => '','module' => $directories[$i], 'name' => $key, 'value' => $value));
			}
		}
	}
}
if (CONFIG_DB_VERSION < 4) {
	$user = $db->select('MIN(id) AS id', 'users');

	$db->update('files', array('user_id' => $user[0]['id']));
	$db->update('gallery', array('user_id' => $user[0]['id']));
	$db->update('news', array('user_id' => $user[0]['id']));
	$db->update('newsletter_archive', array('user_id' => $user[0]['id']));
	$db->update('polls', array('user_id' => $user[0]['id']));
	$db->update('static_pages', array('user_id' => $user[0]['id']));
}
if (CONFIG_DB_VERSION < 5) {
	$dir = scandir(MODULES_DIR);
	foreach ($dir as $row) {
		if ($row != '.' && $row != '..' && is_file(MODULES_DIR . '/' . $row . '/module.xml')) {
			$db->insert('modules', array('name' => $row, 'active' => 1));
		}
	}
}
if (CONFIG_DB_VERSION < 9) {
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
}
if (CONFIG_DB_VERSION < 10) {
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
		if ($row !== '.' && $row !== '..' && is_file(MODULES_DIR . $row . '/module.xml')) {
			$module = scandir(MODULES_DIR . $row . '/');
			$db->insert('modules', array('id' => '', 'name' => $row, 'active' => 1));
			$mod_id = $db->link->lastInsertId();

			if (is_file(MODULES_DIR . $row . '/extensions/search.php'))
				$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id, 'page' => 'extensions/search', 'params' => '', 'privilege_id' => 1));
			if (is_file(MODULES_DIR . $row . '/extensions/feeds.php'))
				$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id, 'page' => 'extensions/feeds', 'params' => '', 'privilege_id' => 1));

			foreach ($module as $file) {
				if ($file !== '.' && $file !== '..' && is_file(MODULES_DIR . $row . '/' . $file) && strpos($file, '.php') !== false) {
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
	$mod_id = $db->select('id', 'modules', 'name = \'access\'');

	$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => 'adm_list_resources', 'privilege_id' => 3));
	$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => 'edit_resource', 'privilege_id' => 5));
	$db->insert('acl_resources', array('id' => '', 'module_id' => $mod_id[0]['id'], 'page' => 'delete_resources', 'privilege_id' => 6));
}

// Konfigurationsdatei aktualisieren
$config = array(
	'db_version' => count($queries),
	'maintenance_mode' => (bool) CONFIG_MAINTENANCE_MODE,
	'seo_mod_rewrite' => (bool) CONFIG_SEO_MOD_REWRITE,
);

if (defined('CONFIG_DATE_FORMAT') && CONFIG_DB_VERSION == 0) {
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
if (!defined('CONFIG_MAILER_TYPE') === false) {
	define('CONFIG_MAILER_SMTP_AUTH', false);
	define('CONFIG_MAILER_SMTP_HOST', '');
	define('CONFIG_MAILER_SMTP_PASSWORD', '');
	define('CONFIG_MAILER_SMTP_PORT', 25);
	define('CONFIG_MAILER_SMTP_SECURITY', 'none');
	define('CONFIG_MAILER_SMTP_USER', '');
	define('CONFIG_MAILER_TYPE', 'mail');
}
print config::system($config) ? 'Konfigurationsdatei erfolgreich aktualisiert!' : 'Die Konfigurationsdatei konnte nicht aktualisiert werden!';

// Cache leeren
cache::purge();
cache::purge('tpl_compiled');