<?php
/**
 * System
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ACP3') && !defined('IN_ADM'))
	exit;

// Aktive Module einholen
$active_modules = $db->select('module', 'modules', 'active = \'1\'');
$c_active_modules = count($active_modules);
$active = array();
for ($i = 0; $i < $c_active_modules; $i++) {
	$mod_info = array();
	if (is_file('modules/' . $active_modules[$i]['module'] . '/adm_list.php')) {
		include 'modules/' . $active_modules[$i]['module'] . '/info.php';
		$active[$active_modules[$i]['module']] = $mod_info['name'];
	}
}

$mods = $db->select('mods', 'access', 'id = \'' . $_SESSION['acp3_access'] . '\'');
$access = explode('|', $mods[0]['mods']);
$c_access = count($access);
$access_system = false;

if ($c_access > 2) {
	$nav_mods = array();
	for ($i = 0; $i < $c_access; $i++) {
		if ($access[$i] != '' && $access[$i] != 'home' && array_key_exists($access[$i], $active)) {
			if ($access[$i] == 'system') {
				$access_system = true;
			} else {
				$name = $active[$access[$i]];
				$nav_mods[$name]['name'] = $name;
				$nav_mods[$name]['dir'] = $access[$i];
			}
		}
	}
	ksort($nav_mods);
	$tpl->assign('nav_mods', $nav_mods);
}

// Überprüfen, ob Zugriff auf System erlaubt ist
$tpl->assign('access_system', $access_system);

$field = $tpl->fetch('system/sidebar.html');
?>