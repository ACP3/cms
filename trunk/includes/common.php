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
	header('Location:' . ACP3_ROOT . 'installation/');
	exit;
}

// Wenn der DEBUG Modus aktiv ist, Fehler ausgeben
error_reporting(defined('DEBUG') && DEBUG ? E_ALL|E_STRICT : null);

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
$tpl = new smarty;
$tpl->template_dir = ACP3_ROOT . 'designs/' . CONFIG_DESIGN . '/';
$tpl->compile_dir = ACP3_ROOT . 'cache/';
$tpl->error_reporting = defined('DEBUG') && DEBUG ? E_ALL|E_STRICT : null;
// $tpl->compile_check = false;

// Einige Template Variablen setzen
$tpl->assign('PHP_SELF', PHP_SELF);
$tpl->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
$tpl->assign('ROOT_DIR', ROOT_DIR);
$tpl->assign('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
$tpl->assign('LANG', CONFIG_LANG);
$tpl->assign('PAGE_TITLE', CONFIG_TITLE);
$tpl->assign('KEYWORDS', CONFIG_META_KEYWORDS);
$tpl->assign('DESCRIPTION', CONFIG_META_DESCRIPTION);

// Falls der Wartungsmodus aktiv ist, Wartungsnachricht ausgeben und Skript beenden
if (CONFIG_MAINTENANCE == '1' && defined('IN_ACP3')) {
	header('Content-Type: text/html; charset=UTF-8');
	$tpl->assign('maintenance_msg', CONFIG_MAINTENANCE_MSG);
	$tpl->display('maintenance.html');
	exit;
}
// Klassen initialisieren
$db = new db;
$uri = new uri;

$tpl->assign('MODULES', new modules);

require_once ACP3_ROOT . 'includes/functions.php';
?>