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

$breadcrumb->assign(lang('common', 'acp'), uri('acp'));
$breadcrumb->assign(lang('system', 'system'), uri('acp/system'));
$breadcrumb->assign(lang('system', 'extensions'), uri('acp/system/extensions'));
$breadcrumb->assign(lang('system', 'modules'));

if ($modules->action == 'activate') {
	if ($modules->dir && is_file(ACP3_ROOT . 'modules/' . $modules->dir . '/module.xml')) {
		$info = $modules->parseInfo($modules->dir);
		if ($info['protected']) {
			$text = lang('system', 'mod_deactivate_forbidden');
		} else {
			$path = ACP3_ROOT . 'modules/' . $modules->dir . '/module.xml';

			$xml = simplexml_load_file($path);
			$xml->info->active = '1';
			$bool = $xml->asXML($path);

			$text = $bool ? lang('system', 'mod_activate_success') : lang('system', 'mod_activate_error');
		}
	} else {
		$text = lang('system', 'mod_activate_error');
	}
	$content = comboBox($text, uri('acp/system/modules'));
} elseif ($modules->action == 'deactivate') {
	if ($modules->dir && is_file(ACP3_ROOT . 'modules/' . $modules->dir . '/module.xml')) {
		$info = $modules->parseInfo($modules->dir);
		if ($info['protected']) {
			$text = lang('system', 'mod_deactivate_forbidden');
		} else {
			$path = ACP3_ROOT . 'modules/' . $modules->dir . '/module.xml';

			$xml = simplexml_load_file($path);
			$xml->info->active = '0';
			$bool = $xml->asXML($path);

			$text = $bool ? lang('system', 'mod_deactivate_success') : lang('system', 'mod_deactivate_error');
		}
	} else {
		$text = lang('system', 'mod_deactivate_error');
	}
	$content = comboBox($text, uri('acp/system/modules'));
} else {
	$mod_list = $modules->modulesList();

	foreach ($mod_list as $name => $info) {
		if ($info['protected']) {
			$mod_list[$name]['action'] = '<img src="' . ROOT_DIR . 'images/crystal/16/forbidden.png" alt="" />';
		} elseif ($info['active']) {
			$mod_list[$name]['action'] = '<a href="' . uri('acp/system/modules/action_deactivate/dir_' . $info['dir']) . '" title="' . lang('system', 'disable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" /></a>';
		} else {
			$mod_list[$name]['action'] = '<a href="' . uri('acp/system/modules/action_activate/dir_' . $info['dir']) . '" title="' . lang('system', 'enable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
		}

	}

	$tpl->assign('LANG_modules_found', sprintf(lang('system', 'modules_found'), count($mod_list)));
	$tpl->assign('modules', $mod_list);

	$content = $tpl->fetch('system/modules.html');
}
?>