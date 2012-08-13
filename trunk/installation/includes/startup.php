<?php
if (defined('IN_INSTALL') === false)
	exit;

error_reporting(E_ALL);

define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
$php_self = dirname(PHP_SELF);
define('INSTALLER_DIR', $php_self != '/' ? $php_self . '/' : '/');
define('ROOT_DIR', substr(INSTALLER_DIR, 0, -13));
define('INCLUDES_DIR', ACP3_ROOT . 'includes/');
define('MODULES_DIR', ACP3_ROOT . 'modules/');
define('CONFIG_VERSION', '4.0 SVN');
define('CONFIG_SEO_ALIASES', false);
define('CONFIG_SEO_MOD_REWRITE', false);

include INCLUDES_DIR . 'globals.php';
require INCLUDES_DIR . 'autoload.php';
require ACP3_ROOT . 'installation/includes/functions.php';

$uri = new ACP3_URI('install', 'welcome');

if (!empty($_POST['lang'])) {
	setcookie('ACP3_INSTALLER_LANG', $_POST['lang'], time() + 3600, '/');
	$uri->redirect($uri->mod . '/' . $uri->file);
}
if (!empty($_COOKIE['ACP3_INSTALLER_LANG']) &&
	!preg_match('=/=', $_COOKIE['ACP3_INSTALLER_LANG']) &&
	is_file(ACP3_ROOT . 'languages/' . $_COOKIE['ACP3_INSTALLER_LANG'] . '/info.xml') === true)
	define('LANG', $_COOKIE['ACP3_INSTALLER_LANG']);
else
	define('LANG', 'en');

$lang = new ACP3_Lang(LANG);

// Smarty einbinden
include INCLUDES_DIR . 'smarty/Smarty.class.php';
$tpl = new Smarty();
$tpl->compile_id = 'installation_' . LANG;
$tpl->setTemplateDir(ACP3_ROOT . 'installation/design/')
	->addPluginsDir(INCLUDES_DIR . 'smarty/custom/')
	->setCompileDir(ACP3_ROOT . 'uploads/cache/tpl_compiled/')
	->setCacheDir(ACP3_ROOT . 'uploads/cache/tpl_cached/');
if (is_writable($tpl->getCompileDir()) === false || is_writable($tpl->getCacheDir()) === false) {
	exit('Bitte geben Sie dem "cache"-Ordner den CHMOD 777!');
}

$tpl->assign('PHP_SELF', PHP_SELF);
$tpl->assign('INSTALLER_DIR', INSTALLER_DIR);
$tpl->assign('ROOT_DIR', ROOT_DIR);
$tpl->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES));
$tpl->assign('LANG', LANG);

$pages = array(
	array(
		'title' => $lang->t('installation', 'welcome'),
		'file' => 'welcome',
		'selected' => '',
	),
	array(
		'title' => $lang->t('installation', 'licence'),
		'file' => 'licence',
		'selected' => '',
	),
	array(
		'title' => $lang->t('installation', 'requirements'),
		'file' => 'requirements',
		'selected' => '',
	),
	array(
		'title' => $lang->t('installation', 'configuration'),
		'file' => 'configuration',
		'selected' => '',
	),
);