<?php
if (!defined('IN_INSTALL'))
	exit;

//Allgemeine Voraussetzungen
$requirements[0]['name'] = lang('php_version');
$requirements[0]['color'] = version_compare(phpversion(), '5.0.5', '>=') ? '090' : 'f00';
$requirements[0]['found'] = phpversion();
$requirements[0]['required'] = '5.0.5+';
$requirements[1]['name'] = lang('mysql_version');
$requirements[1]['color'] = version_compare(mysql_get_client_info(), '4.0', '>=') ? '090' : 'f00';
$requirements[1]['found'] = mysql_get_client_info();
$requirements[1]['required'] = '4.0+';
$requirements[2]['name'] = lang('safe_mode');
$requirements[2]['color'] = (bool)ini_get('safe_mode') ? 'f00' : '090';
$requirements[2]['found'] = (bool)ini_get('safe_mode') ? lang('on') : lang('off');
$requirements[2]['required'] = lang('off');

$tpl->assign('requirements', $requirements);

$defaults = array(
	'../includes/config.php' => 'file',
	'../modules/contact/config.php' => 'file',
	'../modules/home/draft.txt' => 'file',
	'../modules/newsletter/config.php' => 'file',
	'../cache/' => 'dir',
	'../files/dl/' => 'dir',
	'../files/emoticons/' => 'dir',
	'../files/gallery/' => 'dir',
);
$files_dirs = array();
$check_again = false;

$i = 0;
foreach ($defaults as $key => $value) {
	$files_dirs[$i]['path'] = $key;
	$files_dirs[$i]['color_1'] = file_exists($key) ? '090' : 'f00';
	// Überprüfen, ob Datei/Ordner überhaupt vorhanden ist
	if ($value == 'file')
		$files_dirs[$i]['exists'] = file_exists($key) ? lang('file_found') : lang('file_not_found');
	else
		$files_dirs[$i]['exists'] = file_exists($key) ? lang('folder_found') : lang('folder_not_found');

	$files_dirs[$i]['color_2'] = is_writable($key) ? '090' : 'f00';
	$files_dirs[$i]['writeable'] = is_writable($key) ? lang('writeable') : lang('not_writeable');
	if ($files_dirs[$i]['color_1'] == 'f00' || $files_dirs[$i]['color_2'] == 'f00') {
		$check_again = true;
	}
	$i++;
}

$tpl->assign('files_dirs', $files_dirs);

//PHP Einstellungen
$php_settings[0]['setting'] = lang('error_messages');
$php_settings[0]['color'] = (bool)ini_get('display_errors') ? 'f00' : '090';
$php_settings[0]['value'] = (bool)ini_get('display_errors') ? lang('on') : lang('off');
$php_settings[1]['setting'] = lang('register_globals');
$php_settings[1]['color'] = (bool)ini_get('register_globals') ? 'f00' : '090';
$php_settings[1]['value'] = (bool)ini_get('register_globals') ? lang('on') : lang('off');
$php_settings[2]['setting'] = lang('maximum_uploadsize');
$php_settings[2]['color'] = ini_get('post_max_size') > 0 ? '090' : 'f00';
$php_settings[2]['value'] = ini_get('post_max_size');

$tpl->assign('php_settings', $php_settings);

if (version_compare(phpversion(), '5.0.5', '<') || version_compare(mysql_get_client_info(), '4.0', '<') || (bool)ini_get('safe_mode')) {
	$tpl->assign('stop_install', true);
} elseif ($check_again) {
	$tpl->assign('check_again', true);
}

$content = $tpl->fetch('requirements.html');
?>