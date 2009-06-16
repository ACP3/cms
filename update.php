<?php
header('Content-type: text/plain; charset=UTF-8');

define('NEW_VERSION', '4.0 SVN');
define('ACP3_ROOT', './');

require ACP3_ROOT . 'includes/config.php';

require ACP3_ROOT . 'includes/classes/cache.php';
require ACP3_ROOT . 'includes/classes/config.php';
require ACP3_ROOT . 'includes/classes/db.php';

$queries = array(
	0 => 'UPDATE acp3_menu_items SET mode = 4 WHERE uri LIKE \'static_pages/list/id_%\' AND mode = 2;',
);

// Änderungen am DB Schema vornehmen
if (count($queries) > 0) {
	$db = new db();
	$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD);
	if ($handle !== true) {
		exit($handle);
	}

	print "Aktualisierung der Datenbank:\n\n";
	$bool = null;

	$engine = 'ENGINE=MyISAM';
	$charset = 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`';

	$db->link->beginTransaction();

	foreach ($queries as $row) {
		$row = str_replace(array('{pre}', '{engine}', '{charset}'), array(CONFIG_DB_PRE, $engine, $charset), $row);
		$bool = $db->query($row, 3);
		if ($bool === null && defined('DEBUG') && DEBUG) {
			print "\n";
		}
	}

	print "\n" . ($bool ? 'Die Datenbank wurde erfolgreich aktualisiert.' : 'Mindestens eine Datenbankänderung konnte nicht durchgeführt werden.') . "\n";
	print "\n----------------------------\n\n";
}

// Konfigurationsdatei aktualisieren
$config = array(
	'date_dst' => CONFIG_DATE_DST,
	'date_format' => CONFIG_DATE_FORMAT,
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

print config::system($config) ? 'Konfigurationsdatei erfolgreich aktualisiert.' : 'Konfigurationsdatei konnte nicht aktualisiert werden.';

// Cache leeren
cache::purge();
?>