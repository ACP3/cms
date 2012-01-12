<?php
/**
 * Administration Control Panel
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (defined('IN_ADM') === false)
	exit();

breadcrumb::assign($lang->t('common', 'acp'));

// Module einholen
$mod_list = modules::modulesList();
$mods = array();

foreach ($mod_list as $name => $info) {
	$dir = $info['dir'];
	if (modules::check($dir, 'adm_list') == 1 && $dir != 'acp' && $dir != 'system') {
		$mods[$name]['name'] = $name;
		$mods[$name]['dir'] = $dir;
	}
}
$tpl->assign('modules', $mods);

$content = modules::fetchTemplate('acp/adm_list.tpl');
