<?php
ob_start();

// Evtl. gesetzten Content-Type des Servers überschreiben
header('Content-type: text/html; charset=UTF-8');

define('ACP3_ROOT', '../');
require ACP3_ROOT . 'installation/includes/startup.php';

// Überprüfen, ob die angeforderte Seite überhaupt existiert
$i = 0;
$is_page = false;
if (array_key_exists($modules->mod, $pages)) {
	foreach ($pages[$modules->mod]['pages'] as $row) {
		if ($row['page'] == $modules->page) {
			$pages[$modules->mod]['pages'][$i]['selected'] = ' class="selected"';
			$tpl->assign('title', lang('installation', $row['page']));
			$is_page = true;
			break;
		}
		$i++;
	}
}

// Selektion eines Menüpunktes in der Navigation
$pages[$modules->mod]['selected'] = ' class="selected"';

// Sprachpakete
$languages = array();
$directories = scandir(ACP3_ROOT . 'languages');
$count_dir = count($directories);
for ($i = 0; $i < $count_dir; ++$i) {
	$lang_info = array();
	if (file_exists(ACP3_ROOT . 'languages/' . $directories[$i] . '/info.php')) {
		include ACP3_ROOT . 'languages/' . $directories[$i] . '/info.php';
		$languages[$i]['dir'] = $directories[$i];
		$languages[$i]['selected'] = LANG == $directories[$i] ? ' selected="selected"' : '';
		$languages[$i]['name'] = $lang_info['name'];
	}
}
$tpl->assign('languages', $languages);

if ($is_page) {
	$content = '';
	include ACP3_ROOT . 'installation/modules/' . $modules->mod . '/' . $modules->page . '.php';
	$tpl->assign('content', $content);
} else {
	$tpl->assign('title', lang('errors', '404'));
	$tpl->assign('currentModule', 'overview');
	$tpl->assign('content', $tpl->fetch('404.html'));
}

$tpl->display('layout.html');

ob_end_flush();
?>