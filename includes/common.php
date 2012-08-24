<?php
/**
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Core
 */

if (defined('IN_ACP3') === false)
	exit;

// Wenn der DEBUG Modus aktiv ist, Fehler ausgeben
$reporting_level = defined('DEBUG') === true && DEBUG === true ? E_ALL : 0;
error_reporting($reporting_level);

// Einige Konstanten definieren
define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
$php_self = dirname(PHP_SELF);
define('ROOT_DIR', $php_self != '/' ? $php_self . '/' : '/');
define('MODULES_DIR', ACP3_ROOT . 'modules/');
define('INCLUDES_DIR', ACP3_ROOT . 'includes/');

// register_globals OFF Emulation
require INCLUDES_DIR . 'globals.php';

// DB-Config des ACP3 laden
require INCLUDES_DIR . 'config.php';
if (defined('INSTALLED') === false)
	exit('The ACP3 is not installed correctly. Please navigate to the <a href="' . ROOT_DIR . 'installation/">installation wizard</a> and follow the instructions.');

// Class Autoloader
require INCLUDES_DIR . 'autoload.php';

// Klassen initialisieren
$db = new ACP3_DB();
$handle = $db->connect(CONFIG_DB_HOST, CONFIG_DB_NAME, CONFIG_DB_USER, CONFIG_DB_PASSWORD, CONFIG_DB_PRE);
if ($handle !== true)
	exit($handle);

// Sytemeinstellungen laden
ACP3_Config::getSystemSettings();

// Standardzeitzone festlegen
date_default_timezone_set(CONFIG_DATE_TIME_ZONE);

// Smarty einbinden
require INCLUDES_DIR . 'smarty/Smarty.class.php';
$tpl = new Smarty();
$tpl->error_reporting = $reporting_level;
$tpl->compile_id = CONFIG_DESIGN;
$tpl->setCompileCheck(defined('DEBUG') === true && DEBUG === true);
$tpl->setTemplateDir(array(ACP3_ROOT . 'designs/' . CONFIG_DESIGN . '/', MODULES_DIR))
	->addPluginsDir(INCLUDES_DIR . 'smarty/custom/')
	->setCompileDir(ACP3_ROOT . 'uploads/cache/tpl_compiled/')
	->setCacheDir(ACP3_ROOT . 'uploads/cache/tpl_cached/');
if (is_writable($tpl->getCompileDir()) === false || is_writable($tpl->getCacheDir()) === false) {
	exit('The cache folder is not writable!');
}

define('DESIGN_PATH', ROOT_DIR . 'designs/' . CONFIG_DESIGN . '/');

// Einige Template Variablen setzen
$tpl->assign('PHP_SELF', PHP_SELF);
$tpl->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
$tpl->assign('ROOT_DIR', ROOT_DIR);
$tpl->assign('DESIGN_PATH', DESIGN_PATH);
$tpl->assign('LANG', CONFIG_LANG);

$uri = new ACP3_URI();

// Falls der Wartungsmodus aktiv ist, Wartungsnachricht ausgeben
if (defined('IN_ADM') === false && (bool) CONFIG_MAINTENANCE_MODE === true) {
	$tpl->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
	$tpl->assign('CONTENT', CONFIG_MAINTENANCE_MESSAGE);
	$tpl->display('maintenance.tpl');
	exit;
}

// Klassen initialisieren
$session = new ACP3_Session();
$auth = new ACP3_Auth();
$lang = new ACP3_Lang();
$date = new ACP3_Date();
$breadcrumb = new ACP3_Breadcrumb();

ACP3_ACL::initialize($auth->getUserId());

// Aktuelle Datensatzposition bestimmen
define('POS', (int) (ACP3_Validate::isNumber($uri->page) && $uri->page >= 1 ? ($uri->page - 1) * $auth->entries : 0));

require_once INCLUDES_DIR . 'functions.php';