<?php
ob_start();

// Evtl. gesetzten Content-Type des Servers überschreiben
header('Content-type: text/html; charset=UTF-8');

define('ACP3_ROOT', '../');
require ACP3_ROOT . 'installation/includes/startup.php';

// Überprüfen, ob die angeforderte Seite überhaupt existiert
$i = 0;
$is_page = false;
foreach ($pages as $row) {
	if ($row['page'] == $uri->page) {
		$pages[$i]['selected'] = ' class="selected"';
		$tpl->assign('title', $lang->t('installation', $row['page']));
		$is_page = true;
		break;
	}
	$i++;
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

if ($is_page) {
	$content = '';
	include ACP3_ROOT . 'installation/modules/' . $uri->page . '.php';
	$tpl->assign('content', $content);
} else {
	$tpl->assign('title', $lang->t('errors', '404'));
	$tpl->assign('content', $tpl->fetch('404.html'));
}

$tpl->display('layout.html');

ob_end_flush();

if ($uri->mod == 'install' && $uri->page == 'configuration') {
	cache::purge('installation', 1);
	cache::purge();
}
?>