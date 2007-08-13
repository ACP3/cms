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
if (!$modules->check()) {
	redirect('errors/403');
}
$mods = array();
$directories = scandir('modules');
$count_dir = count($directories);
for ($i = 0; $i < $count_dir; $i++) {
	//Modulinfos zurücksetzen
	$mod_info = array();
	if ($directories[$i] != '.' && $directories[$i] != '..' && file_exists('modules/' . $directories[$i] . '/info.php')) {
		include 'modules/' . $directories[$i] . '/info.php';
		$name = $mod_info['name'];
		$mods[$name]['name'] = $name;
		$mods[$name]['description'] = $mod_info['description'];
		$mods[$name]['author'] = $mod_info['author'];
		$mods[$name]['version'] = $mod_info['version'];
		$mods[$name]['active'] = $modules->check(1, $directories[$i], 'info');
		if (isset($mod_info['protected']) && $mod_info['protected']) {
			$mods[$name]['action'] = '<img src="' . ROOT_DIR . 'images/crystal/16/forbidden.png" alt="" />';
		} elseif ($mods[$name]['active']) {
			$mods[$name]['action'] = '<a href="' . uri('acp/system/entry/action_moddeactivation/dir_' . $directories[$i]) . '" title="' . lang('system', 'disable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/active.png" alt="" /></a>';
		} else {
			$mods[$name]['action'] = '<a href="' . uri('acp/system/entry/action_modactivation/dir_' . $directories[$i]) . '" title="' . lang('system', 'enable_module') . '"><img src="' . ROOT_DIR . 'images/crystal/16/inactive.png" alt="" /></a>';
		}
	}
}
ksort($mods);

$tpl->assign('LANG_modules_found', sprintf(lang('system', 'modules_found'), count($mods)));
$tpl->assign('modules', $mods);

$content = $tpl->fetch('system/mod_list.html');
?>