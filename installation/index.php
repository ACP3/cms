<?php
ob_start();

error_reporting(E_ALL|E_STRICT);

// Standardzeitzone festlegen
date_default_timezone_set('Europe/Berlin');

// Evtl. gesetzten Content-Type des Servers überschreiben
header('Content-type: text/html; charset=UTF-8');

define('ACP3_ROOT', '../');
define('IN_INSTALL', true);

include ACP3_ROOT . 'includes/globals.php';

include ACP3_ROOT . 'installation/functions.php';

// Smarty einbinden
define('SMARTY_DIR', ACP3_ROOT . 'includes/smarty/');
include SMARTY_DIR . 'Smarty.class.php';
$tpl = new smarty;
$tpl->template_dir = ACP3_ROOT . 'installation/design/';
$tpl->compile_dir = ACP3_ROOT . 'cache/installation/';
if (!file_exists($tpl->compile_dir)) {
	mkdir($tpl->compile_dir);
}
//$tpl->compile_check = false;

define('PHP_SELF', $_SERVER['PHP_SELF']);
$tpl->assign('php_self', PHP_SELF);
$tpl->assign('request_uri', htmlspecialchars($_SERVER['REQUEST_URI']));

// Sprache
define('LANG', !empty($_REQUEST['lang']) && is_file(ACP3_ROOT . 'installation/languages/' . $_REQUEST['lang'] . '/info.php') ? $_REQUEST['lang'] : 'de');
$tpl->assign('lang', LANG);

// Modul und Seite
$mod = !empty($_GET['mod']) && is_dir(ACP3_ROOT . 'installation/modules/' . $_GET['mod'] . '/') ? $_GET['mod'] : 'overview';
if ($mod == 'overview') {
	$page = !empty($_GET['page']) && is_file(ACP3_ROOT . 'installation/modules/' . $mod . '/' . $_GET['page'] . '.php') ? $_GET['page'] : 'welcome';
} elseif ($mod == 'install') {
	$page = !empty($_GET['page']) && is_file(ACP3_ROOT . 'installation/modules/' . $mod . '/' . $_GET['page'] . '.php') ? $_GET['page'] : 'requirements';
}

// Navigationsleiste
$navbar['overview'] = array(
	'title' => lang('installation', 'overview'),
	'page' => 'overview',
	'selected' => '',
);
$navbar['install'] = array(
	'title' => lang('installation', 'installation'),
	'page' => 'install',
	'selected' => '',
);
// Selektion von Einträgen
if (array_key_exists($mod, $navbar)) {
	$navbar[$mod]['selected'] = ' class="selected"';
}

$tpl->assign('navbar', $navbar);

if ($mod == 'overview') {
	$nav_left[0]['page'] = 'welcome';
	$nav_left[0]['selected'] = '';
	$nav_left[1]['page'] = 'licence';
	$nav_left[1]['selected'] = '';
} elseif ($mod == 'install') {
	$nav_left[0]['page'] = 'requirements';
	$nav_left[0]['selected'] = '';
	$nav_left[0]['no_href'] = true;
	$nav_left[1]['page'] = 'configuration';
	$nav_left[1]['selected'] = '';
	$nav_left[1]['no_href'] = true;
}

// Titel und Selektion eines Eintrages in der seitlichen Navigation
$i = 0;
foreach ($nav_left as $row) {
	if ($row['page'] == $page) {
		$nav_left[$i]['selected'] = ' class="selected"';
		$tpl->assign('title', lang('installation', $row['page']));
		break;
	}
	$i++;
}
$tpl->assign('nav_left', $nav_left);

// Sprachpakete
define('CONFIG_VERSION', '4.0b10 SVN');
$languages = array();
$directories = scandir(ACP3_ROOT . 'languages');
$count_dir = count($directories);
for ($i = 0; $i < $count_dir; ++$i) {
	$lang_info = array();
	if ($directories[$i] != '.' && $directories[$i] != '..' && is_file(ACP3_ROOT . 'languages/' . $directories[$i] . '/info.php')) {
		include ACP3_ROOT . 'languages/' . $directories[$i] . '/info.php';
		$languages[$i]['dir'] = $directories[$i];
		$languages[$i]['selected'] = LANG == $directories[$i] ? ' selected="selected"' : '';
		$languages[$i]['name'] = $lang_info['name'];
	}
}
$tpl->assign('languages', $languages);

$content = '';
include ACP3_ROOT . 'installation/modules/' . $mod . '/' . $page . '.php';
$tpl->assign('content', $content);

$tpl->display('layout.html');

ob_end_flush();
?>