<?php
/**
 * Administration Control Panel
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (! defined('IN_ADM'))
	exit();

breadcrumb::assign(lang('common', 'acp'));

// Module einholen
$mod_list = $modules->modulesList();
$mods = array();

foreach ($mod_list as $name => $info) {
	$dir = $info['dir'];
	if ($modules->check($dir, 'adm_list') && $dir != 'acp' && $dir != 'system' && $dir != 'home') {
		$mods[$name]['name'] = $name;
		$mods[$name]['dir'] = $dir;
	}
}
$tpl->assign('modules', $mods);

//Server Infos
if ($modules->check('system', 'server_config')) {
	$server_info[0]['col_left'] = lang('system', 'architecture');
	$server_info[0]['col_right'] = @php_uname('m');
	$server_info[1]['col_left'] = lang('system', 'operating_system');
	$server_info[1]['col_right'] = @php_uname('s') . ' ' . @php_uname('r');
	$server_info[2]['col_left'] = lang('system', 'server_software');
	$server_info[2]['col_right'] = $_SERVER['SERVER_SOFTWARE'];
	$server_info[3]['col_left'] = lang('system', 'php_version');
	$server_info[3]['col_right'] = phpversion();
	$server_info[4]['col_left'] = lang('system', 'mysql_version');
	$server_info[4]['col_right'] = CONFIG_DB_TYPE == 'mysqli' ? mysqli_get_server_info($db->link) : mysql_get_server_info($db->link);

	$tpl->assign('server_info', $server_info);
}

$content = $tpl->fetch('acp/adm_list.html');
?>