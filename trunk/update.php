<?php
/**
 * Updater
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */
header('Content-type: text/plain; charset=UTF-8');

define('NEW_VERSION', '4.0 SVN');
define('ACP3_ROOT', dirname(__FILE__));

require ACP3_ROOT . 'includes/config.php';

set_include_path(get_include_path() . PATH_SEPARATOR . ACP3_ROOT . 'includes/classes/');
spl_autoload_extensions('.class.php');
spl_autoload_register();

$queries = array(
	0 => 'UPDATE `{pre}menu_items` SET `mode` = 4 WHERE `uri` LIKE \'static_pages/list/id_%\' AND `mode` = 2;',
	1 => 'ALTER TABLE `{pre}users` ADD `date_format_long` VARCHAR(30) NOT NULL AFTER `skype`;',
	2 => 'ALTER TABLE `{pre}users` ADD `date_format_short` VARCHAR(30) NOT NULL AFTER `date_format_long`;',
	3 => 'ALTER TABLE `{pre}users` ADD `entries` TINYINT(2) UNSIGNED NOT NULL AFTER `language`;',
	4 => 'UPDATE `{pre}users` SET `date_format_long` = \'' . (defined('CONFIG_DATE_FORMAT_LONG') ? CONFIG_DATE_FORMAT_LONG : CONFIG_DATE_FORMAT) . '\', `date_format_short` = \'' . (defined('CONFIG_DATE_FORMAT_SHORT') ? CONFIG_DATE_FORMAT_SHORT : 'd.m.Y') . '\', `entries` = ' . ((int) CONFIG_ENTRIES) . ';',
	5 => 'UPDATE `{pre}access` SET `modules` =  \'access:16,acp:16,captcha:16,categories:16,comments:16,contact:16,emoticons:16,errors:16,feeds:16,files:16,gallery:16,guestbook:16,menu_items:16,news:16,newsletter:16,polls:16,search:16,static_pages:16,system:16,users:16\'  WHERE `id` = 1;',
	6 => 'ALTER TABLE `{pre}guestbook` ADD `active` TINYINT(1) UNSIGNED NOT NULL AFTER `mail`;',
	7 => 'UPDATE `{pre}guestbook` SET `active` = 1;',
	8 => 'CREATE TABLE `{pre}aliases` (`uri` varchar(255) NOT NULL, `alias` varchar(100) NOT NULL, PRIMARY KEY (`uri`), UNIQUE KEY `alias` (`alias`)) {engine} {charset};',
);

// Änderungen am DB Schema vornehmen
if (count($queries) > 0) {
	$db = new db();
	$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);
	if ($handle !== true) {
		exit($handle);
	}

	print "Aktualisierung der Datenbank:\n\n";
	$bool = null;

	$engine = 'ENGINE=MyISAM';
	$charset = 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`';

	$db->link->beginTransaction();

	foreach ($queries as $row) {
		$row = str_replace(array('{pre}', '{engine}', '{charset}'), array($db->prefix, $engine, $charset), $row);
		$bool = $db->query($row, 3);
		if ($bool === null && defined('DEBUG') && DEBUG) {
			print "\n";
		}
	}

	$db->link->commit();

	$uri = new uri();
	require ACP3_ROOT . 'includes/functions.php';

	// URI-Aliase für die Statischen Seiten erzeugen
	$pages = $db->select('id, title', 'static_pages');
	$c_pages = count($pages);

	$db->link->beginTransaction();
	for ($i = 0; $i < $c_pages; ++$i) {
		$uri->insertUriAlias(makeStringUrlSafe($pages[$i]['title']), 'static_pages/list/id_' . $pages[$i]['id']);
	}
	$db->link->commit();

	// URI-Aliase für die News erzeugen
	$news = $db->select('id, headline', 'news');
	$c_news = count($news);

	$db->link->beginTransaction();
	for ($i = 0; $i < $c_news; ++$i) {
		$uri->insertUriAlias(makeStringUrlSafe($news[$i]['headline']), 'news/details/id_' . $news[$i]['id']);
	}
	$db->link->commit();

	// URI-Aliase für die Fotogalerien erzeugen
	require_once ACP3_ROOT . 'modules/gallery/functions.php';
	$galleries = $db->select('id, name', 'gallery');
	$c_galleries = count($galleries);

	$db->link->beginTransaction();
	for ($i = 0; $i < $c_galleries; ++$i) {
		$uri->insertUriAlias(makeStringUrlSafe($galleries[$i]['name']), 'gallery/pics/id_' . $galleries[$i]['id']);
		generatePictureAliases($gallery[$i]['id']);
	}
	$db->link->commit();

	// URI-Aliase für die Downloads erzeugen
	$files = $db->select('id, link_title', 'files');
	$c_files = count($files);

	$db->link->beginTransaction();
	for ($i = 0; $i < $c_files; ++$i) {
		$uri->insertUriAlias(makeStringUrlSafe($files[$i]['link_title']), 'files/details/id_' . $files[$i]['id']);
	}
	$db->link->commit();

	print "\n" . ($bool ? 'Die Datenbank wurde erfolgreich aktualisiert!' : 'Mindestens eine Datenbankänderung konnte nicht durchgeführt werden!') . "\n";
	print "\n----------------------------\n\n";
}

// Konfigurationsdatei aktualisieren
$config = array(
	'date_dst' => CONFIG_DATE_DST,
	'date_time_zone' => CONFIG_DATE_TIME_ZONE,
	'db_host' => CONFIG_DB_HOST,
	'db_name' => CONFIG_DB_NAME,
	'db_pre' => CONFIG_DB_PRE,
	'db_password' => CONFIG_DB_PASSWORD,
	'db_user' => CONFIG_DB_USER,
	'design' => CONFIG_DESIGN,
	'entries' => CONFIG_ENTRIES,
	'flood' => CONFIG_FLOOD,
	'homepage' => CONFIG_HOMEPAGE,
	'lang' => CONFIG_LANG,
	'maintenance_mode' => CONFIG_MAINTENANCE_MODE,
	'maintenance_message' => CONFIG_MAINTENANCE_MESSAGE,
	'seo_meta_description' => CONFIG_SEO_META_DESCRIPTION,
	'seo_meta_keywords' => CONFIG_SEO_META_KEYWORDS,
	'seo_mod_rewrite' => CONFIG_SEO_MOD_REWRITE,
	'seo_title' => CONFIG_SEO_TITLE,
	'version' => NEW_VERSION,
	'wysiwyg' => CONFIG_WYSIWYG
);

if (defined('CONFIG_DATE_FORMAT')) {
	$config['date_format_long'] = CONFIG_DATE_FORMAT;
	$config['date_format_short'] = 'd.m.Y';
} else {
	$config['date_format_long'] = CONFIG_DATE_FORMAT_LONG;
	$config['date_format_short'] = CONFIG_DATE_FORMAT_SHORT;
}

print config::system($config) ? 'Konfigurationsdatei erfolgreich aktualisiert!' : 'Die Konfigurationsdatei konnte nicht aktualisiert werden!';

// Cache leeren
cache::purge();