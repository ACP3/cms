<?php
header('Content-type: text/plain; charset=UTF-8');

require 'includes/config.php';
require 'includes/classes/db.php';
require 'includes/classes/cache.php';

$db = new db;

$cache = new cache;

$queries = array(
	0 => 'ALTER TABLE `' . CONFIG_DB_PRE . 'dl` RENAME `' . CONFIG_DB_PRE . 'files`',
	1 => 'UPDATE `' . CONFIG_DB_PRE . 'categories` SET module = \'files\' WHERE module = \'dl\'',
);

$successful = 'Abfrage erfolgreich durchgeführt!';
$unsuccessful = 'Abfrage gescheitert!';

if (count($queries) > 0) {
	foreach ($queries as $row) {
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