<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Installer
 */

if (defined('IN_ACP3') === false)
	exit;

// Evtl. gesetzten Content-Type des Servers überschreiben
header('Content-type: text/html; charset=UTF-8');

// Alle Fehler ausgeben
error_reporting(E_ALL);

define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
$php_self = dirname(PHP_SELF);
define('INSTALLER_DIR', $php_self !== '/' ? $php_self . '/' : '/');
define('ROOT_DIR', substr(INSTALLER_DIR, 0, -13));
define('INCLUDES_DIR', ACP3_ROOT . 'includes/');
define('INSTALLER_INCLUDES_DIR', ACP3_ROOT . 'installation/includes/');
define('LIBRARIES_DIR', ACP3_ROOT . 'libraries/');
define('MODULES_DIR', ACP3_ROOT . 'modules/');

include INCLUDES_DIR . 'globals.php';
require INCLUDES_DIR . 'autoload.php';
require INSTALLER_INCLUDES_DIR . 'functions.php';

// Smarty einbinden
include LIBRARIES_DIR . 'smarty/Smarty.class.php';
$tpl = new Smarty();
$tpl->compile_id = 'installation';
$tpl->setTemplateDir(ACP3_ROOT . 'installation/design/')
	->addPluginsDir(INSTALLER_INCLUDES_DIR . 'smarty_functions/')
	->setCompileDir(ACP3_ROOT . 'uploads/cache/tpl_compiled/')
	->setCacheDir(ACP3_ROOT . 'uploads/cache/tpl_cached/');
if (is_writable($tpl->getCompileDir()) === false || is_writable($tpl->getCacheDir()) === false) {
	exit('Bitte geben Sie dem "cache"-Ordner den CHMOD 777!');
}

if (defined('IN_UPDATER') === false) {
	define('CONFIG_VERSION', '4.0 SVN');
	define('CONFIG_SEO_ALIASES', false);
	define('CONFIG_SEO_MOD_REWRITE', false);

	$pages = array(
		array(
			'file' => 'welcome',
			'selected' => '',
		),
		array(
			'file' => 'licence',
			'selected' => '',
		),
		array(
			'file' => 'requirements',
			'selected' => '',
		),
		array(
			'file' => 'configuration',
			'selected' => '',
		),
	);
	$uri = new ACP3_URI('install', 'welcome');
} else {
	require INCLUDES_DIR . 'bootstrap.php';

	ACP3_CMS::startupChecks();
	ACP3_CMS::initializeDatabase();

	// Alte Versionen auf den Legacy Updater umleiten
	if (defined('CONFIG_LANG') === true) {
		$html = '<!DOCTYPE html>' . "\n";
		$html.= '<html>' . "\n";
		$html.= '<head>' . "\n";
		$html.= '<title>Attention!</title>' . "\n";
		$html.= '</head>' . "\n";
		$html.= '<body>' . "\n";
		$html.= '<h1>Attention!</h1>' . "\n";
		$html.= '<p>A very old version of the ACP3 has been detected.</p>' . "\n";
		$html.= '<p>Please run the <a href="' . INSTALLER_DIR . 'update_old.php" onclick="window.open(this.href); return false">legacy database updater</a> first.<br />' . "\n";
		$html.= 'After that please reload this page to use the new database upgrade wizard.</p>' . "\n";
		$html.= '</body>' . "\n";
		$html.= '</html>';
		exit($html);
	}

	ACP3_Config::getSystemSettings();

	$pages = array(
		array(
			'file' => 'db_update',
			'selected' => '',
		),
	);
	$uri = new ACP3_URI('install', 'db_update');

	ACP3_Cache::purge();
}

if (!empty($_POST['lang'])) {
	setcookie('ACP3_INSTALLER_LANG', $_POST['lang'], time() + 3600, '/');
	$uri->redirect($uri->mod . '/' . $uri->file);
}

if (!empty($_COOKIE['ACP3_INSTALLER_LANG']) && !preg_match('=/=', $_COOKIE['ACP3_INSTALLER_LANG']) &&
	is_file(ACP3_ROOT . 'installation/languages/' . $_COOKIE['ACP3_INSTALLER_LANG'] . '.xml') === true) {
	define('LANG', $_COOKIE['ACP3_INSTALLER_LANG']);
} else {
	define('LANG', ACP3_Lang::parseAcceptLanguage());
}

$tpl->assign('LANGUAGES', languagesDropdown(LANG));

$tpl->assign('PHP_SELF', PHP_SELF);
$tpl->assign('INSTALLER_DIR', INSTALLER_DIR);
$tpl->assign('ROOT_DIR', ROOT_DIR);
$tpl->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES));
$tpl->assign('LANG', LANG);

require INSTALLER_INCLUDES_DIR . 'classes/InstallerLang.class.php';
$lang = new ACP3_InstallerLang(LANG);

// Überprüfen, ob die angeforderte Seite überhaupt existiert
$i = 0;
$is_file = false;
foreach ($pages as $row) {
	if ($row['file'] === $uri->file) {
		$pages[$i]['selected'] = ' class="active"';
		$tpl->assign('TITLE', $lang->t($row['file']));
		$is_file = true;
		break;
	}
	++$i;
}
$tpl->assign('PAGES', $pages);

if ($is_file === true) {
	$content = '';
	include ACP3_ROOT . 'installation/pages/' . $uri->file . '.php';
	$tpl->assign('CONTENT', $content);
} else {
	$tpl->assign('TITLE', $lang->t('errors', '404'));
	$tpl->assign('CONTENT', $tpl->fetch('404.tpl'));
}