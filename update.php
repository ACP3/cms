<?php
header('Content-type: text/plain; charset=UTF-8');

define('ACP3_ROOT', './');
require ACP3_ROOT . 'includes/config.php';
require ACP3_ROOT . 'includes/classes/db.php';
require ACP3_ROOT . 'includes/classes/cache.php';

$db = new db;

$queries = array(
	0 => 'UPDATE {pre}access SET modules =\'access:2,acp:2,captcha:2,categories:2,comments:2,contact:2,emoticons:2,errors:2,feeds:2,files:2,gallery:2,guestbook:2,news:2,newsletter:2,pages:2,polls:2,search:2,system:2,users:2\' WHERE id = \'1\'',
	1 => 'UPDATE {pre}access SET modules =\'access:0,acp:0,captcha:1,categories:1,comments:1,contact:1,emoticons:1,errors:2,feeds:1,files:1,gallery:1,guestbook:1,news:1,newsletter:1,pages:1,polls:1,search:1,system:0,users:1\' WHERE id = \'2\'',
	2 => 'UPDATE {pre}access SET modules =\'access:0,acp:0,captcha:1,categories:1,comments:1,contact:1,emoticons:1,errors:2,feeds:1,files:1,gallery:1,guestbook:1,news:1,newsletter:1,pages:1,polls:1,search:1,system:0,users:1\' WHERE id = \'3\'',
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

	echo $bool ? 'Konfigurationsdatei erfolgreich aktualisiert.' : 'Konfigurationsdatei konnte nicht aktualisiert werden.';
} else {
	echo 'Konfigurationsdatei konnte nicht aktualisiert werden.';
}
?>