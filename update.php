<?php
header('Content-type: text/plain; charset=UTF-8');

require 'includes/config.php';
require 'includes/classes/db.php';
require 'includes/classes/cache.php';

$db = new db;

$cache = new cache;

$queries = array();

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
		'define(\'CONFIG_DB\', \'' . CONFIG_DB . '\');' => 'define(\'CONFIG_DB_NAME\', \'' . CONFIG_DB . '\');',
		'define(\'CONFIG_HOST\', \'' . CONFIG_HOST . '\');' => 'define(\'CONFIG_DB_HOST\', \'' . CONFIG_HOST . '\');',
		'define(\'CONFIG_PRE\', \'' . CONFIG_PRE . '\');' => 'define(\'CONFIG_DB_PRE\', \'' . CONFIG_PRE . '\');',
		'define(\'CONFIG_PWD\', \'' . CONFIG_PWD . '\');' => 'define(\'CONFIG_DB_PWD\', \'' . CONFIG_PWD . '\');',
		'define(\'CONFIG_TYPE\', \'' . CONFIG_TYPE . '\');' => 'define(\'CONFIG_DB_TYPE\', \'' . CONFIG_TYPE . '\');',
		'define(\'CONFIG_USER\', \'' . CONFIG_USER . '\');' => 'define(\'CONFIG_DB_USER\', \'' . CONFIG_USER . '\');',
		'define(\'CONFIG_VERSION\', \'' . CONFIG_VERSION . '\');' => 'define(\'CONFIG_VERSION\', \'4.0b7\');',
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