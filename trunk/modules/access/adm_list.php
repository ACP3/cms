<?php
/**
 * Access
 *
 * @author Goratsch Webdesign
 * @package ACP3
 * @subpackage Modules
 */

if (!defined('IN_ADM'))
	exit;
if (!$modules->check())
	redirect('errors/403');
if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/access/entry.php';
} else {
	$access = $db->select('id, name, mods', 'access', 0, 'name ASC', POS, CONFIG_ENTRIES);
	$c_access = count($access);

	// Aktive Module einholen
	$active_modules = $db->select('module', 'modules', 'active = \'1\'');
	$c_active_modules = count($active_modules);

	$active = array();
	for ($i = 0; $i < $c_active_modules; $i++) {
		$mod_info = array();
		if (file_exists('modules/' . $active_modules[$i]['module'] . '/info.php')) {
			include 'modules/' . $active_modules[$i]['module'] . '/info.php';
			$active[$active_modules[$i]['module']] = $mod_info['name'];
		}
	}

	if ($c_access > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'access', 0, 0, 0, 0, 1)));

		for ($i = 0; $i < $c_access; $i++) {
			$access[$i]['name'] = $access[$i]['name'];
			$access[$i]['access_to_mod'] = '';

			// Modulnamen anzeigen
			$access_to_mods = explode('|', $access[$i]['mods']);
			$c_access_to_mods = count($access_to_mods);
			for ($j = 0; $j < $c_access_to_mods; $j++) {
				if (array_key_exists($access_to_mods[$j], $active)) {
					$name = $active[$access_to_mods[$j]];
					$access[$i]['access_to_mod'].= $name . ', ';
				}
			}
			$access[$i]['access_to_mod'] = substr($access[$i]['access_to_mod'], 0, -2);
'';
		}
		$tpl->assign('access', $access);
	}
	$content = $tpl->fetch('access/adm_list.html');
}
?>