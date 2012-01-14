<?php
ob_start();

define('IN_ACP3', true);
define('IN_INSTALL', true);

// Evtl. gesetzten Content-Type des Servers überschreiben
header('Content-type: text/html; charset=UTF-8');

define('ACP3_ROOT', dirname(__FILE__) . '/../');
require ACP3_ROOT . 'installation/includes/startup.php';

// Überprüfen, ob die angeforderte Seite überhaupt existiert
$i = 0;
$is_file = false;
foreach ($pages as $row) {
	if ($row['file'] == $uri->file) {
		$pages[$i]['selected'] = ' class="selected"';
		$tpl->assign('title', $lang->t('installation', $row['file']));
		$is_file = true;
		break;
	}
	++$i;
}
$tpl->assign('pages', $pages);

// Dropdown-Menü für die Sprachen
$languages = array();
$directories = scandir(ACP3_ROOT . 'languages');
$count_dir = count($directories);
for ($i = 0; $i < $count_dir; ++$i) {
	$lang_info = xml::parseXmlFile(ACP3_ROOT . 'languages/' . $directories[$i] . '/info.xml', '/language');
	if (!empty($lang_info)) {
		$languages[$i]['dir'] = $directories[$i];
		$languages[$i]['selected'] = LANG == $directories[$i] ? ' selected="selected"' : '';
		$languages[$i]['name'] = $lang_info['name'];
	}
}
$tpl->assign('languages', $languages);

if ($is_file) {
	$content = '';
	include ACP3_ROOT . 'installation/modules/' . $uri->file . '.php';
	$tpl->assign('content', $content);
} else {
	$tpl->assign('title', $lang->t('errors', '404'));
	$tpl->assign('content', $tpl->fetch('404.tpl'));
}

$tpl->display('layout.tpl');

ob_end_flush();