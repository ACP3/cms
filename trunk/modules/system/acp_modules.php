<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP'))
	exit;

$breadcrumb->assign(lang('system', 'system'), uri('acp/system'));
$breadcrumb->assign(lang('system', 'extensions'), uri('acp/system/extensions'));
$breadcrumb->assign(lang('system', 'mod_list'));

$mod_list = $modules->modulesList();

foreach ($mod_list as $name => $info) {
	if ($info['protected']) {
		$mod_list[$name]['action'] = '<img src="' . ROOT_DIR . 'images/crystal/16/forbidden.png" alt="" />';
	} elseif ($info['active']) {
		$mod_list[$name]['action'] = '<a href="' . uri('acp/system/entry/action_moddeactivation/dir_' . $info['dir']) . '" title="' . lang('system', 'disable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" /></a>';
	} else {
		$mod_list[$name]['action'] = '<a href="' . uri('acp/system/entry/action_modactivation/dir_' . $info['dir']) . '" title="' . lang('system', 'enable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
	}

}

$tpl->assign('LANG_modules_found', sprintf(lang('system', 'modules_found'), count($mod_list)));
$tpl->assign('modules', $mod_list);

$content = $tpl->fetch('system/acp_modules.html');
?>