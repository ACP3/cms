<?php
/**
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

// Debug - alle Fehler ausgeben
error_reporting(E_ALL);

// register_globals OFF Emulation
require_once 'includes/globals.php';

// Konfiguration des ACP3 laden
require_once 'includes/config.php';
if (!defined('INSTALLED')) {
	header('Location: installation/');
	exit;
}

function __autoload($className)
{
	require_once 'includes/classes/' . $className . '.php';
}

// Klassen initialisieren
$db = new db;
$modules = new modules;
$validate = new validate;
$config = new config;
$cache = new cache;
$breadcrumb = new breadcrumb;

require_once 'includes/functions.php';

// Smarty einbinden
define('SMARTY_DIR', './includes/smarty/');
include SMARTY_DIR . 'Smarty.class.php';
$tpl = new smarty;
$tpl->template_dir = './designs/' . CONFIG_DESIGN . '/';
$path = 'cache/' . CONFIG_DESIGN . '/';
if (is_writable('cache/') && !is_dir($path)) {
	mkdir($path, 0777);
	chmod($path, 0777);
}
$tpl->compile_dir = $path;

define('PHP_SELF', $_SERVER['PHP_SELF']);
$tpl->assign('php_self', PHP_SELF);
$tpl->assign('request_uri', htmlspecialchars($_SERVER['REQUEST_URI']));

// Root-Ordner ermitteln
$root = $_SERVER['PHP_SELF'];
$root = substr($root, 0, strrpos($root, '/') + 1);

define('ROOT_DIR', $root);
$tpl->assign('root_dir', $root);
$tpl->assign('design_path', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');

define('CHARSET', 'UTF-8');
?>