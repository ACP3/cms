<?php
header('Content-type: text/plain; charset=UTF-8');

require 'includes/config.php';
require 'includes/classes/db.php';
require 'includes/classes/cache.php';

$db = new db;

$cache = new cache;

$queries = array(
	0 => 'ALTER TABLE `{pre}dl` RENAME `{pre}files`',
	1 => 'UPDATE `{pre}categories` SET module = \'files\' WHERE module = \'dl\'',
	2 => 'ALTER TABLE `{pre}access` CHANGE `mods` `modules` TEXT NOT NULL',
	3 => 'TRUNCATE TABLE `{pre}access`',
	4 => 'INSERT INTO `{pre}access` VALUES (\'1\', \'Administrator\', \'users:2,feeds:2,files:2,emoticons:2,errors:2,gallery:2,gb:2,categories:2,comments:2,contact:2,pages:2,news:2,newsletter:2,search:2,system:2,polls:2,access:2\'), (\'2\', \'Besucher\', \'users:1,feeds:1,files:1,emoticons:1,errors:1,gallery:1,gb:1,categories:1,comments:1,contact:1,pages:1,news:1,newsletter:1,search:1,system:0,polls:1,access:0\'), (\'3\', \'Benutzer\', \'users:1,feeds:1,files:1,emoticons:1,errors:1,gallery:1,gb:1,categories:1,comments:1,contact:1,pages:1,news:1,newsletter:1,search:1,system:0,polls:1,access:0\');',
	5 => 'ALTER TABLE `{pre}users` ADD `draft` TEXT {charset} NOT NULL AFTER `mail`;',
);

$successful = 'Abfrage erfolgreich durchgeführt!';
$unsuccessful = 'Abfrage gescheitert!';

if (version_compare(mysql_get_client_info(), '4.1', '>=')) {
	$charset = ' CHARACTER SET `utf8` COLLATE `utf8_general_ci`';
} else {
	$charset = 'CHARSET=utf-8';
}

if (count($queries) > 0) {
	foreach ($queries as $row) {
		$row = str_replace(array('{pre}', '{charset}'), array(CONFIG_DB_PRE, $charset), $row);
		$bool = $db->query($row, 3);
		echo $row . ' - ' . ($bool ? $successful : $unsuccessful) . "\n\n";
	}
}

// Gecacheten SQL Queries löschen
$cache->purge();

// Konfigurationsdatei aktualisieren
$path = 'includes/config.php';
if (is_writable($path))	{
	// Konfigurationsdatei in ein Array schreiben
	$config = file($path);
	$entries_to_change = array(
		'define(\'CONFIG_VERSION\', \'' . CONFIG_VERSION . '\');' => 'define(\'CONFIG_VERSION\', \'4.0b8\');',
	);

	foreach ($config as $c_key => $c_value) {
		if (array_key_exists(substr($c_value, 0, -1), $entries_to_change)) {
			$config[$c_key] = $entries_to_change[substr($c_value, 0, -1)] . "\n";
		}
	}
	$bool = file_put_contents($path, $config);

	echo $bool ? 'Konfigurationsdatei erfolgreich aktualisiert!' : 'Konfigurationsdatei konnte nicht aktualisiert werden!';
} else {
	echo 'Konfigurationsdatei konnte nicht aktualisiert werden!';
}
?>