<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit;

// PHP Erweiterungen
$ext = get_loaded_extensions();
$ext_count = count($ext);
$php_ext = '';
for ($i = 0; $i < $ext_count; ++$i) {
	$php_ext.= $ext[$i] . ', ';
}
$tpl->assign('php_ext', substr($php_ext, 0, -2));

$errors = (bool)ini_get('display_errors');
$php_settings[0]['col_left'] = $lang->t('system', 'error_messages');
$php_settings[0]['col_right'] = $errors ? $lang->t('system', 'on') : $lang->t('system', 'off');
$php_settings[0]['col_color'] = $errors ? 'red' : 'green';

$register_globals = (bool)ini_get('register_globals');
$php_settings[1]['col_left'] = $lang->t('system', 'register_globals');
$php_settings[1]['col_right'] = $register_globals ? $lang->t('system', 'on') : $lang->t('system', 'off');
$php_settings[1]['col_color'] = $register_globals ? 'red' : 'green';

$safe_mode = (bool)ini_get('safe_mode');
$php_settings[2]['col_left'] = $lang->t('system', 'safe_mode');
$php_settings[2]['col_right'] = $safe_mode ? $lang->t('system', 'on') : $lang->t('system', 'off');
$php_settings[2]['col_color'] = $safe_mode ? 'red' : 'green';

$php_settings[3]['col_left'] = $lang->t('system', 'max_upload_size');
$php_settings[3]['col_right'] = ini_get('post_max_size');
$php_settings[3]['col_color'] = $php_settings[3]['col_right'] > 0 ? 'green' : 'red';

if (!$errors && !$register_globals && !$safe_mode && $php_settings[3]['col_right'] > 0) {
	$tpl->assign('result_text', $lang->t('system', 'optimal_server_configuration'));
} else {
	$tpl->assign('result_text', $lang->t('system', 'not_optimal_server_configuration'));
}

$tpl->assign('php_settings', $php_settings);

$content = modules::fetchTemplate('system/server_config.html');
