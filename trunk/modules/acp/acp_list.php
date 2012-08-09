<?php
/**
 * Administration Control Panel
 *
 * @author Tino Goratsch
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit();

// Module einholen
$mod_list = ACP3_Modules::getAllModules();
$mods = array();

foreach ($mod_list as $name => $info) {
	$dir = $info['dir'];
	if (ACP3_Modules::check($dir, 'acp_list') === true && $dir !== 'acp' && $dir !== 'system') {
		$mods[$name]['name'] = $name;
		$mods[$name]['dir'] = $dir;
	}
}
$tpl->assign('modules', $mods);

ACP3_View::setContent(ACP3_View::fetchTemplate('acp/acp_list.tpl'));
