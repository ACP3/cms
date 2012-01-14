<?php
if (defined('IN_INSTALL') === false)
	exit;

error_reporting(E_ALL);

define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
$php_self = dirname(PHP_SELF);
define('ROOT_DIR', $php_self != '/' ? $php_self . '/' : '/');
define('INCLUDES_DIR', ACP3_ROOT . 'includes/');
define('MODULES_DIR', ACP3_ROOT . 'modules/');
define('CONFIG_VERSION', '4.0 SVN');
define('CONFIG_SEO_MOD_REWRITE', 0);

include INCLUDES_DIR . 'globals.php';

set_include_path(get_include_path() . PATH_SEPARATOR . ACP3_ROOT . 'includes/classes/');
spl_autoload_extensions('.class.php');
spl_autoload_register();

include ACP3_ROOT . 'installation/includes/functions.php';

$uri = new uri('install', 'welcome');

if (!empty($_POST['lang'])) {
	setcookie('ACP3_INSTALLER_LANG', $_POST['lang'], time() + 3600, ROOT_DIR);
	$uri->redirect($uri->query);
}
if (!empty($_COOKIE['ACP3_INSTALLER_LANG']) &&
	!preg_match('=/=', $_COOKIE['ACP3_INSTALLER_LANG']) &&
	is_file(ACP3_ROOT . 'languages/' . $_COOKIE['ACP3_INSTALLER_LANG'] . '/info.xml'))
	define('LANG', $_COOKIE['ACP3_INSTALLER_LANG']);
else
	define('LANG', 'de');

$lang = new lang(LANG);

// Smarty einbinden
include INCLUDES_DIR . 'smarty/Smarty.class.php';
$tpl = new Smarty();
$tpl->compile_id = 'installation_' . LANG;
$tpl->setTemplateDir(ACP3_ROOT . 'installation/design/')
	->addPluginsDir(INCLUDES_DIR . 'smarty/custom/')
	->setCompileDir(ACP3_ROOT . 'uploads/cache/tpl_compiled/')
	->setCacheDir(ACP3_ROOT . 'uploads/cache/tpl_cached/');
if (!is_writable($tpl->getCompileDir()) || !is_writable($tpl->getCacheDir())) {
	exit('Bitte geben Sie dem "cache"-Ordner den CHMOD 777!');
}

$tpl->assign('PHP_SELF', PHP_SELF);
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