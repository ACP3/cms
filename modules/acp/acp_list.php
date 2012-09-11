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
	if (ACP3_Modules::check($dir, 'acp_list') === true && $dir !== 'acp') {
		$mods[$name]['name'] = $name;
		$mods[$name]['dir'] = $dir;
	}
}
ACP3_CMS::$view->assign('modules', $mods);

ACP3_CMS::setContent(ACP3_CMS::$view->fetchTemplate('acp/acp_list.tpl'));
