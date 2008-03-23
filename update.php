<?php
header('Content-type: text/plain; charset=UTF-8');

define('ACP3_ROOT', './')
require ACP3_ROOT . 'includes/config.php';
require ACP3_ROOT . 'includes/classes/db.php';
require ACP3_ROOT . 'includes/classes/cache.php';

/*
$db = new db;

$queries = array(
	0 => 'ALTER TABLE `{pre}categories` ADD `picture` VARCHAR( 120 ) {charset} NOT NULL AFTER `name` ;',
);

if (version_compare(mysql_get_client_info(), '4.1', '>=')) {
	$charset = 'CHARACTER SET `utf8` COLLATE `utf8_general_ci`';
} else {
	$charset = 'CHARSET=utf-8';
}

print 'Aktualisierung der Datenbank:' . "\n\n";
$success = true;

if (count($queries) > 0) {
	foreach ($queries as $row) {
		$row = str_replace(array('{pre}', '{charset}'), array(CONFIG_DB_PRE, $charset), $row);
		$bool = $db->query($row, 3);
		if (!$bool) {
			print "\n";
			$success = false;
		}
	}
}

print "\n" . ($success ? 'Die Datenbank wurde erfolgreich aktualisiert.' : 'Mindestens eine Datenbankänderung konnte nicht durchgeführt werden.') . "\n";

// Gecachete SQL Queries löschen
cache::purge();

print "\n" . '----------------------------' . "\n\n";
*/

// Konfigurationsdatei aktualisieren
$path = ACP3_ROOT . 'includes/config.php';
if (is_writable($path))	{
	// Konfigurationsdatei in ein Array schreiben
	$config = file($path);
	$entries_to_change = array(
		'define(\'CONFIG_VERSION\', \'' . CONFIG_VERSION . '\');' => 'define(\'CONFIG_VERSION\', \'4.0b10 SVN\');',
	);

	foreach ($config as $c_key => $c_value) {
		if (array_key_exists(substr($c_value, 0, -1), $entries_to_change)) {
			$config[$c_key] = $entries_to_change[substr($c_value, 0, -1)] . "\n";
		}
	}
	$bool = @file_put_contents($path, $config);

	echo $bool ? 'Konfigurationsdatei erfolgreich aktualisiert!' : 'Konfigurationsdatei konnte nicht aktualisiert werden!';
} else {
	echo 'Konfigurationsdatei konnte nicht aktualisiert werden!';
}
?>