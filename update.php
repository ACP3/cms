<?php
header('Content-type: text/plain; charset=UTF-8');

define('ACP3_ROOT', './');
require ACP3_ROOT . 'includes/config.php';
require ACP3_ROOT . 'includes/classes/cache.php';
require ACP3_ROOT . 'includes/classes/config.php';
require ACP3_ROOT . 'includes/classes/db.php';

$queries = array(
	0 => 'CREATE TABLE `{pre}newsletter_archive` (`id` INT NOT NULL AUTO_INCREMENT, `date` VARCHAR(14) NOT NULL, `subject` VARCHAR(120) NOT NULL, `text` TEXT NOT NULL, `status` TINYINT NOT NULL, PRIMARY KEY (`id`)) {engine} {charset} ;',
	1 => 'RENAME TABLE `{pre}galpics` TO `{pre}gallery_pictures`, `{pre}nl_accounts` TO `{pre}newsletter_accounts`;',
);

if (count($queries) > 0) {
	$db = new db();

	if (version_compare(CONFIG_DB_TYPE == 'mysqli' ?  mysqli_get_client_info() : mysql_get_client_info(), '4.1', '>=')) {
		$engine = 'ENGINE=MyISAM';
		$charset = 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`';
	} else {
		$engine = 'TYPE=MyISAM';
		$charset = 'CHARSET=utf-8';
	}

	print "Aktualisierung der Datenbank:\n\n";
	$bool = false;

	foreach ($queries as $row) {
		$row = str_replace(array('{pre}', '{engine}', '{charset}'), array(CONFIG_DB_PRE, $engine, $charset), $row);
		$bool = $db->query($row, 3);
		if (!$bool && defined('DEBUG') && DEBUG) {
			print "\n";
		}
	}
	print "\n" . ($bool ? 'Die Datenbank wurde erfolgreich aktualisiert.' : 'Mindestens eine Datenbankänderung konnte nicht durchgeführt werden.') . "\n";
	print "\n----------------------------\n\n";
}

// Konfigurationsdatei aktualisieren
$config = array(
	'date' => CONFIG_DATE,
	'db_host' => CONFIG_DB_HOST,
	'db_name' => CONFIG_DB_NAME,
	'db_pre' => CONFIG_DB_PRE,
	'db_pwd' => CONFIG_DB_PWD,
	'db_type' => CONFIG_DB_TYPE,
	'db_user' => CONFIG_DB_USER,
	'design' => CONFIG_DESIGN,
	'dst' => CONFIG_DST,
	'entries' => CONFIG_ENTRIES,
	'flood' => CONFIG_FLOOD,
	'homepage' => CONFIG_HOMEPAGE,
	'lang' => CONFIG_LANG,
	'maintenance' => CONFIG_MAINTENANCE,
	'maintenance_msg' => CONFIG_MAINTENANCE_MSG,
	'meta_description' => CONFIG_META_DESCRIPTION,
	'meta_keywords' => CONFIG_META_KEYWORDS,
	'sef' => CONFIG_SEF,
	'time_zone' => CONFIG_TIME_ZONE,
	'title' => CONFIG_TITLE,
	'version' => 'ACP3 4.0RC1 SVN',
	'wysiwyg' => 'fckeditor'
);

echo config::general($config) ? 'Konfigurationsdatei erfolgreich aktualisiert.' : 'Konfigurationsdatei konnte nicht aktualisiert werden.';

// Cache leeren
cache::purge();
?>