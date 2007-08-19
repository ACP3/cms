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
$modules_dir = scandir('modules/');
$c_modules_dir = count($modules_dir);
$active = array();

for ($i = 0; $i < $c_modules_dir; $i++) {
	$mod_info = array();
	if ($modules->check($modules_dir[$i], 'adm_list')) {
		include 'modules/' . $modules_dir[$i] . '/info.php';
		$active[$modules_dir[$i]] = $mod_info['name'];
	}
}

$mods = $db->select('modules', 'access', 'id = \'' . $_SESSION['acp3_access'] . '\'');
$access = explode(',', $mods[0]['modules']);
$c_access = count($access);
$access_system = false;

if ($c_access > 2) {
	$nav_mods = array();
	for ($i = 0; $i < $c_access; $i++) {
		$access[$i] = substr($access[$i], 0, -2);
		if ($access[$i] != '' && $access[$i] != 'home' && array_key_exists($access[$i], $active)) {
			// Überprüfen, ob Zugriff auf System erlaubt ist
			if ($access[$i] == 'system') {
				$tpl->assign('access_system', true);
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

$field = $tpl->fetch('system/sidebar.html');
?>