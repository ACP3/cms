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

$mod_list = array();
$modules_dir = scandir('modules');
$c_modules_dir = count($modules_dir);

for ($i = 0; $i < $c_modules_dir; $i++) {
	//Modulinfos zurücksetzen
	$mod_info = array();
	if ($modules_dir[$i] != '.' && $modules_dir[$i] != '..' && is_file('modules/' . $modules_dir[$i] . '/info.php')) {
		include 'modules/' . $modules_dir[$i] . '/info.php';
		$name = $mod_info['name'];
		$mod_list[$name]['name'] = $name;
		$mod_list[$name]['description'] = $mod_info['description'];
		$mod_list[$name]['author'] = $mod_info['author'];
		$mod_list[$name]['version'] = $mod_info['version'];
		$mod_list[$name]['active'] = $modules->is_active($modules_dir[$i]);
		if (isset($mod_info['protected']) && $mod_info['protected']) {
			$mod_list[$name]['action'] = '<img src="' . ROOT_DIR . 'images/crystal/16/forbidden.png" alt="" />';
		} elseif ($mod_list[$name]['active']) {
			$mod_list[$name]['action'] = '<a href="' . uri('acp/system/entry/action_moddeactivation/dir_' . $modules_dir[$i]) . '" title="' . lang('system', 'disable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" /></a>';
		} else {
			$mod_list[$name]['action'] = '<a href="' . uri('acp/system/entry/action_modactivation/dir_' . $modules_dir[$i]) . '" title="' . lang('system', 'enable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
		}
	}
}
ksort($mod_list);

$tpl->assign('LANG_modules_found', sprintf(lang('system', 'modules_found'), count($mod_list)));
$tpl->assign('modules', $mod_list);

$content = $tpl->fetch('system/mod_list.html');
?>