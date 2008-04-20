<?php
error_reporting(E_ALL^E_NOTICE);

// Standardzeitzone festlegen
date_default_timezone_set('Europe/Berlin');

define('IN_INSTALL', true);
define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
define('ROOT_DIR', substr(PHP_SELF, 0, strrpos(PHP_SELF, '/') + 1));
define('CONFIG_VERSION', '4.0b10');

include ACP3_ROOT . 'includes/globals.php';

function __autoload($className)
{
	require_once ACP3_ROOT . 'includes/classes/' . $className . '.php';
}

include ACP3_ROOT . 'installation/includes/functions.php';

$modules = new modules;

if (empty($modules->query)) {
	$modules->mod = 'overview';
	$modules->page = 'welcome';
}
$lang = !empty($_POST['lang']) ? $_POST['lang'] : $modules->lang;
define('LANG', !empty($lang) && is_file(ACP3_ROOT . 'languages/' . $lang . '/info.php') ? $lang : 'de');

// Smarty einbinden
define('SMARTY_DIR', ACP3_ROOT . 'includes/smarty/');
include SMARTY_DIR . 'Smarty.class.php';
$tpl = new smarty;
$tpl->template_dir = ACP3_ROOT . 'installation/design/';
$tpl->compile_dir = ACP3_ROOT . 'cache/installation/';
if (!file_exists($tpl->compile_dir)) {
	mkdir($tpl->compile_dir);
}

$tpl->assign('PHP_SELF', PHP_SELF);
$tpl->assign('ROOT_DIR', ROOT_DIR);
$tpl->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES));
$tpl->assign('LANG', LANG);

$pages = array(
	'overview' => array(
		'title' => lang('installation', 'overview'),
		'mod' => 'overview',
		'selected' => '',
		'pages' => array(
			array(
				'page' => 'welcome',
				'selected' => '',
			),
			array(
				'page' => 'licence',
				'selected' => '',
			),
		),
	),
	'install' => array(
		'title' => lang('installation', 'installation'),
		'mod' => 'install',
		'selected' => '',
		'pages' => array(
			array(
				'page' => 'requirements',
				'no_href' => true,
				'selected' => '',
			),
			array(
				'page' => 'configuration',
				'no_href' => true,
				'selected' => '',
			),
		),
	),
);
$tpl->assign('currentModule', $modules->mod);
?>