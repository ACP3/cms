<?php
/**
 * Installer
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Installer
 */

if (defined('IN_INSTALL') === false)
	exit;

define('REQUIRED_PHP_VERSION', '5.3.0');
define('COLOR_ERROR', 'f00');
define('COLOR_SUCCESS', '090');
define('CLASS_ERROR', 'important');
define('CLASS_SUCCESS', 'success');
define('CLASS_WARNING', 'warning');

// Allgemeine Voraussetzungen
$requirements = array();
$requirements[0]['name'] = $lang->t('php_version');
$requirements[0]['color'] = version_compare(phpversion(), REQUIRED_PHP_VERSION, '>=') ? COLOR_SUCCESS : COLOR_ERROR;
$requirements[0]['found'] = phpversion();
$requirements[0]['required'] = REQUIRED_PHP_VERSION;
$requirements[1]['name'] = $lang->t('pdo_extension');
$requirements[1]['color'] = extension_loaded('pdo') && extension_loaded('pdo_mysql') ? COLOR_SUCCESS : COLOR_ERROR;
$requirements[1]['found'] = $lang->t($requirements[1]['color'] == COLOR_SUCCESS ? 'on' : 'off');
$requirements[1]['required'] = $lang->t('on');
$requirements[2]['name'] = $lang->t('gd_library');
$requirements[2]['color'] = extension_loaded('gd') ? COLOR_SUCCESS : COLOR_ERROR;
$requirements[2]['found'] = $lang->t($requirements[2]['color'] == COLOR_SUCCESS ? 'on' : 'off');
$requirements[2]['required'] = $lang->t('on');

$tpl->assign('requirements', $requirements);

$defaults = array('includes/config.php');

// Uploadordner
$uploads = scandir(UPLOADS_DIR);
foreach ($uploads as $row) {
	$path = 'uploads/' . $row . '/';
	if ($row !== '.' && $row !== '..' &&  is_dir(ACP3_ROOT . $path) === true) {
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
		$files_dirs[$i]['class_1'] = CLASS_SUCCESS;
		$files_dirs[$i]['exists'] = $lang->t('found');
	} elseif (is_dir(ACP3_ROOT . $row) === true) {
		$files_dirs[$i]['class_1'] = CLASS_SUCCESS;
		$files_dirs[$i]['exists'] = $lang->t('found');
	} else {
		$files_dirs[$i]['class_1'] = CLASS_ERROR;
		$files_dirs[$i]['exists'] = $lang->t('not_found');
	}
	$files_dirs[$i]['class_2'] = is_writable(ACP3_ROOT . $row) === true ? CLASS_SUCCESS : CLASS_ERROR;
	$files_dirs[$i]['writable'] = $files_dirs[$i]['class_2'] === CLASS_SUCCESS ? $lang->t('writable') : $lang->t('not_writable');
	if ($files_dirs[$i]['class_1'] == CLASS_ERROR || $files_dirs[$i]['class_2'] == CLASS_ERROR) {
		$check_again = true;
	}
	$i++;
}
$tpl->assign('files_dirs', $files_dirs);

// PHP Einstellungen
$php_settings = array();
$php_settings[0]['setting'] = $lang->t('error_messages');
$php_settings[0]['class'] = (bool)ini_get('display_errors') ? CLASS_WARNING : CLASS_SUCCESS;
$php_settings[0]['value'] = $lang->t((bool)ini_get('display_errors') ? 'on' : 'off');
$php_settings[1]['setting'] = $lang->t('register_globals');
$php_settings[1]['class'] = (bool)ini_get('register_globals') ? CLASS_WARNING : CLASS_SUCCESS;
$php_settings[1]['value'] = $lang->t((bool)ini_get('register_globals') ? 'on' : 'off');
$php_settings[2]['setting'] = $lang->t('maximum_uploadsize');
$php_settings[2]['class'] = ini_get('post_max_size') > 0 ? CLASS_SUCCESS : CLASS_WARNING;
$php_settings[2]['value'] = ini_get('post_max_size');
$php_settings[3]['setting'] = $lang->t('safe_mode');
$php_settings[3]['class'] =  (bool)ini_get('safe_mode') ? CLASS_WARNING : CLASS_SUCCESS;
$php_settings[3]['value'] = $lang->t((bool)ini_get('safe_mode') ? 'on' : 'off');
$php_settings[4]['setting'] = $lang->t('magic_quotes');
$php_settings[4]['class'] =  (bool)ini_get('magic_quotes_gpc') ? CLASS_WARNING : CLASS_SUCCESS;
$php_settings[4]['value'] = $lang->t((bool)ini_get('magic_quotes_gpc') ? 'on' : 'off');
$tpl->assign('php_settings', $php_settings);

if (version_compare(phpversion(), REQUIRED_PHP_VERSION, '<') ||
	$requirements[1]['color'] == COLOR_ERROR ||
	$requirements[2]['color'] == COLOR_ERROR) {
	$tpl->assign('stop_install', true);
} elseif ($check_again) {
	$tpl->assign('check_again', true);
}

$content = $tpl->fetch('requirements.tpl');