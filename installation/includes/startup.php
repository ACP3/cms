<?php
error_reporting(0);

define('IN_INSTALL', true);
define('PHP_SELF', htmlentities($_SERVER['SCRIPT_NAME']));
$php_self = dirname(PHP_SELF);
define('ROOT_DIR', $php_self != '/' ? $php_self . '/' : '/');
define('CONFIG_VERSION', '4.0RC1 SVN');

include ACP3_ROOT . 'includes/globals.php';

function __autoload($className)
{
	require_once ACP3_ROOT . 'includes/classes/' . $className . '.php';
}

include ACP3_ROOT . 'installation/includes/functions.php';

$uri = new uri;

if (empty($uri->query)) {
	$uri->mod = 'overview';
	$uri->page = 'welcome';
}
$l = !empty($_POST['lang']) ? $_POST['lang'] : $uri->lang;
define('LANG', !empty($l) && is_file(ACP3_ROOT . 'languages/' . $l . '/info.xml') ? $l : 'de');
$lang = new lang;

// Smarty einbinden
define('SMARTY_DIR', ACP3_ROOT . 'includes/smarty/');
include SMARTY_DIR . 'Smarty.class.php';
$tpl = new smarty;
$tpl->template_dir = ACP3_ROOT . 'installation/design/';
$tpl->compile_dir = ACP3_ROOT . 'cache/installation/';
if (!is_dir($tpl->compile_dir)) {
	if (!is_writable(ACP3_ROOT . 'cache/')) {
		exit('Bitte geben Sie dem "cache"-Ordner den CHMOD 777!');
	} else {
		mkdir($tpl->compile_dir, 0777);
	}
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
$tpl->assign('currentModule', $uri->mod);
?>