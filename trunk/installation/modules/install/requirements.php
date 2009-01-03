<?php
if (!defined('IN_INSTALL'))
	exit;

// Allgemeine Voraussetzungen
$requirements[0]['name'] = lang('system', 'php_version');
$requirements[0]['color'] = version_compare(phpversion(), '5.1.0', '>=') ? '090' : 'f00';
$requirements[0]['found'] = phpversion();
$requirements[0]['required'] = '5.1.0';
$requirements[1]['name'] = lang('system', 'mysql_version');
$requirements[1]['color'] = version_compare(mysql_get_client_info(), '4.0', '>=') ? '090' : 'f00';
$requirements[1]['found'] = mysql_get_client_info();
$requirements[1]['required'] = '4.0';
$requirements[2]['name'] = lang('system', 'safe_mode');
$requirements[2]['color'] = (bool)ini_get('safe_mode') ? 'f00' : '090';
$requirements[2]['found'] = lang('system', (bool)ini_get('safe_mode') ? 'on' : 'off');
$requirements[2]['required'] = lang('system', 'off');

$tpl->assign('requirements', $requirements);

$defaults = array(
	'includes/config.php',
	'modules/access/module.xml',
	'modules/acp/module.xml',
	'modules/categories/module.xml',
	'modules/comments/module.xml',
	'modules/captcha/module.xml',
	'modules/contact/module.xml',
	'modules/emoticons/module.xml',
	'modules/errors/module.xml',
	'modules/feeds/module.xml',
	'modules/files/module.xml',
	'modules/gallery/module.xml',
	'modules/guestbook/module.xml',
	'modules/menu_items/module.xml',
	'modules/news/module.xml',
	'modules/newsletter/module.xml',
	'modules/polls/module.xml',
	'modules/search/module.xml',
	'modules/system/module.xml',
	'modules/users/module.xml',
	'cache/',
	'uploads/captcha/',
	'uploads/categories/',
	'uploads/emoticons/',
	'uploads/files/',
	'uploads/gallery/',
);
$files_dirs = array();
$check_again = false;

$i = 0;
foreach ($defaults as $row) {
	$files_dirs[$i]['path'] = $row;
	// Überprüfen, ob es eine Datei oder ein Ordner ist
	if (is_file(ACP3_ROOT . $row)) {
		$files_dirs[$i]['color_1'] = '090';
		$files_dirs[$i]['exists'] = lang('installation', 'file_found');
	} elseif (is_dir(ACP3_ROOT . $row)) {
		$files_dirs[$i]['color_1'] = '090';
		$files_dirs[$i]['exists'] = lang('installation', 'folder_found');
	} else {
		$files_dirs[$i]['color_1'] = 'f00';
		$files_dirs[$i]['exists'] = lang('installation', 'file_folder_not_found');
	}
	$files_dirs[$i]['color_2'] = is_writable(ACP3_ROOT . $row) ? '090' : 'f00';
	$files_dirs[$i]['writeable'] = $files_dirs[$i]['color_2'] == '090' ? lang('installation', 'writeable') : lang('installation', 'not_writeable');
	if ($files_dirs[$i]['color_1'] == 'f00' || $files_dirs[$i]['color_2'] == 'f00') {
		$check_again = true;
	}
	$i++;
}

$tpl->assign('files_dirs', $files_dirs);

// PHP Einstellungen
$php_settings[0]['setting'] = lang('installation', 'error_messages');
$php_settings[0]['color'] = (bool)ini_get('display_errors') ? 'f00' : '090';
$php_settings[0]['value'] = lang('system', (bool)ini_get('display_errors') ? 'on' : 'off');
$php_settings[1]['setting'] = lang('installation', 'register_globals');
$php_settings[1]['color'] = (bool)ini_get('register_globals') ? 'f00' : '090';
$php_settings[1]['value'] = lang('system', (bool)ini_get('register_globals') ? 'on' : 'off');
$php_settings[2]['setting'] = lang('installation', 'maximum_uploadsize');
$php_settings[2]['color'] = ini_get('post_max_size') > 0 ? '090' : 'f00';
$php_settings[2]['value'] = ini_get('post_max_size');

$tpl->assign('php_settings', $php_settings);

if (version_compare(phpversion(), '5.1.0', '<') || version_compare(mysql_get_client_info(), '4.0', '<') || (bool)ini_get('safe_mode')) {
	$tpl->assign('stop_install', true);
} elseif ($check_again) {
	$tpl->assign('check_again', true);
}

$content = $tpl->fetch('requirements.html');
?>