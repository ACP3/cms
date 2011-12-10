<?php
/**
 * Updater
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
header('Content-type: text/plain; charset=UTF-8');

define('ACP3_ROOT', dirname(__FILE__) . '/');
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
	)
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
		foreach ($queries[$i] as $row) {
			$row = str_replace(array('{pre}', '{engine}', '{charset}'), array($db->prefix, $engine, $charset), $row);
			$bool = $db->query($row, 3);
			if ($bool === null && defined('DEBUG') && DEBUG) {
				print "\n";
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

// Konfigurationsdatei aktualisieren
$config = array(
	'db_version' => 3,
	'wysiwyg' => CONFIG_WYSIWYG == 'fckeditor' ? 'ckeditor' : CONFIG_WYSIWYG,
);

if (defined('CONFIG_DATE_FORMAT')) {
	$config['date_format_long'] = CONFIG_DATE_FORMAT;
	$config['date_format_short'] = 'd.m.Y';

	define('CONFIG_DATE_FORMAT_LONG', CONFIG_DATE_FORMAT);
	define('CONFIG_DATE_FORMAT_SHORT', $config['date_format_short']);
}

print config::system($config) ? 'Konfigurationsdatei erfolgreich aktualisiert!' : 'Die Konfigurationsdatei konnte nicht aktualisiert werden!';

// Cache leeren
cache::purge();