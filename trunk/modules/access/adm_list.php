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

if (isset($_POST['entries']) || isset($modules->gen['entries'])) {
	include 'modules/access/entry.php';
} else {
	$access = $db->select('id, name, modules', 'access', 0, 'name ASC', POS, CONFIG_ENTRIES);
	$c_access = count($access);

	if ($c_access > 0) {
		$tpl->assign('pagination', pagination($db->select('id', 'access', 0, 0, 0, 0, 1)));

		for ($i = 0; $i < $c_access; $i++) {
			$access[$i]['access_to_mod'] = '';

			// Modulnamen anzeigen
			$access_to_mods = explode(',', $access[$i]['modules']);
			$c_access_to_mods = count($access_to_mods);

			for ($j = 0; $j < $c_access_to_mods; $j++) {
				$name = substr($access_to_mods[$j], 0, -2);
				if ($modules->is_active($name) && substr($access_to_mods[$j], -1, 1) != '0') {
					$mod_info = array();
					include 'modules/' . $name . '/info.php';

					$access[$i]['access_to_mod'].= $mod_info['name'] . ', ';
				}
			}
			$access[$i]['access_to_mod'] = substr($access[$i]['access_to_mod'], 0, -2);
		}
		$tpl->assign('access', $access);
	}
	$content = $tpl->fetch('access/adm_list.html');
}
?>