<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check())
	redirect('errors/403');
//Server Infos
$server_info[0]['col_left'] = lang('system', 'architecture');
$server_info[0]['col_right'] = @php_uname('m');
$server_info[1]['col_left'] = lang('system', 'operating_system');
$server_info[1]['col_right'] = @php_uname('s') . ' ' . @php_uname('r');
$server_info[2]['col_left'] = lang('system', 'server_software');
$server_info[2]['col_right'] = $_SERVER['SERVER_SOFTWARE'];
$server_info[3]['col_left'] = lang('system', 'php_version');
$server_info[3]['col_right'] = phpversion();
$server_info[4]['col_left'] = lang('system', 'mysql_version');
$server_info[4]['col_right'] = CONFIG_DB_TYPE == 'mysqli' ? mysqli_get_server_info($db->con) : mysql_get_server_info($db->con);
$server_info[5]['col_left'] = lang('system', 'zend_engine');
$server_info[5]['col_right'] = zend_version();

$tpl->assign('server_info', $server_info);

//PHP Erweiterungen
$ext = get_loaded_extensions();
$ext_count = count($ext);
$php_ext = '';
for ($i = 0; $i < $ext_count; $i++) {
	$php_ext.= $ext[$i] . ', ';
}
$tpl->assign('php_ext', substr($php_ext, 0, -2));

$errors = (bool)ini_get('display_errors');
$php_settings[0]['col_left'] = lang('system', 'error_messages');
$php_settings[0]['col_right'] = $errors ? lang('system', 'on') : lang('system', 'off');
$php_settings[0]['col_color'] = $errors ? 'red' : 'green';

$register_globals = (bool)ini_get('register_globals');
$php_settings[1]['col_left'] = lang('system', 'register_globals');
$php_settings[1]['col_right'] = $register_globals ? lang('system', 'on') : lang('system', 'off');
$php_settings[1]['col_color'] = $register_globals ? 'red' : 'green';

$safe_mode = (bool)ini_get('safe_mode');
$php_settings[2]['col_left'] = lang('system', 'safe_mode');
$php_settings[2]['col_right'] = $safe_mode ? lang('system', 'on') : lang('system', 'off');
$php_settings[2]['col_color'] = $safe_mode ? 'red' : 'green';

$php_settings[3]['col_left'] = lang('system', 'max_upload_size');
$php_settings[3]['col_right'] = ini_get('post_max_size');
$php_settings[3]['col_color'] = $php_settings[3]['col_right'] > 0 ? 'green' : 'red';

if (!$errors && !$register_globals && !$safe_mode && $php_settings[3]['col_right'] > 0) {
	$tpl->assign('result_text', lang('system', 'optimal_server_configuration'));
} else {
	$tpl->assign('result_text', lang('system', 'not_optimal_server_configuration'));
}

$tpl->assign('php_settings', $php_settings);

$content = $tpl->fetch('system/software.html');
?>