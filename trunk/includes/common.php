<?php
/**
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

// Standardzeitzone festlegen
date_default_timezone_set('UTC');

// register_globals OFF Emulation
require_once ACP3_ROOT . 'includes/globals.php';

// Konfiguration des ACP3 laden
require_once ACP3_ROOT . 'includes/config.php';
if (defined('INSTALLED') === false)
	exit('Das ACP3 ist nicht richtig installiert. Bitte führen Sie den <a href="' . ACP3_ROOT . 'installation/">Installationsassistenten</a> aus und folgen Sie den Anweisungen.');

// Wenn der DEBUG Modus aktiv ist, Fehler ausgeben
$reporting_level = defined('DEBUG') === true && DEBUG === true ? E_ALL : 0;
error_reporting($reporting_level);

// Einige Konstanten definieren
define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
$php_self = dirname(PHP_SELF);
define('ROOT_DIR', $php_self != '/' ? $php_self . '/' : '/');
define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');
define('MODULES_DIR', ACP3_ROOT . 'modules/');
define('INCLUDES_DIR', ACP3_ROOT . 'includes/');

set_include_path(get_include_path() . PATH_SEPARATOR . INCLUDES_DIR . 'classes/');
spl_autoload_extensions('.class.php');
spl_autoload_register();

// Klassen initialisieren
$db = new db();
$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);
if ($handle !== true) {
	exit($handle);
}
$auth = new auth();
$uri = new uri();
$lang = new lang();
$date = new date();

acl::initialize($auth->getUserId());

// Smarty einbinden
require INCLUDES_DIR . 'smarty/Smarty.class.php';
$tpl = new Smarty();
$tpl->error_reporting = $reporting_level;
$tpl->compile_id = CONFIG_DESIGN . '_' . $auth->getUserLanguage();
$tpl->setCompileCheck(defined('DEBUG') === true && DEBUG === true);
$tpl->setTemplateDir(ACP3_ROOT . 'designs/' . CONFIG_DESIGN . '/')
	->addPluginsDir(INCLUDES_DIR . 'smarty/custom/')
	->setCompileDir(ACP3_ROOT . 'uploads/cache/tpl_compiled/')
	->setCacheDir(ACP3_ROOT . 'uploads/cache/tpl_cached/');
if (is_writable($tpl->getCompileDir()) === false || is_writable($tpl->getCacheDir()) === false) {
	exit('Bitte geben Sie dem "cache"-Ordner den CHMOD 777!');
}

require_once INCLUDES_DIR . 'functions.php';

// Einige Template Variablen setzen
$tpl->assign('PHP_SELF', PHP_SELF);
$tpl->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
$tpl->assign('ROOT_DIR', ROOT_DIR);
$tpl->assign('DESIGN_PATH', DESIGN_PATH);
$tpl->assign('LANG', CONFIG_LANG);

// Falls der Wartungsmodus aktiv ist, Wartungsnachricht ausgeben
if (defined('IN_ADM') === false && CONFIG_MAINTENANCE_MODE === true) {
	$tpl->assign('MAINTENANCE_MSG', CONFIG_MAINTENANCE_MESSAGE);
	define('CUSTOM_LAYOUT', 'maintenance.tpl');
}