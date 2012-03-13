<?php
if (defined('IN_INSTALL') === false)
	exit;

define('REQUIRED_PHP_VERSION', '5.3.0');
define('COLOR_ERROR', 'f00');
define('COLOR_SUCCESS', '090');

// Allgemeine Voraussetzungen
$requirements = array();
$requirements[0]['name'] = $lang->t('installation', 'php_version');
$requirements[0]['color'] = version_compare(phpversion(), REQUIRED_PHP_VERSION, '>=') ? COLOR_SUCCESS : COLOR_ERROR;
$requirements[0]['found'] = phpversion();
$requirements[0]['required'] = REQUIRED_PHP_VERSION;
$requirements[1]['name'] = $lang->t('installation', 'pdo_extension');
$requirements[1]['color'] = extension_loaded('pdo') && extension_loaded('pdo_mysql') ? COLOR_SUCCESS : COLOR_ERROR;
$requirements[1]['found'] = $lang->t('installation', $requirements[1]['color'] == COLOR_ERROR ? 'on' : 'off');
$requirements[1]['required'] = $lang->t('installation', 'on');
$requirements[2]['name'] = $lang->t('installation', 'gd_library');
$requirements[2]['color'] = extension_loaded('gd') ? COLOR_SUCCESS : COLOR_ERROR;
$requirements[2]['found'] = $lang->t('installation', $requirements[2]['color'] == COLOR_ERROR ? 'on' : 'off');
$requirements[2]['required'] = $lang->t('installation', 'on');

$tpl->assign('requirements', $requirements);

$defaults = array('includes/config.php');

// Uploadordner
$uploads = scandir(ACP3_ROOT . 'uploads/');
foreach ($uploads as $row) {
	$path = 'uploads/' . $row . '/';
	if ($row !== '.' && $row !== '..' && $row !== '.svn' &&  is_dir(ACP3_ROOT . $path) === true) {
		$defaults[] = $path;
	}
}
$files_dirs = array();
$check_again = false;

$i = 0;
foreach ($defaults as $row) {
	$files_dirs[$i]['path'] = $row;
	// Überprüfen, ob es eine Datei oder ein Ordner ist
	if (is_file(ACP3_ROOT . $row) === true) {
		$files_dirs[$i]['color_1'] = COLOR_SUCCESS;
		$files_dirs[$i]['exists'] = $lang->t('installation', 'file_found');
	} elseif (is_dir(ACP3_ROOT . $row) === true) {
		$files_dirs[$i]['color_1'] = COLOR_SUCCESS;
		$files_dirs[$i]['exists'] = $lang->t('installation', 'folder_found');
	} else {
		$files_dirs[$i]['color_1'] = COLOR_ERROR;
		$files_dirs[$i]['exists'] = $lang->t('installation', 'file_folder_not_found');
	}
	$files_dirs[$i]['color_2'] = is_writable(ACP3_ROOT . $row) === true ? COLOR_SUCCESS : COLOR_ERROR;
	$files_dirs[$i]['writeable'] = $files_dirs[$i]['color_2'] === COLOR_SUCCESS ? $lang->t('installation', 'writeable') : $lang->t('installation', 'not_writeable');
	if ($files_dirs[$i]['color_1'] == COLOR_ERROR || $files_dirs[$i]['color_2'] == COLOR_ERROR) {
		$check_again = true;
	}
	$i++;
}
$tpl->assign('files_dirs', $files_dirs);

// PHP Einstellungen
$php_settings = array();
$php_settings[0]['setting'] = $lang->t('installation', 'error_messages');
$php_settings[0]['color'] = (bool)ini_get('display_errors') ? COLOR_ERROR : COLOR_SUCCESS;
$php_settings[0]['value'] = $lang->t('installation', (bool)ini_get('display_errors') ? 'on' : 'off');
$php_settings[1]['setting'] = $lang->t('installation', 'register_globals');
$php_settings[1]['color'] = (bool)ini_get('register_globals') ? COLOR_ERROR : COLOR_SUCCESS;
$php_settings[1]['value'] = $lang->t('installation', (bool)ini_get('register_globals') ? 'on' : 'off');
$php_settings[2]['setting'] = $lang->t('installation', 'maximum_uploadsize');
$php_settings[2]['color'] = ini_get('post_max_size') > 0 ? COLOR_SUCCESS : COLOR_ERROR;
$php_settings[2]['value'] = ini_get('post_max_size');
$php_settings[3]['setting'] = $lang->t('installation', 'safe_mode');
$php_settings[3]['color'] =  (bool)ini_get('safe_mode') ? COLOR_ERROR : COLOR_SUCCESS;
$php_settings[3]['value'] = $lang->t('installation', (bool)ini_get('safe_mode') ? 'on' : 'off');
$php_settings[4]['setting'] = $lang->t('installation', 'magic_quotes');
$php_settings[4]['color'] =  (bool)ini_get('magic_quotes_gpc') ? COLOR_ERROR : COLOR_SUCCESS;
$php_settings[4]['value'] = $lang->t('installation', (bool)ini_get('magic_quotes_gpc') ? 'on' : 'off');
$tpl->assign('php_settings', $php_settings);

if (version_compare(phpversion(), REQUIRED_PHP_VERSION, '<') ||
	$requirements[1]['color'] == COLOR_ERROR ||
	$requirements[2]['color'] == COLOR_ERROR) {
	$tpl->assign('stop_install', true);
} elseif ($check_again) {
	$tpl->assign('check_again', true);
}

$content = $tpl->fetch('requirements.tpl');