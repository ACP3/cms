<?php
/**
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

// Standardzeitzone festlegen
date_default_timezone_set('Europe/Berlin');

// register_globals OFF Emulation
require_once ACP3_ROOT . 'includes/globals.php';

// Konfiguration des ACP3 laden
require_once ACP3_ROOT . 'includes/config.php';
if (!defined('INSTALLED')) {
	exit('Das ACP3 ist nicht richtig installiert. Bitte führen Sie den <a href="' . ACP3_ROOT . 'installation/">Installationsassistenten</a> aus und folgen Sie den Anweisungen.');
}

// Wenn der DEBUG Modus aktiv ist, Fehler ausgeben
error_reporting(E_ALL);

function __autoload($className)
{
	require_once ACP3_ROOT . 'includes/classes/' . $className . '.php';
}

// Einige Konstanten definieren
define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
$php_self = dirname(PHP_SELF);
define('ROOT_DIR', $php_self != '/' ? $php_self . '/' : '/');

// Smarty einbinden
define('SMARTY_DIR', ACP3_ROOT . 'includes/smarty/');
include SMARTY_DIR . 'Smarty.class.php';
$tpl = new smarty();
$tpl->template_dir = ACP3_ROOT . 'designs/' . CONFIG_DESIGN . '/';
$tpl->compile_dir = ACP3_ROOT . 'cache/';
$tpl->error_reporting = E_ALL;
// $tpl->compile_check = false;
if (!is_writable($tpl->compile_dir)) {
	exit('Bitte geben Sie dem "cache"-Ordner den CHMOD 777!');
}

// Einige Template Variablen setzen
$tpl->assign('PHP_SELF', PHP_SELF);
$tpl->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
$tpl->assign('ROOT_DIR', ROOT_DIR);
$tpl->assign('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
$tpl->assign('LANG', CONFIG_LANG);
$tpl->assign('PAGE_TITLE', CONFIG_TITLE);
$tpl->assign('KEYWORDS', CONFIG_META_KEYWORDS);
$tpl->assign('DESCRIPTION', CONFIG_META_DESCRIPTION);

$uri = new uri();

// Falls der Wartungsmodus aktiv ist, Wartungsnachricht ausgeben und Skript beenden
if (CONFIG_MAINTENANCE == '1' && defined('IN_ACP3')) {
	header('Content-Type: text/html; charset=UTF-8');
	$tpl->assign('maintenance_msg', CONFIG_MAINTENANCE_MSG);
	$tpl->display('maintenance.html');
	exit;
}

// Klassen initialisieren
$db = new db();
$db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PWD);
$tpl->assign('MODULES', new modules());

require_once ACP3_ROOT . 'includes/functions.php';
$auth = new auth();
$lang = new lang();
$date = new date();
?>