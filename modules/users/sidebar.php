<?php
/**
 * Users
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

if (defined('IS_USER')) {
	// Aktive Module einholen
	$active_modules = $modules->active_modules();
	$nav_mods = array();

	foreach ($active_modules as $name => $dir) {
		if ($modules->check($dir, 'adm_list')) {
			if ($dir == 'system') {
				$tpl->assign('access_system', true);
			} elseif ($dir == 'home') {
				$tpl->assign('access_home', true);
			} else {
				$nav_mods[$name]['name'] = $name;
				$nav_mods[$name]['dir'] = $dir;
			}
		}
	}
	$tpl->assign('nav_mods', $nav_mods);

	$tpl->assign('users_sidebar', $tpl->fetch('users/sidebar_modules.html'));
} else {
	if (defined('IN_ADM'))
		$tpl->assign('uri', uri('acp/users/login'));
	elseif (defined('IN_ACP3'))
		$tpl->assign('uri', uri('users/login'));

	$tpl->assign('users_sidebar', $tpl->fetch('users/sidebar_login.html'));
}
?>